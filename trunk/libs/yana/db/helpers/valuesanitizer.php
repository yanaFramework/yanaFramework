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

namespace Yana\Db\Helpers;

/**
 * <<strategy>> This class is meant to be used to sanitize values before sending them to the database.
 *
 * @package     yana
 * @subpackage  db
 */
class ValueSanitizer extends \Yana\Core\Object implements \Yana\Db\Helpers\IsSanitizer
{

    /**
     * Name of target DBMS.
     *
     * @var  array
     */
    private $_dbms = "";

    /**
     * Sets the target DBMS.
     *
     * @param  string  $dbms  name of DBMS to sanitize values for
     */
    public function __construct($dbms = "generic")
    {
        assert('is_string($dbms); // Invalid argument $dbms: string expected');

        $this->_dbms = (string) $dbms;
    }

    /**
     * Returns the name of the chosen DBMS as a lower-cased string.
     *
     * @return  string
     */
    protected function _getDBMS()
    {
        return $this->_dbms;
    }

    /**
     * Validate a row against database schema.
     *
     * The argument $row is expected to be an associative array of values, representing
     * a row that should be inserted or updated in the table. The keys of the array $row are
     * expected to be the lowercased column names.
     *
     * Returns bool(true) if $row is valid and bool(false) otherwise.
     *
     * @param   array   $row       values of the inserted/updated row
     * @param   bool    $isInsert  type of operation (true = insert, false = update)
     * @param   array   &$files    list of modified or inserted columns of type file or image
     * @return  array
     * @throws  \Yana\Core\Exceptions\NotWriteableException         when a target column or table is not writeable
     * @throws  \Yana\Core\Exceptions\NotFoundException             when the column definition is invalid
     * @throws  \Yana\Core\Exceptions\NotImplementedException       when a column was encountered that has an unknown datatype
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException   when a given value is not valid
     * @throws  \Yana\Core\Exceptions\Forms\InvalidSyntaxException  when a value does not match a required pattern or syntax
     * @throws  \Yana\Core\Exceptions\Forms\MissingFieldException   when a not-nullable column is missing
     * @throws  \Yana\Core\Exceptions\Forms\FieldNotFoundException  when a value was provided but no corresponding column exists
     * @throws  \Yana\Core\Exceptions\Files\SizeException           when an uploaded file is too large
     */
    public function sanitizeRowByTable(\Yana\Db\Ddl\Table $table, array $row, $isInsert = true, array &$files = array())
    {
        assert('is_bool($isInsert); // Wrong type for argument 2. Boolean expected');
        $outputRow = array();
        /* @var $column \Yana\Db\Ddl\Column */
        foreach ($table->getColumns() as $column)
        {
            $columnName = $column->getName();
            /*
             * error - not writeable
             */
            if (!$isInsert && $column->isReadonly() && !array_key_exists($columnName, $row)) {
                throw new \Yana\Core\Exceptions\NotWriteableException("Database is readonly. " .
                    "Update operation on table '{$table->getName()}' aborted.");
            }
            /*
             * valid - value may be empty for update-queries
             */
            if (!$isInsert && !array_key_exists($columnName, $row)) {
                continue;
            }
            /*
             * 3) value is not set (and requires closer investigation)
             */
            if (!isset($row[$columnName]) || $row[$columnName] === "") {

                $default = $column->getAutoValue($this->_getDBMS());

                /*
                 * autofill column
                 */
                if (!is_null($default)) {
                    $outputRow[$columnName] = $default;
                    unset($row[$columnName]);
                    continue;

                } elseif ($column->isAutoIncrement()) {
                    unset($row[$columnName]);
                    continue;
                }

                /*
                 * error - value is missing
                 */
                if (!$column->isNullable()) {
                    $title = $column->getTitle();
                    if (empty($title)) {
                        $title = $column->getName();
                    }
                    $message = "A mandatory column has not been provided: " . $title;
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    $warning = new \Yana\Core\Exceptions\Forms\MissingFieldException($message, $level);
                    throw $warning->setField($title);

                } elseif (array_key_exists($columnName, $row)) {
                    $outputRow[$columnName] = null; // Don't set values to NULL if they are not given
                }
            /*
             * 4) this input is valid - move to next
             */
            } elseif (isset($row[$columnName])) {
                $outputRow[$columnName] = $this->sanitizeValueByColumn($column, $row[$columnName], $files); // may throw exception
            } // end if
            unset($row[$columnName]);
        } // end for

        if (count($row) !== 0) {
            throw new \Yana\Core\Exceptions\Forms\FieldNotFoundException("Unknown column(s): " . implode(', ', array_keys($row)));
        }

        return $outputRow;
    }

