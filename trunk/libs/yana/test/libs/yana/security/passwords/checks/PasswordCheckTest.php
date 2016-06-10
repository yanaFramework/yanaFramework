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

namespace Yana\Security\Passwords\Checks;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class PasswordCheckTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Security\Users\IsUser
     */
    protected $_user;

    /**
     * @var  \Yana\Security\Passwords\Checks\PasswordCheck
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_user = new \Yana\Security\Users\Entity("name");
        $this->_object = new \Yana\Security\Passwords\Checks\PasswordCheck(new \Yana\Security\Passwords\NullAlgorithm());
    }

    /**
     * @test
     */
    public function testInvoke()
    {
        $password = "Password!Äüß";
        $this->assertTrue($this->_object->__invoke($this->_user->setId("Test")->setPassword($password), "Test", $password));
        $this->assertTrue($this->_object->__invoke($this->_user->setId("Test")->setPassword($password), "test", $password));
        $this->assertTrue($this->_object->__invoke($this->_user->setId("test")->setPassword($password), "Test", $password));
        $this->assertFalse($this->_object->__invoke($this->_user->setId("Test")->setPassword($password), "Rest", $password));
        $this->assertFalse($this->_object->__invoke($this->_user->setId("Test")->setPassword("password!Äüß"), "Test", $password));
        $this->assertFalse($this->_object->__invoke($this->_user->setId("Test")->setPassword("invalid"), "Test", $password));
    }

}

?>