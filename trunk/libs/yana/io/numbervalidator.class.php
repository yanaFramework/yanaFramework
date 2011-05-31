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
 * <<abstract>> Used for validating number input.
 *
 * @package     yana
 * @subpackage  io
 */
abstract class NumberValidator extends AbstractValidator
{

    /**
     * Maximum count of digits.
     *
     * @var int
     */
    private $_length = 0;

    /**
     * Sets wether the input must be >= 0.
     *
     * @var bool
     */
    private $_isUnsigned = false;

    /**
     * Maximum count of digits.
     *
     * @var int
     */
    private $_precision = -1;

    /**
     * Get maximum length in digits.
     *
     * @return  int
     */
    protected function getMaxLength()
    {
        return $this->_length;
    }

    /**
     * Set maximum length in digits.
     *
     * @param   int  $length  positive number, 0 = no restrictions
     * @return  NumberValidator
     */
    protected function setMaxLength($length)
    {
        assert('is_int($length); // Invalid argument $length: int expected');
        assert('$length >= 0; // $length must not be negative');
        $this->_length = (int) $length;
        return $this;
    }

    /**
     * Check if value must be >= 0.
     *
     * @return  bool
     */
    protected function isUnsigned()
    {
        return $this->_isUnsigned;
    }

    /**
     * Set wether value must be >= 0.
     *
     * @param   bool  $isUnsigned  true = must be positive integer, false = negative values allowed
     * @return  NumberValidator
     */
    protected function setUnsigned($isUnsigned)
    {
        assert('is_bool($isUnsigned); // Invalid argument $isUnsigned: bool expected');
        $this->_isUnsigned = (bool) $isUnsigned;
        return $this;
    }

    /**
     * Get maximum length in digits.
     *
     * @return  int
     */
    protected function getPrecision()
    {
        return $this->_precision;
    }

    /**
     * Set maximum length in digits.
     *
     * @param   int  $precision  positive number, -1 = no restrictions
     * @return  NumberValidator 
     */
    protected function setPrecision($precision)
    {
        assert('is_int($precision); // Invalid argument $precision: int expected');
        $this->_precision = (int) $precision;
        return $this;
    }

    /**
     * Sanitize input number.
     *
     * @param   numeric  $value  value to sanitize
     * @return  float 
     */
    public function __invoke($value)
    {
        $value = $this->_processTypeCast($value);
        $value = $this->_processMaxLength($value);
        $value = $this->_processUnsigned($value);
        $value = $this->_processPrecision($value);
        return (float) $value;
    }

    /**
     * Converts value to the target data type.
     *
     * @param   numeric  $value  value to process
     * @return  numeric 
     */
    abstract protected function _processTypeCast($value);

    /**
     * Returns the max- / min-number if the value is too big or too small.
     *
     * @param   numeric  $value  value to process
     * @return  numeric 
     */
    protected function _processMaxLength($value)
    {
        $precision = $this->getPrecision();
        $length = $this->getMaxLength();
        if (self::_exceedsMaxLength($value, $length, $precision)) {
            if ($precision > 0) {
                $length -= $precision;
            }
            $value = str_pad("", $length, "9");
            if ($precision > 0) {
                $value .= '.' . str_pad('', $precision, '9');
            }
            if ($value < 0) {
                $value = '-' . $value;
            }
        }
        return $value;
    }

    /**
     * Limit number of digits to maximum length.
     *
     * @param   numeric  $value  value to process
     * @return  numeric 
     */
    protected function _processPrecision($value)
    {
        $precision = $this->getPrecision();
        if ($precision > -1) {
            $value = round($value, $precision);
        }
        return $value;
    }

    /**
     * Set to absolute value if number is unsigned.
     *
     * @param   numeric  $value  value to process
     * @return  numeric 
     */
    protected function _processUnsigned($value)
    {
        if ($this->isUnsigned()) {
            $value = abs($value);
        }
        return $value;
    }

    /**
     * Returns true, if the value is longer than the maximum length in digits.
     *
     * @param   numeric  $value      value to process
     * @param   int      $length     maximum length in digits (incl. fraction)
     * @param   int      $precision  maximum length of fraction in digits
     * @return  bool
     */
    protected static function _exceedsMaxLength($value, $length, $precision = 0)
    {
        $test = false;
        if ($length > 0) {
            if ($precision > 0) {
                $length -= $precision;
            }
            $test = abs($value) >= pow(10, $length);
        }
        return $test;
    }

}

?>