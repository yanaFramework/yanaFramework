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

namespace Yana\Util;

/**
 * CSV format generator.
 *
 * This exports the data as a comma-seperated list of values.
 *
 * You may choose custom column and row delimiters by setting the parameters
 * to appropriate values.
 *
 * The first line may contain a header of column titles. To exclude this
 * information from the result, set headers to bool(false).
 *
 * Example output:
 * <pre>
 * "Last Name","First Name","Title","Name of Book"
 * "Smith","Steven, M.","Mr.","The ""Cookbook"" of Time"
 * "Higgings","Barbara","Ms.","A multiline guide
 * to the Galaxy"
 * </pre>
 *
 * The CSV format is defined in {@link http://www.rfc-editor.org/rfc/rfc4180.txt RFC 4180}.
 *
 * @package    yana
 * @subpackage util
 */
class Csv extends \Yana\Core\StdObject
{

    /**
     * @var string
     */
    private $_columnDelimiter = ';';

    /**
     * @var string
     */
    private $_rowDelimiter = "\n";

    /**
     * @var bool
     */
    private $_header = true;

    /**
     * @var string
     */
    private $_stringDelimiter = '"';

    /**
     * Get column delimiter.
     *
     * This separates the cells of a table from each other.
     * Example: cell-1,cell-2 (with "," as the column delimiter).
     *
     * @return string
     */
    public function getColumnDelimiter(): string
    {
        return $this->_columnDelimiter;
    }

    /**
     * Get row delimiter.
     *
     * This separates the rows of a table from each other.
     * Example: cell-1,cell-2;cell-1,cell-2; (with ";" as the row, and "," as the column delimiter).
     *
     * @return string
     */
    public function getRowDelimiter(): string
    {
        return $this->_rowDelimiter;
    }

    /**
     * Check wether or not to include a header.
     *
     * A "header" is the (optional) first line of a CSV file containing the column names.
     *
     * Bool(true) = include header, bool(false) = don't include header.
     *
     * @return bool
     */
    public function hasHeader(): bool
    {
        return $this->_header;
    }

    /**
     * Get string delimiter.
     *
     * This encloses the contents of a cell.
     * String delimiters are there to handle the case that the content of a cell itself
     * happens to contain a column or row delimiter.
     *
     * The string delimiters marks the beginning and end of a cell, any column or row delimiter
     * found within is ignored.
     *
     * In case the cell content contains a string delimiter, that string delimiter is escaped
     * by having it preceed by another string delimiter.
     * These are to be interpreted as follows: Any even number of string delimiters found is
     * one or more escaped string delimiters and does NOT terminate the string.
     * Any odd number of string delimiters found means that the last string delimiter terminates
     * the string.
     *
     * Example: "cell-1","ce""ll""-2","ce,ll-3" (with '"' as the string, and "," as the column delimiter).
     *
     * @return string
     */
    public function getStringDelimiter(): string
    {
        return $this->_stringDelimiter;
    }

    /**
     * Get column delimiter.
     *
     * This separates the cells of a table from each other.
     * Example: cell-1,cell-2 (with "," as the column delimiter.
     *
     * @param   string  $columnDelimiter  any char or string that is not the row or string delimiter
     * @return  $this
     */
    public function setColumnDelimiter(string $columnDelimiter)
    {
        $this->_columnDelimiter = $columnDelimiter;
        return $this;
    }

    /**
     * Set row delimiter.
     *
     * This separates the rows of a table from each other.
     * Example: cell-1,cell-2;cell-1,cell-2; (with ";" as the row, and "," as the column delimiter).
     *
     * @param   string  $rowDelimiter  any char or string that is not the column or string delimiter
     * @return  $this
     */
    public function setRowDelimiter(string $rowDelimiter)
    {
        $this->_rowDelimiter = $rowDelimiter;
        return $this;
    }

    /**
     * Set wether or not to include a header.
     *
     * A "header" is the (optional) first line of a CSV file containing the column names.
     *
     * @param   bool  $hasHeader  true = include header, false = don't include header
     * @return  $this
     */
    public function setHeader(bool $hasHeader)
    {
        $this->_header = $hasHeader;
        return $this;
    }

    /**
     * Set string delimiter.
     *
     * This encloses the contents of a cell.
     * String delimiters are there to handle the case that the content of a cell itself
     * happens to contain a column or row delimiter.
     *
     * The string delimiters marks the beginning and end of a cell, any column or row delimiter
     * found within is ignored.
     *
     * In case the cell content contains a string delimiter, that string delimiter is escaped
     * by having it preceed by another string delimiter.
     * These are to be interpreted as follows: Any even number of string delimiters found is
     * one or more escaped string delimiters and does NOT terminate the string.
     * Any odd number of string delimiters found means that the last string delimiter terminates
     * the string.
     *
     * Example: "cell-1","ce""ll""-2","ce,ll-3" (with '"' as the string, and "," as the column delimiter).
     *
     * @param   string  $stringDelimiter  any char or string that is not the column or row delimiter
     * @return  $this
     */
    public function setStringDelimiter(string $stringDelimiter)
    {
        $this->_stringDelimiter = $stringDelimiter;
        return $this;
    }

