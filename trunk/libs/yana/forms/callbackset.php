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

namespace Yana\Forms;

/**
 * Set of callable elements.
 *
 * @package     yana
 * @subpackage  form
 */
class CallbackSet extends \Yana\Core\StdObject implements \Yana\Forms\IsCallbackSet
{

    /**
     * @var \Yana\Core\CallableCollection
     */
    private $_afterCreate = null;

    /**
     * @var \Yana\Core\CallableCollection
     */
    private $_afterDelete = null;

    /**
     * @var \Yana\Core\CallableCollection
     */
    private $_afterUpdate = null;

    /**
     * @var \Yana\Core\CallableCollection
     */
    private $_beforeCreate = null;

    /**
     * @var \Yana\Core\CallableCollection
     */
    private $_beforeDelete = null;

    /**
     * @var \Yana\Core\CallableCollection
     */
    private $_beforeUpdate = null;

    /**
     * Returns a list of all known callbacks for this event.
     *
     * @return  \Yana\Core\CallableCollection
     */
    public function getBeforeCreate()
    {
        if (!isset($this->_beforeCreate)) {
            $this->_beforeCreate = new \Yana\Core\CallableCollection();
        }
        return $this->_beforeCreate;
    }

    /**
     * Returns a list of all known callbacks for this event.
     *
     * @return  \Yana\Core\CallableCollection
     */
    public function getBeforeDelete()
    {
        if (!isset($this->_beforeDelete)) {
            $this->_beforeDelete = new \Yana\Core\CallableCollection();
        }
        return $this->_beforeDelete;
    }

    /**
     * Returns a list of all known callbacks for this event.
     *
     * @return  \Yana\Core\CallableCollection
     */
    public function getBeforeUpdate()
    {
        if (!isset($this->_beforeUpdate)) {
            $this->_beforeUpdate = new \Yana\Core\CallableCollection();
        }
        return $this->_beforeUpdate;
    }

    /**
     * Returns a list of all known callbacks for this event.
     *
     * @return  \Yana\Core\CallableCollection
     */
    public function getAfterCreate()
    {
        if (!isset($this->_afterCreate)) {
            $this->_afterCreate = new \Yana\Core\CallableCollection();
        }
        return $this->_afterCreate;
    }

    /**
     * Returns a list of all known callbacks for this event.
     *
     * @return  \Yana\Core\CallableCollection
     */
    public function getAfterDelete()
    {
        if (!isset($this->_afterDelete)) {
            $this->_afterDelete = new \Yana\Core\CallableCollection();
        }
        return $this->_afterDelete;
    }

    /**
     * Returns a list of all known callbacks for this event.
     *
     * @return  \Yana\Core\CallableCollection
     */
    public function getAfterUpdate()
    {
        if (!isset($this->_afterUpdate)) {
            $this->_afterUpdate = new \Yana\Core\CallableCollection();
        }
        return $this->_afterUpdate;
    }

    /**
     * <<hook>> Register call-back function to run before creating a row.
     *
     * Example:
     * <code>
     * $collection->addBeforeCreate(function (array &$newRow) {...});
     * </code>
     *
     * @param   callable  $callback  some call-back function
     * @return  $this
     */
    public function addBeforeCreate($callback)
    {
        if (\is_callable($callback)) {
            $this->getBeforeCreate()->offsetSet(null, $callback);
        }
        return $this;
    }

    /**
     * <<hook>> Register call-back function to run after creating a row.
     *
     * Example:
     * <code>
     * $collection->addAfterCreate(function (array $newRow) {...});
     * </code>
     *
     * @param   callable  $callback  some call-back function
     * @return  $this
     */
    public function addAfterCreate($callback)
    {
        if (\is_callable($callback)) {
            $this->getAfterCreate()->offsetSet(null, $callback);
        }
        return $this;
    }

    /**
     * <<hook>> Register call-back function to run before updating a row.
     *
     * Example:
     * <code>
     * $collection->addBeforeUpdate(function ($id, array &$newRow) {...});
     * </code>
     *
     * @param   callable  $callback  some call-back function
     * @return  $this
     */
    public function addBeforeUpdate($callback)
    {
        if (\is_callable($callback)) {
            $this->getBeforeUpdate()->offsetSet(null, $callback);
        }
        return $this;
    }

    /**
     * <<hook>> Register call-back function to run after updating a row.
     *
     * Example:
     * <code>
     * $collection->addAfterUpdate(function ($id, array $newRow) {...});
     * </code>
     *
     * @param   callable  $callback  some call-back function
     * @return  $this
     */
    public function addAfterUpdate($callback)
    {
        if (\is_callable($callback)) {
            $this->getAfterUpdate()->offsetSet(null, $callback);
        }
        return $this;
    }

    /**
     * <<hook>> Register call-back function to run before deleting a row.
     *
     * Example:
     * <code>
     * $collection->addBeforeDelete(function ($id) {...});
     * </code>
     *
     * @param   callable  $callback  some call-back function
     * @return  $this
     */
    public function addBeforeDelete($callback)
    {
        if (\is_callable($callback)) {
            $this->getBeforeDelete()->offsetSet(null, $callback);
        }
        return $this;
    }

    /**
     * <<hook>> Register call-back function to run after deleting a row.
     *
     * Returns a list of all known callbacks for this event.
     *
     * Example:
     * <code>
     * $collection->addAfterDelete(function ($id) {...});
     * </code>
     *
     * @param   callable  $callback  some call-back function
     * @return  $this
     */
    public function addAfterDelete($callback)
    {
        if (\is_callable($callback)) {
            $this->getAfterDelete()->offsetSet(null, $callback);
        }
        return $this;
    }

}

?>