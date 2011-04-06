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
 * <<abstract>> Report
 *
 * This is an abstract super class for all kinds of messages.
 *
 * @abstract
 * @access      public
 * @package     yana
 * @subpackage  error_reporting
 */
abstract class ReportAbstract extends Exception
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
     * context data
     *
     * Any kind of data that might help to understand the context
     * in which the message was created.
     *
     * @access  protected
     * @var     mixed
     * @ignore
     */
    protected $data = array();

    /**
     * message header text
     *
     * @access  protected
     * @var     string
     * @ignore
     */
    protected $header = null;

    /**
     * a descriptive message text
     *
     * @access  protected
     * @var     string
     * @ignore
     */
    protected $text = null;

    /**
     * constructor
     *
     * Create a new instance, representing a system message.
     *
     * @param  string      $message   the message that should be reported
     * @param  int         $code      optional error code
     * @param  \Exception  $previous  use this when you need to rethrow a catched exception
     */
    public function __construct($message = "", $code = E_USER_NOTICE, \Exception $previous = null)
    {
        assert('is_string($message); // Wrong argument type for argument 1, String expected');
        assert('is_int($code); // Wrong argument type for argument 2, Integer expected');
        assert('is_array(self::$queue); // Static member "queue" is expected to be an array.');
        self::$queue[] = $this;
        if (!empty($message)) {
            $this->data['MESSAGE'] = $message;
        }

        parent::__construct($message, $code, $previous);
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
     * get context data
     *
     * Returns given context data (where available).
     *
     * @access  public
     * @final
     * @return  mixed
     */
    final public function getData()
    {
        return $this->data;
    }

    /**
     * set context data
     *
     * You may provide parameters and additional context information to an exception.
     * E.g. for a failed insert-query you may provide the row which you could not insert.
     *
     * If you are using language references, you may use this to replace tokens used in the translation.
     *
     * Example usage:
     * <code>
     * $data = array('FILE' => $filename);
     * $error = new MyFileException();
     * $error->setData($data);
     * throw $error;
     * </code>
     *
     * @access  public
     * @param   mixed  $data  context data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * set message header
     *
     * @access  protected
     * @param   string  $header  headline
     * @ignore
     */
    protected function setHeader($header)
    {
        assert('is_string($header); // Wrong argument type argument 1. String expected');
        $this->header = (string) $header;
    }

    /**
     * set message text
     *
     * @access  protected
     * @param   string  $text  descriptive text
     * @ignore
     */
    protected function setText($text)
    {
        assert('is_string($text); // Wrong argument type argument 1. String expected');
        $this->text = (string) $text;
    }

    /**
     * returns the message string of this event
     *
     * This creates a string of at most 1000 characters
     * containing the text in the message attribute and a serialized
     * representation of the data attribute.
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->getText();
    }

    /**
     * get message header
     *
     * Returns the message headline (if any).
     * If there is none, an empty string is returned.
     *
     * @access  public
     * @return  string
     */
    public function getHeader()
    {
        if (!isset($this->header)) {
            $language = Language::getInstance();
            $language->readFile('message');
            $id = get_class($this);
            if ($language->isVar("$id.h")) {
                $this->header = (string) $language->getVar("$id.h");
                if (!empty($this->data)) {
                    $this->header = SmartUtility::replaceToken($this->header, $this->data);
                    $this->header = $language->replaceToken($this->header);
                }
            } else {
                $this->header = "";
            }
        }
        return $this->header;
    }

    /**
     * get message text
     *
     * Returns the message text (if any).
     * If there is none, an empty string is returned.
     *
     * @access  public
     * @return  string
     */
    public function getText()
    {
        if (!isset($this->text)) {
            $language = Language::getInstance();
            $language->readFile('message');
            $id = get_class($this);
            $this->text = "";
            if ($language->isVar($id . ".p")) {
                $this->text = (string) $language->getVar($id . ".p");
                if (!empty($this->data)) {
                    $this->text = SmartUtility::replaceToken($this->text, $this->data);
                    $this->text = $language->replaceToken($this->text);
                }
            } elseif (!empty($this->message)) {
                $this->text = $this->message;
                if (count($this->data) > 1) { // must have more than obligatory entry "MESSAGE"
                    $data = $this->data;
                    unset($data['MESSAGE']);
                    $this->text .= ";" . print_r($this->data, true);
                }
            }
            $this->text = preg_replace('/\s/', ' ', $this->text);
            $this->text = mb_substr($this->text, 0, 1000);
            $this->text = str_replace('"', '&quot;', $this->text);
        }
        return $this->text;
    }

}

?>