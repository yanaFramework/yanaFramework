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
 *
 * @ignore
 */

namespace Yana\Views\Icons;

/**
 * Data adapter to load files configuration from XML.
 *
 * @package     yana
 * @subpackage  views
 */
class XmlAdapter extends \Yana\Views\Icons\AbstractXmlAdapter implements \Yana\Views\Icons\IsDataAdapter
{

    /**
     * Check if item exists.
     *
     * @param   scalar  $offset  index of item to test
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return $this->_getCollection()->offsetExists($offset);
    }

    /**
     * Return item at offset.
     *
     * @param   scalar  $offset  index of item to retrieve
     * @return  \Yana\Views\Icons\IsFile
     */
    public function offsetGet($offset)
    {
        return $this->_getCollection()->offsetGet($offset);
    }

    /**
     * Insert or replace item.
     *
     * @param   scalar  $offset  index of item to replace
     * @param   mixed   $value   new value of item
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  if the value is not a valid collection item
     * @return  mixed
     */
    public function offsetSet($offset, $value)
    {
        $this->_getCollection()->offsetSet($offset, $value);
        $this->_saveChangesToFile();
        return $value;
    }

    /**
     * Remove item from collection.
     *
     * @param   scalar  $offset  index of item to remove
     */
    public function offsetUnset($offset)
    {
        $this->_getCollection()->offsetUnset($offset);
        $this->_saveChangesToFile();
    }

    /**
     * Return the number of items in the collection.
     *
     * If the collection is empty, it returns 0.
     *
     * @return  int
     */
    public function count()
    {
        return $this->_getCollection()->count();
    }

    /**
     * Return an array of all valid identifiers.
     *
     * @return  array
     */
    public function getIds()
    {
        return array_keys($this->_getCollection()->toArray());
    }

    /**
     * Persists the given entity.
     *
     * @param  \Yana\Data\Adapters\IsEntity  $entity  instance of IsFile
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not a valid entity
     */
    public function saveEntity(\Yana\Data\Adapters\IsEntity $entity)
    {
        
        $this->offsetSet(null, $entity);
    }

    /**
     * Get collection of file entities.
     *
     * @return  \Yana\Views\Icons\Collection
     */
    public function getAll()
    {
        return $this->_getCollection();
    }

}

?>