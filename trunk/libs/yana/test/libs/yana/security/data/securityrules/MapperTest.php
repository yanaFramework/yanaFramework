<?php
/**
 * PHPUnit test-case
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
 * @package  test
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

namespace Yana\Security\Data\SecurityRules;


/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class MapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Data\SecurityRules\Mapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Security\Data\SecurityRules\Mapper();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @test
     */
    public function testToEntity()
    {
        $databaseRow = array(
            \Yana\Security\Data\Tables\RuleEnumeration::ID => -1,
            \Yana\Security\Data\Tables\RuleEnumeration::GROUP => 'GroupÄö@',
            \Yana\Security\Data\Tables\RuleEnumeration::ROLE => 'RoleÄö@',
            \Yana\Security\Data\Tables\RuleEnumeration::IS_PROXY => false,
            \Yana\Security\Data\Tables\RuleEnumeration::PROFILE => 'ProfileÄö@',
            \Yana\Security\Data\Tables\RuleEnumeration::USER => 'UserÄö@',
            \Yana\Security\Data\Tables\RuleEnumeration::GRANTED_BY_USER => 'GrantedÄö@'
        );
        $entity = $this->object->toEntity($databaseRow);
        $this->assertSame($databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::ID], $entity->getId());
        $this->assertSame($databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::GROUP], $entity->getGroup());
        $this->assertSame($databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::ROLE], $entity->getRole());
        $this->assertSame($databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::IS_PROXY], $entity->isUserProxyActive());
        $this->assertSame($databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::PROFILE], $entity->getProfile());
        $this->assertSame($databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::USER], $entity->getUserName());
        $this->assertSame($databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::GRANTED_BY_USER], $entity->getGrantedByUser());
    }

    /**
     * @test
     */
    public function testToEntityWithNullValues()
    {
        $databaseRow = array(
            \Yana\Security\Data\Tables\RuleEnumeration::ID => -1,
            \Yana\Security\Data\Tables\RuleEnumeration::GROUP => 'GroupÄö@',
            \Yana\Security\Data\Tables\RuleEnumeration::ROLE => 'RoleÄö@',
            \Yana\Security\Data\Tables\RuleEnumeration::IS_PROXY => false,
            \Yana\Security\Data\Tables\RuleEnumeration::PROFILE => 'ProfileÄö@',
            \Yana\Security\Data\Tables\RuleEnumeration::USER => 'UserÄö@'
        );
        $entity = $this->object->toEntity($databaseRow);
        $this->assertSame(false, $entity->isUserProxyActive());
    }

    /**
     * @test
     */
    public function testToDatabaseRow()
    {
        $databaseRow = array(
            \Yana\Security\Data\Tables\RuleEnumeration::ID => 1,
            \Yana\Security\Data\Tables\RuleEnumeration::GROUP => 'GroupÄö@',
            \Yana\Security\Data\Tables\RuleEnumeration::ROLE => 'RoleÄö@',
            \Yana\Security\Data\Tables\RuleEnumeration::IS_PROXY => false,
            \Yana\Security\Data\Tables\RuleEnumeration::PROFILE => 'ProfileÄö@',
            \Yana\Security\Data\Tables\RuleEnumeration::USER => 'UserÄö@',
            \Yana\Security\Data\Tables\RuleEnumeration::GRANTED_BY_USER => 'GrantedÄö@'
        );
        $entity = new \Yana\Security\Data\SecurityRules\Rule(
            $databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::GROUP],
            $databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::ROLE],
            $databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::IS_PROXY]);
        $entity
            ->setId($databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::ID])
            ->setUserName($databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::USER])
            ->setGrantedByUser($databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::GRANTED_BY_USER])
            ->setProfile($databaseRow[\Yana\Security\Data\Tables\RuleEnumeration::PROFILE]);
        $this->assertEquals($databaseRow, $this->object->toDatabaseRow($entity));
    }

}
