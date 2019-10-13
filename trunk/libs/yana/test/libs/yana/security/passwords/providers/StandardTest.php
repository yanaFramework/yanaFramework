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

namespace Yana\Security\Passwords\Providers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Data\Users\IsEntity
     */
    protected $user;

    /**
     * @var \Yana\Security\Passwords\Providers\Standard
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->user = new \Yana\Security\Data\Users\Entity('test');
        $this->user->setPassword("test")->setActive(true);
        $this->object = new \Yana\Security\Passwords\Providers\Standard(new \Yana\Security\Passwords\NullAlgorithm());
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
    public function testIsAbleToChangePassword()
    {
        $this->assertTrue($this->object->isAbleToChangePassword());
    }

    /**
     * @test
     */
    public function testChangePassword()
    {
        $this->assertNull($this->object->changePassword($this->user, "test2"));
        $this->assertSame("test2", $this->user->getPassword());
    }

    /**
     * @test
     */
    public function testCheckPassword()
    {
        $this->assertTrue($this->object->checkPassword($this->user, "test"));
        $this->assertFalse($this->object->checkPassword($this->user, "test2"));
    }

}
