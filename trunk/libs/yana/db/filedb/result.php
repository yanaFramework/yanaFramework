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

namespace Yana\Db\FileDb;

/**
 * Represents a FileDB resultset.
 *
 * @package     yana
 * @subpackage  db
 *
 * @ignore
 */
class Result extends \Yana\Core\StdObject implements \Yana\Db\IsResult
{

    /**
     * @var array
     */
    private $_result = null;

    /**
     * @var string
     */
    private $_message = '';

    /**
     * Creates a new resultset.
     *
     * @param  array   $result   resultset (set "null" for error)
     * @param  string  $message  error message
     */
    public function __construct(?array $result = null, $message = '')
    {
        if (is_array($result)) {
            $this->_result = \Yana\Util\Hashtable::changeCase($result, CASE_LOWER);
        }
        $this->_message  = trim((string) $message);
    }

    /**
     * Returns the number of rows in the result set.
     *
     * @return  int
     */
    public function countRows(): int
    {
        return is_array($this->_result) ? count($this->_result) : 0;
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
        $row = array();
        if (isset($this->_result[$rowNumber])) {
            $row = $this->_result[$rowNumber];
        }
        return $row;
    }

    /**
     * Fetch and return all rows from the result set.
     *
     * @return  array
     */
    public function fetchAll(): array
    {
        return is_array($this->_result) ? $this->_result : array();
    }

    /**
     * Fetch and return a column from the current row pointer position
     *
     * @param   int  $column  the column number to fetch
     * @return  array
     */
    public function fetchColumn(int $column = 0): array
    {
        $result = array();
        foreach ($this->fetchAll() as $row)
        {
            $result[] = $this->_fetchCellFromRow($row, $column);
        }
        return $result;
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
        return $this->_fetchCellFromRow($row, $column);
    }

    /**
     * Fetch a cell from a row array.
     * 
     * @param   array  $row     a single row
     * @param   int    $column  the column number (or name) to fetch
     * @return  mixed
     */
    private function _fetchCellFromRow(array $row, int $column)
    {
        switch (true)
        {
            case count($row) >= $column:
                $row = \array_values($row);
                break;
        }

        return isset($row[$column]) ? $row[$column] : null;
    }

    /**
     * Returns an error message (if any) if the resultset is in error-state.
     *
     * If there is none, an empty string is returned instead.
     *
     * @return  string
     */
    public function getMessage()
    {
        return $this->_message;
    }

}

?>
