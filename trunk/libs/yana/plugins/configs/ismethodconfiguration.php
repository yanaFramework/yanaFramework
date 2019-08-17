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
 * <<interface>> Plugin Method information
 *
 * Represents a plugin method's interface, name, description, and more.
 *
 * @package     yana
 * @subpackage  plugins
 */
interface IsMethodConfiguration
{

    /**
     * Get method type.
     *
     * @return  string
     */
    public function getType(): string;

    /**
     * Get directory.
     *
     * @return  string
     */
    public function getPath(): string;

    /**
     * Fet directory names of subscribing plugins.
     *
     * This includes the path of the implementing method, as it always subscribes to itself.
     *
     * @return  array
     */
    public function getPaths(): array;

    /**
     * Get Javascript files.
     *
     * Returns a list of all associated javascript files.
     * These are loaded together with the template.
     *
     * @return  array
     */
    public function getScripts(): array;

    /**
     * Get CSS-styles.
     *
     * Returns a list of all associated CSS files.
     * These are loaded together with the template.
     *
     * @return  array
     */
    public function getStyles(): array;

    /**
     * Get language files.
     *
     * Returns a list of all associated XLIFF files.
     * These are loaded together with the template.
     *
     * @return  array
     */
    public function getLanguages(): array;

    /**
     * Get parameters.
     *
     * Returns a collection of all parameters, which each member being an instance of {@see \Yana\Plugins\Configs\IsMethodParameter}
     *
     * @return  \Yana\Plugins\Configs\IsMethodParameterCollection
     */
    public function getParams(): \Yana\Plugins\Configs\IsMethodParameterCollection;

    /**
     * Get return value.
     *
     * Returns the methods return value.
     *
     * @return  string
     */
    public function getReturn(): string;

    /**
     * Get group.
     *
     * Returns the method's group (if any).
     * This is similar to a "package" in OO-style programming languages.
     *
     * A group may have multiple plugins, but a plugin may only be a member of one group.
     *
     * @return  string
     */
    public function getGroup(): string;

    /**
     * Get menu entry.
     *
     * Each plugin may define it's own menues and add entries to them. The names
     * are defined in the file's doc-block, while the menu entries are defined
     * at the methods that are to be added to the menu.
     *
     * Use this function to get the menu entry defined by the method (if any).
     *
     * @return  \Yana\Plugins\Menus\IsEntry
     */
    public function getMenu(): ?\Yana\Plugins\Menus\IsEntry;

    /**
     * Get settings on how to react on success.
     *
     * @return  \Yana\Plugins\Configs\EventRoute
     */
    public function getOnSuccess();

    /**
     * Get settings on how to react on error.
     *
     * @return  \Yana\Plugins\Configs\EventRoute
     */
    public function getOnError(): ?\Yana\Plugins\Configs\EventRoute;

    /**
     * Get human readable name.
     *
     * Returns the name (title) as defined in the method's doc block.
     *
     * @return  string
     */
    public function getTitle(): string;

    /**
     * get safemode setting of method
     *
     * Returns value of safemode setting.
     *
     * It is:
     *  bool(true) for "safemode must be active",
     *  bool(false) for "safemode must NOT be active",
     *  or NULL for "don't care".
     *
     * @return  bool
     */
    public function getSafeMode(): ?bool;

    /**
     * Get template path.
     *
     * @return  string
     */
    public function getTemplate(): string;

    /**
     * Get user security levels.
     *
     * Returns a list of instances of PluginUserLevel.
     *
     * @return  \Yana\Plugins\Configs\IsUserPermissionRule[]
     */
    public function getUserLevels(): array;

    /**
     * get overwrite setting of method
     *
     * Returns value of overwrite setting.
     *
     * A method my overwrite the method of it's parent plugin.
     * To do so, it defines the annotation "overwrite".
     * The annotation is a flag, that has no special value.
     *
     * This has no effect if the plugin does not define a parent.
     *
     * @return  bool
     */
    public function getOverwrite(): bool;

    /**
     * Get subscribe setting of method.
     *
     * Returns value of subscribe setting.
     *
     * A method may subscribe to an event that it doesn't define itself.
     * If so, it uses the annotation "subscribe" and must NOT use other
     * annotations to change the type of event et cetera.
     *
     * Note: you may NOT use the annotations "overwrite" and "subscribe" at
     * the same time.
     *
     * @return  bool
     */
    public function getSubscribe(): bool;

    /**
     * Get class name.
     *
     * @return  string
     */
    public function getClassName(): string;

    /**
     * Get method name.
     *
     * @return  string
     */
    public function getMethodName(): string;

    /**
     * Check if the function uses a generic, unchecked parameter list.
     *
     * @return  bool
     */
    public function hasGenericParams(): bool;

    /**
     * Executes the event on the provided instance and returns the result.
     *
     * @param   \Yana\IsPlugin  $instance  object to send event to
     * @return  mixed
     */
    public function sendEvent(\Yana\IsPlugin $instance);

    /**
     * Plug-in has method?
     *
     * Returns bool(true) if the given plug-in implements this method and bool(false) otherwise.
     *
     * @param   \Yana\IsPlugin  $instance  object to send event to
     * @return  bool
     */
    public function hasMethod(\Yana\IsPlugin $instance): bool;

}

?>