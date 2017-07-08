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

namespace Yana\Security\Data\Behaviors;


/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Data\Adapters\ArrayAdapter
     */
    protected $adapter;

    /**
     * @var \Yana\Security\Data\Behaviors\Builder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->adapter = new \Yana\Data\Adapters\ArrayAdapter();
        $this->object = new \Yana\Security\Data\Behaviors\Builder($this->adapter);
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
    public function test__invoke()
    {
        $entity = new \Yana\Security\Data\Users\Entity('test');
        $container = new \Yana\Security\Dependencies\Container();
        $expected = new \Yana\Security\Data\Behaviors\Standard($container, $entity);
        $this->assertEquals($expected, $this->object->__invoke($entity));
    }


    /**
     * @test
     */
    public function testBuildFromSessionGuest()
    {
        $container = new \Yana\Security\Dependencies\Container();
        $expected = new \Yana\Security\Data\Behaviors\Standard($container, new \Yana\Security\Data\Users\Guest());
        $this->assertEquals($expected, $this->object->buildFromSession());
    }

    /**
     * @test
     */
    public function testBuildFromSession()
    {
        $this->adapter['test'] = new \Yana\Security\Data\Users\Entity('test');
        $container = new \Yana\Security\Dependencies\Container();
        $expected = new \Yana\Security\Data\Behaviors\Standard($container, $this->adapter['test']);

        $session = new \Yana\Security\Sessions\NullWrapper(array('user_name' => 'test'));
        $this->assertEquals($expected, $this->object->buildFromSession($session));
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
        $container = new \Yana\Security\Dependencies\Container();
        $expected = new \Yana\Security\Data\Behaviors\Standard($container, $this->adapter['test']);
        $this->assertEquals($expected, $this->object->buildFromUserName('test'));
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
        $this->assertTrue($user instanceof \Yana\Security\Data\Behaviors\Standard);
        $this->assertEquals('TEST', $user->getId());
        $this->assertEquals('mail', $user->getMail());
    }

}
