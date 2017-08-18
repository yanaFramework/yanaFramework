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

namespace Yana\Log\ViewHelpers;

/**
 * <<collection>> A list of view messages.
 *
 * @package    yana
 * @subpackage log
 */
class MessageCollection extends \Yana\Core\AbstractCollection
{

    /**
     * @var string
     */
    private $_level = \Yana\Log\ViewHelpers\MessageLevelEnumeration::ALERT;

    /**
     * Store new value in collection.
     *
     * @param   scalar                         $offset  where to place the value (may also be empty)
     * @param   \Yana\Log\ViewHelpers\Message  $value   new value to store
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not valid
     * @return  \Yana\Log\IsLogger
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof \Yana\Log\ViewHelpers\Message) {
            $message = "Instance of Message expected. Found " . gettype($value) . "(" .
                ((is_object($value)) ? get_class($value) : $value) . ") instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        return $this->_offsetSet($offset, $value);
    }

    /**
     * Returns the message level.
     *
     * @return  string
     */
    public function getLevel()
    {
        return $this->_level;
    }

    /**
     * Add error level setting.
     *
     * This compares the currently set error level with the given code.
     * If the code has a higher severity rating than the current, the code is updated.
     * Otherwise the current setting is kept untouched.
     *
     * @param   int  $errorCode  some E_*** constant
     * @return  \Yana\Log\ViewHelpers\MessageCollection
     */
    public function updateLevel($errorCode)
    {
        $levelName = $this->_toLevelName($errorCode);
        switch ($this->getLevel())
        {
            case \Yana\Log\ViewHelpers\MessageLevelEnumeration::MESSAGE:
            case \Yana\Log\ViewHelpers\MessageLevelEnumeration::ERROR:
                return $this;

            case \Yana\Log\ViewHelpers\MessageLevelEnumeration::WARNING:
                if ($levelName === \Yana\Log\ViewHelpers\MessageLevelEnumeration::ALERT) {
                    break;
                }
                // fall through

            case \Yana\Log\ViewHelpers\MessageLevelEnumeration::ALERT:
            default:
                $this->_level = $levelName;
        }
        return $this;
    }

    /**
     * Returns the messaging level as string.
     *
     * @param   int  $errorCode  some E_*** constant
     * @return  string
     */
    protected function _toLevelName($errorCode)
    {
        $level = \Yana\Log\ViewHelpers\MessageLevelEnumeration::ALERT;

        switch ($errorCode)
        {
            case \Yana\Log\TypeEnumeration::ERROR:
            case \E_USER_ERROR:
            case \E_ERROR:
            case \E_RECOVERABLE_ERROR: // An error that caused PHP to throw an exception
                $level = \Yana\Log\ViewHelpers\MessageLevelEnumeration::ERROR;
                break;
            case \Yana\Log\TypeEnumeration::WARNING:
            case \E_USER_WARNING:
            case \E_WARNING:
                $level = \Yana\Log\ViewHelpers\MessageLevelEnumeration::WARNING;
                break;
            case \Yana\Log\TypeEnumeration::SUCCESS:
                $level = \Yana\Log\ViewHelpers\MessageLevelEnumeration::MESSAGE;
                break;
        }
        return $level;
    }

}