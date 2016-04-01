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
 * <<wrapper>> For collection of request variables.
 *
 * @package     yana
 * @subpackage  http
 */
class ValueWrapper extends \Yana\Http\Requests\AbstractValueWrapper
{

    /**
     * Check if value exists.
     *
     * @param   scalar  $id  index of item to test
     * @return  bool
     */
    public function has($id)
    {
        return array_key_exists(\strtolower($id), $this->_getValues());
    }

    /**
     * Return value at offset.
     *
     * If the requested value does not exist, a value object containing the default parameter is returned instead.
     *
     * @param   scalar  $id       of the value within the request data
     * @param   mixed   $default  to be provided, if the value is not found
     * @return  \Yana\Http\Requests\IsValue
     */
    public function value($id, $default = null)
    {
        assert('is_scalar($id); // Invalid argument: $id. Scalar expected');

        assert('!isset($lowerId); // Cannot redeclare var $lowerId');
        $lowerId = \strtolower($id);
        assert('!isset($value); // Cannot redeclare var $value');
        $value = $default;
        if ($this->has($lowerId)) {
            assert('!isset($values); // Cannot redeclare var $values');
            $values = $this->_getValues();
            $value = $values[$lowerId];
            unset($values);
        }
        return new \Yana\Http\Requests\Value($value);
    }

    /**
     * Returns bool(true) if there list has no values.
     *
     * @return  bool
     */
    public function isEmpty()
    {
        return empty($values = $this->_getValues());
    }

    /**
     * Get all values.
     *
     * @return  array
     */
    public function asUnsafeArray()
    {
        return $this->_getValues();
    }

    /**
     * Convert all values to strings recursively.
     *
     * @return  array
     */
    public function asArrayOfStrings()
    {
        return $this->_untaintArray($this->asUnsafeArray(), YANA_AUTODEQUOTE && get_magic_quotes_gpc());
    }

    /**
     * Untaint request vars.
     *
     * @param   array  $unsafeValues  request vars
     * @param   bool   $unquote       true: strip slashes, false: leave slashes alone
     * @return  array
     */
    private function _untaintArray(array $unsafeValues, $unquote = false)
    {
        assert('is_bool($unquote); // Invalid argument $unquote. Bool expected.');

        assert('!isset($value); // Cannot redeclare var $value');
        $value = array_change_key_case($unsafeValues, CASE_LOWER);
        assert('!isset($sanitizer); // Cannot redeclare var $sanitizer');
        $sanitizer = new \Yana\Data\StringValidator();
        $sanitizer->setMaxLength(50000)
            ->addOption(\Yana\Data\StringValidator::TOKEN);

        assert('!isset($i); // Cannot redeclare var $i');
        assert('!isset($item); // Cannot redeclare var $item');
        foreach ($value as $i => $item)
        {
            if (is_array($item)) {
                $value[$i] = $this->_untaintArray($value[$i], $unquote);
            } elseif (is_string($item) && $unquote === true) {
                $item = stripcslashes($item);
                $value[$i] = $sanitizer($item);
            } elseif (is_scalar($item)) {
                $value[$i] = $sanitizer((string) $item);
            }
        }
        unset($i, $item);

        return $value;
    }

}

?>