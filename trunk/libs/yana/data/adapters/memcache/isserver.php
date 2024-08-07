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
 * <<interface>> Memcache server setup.
 *
 * This is the configuration of the server to be used with the Memcache client.
 *
 * @package     yana
 * @subpackage  data
 */
interface IsServer
{

    /**
     * Returns the hostname of Memcache server.
     *
     * @return  string
     */
    public function getHostName(): string;

    /**
     * Returns the port of Memcache server.
     *
     * @return  int
     */
    public function getPort(): int;

    /**
     * Returns the weight of the server in the pool.
     *
     * @return  int
     */
    public function getWeight(): int;
    
}

?>