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
class plugin_user_registration extends StdClass implements \Yana\IsPlugin
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
        $database = \Yana\Application::connect("user");

        $mail = mb_substr(mb_strtolower($mail), 0, 255);
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid user mail address.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\User\MailInvalidException($message, $level);
        }

        $name = mb_strtolower($username);
        if (empty($name)) {
            $message = "Missing user name.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $e = new \Yana\Core\Exceptions\Forms\MissingFieldException($message, $level);
            throw $e->setField('Name');
        }

        $key = uniqid(substr(md5($mail), 0, 3));

        if ($database->exists("user.$name")) {
            $message = "Another user by that name already exists.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $e = new \Yana\Core\Exceptions\User\AlreadyExistsException($message, $level);
            throw $e->setData($name);
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

        try {
            $database->commit(); // may throw exception
        } catch (\Yana\Db\CommitFailedException $e) {
            // unable to delete duplicates (maybe already deleted)
            unset($e);
        }

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

        try {
            $database->commit(); // may throw exception
        } catch (\Yana\Db\CommitFailedException $e) {
            // unable to delete entries (maybe already deleted)
            unset($e);
        }

        /*
         * 4) add row to table
         */
        $row = array("newuser_name" => $name, "newuser_key" => $key, "newuser_mail" => $mail);
        if (!$database->insert("newuser.*", $row)) {
            $message = "Entry could not be created";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $exception = new \Yana\Db\Queries\Exceptions\NotCreatedException($message, $level);
            $exception->setData($row);
            throw $exception;

        }
        $database->commit(); // may throw exception

        global $YANA;
        $YANA->setVar('WEBSITE_URL', $YANA->getVar("REFERER"));
        $YANA->setVar('KEY', $key);
        $YANA->setVar('MAIL', $mail);
        $template = $YANA->getView()->createContentTemplate("id:USER_CONFIRM_MAIL");
        self::_sendMail($mail, $template);
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
     * @onsuccess   goto: login, text: Yana\Core\Exceptions\Messages\FirstLoginMessage
     * @language    user
     *
     * @access      public
     * @param       string  $target  new user-key
     */
    public function user_authentification($target)
    {
        $database = \Yana\Application::connect("user");

        if (!$database->exists('newuser', array(array('newuser_key', $target, '=')))) {
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\User\NotFoundException("", $level);
        }

        $select = new \Yana\Db\Queries\Select($database);
        $select->setTable("newuser");
        $select->setWhere(array('newuser_key', '=', $target));
        $select->setLimit(1);
        $entry = $database->select($select);
        assert('is_array($entry); // Array expected: $entry. Invalid dataset or invalid query.');
        if (empty($entry)) {
            throw new \Yana\Core\Exceptions\User\NotFoundException();
        }
        $entry = array_pop($entry);
        if (!isset($entry['NEWUSER_NAME']) || !isset($entry['NEWUSER_MAIL'])) {
            throw new \Yana\Core\Exceptions\User\NotFoundException();
        }

        // try to create user
        \Yana\User::createUser($entry['NEWUSER_NAME'], $entry['NEWUSER_MAIL']);
        $user = \Yana\User::getInstance($entry['NEWUSER_NAME']);
        $password = $user->setPassword();

        // send password to user's mail account
        $YANA = \Yana\Application::getInstance();
        $YANA->setVar('PASSWORT', $password);
        $YANA->setVar('NAME', $user->getName());

        $template = $YANA->getView()->createContentTemplate("id:USER_PASSWORD_MAIL");
        $sender = $YANA->getVar("PROFILE.MAIL");
        if (filter_var($sender, FILTER_VALIDATE_EMAIL)) {
            $recipient = $user->getMail();
            $template = $YANA->getView()->createContentTemplate("id:USER_CONFIRM_MAIL");
            self::_sendMail($recipient, $template, $sender);
        }
        unset($sender);
    }

    /**
     * Send some e-mail.
     *
     * @param   string                  $recipient  mail address
     * @param   \Yana\Views\IsTemplate  $template   template
     * @param   string                  $sender     mail address
     * @ignore
     */
    private static function _sendMail($recipient, \Yana\Views\IsTemplate $template, $sender = "")
    {
        assert('is_string($recipient); // Invalid argument $recipient: string expected');
        assert('is_string($sender); // Invalid argument $sender: string expected');
        global $YANA;

        $templateMailer = new \Yana\Mails\TemplateMailer($template);
        $subject = $YANA->getLanguage()->getVar("user.mail_subject");
        $vars = array('DATE' => date('d-m-Y'));

        $headers = array();
        if ($sender) {
            $headers = array('from' => $sender);
        }

        $templateMailer->send($recipient, $subject, $vars, $headers);
    }

}

?>