    /**
     * Get results as CSV.
     *
     * @param   string  $cell    data
     * @param   string  $header  optional name of the cell
     * @return  string
     */
    public function convertCellToCSV(string $cell, string $header = ""): string
    {
        assert(!isset($lang), 'Cannot redeclare var $lang');
        $lang = \Yana\Translations\Facade::getInstance();
        assert(!isset($csv), 'Cannot redeclare var $csv');
        $csv = "";
        // create header
        if ($this->hasHeader() && $header > "") {
            $csv .= $this->_convertValueToCSV($lang->replaceToken($header)) . $this->getRowDelimiter();
        }
        // create body
        return $csv . $this->_convertValueToCSV($cell) . $this->getRowDelimiter();
    }

    /**
     * Get results as CSV.
     *
     * @param   array  $row     data
     * @param   array  $header  array of strings as names of the cells
     * @return  string
     */
    public function convertRowToCSV(array $row, array $header = array()): string
    {
        assert(!isset($lang), 'Cannot redeclare var $lang');
        $lang = \Yana\Translations\Facade::getInstance();
        assert(!isset($csv), 'Cannot redeclare var $csv');
        $csv = "";
        // create header
        if ($this->hasHeader()) {
            // fallback in case no header was provided
            if (empty($header)) {
                foreach (array_keys($row) as $title)
                {
                    $header[] = $lang->replaceToken((string) $title);
                }
            }
            $csv .= $this->_convertRowToCsv($header);
        }
        // create body
        return $csv . $this->_convertRowToCsv($row);
    }

    /**
     * Get results as CSV.
     *
     * This exports the data as a comma-seperated list of values.
     *
     * You may choose custom column and row delimiters by setting the parameters
     * to appropriate values.
     *
     * The first line may contain a header of column titles. To exclude this
     * information from the result, set headers to bool(false).
     *
     * Example output:
     * <pre>
     * "Last Name","First Name","Title","Name of Book"
     * "Smith","Steven, M.","Mr.","The ""Cookbook"" of Time"
     * "Higgings","Barbara","Ms.","A multiline guide
     * to the Galaxy"
     * </pre>
     *
     * The function returns the CSV contents as a multi-line string.
     *
     * The CSV format is defined in {@link http://www.rfc-editor.org/rfc/rfc4180.txt RFC 4180}.
     *
     * @param   array  $table   data
     * @param   array  $header  array of strings as names of the cells
     * @return  string
     */
    public function convertTableToCSV(array $table, array $header = array()): string
    {
        assert(!isset($csv), 'Cannot redeclare var $csv');
        $csv = "";
        assert(!isset($lang), 'Cannot redeclare var $lang');
        $lang = \Yana\Translations\Facade::getInstance();

        assert(!isset($headers), 'Cannot redeclare var $headers');
        $headers = array();
        // create header
        if ($this->hasHeader()) {
            assert(!isset($title), 'Cannot redeclare var $columnTitle');
            foreach ($header as $title)
            {
                $headers[] = $lang->replaceToken((string) $title);
            }
            unset($title);
            // fallback in case no header was provided
            if (empty($header) && !empty($table)) {
                foreach (array_keys(current($table)) as $title)
                {
                    $headers[] = $lang->replaceToken((string) $title);
                }
            }
            $csv .= $this->_convertRowToCsv($headers);
        }
        // create body
        foreach ($table as $row)
        {
            if (is_array($row)) {
                $csv .= $this->_convertRowToCsv($row);
            } else {
                $csv .= $this->_convertValueToCSV($row);
            }
        }
        return $csv;
    }

    /**
     * Returns the CSV contents as a single-line string.
     *
     * @param   array   $row  row data
     * @return  string
     */
    private function _convertRowToCsv(array $row): string
    {
        $csv = "";
        foreach ($row as $value)
        {
            if (!empty($csv)) {
                $csv .= $this->getColumnDelimiter();
            }
            $csv .= $this->_convertValueToCSV($value);
        }
        return $csv . $this->getRowDelimiter();
    }

    /**
     * Returns an escaped string for the given value.
     *
     * @param   mixed   $value  data
     * @return  string
     */
    private function _convertValueToCSV($value): string
    {
        $stringDelim = $this->getStringDelimiter();
        return $stringDelim . str_replace($stringDelim, $stringDelim . $stringDelim, print_r($value, true)) . $stringDelim;
    }

}

?>