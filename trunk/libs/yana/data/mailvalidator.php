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
 * Mail-address validation.
 *
 * @package     yana
 * @subpackage  data
 */
class MailValidator extends AbstractValidator
{

    /**
     * Maximum count of characters.
     *
     * @var int
     */
    private $_length = 0;

    /**
     * Get maximum length in characters.
     *
     * @return  int
     */
    protected function getMaxLength()
    {
        return $this->_length;
    }

    /**
     * Set maximum length in characters.
     *
     * @param   int  $length  positive number, 0 = no restrictions
     * @return  MailValidator
     */
    public function setMaxLength($length)
    {
        assert(is_int($length), 'Invalid argument $length: int expected');
        assert($length >= 0, '$length must not be negative');
        $this->_length = (int) $length;
        return $this;
    }

    /**
     * Evaluate if a value is a valid mail-address.
     *
     * @param   mixed  $mail  value to validate
     * @param   int    $maxLength  maximum count of characters
     * @return  bool
     */
    public static function validate($mail, $maxLength = 0)
    {
        assert(is_int($maxLength), 'Invalid argument $maxLength: int expected');
        return filter_var($mail, FILTER_VALIDATE_EMAIL) && (!$maxLength || mb_strlen($mail) <= $maxLength);
    }

    /**
     * Sanitize mail-address.
     *
     * Returns NULL for invalid values.
     *
     * @param   mixed  $mail  value to sanitize
     * @return  string
     */
    public function __invoke($mail)
    {
        $mail = trim($mail);
        if (!self::validate($mail, $this->getMaxLength())) {
            $mail = null;
        }
        return $mail;
    }

    /**
     * Sanitize e-mail address.
     *
     * Returns NULL for invalid values.
     *
     * @param   mixed  $mail       value to sanitize
     * @param   int    $maxLength  maximum count of characters
     * @return  string 
     */
    public static function sanitize($mail, $maxLength = 0)
    {
        assert(is_int($maxLength), 'Invalid argument $maxLength: int expected');
        $validator = new self();
        return $validator->setMaxLength($maxLength)
            ->__invoke($mail, $maxLength);
    }

}

?>