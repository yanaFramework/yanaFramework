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
 * <<worker>> This does the actual work on the given value.
 *
 * @package     yana
 * @subpackage  db
 */
class ValueSanitizerWorker extends \Yana\Db\Helpers\AbstractValueSanitizerWorker
{

    /**
     * Return value as array.
     *
     * @return  array
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not an array
     */
    public function asArray(): array
    {
        $value = $this->_getValue();
        if (!is_array($value)) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return $value;
    }

    /**
     * Return value as boolean.
     *
     * @return  bool
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value cannot be interpreted as boolean
     */
    public function asBool(): bool
    {
        $value = $this->_getValue();

        if (!is_bool($value)) { // required since bool(false) will return NULL!
            $value = filter_var((string) $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }
        if (!is_bool($value)) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return $value;
    }

    /**
     * Return value as hex-color string.
     *
     * This is a hexadecimal color value.
     * It contains exactly 6 characters of [0-9A-F] and a leading '#' sign.
     * Example: #f01234
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value doesn't have the required format
     */
    public function asColor(): string
    {
        $value = $this->_getValue();
        $options["regexp"] = '/^#[0-9a-f]{6}$/si';
        if (filter_var($value, FILTER_VALIDATE_REGEXP, array("options" => $options)) === false) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return strtoupper((string) $value);
    }

    /**
     * Return date formatted as a string.
     *
     * Example: 2000-05-28
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is cannot be interpreted as boolean
     */
    public function asDateString(): string
    {
        $value = $this->_getValue();
        if (is_array($value) && isset($value['month'], $value['day'], $value['year'])) {
            $value = mktime(0, 0, 0, (int) $value['month'], (int) $value['day'], (int) $value['year']);
        }
        if (is_int($value)) {
            return date('Y-m-d', $value);
        } elseif (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/s', $value)) {
            return $value;
        }
        throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
    }

    /**
     * Return item if it is part of the enumeration.
     *
     * @param   array  $enumerationItems  list of valid items
     * @return  scalar
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not part of the enumeration
     */
    public function asEnumeration(array $enumerationItems)
    {
        $value = $this->_getValue();
        if (!YANA_DB_STRICT || in_array($value, $enumerationItems)) {
            return $value;
        }
        throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
    }

    /**
     * Calculate and return file id.
     *
     * Files and images are both treated in the same way.
     *
     * They are just displayed differently by the GUI and
     * use different code for upload and download in the
     * \Yana\Db\Blob class, which handles all database artifacts.
     *
     * Note! This function does NOT upload the file, it only generates a database ID.
     * It does NOT check if that database ID exists, if the file exists, or anything.
     * It entirely relies in the meta-data it is given and does not check its validity.
     *
     * @param   int  $maxFileSize  in byte
     * @return  string
     * @throws  \Yana\Core\Exceptions\Files\SizeException          when the uploaded file was reported as too big
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not part of the enumeration
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException      when no file was uploaded
     * @throws  \Yana\Core\Exceptions\Files\DeletedException       when the file was deleted
     */
    public function asFileId(int $maxFileSize = 0): ?string
    {
        $value = $this->_getValue();
        if (is_array($value)) {
            /* Value is the uploaded file as if taken from $_FILES[$columnName].
             * This information is used later to iterate over the files to insert or update.
             */
            if (isset($value['error']) && $value['error'] === UPLOAD_ERR_NO_FILE) {
                throw new \Yana\Core\Exceptions\Files\NotFoundException();
            }
            /* check file size
             *
             * Note: the size value is given in 'byte'
             */
            if ($maxFileSize > 0 && isset($value['size']) && $value['size'] > $maxFileSize) {
                $message = "Uploaded file is too large.";
                $alert = new \Yana\Core\Exceptions\Files\SizeException($message);
                $alert->setFilename(isset($value['name']) ? (string) $value['name'] : '');
                throw $alert->setMaxSize((int) $maxFileSize);
            }
            return null;

        } elseif (is_object($value) && $value instanceof \Yana\Http\Uploads\File) {

            if ($value->isNotUploaded()) {
                throw new \Yana\Core\Exceptions\Files\NotFoundException();
            }
            if ($maxFileSize > 0 && $value->getSizeInBytes() > $maxFileSize) {
                $message = "Uploaded file is too large.";
                $alert = new \Yana\Core\Exceptions\Files\SizeException($message);
                $alert->setFilename($value->getName());
                throw $alert->setMaxSize((int) $maxFileSize);
            }
            return null;

        } elseif ($value === "1") {
            throw new \Yana\Core\Exceptions\Files\DeletedException();

        } elseif (is_string($value)) {
            $mapper = new \Yana\Db\Binaries\FileMapper();
            return $mapper->toFileId($value);
        }
        throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
    }

    /**
     * Sanitize value as float within a given range.
     *
     * The value will be rounded to the given precision.
     *
     * @param   float   $maxValue  upper boundary of range
     * @param   float   $minValue  lower boundary of range
     * @return  float
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a valid number or out of range
     */
    public function asRangeValue(float $maxValue, float $minValue = 0.0): float
    {
        $value = filter_var($this->_getValue(), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
            $message = "Input is not a valid number.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException($message, $level);
        }
        if ((float) $value > $maxValue || (float) $value < $minValue) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return (float) $value;
    }

    /**
     * Sanitize value as float.
     *
     * The value will be rounded to the given precision.
     * Rounding strategy is default (round half up).
     *
     * @param   int   $maxLength   number of digits before decimal point
     * @param   int   $precision   number of digits after decimal point
     * @param   bool  $isUnsigned  whether or not the integer can be negative
     * @return  float
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a valid number
     */
    public function asFloat(int $maxLength = 0, int $precision = 0, bool $isUnsigned = false): float
    {
        $value = $this->_getValue();
        if (\Yana\Data\FloatValidator::validate($value, (int) $maxLength - (int) $precision, (bool) $isUnsigned) === false) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return round($value, $precision);
    }

    /**
     * Sanitize value as mail address.
     *
     * @param   int  $maxLength  in characters (not bytes)
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a string
     */
    public function asHtmlString(int $maxLength = 0): string
    {
        assert(is_int($maxLength), 'Invalid argument type: $maxLength. Integer expected');
        $value = $this->_getValue();
        if (!is_string($value)) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        $value = \Yana\Util\Strings::htmlSpecialChars($value);
        if ($maxLength > 0) {
            $value = mb_substr($value, 0, (int) $maxLength);
        }
        return $value;
    }

    /**
     * Sanitize value as integer.
     *
     * The following IPv4 ranges are blocked: 0.0.0.0/8, 169.254.0.0/16, 192.0.2.0/24, 224.0.0.0/4.
     * Doesn't apply to IPv6 ranges.
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a valid IP
     */
    public function asIpAddress(): string
    {
        $value = $this->_getValue();
        if ($value !== '127.0.0.1' && $value !== '::1' && filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) === false) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return $value;
    }

