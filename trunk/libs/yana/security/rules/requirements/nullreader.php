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
 * For testing purposes only.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class NullReader extends \Yana\Core\StdObject implements \Yana\Security\Rules\Requirements\IsDataReader
{

    /**
     * @var  \Yana\Security\Rules\Requirements\Collection
     */
    private $_collection = null;

    /**
     * Returns a requirements collection.
     *
     * @return  \Yana\Security\Rules\Requirements\Collection
     */
    protected function _getCollection()
    {
        if (!isset($this->_collection)) {
            $this->_collection = new \Yana\Security\Rules\Requirements\Collection();
        }
        return $this->_collection;
    }

    /**
     * Initialize requirements.
     *
     * @param  \Yana\Security\Rules\Requirements\Collection  $requirement  list of requirements
     */
    public function __construct(\Yana\Security\Rules\Requirements\IsRequirement $requirement = null)
    {
        if (!\is_null($requirement)) {
            $this->_getCollection()->offsetSet(null, $requirement);
        }
    }

    /**
     * Always returns an empty collection
     *
     * @param   string  $action  loaded requirements must be associated with this rule
     * @return  \Yana\Security\Rules\Requirements\Collection
     */
    public function loadRequirementsByAssociatedAction($action)
    {
        assert(is_string($action), 'Invalid argument type: $action. String expected');

        return $this->_getCollection();
    }

    /**
     * Always returns an empty requirement.
     *
     * @param   int  $id  of row in table securityactionrules
     * @return  \Yana\Security\Rules\Requirements\IsRequirement
     */
    public function loadRequirementById($id)
    {
        assert(is_int($id), 'Invalid argument type: $id. Integer expected');

        $requirement = new \Yana\Security\Rules\Requirements\Requirement('', '', 0);
        if ($this->_getCollection()->offsetExists($id) > 0) {
            $requirement = $this->_getCollection()->offsetGet($id);
        }
        return $requirement;
    }

    /**
     * Always returns an empty array.
     *
     * @return  array
     */
    public function loadListOfGroups()
    {
        $groups = array();
        /* @var $requirement \Yana\Security\Rules\Requirements\IsRequirement */
        foreach ($this->_getCollection()->toArray() as $requirement)
        {
            if ($requirement->getGroup()) {
                $groups[] = $requirement->getGroup();
            }
        }
        return $groups;
    }

    /**
     * Always returns an empty array.
     *
     * @return  array
     */
    public function loadListOfRoles()
    {
        $roles = array();
        /* @var $requirement \Yana\Security\Rules\Requirements\IsRequirement */
        foreach ($this->_getCollection()->toArray() as $requirement)
        {
            if ($requirement->getRole()) {
                $roles[] = $requirement->getRole();
            }
        }
        return $roles;
    }

}

?>