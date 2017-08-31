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

namespace Yana\Data;

/**
 * Used for validating number input.
 *
 * @package     yana
 * @subpackage  io
 */
class FloatValidator extends NumberValidator
{

    /**
     * Set maximum length in digits.
     *
     * @param   int  $precision  positive number, -1 = no restrictions
     * @return  FloatValidator 
     */
    public function setPrecision($precision)
    {
        assert('is_int($precision); // $precision expected to be Integer');
        parent::setPrecision($precision);
        return $this;
    }

    /**
     * Set maximum length in digits.
     *
     * @param   int  $length  positive number, 0 = no restrictions
     * @return  FloatValidator 
     */
    public function setMaxLength($length)
    {
        assert('is_int($length); // $length expected to be Integer');
        parent::setMaxLength($length);
        return $this;
    }

    /**
     * Set wether value must be >= 0.
     *
     * @param   bool  $isUnsigned  true = must be positive integer, false = negative values allowed
     * @return  FloatValidator
     */
    public function setUnsigned($isUnsigned)
    {
        assert('is_bool($isUnsigned); // $isUnsigned expected to be Boolean');
        parent::setUnsigned($isUnsigned);
        return $this;
    }

    /**
     * Validate a value as integer.
     *
     * Returns bool(true) if valid and bool(false) if not.
     *
     * @param   mixed  $float       value to validate
     * @param   int    $maxFloat    maximum number
     * @param   bool   $isUnsigned  wether value must be >= 0
     * @return  bool
     */
    public static function validate($float, $maxFloat = 0, $isUnsigned = false)
    {
        assert('is_int($maxFloat) || is_float($maxFloat); // $maxFloat expected to be Float or Integer');
        assert('is_bool($isUnsigned); // $isUnsigned expected to be Boolean');
        // Note! Function filter_var() casts input to string and evaluates the string based on the selected system locale.
        // It will thuse reject valid float values due to invalid decimal separator if the system locale doesn't use a point
        // as decimal separator. It is thus necessary to handle this case separately.
        return (is_float($float) || filter_var($float, FILTER_VALIDATE_FLOAT) !== false) &&
            !self::_exceedsMaxLength($float, $maxFloat) && (!$isUnsigned || $float >= 0);
    }

    /**
     * Sanitize input number.
     *
     * Examples:
     * <pre>
     * $value=-3.1,   $length=1, $precision 0: returns -3
     * $value=3.4,    $length=1, $precision 0: returns 3
     * $value=3.5,    $length=1, $precision 0: returns 4
     * $value=3.21,   $length=1, $precision 1: returns 3.2
     * $value=13.5,   $length=1, $precision 1: returns 9.9
     * $value=11.11,  $length=2, $precision 1: returns 11.1
     * $value=111.11, $length=2, $precision 1: returns 99.9
     * $value=0.115,  $length=0, $precision 2: returns .12
     * $value=5.115,  $length=1, $precision 2: returns 5.12
     * </pre>
     *
     * @param   mixed  $float       value to sanitize
     * @param   int    $maxLength   maximum number of digits (including precision)
     * @param   int    $precision   maximum precision length in digits
     * @param   bool   $isUnsigned  wether value must be >= 0
     * @return  mixed 
     */
    public static function sanitize($float, $maxLength = 0, $precision = -1, $isUnsigned = false)
    {
        assert('is_int($maxLength); // $maxLength expected to be Integer');
        assert('is_int($precision); // $precision expected to be Integer');
        assert('is_bool($isUnsigned); // $isUnsigned expected to be Boolean');
        $validator = new self();
        return $validator->setMaxLength($maxLength)
            ->setPrecision($precision)
            ->setUnsigned($isUnsigned)
            ->__invoke($float);
    }

    /**
     * Sanitize input number.
     *
     * @param   mixed  $value  value to sanitize
     * @return  float 
     */
    public function __invoke($value)
    {
        return (float) parent::__invoke($value);
    }

    /**
     * Converts value to the target data type.
     *
     * @param   numeric  $value  value to process
     * @return  float 
     */
    protected function _processTypeCast($value)
    {
        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
        }
        return floatval($value);
    }

}

?>