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
 *
 * @ignore
 */

namespace Yana\Db\Doctrine;

/**
 * <<abstract>> Represents a Doctrine DBAL resultset.
 *
 * @package     yana
 * @subpackage  db
 *
 * @ignore
 */
abstract class AbstractResult extends \Yana\Core\Object implements \Yana\Db\IsResult
{

    /**
     * @var array
     */
    private $_cache = array();

    /**
     * Get row from result set.
     *
     * We can't just iterate over the result set. The reason is the Doctrine implements \Traverseable but not \Iterator.
     * Therefore we cannot rewind the iterator.
     *
     * @param   int  $rowNumber  position number of row within the result set, first row = 0
     * @return  array
     */
    protected function _getRow($rowNumber)
    {
        assert('is_int($rowNumber); // Invalid argument $rowNumber: Integer expected');

        if (!isset($this->_cache[$rowNumber])) {
            $result = $this->_getResult();
            for ($i = count($this->_cache); $i < $result->rowCount() && $i <= $rowNumber; $i++)
            {
                $this->_cache[$i] = $result->fetch(\Doctrine\DBAL\FetchMode::ASSOCIATIVE);
            }
            unset($i);
        }
        return (isset($this->_cache[$rowNumber])) ? $this->_cache[$rowNumber] : array();
    }

    /**
     * Returns resultset.
     *
     * @return  \Doctrine\DBAL\Statement
     */
    abstract protected function _getResult();

    /**
     * Returns the number of rows in the result set.
     *
     * @return  int
     */
    public function countRows()
    {
        return $this->_getResult()->rowCount();
    }

    /**
     * Fetch a row from the resultset.
     *
     * Returns an associative array of the row at index $i of the result set.
     *
     * @param   int  $rowNumber  index of the row to retrieve
     * @return  array
     */
    public function fetchRow($rowNumber)
    {
        assert('is_int($rowNumber); // Invalid argument $rowNumber: Integer expected');
        return $this->_getRow((int) $rowNumber);
    }

    /**
     * Fetch and return all rows from the result set.
     *
     * @return  array
     */
    public function fetchAll()
    {
        $this->_cache = $this->_getResult()->fetchAll(\Doctrine\DBAL\FetchMode::ASSOCIATIVE);
        return $this->_cache;
    }

    /**
     * Fetch and return a column from the current row pointer position
     *
     * @param   int  $column  the column number to fetch
     * @return  array
     */
    public function fetchColumn($column = 0)
    {
        return $this->_getResult()->fetchColumn((int) $column);
    }

    /**
     * Fetch single column from the next row from a result set.
     *
     * @param   int|string  $column     the column number (or name) to fetch
     * @param   int         $rowNumber  number of the row where the data can be found
     * @return  mixed
     */
    public function fetchOne($column = 0, $rowNumber = 0)
    {
        assert('is_int($column); // Invalid argument $column: Integer expected');
        assert('is_int($rowNumber); // Invalid argument $rowNumber: Integer expected');
        $row = $this->fetchRow((int) $rowNumber);
        $numberedRow = \array_values($row);
        return isset($numberedRow[$column]) ? $numberedRow[$column] : null;
    }

}

?>