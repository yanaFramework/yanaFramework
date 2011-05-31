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
 * URL validation.
 *
 * @package     yana
 * @subpackage  io
 */
class UrlValidator extends AbstractValidator
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
     * @return  UrlValidator
     */
    public function setMaxLength($length)
    {
        assert('is_int($length); // Invalid argument $length: int expected');
        assert('$length >= 0; // $length must not be negative');
        $this->_length = (int) $length;
        return $this;
    }

    /**
     * Evaluate if a value is a valid URL.
     *
     * @param   mixed  $url        value to validate
     * @param   int    $maxLength  maximum count of characters
     * @return  bool
     */
    public static function validate($url, $maxLength = 0)
    {
        return filter_var($url, FILTER_VALIDATE_URL) && (!$maxLength || mb_strlen($url) <= $maxLength);
    }

    /**
     * Sanitize URL.
     *
     * Returns NULL for invalid values.
     *
     * @param   mixed  $url  value to sanitize
     * @return  string
     */
    public function __invoke($url)
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        if (!preg_match('/^\w+\:\/\//', $url)) {
            $url = 'http://' . $url;
        }
        $maxLength = $this->getMaxLength();
        if ($maxLength > 0) {
            $url = mb_substr($url, 0, $maxLength);
        }
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            $url = null;
        }
        return $url;
    }

    /**
     * Sanitize URL.
     *
     * Returns NULL for invalid values.
     *
     * @param   mixed  $url        value to sanitize
     * @param   int    $maxLength  maximum count of characters
     * @return  string 
     */
    public static function sanitize($url, $maxLength = 0)
    {
        $validator = new self();
        return $validator->setMaxLength($maxLength)
            ->__invoke($url, $maxLength);
    }

}

?>