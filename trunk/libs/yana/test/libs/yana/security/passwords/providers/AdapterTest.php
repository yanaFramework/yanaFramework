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
class AdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Passwords\Providers\Adapter
     */
    protected $object;

    /**
     * @var \Yana\Db\IsConnection
     */
    protected $connection;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $factory = new \Yana\Db\SchemaFactory();
        $this->connection = new \Yana\Db\FileDb\NullConnection($factory->createSchema("user"));
        $this->object = new \Yana\Security\Passwords\Providers\Adapter($this->connection);
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
    public function testCount()
    {
        $this->assertSame(2, $this->object->count());
    }

    /**
     * @test
     */
    public function testGetIds()
    {
        $this->assertSame(array(1 => 1, 2 => 2), $this->object->getIds());
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $this->assertSame("ldap", $this->object->offsetGet("2")->getMethod());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testOffsetGetNotFoundException()
    {
        $this->object->offsetGet("-1");
    }

    /**
     * @test
     */
    public function testGetFromUserNameEmpty()
    {
        $this->assertSame("", $this->object->getFromUserName("administrator")->getMethod());
    }

    /**
     * @test
     */
    public function testGetFromUserName()
    {
        $this->assertSame("ldap", $this->object->getFromUserName("testuser")->getMethod());
    }

    /**
     * @test
     */
    public function testGetFromUserNameNotExists()
    {
        $this->assertSame("", $this->object->getFromUserName("no-such-user")->getMethod());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $entity = new \Yana\Plugins\Data\Entity();
        $this->object->offsetSet(null, $entity);
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $entity = new \Yana\Security\Passwords\Providers\Entity();
        $entity->setName("test")->setMethod("standard");
        $this->assertSame($entity, $this->object->offsetSet(null, $entity));
    }

    /**
     * @test
     */
    public function testSaveEntity()
    {
        $entity = new \Yana\Security\Passwords\Providers\Entity();
        $entity->setName("test")->setMethod("standard");
        $this->object->saveEntity($entity);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\MissingFieldException
     */
    public function testSaveEntityMissingFieldException()
    {
        $entity = new \Yana\Security\Passwords\Providers\Entity();
        $this->object->saveEntity($entity);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSaveEntityInvalidArgumentException()
    {
        $entity = new \Yana\Plugins\Data\Entity();
        $this->object->saveEntity($entity);
    }

    /**
     * @test
     */
    public function testOffsetUnsetNotDeletedException()
    {
        $this->object->offsetUnset(-1);
    }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $this->object->offsetUnset(1);
    }

}
