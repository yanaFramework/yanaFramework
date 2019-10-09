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

namespace Yana\Security\Data\SecurityRules;

/**
 * Collection of security rules.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Collection extends \Yana\Core\AbstractCollection implements \Yana\Security\Data\SecurityRules\IsCollection
{

    /**
     * Add a new rule to the collection.
     *
     * @param   scalar                                    $offset  rule id
     * @param   \Yana\Security\Data\SecurityRules\IsRule  $value   rule that shoud be added
     * @return  \Yana\Security\Data\SecurityRules\IsRule
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not a mapper
     */
    public function offsetSet($offset, $value)
    {
        assert(is_null($offset) || is_scalar($offset), '$offset expected to be Scalar');
        if (!$value instanceof \Yana\Security\Data\SecurityRules\IsRule) {
            $message = "Instance of \Yana\Security\Data\SecurityRules\IsRule expected. " .
                "Found " . gettype($value) . "(" . get_class($value) . ") instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        return $this->_offsetSet($offset, $value);
    }

    /**
     * Check for group+role combination.
     *
     * Returns bool(true) if the collection contains a rule that has a combination
     * of this group and role.
     *
     * Returns bool(false) otherwise.
     *
     * @param   string  $group  user group
     * @param   string  $role   user role
     * @return  bool
     */
    public function hasGroupAndRole($group, $role)
    {
        /* @var $rule \Yana\Security\Data\SecurityRules\IsRule */
        foreach ($this->toArray() as $rule)
        {
            if (0 === \strcasecmp($rule->getRole(), $role) && 0 === \strcasecmp($rule->getGroup(), $group)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check for role.
     *
     * Returns bool(true) if the collection contains a rule that has the role.
     *
     * Returns bool(false) otherwise.
     *
     * @param   string  $role  user role
     * @return  bool
     */
    public function hasRole($role)
    {
        /* @var $rule \Yana\Security\Data\SecurityRules\IsRule */
        foreach ($this->toArray() as $rule)
        {
            if (0 === \strcasecmp($rule->getRole(), $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check for group.
     *
     * Returns bool(true) if the collection contains a rule that has the group.
     *
     * Returns bool(false) otherwise.
     *
     * @param   string  $group  user group
     * @return  bool
     */
    public function hasGroup($group)
    {
        /* @var $rule \Yana\Security\Data\SecurityRules\IsRule */
        foreach ($this->toArray() as $rule)
        {
            if (0 === \strcasecmp($rule->getGroup(), $group)) {
                return true;
            }
        }
        return false;
    }
            
}

?>