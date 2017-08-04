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
class AdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\NullConnection
     */
    protected $connection;

    /**
     * @var \Yana\Security\Data\SecurityLevels\Adapter
     */
    protected $object;

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        \Yana\Db\Ddl\DDL::setDirectory(CWD. 'resources/');
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        try {

            \Yana\Db\FileDb\Driver::setBaseDirectory(CWD. 'resources/db/');
            \Yana\Db\Ddl\DDL::setDirectory(CWD. 'resources/');
            $schema = \Yana\Files\XDDL::getDatabase('user');
            $this->connection = new \Yana\Db\FileDb\NullConnection($schema);
            restore_error_handler();

        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
        $this->object = new \Yana\Security\Data\SecurityLevels\Adapter($this->connection);
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
     * @expectedException \Yana\Core\Exceptions\User\NotFoundException
     */
    public function testFindEntityNotFoundException()
    {
        $this->object->findEntity('non-existing-user', 'default');
    }

    /**
     * @test
     */
    public function testFindEntity()
    {
        $enity = $this->object->findEntity('testuser1', 'default');
        $this->assertSame(80, $enity->getSecurityLevel());
        $this->assertSame(false, $enity->isUserProxyActive());
    }

    /**
     * @test
     */
    public function testFindEntities()
    {
        $enities = $this->object->findEntities('administrator');
        $this->assertTrue($enities instanceof \Yana\Security\Data\SecurityLevels\Collection);
        $this->assertCount(4, $enities);
        $this->assertSame(100, $enities['NG']->getSecurityLevel());
        $this->assertSame(100, $enities['BAR']->getSecurityLevel());
        $this->assertSame(100, $enities['DEFAULT']->getSecurityLevel());
        $this->assertSame(100, $enities['TT']->getSecurityLevel());
    }

    /**
     * @test
     */
    public function testGetIds()
    {
        $ids = $this->object->getIds();
        $this->assertCount(14, $ids);
        $this->assertSame(1, current($ids));
        $this->assertSame(1, $ids[1]);
        $this->assertSame(2, $ids[2]);
    }

    /**
     * @test
     */
    public function testOffsetExists()
    {
        $this->assertFalse($this->object->offsetExists(-1));
        $this->assertTrue($this->object->offsetExists(1));
        $this->assertTrue($this->object->offsetExists(2));
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $expected = new \Yana\Security\Data\SecurityLevels\Level(80, false);
        $expected->setUserName('TESTUSER1')
            ->setProfile('DEFAULT')
            ->setId(1)
            ->setGrantedByUser('TESTUSER1')
            ->setDataAdapter($this->object);

        $entity = $this->object->offsetGet(1);
        $this->assertEquals($expected, $entity);
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $expected = new \Yana\Security\Data\SecurityLevels\Level(10, true);
        $expected->setUserName('ADMINISTRATOR')
            ->setProfile('Profile')
            ->setGrantedByUser('ADMINISTRATOR')
            ->setId(1);

        $entity = $this->object->offsetSet(null, $expected);
        $this->assertSame($expected, $entity);
        $this->assertSame(1, $entity->getId());
        $actual = $this->object->offsetGet(1);
        $this->assertSame(1, $actual->getId());
        $this->assertEquals($expected, $entity);
    }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $this->assertTrue($this->object->offsetExists(1));
        $this->object->offsetUnset(1);
        $this->assertFalse($this->object->offsetExists(1));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\NotFoundException
     */
    public function testOffsetUnsetNotFoundException()
    {
        $this->object->offsetUnset(-1);
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\NotDeletedException
     */
    public function testOffsetUnsetNotDeletedException()
    {
        $this->connection
            ->getSchema()
            ->setReadonly(true);
        $this->object->offsetUnset(1);
    }

}
