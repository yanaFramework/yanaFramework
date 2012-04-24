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

namespace Yana\Io\Adapters;

/**
 * <<abstract>> Entity class.
 *
 * An entity is a <<subject>> persisted on demand by an observer.
 *
 * @package     yana
 * @subpackage  core
 */
abstract class AbstractEntity extends \Yana\Core\Object implements \Yana\Io\Adapters\IsEntity
{

    /**
     * List of observers used to persist the entity's state.
     *
     * @var  \Yana\Io\Adapters\ArrayAdapter
     */
    private $_adapter = null;

    /**
     * Initializes the adapter with a dummy implementation.
     *
     * @ignore
     */
    public function __construct()
    {
        $this->setDataAdapter(new \Yana\Io\Adapters\ArrayAdapter());
    }

    /**
     * This sets the data adapter used to persist the entity
     *
     * @param  \Yana\Io\Adapters\IsDataAdapter  $adapter  object that should be used
     */
    public function setDataAdapter(\Yana\Io\Adapters\IsDataAdapter $adapter)
    {
        $this->_adapter = $adapter;
    }

    /**
     * Get adapter.
     *
     * This returns the adapter selected via setDataAdapter() or a default dummy implementation if no other is selected.
     * 
     * @return  \Yana\Io\Adapters\ArrayAdapter
     */
    protected function _getDataAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Notifies all active observers to check the state of the entity.
     */
    public function saveEntity()
    {
        $this->_getDataAdapter()->saveEntity($entity);
    }

}

?>