    /**
     * Sanitize value as integer.
     *
     * @param   int   $maxLength   in digits
     * @param   bool  $isUnsigned  whether or not the integer can be negative
     * @return  int
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a valid number
     */
    public function asInteger(int $maxLength = 0, bool $isUnsigned = false): int
    {
        assert(is_int($maxLength), 'Invalid argument type: $maxLength. Integer expected');
        assert(is_bool($isUnsigned), 'Invalid argument type: $isUnsigned. Boolean expected');
        $value = $this->_getValue();
        if (\Yana\Data\IntegerValidator::validate($value, $maxLength, (bool) $isUnsigned) === false) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return (int) $value;
    }

    /**
     * Return as numeric array.
     *
     * If the input was an associative array, the keys are dropped.
     * The values themselves can be of any type and are not checked.
     *
     * @return  array
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not an array
     */
    public function asListOfValues(): array
    {
        $value = $this->_getValue();
        if (!is_array($value)) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return array_values($value);
    }

    /**
     * Sanitize value as mail address.
     *
     * @param   int  $maxLength  in characters (not bytes)
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a valid mail or longer than the maximum
     */
    public function asMailAddress(int $maxLength = 0): string
    {
        assert(is_int($maxLength), 'Invalid argument type: $maxLength. Integer expected');
        $value = filter_var($this->_getValue(), FILTER_SANITIZE_EMAIL);
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false || ($maxLength > 0 && \mb_strlen($value) > $maxLength)) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return $value;
    }

    /**
     * Return array containing only items of an enumeration.
     *
     * @param   array  $enumerationItems  list of valid items
     * @return  array
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not an array or one of its values is not part of the enumeration
     */
    public function asSetOfEnumerationItems(array $enumerationItems): array
    {
        $value = $this->_getValue();
        if (!is_array($value)) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        if (count(array_diff($value, $enumerationItems)) > 0) {
            $message = "Field is not a valid enumartion set.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException($message, $level);
        }
        unset($enumerationItems);
        return $value;
    }

    /**
     * Calculate simple hash.
     *
     * Warning! This is not meant to replace proper hashing of passwords.
     * All this does is ensure you can't read the password.
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a string
     */
    public function asPassword(): string
    {
        $value = $this->_getValue();
        if (!is_string($value)) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return md5($value);
    }

    /**
     * Sanitize value as string.
     *
     * @param   int  $maxLength  in characters (not bytes)
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a valid string
     */
    public function asString(int $maxLength = 0): string
    {
        assert(is_int($maxLength), 'Invalid argument type: $maxLength. Integer expected');
        $value = $this->_getValue();
        if (!is_string($value)) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return \Yana\Data\StringValidator::sanitize($value, $maxLength, \Yana\Data\StringValidator::LINEBREAK);
    }

    /**
     * Sanitize value as text.
     *
     * @param   int  $maxLength  in characters (not bytes)
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a valid text
     */
    public function asText(int $maxLength = 0): string
    {
        assert(is_int($maxLength), 'Invalid argument type: $maxLength. Integer expected');
        $value = $this->_getValue();
        if (!is_string($value)) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return \Yana\Data\StringValidator::sanitize($value, (int) $maxLength, \Yana\Data\StringValidator::USERTEXT);
    }

    /**
     * Convert to time string.
     *
     * Example: 2000-05-28 18:10:25
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  if the value is not a string or an array containing time information.
     */
    public function asTimeString(): string
    {
        $value = $this->_getTimeValue();
        $format = 'Y-m-d H:i:s';
        if (is_int($value)) {
            return date($format, $value);
        }
        if (!is_string($value) || \DateTime::createFromFormat($format, $value) === false) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return $value;
    }

    /**
     * Convert to timestamp.
     *
     * @return  int
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  if the value is not a timestamp or an array containing time information.
     */
    public function asTimestamp(): int
    {
        $value = $this->_getTimeValue();
        if (!is_int($value)) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return $value;
    }

    /**
     * Converts the value to an integer timestamp, if it is an array containing time information.
     *
     * @return  mixed
     */
    private function _getTimeValue()
    {
        $value = $this->_getValue();
        if (is_array($value)) {
            if (isset($value['hour'], $value['minute'], $value['month'], $value['day'], $value['year'])) {
                $value = mktime(
                    (int) $value['hour'],
                    (int) $value['minute'],
                    0,
                    (int) $value['month'],
                    (int) $value['day'],
                    (int) $value['year']
                );
            }
        }
        return $value;
    }

    /**
     * Sanitize value as URL.
     *
     * @param   int  $maxLength  in characters (not bytes)
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a valid URL
     */
    public function asUrl(int $maxLength = 0): string
    {
        assert(is_int($maxLength), 'Invalid argument type: $maxLength. Integer expected');
        $value = filter_var($this->_getValue(), FILTER_SANITIZE_URL);
        if ($maxLength > 0) {
            $value = mb_substr($value, 0, (int) $maxLength);
        }
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            throw new \Yana\Core\Exceptions\Forms\InvalidValueException();
        }
        return $value;
    }

}

?>