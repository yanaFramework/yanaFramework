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

namespace Yana\Plugins\Configs;

/**
 * Plugin event routing configuration.
 *
 * This class informs the framework what to do, if a plugin triggers a certain event.
 *
 * @access      public
 * @package     yana
 * @subpackage  plugins
 * @ignore
 */
class EventRoute
{

    /**
     * Predefined event code.
     */
    const CODE_SUCCESS = 0;
    /**
     * Predefined event code.
     */
    const CODE_ERROR = 1;

    /**
     * Predefined event message.
     */
    const MSG_SUCCESS = '\Yana\Core\Exceptions\Messages\SuccessMessage';
    /**
     * Predefined event message.
     */
    const MSG_ERROR = '\Yana\Core\Exceptions\LogicException';

    /**
     * Type of event triggered.
     *
     * E.g. success, error, exception, warning, ...
     *
     * @access private
     * @var    string
     */
    private $_code = self::CODE_SUCCESS;

    /**
     * Name of plugin-method to route to.
     *
     * @access private
     * @var    string
     */
    private $_target = "";

    /**
     * Message to display in GUI.
     *
     * May be a language token.
     * Might be left emtpy to trigger auto-detect.
     *
     * @access private
     * @var    string
     */
    private $_message = "";

    /**
     * Get type of event triggered.
     *
     * @access  public
     * @return  int
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * Set type of event triggered.
     *
     * Use this to set error codes.
     * Error codes should be > 0.
     * Success code should be 0.
     *
     * @access  public
     * @param   int  $code  unsigned small int, defaults: CODE_SUCCESS, CODE_ERROR
     * @return  \Yana\Plugins\Configs\EventRoute
     */
    public function setCode($code)
    {
        assert('is_int($code); // Invalid argument $code: int expected');
        $this->_code = (int) $code;
        return $this;
    }

    /**
     * Get name of plugin-method to route to.
     *
     * @access  public
     * @return  string
     */
    public function getTarget()
    {
        return $this->_target;
    }

    /**
     * Set name of plugin-method to route to.
     *
     * @access  public
     * @param   string  $target  PHP method name
     * @return  \Yana\Plugins\Configs\EventRoute
     */
    public function setTarget($target)
    {
        assert('is_string($target); // Invalid argument $target: string expected');
        $this->_target = (string) $target;
        return $this;
    }

    /**
     * Get message to display in GUI.
     *
     * @access  public
     * @return  string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Set message to display in GUI.
     *
     * While this setting may be any string,
     * you are best advised to use a class-name that is derived from class Report.
     * These class names should be mapped to appropriate translations using the "message" translation files.
     *
     * @access  public
     * @param   string  $message  default messages: MSG_SUCCESS and MSG_ERROR
     * @return  \Yana\Plugins\Configs\EventRoute
     */
    public function setMessage($message)
    {
        assert('is_string($message); // Invalid argument $message: string expected');
        $this->_message = (string) $message;
        return $this;
    }

}

?>