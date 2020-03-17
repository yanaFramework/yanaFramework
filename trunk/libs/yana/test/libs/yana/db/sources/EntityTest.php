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
class EntityTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Sources\Entity
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Sources\Entity();
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
    public function testBuildFromDsn()
    {
        $dsn = array(
            \Yana\Db\Sources\DsnEnumeration::HOST => "Host",
            \Yana\Db\Sources\DsnEnumeration::DBMS => "Dbms",
            \Yana\Db\Sources\DsnEnumeration::DATABASE => "Database",
            \Yana\Db\Sources\DsnEnumeration::PASSWORD => "Password",
            \Yana\Db\Sources\DsnEnumeration::PORT => "456",
            \Yana\Db\Sources\DsnEnumeration::USER => "User"
        );
        $entity = \Yana\Db\Sources\Entity::buildFromDsn($dsn);
        $this->assertEquals($dsn[\Yana\Db\Sources\DsnEnumeration::HOST], $entity->getHost());
        $this->assertEquals($dsn[\Yana\Db\Sources\DsnEnumeration::DBMS], $entity->getDbms());
        $this->assertEquals($dsn[\Yana\Db\Sources\DsnEnumeration::DATABASE], $entity->getDatabase());
        $this->assertEquals($dsn[\Yana\Db\Sources\DsnEnumeration::PASSWORD], $entity->getPassword());
        $this->assertEquals($dsn[\Yana\Db\Sources\DsnEnumeration::PORT], $entity->getPort());
        $this->assertEquals($dsn[\Yana\Db\Sources\DsnEnumeration::USER], $entity->getUser());
    }

    /**
     * @test
     */
    public function testToDsn()
    {
        $this->object
            ->setHost("Host")
            ->setDbms("Dbms")
            ->setDatabase("Database")
            ->setPassword("Password")
            ->setPort(456)
            ->setUser("User");
        $dsn = $this->object->toDsn();
        $this->assertEquals($dsn[\Yana\Db\Sources\DsnEnumeration::HOST], $this->object->getHost());
        $this->assertEquals($dsn[\Yana\Db\Sources\DsnEnumeration::DBMS], $this->object->getDbms());
        $this->assertEquals($dsn[\Yana\Db\Sources\DsnEnumeration::DATABASE], $this->object->getDatabase());
        $this->assertEquals($dsn[\Yana\Db\Sources\DsnEnumeration::PASSWORD], $this->object->getPassword());
        $this->assertEquals($dsn[\Yana\Db\Sources\DsnEnumeration::PORT], $this->object->getPort());
        $this->assertEquals($dsn[\Yana\Db\Sources\DsnEnumeration::USER], $this->object->getUser());
    }

    /**
     * @test
     */
    public function testGetId()
    {
        $this->assertNull($this->object->getId());
    }

    /**
     * @test
     */
    public function testSetId()
    {
        $this->assertSame(1, $this->object->setId(1)->getId());
        $this->assertSame(1, $this->object->setId("1")->getId());
    }

    /**
     * @test
     */
    public function testGetName()
    {
        $this->assertSame("", $this->object->getName());
    }

    /**
     * @test
     */
    public function testSetName()
    {
        $this->assertSame("Abc ßá!", $this->object->setName("Abc ßá!")->getName());
    }

    /**
     * @test
     */
    public function testGetDbms()
    {
        $this->assertSame("", $this->object->getDbms());
    }

    /**
     * @test
     */
    public function testGetHost()
    {
        $this->assertSame("", $this->object->getHost());
    }

    /**
     * @test
     */
    public function testGetPort()
    {
        $this->assertNull($this->object->getPort());
    }

    /**
     * @test
     */
    public function testGetDatabase()
    {
        $this->assertSame("", $this->object->getDatabase());
    }

    /**
     * @test
     */
    public function testGetUser()
    {
        $this->assertSame("", $this->object->getUser());
    }

    /**
     * @test
     */
    public function testGetPassword()
    {
        $this->assertSame("", $this->object->getPassword());
    }

    /**
     * @test
     */
    public function testSetDbms()
    {
        $this->assertSame("Abc ßá!", $this->object->setDbms("Abc ßá!")->getDbms());
    }

    /**
     * @test
     */
    public function testSetHost()
    {
        $this->assertSame("Abc ßá!", $this->object->setHost("Abc ßá!")->getHost());
    }

    /**
     * @test
     */
    public function testSetPort()
    {
        $this->assertSame(123, $this->object->setPort(123)->getPort());
    }

    /**
     * @test
     */
    public function testSetDatabase()
    {
        $this->assertSame("Abc ßá!", $this->object->setDatabase("Abc ßá!")->getDatabase());
    }

    /**
     * @test
     */
    public function testSetUser()
    {
        $this->assertSame("Abc ßá!", $this->object->setUser("Abc ßá!")->getUser());
    }

    /**
     * @test
     */
    public function testSetPassword()
    {
        $this->assertSame("Abc ßá!", $this->object->setPassword("Abc ßá!")->getPassword());
    }

}
