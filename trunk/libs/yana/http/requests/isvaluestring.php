<?php
/**
 * YANA library
 *
 * Primary controller class
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

namespace Yana\Http\Requests;

/**
 * <<interface>> Request variable.
 *
 * Allows validaty checks and conversion.
 *
 * @package     yana
 * @subpackage  http
 */
interface IsValueString
{

    /**
     * Returns bool(true) if both values are equal.
     *
     * @param   mixed  $value  to compare with
     * @return  bool
     */
    public function is($value);

    /**
     * Returns bool(true) if the value does NOT equal NULL.
     *
     * @return  bool
     */
    public function isNotNull();

    /**
     * Returns bool(true) if the value does equal NULL.
     *
     * @return  bool
     */
    public function isNull();

    /**
     * Returns the string as is.
     *
     * Use this only for input that is allowed to have special characters and/or HTML and you are prepared to deal with it otherwise.
     *
     * @return  string
     */
    public function asUnsafeString();

    /**
     * Sanitizes the string and returns it.
     *
     * Be warned that this will remove HTML and special characters.
     *
     * @return  string
     */
    public function asSafeString();

    /**
     * Sanitizes the string to one line and returns it.
     *
     * Use this for text coming from text input fields.
     *
     * Any white-space found will be converted to spaces.
     * Be warned that this will also remove HTML and special characters.
     * 
     * @return  string
     */
    public function asOneLineString();

    /**
     * Sanitizes the string for output and returns it.
     *
     * Use this for text coming from textarea fields.
     *
     * In addition to the usual filters, this also inserts [wbr]-tags, if a string of letters ist too long,
     * detects and replaces line-breaks with [br]-tags. Note that these have to be converted to HTML by
     * calling the appropriate funcitons in your template.
     *
     * This filter will also escape several potentially dangerous characters, like HTML-tags and quotes, with HTML entities.
     * 
     * @return  string
     */
    public function asOutputString();

    /**
     * For floating point numbers.
     *
     * Sanitizes the numeric input and returns it as float.
     * Note that this expects you to use decimal separators that used locale.
     *
     * @return  float
     */
    public function asFloat();

    /**
     * For integer numbers.
     *
     * Sanitizes the numeric input and returns it as integer.
     *
     * @return  int
     */
    public function asInt();

    /**
     * For boolean values.
     *
     * Returns bool(true) for "1", "true", "on" and "yes" and bool(false) for anything else.
     *
     * @return  bool
     */
    public function asBool();

    /**
     * For BIC identifiers.
     *
     * Note: does not check if the BIC actually exists. Only checks if the structure is valid.
     *
     * @return  string
     */
    public function asBic();

    /**
     * For IBAN numbers.
     *
     * Note: does not check if the IBAN actually exists. Only checks if the structure is valid.
     *
     * @return  string
     */
    public function asIban();

    /**
     * For IP addresses.
     *
     * Note: does not check if the address actually exists. Only checks if the structure is valid.
     * Accepts both IPv4 and IPv6 addresses.
     *
     * @return  string
     */
    public function asIp();

    /**
     * For mail addresses.
     *
     * Note: does not check if the address actually exists. Only checks if the structure is valid.
     *
     * @return  string
     */
    public function asMail();

    /**
     * For web addresses.
     *
     * Note: does not check if the address actually exists. Only checks if the structure is valid.
     *
     * For security reasons "javascript" and "file" are not permitted as URL schemes.
     * If you need either of those, please export as unsafe string and sanitize manually.
     *
     * @return  string
     */
    public function asUrl();

    /**
     * Returns bool(true) if the value is NULL, "0" or an empty string.
     *
     * @return  bool
     */
    public function isEmpty();

    /**
     * Returns bool(true) if the value is a numeric string.
     *
     * @return  bool
     */
    public function isNumeric();

    /**
     * Returns bool(true) if the value is a string that can be parsed as integer.
     *
     * @return  bool
     */
    public function isInt();

    /**
     * Returns bool(true) if the value is a string that can be parsed as float.
     *
     * @return  bool
     */
    public function isFloat();

    /**
     * Returns bool(true) if the value is a string instead of an array.
     *
     * @return  bool
     */
    public function isString();

    /**
     * Returns bool(true) if the value is a string that can be interpreted as bool.
     *
     * @return  bool
     */
    public function isBool();

    /**
     * Returns bool(true) if the value is a valid BIC.
     *
     * Note: does not check if the BIC actually exists. Only checks if the structure is valid.
     *
     * @return  bool
     */
    public function isBic();

    /**
     * Returns bool(true) if the value is a valid IBAN.
     *
     * Note: does not check if the IBAN actually exists. Only checks if the structure is valid.
     *
     * @return  bool
     */
    public function isIban();

    /**
     * Returns bool(true) if the value is a valid IP.
     *
     * Note: does not check if the IP actually exists. Only checks if the structure is valid.
     * This will return bool(true) for both IPv4 AND IPv6 addresses.
     *
     * @return  bool
     */
    public function isIp();

    /**
     * Returns bool(true) if the value is a valid mail address.
     *
     * Note: does not check if the address actually exists. Only checks if the structure is valid.
     *
     * @return  bool
     */
    public function isMail();

    /**
     * Returns bool(true) if the value is a valid URL.
     *
     * Note: does not check if the address actually exists. Only checks if the structure is valid.
     *
     * @return  bool
     */
    public function isUrl();

}

?>