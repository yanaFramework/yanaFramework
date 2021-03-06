<?php
/**
 * YANA library
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

namespace Yana\Core\Sessions;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class NullWrapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Sessions\NullWrapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Core\Sessions\NullWrapper();
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
    public function testOffsetExists()
    {
        $this->assertFalse($this->object->offsetExists(0));
        $this->object->offsetSet(0, 0);
        $this->assertTrue($this->object->offsetExists(0));
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $this->assertNull($this->object->offsetGet(0));
        $this->object->offsetSet(0, 0);
        $this->assertSame(0, $this->object->offsetGet(0));
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $this->assertFalse($this->object->offsetExists(0));
        $this->object->offsetSet(null, 1);
        $this->object->offsetSet(null, 'a');
        $this->object->offsetSet(12, 3.5);
        $this->assertTrue($this->object->offsetExists(0));
        $this->assertTrue($this->object->offsetExists(1));
        $this->assertTrue($this->object->offsetExists(12));
        $this->assertSame(1, $this->object->offsetGet(0));
        $this->assertSame('a', $this->object->offsetGet(1));
        $this->assertSame(3.5, $this->object->offsetGet(12));
    }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $this->assertFalse($this->object->offsetExists(1));
        $this->object->offsetSet(1, 1);
        $this->assertTrue($this->object->offsetExists(1));
        $this->object->offsetUnset(1);
        $this->assertFalse($this->object->offsetExists(1));
        $this->object->offsetUnset(1, 'must not throw exception when deleting non-existing index');
    }

    /**
     * @test
     */
    public function testCount()
    {
        $this->assertSame(0, $this->object->count());
        $this->object->offsetSet(null, 1);
        $this->assertSame(1, $this->object->count());
        $this->object->offsetSet(null, 'a');
        $this->assertSame(2, $this->object->count());
        $this->object->offsetSet(12, 3.5);
        $this->assertSame(3, $this->object->count());
        $this->object->offsetUnset(1);
        $this->object->offsetUnset(1);
        $this->assertSame(2, $this->object->count());
        $this->object->offsetUnset(10);
        $this->assertSame(2, $this->object->count());
    }

    /**
     * @test
     */
    public function testGetId()
    {
        $this->assertSame("", $this->object->getId());
    }

    /**
     * @test
     */
    public function testSetId()
    {
        $this->assertSame("Test", $this->object->setId('Test')->getId());
    }

    /**
     * @test
     */
    public function testUnsetAll()
    {
        $this->object->offsetSet(null, 1);
        $this->object->offsetSet(null, 'a');
        $this->object->offsetSet(12, 3.5);
        $this->assertCount(3, $this->object);
        $this->object->unsetAll();
        $this->assertCount(0, $this->object);
    }

    /**
     * @test
     */
    public function testRegenerateId()
    {
        $this->object->offsetSet(null, 1);
        $this->object->offsetSet(null, 'a');
        $this->object->offsetSet(12, 3.5);
        $this->assertSame("", $this->object->setId('Test')->regenerateId()->getId());
        $this->assertCount(0, $this->object);
        $this->object->offsetSet(null, 1);
        $this->object->offsetSet(null, 'a');
        $this->object->offsetSet(12, 3.5);
        $this->assertSame("", $this->object->regenerateId(true)->getId());
        $this->assertCount(0, $this->object);
    }

    /**
     * @test
     */
    public function testRegenerateIdDeleteFile()
    {
        $this->object->offsetSet(null, 1);
        $this->object->offsetSet(null, 'a');
        $this->object->offsetSet(12, 3.5);
        $this->assertSame("", $this->object->regenerateId(true)->getId());
        $this->assertCount(0, $this->object);
    }

    /**
     * @test
     */
    public function testGetName()
    {
        $this->assertSame("", $this->object->getName());
    }

    /**
     * @test
     */
    public function testSetName()
    {
        $this->assertSame("Test", $this->object->setName("Test")->getName());
    }

    /**
     * @test
     */
    public function testStart()
    {
        $this->assertTrue($this->object->start());
        $this->assertTrue($this->object->start());
    }

    /**
     * @test
     */
    public function testStop()
    {
        $this->assertNull($this->object->stop());
    }

    /**
     * @test
     */
    public function testDestroy()
    {
        $this->assertTrue($this->object->destroy());
    }

    /**
     * @test
     */
    public function test__toString()
    {
        $data = array(1, 'a', 12 => 3.5);
        $object = new \Yana\Core\Sessions\NullWrapper($data);
        $this->assertSame(serialize($data), $object->__toString());
    }

    /**
     * @test
     */
    public function testFromString()
    {
        $data = array(1, 'a', 12 => 3.5);
        $object = new \Yana\Core\Sessions\NullWrapper($data);
        $object2 = new \Yana\Core\Sessions\NullWrapper();
        $object2->fromString($object->__toString());#
        $this->assertEquals($object, $object2);
    }

}
