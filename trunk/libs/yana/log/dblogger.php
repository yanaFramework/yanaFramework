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
class DbLogger extends \Yana\Log\AbstactLogger implements \Yana\Log\IsLogger
{

    /**
     * @var \Yana\Db\IsConnection
     */
    private $_database = null;

    /**
     * @var array
     */
    private $_messages = array();

    /**
     * @var int
     */
    private $_maxNumberOfEntries = 0;

    /**
     * @var string
     */
    private $_mailRecipient = "";

    /**
     * @param  \Yana\Db\IsConnection  $database  connection object
     */
    public function __construct(\Yana\Db\IsConnection $database)
    {
        $this->_database = $database;
    }

    /**
     * This implements the logging behavior.
     *
     * @param   string  $message  the message that should be reported
     * @param   int     $level    numeric level of severity
     * @param   mixed   $data     any kind of data that might help to understand context in which the message was created
     */
    public function addLog($message, $level = \Yana\Log\TypeEnumeration::INFO, $data = array())
    {
        if ($this->_isAcceptable($level)) {
            $this->_messages[] = array(
                'log_action' => \Yana\Plugins\Facade::getLastEvent(),
                'log_message' => $message,
                'log_data' => (array) $data
            );
        }
    }

    /**
     * Set the maximum number of database entries.
     *
     * When the max. number of rows is reached, the logger will flush the oldest entries.
     *
     * @param   int  $max  0 means infinite
     * @return  self
     */
    public function setMaxNumerOfEntries($max = 0)
    {
        assert(is_int($max), 'Invalid argument $max: int expected');
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
     * @param   string  $recipient  must be a valid e-mail address, empty string means no mail will be send
     * @return  self
     */
    public function setMailRecipient($recipient = "")
    {
        assert($recipient === filter_var($recipient, FILTER_VALIDATE_EMAIL), 'Invalid argument $recipient: must be a valid mail address');

        $this->_mailRecipient = $recipient;
        return $this;
    }

    /**
     * Get e-mail recipient for log-files.
     *
     * @return  string
     */
    public function getMailRecipient()
    {
        return $this->_mailRecipient;
    }

    /**
     * This writes all messages to the database and flushes the cache.
     *
     * Called by destructor.
     *
     * @ignore
     * @codeCoverageIgnore
     */
    protected function _flushToDatabase()
    {
        // skip if message-queue is empty
        if (empty($this->_messages)) {
            return;
        }

        $this->_database;
        $logChanged = false;
        $messageCount = 0;

        $previousLog = '';

        assert(!isset($newLog), 'Cannot redeclare var $newLog');
        foreach ($this->_messages as $newLog)
        {
            assert(is_array($newLog), 'unexpected result: Log entry is expected to be an array');

            // check if new log entry is valid
            if (empty($newLog)) {
                continue;
            }

            // do not create duplicate entries
            if ($newLog == $previousLog) {
                continue;
            }

            // abort if insert failed
            $this->_database->insert("log", $newLog);

            $previousLog = $newLog;
            $logChanged = true;
            $messageCount++;
        } // end foreach ($message)
        unset($newLog);

        // skip if nothing has changed
        if ($logChanged !== true) {
            return;
        }

        // abort if database commit failed
        $this->_database->commit(); // may throw exception
        $this->_messages = array();
    }

    /**
     * Checks if the database is full and if so, removes the oldest entries.
     *
     * Called by destructor.
     *
     * @param   int  $maxLogLength  maximum number of entries that will remain in the logs
     * @throws  \Yana\Core\Exceptions\NotWriteableException  when database is set to read-only
     *
     * @codeCoverageIgnore
     */
    protected function _cleanUpDatabase($maxLogLength)
    {
        $oldLogEntry = $this->_database->select("log.*.log_id", array(), array('LOG_ID'), $maxLogLength, 1, true);
        $oldId = array_pop($oldLogEntry);

        $this->_database->remove("log", array('LOG_ID', '<=', $oldId), 0)
            ->commit(); // may throw exception
    }

    /**
     * Creates a new form mailer and returns it.
     *
     * @return  \Yana\Mails\FormMailer
     *
     * @internal Override this method in unit-tests to inject a null mailer.
     *
     * @codeCoverageIgnore
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
     * @param   string  $recipient  valid e-mail address
     * @throws  \Yana\Core\Exceptions\NotWriteableException  when database is set to read-only
     *
     * @codeCoverageIgnore
     */
    protected function _flushDatabaseToMail($recipient)
    {
        assert(is_string($recipient), 'Invalid argument $recipient: string expected');

        $oldLogEntries = $this->_database->select("log", array(), array('LOG_ID'));

        // send e-mail
        $mail = $this->_getMailer();
        $mail->send($recipient, 'JOURNAL', $oldLogEntries);

        // truncate table log
        $this->_database->remove("log", array(), 0)
            ->commit(); // may throw exception
    }

    /**
     * <<destructor>> Will flush all messages to the database and send mails, where applicable.
     *
     * @codeCoverageIgnore
     */
    public function __destruct()
    {
        try {
            $this->_flushToDatabase();
            // finished, if number of log entries is still smaller than maximum
            if ($this->getMaxNumerOfEntries() > 0 && $this->_database->length('log') > $this->getMaxNumerOfEntries()) {
                if ($this->getMailRecipient()) {
                    $recipient = $this->getMailRecipient();
                    $this->_flushDatabaseToMail($recipient);
                } else {
                    $this->_cleanUpDatabase($this->getMaxNumerOfEntries());
                }
            }
        } catch (\Exception $e) {
            unset($e);
        }
    }

    /**
     * Get list of log-messages.
     *
     * Each "message" is an array containing 'log_action', 'log_message', 'log_data'.
     *
     * @return  array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

}

?>