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
declare(strict_types=1);

namespace Yana\Core\Exceptions;

/**
 * <<exception>> Super class for all kinds of exceptions and messages.
 *
 * This class is abstract, because some people have a tendency to throw unspecific
 * instances of \Exception and identify the kind of exception thrown by the text
 * of the error message. JUST DON'T!
 *
 * Error texts may change: don't rely on them!
 *
 * Thus you should NEVER trow an unspecific exception, because it beats the whole
 * concept of having an exception in the first place: which is being able to catch
 * thrown exceptions in the calling method and react on to them based on the class.
 *
 * @package     yana
 * @subpackage  core
 */
abstract class AbstractException extends \Exception implements \Yana\Core\Exceptions\IsException
{

    /**
     * Message queue
     *
     * @var  array
     * @ignore
     */
    protected static $queue = array();

    /**
     * context data
     *
     * Any kind of data that might help to understand the context
     * in which the message was created.
     *
     * @var     mixed
     * @ignore
     */
    protected $data = array();

    /**
     * message header text
     *
     * @var     string
     * @ignore
     */
    protected $header = null;

    /**
     * a descriptive message text
     *
     * @var     string
     * @ignore
     */
    protected $text = null;

    /**
     * Used to translate error messages where needed.
     *
     * @var \Yana\Core\Dependencies\IsExceptionContainer
     */
    private static $_dependencyContainer = null;

    /**
     * Create a new instance, representing a system message.
     *
     * @param  string      $message   the message that should be reported
     * @param  int         $code      optional error code
     * @param  \Exception  $previous  use this when you need to rethrow a catched exception
     */
    public function __construct($message = "", $code = \Yana\Log\TypeEnumeration::ERROR, ?\Exception $previous = null)
    {
        assert(is_string($message), 'Wrong argument type for argument 1, String expected');
        assert(is_int($code), 'Wrong argument type for argument 2, Integer expected');
        assert(is_array(self::$queue), 'Static member "queue" is expected to be an array.');
        self::$queue[] = $this;
        if (!empty($message)) {
            $this->data['MESSAGE'] = $message;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get dependencies for exception messages.
     *
     * @return  \Yana\Core\Dependencies\IsExceptionContainer
     */
    protected static function getDependencyContainer()
    {
        if (!isset(self::$_dependencyContainer)) {
            self::$_dependencyContainer = new \Yana\Core\Dependencies\ExceptionContainer(new \Yana\Translations\NullFacade());
        }
        self::$_dependencyContainer->getLanguage()->loadTranslations("message"); // may throw TranslationException
        return self::$_dependencyContainer;
    }

    /**
     * Set dependencies for exception messages.
     *
     * The translation manager is needed to provide translations from the generic translation package "message".
     *
     * @param  \Yana\Core\Dependencies\IsExceptionContainer  $container  wraps dependencies for this class
     */
    public static function setDependencyContainer(\Yana\Core\Dependencies\IsExceptionContainer $container)
    {
        self::$_dependencyContainer = $container;
    }

    /**
     * get list of messages
     *
     * Returns the message queue as an array of objets of type Message.
     * The list is sorted in order of creation of the messages.
     *
     * @return  array
     */
    public static function getMessages()
    {
        assert(is_array(self::$queue), 'Static member "queue" is expected to be an array.');
        return self::$queue;
    }

    /**
     * get number of messages
     *
     * Returns the number of messages in the message queue.
     * If the queue is empty, 0 is returned.
     *
     * @return  int
     */
    public static function countMessages()
    {
        assert(is_array(self::$queue), 'Static member "queue" is expected to be an array.');
        return count(self::$queue);
    }

    /**
     * get context data
     *
     * Returns given context data (where available).
     *
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
     * @param   mixed  $data  context data
     * @return  \Yana\Core\Exceptions\AbstractException
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * set message header
     *
     * @param   string  $header  headline
     * @return  \Yana\Core\Exceptions\AbstractException
     * @ignore
     */
    protected function setHeader($header)
    {
        assert(is_string($header), 'Wrong argument type argument 1. String expected');
        $this->header = (string) $header;
        return $this;
    }

    /**
     * set message text
     *
     * @param   string  $text  descriptive text
     * @return  \Yana\Core\Exceptions\AbstractException
     * @ignore
     */
    protected function setText($text)
    {
        assert(is_string($text), 'Wrong argument type argument 1. String expected');
        $this->text = (string) $text;
        return $this;
    }

    /**
     * returns the message string of this event
     *
     * This creates a string of at most 1000 characters
     * containing the text in the message attribute and a serialized
     * representation of the data attribute.
     *
     * @return  string
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
     * @return  string
     */
    public function getHeader()
    {
        if (!isset($this->header)) {
            $this->header = "";

            try {
                $language = self::getDependencyContainer()->getLanguage(); // may throw TranslationException

                $id = get_class($this);
                if ($language->isVar($id . ".h")) {
                    $this->header = (string) $language->getVar($id . ".h");
                    if (!empty($this->data)) {
                        $this->header = \Yana\Util\Strings::replaceToken($this->header, $this->data);
                        $this->header = $language->replaceToken($this->header);
                    }
                }
            } catch (\Yana\Core\Exceptions\Translations\TranslationException $e) {
                unset($e);
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
     * @return  string
     */
    public function getText()
    {
        if (!isset($this->text)) {
            $this->text = "";

            try {
                $language = self::getDependencyContainer()->getLanguage(); // may throw TranslationException

                $id = get_class($this);
                if ($language->isVar($id . ".p")) {
                    $this->text = (string) $language->getVar($id . ".p");
                    if (!empty($this->data)) {
                        $this->text = \Yana\Util\Strings::replaceToken($this->text, $this->data);
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
            } catch (\Yana\Core\Exceptions\Translations\TranslationException $e) {
                unset($e);
                $this->text = $this->message;
            }

            $this->text = preg_replace('/\s/', ' ', $this->text);
            $this->text = mb_substr($this->text, 0, 1000);
            $this->text = str_replace('"', '&quot;', $this->text);
        }
        return $this->text;
    }

}

?>
