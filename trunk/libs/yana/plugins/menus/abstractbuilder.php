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

namespace Yana\Plugins\Menus;

/**
 * Creates the main application menu.
 *
 * Note: there is by definition only 1 main application menu.
 * Which makes it a de-facto singleton.
 * Thus calling this builder twice will always give you the same instance.
 *
 * @name        PluginMenu
 * @package     yana
 * @subpackage  plugins
 */
abstract class AbstractBuilder extends \Yana\Core\Object implements \Yana\Plugins\Menus\IsBuilder
{

    /**
     * @var  string
     */
    private $_locale = "";

    /**
     * @var  \Yana\Plugins\Menus\MenuCollection
     */
    private static $_menus = null;

    /**
     * Returns the menu collection.
     *
     * @return  \Yana\Plugins\Menus\MenuCollection
     */
    protected function _getMenus()
    {
        if (!isset(self::$_menus)) {
            self::$_menus = new \Yana\Plugins\Menus\MenuCollection();
        }
        return self::$_menus;
    }

    /**
     * Returns the current menu id depending on the current locale settings.
     *
     * Defaults to the selected locale of the application if none was selected.
     *
     * @return  string
     */
    public function getLocale()
    {
        if ($this->_locale === "") {
            $this->_locale = \Yana\Translations\Facade::getInstance()->getLocale();
        }
        return $this->_locale;
    }

    /**
     * Select the locale that should be used to translate the menu entries.
     *
     * The "locale" functions as the "name" of the menu.
     *
     * YES: we wrote earlier that there is only 1 "main application menu",
     * and this is still true.
     * BUT: the items of this "main application menu" may have X translations.
     * Thus the "locale" is required to identify the language that should be used to create it.
     *
     * @param   string  $locale  LOCALE consisting of Language and (optional) country code
     * @return  \Yana\Plugins\Menus\Builder
     */
    public function setLocale($locale)
    {
        assert('is_string($locale); // Invalid argument type: $locale. String expected.');
        $this->_locale = (string) $locale;
        return $this;
    }

    /**
     * Build main application menu.
     *
     * Note: there is by definition only 1 main application menu.
     * Which makes it a de-facto singleton.
     * Thus calling this builder twice (for the same locale) will give you the same instance.
     *
     * @return  \Yana\Plugins\Menus\IsMenu
     */
    public function buildMenu()
    {
        assert('!isset($locale); // Cannot redeclare var $locale');
        $locale = $this->getLocale();
        assert('!isset($menus); // Cannot redeclare var $menus');
        $menus = $this->_getMenus();

        if (!isset($menus[$locale]) || !$menus[$locale] instanceof \Yana\Plugins\Menus\IsMenu) {

            $textMenuBuilder = new \Yana\Plugins\Menus\TextMenuBuilder();
            assert('!isset($menu); // Cannot redeclare var $menu');
            $menu = new \Yana\Plugins\Menus\Menu($textMenuBuilder);

            /* @var $pluginConfiguration \Yana\Plugins\Configs\ClassConfiguration */
            assert('!isset($pluginConfiguration); // Cannot redeclare var $pluginConfiguration');
            foreach ($this->_getListOfActivePlugins() as $pluginConfiguration)
            {
                $this->_determineMenuNames($menu, $pluginConfiguration);
                $this->_determineMenuEntries($menu, $pluginConfiguration);
            }
            unset($pluginConfiguration);

            $menus[$locale] = $menu;
        }

        return $menus[$locale];
    }

    /**
     * Determine menu group names.
     *
     * The main application menu has sub-menus (called "groups" of menu-entries).
     * For example the "start" menu-group, or the "setup" menu-group.
     *
     * Each plugin may define additional menu-groups of its own.
     * Typically these will be application menues like a "events" or "tasks" menu-groups for a "calendar" plugin aso.
     *
     * The names for these groups are defined by the plugin itself.
     * If no specific name is given, the name of the plugin will be used as the name of the group by default.
     * (This makes sense as most plugins define only one additional group, which usually identifies the plugin itself.)
     *
     * Meaning: that there may be a group with a name. However, there must not be a name without a group.
     *
     * @param   \Yana\Plugins\Menus\IsMenu                $menu
     * @param   \Yana\Plugins\Configs\ClassConfiguration  $pluginConfiguration
     * @return  \Yana\Plugins\Menus\IsMenu
     */
    protected function _determineMenuNames(\Yana\Plugins\Menus\IsMenu $menu,\Yana\Plugins\Configs\ClassConfiguration $pluginConfiguration)
    {
        /* @var $menuEntry \Yana\Plugins\Menus\IsEntry */
        foreach ($pluginConfiguration->getMenuNames() as $menuEntry)
        {
            if (!$menuEntry->getGroup()) {
                $this->getLogger()
                    ->addLog("Error in plugin configuration '" . $pluginConfiguration->getTitle() . "'. " .
                        "Menu definition is missing setting 'group'.", \Yana\Log\TypeEnumeration::WARNING);
                continue;
            }
            $title = $menuEntry->getTitle();
            $group = $menuEntry->getGroup();
            if (empty($title)) {
                $title = $pluginConfiguration->getTitle();
            }
            $menu->setMenuName($group, $title);
        } // end foreach
        return $menu;
    }

