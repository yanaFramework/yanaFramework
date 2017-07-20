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

namespace Yana\Security\Data;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class UserBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Data\Adapters\ArrayAdapter
     */
    protected $adapter;

    /**
     * @var \Yana\Security\Data\UserBuilder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->adapter = new \Yana\Security\Data\Users\ArrayAdapter();
        $this->object = new \Yana\Security\Data\UserBuilder($this->adapter);
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
    public function testIsExistingUserName()
    {
        $this->assertFalse($this->object->isExistingUserName('test'));
        $this->adapter['test'] = new \Yana\Security\Data\Users\Entity('test');
        $this->assertTrue($this->object->isExistingUserName('test'));
    }

    /**
     * @test
     */
    public function testBuildFromSessionGuest()
    {
        $this->assertEquals(new \Yana\Security\Data\Users\Guest(), $this->object->buildFromSession());
    }

    /**
     * @test
     */
    public function testBuildFromSession()
    {
        $this->adapter['test'] = new \Yana\Security\Data\Users\Entity('test');
        $session = new \Yana\Security\Sessions\NullWrapper(array('user_name' => 'test'));
        $this->assertSame($this->adapter['test'], $this->object->buildFromSession($session));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\NotFoundException
     */
    public function testBuildFromUserNameNotFoundException()
    {
        $this->object->buildFromUserName('test');
    }

    /**
     * @test
     */
    public function testBuildFromUserName()
    {
        $this->adapter['test'] = new \Yana\Security\Data\Users\Entity('test');
        $this->assertSame($this->adapter['test'], $this->object->buildFromUserName('test'));
    }

    /**
     * @test
     */
    public function testBuildFromUserMail()
    {
        $entity = new \Yana\Security\Data\Users\Entity('test');
        $entity->setMail('test@domain.tld');
        $this->adapter['test'] = $entity;
        $this->assertSame($this->adapter['test'], $this->object->buildFromUserMail('test@domain.tld'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\AlreadyExistsException
     */
    public function testBuildNewUserAlreadyExistsException()
    {
        $this->adapter['test'] = new \Yana\Security\Data\Users\Entity('test');
        $this->object->buildNewUser('test', 'mail');
    }

    /**
     * @test
     */
    public function testBuildNewUser()
    {
        $user = $this->object->buildNewUser('test', 'mail');
        $this->assertEquals('TEST', $user->getId());
        $this->assertEquals('mail', $user->getMail());
    }

}
