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

namespace Yana\Security\Passwords\Behaviors;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class StandardBehaviorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Passwords\Behaviors\StandardBehavior
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $password = new \Yana\Security\Passwords\NullAlgorithm();
        $generator = new \Yana\Security\Passwords\Generators\NullAlgorithm();
        $provider = new \Yana\Security\Passwords\Providers\Standard($password);
        $this->object = new \Yana\Security\Passwords\Behaviors\StandardBehavior($password, $generator, $provider);
        $this->object->setUser(new \Yana\Security\Data\Users\Entity('test'));
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
    public function testCheckRecoveryId()
    {
        $this->assertFalse($this->object->checkRecoveryId(""));
        $recoveryId = $this->object->generatePasswordRecoveryId();
        $this->assertTrue($this->object->checkRecoveryId($recoveryId));
    }

    /**
     * @test
     */
    public function testCheckPassword()
    {
        $this->assertFalse($this->object->checkPassword(""));
        $randomPasswort = $this->object->generateRandomPassword();
        $this->assertTrue($this->object->checkPassword($randomPasswort));
    }

    /**
     * @test
     */
    public function testCheckPasswordUninitialized()
    {
        $this->object->getUser()->setPassword('UNINITIALIZED');
        $this->assertTrue($this->object->checkPassword(''));
        $this->assertTrue($this->object->checkPassword('Test'));
        $this->object->getUser()->setPassword('');
        $this->assertFalse($this->object->checkPassword('Test'));
    }

    /**
     * @test
     */
    public function testChangePassword()
    {
        $password = "12345678";
        $this->object->getUser()->setRecentPasswords(array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10'));
        $this->object->getUser()->setPasswordRecoveryId("test");
        $this->assertEquals($password, $this->object->changePassword($password)->getUser()->getPassword());
        $this->assertEquals(array('2', '3', '4', '5', '6', '7', '8', '9', '10', '12345678'), $this->object->getUser()->getRecentPasswords());
        $this->assertEquals("", $this->object->getUser()->getPasswordRecoveryId());
    }

    /**
     * @test
     */
    public function testGenerateRandomPassword()
    {
        $this->assertEquals("", $this->object->getUser()->getPassword());
        $randomPassword = $this->object->generateRandomPassword();
        $this->assertTrue(is_string($randomPassword));
        $this->assertEquals(10, \strlen($randomPassword));
        $this->assertEquals($randomPassword, $this->object->getUser()->getPassword());
    }

    /**
     * @test
     */
    public function testGeneratePasswordRecoveryId()
    {
        $recoveryId = $this->object->generatePasswordRecoveryId();
        $this->assertTrue($this->object->checkRecoveryId($recoveryId));
        $this->assertEquals($recoveryId, $this->object->getUser()->getPasswordRecoveryId());
    }

}
