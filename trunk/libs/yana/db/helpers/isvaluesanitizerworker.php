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
 * <<interface>> This does the actual work on the given value.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsValueSanitizerWorker
{

    /**
     * Return value as array.
     *
     * @return  array
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not an array
     */
    public function asArray(): array;

    /**
     * Return value as boolean.
     *
     * @return  bool
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value cannot be interpreted as boolean
     */
    public function asBool(): bool;

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
    public function asColor(): string;

    /**
     * Return date formatted as a string.
     *
     * Example: 2000-05-28
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is cannot be interpreted as boolean
     */
    public function asDateString(): string;

    /**
     * Return item if it is part of the enumeration.
     *
     * @param   array  $enumerationItems  list of valid items
     * @return  scalar
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not part of the enumeration
     */
    public function asEnumeration(array $enumerationItems);

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
    public function asFileId(int $maxFileSize = 0): ?string;

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
    public function asRangeValue(float $maxValue, float $minValue = 0.0): float;

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
    public function asFloat(int $maxLength = 0, int $precision = 0, bool $isUnsigned = false): float;

    /**
     * Sanitize value as mail address.
     *
     * @param   int  $maxLength  in characters (not bytes)
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a string
     */
    public function asHtmlString(int $maxLength = 0): string;

    /**
     * Sanitize value as integer.
     *
     * The following IPv4 ranges are blocked: 0.0.0.0/8, 169.254.0.0/16, 192.0.2.0/24, 224.0.0.0/4.
     * Doesn't apply to IPv6 ranges.
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a valid IP
     */
    public function asIpAddress(): string;

    /**
     * Sanitize value as integer.
     *
     * @param   int   $maxLength   in digits
     * @param   bool  $isUnsigned  whether or not the integer can be negative
     * @return  int
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a valid number
     */
    public function asInteger(int $maxLength = 0, bool $isUnsigned = false): int;

    /**
     * Return as numeric array.
     *
     * If the input was an associative array, the keys are dropped.
     * The values themselves can be of any type and are not checked.
     *
     * @return  array
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not an array
     */
    public function asListOfValues(): array;

    /**
     * Sanitize value as mail address.
     *
     * @param   int  $maxLength  in characters (not bytes)
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a valid mail or longer than the maximum
     */
    public function asMailAddress(int $maxLength = 0): string;

    /**
     * Return array containing only items of an enumeration.
     *
     * @param   array  $enumerationItems  list of valid items
     * @return  array
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not an array or one of its values is not part of the enumeration
     */
    public function asSetOfEnumerationItems(array $enumerationItems): array;

    /**
     * Calculate simple hash.
     *
     * Warning! This is not meant to replace proper hashing of passwords.
     * All this does is ensure you can't read the password.
     *
     * Or in other words: This is for working on result sets of SELECT statements ONLY, and even then ONLY as a fallback.
     * Its purpose is to ensure you can't accidentally OUTPUT the password-hash you read from the database.
     * Do NOT rely on this functionality EVER. It is an additional layer, nothing more.
     * There are proper hashing functions for hashing passwords and I stronlgy suggest that you USE them!
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a string
     */
    public function asPassword(): string;

    /**
     * Sanitize value as string.
     *
     * @param   int  $maxLength  in characters (not bytes)
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a valid string
     */
    public function asString(int $maxLength = 0): string;

    /**
     * Sanitize value as text.
     *
     * @param   int  $maxLength  in characters (not bytes)
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a valid text
     */
    public function asText(int $maxLength = 0): string;

    /**
     * Convert to time string.
     *
     * Example: 2000-05-28 18:10:25
     *
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  if the value is not a string or an array containing time information.
     */
    public function asTimeString(): string;

    /**
     * Convert to timestamp.
     *
     * @return  int
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  if the value is not a timestamp or an array containing time information.
     */
    public function asTimestamp(): int;

    /**
     * Sanitize value as URL.
     *
     * @param   int  $maxLength  in characters (not bytes)
     * @return  string
     * @throws  \Yana\Core\Exceptions\Forms\InvalidValueException  when the value is not a valid URL
     */
    public function asUrl(int $maxLength = 0): string;

}

?>