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

namespace Yana\Security\Rules\Requirements;

/**
 * <<interface>> Helps with writing requirements to a data-source.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsDataWriter
{

    /**
     * Rescan plugin list and refresh the action security settings.
     *
     * @param   \Yana\Plugins\Configs\MethodCollection  $eventConfigurations  to scan and insert into data-source
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if new entries could not be inserted
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  if existing entries could not be deleted
     * @return  self
     */
    public function __invoke(\Yana\Plugins\Configs\MethodCollection $eventConfigurations);

    /**
     * Try to write changes to the data-source.
     *
     * @return  self
     * @throws  \Exception  if there is some unexpected problem with the data-source
     */
    public function commitChanges();

    /**
     * Remove all existing requirements.
     *
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  if the existing entries could not be deleted
     * @return  self
     */
    public function flushRequirements();

    /**
     * Remove all existing actions.
     *
     * @throws  \Yana\Db\Queries\Exceptions\NotDeletedException  if the existing entries could not be deleted
     * @return  self
     */
    public function flushActions();

    /**
     * Extract action ids and titles from a collection of event configurations.
     *
     * @param   \Yana\Plugins\Configs\MethodCollection  $eventConfigurations  requirements information
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     * @return  array
     */
    public function extractActionTitles(\Yana\Plugins\Configs\MethodCollection $eventConfigurations);

    /**
     * Extract roles from a collection of event configurations.
     *
     * @param   \Yana\Plugins\Configs\MethodCollection  $eventConfigurations  requirements information
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     * @return  array
     */
    public function extractRoleNames(\Yana\Plugins\Configs\MethodCollection $eventConfigurations);

    /**
     * Extract groups from a collection of event configurations.
     *
     * @param   \Yana\Plugins\Configs\MethodCollection  $eventConfigurations  requirements information
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     * @return  array
     */
    public function extractGroupNames(\Yana\Plugins\Configs\MethodCollection $eventConfigurations);

    /**
     * Extract rows of requirements from a collection of event configurations.
     *
     * @param   \Yana\Plugins\Configs\MethodCollection  $eventConfigurations  requirements information
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     * @return  array
     */
    public function extractRequirements(\Yana\Plugins\Configs\MethodCollection $eventConfigurations);

    /**
     * Insert rows into requirements table.
     *
     * @param   array  $rows  of requirement information to insert
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     * @return  self
     */
    public function insertRequirements(array $rows);

    /**
     * Insert new roles.
     *
     * Already existing entries are skipped, so that user-defined names are not overwritten.
     *
     * @see     \Yana\Security\Users\Tables\RoleEnumeration
     * @param   array  $roles  rows for database table
     * @return  \Yana\Security\Rules\Requirements\DataWriter
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     */
    public function insertRoles(array $roles);

    /**
     * Insert new groups.
     *
     * Already existing entries are skipped, so that user-defined names are not overwritten.
     *
     * @see     \Yana\Security\Users\Tables\GroupEnumeration
     * @param   array  $groups  rows for database table
     * @return  \Yana\Security\Rules\Requirements\DataWriter
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     */
    public function insertGroups(array $groups);

    /**
     * Insert new actions.
     *
     * Already existing entries are updated.
     *
     * @see     \Yana\Security\Users\Tables\ActionEnumeration
     * @param   array  $actions  rows for database table
     * @return  \Yana\Security\Rules\Requirements\DataWriter
     * @throws  \Yana\Db\Queries\Exceptions\NotCreatedException  if the new entries could not be inserted
     */
    public function insertActions(array $actions);

}

?>