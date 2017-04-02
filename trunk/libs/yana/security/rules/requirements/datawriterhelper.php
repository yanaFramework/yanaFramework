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
 * <<wrapper>> Wraps around a method collection to add additional functions.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class DataWriterHelper extends \Yana\Core\Object
{

    /**
     * Wrapped collection.
     *
     * @var  \Yana\Plugins\Configs\MethodCollection
     */
    private $_methodCollection = null;

    /**
     * Initialize wrapped collection.
     *
     * @param  \Yana\Plugins\Configs\MethodCollection  $methodCollection  to be wrapped
     */
    public function __construct(\Yana\Plugins\Configs\MethodCollection $methodCollection)
    {
        $this->_methodCollection = $methodCollection;
    }

    /**
     * Returns the wrapped collection.
     *
     * @return  \Yana\Plugins\Configs\MethodCollection
     */
    protected function _getMethodCollection()
    {
        return $this->_methodCollection;
    }

    /**
     * Extract action ids and titles from event configurations.
     *
     * @return  array
     */
    public function getActionTitles()
    {
        $actions = array();
        /* @var $configuration \Yana\Plugins\Configs\MethodConfiguration */
        foreach ($this->_getMethodCollection() as $configuration)
        {
            $name = $configuration->getMethodName();
            $title = $configuration->getTitle();
            /**
             * @todo reactivate this when form creator is done
             * if (!isset($actions[$name]) && !empty($title)) {
             */
            if (!isset($actions[$name]) || $actions[$name] == $name) {
                if (empty($title)) {
                    $title = $name;
                }
                $actions[$name] = $title;
            }
        }
        return $actions;
    }

    /**
     * Extract roles from event configurations.
     *
     * @return  array
     */
    public function getRoleNames()
    {
        $roles = array();
        /* @var $configuration \Yana\Plugins\Configs\MethodConfiguration */
        foreach ($this->_getMethodCollection() as $configuration)
        {
            foreach ($configuration->getUserLevels() as $level)
            {
                if ($level->getRole() === "") {
                    continue;
                } else {
                    $role = mb_strtolower($level->getRole());
                    $roles[$role] = $role;
                }
            }
        }

        return $roles;
    }

    /**
     * Extract groups from event configurations.
     *
     * @return  array
     */
    public function getGroupNames()
    {
        $roles = array();
        /* @var $configuration \Yana\Plugins\Configs\MethodConfiguration */
        foreach ($this->_getMethodCollection() as $configuration)
        {
            foreach ($configuration->getUserLevels() as $level)
            {
                if ($level->getGroup() === "") {
                    continue;
                } else {
                    $role = mb_strtolower($level->getGroup());
                    $roles[$role] = $role;
                }
            }
        }

        return $roles;
    }

    /**
     * Extract rows of requirements from event configurations.
     *
     * @return  array
     */
    public function getRequirements()
    {
        $rows = array();
        /* @var $configuration \Yana\Plugins\Configs\MethodConfiguration */
        foreach ($this->_getMethodCollection() as $configuration)
        {
            assert('!isset($row); // Cannot redeclare var $row');
            assert('!isset($level); // Cannot redeclare var $level');
            foreach ($configuration->getUserLevels() as $level)
            {
                $row = $this->_mapRequirement($level, $configuration->getMethodName());
                $rows[] = $row;
            }
            unset($level, $row);
        }
        unset($configuration);

        return $rows;
    }

    /**
     * Map information of given requirement to an array.
     *
     * @param   \Yana\Plugins\Configs\UserPermissionRule  $rule  contains information about requirements
     * @param   string                                    $name   
     * @return  array
     */
    private function _mapRequirement(\Yana\Plugins\Configs\UserPermissionRule $rule, $name)
    {
        assert('is_string($name); // Invalid argument type: $name. String expected');

        $row = array(
            \Yana\Security\Users\Tables\RequirementEnumeration::IS_PREDEFINED => true,
            \Yana\Security\Users\Tables\RequirementEnumeration::ACTION => (string) $name
        );
        if ($rule->getGroup() !== "") {
            $row[\Yana\Security\Users\Tables\RequirementEnumeration::GROUP] = mb_strtolower($rule->getGroup());
        }
        if ($rule->getRole() !== "") {
            $row[\Yana\Security\Users\Tables\RequirementEnumeration::ROLE] = mb_strtolower($rule->getRole());
        }
        if ((int) $rule->getLevel() !== 0) {
            $row[\Yana\Security\Users\Tables\RequirementEnumeration::LEVEL] = (int) $rule->getLevel();
        }

        return $row;
    }

}

?>