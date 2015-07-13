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
 * Memcache server setup.
 *
 * This is the configuration of the server to be used with the Memcache client.
 *
 * @package     yana
 * @subpackage  io
 */
class Server extends \Yana\Core\Object
{

    /**
     * Hostname of Memcache server.
     *
     * @var  string
     */
    private $_host = '127.0.0.1';

    /**
     * Port of Memcache server.
     *
     * @var  int
     */
    private $_port = 11211;

    /**
     * Weight of the server in relation to all other servers in the pool.
     *
     * @var  int
     */
    private $_weight = 1;

    /**
     * Set server configuration.
     *
     * @param  string  $hostName  hostname of Memcache server.
     * @param  int     $port      port of Memcache server.
     * @param  int     $weight    controls probability of the server being selected.
     */
    public function __construct($hostName = '127.0.0.1', $port = 11211, $weight = 1)
    {
        assert('is_string($hostName)', ' Invalid argument $hostName: string expected');
        assert('is_int($port)', ' Invalid argument $port: int expected');
        assert('is_int($weight)', ' Invalid argument $weight: int expected');
        assert('$weight > 0', ' The weight for the server must be a positive integer greater than 0.');

        $this->_host = (string) $hostName;
        $this->_port = (int) $port;
        $this->_weight = (int) $weight;
    }

    /**
     * Returns the hostname of Memcache server.
     *
     * @return  string
     */
    public function getHostName()
    {
        return $this->_host;
    }

    /**
     * Returns the port of Memcache server.
     *
     * @return  int
     */
    public function getPort()
    {
        return $this->_port;
    }

    /**
     * Returns the weight of the server in the pool.
     *
     * @return  int
     */
    public function getWeight()
    {
        return $this->_weight;
    }
    
}

?>