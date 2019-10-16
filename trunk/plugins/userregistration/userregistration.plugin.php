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

namespace Plugins\UserRegistration;

/**
 * user management plugin
 *
 * This creates forms and implements functions to
 * manage user data.
 *
 * @package    yana
 * @subpackage plugins
 */
class UserRegistrationPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * Create session: mail-authentification.
     *
     * @type        config
     * @template    MESSAGE
     *
     * @param       string  $username  user name
     * @param       string  $mail      user mail
     * @return      bool
     */
    public function set_user_mail(string $username, string $mail)
    {
        $database = $this->_connectToDatabase("user");

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
        $duplicates = $database->select("newuser.*.newuser_id", array('newuser_mail', '=', $mail));
        if (is_array($duplicates)) {
            foreach ($duplicates as $duplicate)
            {
                try {
                    $database->remove("newuser.$duplicate");
                } catch (\Exception $e) {
                    unset($e); // worst case 2 codes are active - so what? We can live with that.
                }
            }
        }
        unset($duplicates, $duplicate);

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
        $oldEntries = $database->select("newuser", array('NEWUSER_UTC', \Yana\Db\Queries\OperatorEnumeration::LESS, time() - 86400));
        if (is_array($oldEntries)) {
            foreach ($oldEntries as $row)
            {
                try {
                    $database->remove("newuser.".$row['NEWUSER_ID']);
                } catch (\Exception $e) {
                    unset($e); // we can do without this step if necessary
                }
            }
        }
        unset($oldEntries);

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
        try {
            $database->insert("newuser.*", $row)
                ->commit(); // may throw exception
        } catch (\Exception $e) {
            $message = "Entry could not be created";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $exception = new \Yana\Db\Queries\Exceptions\NotCreatedException($message, $level);
            $exception->setData($row);
            throw $exception;

        }

        $YANA = $this->_getApplication();
        $YANA->setVar('WEBSITE_URL', $YANA->getVar("REFERER"));
        $YANA->setVar('KEY', $key);
        $YANA->setVar('MAIL', $mail);
        $template = $YANA->getView()->createContentTemplate("id:USER_CONFIRM_MAIL");
        $this->_sendMail($mail, $template, (string) $YANA->getVar('PROFILE.MAIL'));
        return true;
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
     * @param       string  $target  new user-key
     */
    public function user_authentification(string $target)
    {
        $database = $this->_connectToDatabase('user');

        if (!$database->exists('newuser', array(array('newuser_key', $target, '=')))) {
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\User\NotFoundException("", $level);
        }

        $select = new \Yana\Db\Queries\Select($database);
        $select->setTable("newuser");
        $select->setWhere(array('newuser_key', '=', $target));
        $select->setLimit(1);
        $entry = $database->select($select);
        assert(is_array($entry), 'Array expected: $entry. Invalid dataset or invalid query.');
        if (empty($entry)) {
            throw new \Yana\Core\Exceptions\User\NotFoundException();
        }
        $entry = array_pop($entry);
        if (!isset($entry['NEWUSER_NAME']) || !isset($entry['NEWUSER_MAIL'])) {
            throw new \Yana\Core\Exceptions\User\NotFoundException();
        }

        // try to create user
        $user = $this->_getSecurityFacade()->createUser($entry['NEWUSER_NAME'], $entry['NEWUSER_MAIL']);
        $password = $user->generateRandomPassword();

        // send password to user's mail account
        $YANA = $this->_getApplication();
        $YANA->setVar('PASSWORT', $password);
        $YANA->setVar('NAME', $user->getId());

        $template = $YANA->getView()->createContentTemplate("id:USER_PASSWORD_MAIL");
        $sender = $YANA->getVar("PROFILE.MAIL");
        if (filter_var($sender, FILTER_VALIDATE_EMAIL)) {
            $recipient = $user->getMail();
            $template = $YANA->getView()->createContentTemplate("id:USER_CONFIRM_MAIL");
            $this->_sendMail($recipient, $template, $sender);
        }
        unset($sender);
    }

    /**
     * Send some e-mail.
     *
     * @param   string                            $recipient  mail address
     * @param   \Yana\Views\Templates\IsTemplate  $template   template
     * @param   string                            $sender     mail address
     */
    private function _sendMail(string $recipient, \Yana\Views\Templates\IsTemplate $template, string $sender)
    {
        $templateMailer = new \Yana\Mails\TemplateMailer($template);
        $subject = $this->_getApplication()->getLanguage()->getVar("user.mail_subject");
        $vars = array('DATE' => date('d-m-Y'));

        $headers = new \Yana\Mails\Headers\MailHeaderCollection();
        if ($sender) {
            $headers->setFromAddress($sender);
        }

        $templateMailer->send($recipient, $subject, $vars, $headers->toArray());
    }

}

?>