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

namespace Yana\Security\Sessions;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class IdGeneratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Security\Sessions\IdGenerator
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new \Yana\Security\Sessions\IdGenerator();
    }

    /**
     * @test
     */
    public function testCreateApplicationUserId()
    {
        $this->assertGreaterThan("", $this->_object->createApplicationUserId());
        $this->assertStringStartsWith("127.0.0.1@", $this->_object->createApplicationUserId());
        $this->assertEquals(1, preg_match('/^127\.0\.0\.1\@[\w \:\\\\\/]+$/s', $this->_object->createApplicationUserId()));
    }

    /**
     * @test
     */
    public function testCreateUnauthenticatedSessionId()
    {
        $this->assertGreaterThan("", $this->_object->createUnauthenticatedSessionId());
        $this->assertEquals(1, preg_match('/^[a-f\d]{10,}$/s', $this->_object->createUnauthenticatedSessionId()));
    }

    /**
     * @test
     */
    public function testCreateAuthenticatedSessionId()
    {
        $this->assertGreaterThan("", $this->_object->createAuthenticatedSessionId());
        $this->assertEquals(1, preg_match('/^[a-f\d]{10,}$/s', $this->_object->createAuthenticatedSessionId()));
        $this->assertGreaterThanOrEqual(strlen($this->_object->createUnauthenticatedSessionId()), strlen($this->_object->createAuthenticatedSessionId()));
    }

}

?>