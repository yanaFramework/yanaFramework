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

class MyAbstractCheck extends \Yana\Security\Passwords\Checks\AbstractCheck
{
    public function __invoke(\Yana\Security\Users\IsUser $user, $userName, $password)
    {
        return $this->_isValidUserName($user, $userName);
    }
}

/**
 * Test-case
 *
 * @package  test
 */
class AbstractCheckTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Security\Passwords\Checks\MyAbstractCheck
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new \Yana\Security\Passwords\Checks\MyAbstractCheck();
    }

    /**
     * @test
     */
    public function testInvoke()
    {
        $this->assertTrue($this->_object->__invoke(new \Yana\Security\Users\Entity("Test"), "Test", "Password!"));
        $this->assertTrue($this->_object->__invoke(new \Yana\Security\Users\Entity("Test"), "test", "Password!"));
        $this->assertTrue($this->_object->__invoke(new \Yana\Security\Users\Entity("test"), "Test", "Password!"));
        $this->assertFalse($this->_object->__invoke(new \Yana\Security\Users\Entity("Test"), "Rest", "Password!"));
    }

}

?>