    /**
     * Validate a row against database schema.
     *
     * The argument $row is expected to be an associative array of values, representing
     * a row that should be inserted or updated in the table. The keys of the array $row are
     * expected to be the lowercased column names.
     *
     * Returns the sanitized value.
     *
     * @param   \Yana\Db\Ddl\Column $column  
     * @param   mixed               $value   value of the inserted/updated row
     * @param   array               &$files  list of modified or inserted columns of type file or image
     * @return  mixed
     * @throws  \Yana\Core\Exceptions\NotFoundException            if the column definition is invalid
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  if an invalid value is encountered, that could not be sanitized
     * @throws  \Yana\Core\Exceptions\Forms\InvalidSyntaxException if a value does not match a required pattern or syntax
     * @throws  \Yana\Core\Exceptions\NotImplementedException      when the column has an unknown datatype
     * @throws  \Yana\Core\Exceptions\Files\SizeException          when uploaded file is too large
     */
    public function sanitizeValueByColumn(\Yana\Db\Ddl\Column $column, $value, array &$files = array())
    {
        $title = $column->getTitle();
        if (empty($title)) {
            $title = $column->getName();
        }
        $refColumn = $column->getReferenceColumn();

        // validate pattern
        $pattern = $refColumn->getPattern();
        if (!empty($pattern) && !preg_match("/^$pattern\$/", $value)) {
            $message = "Field data does not match pattern.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $error = new \Yana\Core\Exceptions\Forms\InvalidSyntaxException($message, $level);
            throw $error->setValid($pattern)->setValue($value)->setField($title);
        }
        $worker = new \Yana\Db\Helpers\ValueSanitizerWorker($value);

        switch ($refColumn->getType())
        {
            case \Yana\Db\Ddl\ColumnTypeEnumeration::ARR:
                return $worker->asArray();

            case \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL:
                return $worker->asBool();

            case \Yana\Db\Ddl\ColumnTypeEnumeration::COLOR:
                return $worker->asColor();

            case \Yana\Db\Ddl\ColumnTypeEnumeration::DATE:
                return $worker->asDateString();

            case \Yana\Db\Ddl\ColumnTypeEnumeration::ENUM:
                return $worker->asEnumeration($refColumn->getEnumerationItemNames());

            case \Yana\Db\Ddl\ColumnTypeEnumeration::IMAGE:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::FILE:
                try {
                    $fileId = $worker->asFileId((int) $refColumn->getSize());

                } catch (\Yana\Core\Exceptions\Files\NotFoundException $e) {
                    return null;

                } catch (\Yana\Core\Exceptions\Files\DeletedException $e) {
                    $files[] = array('column' => $column);
                    return "";
                }

                if (is_string($fileId)) {
                    return $fileId;
                }
                $value['column'] = $column;
                $files[] = $value;
                $idGenerator = new \Yana\Db\Helpers\IdGenerator();
                return $idGenerator($column);

            case \Yana\Db\Ddl\ColumnTypeEnumeration::RANGE:
                return $worker->asRangeValue((int) $refColumn->getRangeMax(), (int) $refColumn->getRangeMin());

            case \Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT:
                return $worker->asFloat((int) $refColumn->getLength(), (int) $refColumn->getPrecision(), (bool) $refColumn->isUnsigned());

            case \Yana\Db\Ddl\ColumnTypeEnumeration::HTML:
                return $worker->asHtmlString((int) $refColumn->getLength());

            case \Yana\Db\Ddl\ColumnTypeEnumeration::INET:
                return $worker->asIpAddress();

            case \Yana\Db\Ddl\ColumnTypeEnumeration::INT:
                return $worker->asInteger((int) $refColumn->getLength(), (bool) $refColumn->isUnsigned());

            case \Yana\Db\Ddl\ColumnTypeEnumeration::LST:
                return $worker->asListOfValues();

            case \Yana\Db\Ddl\ColumnTypeEnumeration::MAIL:
                return $worker->asMailAddress((int) $refColumn->getLength());

            case \Yana\Db\Ddl\ColumnTypeEnumeration::PASSWORD:
                return $worker->asPassword();

            case \Yana\Db\Ddl\ColumnTypeEnumeration::SET:
                return $worker->asSetOfEnumerationItems($refColumn->getEnumerationItemNames());

            case \Yana\Db\Ddl\ColumnTypeEnumeration::REFERENCE:
            case \Yana\Db\Ddl\ColumnTypeEnumeration::STRING:
                return $worker->asString((int) $refColumn->getLength());

            case \Yana\Db\Ddl\ColumnTypeEnumeration::TEXT:
                return $worker->asText((int) $refColumn->getLength());

            case \Yana\Db\Ddl\ColumnTypeEnumeration::TIME:
                return $worker->asTimeString();

            case \Yana\Db\Ddl\ColumnTypeEnumeration::TIMESTAMP:
                return $worker->asTimestamp();

            case \Yana\Db\Ddl\ColumnTypeEnumeration::URL:
                return $worker->asUrl((int) $refColumn->getLength());

            default:
                assert('!in_array($value, \Yana\Db\Ddl\ColumnTypeEnumeration::getSupportedTypes()); // Unhandled column type. ');
                throw new \Yana\Core\Exceptions\NotImplementedException(
                    "Type '" . $refColumn->getType() . "' not implemented.", \Yana\Log\TypeEnumeration::ERROR
                );
        }
    }

}

?>