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
        $this->object->findEntityOwnedByUser('non-existing-user', 'default');
    }

    /**
     * @test
     */
    public function testFindEntity()
    {
        $enity = $this->object->findEntityOwnedByUser('testuser1', 'default');
        $this->assertSame(80, $enity->getSecurityLevel());
        $this->assertSame(false, $enity->isUserProxyActive());
    }

    /**
     * @test
     */
    public function testFindEntitiesOwnedByUser()
    {
        $enities = $this->object->findEntitiesOwnedByUser('administrator');
        $this->assertTrue($enities instanceof \Yana\Security\Data\SecurityLevels\IsCollection);
        $this->assertCount(5, $enities);
        $this->assertSame(8, $enities[0]->getId());
        $this->assertSame(9, $enities[1]->getId());
        $this->assertSame(10, $enities[2]->getId());
        $this->assertSame(170, $enities[3]->getId());
        $this->assertSame(173, $enities[4]->getId());
    }

    /**
     * @test
     */
    public function testFindEntitiesGratnedByUser()
    {
        $enities = $this->object->findEntitiesGrantedByUser('grant_test');
        $this->assertTrue($enities instanceof \Yana\Security\Data\SecurityLevels\IsCollection);
        $this->assertCount(2, $enities);
        $this->assertSame(217, $enities[0]->getId());
        $this->assertSame(218, $enities[1]->getId());
    }

    /**
     * @test
     */
    public function testFindEntitiesGratnedByUserWithProfile()
    {
        $enities = $this->object->findEntitiesGrantedByUser('grant_test', 'default');
        $this->assertTrue($enities instanceof \Yana\Security\Data\SecurityLevels\IsCollection);
        $this->assertCount(1, $enities);
        $this->assertSame(17, $enities[0]->getSecurityLevel());
    }

    /**
     * @test
     */
    public function testGetIds()
    {
        $ids = $this->object->getIds();
        $this->assertCount(18, $ids);
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
