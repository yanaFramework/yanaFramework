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

/**
 * <<Singleton>> Menu information
 *
 * @access      public
 * @name        PluginMenu
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 */
class PluginMenu extends Singleton implements IsSerializable
{
    /**
     * This is a place-holder for the singleton's instance
     *
     * @access  private
     * @static
     * @var     PluginMenu
     */
    private static $instance = null;

    /**
     * @access  private
     * @static
     * @var     string
     */
    private static $locale = null;

    /**
     * @access  private
     * @static
     * @var     array
     */
    private $names = array();

    /**
     * @access  private
     * @static
     * @var     array
     */
    private $plugins = array();

    /**
     * @access  private
     * @static
     * @var     array
     */
    private $entries = array();

    /**
     * @access  private
     * @static
     * @var     array
     */
    private $hasGroup = array();

    /**
     * constructor
     *
     * To prevent the constructor from being called directly
     *
     * @access private
     */
    private function __construct()
    {
        /**
         * initialize names
         */
        $pluginManager = PluginManager::getInstance();
        $pluginNames = $pluginManager->getPluginNames();
        $sessionManager = SessionManager::getInstance();

        foreach ($pluginNames as $i => $pluginName)
        {
            if ($pluginManager->isActive($pluginName)) {
                $pluginConfiguration = $pluginManager->getPluginConfiguration($pluginName);
                if ($pluginConfiguration->getGroup()) {
                    $this->hasGroup[$pluginName] = true;
                }
                foreach ($pluginConfiguration->getMenuNames() as $entry)
                {
                    if (!isset($entry[PluginAnnotation::GROUP])) {
                        $message = "Error in plugin configuration '" . $pluginConfiguration->getTitle() . "'. " .
                            "Menu definition is missing setting 'group'.";
                        Log::report($message, E_USER_WARNING);
                        continue;
                    }
                    if (isset($entry[PluginAnnotation::TITLE])) {
                        $title = $entry[PluginAnnotation::TITLE];
                    }
                    $group = $entry[PluginAnnotation::GROUP];
                    if (empty($title)) {
                        $title = $pluginConfiguration->getTitle();
                    }
                    if (!empty($group)) {
                        $this->names[$group] = $title;
                        $this->plugins[$group] = $pluginName;
                    }
                } /* end foreach */
            } else {
                unset($pluginNames[$i]);
            } /* end if */
        } /* end foreach */

        /**
         * initialize entries
         */
        foreach ($pluginNames as $pluginName)
        {
            if ($pluginManager->isActive($pluginName)) {
                $pluginConfiguration = $pluginManager->getPluginConfiguration($pluginName);
                foreach ($pluginConfiguration->getMenuEntries() as $action => $entry)
                {
                    if (!$sessionManager->checkPermission(null, $action)) {
                        continue;
                    }
                    // check for @menu title: foo
                    if (!empty($entry[PluginAnnotation::TITLE])) {
                        $title = $entry[PluginAnnotation::TITLE];
                    // otherwise look for @title tag
                    } else {
                        $title = $pluginConfiguration->getMethod($action)->getName();
                    }
                    // if everything else fails, fall back to plugin title
                    if (empty($title)) {
                        $title = $pluginConfiguration->getTitle();
                    }
                    if (empty($entry[PluginAnnotation::IMAGE])) {
                        $image = $pluginConfiguration->getIcon();
                    } else {
                        $image = $pluginConfiguration->getDirectory() . '/' . $entry[PluginAnnotation::IMAGE];
                    }
                    if (empty($entry[PluginAnnotation::GROUP])) {
                        $group = "";
                    } else {
                        $group = $entry[PluginAnnotation::GROUP];
                    }
                    $safemode = $pluginConfiguration->getMethod($action)->getSafeMode();
                    $entry = array(PluginAnnotation::IMAGE => $image, 
                                   PluginAnnotation::TITLE => $title,
                                   PluginAnnotation::SAFEMODE => $safemode);
                    Hashtable::set($this->entries, "$group.$action", $entry);
                } /* end foreach */
            } /* end if */
        } /* end foreach */
    }

    /**
     * get instance of this class
     *
     * Creates an instance if there is none.
     * Then it returns a reference to this (single) instance.
     *
     * @access  public
     * @static
     * @return  PluginMenu
     */
    public static function &getInstance()
    {
        if (!isset(self::$instance)) {

            $id = self::_getLocale();
            /*
             * load from cache
             */
            if (isset($_SESSION[__CLASS__][$id])) {
                 self::$instance = unserialize($_SESSION[__CLASS__][$id]);
                 assert('self::$instance instanceof PluginMenu;');

            /*
             * create cache
             */
            } else {
                self::$instance = new PluginMenu();
                $_SESSION[__CLASS__][$id] = serialize(self::$instance);
            }
        }
        return self::$instance;
    }

    /**
     * clear cache
     *
     * Deletes all temporary instances in session cache.
     *
     * @access  public
     * @static
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
     * @access  private
     * @static
     * @return  string
     */
    private static function _getLocale()
    {
        if (!isset(self::$locale)) {
            self::$locale = Language::getInstance()->getLocale();
        }
        return self::$locale;
    }

    /**
     * get list of menus
     *
     * Returns a list of all menus as a numeric array.
     *
     * @access  public
     * @return  array
     */
    public function getMenus()
    {
        return array_keys($this->entries);
    }

