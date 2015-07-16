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
 * Menu information
 *
 * @name        PluginMenu
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
class TextMenuBuilder extends \Yana\Core\Object implements \Yana\Plugins\Menus\IsTextMenuBuilder
{

    /**
     * @var  array
     */
    private $_pluginsWithGroups = array();

    /**
     * @var  array
     */
    private $_hasGroup = array();

    /**
     * @var  \Yana\Plugins\Manager
     */
    private $_pluginManager = null;

    /**
     * @var  \Yana\Translations\Facade
     */
    private $_translationManager = null;

    /**
     * @return  \Yana\Plugins\Manager
     */
    public function getPluginManager()
    {
        if (!isset($this->_pluginManager)) {
            $this->_pluginManager = \Yana\Plugins\Manager::getInstance();
        }
        return $this->_pluginManager;
    }

    /**
     * @return  \Yana\Translations\Facade
     */
    public function getTranslationManager()
    {
        if (!isset($this->_translationManager)) {
            $this->_translationManager = \Yana\Translations\Facade::getInstance();
        }
        return $this->_translationManager;
    }

    /**
     * 
     * @param   \Yana\Plugins\Manager  $pluginManager  instance to inject
     * @return  \Yana\Plugins\Menus\TextMenuBuilder
     */
    public function setPluginManager(\Yana\Plugins\Manager $pluginManager)
    {
        $this->_pluginManager = $pluginManager;
        return $this;
    }

    public function setTranslationManager(\Yana\Translations\Facade $translationManager)
    {
        $this->_translationManager = $translationManager;
        return $this;
    }

    /**
     * Check whether plugin defines a menu-group.
     *
     * @param   string  $pluginName  name of plugin
     * @return  bool
     */
    protected function _hasGroup($pluginName)
    {
        assert('is_string($pluginName); // Invalid argument $pluginName: string expected');
        if (empty($this->_hasGroup)) {

            /* @var $pluginConfiguration PluginConfigurationClass */
            foreach ($this->getPluginManager()->getPluginConfigurations()->toArray() as $pluginName => $pluginConfiguration)
            {
                if ($pluginConfiguration->getGroup()) {
                    $this->_hasGroup[$pluginName] = true;
                }
            } // end foreach
        }

        return !empty($this->_hasGroup[$pluginName]);
    }

    /**
     * Get plugin name by group id.
     *
     * @param   string  $menuId  menu name
     * @return  string
     */
    protected function _getPluginNameByGroupId($menuId)
    {
        assert('is_string($menuId); // Invalid argument $menuId: string expected');
        if (empty($this->_pluginsWithGroups)) {
            $plugins = $this->getPluginManager()->getPluginConfigurations()->toArray();

            /* @var $pluginConfiguration PluginConfigurationClass */
            assert('!isset($pluginName); // Cannot redeclare var $pluginName');
            assert('!isset($pluginConfiguration); // Cannot redeclare var $pluginConfiguration');
            foreach ($plugins as $pluginName => $pluginConfiguration)
            {
                assert('!isset($menuEntry); // Cannot redeclare var $menuEntry');
                foreach ($pluginConfiguration->getMenuNames() as $menuEntry)
                {
                    if ($menuEntry->getGroup()) {
                        $this->_pluginsWithGroups[$menuEntry->getGroup()] = $pluginName;
                    }
                } // end foreach
                unset($menuEntry);
            } // end foreach
            unset($pluginName, $pluginConfiguration);
        }
        assert('!isset($pluginName); // Cannot redeclare var $pluginName');
        $pluginName = "";
        if (isset($this->_pluginsWithGroups[$menuId])) {
            $pluginName = $this->_pluginsWithGroups[$menuId];
        }
        return $pluginName;
    }

    /**
     * Translate menu name in selected system locale.
     *
     * @param   string  $menuNameToken  menu name language token
     * @return  string
     */
    public function translateMenuName($menuNameToken)
    {
        assert('is_string($menuNameToken); // Invalid argument $menuNameToken: string expected');
        return $this->getTranslationManager()->replaceToken($menuNameToken);
    }

    /**
     * return menu as associative array
     *
     * Extracts all menus and items.
     * Array Keys are menu names.
     * Value Keys are URLs, values are text labels.
     *
     * @param   \Yana\Plugins\Menus\IsMenu  $menuConfiguration  from which to take the entries
     * @return  array
     */
    public function getTextMenu(\Yana\Plugins\Menus\IsMenu $menuConfiguration)
    {
        $pluginManager = \Yana\Plugins\Manager::getInstance();
        $isSafemode = \Yana\Application::getId() === \Yana\Application::getDefault('profile');
        $textMenu = array();

        foreach ($menuConfiguration->getMenuEntries() as $menuId => $menuEntries)
        {
            $pluginId = $this->_getPluginNameByGroupId($menuId);
            if ($pluginId && $this->_hasGroup($pluginId) && !$pluginManager->isLoaded($pluginId)) {
                continue;
            }
            $this->_getMenu($menuConfiguration, $textMenu, $menuId, $menuEntries, $isSafemode);
        }
        unset($menuId, $menuEntries);

        return $textMenu;
    }

    /**
     * get menu entries
     *
     * @param   \Yana\Plugins\Menus\IsMenu  $menuConfiguration  from which to take the entries
     * @param   array                       &$textMenu          output
     * @param   string                      $menuId             index of menu
     * @param   array                       $menuEntries        list of instances of PluginMenuEntry or sub-menus
     * @param   bool                        $isSafemode         for safemode set true , false otherweise
     */
    private function _getMenu(\Yana\Plugins\Menus\IsMenu $menuConfiguration, array &$textMenu, $menuId, array $menuEntries, $isSafemode)
    {
        assert('is_string($menuId); // Invalid argument $menuId: string expected');
        $name = $menuConfiguration->getMenuName($menuId);
        $urlFormatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();

        foreach ($menuEntries as $action => $entry)
        {
            if ($entry instanceof \Yana\Plugins\Menus\IsEntry) {

                // is entry
                $safemode = $entry->getSafeMode();
                if (!is_null($safemode) && $isSafemode !== $safemode) {
                    continue;
                }
                $url = $urlFormatter("action=$action", true);
                $label = $this->translateMenuName($entry->getTitle());
                if (!empty($name)) {
                    $textMenu[$name][$url] = $label;
                } else {
                    $textMenu[$url] = $label;
                }
            } elseif (is_array($entry)) {

                // is menu
                if (!isset($textMenu[$name])) {
                    $textMenu[$name] = array();
                }
                $this->_getMenu($menuConfiguration, $textMenu[$name], "$menuId.$action", $entry, $isSafemode);
            }
        }
    }

}

?>