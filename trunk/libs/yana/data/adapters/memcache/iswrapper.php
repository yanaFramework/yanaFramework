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

namespace Yana\Data\Adapters\MemCache;

/**
 * <<interface>> Memcache wrapper.
 *
 * Provide a common interface for all Memcache client APIs.
 *
 * @package     yana
 * @subpackage  data
 */
interface IsWrapper
{

    /**
     * Get the matching value.
     *
     * Returns bool(false) on failure.
     *
     * @param   string  $key  identifying the item
     * @return  string
     */
    public function getVar($key);

    /**
     * Get array of matching values.
     *
     * Returns bool(false) on failure.
     *
     * @param   array  $keys  list of identifiers
     * @return  array
     */
    public function getVars(array $keys);

    /**
     * Store data at the server.
     *
     * @param   string  $key     identifying the item
     * @param   mixed   $var     value to be stored
     * @param   int     $expire  number of seconds it will take for the cache to expire
     * @return  bool
     */
    public function setVar($key, $var, $expire = 0);

    /**
     * Delete data at the server.
     *
     * Returns bool(true) on success or bool(false) on failure. 
     *
     * @param   string  $key  identifying the item
     * @return  bool
     */
    public function unsetVar($key);

    /**
     * Add a memcached server to the connection pool.
     *
     * Returns bool(true) on success or bool(false) on failure. 
     *
     * @param   \Yana\Data\Adapters\MemCache\Server $server  server configuration
     * @return  bool
     */
    public function addServer(\Yana\Data\Adapters\MemCache\Server $server);

    /**
     * Checks wether or not any of the Memcache servers is reachable.
     *
     * Returns bool(true) if it gets an answer from any of the listed servers.
     *
     * @return  bool
     */
    public function getStats();

}

?>