<?php
/**
 * YANA library
 *
 * Primary controller class
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

namespace Yana\Core\Dependencies;

/**
 * <<interface>> Dependency container for the application class.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsApplicationContainer
    extends
    \Yana\Core\Dependencies\IsExceptionContainer,
    \Yana\Core\Dependencies\IsViewContainer,
    \Yana\Core\Dependencies\IsFormContainer,
    \Yana\Core\Dependencies\IsSecurityContainer,
    \Yana\Core\Dependencies\IsPluginContainer
{

    /**
     * Get exception logger.
     *
     * Builds and returns a class that converts exceptions to messages and passes them as var
     * "STDOUT" to a var-container for output in a template or on the command line.
     *
     * @return  \Yana\Log\ExceptionLogger
     */
    public function getExceptionLogger();

    /**
     * Get current action.
     *
     * @internal This also checks the action parameter for validity.
     *
     * Work-around for IE-bug.
     *
     * Example:
     * <code>
     * <form><button type="submit" name="a" value="1">2</button></form>
     * </code>
     *
     * IE sends a=2 instead of a=1. This is because IE automatically
     * handles button-tags as input tags and copies the caption text
     * to the value attribute. This is WRONG according to W3C.
     *
     * Solution:
     * <code>
     * <form><input type="submit" name="a[1]" value="2"/></form>
     * if ($a[1]) $a = 1;
     * </code>
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\InvalidActionException  when the event is undefined
     */
    public function getAction();

    /**
     * Get default configuration value.
     *
     * Returns the default value for a given var if any,
     * returns NULL (not false!) if there is none.
     *
     * Example 1:
     * <code>
     * \Yana\Application::getDefault('CONTAINER1.CONTAINER2.DATA');
     * </code>
     *
     * Example 2:
     * <code>
     * if (!isset($foo)) {
     *     $foo = \Yana\Application::getDefault('FOO');
     * }
     * </code>
     *
     * Note: system default values are typically defined in the
     * 'default' section of the 'config/system.config' configurations file.
     *
     * @param   string  $key  adress of data in memory (case insensitive)
     * @return  mixed
     */
    public function getDefault($key);

    /**
     * Application is in safe-mode.
     *
     * @return  bool
     */
    public function isSafemode();

    /**
     * get skin
     *
     * This returns the skin component. If none exists, a new instance is created.
     *
     * @return  \Yana\Views\Skins\IsSkin
     */
    public function getSkin();

    /**
     * XML default configuration object.
     *
     * @return  \Yana\Util\Xml\IsObject
     */
    public function getDefaultConfiguration();

}

?>