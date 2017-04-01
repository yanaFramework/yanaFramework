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
abstract class AbstractAlgorithmTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Security\Passwords\IsAlgorithm
     */
    protected $_object;

    /**
     * Creates and returns a random string of 16 byte.
     *
     * @return  string
     */
    protected function _createSalt()
    {
        return (\function_exists('random_bytes')) ? random_bytes(16) : mcrypt_create_iv(16, \MCRYPT_DEV_URANDOM);
    }

    /**
     * @test
     */
    public function testInvoke()
    {
        $password = "Password!Äüß";

        $actualValue1 = $this->_object->__invoke($password);
        $this->assertTrue(is_string($actualValue1), 'Must return string expected');
        $this->assertNotEquals($password, $actualValue1, 'Must not return unhashed string');

        $actualValue2 = $this->_object->__invoke($password);
        $this->assertNotEquals($actualValue1, $actualValue2, 'Must not return unsalted hashes');

        $this->assertTrue(\password_verify($password, $actualValue1));
        $this->assertTrue($this->_object->isEqual($password, $actualValue1));
        $this->assertTrue($this->_object->isEqual($password, $actualValue2));
        $this->assertFalse($this->_object->isEqual($password, $password));
    }
}

?>