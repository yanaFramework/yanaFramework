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
 * <<data object>> Helper class for XmlArray.
 *
 * @package    yana
 * @subpackage core
 * @ignore
 */
class StdObject extends \stdClass implements \Yana\Util\Xml\IsObject
{

    /**
     * <<constructor>> Initialize text content of tag.
     *
     * @param  string  $text  PCDATA
     */
    public function __construct($text = null)
    {
        if (is_string($text)) {
            $this->setPcData($text);
        }
    }

    /**
     * Alias of getPcData().
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getPcData();
    }

    /**
     * 
     * @param   string  $name   of attribute
     * @param   string  $value  some text
     * @return  $this
     */
    public function addAttribute($name, $value)
    {
        $this->{"@" . $name} = (string) $value;
        return $this;
    }

    /**
     * Returns bool(true) if the attribute exists and bool(false) otherwise.
     *
     * @param   string  $name  of the attribute
     * @return  bool
     */
    public function hasAttribute($name)
    {
        return isset($this->{"@" . $name});
    }

    /**
     * Returns the attribute with the given name if it exists.
     *
     * Returns NULL if there is no such attribute.
     *
     * @param   string  $name  of the attribute
     * @return  string
     */
    public function getAttribute($name)
    {
        return isset($this->{"@" . $name}) ? (string) $this->{"@" . $name} : "";
    }

    /**
     * Returns list of all children with that tag name.
     *
     * If there are none, the list is empty.
     *
     * @param   string  $name  tag name to retrieve
     * @return  \Yana\Util\Xml\Collection
     */
    public function getAll($name)
    {
        $all = new \Yana\Util\Xml\Collection();
        if (isset($this->$name)) {
            if (is_array($this->$name)) {
                $all->setItems($this->$name);
            } elseif (\is_scalar($this->$name) || $this->$name instanceof \Yana\Util\Xml\IsObject) {
                $all[] = $this->$name;
            }
        }
        return $all;
    }

    /**
     * Set PCDATA content of tag.
     *
     * PCDATA is every "character data" (text) between the opening and closing tag.
     *
     * @param   string  $text  tag content
     * @return  $this
     */
    public function setPcData($text)
    {
        $this->{"#pcdata"} = (string) $text;
        return $this;
    }

    /**
     * Returns tag content if there is any.
     *
     * @return  string
     */
    public function getPcData()
    {
        return isset($this->{"#pcdata"}) ? (string) $this->{"#pcdata"} : "";
    }

    /**
     * Returns bool(true) if the property exists and bool(false) otherwise.
     *
     * @param   string  $name  of property
     * @return  bool
     */
    public function offsetExists($name)
    {
        return isset($this->{$name});
    }

    /**
     * Returns the property or NULL if it doesn't exist.
     *
     * @param   string  $name  of property
     * @return  mixed
     */
    public function offsetGet($name)
    {
        return isset($this->{$name}) ? $this->{$name} : null; 
    }

    /**
     * Replaces the given property.
     *
     * Returns the value.
     *
     * @param   string  $name   of property
     * @param   mixed   $value  of property
     * @return  mixed
     */
    public function offsetSet($name, $value)
    {
        $this->{$name} = $value;
        return $value;
    }

    /**
     * Removes the given property.
     *
     * @param   string  $name   of property
     */
    public function offsetUnset($name)
    {
        if (isset($this->{$name})) {
            unset($this->{$name});
        }
    }

    /**
     * Returns an Iterator
     *
     * @return  \Yana\Core\GenericCollection
     */
    public function getIterator()
    {
        $iterator = new \Yana\Core\GenericCollection();
        $iterator->setItems(\get_object_vars($this));
        return $iterator;
    }

}

?>