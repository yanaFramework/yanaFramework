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
 * @ignore
 */
declare(strict_types=1);

namespace Yana\Db\Helpers;

/**
 * <<strategy>> This class is meant to be used to sanitize values before sending them to the database.
 *
 * @package     yana
 * @subpackage  db
 */
class ValueConverter extends \Yana\Core\StdObject implements \Yana\Db\Helpers\IsValueConverter
{

    /**
     * Name of target DBMS.
     *
     * @var  array
     */
    private $_dbms = "";

    /**
     * @var \Yana\Db\Sql\Quoting\IsAlgorithm
     */
    private $_quotingAlgorithm = null;

    /**
     * @var \Yana\Db\Binaries\IsFileMapper
     */
    private $_fileMapper = null;

    /**
     * Sets the target DBMS.
     *
     * @param  string  $dbms  name of DBMS to sanitize values for
     */
    public function __construct(string $dbms = \Yana\Db\DriverEnumeration::GENERIC)
    {
        $this->_dbms = $dbms;
    }

    /**
     * Returns the name of the chosen DBMS as a lower-cased string.
     *
     * @return  string
     */
    protected function _getDBMS(): string
    {
        return $this->_dbms;
    }

    /**
     * Return a quoting algorithm to handle strings to be included as values in SQL queries.
     *
     * @return  \Yana\Db\Sql\Quoting\IsAlgorithm
     */
    public function getQuotingAlgorithm(): \Yana\Db\Sql\Quoting\IsAlgorithm
    {
        if (!isset($this->_quotingAlgorithm)) {
            $this->_quotingAlgorithm = new \Yana\Db\Sql\Quoting\GenericAlgorithm();
        }
        return $this->_quotingAlgorithm;
    }

    /**
     * Inject a quoting algorithm.
     *
     * @param   \Yana\Db\Sql\Quoting\IsAlgorithm  $quotingAlgorithm  quoting algorithm to handle strings to be included as values in SQL queries
     * @return  $this
     */
    public function setQuotingAlgorithm(\Yana\Db\Sql\Quoting\IsAlgorithm $quotingAlgorithm)
    {
        $this->_quotingAlgorithm = $quotingAlgorithm;
        return $this;
    }

    /**
     * Returns a file mapper.
     *
     * If none was set, the function will create a new one with default settings and return it.
     *
     * @return \Yana\Db\Binaries\IsFileMapper
     */
    public function getFileMapper(): \Yana\Db\Binaries\IsFileMapper
    {
        if (!isset($this->_fileMapper)) {
            $this->_fileMapper = new \Yana\Db\Binaries\FileMapper();
        }
        return $this->_fileMapper;
    }

    /**
     * Inject a file mapper.
     *
     * This object handles mapping of database Id to physical file names and vice versa.
     *
     * @param   \Yana\Db\Binaries\IsFileMapper  $fileMapper  instance to replace
     * @return  $this
     */
    public function setFileMapper(\Yana\Db\Binaries\IsFileMapper $fileMapper)
    {
        $this->_fileMapper = $fileMapper;
        return $this;
    }

