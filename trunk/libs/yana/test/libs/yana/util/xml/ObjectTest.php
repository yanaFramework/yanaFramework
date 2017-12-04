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

namespace Yana\Util\Xml;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../../include.php';


/**
 * @package  test
 */
class ObjectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Util\Xml\Object
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Util\Xml\Object();
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
    public function test__toString()
    {
        $this->assertSame("", $this->object->__toString());
    }

    /**
     * @test
     */
    public function testAddAttribute()
    {
        $this->assertSame("Test", $this->object->addAttribute('a', 'Test')->getAttribute('a'));
    }

    /**
     * @test
     */
    public function testHasAttribute()
    {
        $this->assertFalse($this->object->hasAttribute('a'));
        $this->assertTrue($this->object->addAttribute('a', '')->hasAttribute('a'));
    }

    /**
     * @test
     */
    public function testGetAttribute()
    {
        $this->assertSame("", $this->object->getAttribute('a'));
    }

    /**
     * @test
     */
    public function testSetPcData()
    {
        $this->assertSame("Test", $this->object->setPcData("Test")->getPcData());
        $this->assertObjectHasAttribute("#pcdata", $this->object);
        $this->assertSame("Test", $this->object->{'#pcdata'});
        $this->assertSame("Test", $this->object->getPcData());
        $this->assertSame("Test", $this->object->__toString());
        $this->assertSame("Test", (string) $this->object);
    }

    /**
     * @test
     */
    public function testGetPcData()
    {
        $this->assertSame("", $this->object->getPcData());
    }

    /**
     * @test
     */
    public function testOffsetExists()
    {
        $this->assertFalse($this->object->offsetExists("a"));
        $this->object["a"] = "test";
        $this->assertTrue($this->object->offsetExists("a"));
        $this->assertSame("test", $this->object["a"]);
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $this->assertNull($this->object->offsetGet("a"));
        $this->object["a"] = "test";
        $this->assertSame("test", $this->object->offsetGet("a"));
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $this->object->offsetSet("a", "Test");
        $this->assertSame("Test", $this->object->offsetGet("a"));
    }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $this->object->offsetSet("a", "Test");
        $this->assertTrue($this->object->offsetExists("a"));
        $this->object->offsetUnset("a");
        $this->assertFalse($this->object->offsetExists("a"));
    }

    /**
     * @test
     */
    public function testGetAll()
    {
        $expected = new \Yana\Util\Xml\Collection();
        $this->assertEquals($expected, $this->object->getAll("a"));
        $this->object->a = "1";
        $this->assertEquals($expected->setItems(array("1")), $this->object->getAll("a"));
        $this->object->a = new \Yana\Util\Xml\Object("1");
        $this->assertEquals($expected->setItems(array("1")), $this->object->getAll("a"));
        $this->object->a = array(new \Yana\Util\Xml\Object("1"));
        $this->assertEquals($expected->setItems(array("1")), $this->object->getAll("a"));
        $this->object->a = array("1", "2");
        $this->assertEquals($expected->setItems(array("1", "2")), $this->object->getAll("a"));
    }

    /**
     * @test
     */
    public function testConstructor()
    {
        $this->object = new \Yana\Util\Xml\Object("Test");
        $this->assertSame("Test", $this->object->getPcData());
    }

    /**
     * @test
     */
    public function testGetIterator()
    {
        $this->object->a = "1";
        $iterator = $this->object->getIterator();
        $expected = new \Yana\Core\GenericCollection();
        $expected["a"] = "1";
        $this->assertEquals($expected, $iterator);        
    }
}
