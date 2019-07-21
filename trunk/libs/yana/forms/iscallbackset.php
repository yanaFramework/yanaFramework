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
 * <<interface>> Of callable elements.
 *
 * @package     yana
 * @subpackage  form
 */
interface IsCallbackSet
{

    /**
     * Returns a list of all known callbacks for this event.
     *
     * @return  \Yana\Core\CallableCollection
     */
    public function getBeforeCreate();

    /**
     * Returns a list of all known callbacks for this event.
     *
     * @return  \Yana\Core\CallableCollection
     */
    public function getBeforeDelete();

    /**
     * Returns a list of all known callbacks for this event.
     *
     * @return  \Yana\Core\CallableCollection
     */
    public function getBeforeUpdate();

    /**
     * Returns a list of all known callbacks for this event.
     *
     * @return  \Yana\Core\CallableCollection
     */
    public function getAfterCreate();

    /**
     * Returns a list of all known callbacks for this event.
     *
     * @return  \Yana\Core\CallableCollection
     */
    public function getAfterDelete();

    /**
     * Returns a list of all known callbacks for this event.
     *
     * @return  \Yana\Core\CallableCollection
     */
    public function getAfterUpdate();

    /**
     * <<hook>> Register call-back function to run before creating a row.
     *
     * Example:
     * <code>
     * $collection->setBeforeCreate(function (array &$newRow) {...});
     * </code>
     *
     * @param   callable  $callback  some call-back function
     * @return  $this
     */
    public function addBeforeCreate($callback);

    /**
     * <<hook>> Register call-back function to run after creating a row.
     *
     * Example:
     * <code>
     * $collection->setAfterCreate(function (array $newRow) {...});
     * </code>
     *
     * @param   callable  $callback  some call-back function
     * @return  $this
     */
    public function addAfterCreate($callback);

    /**
     * <<hook>> Register call-back function to run before updating a row.
     *
     * Example:
     * <code>
     * $collection->setBeforeUpdate(function ($id, array &$newRow) {...});
     * </code>
     *
     * @param   callable  $callback  some call-back function
     * @return  $this
     */
    public function addBeforeUpdate($callback);

    /**
     * <<hook>> Register call-back function to run after updating a row.
     *
     * Example:
     * <code>
     * $collection->setAfterUpdate(function ($id, array $newRow) {...});
     * </code>
     *
     * @param   callable  $callback  some call-back function
     * @return  $this
     */
    public function addAfterUpdate($callback);

    /**
     * <<hook>> Register call-back function to run before deleting a row.
     *
     * Example:
     * <code>
     * $worker->beforeDelete(function ($id) {...});
     * </code>
     *
     * @param   callable  $callback  some call-back function
     * @return  $this
     */
    public function addBeforeDelete($callback);

    /**
     * <<hook>> Register call-back function to run after deleting a row.
     *
     * Returns a list of all known callbacks for this event.
     *
     * Example:
     * <code>
     * $worker->afterDelete(function ($id) {...});
     * </code>
     *
     * @param   callable  $callback  some call-back function
     * @return  $this
     */
    public function addAfterDelete($callback);

}

?>