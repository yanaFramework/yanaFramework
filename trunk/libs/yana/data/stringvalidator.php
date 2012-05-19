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
 * String validation class.
 *
 * @package     yana
 * @subpackage  io
 */
class StringValidator extends AbstractValidator
{

    /**
     * Binary array of options.
     *
     * @var int
     */
    private $_options = 0;

    /**
     * Maximum count of characters.
     *
     * @var int
     */
    private $_length = 0;

    /**
     * Apply addslashes()
     */
    const SLASHES = 2;

    /**
     * Replace template delimiters with html-entities
     */
    const TOKEN = 4;

    /**
     * Convert all characters to html-entities
     */
    const CODED = 8;

    /**
     * Revert all white-space to spaces.
     *
     * For security reasons you should ALWAYS use this setting if you expect data from any other field than textarea.
     */
    const LINEBREAK = 16;

    /**
     * Treat full-text message from an textarea element.
     *
     * Prevents flooding by removing duplicate elements.
     */
    const USERTEXT = 32;

    /**
     * Apply all.
     *
     * Allows to combine options.
     * E.g. StringValidator::ALL & ~StringValidator::USERTEXT = all but usertext
     */
    const ALL = 0xFF;

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
     * @return  StringValidator
     */
    public function setMaxLength($length)
    {
        assert('is_int($length); // Invalid argument $length: int expected');
        assert('$length >= 0; // $length must not be negative');
        $this->_length = (int) $length;
        return $this;
    }

    /**
     * Get processing options.
     *
     * The options are a binary array of values, taken from this class' constants.
     *
     * @return  int
     */
    protected function getOptions()
    {
        return $this->_options;
    }

    /**
     * Add processing option.
     *
     * @param   int  $option  any of the class' constants.
     * @return  StringValidator 
     */
    public function addOption($option)
    {
        assert('is_int($option); // Invalid argument $option: int expected');
        $this->_options = $this->_options | $option;
        return $this;
    }

    /**
     * Validate a value as string.
     *
     * Returns bool(true) if valid and bool(false) if not.
     *
     * @param   mixed  $string     value to validate
     * @param   int    $maxLength  maximum count of characters
     * @return  bool
     */
    public static function validate($string, $maxLength = 0)
    {
        return is_string($string) && (!$maxLength || mb_strlen($string) <= $maxLength);
    }

    /**
     * Sanitize / convert an string-input.
     *
     * @param   mixed  $string     value to sanitize
     * @param   int    $maxLength  maximum count of characters
     * @param   int    $option     any of the class' constants, use bitwise OR to chain options
     * @return  mixed 
     */
    public static function sanitize($string, $maxLength = 0, $options = 0)
    {
        $validator = new self();
        return $validator->setMaxLength($maxLength)
            ->addOption($options)
            ->__invoke($string);
    }

    /**
     * Sanitize / convert an string-input.
     *
     * @param   mixed  $value  value to sanitize
     * @return  mixed 
     */
    public function __invoke($value)
    {
        $value = preg_replace('/[\x00-\x08\x0b]*|[\x0e-\x1f]*/', '', "$value");
        $value = $this->_processMaxLength($value);
        $value = $this->_processOptions($value);
        return $value;
    }

    /**
     * Limit number of characters to maximum length and return result.
     *
     * @param   string  $value  value to process
     * @return  string 
     */
    protected function _processMaxLength($value)
    {
        $length = $this->getMaxLength();
        if ($length > 0 && mb_strlen("$value") > $length) {
            $value = mb_substr($value, 0, $length);
        }
        return $value;
    }

    /**
     * Execute string conversion- and sanitation-options and return result.
     *
     * Note: should always be called last.
     *
     * @param   string  $value  value to process
     * @return  string 
     */
    protected function _processOptions($value)
    {
        $options = $this->getOptions();
        // filter SLASHED
        if ($options & self::SLASHES) {
            $value = addslashes($value);
        }
        // filter TOKEN
        if ($options & self::TOKEN) {
            $value = str_replace(YANA_LEFT_DELIMITER, '&#'.ord(YANA_LEFT_DELIMITER).';', $value);
            $value = str_replace(YANA_RIGHT_DELIMITER, '&#'.ord(YANA_RIGHT_DELIMITER).';', $value);
            $value = str_replace('$', '&#'.ord('$').';', $value);
        }
        // filter LINEBREAK
        if ($options & self::LINEBREAK) {
            $value = trim(preg_replace("/\s/", " ", $value));
        }
        // filter user text input
        if ($options & self::USERTEXT) {
            /* Note: it is important to ensure that an already checked string
             * that undergoes the same procedure again remains unchanged,
             * to avoid double conversion.
             */
            $value = nl2br($value);
            /* white space */
            $value = preg_replace('/[\x00-\x1f]/', '', $value);
            /* length */
            $length = $this->getMaxLength();
            if (is_int($length) && $length > 0) {
                $value = preg_replace("/^(.{" . $length . "}\S*).*/", '$1', $value);
                $value = preg_replace("/^(.{" . ($length + 100) . "}).*/", '$1', $value);
            }
            /* white space before start- and after end- tag */
            $value = preg_replace("/(\[\/\S+\])(\S)/U", '$1 $2', $value);
            $value = preg_replace("/(\S)(\[!(?:wbr|br|\/[^\s\]]+)\])/U", '$1 $2', $value);
            /* white space around emoticons */
            $value = preg_replace("/(\S)(\:\S+\:)(\S)/U", '$1 $2 $3', $value);
            /* line break */
            $value = preg_replace("/([^\b\s\[\]]{80})(\B)/", '$1[wbr]$2', $value);
            /* clean up */
            $value = preg_replace("/\[wbr\]\[wbr\]/", "[wbr]", $value);
            $value = preg_replace("/\<br\s*\/?>/", "[br]", $value);
            $value = preg_replace("/\s/", " ", $value);
            /* trim spaces */
            $value = trim($value);
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            /*
             * Escape Token-delimiters, to prevent possible code injection.
             * Note: YANA_UNI_DELIMITER has also to be a part of YANA_LEFT_DELIMITER and
             * YANA_RIGHT_DELIMITER, so these will also be escaped.
             */
            $value = str_replace(YANA_LEFT_DELIMITER, '&#'.ord(YANA_LEFT_DELIMITER).';', $value);
            $value = str_replace(YANA_RIGHT_DELIMITER, '&#'.ord(YANA_RIGHT_DELIMITER).';', $value);
            /*
             * Escape '$' character, to prevent possible code injection.
             */
            $value = str_replace('$', '&#36;', $value);
        }
        // special filter CODED (complete conversion)
        if ($options & self::CODED) {
            $value = (string) ($value);
            $value = \Yana\Util\String::htmlEntities($value);
        }
        return $value;
    }

}

?>