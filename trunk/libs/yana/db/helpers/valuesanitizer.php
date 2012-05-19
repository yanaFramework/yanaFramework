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
     * @access  public
     * @param   array   $row       values of the inserted/updated row
     * @param   bool    $isInsert  type of operation (true = insert, false = update)
     * @param   array   &$files    list of modified or inserted columns of type file or image
     * @return  array
     * @throws  \Yana\Core\Exceptions\NotWriteableException  if a target column or table is not writeable
     * @throws  InvalidValueWarning                          if a given value is missing or not valid
     */
    public function sanitizeRowByTable(\Yana\Db\Ddl\Table $table, array $row, $isInsert = true, array &$files = array())
    {
        assert('is_bool($isInsert); // Wrong type for argument 2. Boolean expected');
        /* @var $column \Yana\Db\Ddl\Column */
        foreach ($table->getColumns() as $column)
        {
            $columnName = $column->getName();
            /*
             * error - not writeable
             */
            if (!$isInsert && $column->isReadonly() && isset($row[$columnName])) {
                throw new \Yana\Core\Exceptions\NotWriteableException("Database is readonly. " .
                    "Update operation on table '{$table->getName()}' aborted.");
            }
            /*
             * valid - value may be empty for update-queries
             */
            if (!$isInsert && !isset($row[$columnName])) {
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
                    $row[$columnName] = $default;
                    continue;
                } elseif ($column->isAutoIncrement()) {
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
                    $warning = new \MissingFieldWarning();
                    throw $warning->setField($title);
                } else {
                    $row[$columnName] = null;
                }
            /*
             * 4) this input is valid - move to next
             */
            } else {
                if (isset($row[$columnName])) {
                    $row[$columnName] = $this->sanitizeValueByColumn($column, $row[$columnName], $files);
                }
            } // end if
        } // end for

        return $row;
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
     * @throws  \Yana\Core\Exceptions\NotFoundException        if the column definition is invalid
     * @throws  InvalidValueWarning                            if an invalid value is encountered, that could not be sanitized
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when the column has an unknown datatype
     */
    public function sanitizeValueByColumn(\Yana\Db\Ddl\Column $column, $value, array &$files = array())
    {
        $title = $column->getTitle();
        if (empty($title)) {
            $title = $column->getName();
        }
        $column = $column->getReferenceColumn();
        $type = $column->getType();
        $length = (int) $column->getLength();

        // validate pattern
        $pattern = $column->getPattern();
        if (!empty($pattern) && !preg_match("/^$pattern\$/", $value)) {
            $error = new \InvalidValueWarning();
            throw $error->setField($title);
        }

        switch ($type)
        {
            case 'array':
                if (is_array($value)) {
                    return $value;
                }
            break;
            case 'bool':
                if (!is_bool($value)) { // required since bool(false) will return NULL!
                    $value = filter_var((string) $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                }
                if (is_bool($value)) {
                    return $value;
                }
            break;
            case 'color':
                /* This is a hexadecimal color value.
                 * It contains exactly 6 characters of [0-9A-F] and a leading '#' sign.
                 * Example: #f01234
                 */
                $options["regexp"] = '/^#[0-9a-f]{6}$/si';
                if (filter_var($value, FILTER_VALIDATE_REGEXP, array("options" => $options)) !== false) {
                    return strtoupper($value);
                }
            break;
            case 'date':
                // example: 2000-05-28
                if (is_array($value) && isset($value['month'], $value['day'], $value['year'])) {
                    $value = mktime(0, 0, 0, $value['month'], $value['day'], $value['year']);
                }
                if (is_int($value)) {
                    return date('Y-m-d', $value);
                } elseif (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/s', $value)) {
                    return $value;
                }
            break;
            case 'enum':
                $enumerationItems = $column->getEnumerationItemNames();
                if (!YANA_DB_STRICT || in_array($value, $enumerationItems)) {
                    return $value;
                }
            break;
            case 'image':
            case 'file':
                /* Files and images are both treated in the same way.
                 * They are just displayed differently by the GUI and
                 * use different code for upload and download in the
                 * \Yana\Db\Blob class, which handles all database artifacts.
                 */
                if (is_array($value)) {
                    /* Value is the uploaded file as if taken from $_FILES[$columnName].
                     * This information is used later to iterate over the files to insert or update.
                     */
                    if (isset($value['error']) && $value['error'] !== UPLOAD_ERR_NO_FILE) {
                        /* check file size
                         *
                         * Note: the size value is given in 'byte'
                         */
                        $maxSize = (int) $column->getSize();
                        if ($maxSize > 0 && $value['size'] > $maxSize) {
                            $alert = new \FilesizeError("", UPLOAD_ERR_SIZE);
                            throw $alert->setFilename($value['name'])->setMaxSize($maxSize);
                        }
                        $id = \Yana\Db\Blob::getNewFileId($column);
                        $value['column'] = $column;
                        $files[] = $value;
                        return $id;
                    } else {
                        return null;
                    }
                } elseif ($value === "1") {
                    // This occurs when a file is deleted
                    $files[] = array('column' => $column);
                    return "";
                } else {
                    return \Yana\Db\Blob::getFileIdFromFilename($value);
                }
            break;
            case 'range':
                $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
                    $error = new \InvalidValueWarning();
                    throw $error->setField($title);
                }
                if (($value <= $column->getRangeMax()) && ($value >= $column->getRangeMin())) {
                    return (float) $value;
                }
            break;
            case 'float':
                $precision = (int) $column->getPrecision();
                if (\Yana\Data\FloatValidator::validate($value, $length - $precision, (bool) $column->isUnsigned())) {
                    return round($value, $precision);
                }
            break;
            case 'html':
                if (is_string($value)) {
                    $value = \Yana\Util\String::htmlSpecialChars($value);
                    if ($length > 0) {
                        $value = mb_substr($value, 0, $length);
                    }
                    return $value;
                }
            break;
            case 'inet':
                if (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $value;
                }
            break;
            case 'integer':
                if (\Yana\Data\IntegerValidator::validate($value, $length, (bool) $column->isUnsigned())) {
                    return (int) $value;
                }
            break;
            case 'list':
                if (is_array($value)) {
                    return array_values($value);
                }
            break;
            case 'mail':
                $value = filter_var($value, FILTER_SANITIZE_EMAIL);
                if ($length > 0) {
                    $value = mb_substr($value, 0, $length);
                }
                if (filter_var($value, FILTER_VALIDATE_EMAIL) !== false) {
                    return $value;
                }
            break;
            case 'password':
                if (is_string($value)) {
                    return md5($value);
                }
            break;
            case 'set':
                if (is_array($value)) {
                    if (YANA_DB_STRICT) {
                        $enumerationItems = $column->getEnumerationItemNames();
                        if (count(array_diff($value, $enumerationItems)) > 0) {
                            $error = new \InvalidValueWarning();
                            throw $error->setField($title);
                        }
                        unset($enumerationItems);
                    }
                    return $value;
                }
            break;
            case 'reference':
            case 'string':
                if (is_string($value)) {
                    return \Yana\Data\StringValidator::sanitize($value, $length, \Yana\Data\StringValidator::LINEBREAK);
                }
            break;
            case 'text':
                if (is_string($value)) {
                    return \Yana\Data\StringValidator::sanitize($value, $length, \Yana\Data\StringValidator::USERTEXT);
                }
            break;
            case 'time':
            case 'timestamp':
                if (is_array($value)) {
                    if (isset($value['hour'], $value['minute'], $value['month'], $value['day'], $value['year'])) {
                        $value = mktime(
                            $value['hour'],
                            $value['minute'],
                            0,
                            $value['month'],
                            $value['day'],
                            $value['year']
                        );
                    }
                }
                if ($type === 'time') {
                    if (is_int($value)) {
                        return date('c', $value);
                    } elseif (is_string($value)) {
                        // 2000-05-28T18:10:25+00:00
                        if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}([\+\-]\d{2}:\d{2})?$/s', $value)) {
                            return $value;
                        }
                    }
                } elseif (is_int($value)) { // $type === 'timestamp'
                    return $value;
                }
            break;
            case 'url':
                $value = filter_var($value, FILTER_SANITIZE_URL);
                if ($length > 0) {
                    $value = mb_substr($value, 0, $length);
                }
                if (filter_var($value, FILTER_VALIDATE_URL) !== false) {
                    return $value;
                }
            break;
            default:
                assert('!in_array($value, self::getSupportedTypes()); // Unhandled column type. ');
                throw new \Yana\Core\Exceptions\NotImplementedException("Type '$type' not implemented.", E_USER_ERROR);
        }
        $error = new \InvalidValueWarning();
        $error->setField($title);
        throw $error;
    }

}

?>