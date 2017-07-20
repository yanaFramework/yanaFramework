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

namespace Yana\Security\Data\Users;


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
     * @var \Yana\Security\Data\Users\Adapter
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
            restore_error_handler();
            $this->connection = new \Yana\Db\FileDb\NullConnection($schema);
            $this->object = new \Yana\Security\Data\Users\Adapter($this->connection);

        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
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
    public function testOffsetExists()
    {
        $this->assertFalse($this->object->offsetExists('non-existing-user'));
        $this->assertTrue($this->object->offsetExists('Administrator'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\NotFoundException
     */
    public function testOffsetGetNotFoundException()
    {
        $this->object->offsetGet('non-existing-user');
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $this->assertTrue($this->object->offsetGet('Administrator') instanceof \Yana\Security\Data\Users\Entity);
    }

    /**
     * @test
     */
    public function testFindUserByMail()
    {
        $this->assertTrue($this->object->findUserByMail('anymail@domain.tld') instanceof \Yana\Security\Data\Users\Entity);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\MailNotFoundException
     */
    public function testFindUserByMailNotUnique()
    {
        $this->object->findUserByMail('mail@domain.tld');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\MailNotFoundException
     */
    public function testFindUserByMailNotFoundException()
    {
        $this->object->findUserByMail('noSuchMail@domain.tld');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $this->object->offsetSet(null, "invalid");
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\MissingFieldException
     */
    public function testOffsetSetMissingFieldException()
    {
        $entity = new \Yana\Security\Data\Users\Entity("test");
        $this->object->offsetSet(null, $entity);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testOffsetSetInvalidValueException()
    {
        $entity = new \Yana\Security\Data\Users\Entity("test");
        $entity->setMail("mail");
        $this->object->offsetSet(null, $entity);
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $entity = new \Yana\Security\Data\Users\Entity("test");
        $entity->setMail("mail@domain.tld");
        $this->object->offsetSet(null, $entity);
        $actual = $this->object->offsetGet("test");
        $this->assertEquals(\mb_strtoupper($entity->getId()), $actual->getId());
        $this->assertEquals($entity->getMail(), $actual->getMail());
        $this->assertEquals("UNINITIALIZED", $actual->getPassword());
    }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $this->assertTrue($this->object->offsetExists('Testuser'));
        $this->object->offsetUnset('Testuser');
        $this->assertFalse($this->object->offsetExists('Testuser'));
    }

    /**
     * @test
     */
    public function testCount()
    {
        $this->assertSame(5, $this->object->count());
    }

    /**
     * @test
     */
    public function testGetIds()
    {
        $this->assertEquals(array(
            'ADMINISTRATOR' => 'ADMINISTRATOR',
            'TESTUSER' => 'TESTUSER',
            'MANAGER' => 'MANAGER',
            'USER' => 'USER',
            'OTHERUSERS' => 'OTHERUSERS'
        ), $this->object->getIds());
    }

    /**
     * @test
     */
    public function testSaveEntity()
    {
        $entity = $this->object->offsetGet("Testuser");
        $this->assertTrue($entity->isActive());
        $entity->setActive(false);
        $this->object->saveEntity($entity);
        $entity2 = $this->object->offsetGet("Testuser");
        $this->assertSame($entity->isActive(), $entity2->isActive());
    }

}
