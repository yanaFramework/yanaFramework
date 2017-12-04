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
 * <<interface>> Helper class for XmlArray.
 *
 * @package    yana
 * @subpackage core
 * @ignore
 */
interface IsObject extends \IteratorAggregate, \ArrayAccess
{

    /**
     * Alias of getPcData().
     *
     * @return  string
     */
    public function __toString();

    /**
     * 
     * @param   string  $name   of attribute
     * @param   string  $value  some text
     * @return  $this
     */
    public function addAttribute($name, $value);

    /**
     * Returns bool(true) if the attribute exists and bool(false) otherwise.
     *
     * @param   string  $name  of the attribute
     * @return  bool
     */
    public function hasAttribute($name);

    /**
     * Returns the attribute with the given name if it exists.
     *
     * Returns NULL if there is no such attribute.
     *
     * @param   string  $name  of the attribute
     * @return  string
     */
    public function getAttribute($name);

    /**
     * Returns list of all children with that tag name.
     *
     * If there are none, the list is empty.
     *
     * @param   string  $name  tag name to retrieve
     * @return  \Yana\Util\Xml\Collection
     */
    public function getAll($name);

    /**
     * Set PCDATA content of tag.
     *
     * PCDATA is every "character data" (text) between the opening and closing tag.
     *
     * @param   string  $text  tag content
     * @return  $this
     */
    public function setPcData($text);

    /**
     * Returns tag content if there is any.
     *
     * @return  string
     */
    public function getPcData();

}

?>