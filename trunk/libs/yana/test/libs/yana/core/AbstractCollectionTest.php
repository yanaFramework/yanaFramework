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
declare(strict_types=1);

namespace Yana\Core;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * Test implementation
 *
 * @package  test
 */
class MyCollection extends \Yana\Core\AbstractCollection
{

    /**
     * Implements abstract function.
     *
     * @param scalar $offset
     * @param mixed  $value 
     */
    public function offsetSet($offset, $value)
    {
        return parent::_offsetSet($offset, $value);
    }

}

/**
 * @package  test
 */
class AbstractCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Collection
     */
    private $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new MyCollection();
        $this->_object->setItems(array(0, 1, 2, 3));
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
    public function testSetItems()
    {
        $this->assertEquals(array('1', '2'), $this->_object->setItems(array('1', '2'))->toArray());
    }

    /**
     * @test
     */
    public function testIterator()
    {
        foreach ($this->_object as $key => $value)
        {
            $this->assertEquals($key, $value);
            $this->assertTrue($this->_object->valid());
        }
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\OutOfBoundsException
     */
    public function testIteratorOutOfBoundsException()
    {
        foreach ($this->_object as $key => $value)
        {
            // intenionally left blank
        }
        $this->assertFalse($this->_object->valid());
        $this->_object->current();
    }

    /**
     * @test
     */
    public function testCurrent()
    {
        $i = 0;
        while ($this->_object->valid())
        {
            $this->assertEquals($i, $this->_object->current());
            $this->assertEquals($i, $this->_object->key());
            $this->_object->next();
            $this->assertTrue($i < 4);
            $i++;
        }
    }

    /**
     * @test
     */
    public function testCountable()
    {
        $this->assertEquals(4, count($this->_object));
        $this->assertEquals(4, $this->_object->count());
    }

    /**
     * @test
     */
    public function testToArray()
    {
        $this->assertEquals(array(0, 1, 2, 3), $this->_object->toArray());
    }

    /**
     * @test
     */
    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->_object[1]));
        $this->assertFalse(isset($this->_object[-1]));
    }

    /**
     * @test
     */
    public function testOffsetExistsAPI()
    {
        $this->assertTrue($this->_object->offsetExists(1));
        $this->assertFalse($this->_object->offsetExists(-1));
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $this->assertEquals(1, $this->_object[1]);
        $this->assertEquals(null, $this->_object[-1]);
    }

    /**
     * @test
     */
    public function testOffsetGetViaAPI()
    {
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
    }

    /**
     * @test
     */
    public function testOffsetSetViaAPI()
    {
        $this->assertEquals(null, $this->_object[-1]);
        $this->assertSame('a', $this->_object->offsetSet(-1, 'a'));
        $this->assertEquals('a', $this->_object[-1]);
    }

    /**
     * @test
     */
    public function testOffsetSetNull()
    {
        $this->_object->setItems();
        $this->assertSame('b', $this->_object->offsetSet(null, 'b'));
        $this->assertEquals('b', $this->_object->offsetGet(0));
    }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $this->assertTrue(isset($this->_object[0]));
        $this->_object->offsetUnset(0);
        $this->assertFalse(isset($this->_object[0]));
    }

    /**
     * @test
     */
    public function testOffsetUnsetViaAPI()
    {
        $this->assertTrue(isset($this->_object[0]));
        $this->_object->offsetUnset(0);
        $this->assertFalse(isset($this->_object[0]));
    }

}
