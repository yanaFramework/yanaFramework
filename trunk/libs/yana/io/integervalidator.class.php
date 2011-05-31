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

namespace Yana\Io;

/**
 * Used for validating number input.
 *
 * @package     yana
 * @subpackage  io
 */
class IntegerValidator extends NumberValidator
{

    /**
     * Set maximum length in digits.
     *
     * @param   int  $length  positive number, 0 = no restrictions
     * @return  IntegerValidator 
     */
    public function setMaxLength($length)
    {
        parent::setMaxLength($length);
        return $this;
    }

    /**
     * Set wether value must be >= 0.
     *
     * @param   bool  $isUnsigned  true = must be positive integer, false = negative values allowed
     * @return  IntegerValidator
     */
    public function setUnsigned($isUnsigned)
    {
        parent::setUnsigned($isUnsigned);
        return $this;
    }

    /**
     * Validate a value as integer.
     *
     * Returns bool(true) if valid and bool(false) if not.
     *
     * @param   mixed  $integer     value to validate
     * @param   int    $maxLength   maximum number of digits
     * @param   bool   $isUnsigned  wether value must be >= 0
     * @return  bool
     */
    public static function validate($integer, $maxLength = 0, $isUnsigned = false)
    {
        return filter_var($integer, FILTER_VALIDATE_INT) !== false &&
            !self::_exceedsMaxLength($integer, $maxLength) && (!$isUnsigned || $integer >= 0);
    }

    /**
     * Sanitize input number.
     *
     * Examples:
     * <pre>
     * $value=-3,     $length=1 : returns -3
     * $value=3.2,    $length=1 : returns 3
     * $value=3.4,    $length=1 : returns 3
     * $value=3.5,    $length=1 : returns 4
     * $value=3.6,    $length=1 : returns 4
     * $value=9.9,    $length=1 : returns 9
     * $value=11.11,  $length=2 : returns 11
     * $value=111.11, $length=2 : returns 99
     * $value=10,     $length=1 : returns 9
     * </pre>
     *
     * @param   mixed  $integer     value to sanitize
     * @param   int    $maxLength   maximum number of digits
     * @param   bool   $isUnsigned  wether value must be >= 0
     * @return  mixed 
     */
    public static function sanitize($integer, $maxLength = 0, $isUnsigned = false)
    {
        $validator = new self();
        return $validator->setMaxLength($maxLength)
            ->setUnsigned($isUnsigned)
            ->__invoke($integer);
    }

    /**
     * Sanitize input number.
     *
     * @param   mixed  $value  value to sanitize
     * @return  int 
     */
    public function __invoke($value)
    {
        return (int) parent::__invoke($value);
    }

    /**
     * Converts value to the target data type.
     *
     * @param   numeric  $value  value to process
     * @return  numeric 
     */
    protected function _processTypeCast($value)
    {
        return (int) round(floatval($value));
    }

}

?>