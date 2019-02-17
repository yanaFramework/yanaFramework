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
 * <<abstract>> Doctrine DBAL wrapper.
 *
 * Consumed by DatabaseFactory.
 *
 * This class exports a number of relevant functions of the Doctrine DBAL driver and hides the rest.
 *
 * @package     yana
 * @subpackage  db
 * @ignore
 * @codeCoverageIgnore
 */
abstract class AbstractDoctrineWrapper extends \Yana\Core\Object implements \Yana\Db\Ddl\Factories\IsDoctrineWrapper
{

    /**
     * The wrapped Doctrine instance.
     *
     * @var  \Doctrine\DBAL\Connection
     */
    private $_connection = null;

    /**
     * <<constructor>> Initialize instance.
     *
     * @param   \Doctrine\DBAL\Connection     $connection  Doctrine DBAL database connection
     * @throws  \Yana\Db\ConnectionException  when we are unable to open a connection to the database
     */
    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        $this->_connection = $connection;
    }

    /**
     * Returns the wrapped Doctrine DBAL connection instance.
     *
     * @return  \Doctrine\DBAL\Connection
     */
    protected function _getConnection()
    {
        return $this->_connection;
    }

}

?>