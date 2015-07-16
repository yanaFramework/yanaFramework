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
class Builder extends \Yana\Plugins\Menus\AbstractBuilder implements \Yana\Log\IsLogable, \Yana\Plugins\Menus\IsCacheableBuilder
{

    /**
     * @var  \Yana\Data\Adapters\IsDataAdapter
     */
    private $_cache = null;

    /**
     * @var  \Yana\Log\LoggerCollection
     */
    private $_loggers = null;

    /**
     * Initialize instance
     */
    public function __construct()
    {
        $this->_loggers = new \Yana\Log\LoggerCollection();
    }

    /**
     * Adds a logger to the class.
     *
     * @param  \Yana\Log\IsLogger  $logger  instance that will handle the logging
     */
    public function attachLogger(\Yana\Log\IsLogger $logger)
    {
        $collection = $this->getLogger();
        $collection[] = $logger;
    }

    /**
     * Returns the attached loggers.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger()
    {
        if ($this->_loggers->count() === 0) {
            $this->_loggers = \Yana\Log\LogManager::getLogger();
        }
        return $this->_logger;
    }

    /**
     * Returns the cache adapter used by this class.
     *
     * Defaults to a SessionAdapter if nothing else is provided.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    protected function _getCache()
    {
        if (!$this->_cache instanceof \Yana\Data\Adapters\IsDataAdapter) {
            $this->_cache = new \Yana\Data\Adapters\SessionAdapter(__CLASS__);
        }
        return $this->_cache;
    }

    /**
     * Select the cache adapter to store the menu in.
     *
     * Sets or replaces the cache adapter.
     * Note that for Unit tests you may force the class NOT to use a cache by injecting a NullAdapter here.
     *
     * @param   \Yana\Data\Adapters\IsDataAdapter  $cacheAdapter  preferably a SessionAdapter
     * @return  \Yana\Plugins\Menus\Builder
     */
    public function setCache(\Yana\Data\Adapters\IsDataAdapter $cacheAdapter)
    {
        $this->_cache = $cacheAdapter;
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
        assert('!isset($cache); // Cannot redeclare var $cache');
        $cache = $this->_getCache();
        assert('!isset($menus); // Cannot redeclare var $menus');
        $menus = $this->_getMenus();

        /* Retrieve the menu settings, either:
         * - by using settings we already know
         * - restoring settings from the cache
         * - or loading the defaults
         */
        assert('!isset($menu); // Cannot redeclare var $menu');
        if (isset($menus[$locale])) {
            $menu = $menus[$locale];

        } elseif (isset($cache[$locale])) {
            $menu = \unserialize($cache[$locale]);

        } else {

            $menu = parent::buildMenu();
        }

        // If the instance is not yet in cache, put it there for later use
        if (!isset($cache[$locale])) {

            $cache[$locale] = serialize($menu);

        }

        return $menu;
    }

    /**
     * Remove all instances of the main application menu from the cache.
     *
     * After calling this function, calling "buildMenu()" again will give you a fresh instance.
     *
     * @return \Yana\Plugins\Menus\Builder
     */
    public function clearMenuCache()
    {
        assert('!isset($cache); // Cannot redeclare var $cache');
        $cache = $this->_getCache();

        // reset
        assert('!isset($id); // Cannot redeclare var $id');
        foreach ($cache->getIds() as $id) {
            unset($cache[$id]);
        }
        unset($id);

        // reset collection items
        $this->_getMenus()->setItems(array());

        return $this;
    }

}

?>