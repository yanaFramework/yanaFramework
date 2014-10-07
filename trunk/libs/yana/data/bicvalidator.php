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
 * Used for validating bank information.
 *
 * @package     yana
 * @subpackage  io
 */
class BicValidator extends \Yana\Data\AbstractValidator
{

    /**
     * Validate a value as valid bank identifier code.
     *
     * Returns bool(true) if valid and bool(false) if not.
     *
     * @param   mixed  $bic  Bank Identifier Code
     * @return  bool
     */
    public static function validate($bic)
    {
        return 1 === preg_match('/^[A-Z]{4}[A-Z]{2}[A-Z2-9][A-NP-Z12](XXX|[A-WYZ0-9][A-Z0-9]{2})?$/', (string) $bic);
    }

    /**
     * Sanitize BIC.
     *
     * @param   mixed  $bic  Bank Identifier Code
     * @return  mixed 
     */
    public static function sanitize($bic)
    {
        return preg_replace('/[^A-Z0-9]/', '', (string) $bic);
    }

    /**
     * Sanitize BIC.
     *
     * Returns NULL for invalid objects.
     *
     * @param   mixed  $value  value to sanitize
     * @return  string 
     */
    public function __invoke($value)
    {
        assert('!isset($trimmedString); // Cannot redeclare var $trimmedString');
        $trimmedString = $this->sanitize($value);
        assert('!isset($result); // Cannot redeclare var $result');
        $result = $this->validate($trimmedString) ? $trimmedString : null;
        return $result;
    }

}

?>