    /**
     * add menu entry
     *
     * Adds an entry to a menu of your choice.
     * If no menu is specified, the entry is added to the root-level.
     *
     * @access  public
     * @param   string  $action    name of action
     * @param   string  $title     title of your choice
     * @param   string  $menuName  menu where entry should be added, blank means root-level
     * @param   string  $icon      URL to an icon of your choice
     */
    public function setMenuEntry($action, $title, $menuName = "", $icon = "")
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        assert('is_string($title); // Wrong type for argument 2. String expected');
        assert('is_string($menuName); // Wrong type for argument 3. String expected');
        assert('is_string($icon); // Wrong type for argument 4. String expected');

        $this->entries[$menuName][$action] = array
            (
                PluginAnnotation::TITLE => $title,
                PluginAnnotation::IMAGE => $icon,
                PluginAnnotation::GROUP => $menuName
            );
    }

    /**
     * remove menu entry
     *
     * Returns bool(true) on success and bool(false) if the entry does not exist.
     *
     * @access  public
     * @param   string  $action    name of action
     * @param   string  $menuName  set this entry is inside a menu
     * @return  bool
     */
    public function unsetMenuEntry($action, $menuName = "")
    {
        assert('is_string($action); // Wrong type for argument 1. String expected');
        assert('is_string($menuName); // Wrong type for argument 2. String expected');
        if (isset($this->entries[$menuName][$action])) {
            unset($this->entries[$menuName][$action]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * set menu name
     *
     * Set a name for a menu of your choice.
     *
     * @access  public
     * @param   string  $menu  menu where entry should be added, blank means root-level
     * @param   string  $name  name of your choice
     */
    public function setMenuName($menu, $name = "")
    {
        assert('is_string($menuName); // Wrong type for argument 1. String expected');
        assert('is_string($name); // Wrong type for argument 2. String expected');
        $this->names[$menu] = $name;
    }

    /**
     * get menu entries
     *
     * Returns an array of all entries of a specific menu,
     * or all menus if none is specified, as a associative
     * array.
     *
     * @access  public
     * @param   string  $menuName  filter by menu name
     * @return  array
     */
    public function getMenuEntries($menuName = null)
    {
        assert('is_null($menuName) || is_string($menuName); // Wrong type for argument 1. String expected');
        if (empty($menuName)) {
            return $this->entries;
        } elseif (isset($this->entries[$menuName])) {
            return $this->entries[$menuName];
        } else {
            return array();
        }
    }

    /**
     * get menu name
     *
     * Returns the label of the given menu as a string.
     * If the menu is unknown the id is returned instead.
     *
     * @access  public
     * @param   string  $menuId  menu id to look up
     * @return  string
     */
    public function getMenuName($menuId)
    {
        if (isset($this->names[$menuId])) {
            return Language::getInstance()->replaceToken($this->names[$menuId]);
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
     * @access  public
     * @static
     * @return  array
     */
    public static function getTextMenu()
    {
        $pluginMenu = PluginMenu::getInstance();
        $pluginManager = PluginManager::getInstance();
        $sessionManager = SessionManager::getInstance();
        $isSafemode = Yana::getId() === Yana::getDefault('profile');
        $menu = array();

        foreach ($pluginMenu->getMenuEntries() as $menuId => $menuEntries)
        {
            $pluginId = $pluginMenu->plugins[$menuId];
            if (empty($pluginMenu->hasGroup[$pluginId]) || $pluginManager->isLoaded($pluginId)) {
                $pluginMenu->_getMenu($menu, $menuId, $menuEntries, $pluginManager, $isSafemode);
            }
        }

        return $menu;
    }

    /**
     * get menu entries
     *
     * @access  private
     * @param   array           &$menu          menu
     * @param   string          $menuId         menuID
     * @param   array           $menuEntries    menu entries
     * @param   PluginManager   $pluginManager  plugin manager
     * @param   bool            $isSafemode     for safemode set true , false otherweise
     */
    private function _getMenu(array &$menu, $menuId, array $menuEntries, PluginManager $pluginManager, $isSafemode)
    {
        $name = $this->getMenuName($menuId);

        foreach ($menuEntries as $action => $entry)
        {
            // is entry
            if (isset($entry[PluginAnnotation::TITLE])) {
                $safemode = $entry[PluginAnnotation::SAFEMODE];
                if (!is_null($safemode) && $isSafemode !== $safemode) {
                    continue;
                }
                $url = SmartUtility::url("action=$action", true);
                $label = Language::getInstance()->replaceToken($entry[PluginAnnotation::TITLE]);
                if (!empty($name)) {
                    $menu[$name][$url] = $label;
                } else {
                    $menu[$url] = $label;
                }
            // is menu
            } else {
                if (!isset($menu[$name])) {
                    $menu[$name] = array();
                }
                $this->_getMenu($menu[$name], "$menuId.$action", $entry, $pluginManager, $isSafemode);
            }
        }
    }

    /**
     * serialize this object to a string
     *
     * Returns the serialized object as a string.
     *
     * @access  public
     * @return  string
     */
    public function serialize()
    {
        return serialize($this);
    }

    /**
     * unserialize a string to a serializable object
     *
     * Returns the unserialized object.
     *
     * @access  public
     * @static
     * @param   string  $string  string to unserialize
     * @return  IsSerializable
     */
    public static function unserialize($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        if (!isset(self::$instance)) {
            self::$instance = unserialize($string);
            return self::$instance;
        } else {
            return self::$instance;
        }
    }

}
?>