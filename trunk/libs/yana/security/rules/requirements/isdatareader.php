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
 * <<interface>> Helps with loading requirements from a data-source.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsDataReader
{

    /**
     * Find and return (active) requirements for the given action.
     *
     * An exception is thrown if the datasource is empty.
     * If the datasource is not empty, but no requirements are found for (this) action nonetheless, an empty collection will be returned.
     *
     * @param   string  $action  loaded requirements must be associated with this rule
     * @return  \Yana\Security\Rules\Requirements\Collection
     * @throws  \Yana\Security\Rules\Requirements\NotFoundException  when no rules are found in the datasource
     */
    public function loadRequirementsByAssociatedAction($action);

    /**
     * Find and return (active) requirement with the given id.
     *
     * @param   int  $id  of row in table securityactionrules
     * @return  \Yana\Security\Rules\Requirements\IsRequirement
     * @throws  \Yana\Security\Rules\Requirements\NotFoundException  when no such rule is found in the datasource
     */
    public function loadRequirementById($id);

    /**
     * Get user groups.
     *
     * Returns an array of group names, where the keys are the group ids and the values are
     * the human-readable group names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function loadListOfGroups();

    /**
     * Get user roles.
     *
     * Returns an array of role names, where the keys are the group ids and the values are
     * the human-readable role names.
     *
     * Returns an empty array, if there are no entries.
     *
     * @return  array
     */
    public function loadListOfRoles();

}

?>