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

namespace Yana\Security\Passwords\Providers;

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
     * @var \Yana\Security\Passwords\Providers\Mapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Security\Passwords\Providers\Mapper();
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
            \Yana\Security\Data\Tables\AuthenticationProviderEnumeration::ID => "123",
            \Yana\Security\Data\Tables\AuthenticationProviderEnumeration::HOST => "Host",
            \Yana\Security\Data\Tables\AuthenticationProviderEnumeration::METHOD => "Method",
            \Yana\Security\Data\Tables\AuthenticationProviderEnumeration::NAME => "Name"
        );
        $entity = $this->object->toEntity($databaseRow);
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::ID], $entity->getId());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::NAME], $entity->getName());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::HOST], $entity->getHost());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::METHOD], $entity->getMethod());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testToEntityInvalidArgumentException()
    {
        $databaseRow = array();
        $this->object->toEntity($databaseRow);
    }

    /**
     * @test
     */
    public function testToDatabaseRow()
    {
        $entity = (new \Yana\Security\Passwords\Providers\Entity())
            ->setId(123)
            ->setName("Name")
            ->setHost("Host")
            ->setMethod("Method");
        $databaseRow = $this->object->toDatabaseRow($entity);
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::ID], $entity->getId());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::NAME], $entity->getName());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::HOST], $entity->getHost());
        $this->assertEquals($databaseRow[\Yana\Security\Data\Tables\AuthenticationProviderEnumeration::METHOD], $entity->getMethod());
    }

}
