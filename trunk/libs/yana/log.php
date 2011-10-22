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

/**
 * Log
 *
 * This class represents log entries passed to the YANA framework.
 * Note that the difference between instances of this class and
 * classes that derive from the base class "Message" is how instances
 * are treated.
 * While instances of class "Log" are send to the system logs for a
 * later review by an administrator, instances of class "Message"
 * are directly put to the user's browser.
 *
 * So: Logs are for the administrator - while Messages are for the user.
 *
 * Usually you will have both: a Message for the user and a Log for the
 * administrator.
 *
 * E.g. your software detects a halt due to the failure of an important database operation.
 * Then you might issue a Message saying "Service is temporarily not available" to the user,
 * plus a Log saying "SQL statement '{$stmt}' failed. The error reads: {$errMessage}."
 * for review by the administrator.
 *
 * @access      public
 * @package     yana
 * @subpackage  error_reporting
 */
class Log extends \Yana\Core\Exceptions\AbstractException
{

    /**
     * Message queue
     *
     * @access  protected
     * @static
     * @var     array
     * @ignore
     */
    protected static $queue = array();

    /**
     * constructor
     *
     * @param  string      $message   the message that should be reported
     * @param  int         $code      optional error code
     * @param  \Exception  $previous  use this when you need to rethrow a catched exception
     */
    public function __construct($message = "", $code = E_USER_NOTICE, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        self::$queue[] = $this;
    }

    /**
     * Report a new message
     *
     * This is an alias for calling the constructor and just report (but not use) the instance.
     *
     * @access  public
     * @static
     * @param   string  $message    the message that should be reported
     * @param   scalar  $code       optional error number or class name
     * @param   mixed   $data       any kind of data that might help to understand context
     *                              in which the message was created
     */
    public static function report($message, $code = E_USER_NOTICE, $data = null)
    {
        if (is_int($code)) {
            $exception = new self($message, $code);
        } else {
            $exception = new $code($message);
        }
        if (!empty($data)) {
            $exception->setData($data);
        }
    }

    /**
     * get list of messages
     *
     * Returns the message queue as an array of objets of type Message.
     * The list is sorted in order of creation of the messages.
     *
     * @access  public
     * @static
     * @return  array
     */
    public static function getMessages()
    {
        assert('is_array(self::$queue); // Static member "queue" is expected to be an array.');
        return self::$queue;
    }

    /**
     * get number of messages
     *
     * Returns the number of messages in the message queue.
     * If the queue is empty, 0 is returned.
     *
     * @access  public
     * @static
     * @return  int
     */
    public static function countMessages()
    {
        assert('is_array(self::$queue); // Static member "queue" is expected to be an array.');
        return count(self::$queue);
    }

    /**
     * create log by a given text message
     *
     * This creates a log entry that is supposed to be written to a log-file.
     * The result is an associative array or bool(false) on error.
     *
     * @access  public
     * @static
     * @param   string  $message  exception object to get log from
     * @param   string  $prefix   string to put before each key string
     * @return  array
     */
    public static function getLogFromMessage($message, $prefix = "")
    {
        assert('is_string($message); // Wrong argument type for argument 1, String expected');
        assert('is_string($prefix); // Wrong argument type for argument 2, String expected');
        $prefix = mb_strtoupper("$prefix");

        /* 1. initialize vars */
        global $YANA;
        $report = array();

        /* 3. create entry */
        if (!empty($message)) {
            $report[$prefix.'MESSAGE'] = (string) $message;
        }
        if (!empty($_SESSION['user_name'])) {
            $report[$prefix.'USER'] = $_SESSION['user_name'];
        } else {
            $report[$prefix.'USER'] = '*GUEST';
        }
        $report[$prefix.'ACTION'] = PluginManager::getLastEvent();

        assert('is_array($report); // Unexpected result: $report should be an array.');
        return $report;
    }

    /**
     * create log
     *
     * This creates a log entry that is supposed to be written to a log-file.
     * The result is an associative array or bool(false) on error.
     *
     * @access  public
     * @static
     * @param   \Exception  $exception  exception object to get log from
     * @param   string      $prefix     string to put before each key string
     * @return  array
     */
    public static function getLog(\Exception $exception, $prefix = "")
    {
        assert('is_string($prefix); // Wrong argument type for argument 2, String expected');
        $prefix = mb_strtoupper("$prefix");

        $message = $exception->getMessage();
        $data = null;
        $report = Log::getLogFromMessage($message, $prefix);

        /* 1. get data (if available) */
        if ($exception instanceof \Yana\Core\Exceptions\AbstractException) {
            $data = $exception->getData();
            if (empty($data)) {
                $data = null;
            } elseif (!is_array($data)) {
                $data = array($data);
            }
        }

        /* 2. create entry */
        if (is_array($data)) {
            $report[$prefix.'DATA'] = $data;
        }

        assert('is_array($report); // Unexpected result: $report should be an array.');
        return $report;
    }

    /**
     * test for equality
     *
     * The parameter $otherLog needs to be an array created by Log::getLog().
     *
     * Checks if the input array and the log created from the current object are
     * the same.
     *
     * The entries are considered identical if and only if: the textual
     * representation of the "message" and "data" values are the same.
     *
     * @access  public
     * @static
     * @param   array   $thisLog    current log
     * @param   array   $otherLog   other log
     * @param   array   $prefix     prefix
     * @return  bool
     * @ignore
     */
    public static function logEquals(array $thisLog, array $otherLog, $prefix = "")
    {
        assert('is_string($prefix); // Wrong type for argument 3. String expected');
        $prefix = mb_strtoupper("$prefix");

        $message = $prefix . 'MESSAGE';
        $data = $prefix . 'DATA';

        // extract strings
        $thisMessage = print_r(@$thisLog[$message], true);
        $otherMessage = print_r(@$otherLog[$message], true);
        $thisData = print_r(@$thisLog[$data], true);
        $otherData = print_r(@$otherLog[$data], true);

        // compare strings and return result
        if ($thisMessage === $otherMessage && strcasecmp($thisData, $otherData) === 0) {
            return true;
        } else {
            return false;
        }
    }

}

?>