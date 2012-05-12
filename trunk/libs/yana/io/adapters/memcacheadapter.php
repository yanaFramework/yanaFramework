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

namespace Yana\Io\Adapters;

/**
 * <<adapter>> data adapter
 *
 * Session adapter, that stores and restores the given object from the session settings.
 *
 * @package     yana
 * @subpackage  io
 */
class MemCacheAdapter extends \Yana\Core\AbstractCountableArray implements \Yana\Io\Adapters\IsDataAdapter
{

    /**
     * Key prefix.
     *
     * Used to avoid overwriting entries of other cache adapters.
     *
     * @var  string
     */
    private $_prefix = "";

    /**
     * Lifetime of the entries.
     *
     * @var  int
     */
    private $_lifetime = 0;

    /**
     * MemCache instance.
     *
     * @var  \Yana\Io\Adapters\MemCache\IsWrapper
     */
    private $_memCache = null;

    /**
     * Constructor.
     *
     * @param  \Yana\Io\Adapters\MemCache\IsWrapper  $memCache  connection to MemCache server
     * @param  string                                $prefix    id to prevent adapters from overwriting one another
     * @param  int                                   $lifetime  0 = forever, or seconds (max 30 days), or timestamp
     */
    public function __construct(\Yana\Io\Adapters\MemCache\IsWrapper $memCache, $prefix = __CLASS__, $lifetime = 0)
    {
        assert('is_string($prefix); // Invalid argument $prefix: string expected');
        assert('is_int($lifetime); // Invalid argument $lifetime: int expected');

        $this->_prefix = (string) $prefix;
        $this->_memCache = $memCache;
        $this->_lifetime = (int) $lifetime;

        if (!$this->_isAvailable()) {
            $message = "No Memcache server available. You may want to check your server and network settings.";
            throw new \Yana\Io\Adapters\MemCache\ServerNotAvailableException($message);
        }
        // In order to be countable the class keeps track of all valid ids.
        $keyList = $this->_memCache->getVar($this->_toMemCacheKey(""));
        if (\is_array($keyList)) {
            $this->_setItems($keyList);
        }
    }

    /**
     * Returns prefix to put in front of offset keys.
     *
     * @return string
     */
    protected function _getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Returns the Memcache instance.
     *
     * @return \Yana\Io\Adapters\MemCache\IsWrapper
     */
    protected function _getMemCache()
    {
        return $this->_memCache;
    }

    /**
     * Checks wether or not any of the Memcache servers is reachable.
     *
     * Returns bool(true) if it gets an answer from any of the listed servers.
     *
     * @return  bool
     */
    protected function _isAvailable()
    {
        return (bool) $this->_getMemCache()->getStats();
    }

    /**
     * Return array of ids in use.
     *
     * @return  array
     */
    public function getIds()
    {
        return \array_keys(parent::_getItems());
    }

    /**
     * Adds the item if it is missing.
     *
     * Same as:
     * <code>
     * $array[] = $subject;
     * </code>
     *
     * @param  \Yana\Io\Adapters\IsEntity  $entity  what you want to add
     */
    public function saveEntity(\Yana\Io\Adapters\IsEntity $entity)
    {
        $offset = ($entity->getId()) ? $entity->getId() : null;
        $this->offsetSet($offset, $entity);
    }

    /**
     * Returns the maximum lifetime.
     *
     * The returned value is either:
     * <ul>
     *   <li>0: never expires</li>
     *   <li>1 - 2592000: the number of seconds</li>
     *   <li>some Unix timestamp (must be a date in the future)</li>
     * </ul>
     *
     * If the given value is a past timestamp, the values will expire immediately.
     *
     * @return int
     */
    protected function _getLifetime()
    {
        return $this->_lifetime;
    }

    /**
     * Converts the offset to a memcache key.
     *
     * @param   string  $offset  base offset
     * @return  string
     */
    private function _toMemCacheKey($offset)
    {
        assert('is_scalar($offset); // Invalid argument $offset: string expected');

        return md5($this->_getPrefix() . (string) $offset);
    }

    /**
     * Return item at offset.
     *
     * Example:
     * <code>
     * $item = $collection[$offset];
     * $item = $collection->offsetGet($offset);
     * </code>
     *
     * @param   scalar  $offset  index of item to retrieve
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        $result = null;

        if ($this->offsetExists($offset)) {
            $result = $this->_getMemCache()->getVar($this->_toMemCacheKey($offset));
        }

        return $result;
    }

    /**
     * Insert or replace item.
     *
     * @param   scalar  $offset  index of item to replace
     * @param   mixed   $value   new value of item
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the value is not a valid collection item
     * @return  mixed
     */
    public function offsetSet($offset, $value)
    {
        // update key list
        parent::offsetSet($offset, true); // $offset may be NULL

        // determine key if none is given (simulates auto-increment)
        if (\is_null($offset)) {
            assert('!isset($_keys); // Cannot redeclare var $_keys');
            $_keys = \array_keys(parent::_getItems());
            $offset = (string) \array_pop($_keys);
            unset($_keys);
        }

        // MemCache::set( scalar index, value, flags, lifetime in seconds )
        $this->_getMemCache()->setVar($this->_toMemCacheKey($offset), $value, $this->_getLifetime());
        // store updated key list
        $this->_getMemCache()->setVar($this->_toMemCacheKey(""), $this->_getItems(), $this->_getLifetime());
        return $value;
    }

    /**
     * Remove item from collection.
     *
     * @param  scalar  $offset  index of item to remove
     */
    public function offsetUnset($offset)
    {
        $this->_getMemCache()->unsetVar($this->_toMemCacheKey($offset));
        // update key list
        parent::offsetUnset($offset);
        // store updated key list
        $this->_getMemCache()->setVar($this->_toMemCacheKey(""), $this->_getItems(), $this->_getLifetime());
    }

}

?>