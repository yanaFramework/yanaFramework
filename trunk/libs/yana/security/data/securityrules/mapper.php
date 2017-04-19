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
 * User manager.
 *
 * This persistent class provides access to user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Mapper extends \Yana\Core\Object implements \Yana\Data\Adapters\IsEntityMapper
{

    /**
     * Creates an entity based on a database row.
     *
     * @param   array  $databaseRow  row containing user info
     * @return  \Yana\Security\Data\SecurityRules\Rule
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when given user has no name
     */
    public function toEntity(array $databaseRow)
    {
        assert('!isset($group); // Cannot redeclare var $group');
        $group = "";
        if (isset($databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::GROUP])) {
            $group = (string) $databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::GROUP];
        }
        assert('!isset($role); // Cannot redeclare var $role');
        $role = "";
        if (isset($databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::ROLE])) {
            $role = (string) $databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::ROLE];
        }
        assert('!isset($isProxy); // Cannot redeclare var $isProxy');
        $isProxy = true;
        if (isset($databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::IS_PROXY])) {
            $isProxy = (bool) $databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::IS_PROXY];
        }

        return new \Yana\Security\Data\SecurityRules\Rule($group, $role, $isProxy);
    }

    /**
     * Creates a database row based on an entity.
     *
     * @param   \Yana\Security\Data\SecurityRules\Rule  $entity  entity containing user info
     * @return  array
     */
    public function toDatabaseRow(\Yana\Data\Adapters\IsEntity $entity)
    {
        assert('!isset($row); // Cannot redeclare var $row');
        $row = array();

        if ($entity instanceof \Yana\Security\Data\SecurityRules\Rule) {

            $row[\Yana\Security\Data\Tables\RuleEnumeration::IS_PROXY] = $entity->isUserProxyActive();
            $row[\Yana\Security\Data\Tables\RuleEnumeration::GROUP] = $entity->getGroup();
            $row[\Yana\Security\Data\Tables\RuleEnumeration::ROLE] = $entity->getRole();
        }
        return $row;
    }

}

?>