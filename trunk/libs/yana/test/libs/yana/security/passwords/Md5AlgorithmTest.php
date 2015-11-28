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

namespace Yana\Security\Passwords;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class Md5AlgorithmTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Security\Passwords\Md5Algorithm
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new \Yana\Security\Passwords\Md5Algorithm();
    }

    /**
     * @test
     */
    public function testInvoke()
    {
        $password = "Password!Äüß";
        $this->assertEquals(md5($password), $this->_object->__invoke("", $password));
        // First 2 letters of user name should be used as salt
        $this->assertEquals(md5("12" . $password), $this->_object->__invoke("123", $password));
        $this->assertEquals(md5("AB" . $password), $this->_object->__invoke("abc", $password));
        $this->assertEquals(md5("Äß" . $password), $this->_object->__invoke("äßc", $password));
        $this->assertNotEquals(md5("ab" . $password), $this->_object->__invoke("abc", $password));
    }

}

?>