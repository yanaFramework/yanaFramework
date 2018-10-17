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
 * <<abstract>> Database object factory.
 *
 * @package     yana
 * @subpackage  db
 * @codeCoverageIgnore
 */
abstract class AbstractDatabaseFactory extends \Yana\Core\Object implements \Yana\Db\Ddl\Factories\IsDatabaseFactory
{

    /**
     * Wrapped MDB2 database connection.
     *
     * @var  \Yana\Db\Ddl\Factories\IsMdb2Mapper
     */
    private $_mapper = null;

    /**
     * <<constructor>> Initialize instance.
     *
     * @param  \Yana\Db\Ddl\Factories\IsMdb2Mapper  $mapper  implements the functions that map MDB2 table info to a database object
     */
    public function __construct(\Yana\Db\Ddl\Factories\IsMdb2Mapper $mapper = null)
    {
        if (\is_null($mapper)) {
            $mapper = new \Yana\Db\Ddl\Factories\Mdb2Mapper();
        }
        $this->_mapper = $mapper;
    }

    /**
     * Returns the MDB2 to database object mapper.
     *
     * @return  \Yana\Db\Ddl\Factories\IsMdb2Mapper
     */
    protected function _getMapper()
    {
        return $this->_mapper;
    }

}

?>