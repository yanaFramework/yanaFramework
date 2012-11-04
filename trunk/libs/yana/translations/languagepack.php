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

namespace Yana\Translations;

/**
 * This class may be used to dynamically load additional language files at runtime.
 *
 * @package     yana
 * @subpackage  translations
 */
class LanguagePack extends \Yana\Core\VarContainer
{

    /**
     * Convert a key name to a lower-cased offset string.
     *
     * @param   sclar  $key  some valid identifier, either a number or a non-empty text
     * @return  string
     */
    protected function _toArrayOffset($key)
    {
        assert('is_scalar($key); // Invalid argument $key: string expected');
        return (string) \mb_strtolower($key);;
    }

    /**
     * Replace a token within a provided text.
     *
     * Note that this function replaces ALL entities found.
     * If a token refers to a non-existing value it is removed.
     *
     * Example:
     * <code>
     * // assume the token {$foo} is set to 'World'
     * $text = 'Hello {$foo}.';
     * // prints 'Hello World.'
     * print \Yana\Util\String::replaceToken($string);
     * </code>
     *
     * @param   string  $string   sting text (look example)
     * @return  string
     */
    public function replaceToken($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');

        assert('!isset($pattern); // Cannot redeclare var $pattern');
        $pattern = '/'. YANA_LEFT_DELIMITER_REGEXP . 'lang id=["\']([\w_\.]+)["\']' . YANA_RIGHT_DELIMITER_REGEXP .'/';
        assert('!isset($matches); // Cannot redeclare var $matches');
        $matches = array();
        // Search for {lang id="($key)"} and replace with translation string
        if (preg_match_all($pattern, $string, $matches)) {
            assert('!isset($i); // Cannot redeclare var $i');
            assert('!isset($key); // Cannot redeclare var $key');
            foreach ($matches[1] as $i => $key)
            {
                assert('!isset($translation); // Cannot redeclare var $translation');
                $translation = ($this->isVar($key)) ? $this->getVar($key) : $key;
                $string = str_replace($matches[0][$i], $translation, $string);
                unset($translation);
            }
            unset($i, $key);
        }
        return $string;
    }

}

?>