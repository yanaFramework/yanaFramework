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
declare(strict_types=1);

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
     * Returns the value associated with the key or an array of found key-value pairs when key is an array.
     * Returns bool(false) on failure, when the key is not found, or the key is an empty array.
     *
     * @param   string  $key  identifying the item
     * @return  string|array|bool
     */
    public function getVar(string $key);

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
    public function setVar(string $key, $var, int $expire = 0): bool;

    /**
     * Delete data at the server.
     *
     * Returns bool(true) on success or bool(false) on failure. 
     *
     * @param   string  $key  identifying the item
     * @return  bool
     */
    public function unsetVar(string $key): bool;

    /**
     * Add a memcached server to the connection pool.
     *
     * Returns bool(true) on success or bool(false) on failure. 
     *
     * @param   \Yana\Data\Adapters\MemCache\IsServer $server  server configuration
     * @return  bool
     */
    public function addServer(\Yana\Data\Adapters\MemCache\IsServer $server);

    /**
     * Checks wether or not any of the Memcache servers is reachable.
     *
     * Returns bool(true) if it gets an answer from any of the listed servers.
     *
     * @return  array|bool
     */
    public function getStats();

}

?>
