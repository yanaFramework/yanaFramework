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
 * <<interface>> Plugin information.
 *
 * Represents a plugin's interface, name, description, and more.
 *
 * @package     yana
 * @subpackage  plugins
 */
interface IsClassConfiguration extends \Yana\Core\MetaData\IsPackageMetaData
{

    /**
     * Get plug-in's id.
     *
     * @return  string
     */
    public function getId();

    /**
     * Get plugin type.
     *
     * @return  string
     */
    public function getType();

    /**
     * Get authors.
     *
     * Returns a list of all authors.
     *
     * @return  array
     */
    public function getAuthors();

    /**
     * Get priority.
     *
     * Returns the plugin's priority level as an integer.
     * The loweset priority is 0. The higher the value, the higher the priority.
     *
     * @return  string
     */
    public function getPriority();

    /**
     * Get group.
     *
     * Returns the plugin's group (if any).
     * This is similar to a "package" in OO-style programming languages.
     *
     * A group may have multiple plugins, but a plugin may only be a member of one group.
     *
     * @return  string
     */
    public function getGroup();

    /**
     * Returns the plugin's parent plugin.
     *
     * This is when a plugin extends another by adding,
     * extending or overwriting methods.
     *
     * This is similar to a "parent class" in most OO-style programming languages.
     * A parent may have multiple child plugins, but a plugin may only have one parent.
     *
     * @return  string
     */
    public function getParent();

    /**
     * Returns the list of plugins who depend on this.
     *
     * @return  array
     */
    public function getDependencies();

    /**
     * Returns the plugin's license string.
     *
     * This tag is optional.
     *
     * @return  string
     */
    public function getLicense();

    /**
     * Get menu names.
     *
     * Each plugin may define it's own menues
     * and add entries to them. The names
     * are defined in the file's doc-block,
     * while the menu entries are defined
     * at the methods that are to be added to
     * the menu.
     *
     * Use this function to get all menu titles
     * defined by the plugin.
     *
     * @return  array
     */
    public function getMenuNames();

    /**
     * Get menu entries.
     *
     * Each plugin may define it's own menues
     * and add entries to them. The names
     * are defined in the file's doc-block,
     * while the menu entries are defined
     * at the methods that are to be added to
     * the menu.
     *
     * Use this function to get all menu entries
     * defined by methods.
     *
     * @param   string  $group  optionally limit entries to a certain group
     * @return  array
     */
    public function getMenuEntries($group = null);

    /**
     * Get directory where file is stored.
     *
     * Returns bool(false) on error.
     *
     * @return  string
     */
    public function getDirectory();

    /**
     * Get plugin's default active state.
     *
     * A plugin may define it's own prefered initial
     * active state. Default is 'inactive'.
     *
     * @return  int
     * @see     \Yana\Plugins\ActivityEnumeration
     */
    public function getActive();

    /**
     * Get URI to an icon image.
     *
     * @return  array
     */
    public function getIcon();

    /**
     * Get namespace name.
     *
     * @return  string
     */
    public function getNamespace();

    /**
     * Get class name.
     *
     * @return  string
     */
    public function getClassName();

    /**
     * Get method configuration.
     *
     * Returns the method configuration if it exists,
     * or NULL if there is none.
     *
     * @param   string  $methodName  name of method
     * @return  \Yana\Plugins\Configs\IsMethodConfiguration
     */
    public function getMethod($methodName);

    /**
     * Get method configurations.
     *
     * @return  \Yana\Plugins\Configs\MethodCollection
     */
    public function getMethods();

    /**
     * Check if plugin is active by default.
     *
     * @return  bool
     */
    public function isActiveByDefault();

}

?>