    /**
     * Finds and attaches the menu entries to the menu.
     *
     * If the user doesn't have the required permissions to click the entry, it will be discarded.
     * 
     * @param   \Yana\Plugins\Menus\IsMenu                $menu
     * @param   \Yana\Plugins\Configs\ClassConfiguration  $pluginConfiguration
     * @return  \Yana\Plugins\Menus\IsMenu
     */
    protected function _determineMenuEntries(\Yana\Plugins\Menus\IsMenu $menu, \Yana\Plugins\Configs\ClassConfiguration $pluginConfiguration)
    {
        /* @var $menuEntry \Yana\Plugins\Menus\IsEntry */
        foreach ($pluginConfiguration->getMenuEntries() as $action => $menuEntry)
        {
            if (!\Yana\Security\Data\SessionManager::getInstance()->checkPermission(null, $action)) {
                continue;
            }
            $menu->setMenuEntry($action, $this->_completeMenuEntry($action, $menuEntry, $pluginConfiguration));
        } // end foreach
        return $menu;
    }

    /**
     * Auto-complete any missing information of the entry using the plugin configuration.
     *
     * Missing information would be (for example) a title.
     * Also checks if the given icon exists aso.
     *
     * @param   string                                    $action               value to send as "action" parameter when the item is clicked
     * @param   \Yana\Plugins\Menus\IsEntry               $menuEntry            the basic item configuration
     * @param   \Yana\Plugins\Configs\ClassConfiguration  $pluginConfiguration  the plugin configuration used to complete missing item-properties
     * @return  \Yana\Plugins\Menus\IsEntry
     */
    protected function _completeMenuEntry($action, \Yana\Plugins\Menus\IsEntry $menuEntry, \Yana\Plugins\Configs\ClassConfiguration $pluginConfiguration)
    {
        // check if title is set
        if (!$menuEntry->getTitle()) {
            // otherwise check if method has a title
            $title = $pluginConfiguration->getMethod($action)->getTitle();
            // if not, take the plugin title
            if (empty($title)) {
                $title = $pluginConfiguration->getTitle();
            }
            $menuEntry->setTitle($title);
        }
        // check if icon is set
        $directory = $pluginConfiguration->getDirectory() . '/';
        if ($menuEntry->getIcon()) {
            // try to find icon in plugin directory
            if (is_file($directory . $menuEntry->getIcon())) {
                $menuEntry->setIcon($directory . $menuEntry->getIcon());
            }
        } else {
            // or take the plugin's icon
            if (is_file($pluginConfiguration->getIcon())) {
                $menuEntry->setIcon($pluginConfiguration->getIcon());
            }
        }
        // copy safemode setting
        $menuEntry->setSafeMode($pluginConfiguration->getMethod($action)->getSafeMode());

        return $menuEntry;
    }

    /**
     * Load list of active plugin settings using the plugin manager.
     *
     * @return  \Yana\Plugins\Configs\ClassConfiguration[]
     */
    protected function _getListOfActivePlugins()
    {
        assert('!isset($pluginManager); // Cannot redeclare var $pluginManager');
        $pluginManager = \Yana\Plugins\Manager::getInstance();
        /* @var $pluginManager \Yana\Plugins\Manager */
        assert('!isset($plugins); // Cannot redeclare var $plugins');
        $plugins = $pluginManager->getPluginConfigurations()->toArray();

        /* @var $pluginConfiguration PluginConfigurationClass */
        foreach (array_keys($plugins) as $pluginName)
        {
            if (!$pluginManager->isActive($pluginName)) {
                unset($plugins[$pluginName]);
            }
        }
        unset($pluginName);

        return $plugins;
    }

}

?>