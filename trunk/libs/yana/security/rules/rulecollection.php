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

namespace Yana\Security\Rules;

/**
 * Collection of security rules.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class RuleCollection extends \Yana\Core\AbstractCollection
{

    /**
     * Add a new rule to the collection.
     *
     * @param   scalar                       $offset  rule id
     * @param   \Yana\Security\Rules\IsRule  $value   rule that shoud be added
     * @return  \Yana\Security\Rules\IsRule
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the value is not a mapper
     */
    public function offsetSet($offset, $value)
    {
        assert('is_null($offset) || is_scalar($offset); // $offset expected to be Scalar');
        if (!$value instanceof \Yana\Security\Rules\IsRule) {
            $message = "Instance of \Yana\Security\Rules\IsRule expected. " .
                "Found " . gettype($value) . "(" . get_class($value) . ") instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
        return $this->_offsetSet($offset, $value);
    }

    /**
     * Check requirements against given rules.
     *
     * @param   \Yana\Security\Rules\Requirements\Collection  $requirements  list of required privileges
     * @param   string                                        $profileId     profile id
     * @param   string                                        $action        action name
     * @param   string                                        $userName      user name
     * @return  bool
     */
    public function checkRules(\Yana\Security\Rules\Requirements\Collection $requirements, $profileId, $action, $userName)
    {
        assert('is_string($profileId); // Wrong argument type argument 2. String expected');
        assert('is_string($action); // Wrong argument type argument 3. String expected');
        assert('is_string($userName); // Wrong argument type argument 4. String expected');

        assert('!isset($result); // cannot redeclare $result');
        $result = false; // By default we always deny permission

        // loop through rules
        assert('!isset($rule); // cannot redeclare $rule');
        foreach ($this->toArray() as $rule)
        {
            assert('!isset($requirement); // cannot redeclare $requirement');
            foreach ($requirements as $requirement)
            {
                switch ($rule($requirement, $profileId, $action, $userName))
                {
                    case false:
                        return false;
                    case true:
                        $result = true;
                    // else: rule does not apply
                }
            }
            unset($requirement);
        }
        unset($rule);

        assert('is_bool($result); // return type should be boolean');
        return $result;
    }

}

?>