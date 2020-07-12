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

namespace Yana\Db\Export;

/**
 * <<decorator>> Database query factory.
 *
 * This class helps transfer data from a source to a target.
 *
 * @package     yana
 * @subpackage  db
 */
class QueryFactory extends \Yana\Core\StdObject
{

    /**
     * @var \Yana\Db\IsConnection
     */
    private $_source = null;

    /**
     * @var \Yana\Db\IsConnection
     */
    private $_target = null;

    /**
     * @var \Yana\Db\Ddl\Table[]
     */
    private $_tables = array();

    /**
     * @var array
     */
    private $_currentOffsets = array();

    /**
     * Create a new instance.
     *
     * This class requires a database resource as input.
     *
     * @param  \Yana\Db\IsConnection  $source  selecting values from this database
     * @param  \Yana\Db\IsConnection  $target  creating insert statements for this database
     */
    public function __construct(\Yana\Db\IsConnection $source, \Yana\Db\IsConnection $target)
    {
        $this->_source = $source;
        $this->_target = $target;
        $this->_tables = $source->getSchema()->getTablesSortedByForeignKey();
    }

    /**
     * Get connection to source database.
     *
     * @return \Yana\Db\IsConnection
     */
    protected function _getSource(): \Yana\Db\IsConnection
    {
        return $this->_source;
    }

    /**
     * Get connection to target database.
     *
     * @return \Yana\Db\IsConnection
     */
    protected function _getTarget(): \Yana\Db\IsConnection
    {
        return $this->_target;
    }

    /**
     * Create insert statements.
     *
     * This iterates over all tables and rows of the source and creates
     * insert statements for each one.
     *
     * For each call it will return no more than $howManyStatements of
     * statements so that you won't run out of memory on bigger
     * databases. Thus you may have to call this function multiple times
     * to see all data sets.
     *
     * If there are no further data sets (or if $howManyStatements == 0), the
     * function will return an empty array.
     *
     * @param   int  $howManyStatements  max number of statements to return
     * @return  \Yana\Db\Queries\Insert[]
     */
    public function createInsertStatements(int $howManyStatements = 500): array
    {
        $insertStmts = array();
        // Loop through all tables in the database and extract each one
        while (count($insertStmts) < $howManyStatements)
        {
            $table = current($this->_tables);
            if (!isset($this->_currentOffsets[$table->getName()])) {
                $this->_currentOffsets[$table->getName()] = 0;
            }
            $offset = $this->_currentOffsets[$table->getName()];
            $tableSize = $this->_getSource()->length($table->getName());
            $select = new \Yana\Db\Queries\Select($this->_getSource());
            $select->useInheritance(false);
            $select->setTable($table->getName());
            $select->setOffset($offset);
            $select->setLimit($howManyStatements - count($insertStmts));
            $rows = $select->getResults();
            // Select * From table
            foreach($rows as $row)
            {
                $insert = new \Yana\Db\Queries\Insert($this->_getTarget());
                $insertStmts[] = $insert
                    ->useInheritance(false)
                    ->setTable($table->getName())
                    ->setValues($row);
            }
            $offset += count($rows);
            $this->_currentOffsets[$table->getName()] = $offset;
            if ($offset >= $tableSize && next($this->_tables) === false) {
                break;
            }
        }
        return $insertStmts;
    }

}

?>