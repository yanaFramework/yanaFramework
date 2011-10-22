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
 * Message
 *
 * This is a super class for all common messages.
 * It may be used to represent information, that
 * is not an error. E.g. reporting, that a certain
 * operation has been finished successfully.
 *
 * @access      public
 * @package     yana
 * @subpackage  error_reporting
 */
class Message extends ReportAbstract
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
     * This function checks whether message is fulltext or id and resolves id.
     * It checks whether data is array or empty or other and replaces token
     * found in the message string.
     *
     * @param  string      $message   the message that should be reported
     * @param  int         $code      optional error code
     * @param  \Exception  $previous  use this when you need to rethrow a catched exception
     */
    public function __construct($message = "", $code = E_USER_NOTICE, \Exception $previous = null)
    {
        assert('is_scalar($message); // Wrong argument type for argument 1, String expected');
        assert('is_int($code); // Wrong argument type for argument 2, Integer expected');
        assert('is_array(self::$queue); // Static member "queue" is expected to be an array.');
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
        $exception = null;
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
}

?>