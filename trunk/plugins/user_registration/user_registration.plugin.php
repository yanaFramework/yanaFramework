<?php
/**
 * User Registration
 *
 * Allows user to register with no need for an administrator.
 * New users are by default automatically granted a minimum security level,
 * that will allow them to log-in and see information preserved for registered
 * users.
 *
 * If you wish to grant more rights to a user, either do so manually or edit this plugin to
 * change the default settings.
 *
 * {@translation
 *
 *   de:   Nutzerregistrierung
 *
 *         Erlaubt es Nutzer sich selbstständig zu registrieren ohne das ein Administrator dazu erforderlich ist.
 *         Neue Nutzer erhalten automatisch einen minimalen Sicherheitslevel,
 *         welcher es ihnen gestattet, sich anzumelden und Informationen abzurufen, deren Zugriff auf registrierte
 *         Nutzer beschränkt ist.
 *
 *         Falls Sie einem Nutzer weitere Rechte einräumen möchten, können Sie diesen entweder manuell editieren,
 *         oder Sie passen dieses Plugin an, um die Standardeinstellungen zu ändern.
 *
 * }
 *
 * @author     Thomas Meyer
 * @type       config
 * @extends    user
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * user management plugin
 *
 * This creates forms and implements functions to
 * manage user data.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_user_registration extends StdClass implements IsPlugin
{

    /**
     * Default event handler.
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     *
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * Create session: mail-authentification.
     *
     * @type        config
     * @template    MESSAGE
     *
     * @access      public
     * @param       string  $username  user name
     * @param       string  $mail      user mail
     */
    public function set_user_mail($username, $mail)
    {
        $database = Yana::connect("user");

        $mail = mb_substr(mb_strtolower($mail), 0, 255);
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidInputWarning();
        }

        $name = mb_strtolower($username);
        if (empty($name)) {
            throw new InvalidInputWarning();
        }

        $key = uniqid(substr(md5($mail), 0, 3));

        if ($database->exists("user.$name")) {
            throw new UserAllreadyExistsWarning();
        }

        /*
         * 1) Remove duplicates
         *
         * Find and delete all previous entries of users with the same mail address
         */
        $duplicates = $database->select("newuser.*.newuser_id", array('newuser_key', '=', $key));
        if (is_array($duplicates)) {
            foreach ($duplicates as $newuser_id)
            {
                $database->remove("newuser.$newuser_id");
            }
        }
        unset($duplicates);
        $database->commit();

        /*
         * 2) Remove timed out entries
         *
         * limit = 24h = 86400sek
         */
        $old_entries = $database->select("newuser");
        $limit = time() - 86400;
        if (is_array($old_entries)) {
            foreach ($old_entries as $row)
            {
                if ($row['NEWUSER_UTC'] < $limit) {
                    $database->remove("newuser.".$row['NEWUSER_ID']);
                }
            }
        }
        unset($old_entries);
        $database->commit();

        /*
         * 4) add row to table
         */
        $row = array("newuser_name" => $name, "newuser_key" => $key, "newuser_mail" => $mail);
        if (!$database->insert("newuser.*", $row)) {
            throw new InvalidInputWarning();

        } elseif (!$database->commit()) {
            throw new Error();
        }

        global $YANA;
        $YANA->setVar('WEBSITE_URL', $YANA->getVar("REFERER"));
        $YANA->setVar('KEY', $key);
        $YANA->setVar('MAIL', $mail);
        self::_sendMail($mail, "id:USER_CONFIRM_MAIL");
    }

    /**
     * Form to create a new user
     *
     * Note: visitors must be allowed to sign on for themselves, or this will fail.
     *
     * Shows a form, where visitors may enter the prefered user name and mail address.
     * A mail with the login link will be send to them. (implements double-opt-in procedure)
     *
     * @type        config
     * @template    USER_CREATE_TEMPLATE
     * @menu        group: setup
     * @title       {lang id="create_user"}
     *
     * @access      public
     */
    public function get_user_mail()
    {
        // Just views template - no business logic required.
    }

    /**
     * Create a new user
     *
     * This function registers new user.
     * Note: visitors must be allowed to sign on for themselves, or this will fail.
     *
     * This checks the provided authentification id and if valid, creates a new user
     * according to the dataset in table "newuser".
     * Settings of this new user depend on the system settings for the default user
     * (see file: system.config).
     *
     * @type        default
     * @template    MESSAGE
     * @onsuccess   goto: login, text: FirstLoginMessage
     * @language    user
     *
     * @access      public
     * @param       string  $target  new user-key
     */
    public function user_authentification($target)
    {
        $database = Yana::connect("user");

        if (!$database->exists('newuser', array(array('newuser_key', $target, '=')))) {
            throw new InvalidInputWarning();
        }

        $select = new \Yana\Db\Queries\Select($database);
        $select->setTable("newuser");
        $select->setWhere(array('newuser_key', '=', $target));
        $select->setLimit(1);
        $entry = $database->select($select);
        assert('is_array($entry); // Array expected: $entry. Invalid dataset or invalid query.');
        if (empty($entry)) {
            throw new UserNotFoundError();
        }
        $entry = array_pop($entry);
        if (!isset($entry['NEWUSER_NAME']) || !isset($entry['NEWUSER_MAIL'])) {
            throw new UserNotFoundError();
        }

        // try to create user
        try {

            YanaUser::createUser($entry['NEWUSER_NAME'], $entry['NEWUSER_MAIL']);
            $user = YanaUser::getInstance($entry['NEWUSER_NAME']);
            $password = $user->setPassword();

            // send password to user's mail account
            $YANA = Yana::getInstance();
            $YANA->setVar('PASSWORT', $password);
            $YANA->setVar('NAME', $user->getName());
            $mail = new \Yana\Mails\Mailer($YANA->getView()->createContentTemplate("id:USER_PASSWORD_MAIL"));
            $mail->setSender($YANA->getVar("PROFILE.MAIL"));
            $mail->setVar('DATE', date('d-m-Y'));
            $mail->setSubject($YANA->getLanguage()->getVar("user.mail_subject"));
            $mail->send($user->getMail());

        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {
            throw new InvalidInputWarning();
        } catch (\Yana\Core\Exceptions\AlreadyExistsException $e) {
            throw new UserAllreadyExistsWarning();
        } catch (\Exception $e) {
            throw new Error();
        }
    }

    /**
     * send mail
     *
     * @access  private
     * @static
     * @param   string  $mail      mail address (recipient)
     * @param   string  $template  template
     * @ignore
     */
    private static function _sendMail($mail, $template)
    {
        settype($mail, "string");
        settype($template, "string");

        $YANA = \Yana::getInstance();
        $now = getdate();
        $mail = new \Yana\Mails\Mailer($YANA->getView()->createContentTemplate($template));
        $mail->setSubject($YANA->getLanguage()->getVar("USER.MAIL_SUBJECT")."\n");
        $mail->setVar('DATE', $now['mday'] . '.' . $now['mon'] . '.' . $now['year']);
        $mail->send($mail);
    }

}

?>