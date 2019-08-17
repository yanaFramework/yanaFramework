<?php
/**
 * PHPUnit test-case.
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
declare(strict_types=1);

namespace Yana\Core;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class VarContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\VarContainer
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Core\VarContainer();
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
    public function testGet()
    {
        $this->assertEquals(null, $this->object->nonExisting);
    }

    /**
     * @test
     */
    public function testSet()
    {
        $this->assertEquals("Foo", $this->object->test = "Foo");
        $this->assertEquals("Foo", $this->object->getVar('test'));
    }

    /**
     * @test
     */
    public function testGetVar()
    {
        $this->assertEquals(null, $this->object->getVar('nonExisting'));
    }

    /**
     * @test
     */
    public function testGetVars()
    {
        $this->assertEquals(array(), $this->object->getVars());
    }

    /**
     * @test
     */
    public function testIsVar()
    {
        $this->assertFalse($this->object->isVar('nonExisting'));
        $this->object->nonExisting = false;
        $this->assertTrue($this->object->isVar('nonExisting'));
    }

    /**
     * @test
     */
    public function testSetVarByReference()
    {
        $test = true;
        $test = $this->object->setVarByReference('test', $test)->getVar('test');
        $test = false;
        $this->assertFalse($this->object->getVar('test'));
    }

    /**
     * @test
     */
    public function testSetVarsByReference()
    {
        $test = array(1 => 1, 2 => 2);
        $test = $this->object->setVarsByReference($test)->getVars();
        $test[3] = 3;
        $this->assertEquals($test, $this->object->getVars());
    }

    /**
     * @test
     */
    public function testSetVar()
    {
        $test = '1';
        $this->assertEquals($test, $this->object->setVar('test', $test)->getVar('test'));
    }

    /**
     * @test
     */
    public function testSetVars()
    {
        $test = array(1 => 1, 2 => 2);
        $this->assertEquals($test, $this->object->setVars($test)->getVars());
    }

}
