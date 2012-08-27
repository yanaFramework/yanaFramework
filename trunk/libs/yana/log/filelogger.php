<?php

/**
 * YANA library
 *
 * Software:  Yana PHP-Framework
 * Version:   {VERSION} - {DATE}
 * License:   GNU GPL  http://www.gnu.org/licenses/
 *
 * This program: can be redistributed and/or modified under the
 * terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 *
 * This notice MAY NOT be removed.
 *
 * @package  yana
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

namespace Yana\Log;

/**
 * Database logger that writes messages to the "log" table.
 *
 * You may decide to set a maximum number of log entries,
 * and optionally have log entries send to your mail-address.
 *
 * @package    yana
 * @subpackage log
 */
class FileLogger extends \Yana\Log\AbstactLogger implements \Yana\Log\IsLogger
{

    /**
     * @var \Yana\Files\IsTextFile
     */
    private $_file = null;

    /**
     * @var int
     */
    private $_maxNumberOfEntries = 0;

    /**
     * @var string
     */
    private $_mailRecipient = "";

    /**
     * @param  \Yana\Files\IsTextFile  $database  connection object
     */
    public function __construct(\Yana\Files\IsTextFile $file)
    {
        $this->_file = $file;
    }

    /**
     * This implements the logging behavior.
     *
     * @param   string  $message  the message that should be reported
     * @param   int     $level    numeric level of severity
     * @param   mixed   $data     any kind of data that might help to understand context in which the message was created
     */
    public function addLog($message, $level = IsLogger::INFO, $data = array())
    {
        if ($this->_isAcceptable($level)) {

            $logEntry = array(
                'MESSAGE' => (string) $message,
                'USER' => (!empty($_SESSION['user_name'])) ? (string) $_SESSION['user_name'] : '*GUEST',
                'ACTION' => \Yana\Plugins\Manager::getLastEvent(),
                'TIME' => date('r')
            );
            $errorMessage = "";

            /**
             * The result is an array, possibly containing a message
             * and additional information describing the circumstances
             * in which the error occured.
             */
            foreach ($logEntry as $label => $value)
            {
                $errorMessage .= $label;
                for ($i = mb_strlen($label); $i < 15; $i++)
                {
                    $errorMessage .= ' ';
                }
                $errorMessage .= $value . "\n";
            }

            /* output the error message to a log file */
            if (!$this->_file->exists()) {
                $this->_file->create();
            }
            $this->_file->appendLine($errorMessage);
            $this->_file->write();
        }
    }

    /**
     * Set the maximum number of database entries.
     *
     * When the max. number of rows is reached, the logger will flush the oldest entries.
     *
     * @param  int  $max  0 means infinite
     * @return DbLogger
     */
    public function setMaxNumerOfEntries($max = 0)
    {
        assert('is_int($max); // Invalid argument $max: int expected');
        $this->_maxNumberOfEntries = $max;
        return $this;
    }

    /**
     * @return  int
     */
    public function getMaxNumerOfEntries()
    {
        return $this->_maxNumberOfEntries;
    }

    /**
     * Set e-mail recipient for log-files.
     *
     * You may set up an e-mail recipient to which all log-entries will be sent,
     * when the log is full, instead of dumping the oldest log entries.
     *
     * @param  string  $recipient  must be a valid e-mail address, empty string means no mail will be send
     * @return DbLogger
     */
    public function setMailRecipient($recipient = "")
    {
        assert('filter_var($recipient, FILTER_VALIDATE_EMAIL); // Invalid argument $recipient: must be a valid mail address');

        $this->_mailRecipient = $recipient;
        return $this;
    }

    /**
     * Get e-mail recipient for log-files.
     *
     * @return string
     */
    public function getMailRecipient()
    {
        return $this->_mailRecipient;
    }

    /**
     * Checks if the database is full and if so, removes the oldest entries.
     *
     * @param  int  $maxLogLength  maximum number of entries that will remain in the logs
     */
    protected function _cleanUp($maxLogLength)
    {
        assert('is_int($maxLogLength); // Invalid argument $maxLogLength: int expected');

        // finished, if number of log entries is still smaller than maximum
        if ($this->_file->length() <= $maxLogLength) {
            return;
        }

        $totalContent = $this->_file->getContent();
        // remove all but the maximum number of latest entries (giving a negative number makes PHP sort descending)
        $trailingContent = array_slice($totalContent, -$maxLogLength);
        $this->_file->setContent($trailingContent);
        $this->_file->write();
    }

    /**
     * Creates a new form mailer and returns it.
     *
     * @return  \Yana\Mails\FormMailer
     *
     * @internal Override this method in unit-tests to inject a null mailer.
     */
    protected function _getMailer()
    {
        return new \Yana\Mails\FormMailer();
    }

    /**
     * Removes all entries from the log table and forwards them as an e-mail.
     *
     * Called by destructor.
     *
     * @param  string  $recipient  valid e-mail address
     */
    protected function _flushToMail($recipient)
    {
        assert('is_string($recipient); // Invalid argument $recipient: string expected');

        $oldLogEntries = $this->_file->getContent();

        // send e-mail
        $mail = $this->_getMailer();
        $mail->send($recipient, 'JOURNAL', $oldLogEntries);

        // truncate file
        $this->_file->setContent(array());
        $this->_file->write();
    }

    /**
     * <<destructor>> Will flush all messages to the database and send mails, where applicable.
     */
    public function __destruct()
    {
        if ($this->getMaxNumerOfEntries() > 0 && $this->_file->length() > $this->getMaxNumerOfEntries()) {
            if ($this->getMailRecipient()) {
                $this->_flushToMail($this->getMailRecipient());
            } else {
                $this->_cleanUp($this->getMaxNumerOfEntries());
            }
        }
    }

}

?>