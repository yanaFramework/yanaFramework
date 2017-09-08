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

namespace Yana\Core;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * Test implementation
 *
 * @package  test
 * @ignore
 */
class MyCountableArray extends \Yana\Core\AbstractCountableArray
{
}

/**
 * @package  test
 */
class AbstractCountableArrayTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\MyCountableArray
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new \Yana\Core\MyCountableArray();
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
    public function testCount()
    {
        $this->assertEquals(0, count($this->_object));
        $this->assertEquals(0, $this->_object->count());
        $this->_object->offsetSet(null, 0);
        $this->_object->offsetSet(null, 1);
        $this->_object->offsetSet(null, 2);
        $this->_object->offsetSet(null, 3);
        $this->assertEquals(4, count($this->_object));
        $this->assertEquals(4, $this->_object->count());
    }

    /**
     * @test
     */
    public function testOffsetExists()
    {
        $this->_object->offsetSet(1, 0);
        $this->assertTrue(isset($this->_object[1]));
        $this->assertTrue($this->_object->offsetExists(1));
        $this->assertFalse(isset($this->_object[-1]));
        $this->assertFalse($this->_object->offsetExists(-1));
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $this->_object->offsetSet(1, 1);
        $this->assertEquals(1, $this->_object[1]);
        $this->assertEquals(null, $this->_object[-1]);
        $this->assertEquals(1, $this->_object->offsetGet(1));
        $this->assertEquals(null, $this->_object->offsetGet(-1));
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $this->assertEquals(null, $this->_object[-1]);
        $this->_object[-1] = 'a';
        $this->assertEquals('a', $this->_object[-1]);
        $this->_object->offsetSet(-1, 'a');
        $this->assertEquals('a', $this->_object[-1]);
    }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $this->_object[0] = 1;
        $this->assertTrue(isset($this->_object[0]));
        $this->_object->offsetUnset(0);
        $this->assertFalse(isset($this->_object[0]));
    }

}
