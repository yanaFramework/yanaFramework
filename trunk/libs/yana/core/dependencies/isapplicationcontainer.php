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

namespace Yana\Core\Dependencies;

/**
 * <<interface>> Dependency container for the application class.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsApplicationContainer extends \Yana\Core\Dependencies\IsExceptionContainer
{

    /**
     * Builds and returns request object.
     *
     * By default this will be done by using the respective super-globals like $_GET, $_POST aso.
     *
     * @return  \Yana\Http\IsFacade
     */
    public function getRequest();

    /**
     * Get the application cache.
     *
     * By default this will be a file-cache in the temporary directory of the framework.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    public function getCache();

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
     * Get security facade.
     *
     * This facade is used to manage user information and check permissions.
     * 
     * @return \Yana\Security\IsFacade
     */
    public function getSecurity();

    /**
     * Application is in safe-mode.
     *
     * @return  bool
     */
    public function isSafemode();

    /**
     * Get registry.
     *
     * This returns the registry. If none exists, a new instance is created.
     * These settings may be read later by using \Yana\Application::getVar().
     *
     * @return  \Yana\VDrive\IsRegistry
     * @throws  \Yana\Core\Exceptions\NotReadableException    when Registry file is not readable
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when Registry file could not be read or contains invalid syntax
     */
    public function getRegistry();

    /**
     * Get plugin-manager.
     *
     * This returns the plugin manager. If none exists, a new instance is created.
     * The pluginManager holds repositories for interfaces and implementations of plugins.
     *
     * @return  \Yana\Plugins\Manager
     */
    public function getPlugins();

    /**
     * Get view.
     *
     * This returns the view component. If none exists, a new instance is created.
     * This is an auxiliary class that provides access to output-specific functions.
     *
     * @return  \Yana\Views\Managers\IsManager
     */
    public function getView();

    /**
     * Get language translation-repository.
     *
     * This returns the language component. If none exists, a new instance is created.
     *
     * @return  \Yana\Translations\IsFacade
     */
    public function getLanguage();

    /**
     * get skin
     *
     * This returns the skin component. If none exists, a new instance is created.
     *
     * @return  \Yana\Views\Skins\IsSkin
     */
    public function getSkin();

    /**
     * Get current profile id.
     *
     * Returns the id of the profile the data of the current profile is to be associated with.
     *
     * This is a shortcut for $YANA->getVar('ID').
     * However it is important to note a slight difference.
     * <ul>
     *   <li> $YANA->getVar('ID'):
     *     This value is available to all plugins and all 
     *     of them may read AND write this setting as the
     *     developer sees fit.
     *     This may mean, that this setting has been subject
     *     to changes by some plugin, e.g. to switch between
     *     profiles.
     *   </li>
     *   <li> $container->getId():
     *     Always returns the original value, regardless of
     *     changes by plugins.
     *   </li>
     * </ul>
     *
     * You may want to decide for the behaviour you prefer
     * and choose either one or the other.
     *
     * @return  string
     */
    public function getProfileId();

    /**
     * Returns the attached loggers.
     *
     * @return  \Yana\Log\LoggerCollection
     */
    public function getLogger();

    /**
     * Retrieve session wrapper.
     *
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    public function getSession();

    /**
     * Creates and returns an application menu builder.
     *
     * @param   \Yana\Application  $application  necessary to initialize dependency container
     * @return  \Yana\Plugins\Menus\IsCacheableBuilder
     */
    public function getMenuBuilder(\Yana\Application $application);

}

?>