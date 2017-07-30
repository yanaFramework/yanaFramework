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

namespace Yana\Security\Data\SecurityLevels;

/**
 * <<abstract>> Security level.
 *
 * Readonly information about the user's security level.
 *
 * @package     yana
 * @subpackage  security
 */
abstract class AbstractLevel extends \Yana\Core\Object implements \Yana\Security\Data\SecurityLevels\IsLevelEntity
{

    /**
     * @var \Yana\Data\Adapters\IsDataAdapter
     */
    private $_dataAdaper = null;

    /**
     * Returns data adapter.
     *
     * If none has been set, returns an array adapter instead.
     *
     * @return \Yana\Data\Adapters\IsDataAdapter
     */
    protected function _getDataAdapter()
    {
        if (!isset($this->_dataAdaper)) {
            $this->_dataAdaper = new \Yana\Data\Adapters\ArrayAdapter();
        }
        return $this->_dataAdaper;
    }

    /**
     * Calls the assigned data adapter to persist the entity.
     */
    public function saveEntity()
    {
        $this->_getDataAdapter()->saveEntity($this);
    }

    /**
     * This sets the data adapter used to persist the entity
     *
     * @param   \Yana\Data\Adapters\IsDataAdapter  $adapter  object that should be used
     * @return  self
     */
    public function setDataAdapter(\Yana\Data\Adapters\IsDataAdapter $adapter)
    {
        $this->_dataAdaper = $adapter;
        return $this;
    }

}

?>