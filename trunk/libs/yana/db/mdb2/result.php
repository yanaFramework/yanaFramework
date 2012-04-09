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

namespace Yana\Db\Mdb2;

/**
 * <<wrapper>> Represents a FileDB resultset.
 *
 * @package     yana
 * @subpackage  db
 *
 * @ignore
 */
class Result extends \Yana\Core\Object implements \Yana\Db\IsResult
{

    /**
     * @var \MDB2_Result_Common
     */
    private $_result = null;

    /**
     * Creates a new resultset.
     *
     * @param  \MDB2_Result_Common  $result  resultset
     */
    public function __construct(\MDB2_Result_Common $result)
    {
        $this->_result = $result;
    }

    /**
     * Returns the number of rows in the result set.
     *
     * @return  int
     */
    public function countRows()
    {
        return $this->_result->numRows();
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
        return $this->_result->fetchRow(\MDB2_FETCHMODE_ASSOC, $rowNumber);
    }

    /**
     * Fetch and return all rows from the result set.
     *
     * @return  array
     */
    public function fetchAll()
    {
        return $this->_result->fetchAll(\MDB2_FETCHMODE_ASSOC);
    }

    /**
     * Fetch and return a column from the current row pointer position
     *
     * @param   int|string  $column  the column number (or name) to fetch
     * @return  array
     */
    public function fetchColumn($column = 0)
    {
        return $this->_result->fetchCol($column);
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
        return $this->_result->fetchOne($column, $rowNumber);
    }

}

?>