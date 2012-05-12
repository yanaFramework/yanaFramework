
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

namespace Yana\Io\Adapters\MemCache;

/**
 * <<wrapper>> For use with PECL extention "MemcacheD" (NOT "Memcache").
 *
 * @package     yana
 * @subpackage  core
 */
class MemcachedWrapper extends \Yana\Core\Object implements \Yana\Io\Adapters\MemCache\IsWrapper
{

    /**
     * Connection to Memcache server.
     *
     * @var \Memcached
     */
    private $_memCache = null;

    /**
     * Set up connection to Memcache server.
     *
     * @param  \Memcached  $memCache  Connection to Memcache server.
     */
    public function __construct(\Memcached $memCache)
    {
        $this->_memCache = $memCache;
    }

    /**
     * Returns the Memcache instance.
     *
     * @return  \Memcached
     */
    protected function _getMemCache()
    {
        return $this->_memCache;
    }

    /**
     * Get the matching value.
     *
     * Returns bool(false) on failure.
     *
     * @param   string  $key  identifying the item
     * @return  string
     */
    public function getVar($key)
    {
        assert('is_string($key); // Invalid argument $key: string expected');

        return $this->_getMemCache()->get($key);
    }

    /**
     * Get array of matching values.
     *
     * Returns bool(false) on failure.
     *
     * @param   array  $keys  list of identifiers
     * @return  array
     */
    public function getVars(array $keys)
    {
        return $this->_getMemCache()->getMulti($keys);
    }

    /**
     * Store data at the server.
     *
     * Returns bool(true) on success or bool(false) on failure. 
     *
     * @param   string  $key     identifying the item
     * @param   mixed   $var     value to be stored
     * @param   int     $expire  number of seconds it will take for the cache to expire
     * @return  bool
     */
    public function setVar($key, $var, $expire = 0)
    {
        assert('is_string($key); // Invalid argument $key: string expected');
        assert('is_int($expire); // Invalid argument $expire: int expected');

        return $this->_getMemCache()->set($key, $var, 0, (int) $expire);
    }

    /**
     * Delete data at the server.
     *
     * Returns bool(true) on success or bool(false) on failure. 
     *
     * @param   string  $key  identifying the item
     * @return  bool
     */
    public function unsetVar($key)
    {
        return $this->_getMemCache()->delete($key);
    }

    /**
     * Add a memcached server to the connection pool.
     *
     * Returns bool(true) on success or bool(false) on failure. 
     *
     * @param   \Yana\Io\Adapters\MemCache\Server $server  server configuration
     * @return  bool
     */
    public function addServer(\Yana\Io\Adapters\MemCache\Server $server)
    {
        return $this->_getMemCache()->addServer($server->getHostName(), $server->getPort(), $server->getWeight());
    }

    /**
     * Checks wether or not any of the Memcache servers is reachable.
     *
     * Retuns an array of statistics or bool(false) on error.
     *
     * Example:
     * <code>
     * Array (
     *      [localhost:11211] => Array
     *          (
     *              [pid] => 3756
     *              [uptime] => 603011
     *              [time] => 1133810435
     *              [version] => 1.1.12
     *              [rusage_user] => 0.451931
     *              [rusage_system] => 0.634903
     *              [curr_items] => 2483
     *              [total_items] => 3079
     *              [bytes] => 2718136
     *              [curr_connections] => 2
     *              [total_connections] => 807
     *              [connection_structures] => 13
     *              [cmd_get] => 9748
     *              [cmd_set] => 3096
     *              [get_hits] => 5976
     *              [get_misses] => 3772
     *              [bytes_read] => 3448968
     *              [bytes_written] => 2318883
     *              [limit_maxbytes] => 33554432
     *          )
     *      [failed_host:11211] => false
     *  )
     * </code>
     *
     * See PHP manual for more details.
     *
     * @return  bool
     */
    public function getStats()
    {
        return $this->_getMemCache()->getStats();
    }

}

?>