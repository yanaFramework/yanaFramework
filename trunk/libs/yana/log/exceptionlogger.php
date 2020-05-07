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
 * Prints all messages to the screen.
 *
 * @package    yana
 * @subpackage log
 */
class ExceptionLogger extends \Yana\Log\AbstactLogger
{

    /**
     * @var  \Yana\Core\IsVarContainer
     */
    private $_inputContainer = null;

    /**
     * @var  \Yana\Log\ViewHelpers\MessageCollection
     */
    private $_resultObject = null;

    /**
     * Initializes the output result.
     *
     * @param  \Yana\Core\IsVarContainer  $inputContainer   some container to take messages from
     */
    public function __construct(\Yana\Core\IsVarContainer $inputContainer)
    {
        $this->_inputContainer = $inputContainer;
        $this->_resultObject = new \Yana\Log\ViewHelpers\MessageCollection();
    }

    /**
     * Returns container with translation strings.
     *
     * @return  \Yana\Core\IsVarContainer
     */
    protected function _getInputContainer()
    {
        return $this->_inputContainer;
    }

    /**
     * Returns the result object.
     *
     * @return  \Yana\Log\ViewHelpers\MessageCollection
     */
    protected function _getResultObject()
    {
        return $this->_resultObject;
    }

    /**
     * This implements the logging behavior.
     *
     * @param   string  $message  the message that should be reported
     * @param   int     $level    numeric level of severity
     * @param   mixed   $data     any kind of data that might help to understand context in which the message was created
     */
    public function addException(\Yana\Core\Exceptions\IsException $exception)
    {
        if ($this->_isAcceptable($exception->getCode())) {
            $resultObject = $this->_getResultObject();
            $resultObject[] = $this->_toMessage($exception);
            $resultObject->updateLevel($exception->getCode());
        }
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
        if ($level !== \Yana\Log\TypeEnumeration::SUCCESS) {
            $exception = new \Yana\Core\Exceptions\LogicException($message, $level);
        } else {
            $exception = new \Yana\Core\Exceptions\Messages\SuccessMessage($message);
        }
        $exception->setData($data);
        $this->addException($exception);
    }

    /**
     * Returns the message text (if any).
     *
     * If there is none, an empty array is returned.
     *
     * @return  \Yana\Log\ViewHelpers\Message
     */
    protected function _toMessage(\Yana\Core\Exceptions\IsException $exception): \Yana\Log\ViewHelpers\Message
    {
        $viewMessage = new \Yana\Log\ViewHelpers\Message();

        $className = get_class($exception);

        while (is_string($className))
        {
            if ($this->_getInputContainer()->isVar($className)) {
                $text = $this->_getInputContainer()->getVar($className);
                if (isset($text['h'])) {
                    $header = \Yana\Util\Strings::replaceToken($text['h'], $exception->getData());
                    $viewMessage->setHeader($header);
                }
                if (isset($text['p'])) {
                    $paragraph = \Yana\Util\Strings::replaceToken($text['p'], $exception->getData());
                    $viewMessage->setText($paragraph);
                }
                break;
            }
            $className = \get_parent_class($className);
        }

        if ($viewMessage->getHeader() === "" && $viewMessage->getText() === "") {
            $text = \Yana\Util\Strings::htmlEntities($exception->getMessage() . "; " . print_r($exception->getData(), true));
            $viewMessage->setText($text);
        }
        return $viewMessage;
    }

    /**
     * Build and return result object
     *
     * @return  \Yana\Log\ViewHelpers\MessageCollection
     */
    public function getMessages()
    {
        return $this->_resultObject;
    }
}

?>