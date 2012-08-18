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

namespace Yana\Plugins;

/**
 * <<Singleton>> Menu information
 *
 * @name        PluginMenu
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class Menu extends \Yana\Core\AbstractSingleton
{

    /**
     * This is a place-holder for the singleton's instance
     *
     * @var  \Yana\Plugins\Menu
     */
    private static $_instance = null;

    /**
     * @var  string
     */
    private static $_locale = null;

    /**
     * @var  array
     */
    private $_names = array();

    /**
     * @var  array
     */
    private $_plugins = array();

    /**
     * @var  array
     */
    private $_entries = array();

    /**
     * @var  array
     */
    private $_hasGroup = array();

    /**
     * constructor
     *
     * To prevent the constructor from being called directly
     */
    private function __construct()
    {
        /**
         * initialize names
         */
        $pluginManager = \Yana\Plugins\Manager::getInstance();
        $plugins = $pluginManager->getPluginConfigurations()->toArray();

        /* @var $pluginConfiguration PluginConfigurationClass */
        foreach ($plugins as $pluginName => $pluginConfiguration)
        {
            if ($pluginManager->isActive($pluginName)) {
                if ($pluginConfiguration->getGroup()) {
                    $this->_hasGroup[$pluginName] = true;
                }
                foreach ($pluginConfiguration->getMenuNames() as $menuEntry)
                {
                    if (!$menuEntry->getGroup()) {
                        $message = "Error in plugin configuration '" . $pluginConfiguration->getTitle() . "'. " .
                            "Menu definition is missing setting 'group'.";
                        \Yana\Log\LogManager::getLogger()->addLog($message, E_USER_WARNING);
                        continue;
                    }
                    $title = $menuEntry->getTitle();
                    $group = $menuEntry->getGroup();
                    if (empty($title)) {
                        $title = $pluginConfiguration->getTitle();
                    }
                    $this->setMenuName($group, $title);
                    $this->_plugins[$group] = $pluginName;
                } // end foreach
            } else {
                unset($plugins[$pluginName]);
            } // end if
        } // end foreach

        $sessionManager = \SessionManager::getInstance();
        /**
         * initialize entries
         */
        foreach ($plugins as $pluginName => $pluginConfiguration)
        {
            /* @var $menuEntry \Yana\Plugins\MenuEntry */
            foreach ($pluginConfiguration->getMenuEntries() as $action => $menuEntry)
            {
                if (!$sessionManager->checkPermission(null, $action)) {
                    continue;
                }
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
                $this->setMenuEntry($action, $menuEntry);
            } // end foreach
        } // end foreach
    }

    /**
     * get instance of this class
     *
     * Creates an instance if there is none.
     * Then it returns a reference to this (single) instance.
     *
     * @return  \Yana\Plugins\Menu
     */
    public static function &getInstance()
    {
        if (!isset(self::$_instance)) {

            $id = self::_getLocale();
            /*
             * load from cache
             */
            if (isset($_SESSION[__CLASS__][$id])) {
                self::$_instance = unserialize($_SESSION[__CLASS__][$id]);
                assert('self::$_instance instanceof self;');

                /*
                 * create cache
                 */
            } else {
                self::$_instance = new \Yana\Plugins\Menu();
                $_SESSION[__CLASS__][$id] = serialize(self::$_instance);
            }
        }
        return self::$_instance;
    }

    /**
     * clear cache
     *
     * Deletes all temporary instances in session cache.
     *
     * @ignore
     */
    public static function clearCache()
    {
        unset($_SESSION[__CLASS__]);
    }

    /**
     * get menu id
     *
     * Returns the current menu id depending on the current locale settings.
     *
     * @return  string
     */
    private static function _getLocale()
    {
        if (!isset(self::$_locale)) {
            self::$_locale = \Yana\Translations\Language::getInstance()->getLocale();
        }
        return self::$_locale;
    }

    /**
     * Adds an entry to a menu of your choice.
     *
     * If no menu-group is specified, the entry is added to the root-level.
     *
     * @param   string  $action  name of action
     * @param   \Yana\Plugins\MenuEntry  $title     
     */
    public function setMenuEntry($action, \Yana\Plugins\MenuEntry $menuEntry)
    {
        assert('is_string($action); // Invalid argument $action: string expected');
        \Yana\Util\Hashtable::set($this->_entries, $menuEntry->getGroup() . ".$action", $menuEntry);
    }

    /**
     * remove menu entry
     *
     * Returns bool(true) on success and bool(false) if the entry does not exist.
     *
     * @param   string  $action    name of action
     * @param   string  $menuName  set this entry is inside a menu
     * @return  bool
     */
    public function unsetMenuEntry($action, $menuName = "")
    {
        assert('is_string($action); // Invalid argument $action: string expected');
        assert('is_string($menuName); // Invalid argument $menuName: string expected');
        return \Yana\Util\Hashtable::remove($this->_entries, $menuName . "." . $action);
    }

    /**
     * Set a name for a menu of your choice.
     *
     * @param   string  $menu  menu where entry should be added, blank means root-level
     * @param   string  $name  name of your choice
     */
    public function setMenuName($menu, $name = "")
    {
        assert('is_string($menu); // Invalid argument $menu: string expected');
        assert('is_string($name); // Invalid argument $name: string expected');
        $this->_names[$menu] = $name;
    }

    /**
     * get menu entries
     *
     * Returns an array of all entries of a specific menu,
     * or all menus if none is specified, as a associative
     * array.
     *
     * @param   string  $menuName  filter by menu name
     * @return  array
     */
    public function getMenuEntries($menuName = null)
    {
        assert('is_null($menuName) || is_string($menuName); // Wrong type for argument 1. String expected');
        if (empty($menuName)) {
            return $this->_entries;
        } else {
            $result = \Yana\Util\Hashtable::get($this->_entries, $menuName);
            if (is_array($result)) {
                return $result;
            } else {
                return array();
            }
        }
    }

    /**
     * get menu name
     *
     * Returns the label of the given menu as a string.
     * If the menu is unknown the id is returned instead.
     *
     * @param   string  $menuId  menu id to look up
     * @return  string
     */
    public function getMenuName($menuId)
    {
        if (isset($this->_names[$menuId])) {
            $language = \Yana\Translations\Language::getInstance();
            return $language->replaceToken($this->_names[$menuId]);
        } else {
            return $menuId;
        }
    }

    /**
     * return menu as associative array
     *
     * Extracts all menus and items.
     * Array Keys are menu names.
     * Value Keys are URLs, values are text labels.
     *
     * @return  array
     */
    public function getTextMenu()
    {
        $pluginManager = \Yana\Plugins\Manager::getInstance();
        $sessionManager = \SessionManager::getInstance();
        $isSafemode = \Yana::getId() === \Yana::getDefault('profile');
        $menu = array();

        foreach ($this->getMenuEntries() as $menuId => $menuEntries)
        {
            if (isset($this->_plugins[$menuId])) {
                $pluginId = $this->_plugins[$menuId];
                if (!empty($this->_hasGroup[$pluginId]) && !$pluginManager->isLoaded($pluginId)) {
                    continue;
                }
            }
            $this->_getMenu($menu, $menuId, $menuEntries, $pluginManager, $isSafemode);
        }

        return $menu;
    }

    /**
     * get menu entries
     *
     * @param   array           &$menu          output
     * @param   string          $menuId         index of menu
     * @param   array           $menuEntries    list of instances of PluginMenuEntry or sub-menus
     * @param   \Yana\Plugins\Manager   $pluginManager  plugin manager
     * @param   bool            $isSafemode     for safemode set true , false otherweise
     */
    private function _getMenu(array &$menu, $menuId, array $menuEntries, \Yana\Plugins\Manager $pluginManager, $isSafemode)
    {
        $name = $this->getMenuName($menuId);
        $urlFormatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();

        foreach ($menuEntries as $action => $entry)
        {
            if ($entry instanceof \Yana\Plugins\MenuEntry) {

                // is entry
                $safemode = $entry->getSafeMode();
                if (!is_null($safemode) && $isSafemode !== $safemode) {
                    continue;
                }
                $url = $urlFormatter("action=$action", true);
                $label = \Yana\Translations\Language::getInstance()->replaceToken($entry->getTitle());
                if (!empty($name)) {
                    $menu[$name][$url] = $label;
                } else {
                    $menu[$url] = $label;
                }

            } elseif (is_array($entry)) {

                // is menu
                if (!isset($menu[$name])) {
                    $menu[$name] = array();
                }
                $this->_getMenu($menu[$name], "$menuId.$action", $entry, $pluginManager, $isSafemode);

            }
        }
    }

    /**
     * Reinitialize instance.
     *
     * @access  public
     */
    public function __wakeup()
    {
        self::$_instance = $this;
    }

}

?>