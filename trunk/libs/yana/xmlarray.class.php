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

/**
 * SimpleXML implementation with array conversion
 *
 * To create an instance of this class use:
 * <code>
 * $xml = simplexml_load_file('foo.xml', 'XmlArray');
 * $array = $xml->toArray();
 * </code>
 *
 * @access     public
 * @package    yana
 * @subpackage file_system
 * @name       XmlArray
 */
class XmlArray extends \SimpleXMLElement
{
    /**
     * get XML content as array
     *
     * Especially usefull for XML documents that use no attributes and are thus
     * very simple.
     *
     * Be aware! This will NOT work for every valid XML file.
     * It's use is limited to real simple XML documents.
     *
     * If attributes are present, they are treated as text nodes.
     *
     * If the node is a text node that has no attributes, only the PCDATA
     * section is returned, which is a string (not an array).
     *
     * Note: a node may either be a container or a text node.
     * It must not be both. This means: it must not have inline nodes.
     *
     * Examples:
     * <code>
     * <foo>bar</foo>
     * // Returns: "bar"
     *
     * <foo bar="1"/>
     * // Returns: array("@bar" => "1")
     *
     * <foo bar="1">
     * bar
     * </foo>
     * // Returns: array("@bar" => "1", "text" => "bar")
     *
     * <foo bar="1">
     * <foo2>foo</foo2>
     * </foo>
     * // Returns: array("@bar" => "1", "foo2" => "foo")
     *
     * <foo bar="1">
     * foo
     * </foo>
     * // Returns: array("@bar" => "1", "#pcdata" => "foo")
     *
     * <foo bar="1">
     * <bar>2</bar>
     * </foo>
     * // Returns: array("@bar" => "1", "bar" => "2")
     *
     * <foo bar="1">
     * bar1
     * <foo2>foo</foo2>
     * bar2
     * </foo>
     * // Returns: array("@bar" => "1", "foo2" => "foo")
     * </code>
     *
     * This last example cannot be converted to an array without loss of
     * information.
     * If this is not what you expected, you need another way to do it.
     *
     * You may provide the optional argument $asNumericArray with the value
     * bool(true), to get a numeric array. If so the tags will be numbered.
     * Each array will have a "#tag" entry, containing the tag-name.
     * The default is bool(false).
     *
     * Note: for non-numeric arrays, the order in which the tags appear in the XML
     * document, is not preserved. If this is not an issue, you should prefer
     * this setting. Otherwise use a numeric array, for it stores the nodes in
     * exactly the same order in which they appear in the source document.
     *
     * @access  public
     * @param   bool   $asNumericArray  return result either as numeric (true) or associative array (false)
     * @return  mixed
     */
    public function toArray($asNumericArray = false)
    {
        $attributes = $this->attributes();
        $children = $this->children();

        if (empty($attributes) && empty($children)) {
            return (string) $this;

        } else {
            $array = array();
            if ($asNumericArray) {
                $array['#tag'] = $this->getName();
            }

            if (!empty($attributes)) {
                foreach ($attributes as $name => $value)
                {
                    $array["@$name"] = (string) $value;
                }
            }

            if (count($children) > 0) {
                foreach ($children as $name => $node)
                {
                    if (!$node->children()) {
                        if ($asNumericArray) {
                            $value = array(
                                '#tag' => $name,
                                '#pcdata' => (string) $node
                            );
                        } else {
                            $value = (string) $node;
                        }
                    } else {
                        $value = $node->toArray($asNumericArray);
                    }
                    // numeric mode
                    if ($asNumericArray) {
                        $array[] = $value;

                    // new node
                    } elseif (!isset($array[$name])) {
                        $array[$name] = $value;

                    // node is already a list
                    } elseif (is_array($array[$name]) && isset($array[$name][0])) {
                        $array[$name][] = $value;

                    // convert node to list
                    } else {
                        $array[$name] = array($array[$name]);
                        $array[$name][] = $value;

                    }
                } // end foreach

            // has no children (is text-node)
            } else {
                $textNode = trim((string) $this);
                // non-empty text node
                if ($textNode !== '') {
                    $array['#pcdata'] = $textNode;

                // empty text-node
                } else {
                    // ignore
                }
            } // end if
            return $array;
        }
    }
}

?>