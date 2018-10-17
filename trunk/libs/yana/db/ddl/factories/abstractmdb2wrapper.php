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

namespace Yana\Db\Ddl\Factories;

/**
 * <<abstract>> MDB2 wrapper.
 *
 * Consumed by DatabaseFactory.
 *
 * This class exports a number of relevant function of the MDB2 driver and hides the rest.
 *
 * @package     yana
 * @subpackage  db
 * @ignore
 * @codeCoverageIgnore
 */
abstract class AbstractMdb2Wrapper extends \Yana\Core\Object implements \Yana\Db\Ddl\Factories\IsMdb2Wrapper
{

    /**
     * The wrapped MDB2 instance.
     *
     * @var  \MDB2_Driver_Common
     */
    private $_connection = null;

    /**
     * <<constructor>> Initialize instance.
     *
     * @param   \MDB2_Driver_Common  $connection  MDB2 database connection
     * @throws  \Yana\Db\ConnectionException  when unable to open connection to database
     */
    public function __construct(\MDB2_Driver_Common $connection)
    {
        $connection->loadModule('Manager');
        $connection->loadModule('Reverse');

        $name = $connection->getDatabase();
        if ($name instanceof \MDB2_Error) {
            throw new \Yana\Db\ConnectionException($name->getMessage());
        }
        unset($name);
        $this->_connection = $connection;
    }

    /**
     * Returns the wrapped MDB2 instance.
     *
     * @return  \MDB2_Driver_Common
     */
    protected function _getConnection()
    {
        return $this->_connection;
    }

}

?>