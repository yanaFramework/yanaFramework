<?php
/**
 * PHPUnit test-case.
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
declare(strict_types=1);

namespace Yana\Db\Sources;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class MapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Sources\Mapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Sources\Mapper();
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
            \Yana\Db\Sources\TableEnumeration::ID => "123",
            \Yana\Db\Sources\TableEnumeration::NAME => "Name",
            \Yana\Db\Sources\TableEnumeration::HOST => "Host",
            \Yana\Db\Sources\TableEnumeration::DBMS => "Dbms",
            \Yana\Db\Sources\TableEnumeration::DATABASE => "Database",
            \Yana\Db\Sources\TableEnumeration::PASSWORD => "Password",
            \Yana\Db\Sources\TableEnumeration::PORT => "456",
            \Yana\Db\Sources\TableEnumeration::USER => "User"
        );
        $entity = $this->object->toEntity($databaseRow);
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::ID], $entity->getId());
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::NAME], $entity->getName());
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::HOST], $entity->getHost());
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::DBMS], $entity->getDbms());
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::DATABASE], $entity->getDatabase());
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::PASSWORD], $entity->getPassword());
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::PORT], $entity->getPort());
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::USER], $entity->getUser());
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
        $entity = (new \Yana\Db\Sources\Entity())
            ->setId(123)
            ->setName("Name")
            ->setHost("Host")
            ->setDbms("Dbms")
            ->setDatabase("Database")
            ->setPassword("Password")
            ->setPort(456)
            ->setUser("User");
        $databaseRow = $this->object->toDatabaseRow($entity);
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::ID], $entity->getId());
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::NAME], $entity->getName());
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::HOST], $entity->getHost());
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::DBMS], $entity->getDbms());
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::DATABASE], $entity->getDatabase());
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::PASSWORD], $entity->getPassword());
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::PORT], $entity->getPort());
        $this->assertEquals($databaseRow[\Yana\Db\Sources\TableEnumeration::USER], $entity->getUser());
    }

}
