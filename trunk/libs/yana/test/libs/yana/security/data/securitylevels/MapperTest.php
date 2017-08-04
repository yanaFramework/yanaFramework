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

namespace Yana\Security\Data\SecurityLevels;


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
     * @var \Yana\Security\Data\SecurityLevels\Mapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Security\Data\SecurityLevels\Mapper();
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
            \Yana\Security\Data\Tables\LevelEnumeration::ID => 10,
            \Yana\Security\Data\Tables\LevelEnumeration::LEVEL => 100,
            \Yana\Security\Data\Tables\LevelEnumeration::IS_PROXY => true
        );
        $entity = $this->object->toEntity($databaseRow);
        $this->assertSame($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::ID], $entity->getId());
        $this->assertSame($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::LEVEL], $entity->getSecurityLevel());
        $this->assertSame($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::IS_PROXY], $entity->isUserProxyActive());
    }

    /**
     * @test
     */
    public function testToEntityWithNullValues()
    {
        $databaseRow = array(
            \Yana\Security\Data\Tables\LevelEnumeration::ID => 10,
            \Yana\Security\Data\Tables\LevelEnumeration::LEVEL => 100
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
            \Yana\Security\Data\Tables\LevelEnumeration::ID => 1,
            \Yana\Security\Data\Tables\LevelEnumeration::LEVEL => 10,
            \Yana\Security\Data\Tables\LevelEnumeration::IS_PROXY => false,
            \Yana\Security\Data\Tables\LevelEnumeration::PROFILE => 'ProfileÄö@',
            \Yana\Security\Data\Tables\LevelEnumeration::USER => 'UserÄö@',
            \Yana\Security\Data\Tables\LevelEnumeration::GRANTED_BY_USER => 'GrantedÄö@'
        );
        $entity = new \Yana\Security\Data\SecurityLevels\Level(
            $databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::LEVEL],
            $databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::IS_PROXY]);
        $entity
            ->setId($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::ID])
            ->setUserName($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::USER])
            ->setGrantedByUser($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::GRANTED_BY_USER])
            ->setProfile($databaseRow[\Yana\Security\Data\Tables\LevelEnumeration::PROFILE]);
        $this->assertEquals($databaseRow, $this->object->toDatabaseRow($entity));
    }

}
