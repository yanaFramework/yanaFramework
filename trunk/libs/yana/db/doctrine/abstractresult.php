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
declare(strict_types=1);

namespace Yana\Db\Doctrine;

/**
 * <<abstract>> Represents a Doctrine DBAL resultset.
 *
 * @package     yana
 * @subpackage  db
 *
 * @ignore
 */
abstract class AbstractResult extends \Yana\Core\StdObject implements \Yana\Db\IsResult
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
     * @return  mixed
     */
    protected function _getRow(int $rowNumber)
    {
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
     * @return  \Doctrine\DBAL\Driver\Statement
     */
    abstract protected function _getResult();

    /**
     * Returns the number of rows in the result set.
     *
     * @return  int
     */
    public function countRows(): int
    {
        return $this->_getResult()->rowCount();
    }

    /**
     * Fetch a row from the resultset.
     *
     * Returns the entry at index $i of the result set.
     *
     * @param   int  $rowNumber  index of the row to retrieve
     * @return  mixed
     */
    public function fetchRow(int $rowNumber)
    {
        return $this->_getRow($rowNumber);
    }

    /**
     * Fetch and return all rows from the result set.
     *
     * @return  array
     */
    public function fetchAll(): array
    {
        $result = $this->_getResult()->fetchAll(\Doctrine\DBAL\FetchMode::ASSOCIATIVE);
        $this->_cache = is_array($result) ? $result : array();
        return $this->_cache;
    }

    /**
     * Fetch and return a column from the current row pointer position.
     *
     * If there is none, an empty array will be returned.
     *
     * @param   int  $column  the column number to fetch
     * @return  array
     */
    public function fetchColumn(int $column = 0): array
    {
        $result = $this->_getResult()->fetchColumn($column); // Returns array on success and bool(false) on failure
        return is_array($result) ? $result : array();
    }

    /**
     * Fetch single column from the next row from a result set.
     *
     * @param   int  $column     the column number to fetch
     * @param   int  $rowNumber  number of the row where the data can be found
     * @return  mixed
     */
    public function fetchOne(int $column = 0, int $rowNumber = 0)
    {
        $row = $this->fetchRow($rowNumber);
        $numberedRow = \array_values($row);
        return isset($numberedRow[$column]) ? $numberedRow[$column] : null;
    }

}

?>