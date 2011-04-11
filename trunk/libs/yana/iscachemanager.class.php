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
 * <<interface>> Cache manager interface.
 *
 * This base class is meant to define the method names to work with a cache.
 * It uses only magic functions to access cache entries transparently, in the way you would in a data container.
 *
 * Example of usage:
 * <code>
 * $cache = new CacheFooManager();
 * if (isset($cache->myFoo)) {
 *   $myFoo = $cache->myFoo;
 * } else {
 *   $myFoo = new Foo();
 *   $cache->myFoo = $myFoo;
 * }
 * $myFoo->bar();
 * </code>
 *
 * @access      public
 * @package     yana
 * @subpackage  core
 */
interface IsCacheManager
{

    /**
     * <<magic>> Set cache item.
     *
     * This adds or replaces an item of the cache at the given index with whatever object $value contains.
     *
     * @access  public
     * @param   string  $name   index of cached object
     * @param   object  $value  new value of cached instance
     */
    public function __set($name, $value);

    /**
     * <<magic>> Get cache item.
     *
     * This retrieves an item of the cache at the given index.
     * Returns NULL if the index has no item.
     *
     * @access  public
     * @param   string  $name  index of cached object
     */
    public function __get($name);

    /**
     * <<magic>> Unset cache item.
     *
     * This deletes an item of the cache at the given index.
     * If no item is found, nothing happens.
     *
     * @access  public
     * @param   string  $name  index of cached object
     */
    public function __unset($name);

    /**
     * <<magic>> Check if cached item exists.
     *
     * This returns bool(true) if an item is set for the cached index and bool(false) if not.
     *
     * @access  public
     * @param   string  $name  index of cached object
     * @return  bool
     */
    public function __isset($name);

}