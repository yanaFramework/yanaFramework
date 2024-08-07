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
declare(strict_types=1);

namespace Yana\Data\Adapters;

/**
 * <<abstract>> Entity class.
 *
 * An entity is a <<subject>> persisted on demand by an observer.
 *
 * @package     yana
 * @subpackage  data
 */
abstract class AbstractEntity extends \Yana\Core\StdObject implements \Yana\Data\Adapters\IsEntity
{

    /**
     * List of observers used to persist the entity's state.
     *
     * @var  \Yana\Data\Adapters\ArrayAdapter
     */
    private $_adapter = null;

    /**
     * This sets the data adapter used to persist the entity
     *
     * @param  \Yana\Data\Adapters\IsDataAdapter  $adapter  object that should be used
     * @return  \Yana\Data\Adapters\IsEntity
     */
    public function setDataAdapter(\Yana\Data\Adapters\IsDataAdapter $adapter)
    {
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * Get adapter.
     *
     * This returns the adapter selected via setDataAdapter() or a default dummy implementation if no other is selected.
     * 
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    protected function _getDataAdapter()
    {
        if (!isset($this->_adapter)) {
            $this->_adapter = new \Yana\Data\Adapters\ArrayAdapter();
        }
        return $this->_adapter;
    }

    /**
     * Notifies all active observers to check the state of the entity.
     */
    public function saveEntity()
    {
        $this->_getDataAdapter()->saveEntity($this);
    }

}

?>
