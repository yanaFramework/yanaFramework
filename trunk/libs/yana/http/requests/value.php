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
 * Request variable.
 *
 * Allows validaty checks and conversion.
 *
 * @package     yana
 * @subpackage  http
 */
class Value extends \Yana\Core\Object implements \Yana\Http\Requests\IsValue
{

    /**
     * raw, unsafe variable value as string or array of strings
     *
     * @var  mixed
     */
    private $_value = null;

    /**
     * Wrap a value with a new instance of this class.
     *
     * @param  mixed  $value  to wrap
     */
    public function __construct($value)
    {
        assert('is_null($value) || is_scalar($value) || is_array($value); // $value expected to be String');
        if (!is_null($value)) {
            $this->_value = (is_scalar($value)) ? (string) $value : (array) $value;
        }
    }

    /**
     * Returns the raw, unsafe value.
     *
     * @return  mixed
     */
    protected function _getValue()
    {
        return $this->_value;
    }

    /**
     * Returns bool(true) if both values are equal.
     *
     * @param   mixed  $value  to compare with
     * @return  bool
     */
    public function is($value)
    {
        return $this->_getValue() == $value;
    }

    /**
     * Returns bool(true) if the value does NOT equal NULL.
     *
     * @return  bool
     */
    public function isNotNull()
    {
        return !$this->isNull();
    }

    /**
     * Returns bool(true) if the value does equal NULL.
     *
     * @return  bool
     */
    public function isNull()
    {
        return is_null($this->_getValue());
    }

    /**
     * Returns the string as is.
     *
     * Use this only for input that is allowed to have special characters and/or HTML and you are prepared to deal with it otherwise.
     *
     * @return  string
     */
    public function asUnsafeString()
    {
        return (string) filter_var($this->_getValue(), FILTER_UNSAFE_RAW);
    }

    /**
     * Sanitizes the string and returns it.
     *
     * Be warned that this will remove HTML and special characters.
     *
     * @return  string
     */
    public function asSafeString()
    {
        return (string) filter_var(\substr((string) $this->_getValue(), 0, 50000), FILTER_SANITIZE_STRING);
    }

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
    public function asOneLineString()
    {
        return \Yana\Data\StringValidator::sanitize($this->asSafeString(), 50000, \Yana\Data\StringValidator::LINEBREAK);
    }

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
    public function asOutputString()
    {
        return \Yana\Data\StringValidator::sanitize($this->_getValue(), 50000, \Yana\Data\StringValidator::USERTEXT);
    }

    /**
     * For floating point numbers.
     *
     * Sanitizes the numeric input and returns it as float.
     * Note that this expects you to use decimal separators that used locale.
     *
     * @return  float
     */
    public function asFloat()
    {
        return \Yana\Data\FloatValidator::sanitize($this->_getValue());
    }

    /**
     * For integer numbers.
     *
     * Sanitizes the numeric input and returns it as integer.
     *
     * @return  int
     */
    public function asInt()
    {
        return \Yana\Data\IntegerValidator::sanitize($this->_getValue());
    }

