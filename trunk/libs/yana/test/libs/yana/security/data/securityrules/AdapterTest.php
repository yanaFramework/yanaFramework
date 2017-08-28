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
class AdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\NullConnection
     */
    protected $connection;

    /**
     * @var \Yana\Security\Data\SecurityRules\Adapter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        try {

            \Yana\Db\FileDb\Driver::setBaseDirectory(CWD . 'resources/db/');
            \Yana\Db\Ddl\DDL::setDirectory(CWD . 'resources/');
            $schema = \Yana\Files\XDDL::getDatabase('user');
            $this->connection = new \Yana\Db\FileDb\NullConnection($schema);
            restore_error_handler();

        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
        $this->object = new \Yana\Security\Data\SecurityRules\Adapter($this->connection);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->connection->getSchema()->setReadonly(false); // all schema instances are cached. So this needs to be reset
    }

    /**
     * @test
     */
    public function testFindEntitiesOwnedByUser()
    {
        $entities = $this->object->findEntitiesOwnedByUser('administrator', 'default');
        $this->assertCount(3, $entities);
        $entity0 = new \Yana\Security\Data\SecurityRules\Rule('ADMIN', 'DEFAULT', false);
        $entity0->setUserName('ADMINISTRATOR')
            ->setProfile('DEFAULT')
            ->setDataAdapter($this->object);
        $this->assertEquals($entity0->setId(0), $entities[0]);
        $entity1 = new \Yana\Security\Data\SecurityRules\Rule('', 'PRINT', false);
        $entity1->setUserName('ADMINISTRATOR')
            ->setProfile('DEFAULT')
            ->setDataAdapter($this->object);
        $this->assertEquals($entity1->setId(2), $entities[1]);
        $entity2 = new \Yana\Security\Data\SecurityRules\Rule('ADMIN', 'ADMIN', false);
        $entity2->setUserName('ADMINISTRATOR')
            ->setProfile('DEFAULT')
            ->setDataAdapter($this->object);
        $this->assertEquals($entity2->setId(10), $entities[2]);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\NotFoundException
     */
    public function testFindEntitiesOwnedByUserNotFoundExceptionInvalidUser()
    {
        $this->object->findEntitiesOwnedByUser('no-such-thing', 'default');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\NotFoundException
     */
    public function testFindEntitiesOwnedByUserNotFoundExceptionInvalidProfile()
    {
        $this->object->findEntitiesOwnedByUser('administrator', 'no-such-thing');
    }

    /**
     * @test
     */
    public function testFindEntitiesWithoutProfile()
    {
        $entities = $this->object->findEntitiesOwnedByUser('administrator');
        $this->assertCount(4, $entities);
        $entity0 = new \Yana\Security\Data\SecurityRules\Rule('ADMIN', 'DEFAULT', false);
        $entity0->setUserName('ADMINISTRATOR')
            ->setProfile('DEFAULT')
            ->setDataAdapter($this->object);
        $this->assertEquals($entity0->setId(0), $entities[0]);
        $entity1 = new \Yana\Security\Data\SecurityRules\Rule('MOD', 'DEFAULT', false);
        $entity1->setUserName('ADMINISTRATOR')
            ->setProfile('foo')
            ->setDataAdapter($this->object);
        $this->assertEquals($entity1->setId(1), $entities[1]);
        $entity2 = new \Yana\Security\Data\SecurityRules\Rule('', 'PRINT', false);
        $entity2->setUserName('ADMINISTRATOR')
            ->setProfile('DEFAULT')
            ->setDataAdapter($this->object);
        $this->assertEquals($entity2->setId(2), $entities[2]);
        $entity3 = new \Yana\Security\Data\SecurityRules\Rule('ADMIN', 'ADMIN', false);
        $entity3->setUserName('ADMINISTRATOR')
            ->setProfile('DEFAULT')
            ->setDataAdapter($this->object);
        $this->assertEquals($entity3->setId(10), $entities[3]);
    }

    /**
     * @test
     */
    public function testFindEntitiesGrantedByUser()
    {
        $entities = $this->object->findEntitiesGrantedByUser('grant_test', 'default');
        $this->assertCount(1, $entities);
        $entity0 = new \Yana\Security\Data\SecurityRules\Rule('', 'TESTROLE', false);
        $entity0->setId(17)
            ->setUserName('TARGET')
            ->setGrantedByUser('GRANT_TEST')
            ->setProfile('DEFAULT')
            ->setDataAdapter($this->object);
        $this->assertEquals($entity0, $entities[0]);
    }

    /**
     * @test
     */
    public function testFindEntitiesGrantedByUserProfile()
    {
        $entities = $this->object->findEntitiesGrantedByUser('grant_test');
        $this->assertCount(2, $entities);
        $entity0 = new \Yana\Security\Data\SecurityRules\Rule('', 'TESTROLE', false);
        $entity0->setId(17)
            ->setUserName('TARGET')
            ->setGrantedByUser('GRANT_TEST')
            ->setProfile('DEFAULT')
            ->setDataAdapter($this->object);
        $this->assertEquals($entity0, $entities[0]);
        $entity1 = new \Yana\Security\Data\SecurityRules\Rule('TESTGROUP', '', false);
        $entity1->setId(18)
            ->setUserName('TARGET')
            ->setGrantedByUser('GRANT_TEST')
            ->setProfile('OTHER')
            ->setDataAdapter($this->object);
        $this->assertEquals($entity1, $entities[1]);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\NotFoundException
     */
    public function testFindEntitiesGrantedByUserNotFoundExceptionInvalidUser()
    {
        $this->object->findEntitiesOwnedByUser('no-such-thing', 'default');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\NotFoundException
     */
    public function testFindEntitiesGrantedByUserNotFoundExceptionInvalidProfile()
    {
        $this->object->findEntitiesOwnedByUser('administrator', 'no-such-thing');
    }

    /**
     * @test
     */
    public function testGetIds()
    {
        $ids = $this->object->getIds();
        $this->assertCount(20, $ids);
        $this->assertSame(0, current($ids));
        $this->assertSame(1, $ids[1]);
        $this->assertSame(2, $ids[2]);
    }

    /**
     * @test
     */
    public function testOffsetExists()
    {
        $this->assertFalse($this->object->offsetExists(-1));
        $this->assertTrue($this->object->offsetExists(0));
        $this->assertTrue($this->object->offsetExists(1));
        $this->assertTrue($this->object->offsetExists(2));
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $expected = new \Yana\Security\Data\SecurityRules\Rule('ADMIN', 'DEFAULT', false);
        $expected->setUserName('ADMINISTRATOR')
            ->setProfile('DEFAULT')
            ->setId(0)
            ->setDataAdapter($this->object);

        $entity = $this->object->offsetGet(0);
        $this->assertEquals($expected, $entity);
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $expected = new \Yana\Security\Data\SecurityRules\Rule('TestGroup', 'TestRole', true);
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
