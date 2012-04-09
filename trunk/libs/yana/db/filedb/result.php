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
class Result extends \Yana\Core\Object implements \Yana\Db\IsResult
{

    /**
     * @var array
     */
    private $_result = array();

    /**
     * @var string
     */
    private $_message = '';

    /**
     * Creates a new resultset.
     *
     * @param  mixed   $result   resultset (set "null" for error)
     * @param  string  $message  error message
     */
    public function __construct($result, $message = '')
    {
        $message = (string) $message;
        if (is_null($result)) {
            $this->_result = null;
        } else {
            assert('is_array($result);');
            $this->_result = \Yana\Util\Hashtable::changeCase($result, CASE_LOWER);
        }
        $this->_message  = trim($message);
    }

    /**
     * Returns the number of rows in the result set.
     *
     * @return  int
     */
    public function countRows()
    {
        return count($this->_result);
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
        assert('is_int($rowNumber); // Invalid argument $rowNumber: int expected');

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
    public function fetchAll()
    {
        return $this->_result;
    }

    /**
     * Fetch and return a column from the current row pointer position
     *
     * @param   int|string  $column  the column number (or name) to fetch
     * @return  array
     */
    public function fetchColumn($column = 0)
    {
        assert('is_string($column) || is_int($column); // Invalid argument $column: int expected');

        $result = array();
        foreach ($this->_result as $row)
        {
            $result[] = $this->_fetchCellFromRow($row, $column);
        }
        return $result;
    }

    /**
     * Fetch single column from the next row from a result set.
     *
     * @param   int|string  $column  the column number (or name) to fetch
     * @param   int         $row     number of the row where the data can be found
     * @return  mixed
     */
    public function fetchOne($column = 0, $rowNumber = 0)
    {
        assert('is_string($column) || is_int($column); // Invalid argument $column: int expected');
        assert('is_int($rowNumber); // Invalid argument $rowNumber: int expected');

        $row = $this->fetchRow($rowNumber);
        return $this->_fetchCellFromRow($row, $column);
    }

    /**
     * Fetch a cell from a row array.
     * 
     * @param   array       $row     a single row
     * @param   int|string  $column  the column number (or name) to fetch
     * @return  mixed
     */
    private function _fetchCellFromRow(array $row, $column)
    {
        assert('is_string($column) || is_int($column); // Invalid argument $column: int expected');

        $cell = null;
        if (is_string($column) && isset($row[$column])) {
            $cell = $row[$column]; 
        } elseif (is_int($column) && count($row) >= $column) {
            $cell = \array_slice($row, $column, 1);
        }
        return $cell;
    }

    /**
     * Returns an error message (if any) if the resultset is in error-state.
     *
     * If there is none, an empty string is returned instead.
     *
     * Use FileDbResult::isError() to check, if the result is in an error state.
     *
     * @return  string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Check wether the result is an error.
     *
     * Returns bool(true) if the request resulted in an error state and bool(false) otherwise.
     *
     * {@internal
     * If something went wrong, the property "result" is not set. This means, $this->result is NULL.
     * }}
     *
     * @return  bool
     */
    public function isError()
    {
        return is_null($this->_result);
    }

}

?>