    /**
     * Prepare a database entry for output.
     *
     * @param   mixed                $value   value of the row
     * @param   \Yana\Db\Ddl\Column  $column  base definition
     * @param   string               $key     array address (applies to columns of type array only)
     * @return  mixed
     */
    public function convertToInternalValue($value, \Yana\Db\Ddl\Column $column, string $key = "")
    {
        $title = $column->getTitle();
        if (empty($title)) {
            $title = $column->getName();
        }
        $column = $column->getReferenceColumn();
        $type = $column->getType();
        $length = (int) $column->getLength();

        switch ($type)
        {
            case \Yana\Db\Ddl\ColumnTypeEnumeration::ARR:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::LST:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::SET:
                if (is_string($value)) {
                    $value = json_decode($value, true);

                } elseif (!is_array($value)) {
                    return null; // Just in case the value is invalid
                }
                assert(is_array($value), 'Unexpected result: $value should be an array.');
                if ($key !== "") {
                    $value = \Yana\Util\Hashtable::get($value, mb_strtolower($key));
                }
                return $value;

            case \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL:
                // Everything except 1, TRUE, "true", "yes", "1", and "T" is FALSE
                return ($value === true || $value === 1 || filter_var((string) $value, FILTER_VALIDATE_BOOLEAN) || $value === 'T'); // T = interbase DBMS

            case \Yana\Db\Ddl\ColumnTypeEnumeration::DATE:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::TIME:
                if (!is_string($value)) {
                    return null;
                }
                $timestamp = strtotime($value);
                if (!is_int($timestamp)) {
                    return null;
                }
                return $timestamp;

            case \Yana\Db\Ddl\ColumnTypeEnumeration::HTML:
                if (!is_scalar($value)) {
                    return null;
                }
                return \Yana\Util\Strings::htmlSpecialChars((string) $value);

            case \Yana\Db\Ddl\ColumnTypeEnumeration::FILE:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::IMAGE:
                if (empty($value)) {
                    return null;
                }
                // get filename
                $mapper = $this->getFileMapper();
                $fileType = ($type === 'image') ? \Yana\Db\Binaries\FileTypeEnumeration::IMAGE : \Yana\Db\Binaries\FileTypeEnumeration::FILE;
                $filename = $mapper->toFileName($value, $fileType);
                unset($fileType, $mapper);
                // return NULL if file doesn't exist
                if (!\is_file($filename)) {
                    $filename = null;
                }
                return $filename;

            case \Yana\Db\Ddl\ColumnTypeEnumeration::RANGE:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT:
                if (!is_numeric($value)) {
                    return null;
                }
                $value = (float) $value;
                assert(!isset($precision), 'Cannot redeclare var $precision');
                $precision = $column->getPrecision();
                /* apply precision */
                if ($precision > 0) {
                    $value = round($value, $precision);
                }
                /* apply unsigned */
                if ($column->isUnsigned()) {
                    $value = abs($value);
                }
                /* apply zerofill (MySQL-compatible)
                 *
                 * Example: FLOAT(6,2) ZEROFILL
                 * -12.1 => 0012.10
                 */
                if ($column->isFixed()) {
                    $length = $column->getLength();
                    // fixed length columns are always unsigned
                    $value = (string) abs($value);
                    $number = array();
                    preg_match('/^(\d+)\.(\d+)$/s', $value, $number);
                    $digits = $number[1] . $number[2];
                    if ($precision <= 0) {
                        $precision = strlen((string) $number[2]);
                    }
                    if ($length > 0 && strlen($digits) < $length) {
                        $value = str_pad($number[1], $length - $precision, '0', STR_PAD_LEFT);
                        $value .= '.';
                        $value .= str_pad($number[2], is_int($precision) ? $precision : 0, '0', STR_PAD_RIGHT);
                    }
                }
                return $value;

            case \Yana\Db\Ddl\ColumnTypeEnumeration::INT:
                if (!is_numeric($value)) {
                    return null;
                }
                $value = (int) $value;
                /* apply unsigned */
                if ($column->isUnsigned()) {
                    $value = abs($value);
                }
                /* apply zerofill (MySQL-compatible)
                 *
                 * Example: INT(4) ZEROFILL
                 * -12 => 0012
                 */
                if ($column->isFixed()) {
                    $length = $column->getLength();
                    // fixed length columns are always unsigned
                    $value = (string) abs($value);
                    if ($length > 0 && mb_strlen($value) < $length) {
                        $value = str_pad($value, $length, '0', STR_PAD_LEFT);
                    }
                }
                return $value;

            case \Yana\Db\Ddl\ColumnTypeEnumeration::TIMESTAMP:
                if (!is_numeric($value)) {
                    return null;
                }
                return (int) $value;

            default:
                if (!is_scalar($value)) {
                    return null;
                }
                return "$value";
        }
    }

    /**
     * Validate a row against database schema.
     *
     * @param   \Yana\Db\Ddl\Table  $table  database object to use as base
     * @param   array               $row    values of the inserted/updated row
     * @return  array
     * @throws  \Yana\Core\Exceptions\Forms\FieldNotFoundException  when a value was provided but no corresponding column exists
     */
    public function convertRowToString(\Yana\Db\Ddl\Table $table, array $row): array
    {
        assert(!isset($outputRow), 'Cannot redeclare var $outputRow');
        $outputRow = array();

        assert(!isset($column), 'Cannot redeclare var $column');
        assert(!isset($columnName), 'Cannot redeclare var $columnName');
        assert(!isset($columnType), 'Cannot redeclare var $columnType');
        /* @var $column \Yana\Db\Ddl\Column */
        foreach ($table->getColumns() as $column)
        {
            $columnName = $column->getName();
            $columnType = $column->getReferenceColumn()->getType();
            if (array_key_exists($columnName, $row)) {
                $outputRow[$columnName] = $this->convertValueToString($row[$columnName], $columnType);
                unset($row[$columnName]);
            }
        } // end for
        unset($column, $columnName, $columnType);

        if (count($row) !== 0) {
            throw new \Yana\Core\Exceptions\Forms\FieldNotFoundException("Unknown column(s): " . implode(', ', array_keys($row)));
        }

        return $outputRow;
    }

    /**
     * Serialize value to string.
     *
     * @param   mixed   $value  value of the row
     * @param   string  $type   element of ColumnTypeEnumeration
     * @return  string
     */
    public function convertValueToString($value, string $type): string
    {
        if (is_null($value)) {
            return "NULL";
        }

        switch ($type)
        {
            case \Yana\Db\Ddl\ColumnTypeEnumeration::RANGE:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT:
                return (string) (float) $value;

            case \Yana\Db\Ddl\ColumnTypeEnumeration::TIMESTAMP:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::INT:
                return (string) (int) $value;

            case \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL:
                switch ($this->_getDBMS())
                {
                    case \Yana\Db\DriverEnumeration::FRONTBASE:
                    case \Yana\Db\DriverEnumeration::POSTGRESQL:
                        return $value === true ? "TRUE" : "FALSE";

                    case \Yana\Db\DriverEnumeration::INTERBASE:
                        return $value === true ? "T" : "F";

                    default:
                        return $value === true ? "1" : "0";
                }

            case \Yana\Db\Ddl\ColumnTypeEnumeration::DATE:
                return $this->getQuotingAlgorithm()->quote(date('c', (int) $value));

            case \Yana\Db\Ddl\ColumnTypeEnumeration::ARR:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::LST:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::SET:
                $value = \json_encode($value);
            // fall through
            default:
                return $this->getQuotingAlgorithm()->quote((string) $value);
        }
    }

}

?>