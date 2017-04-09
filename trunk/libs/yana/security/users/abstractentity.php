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

namespace Yana\Security\Users;

/**
 * <<entity>> User.
 *
 * Holds user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
abstract class AbstractEntity extends \Yana\Core\Object implements \Yana\Security\Users\IsUser
{

    /**
     * data adapter used to load and save the entity's contents.
     *
     * @var  \Yana\Security\Users\IsDataAdapter
     */
    private $_dataAdapter = null;

    /**
     * This sets the data adapter used to persist the entity
     *
     * @param   \Yana\Security\Users\IsDataAdapter  $adapter  object that should be used
     * @return  self
     */
    public function setDataAdapter(\Yana\Security\Users\IsDataAdapter $adapter)
    {
        $this->_dataAdapter = $adapter;
        return $this;
    }

    /**
     * Returns a data adapter.
     *
     * If there is none, the function returns NULL instead.
     *
     * @return  \Yana\Security\Users\IsDataAdapter
     */
    protected function _getDataAdapter()
    {
        return $this->_dataAdapter;
    }

    /**
     * Returns bool(true) if the instance has a valid data adapter.
     *
     * Returns bool(false) otherwise.
     *
     * @return  bool
     */
    protected function _hasDataAdapter()
    {
        return $this->_getDataAdapter() instanceof \Yana\Security\Users\IsDataAdapter;
    }

    /**
     * Calls the assigned data adapter to persist the entity.
     */
    public function saveEntity()
    {
        if ($this->_hasDataAdapter()) {
            $adapter = $this->_getDataAdapter();
            $adapter->saveEntity($this);
        }
    }

}

?>