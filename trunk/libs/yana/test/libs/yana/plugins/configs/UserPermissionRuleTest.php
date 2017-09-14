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

namespace Yana\Plugins;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class UserPermissionRuleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Configs\UserPermissionRule
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Configs\UserPermissionRule();
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
    public function testGetRole()
    {
        $this->assertEquals('', $this->object->getRole());
    }

    /**
     * @test
     */
    public function testSetRole()
    {
        $this->object->setRole('test');
        $this->assertEquals('test', $this->object->getRole());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetRoleInvalidArgumentException()
    {
        $this->object->setRole(' ');
    }

    /**
     * @test
     */
    public function testGetGroup()
    {
        $this->assertEquals('', $this->object->getGroup());
    }

    /**
     * @test
     */
    public function testSetGroup()
    {
        $this->object->setGroup('test');
        $this->assertEquals('test', $this->object->getGroup());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetGroupInvalidArgumentException()
    {
        $this->object->setGroup(' ');
    }

    /**
     * @test
     */
    public function testGetLevel()
    {
        $this->assertEquals(0, $this->object->getLevel());
    }

    /**
     * @test
     */
    public function testSetLevel()
    {
        $this->object->setLevel(0);
        $this->assertEquals(0, $this->object->getLevel());
        $this->object->setLevel(100);
        $this->assertEquals(100, $this->object->getLevel());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetLevelLowerBoundary()
    {
        $this->object->setLevel(-1);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetLevelUpperBoundary()
    {
        $this->object->setLevel(101);
    }

}
