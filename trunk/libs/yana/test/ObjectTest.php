<?php
/**
 * PHPUnit test-case: Object
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

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';


/**
 * Test class for Object
 *
 * @package  test
 */
class ObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Object
     * @access protected
     */
    protected $object;
    
    /**
     * @ignore
     */
    public function __construct()
    {
        // intentionally left blank
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->object = new Object();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        // intentionally left blank
    }

    /**
     * __toString
     *
     * @test
     */
    public function testToString()
    {
        $string = $this->object->__toString();
        $this->assertType('string', $string, 'value is not from type string');
        $string2 = (string) $this->object;
        $this->assertEquals($string, $string2);
        $this->assertEquals("Instance of '" . get_class($this->object). "'.", $string);
    }

    /**
     * Clone Object
     *
     * @test
     */
    public function testCloneObject()
    {
        $cloneObject = clone $this->object;
        $this->assertEquals($cloneObject, $this->object);
    }

    /**
     * __clone
     *
     * @test
     */
    public function test__clone()
    {
        $cloneObject = clone($this->object);
        $this->assertEquals($cloneObject, $this->object, 'there are two different objects');
        $this->assertFalse($cloneObject === $this->object, 'copy should not be identical');
    }

    /**
     * get class
     *
     * @test
     */
    public function testGetClass()
    {
        $getClass = $this->object->getClass();
        $this->assertType('string', $getClass, 'asserft faield, the value is not from type string');
        // expected Object as a string
        $this->assertEquals('Object', $getClass, ' the values should be equal');
    }

    /**
     * equals
     *
     * @test
     */
    public function testEquals()
    {
        $clone = clone $this->object;
        $this->assertEquals($this->object, $clone, 'there are two different objects');
        $equals = $this->object->equals($clone);
        // expected the same object
        $this->assertTrue($equals, 'there are two different objects');

        $this->assertEquals($this->object, $clone, 'the two objects are equal');
        // expected false 
        $this->assertFalse($clone === $this->object, 'that two cant be identical');
    }
}
?>