    /**
     * For boolean values.
     *
     * Returns bool(true) for "1", "true", "on" and "yes" and bool(false) for anything else.
     *
     * @return  bool
     */
    public function asBool()
    {
        $value = $this->_getValue();

        return (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * For BIC identifiers.
     *
     * Note: does not check if the BIC actually exists. Only checks if the structure is valid.
     *
     * @return  string
     */
    public function asBic()
    {
        $validator = new \Yana\Data\BicValidator();
        return $validator($this->_getValue());
    }

    /**
     * For IBAN numbers.
     *
     * Note: does not check if the IBAN actually exists. Only checks if the structure is valid.
     *
     * @return  string
     */
    public function asIban()
    {
        $validator = new \Yana\Data\IbanValidator();
        return $validator($this->_getValue());
    }

    /**
     * For IP addresses.
     *
     * Note: does not check if the address actually exists. Only checks if the structure is valid.
     * Accepts both IPv4 and IPv6 addresses.
     *
     * @return  string
     */
    public function asIp()
    {
        $validator = new \Yana\Data\IpValidator();
        return $validator($this->_getValue());
    }

    /**
     * For mail addresses.
     *
     * Note: does not check if the address actually exists. Only checks if the structure is valid.
     *
     * @return  string
     */
    public function asMail()
    {
        return \Yana\Data\MailValidator::sanitize($this->_getValue());
    }

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
    public function asUrl()
    {
        return \Yana\Data\UrlValidator::sanitize($this->_getValue());
    }

    /**
     * Returns contents as unsanitized array.
     *
     * If the contents are not an array, they are wrapped in a numeric array, where the value is at index "0".
     *
     * @return  array
     */
    public function asUnsafeArray()
    {
        $value = $this->_getValue();
        return (is_array($value)) ? $value : ((!is_null($value)) ? array($value) : array());
    }

    /**
     * Returns contents as sanitized array.
     *
     * If the contents are not an array, they are wrapped in a numeric array, where the value is at index "0".
     *
     * @return  array
     */
    public function asArrayOfSafeStrings()
    {
        $value = $this->_getValue();
        if (is_array($value)) {
            \array_walk_recursive($value, function (&$item, $key) {
                $valueObject = new \Yana\Http\Requests\Value($item);
                $item = ($valueObject->isScalar()) ? $valueObject->asSafeString() : $valueObject->asArrayOfSafeStrings();
            });
        } elseif (!is_null($value)) {
            $value = $this->asSafeString();
        }
        return (is_array($value)) ? $value : ((!is_null($value)) ? array($value) : array());
    }

    /**
     * Returns contents as collection for sanitation.
     *
     * @return  \Yana\Http\Requests\ValueWrapper
     */
    public function all()
    {
        return new \Yana\Http\Requests\ValueWrapper($this->asUnsafeArray());
    }

    /**
     * @return  bool
     */
    public function isArray()
    {
        $value = $this->_getValue();
        return is_array($value);
    }

    /**
     * Returns bool(true) if this is a scalar value.
     *
     * Note that typically input values can come in one of two types: strings and arrays of strings.
     * Thus not every input value is automatically scalar.
     *
     * @return  bool
     */
    public function isScalar()
    {
        return is_scalar($this->_getValue());
    }

    /**
     * Returns bool(true) if the value is NULL, "0" or an empty string.
     *
     * @return  bool
     */
    public function isEmpty()
    {
        $test = $this->_getValue();
        return empty($test);
    }

    /**
     * Returns bool(true) if the value is a numeric string.
     *
     * @return  bool
     */
    public function isNumeric()
    {
        return \is_numeric($this->_getValue());
    }

    /**
     * Returns bool(true) if the value is a string that can be parsed as integer.
     *
     * @return  bool
     */
    public function isInt()
    {
        return \Yana\Data\IntegerValidator::validate($this->_getValue());
    }

    /**
     * Returns bool(true) if the value is a string that can be parsed as float.
     *
     * @return  bool
     */
    public function isFloat()
    {
        return \Yana\Data\FloatValidator::validate($this->_getValue());
    }

    /**
     * Returns bool(true) if the value is a string instead of an array.
     *
     * @return  bool
     */
    public function isString()
    {
        return is_string($this->_getValue());
    }

    /**
     * Returns bool(true) if the value is a string that can be interpreted as bool.
     *
     * @return  bool
     */
    public function isBool()
    {
        return !$this->isNull() && !is_null(filter_var($this->_getValue(), \FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
    }

    /**
     * Returns bool(true) if the value is a valid BIC.
     *
     * Note: does not check if the BIC actually exists. Only checks if the structure is valid.
     *
     * @return  bool
     */
    public function isBic()
    {
        return \Yana\Data\BicValidator::validate($this->_getValue());
    }

    /**
     * Returns bool(true) if the value is a valid IBAN.
     *
     * Note: does not check if the IBAN actually exists. Only checks if the structure is valid.
     *
     * @return  bool
     */
    public function isIban()
    {
        return \Yana\Data\IbanValidator::validate($this->_getValue());
    }

    /**
     * Returns bool(true) if the value is a valid IP.
     *
     * Note: does not check if the IP actually exists. Only checks if the structure is valid.
     * This will return bool(true) for both IPv4 AND IPv6 addresses.
     *
     * @return  bool
     */
    public function isIp()
    {
        return \Yana\Data\IpValidator::validate($this->_getValue());
    }

    /**
     * Returns bool(true) if the value is a valid mail address.
     *
     * Note: does not check if the address actually exists. Only checks if the structure is valid.
     *
     * @return  bool
     */
    public function isMail()
    {
        return \Yana\Data\MailValidator::validate($this->_getValue());
    }

    /**
     * Returns bool(true) if the value is a valid URL.
     *
     * Note: does not check if the address actually exists. Only checks if the structure is valid.
     *
     * @return  bool
     */
    public function isUrl()
    {
        return \Yana\Data\UrlValidator::validate($this->_getValue());
    }

}

?>