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

namespace Yana\Plugins\Annotations;

/**
 * Plugin annotation parser.
 *
 * This class parses annotations using the style that the framework uses for plugins.
 *
 * Valid tag styles are:
 * <code>
 * &#64;foo
 * &#64;foo  value
 * &#64;foo  key: value
 * &#64;foo  key1: value 1, key2: value 2
 * {&#64;foo
 *   key1: value 1,
 *   key2: value 2
 * }
 * </code>
 *
 * The above tags will return the following values:
 * <code>
 * Array
 * (
 *     [0] => 1
 *
 *     [1] => value
 *
 *     [2] => Array
 *         (
 *             [key] => value
 *         )
 *
 *     [2] => Array
 *         (
 *             [key1] => value 1
 *             [key2] => value 2
 *         )
 *
 *     [3] => Array
 *         (
 *             [key1] => value 1
 *             [key2] => value 2
 *         )
 * )
 * </code>
 *
 * If the same key is mentionend multiple times,
 * the value of the last occurence overwrites the previous.
 *
 * @package     yana
 * @subpackage  plugins
 */
class Parser extends \Yana\Plugins\Annotations\AbstractParser
{

    /**
     * Get doc-tags from comment.
     *
     * Returns a list of all matching annotations as an associative array.
     * It returns the default value (which defaults to an empty array), if no matching tag is found.
     *
     * @param   string  $tagName  name of doc-tag to extract
     * @param   array   $default  is returned if no tag is found
     * @return  array
     */
    public function getTags($tagName, array $default = array())
    {
        assert('is_string($tagName); // Wrong type for argument 2. String expected');

        $resultWithSimpleTags = $this->_parseSimpleTag($tagName);           // simple tags: @foo
        $result = $this->_parseComplexTag($tagName, $resultWithSimpleTags); // complex tags: {@foo ... }

        assert('is_array($result); // result is expected to be an array');
        if (!empty($result)) {
            return $result;
        } else {
            return $default;
        }
    }

    /**
     * Returns the doc tag as a string.
     *
     * Use this function if you expect only one tag with a single value.
     * Otherwise the default value is returned (which defaults to an empty string).
     *
     * @param   string  $tagName  name of doc-tag to extract
     * @param   string  $default  returned if not matching tag is found
     * @return  string
     */
    public function getTag($tagName, $default = "")
    {
        assert('is_string($tagName); // Wrong type for argument 2. String expected');
        assert('is_string($default); // Wrong type for argument 3. String expected');
        $result = $this->getTags($tagName);
        if (count($result) === 1) {
            return $result[0];
        } else {
            return $default;
        }
    }

    /**
     * Parse comment for simple tags.
     *
     * Valid tag styles are:
     * <code>
     * &#64;foo
     * &#64;foo  value
     * </code>
     *
     * @param   string  $tagName  name of tag to parse
     * @param   array   &$result  result array to add values to
     * @return  array
     */
    private function _parseSimpleTag($tagName, array &$result = array())
    {
        assert('is_string($tagName); // Invalid argument $tagName: string expected');

        /**
         * 1) simple tags: @foo
         */
        $match = array();
        if (preg_match_all('/ @' . preg_quote($tagName, '/') . '(|\s.*)$/mi', $this->getText(), $match)) {

            assert('!isset($i); // Cannot redeclare var $i');
            assert('!isset($tagContent); // Cannot redeclare var $tagContent');
            foreach ($match[1] as $i => $tagContent)
            {
                $count = count($result);

                $tagContent = trim($tagContent);
                /**
                 * 2) get list of values
                 */
                assert('!isset($match2); // Cannot redeclare var $match2');
                $match2 = array(); // for use in reg-exp.
                if ($tagContent === "") {
                    $result[$count] = true;
                } elseif (preg_match_all('/([\w\-]+)\:\s+([^,]*)/', $match[1][$i], $match2)) {
                    $result[$count] = array();
                    assert('!isset($key); // Cannot redeclare var $key');
                    assert('!isset($value); // Cannot redeclare var $value');
                    assert('!isset($j); // Cannot redeclare var $j');
                    for ($j = 0; $j < count($match2[0]); $j++)
                    {
                        /**
                         * 3) assign values
                         */
                        $key = $match2[1][$j];
                        $value = trim($match2[2][$j]);
                        $result[$count][$key] = ($value === "") ? true : $value;
                    } // end for
                    unset($j, $key, $value);
                } else {
                    $result[$count] = $tagContent;
                }
                unset($match2);
            } // end foreach
            unset($i, $tagContent);
 
        } // end if 
        return $result;
    }

    /**
     * Parse comment for simple tags.
     *
     * Valid tag styles are:
     * <code>
     * &#64;foo  key: value
     * &#64;foo  key1: value 1, key2: value 2
     * {&#64;foo
     *   key1: value 1,
     *   key2: value 2
     * }
     * </code>
     *
     * @param   string  $tagName  name of tag to parse
     * @param   array   &$result  result array to add values to
     * @return  array
     */
    private function _parseComplexTag($tagName, array &$result = array())
    {
        /**
         * 1) more complex tags: {@foo key: value }
         */
        $match = array();
        if (preg_match_all('/\{@' . preg_quote($tagName, '/') . '(?:\s+([^\}]*)\}|\s*\})/si', $this->getText(), $match)) {

            assert('!isset($tagContent); // Cannot redeclare var $tagContent');
            foreach ($match[1] as $tagContent)
            {
                $count = count($result);

                $tagContent = trim(preg_replace('/^\s*\*\s*/Um', ' ', $tagContent));
                /**
                 * 2) get list of values
                 */
                assert('!isset($match2); // Cannot redeclare var $match2');
                $match2 = array();
                if ($tagContent === "") {
                    $result[$count] = true;
                } elseif (preg_match('/([\w\-]+)\:(?:\s+|$)/', $tagContent)) {
                    $result[$count] = array();
                    assert('!isset($key); // Cannot redeclare var $key');
                    assert('!isset($value); // Cannot redeclare var $value');
                    while (preg_match('/(([\w\-]+)\:(\s+.*?|))(?:,\s*?[\w\-]+\:|$)/s', $tagContent, $match2))
                    {
                        $tagContent = str_replace($match2[1], '', $tagContent);
                        /**
                         * 3) assign values
                         */
                        $key = $match2[2];
                        $value = trim($match2[3]);
                        $result[$count][$key] = ($value === "") ? true : $value;
                    } // end while
                    unset($key, $value);
                } else {
                    $result[$count] = $tagContent;
                }
                unset($match2);
            } // end foreach
        }
        return $result;
    }

}

?>