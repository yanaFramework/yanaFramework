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

/**
 * <<manager>> Base class for session caches.
 *
 * This base class is meant to define how to implement a session cache.
 *
 * @access      public
 * @package     yana
 * @subpackage  cache
 */
class CacheSessionManager extends Object implements IsCacheManager
{

    /**
     * Selected cache namespace.
     *
     * This is the name of the selected index of the session array, where values will be stored.
     *
     * @access  protected
     * @var     string
     * @ignore
     */
    private $_cacheNamespace = '';

    /**
     * Initialize instance and select name of cache-group.
     *
     * @access  public
     */
    public function __construct()
    {
        $this->setNamespace(get_class($this));
    }

    /**
     * Set selected cache namespace.
     *
     * This is the name of the selected index of the session array, where values will be stored.
     *
     * @access  protected
     * @param   string  $namespace  index name
     */
    protected function setNamespace($namespace)
    {
        $this->_cacheNamespace = (string) $namespace;
    }

    /**
     * Get selected cache namespace.
     *
     * This is the name of the selected index of the session array, where values will be stored.
     *
     * @access  protected
     * @return  string
     */
    protected function getNamespace()
    {
        return $this->_cacheNamespace;
    }

    /**
     * <<magic>> Set cache item.
     *
     * This adds or replaces an item of the cache at the given index with whatever object $value contains.
     *
     * @access  public
     * @param   string  $name   index of cached object
     * @param   object  $value  new value of cached instance
     */
    public function __set($name, $value)
    {
        $_SESSION[$this->getNamespace()][$name] = $value;
    }

    /**
     * <<magic>> Get cache item.
     *
     * This retrieves an item of the cache at the given index.
     * Returns NULL if the index has no item.
     *
     * @access  public
     * @param   string  $name  index of cached object
     */
    public function __get($name)
    {
        return $_SESSION[$this->getNamespace()][$name];
    }

    /**
     * <<magic>> Unset cache item.
     *
     * This deletes an item of the cache at the given index.
     * If no item is found, nothing happens.
     *
     * @access  public
     * @param   string  $name  index of cached object
     */
    public function __unset($name)
    {
        unset($_SESSION[$this->getNamespace()][$name]);
    }

    /**
     * <<magic>> Check if cached item exists.
     *
     * This returns bool(true) if an item is set for the cached index and bool(false) if not.
     *
     * @access  public
     * @param   string  $name  index of cached object
     * @return  bool
     */
    public function __isset($name)
    {
        return isset($_SESSION[$this->getNamespace()][$name]);
    }

}

?>