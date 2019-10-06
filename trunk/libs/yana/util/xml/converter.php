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

namespace Yana\Util\Xml;

/**
 * <<utility>> Converts SimpleXml objects to Arrays or serializable XmlObjects.
 *
 * @package    yana
 * @subpackage core
 */
class Converter extends \Yana\Core\AbstractUtility
{

    /**
     * Get XML content as array.
     *
     * Especially usefull for XML documents that use no attributes and are thus
     * very simple.
     *
     * Be aware! This will NOT work for every valid XML file.
     * Its use is limited to real simple XML documents.
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
     * Note: the order in which the tags appear in the XML document, is not preserved.
     * If this is not an issue, you should prefer using a numeric array.
     *
     * @param   \SimpleXMLElement  $xml  source
     * @return  mixed
     */
    public static function convertXmlToAssociativeArray(\SimpleXMLElement $xml)
    {
        $attributes = $xml->attributes();
        $children = $xml->children();

        if (empty($attributes) && empty($children)) {
            return (string) $xml;

        }
        $array = array();

        if (!empty($attributes)) {
            foreach ($attributes as $name => $value)
            {
                $array["@$name"] = (string) $value;
            }
            unset($name, $value);
        }

        if (count($children) > 0) {
            foreach ($children as $name => $node)
            {
                $value = null;
                if (!$node->children()) {
                    $value = (string) $node;
                } else {
                    $value = self::convertXmlToAssociativeArray($node);
                }
                // new node
                if (!isset($array[$name])) {
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
            unset($name, $node);

            // has no children (is text-node)
        } else {
            $textNode = trim((string) $xml);
            // non-empty text node
            if ($textNode !== '') {
                $array['#pcdata'] = $textNode;

            } else { // empty text-node
                // ignore
            }
        } // end if
        return $array;
    }

    /**
     * Get XML content as array.
     *
     * Especially usefull for XML documents that use no attributes and are thus
     * very simple.
     *
     * Be aware! This will NOT work for every valid XML file.
     * Its use is limited to real simple XML documents.
     *
     * If attributes are present, they are treated as text nodes.
     *
     * If the node is a text node that has no attributes, only the PCDATA
     * section is returned, which is a string (not an array).
     *
     * Note: a node may either be a container or a text node.
     * It must not be both. This means: it must not have inline nodes.
     *
     * The tags will be numbered.
     * Each array will have a "#tag" entry, containing the tag-name.
     * The default is bool(false).
     *
     * This stores the nodes in exactly the same order in which they appear in the source document.
     *
     * @param   \SimpleXMLElement  $xml  source
     * @return  mixed
     */
    public static function convertXmlToNumericArray(\SimpleXMLElement $xml)
    {
        $attributes = $xml->attributes();
        $children = $xml->children();

        if (empty($attributes) && empty($children)) {
            return (string) $xml;

        }
        $array = array('#tag' => $xml->getName());

        if (!empty($attributes)) {
            foreach ($attributes as $name => $value)
            {
                $array["@$name"] = (string) $value;
            }
            unset($name, $value);
        }

        if (count($children) > 0) {
            foreach ($children as $name => $node)
            {
                $value = null;
                if (!$node->children()) {
                    $value = array(
                        '#tag' => $name,
                        '#pcdata' => (string) $node
                    );
                } else {
                    $value = self::convertXmlToNumericArray($node);
                }
                $array[] = $value;
            } // end foreach
            unset($name, $node);

            // has no children (is text-node)
        } else {
            $textNode = trim((string) $xml);
            // non-empty text node
            if ($textNode !== '') {
                $array['#pcdata'] = $textNode;
            } 
        } // end if
        return $array;
    }

    /**
     * Get XML content as object.
     *
     * Especially usefull for XML documents that use no attributes and are thus
     * very simple.
     *
     * Be aware! This will NOT work for every valid XML file.
     *
     * If attributes are present, they are treated as text nodes.
     *
     * If the node is a text node that has no attributes, only the PCDATA
     * section is returned, which is a string (not an array).
     *
     * Note: a node may either be a container or a text node.
     * It must not be both. This means: it must not have inline nodes.
     *
     * @return  \Yana\Util\Xml\StdObject
     */
    public static function convertXmlToObject(\SimpleXMLElement $xml)
    {
        $attributes = $xml->attributes();
        $children = $xml->children();

        $object = new \Yana\Util\Xml\StdObject();

        if (empty($attributes) && empty($children)) {
            return $object;
        }

        if (!empty($attributes)) {
            foreach ($attributes as $name => $value)
            {
                $object->addAttribute($name, (string) $value);
            }
            unset($name, $value);
        }

        if (count($children) > 0) {
            foreach ($children as $name => $node)
            {
                $value = null;
                if (!$node->children()) {
                    $value = (string) $node;
                } else {
                    $value = self::convertXmlToObject($node);
                }
                // new node
                if (!isset($object->{$name})) {
                    $object->{$name} = $value;

                // node is already a list
                } elseif (is_array($object->{$name}) && isset($object->{$name}[0])) {
                    $object->{$name}[] = $value;

                // convert node to list
                } else {
                    $object->{$name} = array($object->{$name});
                    $object->{$name}[] = $value;

                }
            } // end foreach
            unset($name, $node);

            // has no children (is text-node)
        } else {
            $textNode = trim((string) $xml);
            // non-empty text node
            if ($textNode !== '') {
                $object->setPcData($textNode);
            }
        } // end if
        return $object;
    }

    /**
     * Takes an object and converts it to an associative array.
     *
     * @param   \Yana\Util\Xml\IsObject  $xml  any object to convert
     * @return  array
     */
    private static function _mapObjectToAssociativeArray($xml)
    {
        $array = array();
        foreach ($xml as $name => $value)
        {
            switch (true)
            {
                case \is_scalar($value):
                    $array[$name] = $value;
                break;

                case $value instanceof \Yana\Util\Xml\IsObject:
                case is_array($value):
                    $array[$name] = self::_mapObjectToAssociativeArray($value);
            }
        }
        return $array;
    }

    /**
     * Takes an object and converts it to an associative array.
     *
     * All public members will be returned as part of the array.
     * The name of the member will be the key.
     * Members that are not accessible
     *
     * @param   \Yana\Util\Xml\IsObject  $xml  any object to convert
     * @return  array
     */
    public static function convertObjectToAssociativeArray(\Yana\Util\Xml\IsObject $xml)
    {
        return self::_mapObjectToAssociativeArray($xml);
    }

}

?>