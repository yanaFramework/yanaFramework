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

namespace Yana\Db\Sql\Quoting;

/**
 * <<algorithm>> This algorithm wraps a connection object and defers quoting to the database connection.
 *
 * @package     yana
 * @subpackage  db
 */
class ConnectionAlgorithm extends \Yana\Core\StdObject implements \Yana\Db\Sql\Quoting\IsAlgorithm
{

    /**
     * @var \Yana\Db\IsConnection
     */
    private $_connection = null;

    /**
     * <<constructor>> Inject connection.
     *
     * @param  \Yana\Db\IsConnection  $connection  used for quoting
     */
    public function __construct(\Yana\Db\IsConnection $connection)
    {
        $this->_connection = $connection;
    }

    /**
     * Returns connection for quoting.
     *
     * @return \Yana\Db\IsConnection
     */
    protected function _getConnection(): \Yana\Db\IsConnection
    {
        return $this->_connection;
    }

    /**
     * Returns the quoted database identifier as a string.
     *
     * @param   string  $value  any string that needs to be quoted
     * @return  string
     */
    public function quote(string $value): string
    {
        return $this->_getConnection()->quote($value);
    }

}

?>