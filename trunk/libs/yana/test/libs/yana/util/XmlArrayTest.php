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

namespace Yana\Util;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../include.php';


/**
 * @package  test
 */
class XmlArrayTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  string
     */
    private $_xmlSource = '<root><child1>1</child1></root>';

    /**
     * @var  \Yana\Util\XmlArray
     */
    private $_object = null;

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        // intentionally left blank
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        
        $this->_object = new \Yana\Util\XmlArray($this->_xmlSource);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        // intentionally left blank
    }

    /**
     * @test
     */
    public function testIsset()
    {
        $this->assertTrue(isset($this->_object->child1));
    }

    /**
     * @test
     */
    public function testToArrayEmpty()
    {
        $xmlSource = '<root>bar</root>';
        $this->_object = new \Yana\Util\XmlArray($xmlSource);
        $array = $this->_object->toArray();
        $this->assertSame("bar", $array);
    }

    /**
     * @test
     */
    public function testToArrayAsNumericArray()
    {
        $array = $this->_object->toArray(true);
        $this->assertInternalType('array', $array, 'assert failed, value is not from type array');
        $expected = array("#tag" => "root", array("#tag" => "child1", "#pcdata" => "1"));
        $this->assertEquals($expected, $array);
    }

    /**
     * @test
     */
    public function testToArrayAsAssociativeArray()
    {
        $array = $this->_object->toArray(false);
        $this->assertInternalType('array', $array);
        $expected = array("child1" => "1");
        $this->assertEquals($expected, $array);
    }

    /**
     * @test
     */
    public function testToArrayAsAssociativeArray2()
    {
        $xmlSource = '<root><child1>1</child1><child2 a="2"><child3 a="4" b="5">3</child3>' .
            '<child4 a="6">7</child4><child4 a="8">9</child4><child4 a="10">11</child4></child2></root>';
        $this->_object = new \Yana\Util\XmlArray($xmlSource);
        $array = $this->_object->toArray();
        $expected = array(
            "child1" => "1",
            "child2" => array(
                "@a" => "2",
                "child3" => array(
                    "@a" => "4",
                    "@b" => "5",
                    "#pcdata" => "3",
                ),
                "child4" => array(
                    array("@a" => "6", "#pcdata" => "7"),
                    array("@a" => "8", "#pcdata" => "9"),
                    array("@a" => "10", "#pcdata" => "11"),
                )
            )
        );
        $this->assertEquals($expected, $array);
    }

    /**
     * @test
     */
    public function testToObject()
    {
        $xmlSource = '<root><child1>1</child1><child2 a="2"><child3 a="4" b="5">3</child3>' .
            '<child4 a="6">7</child4><child4 a="8">9</child4><child4 a="10">11</child4></child2></root>';
        $this->_object = new \Yana\Util\XmlArray($xmlSource);
        $object = $this->_object->toObject();
        $this->assertTrue($object instanceof \Yana\Util\Xml\Object);
        $this->assertObjectHasAttribute('child1', $object);
        $this->assertSame("1", $object->child1);
        $this->assertObjectHasAttribute('child2', $object);
        $this->assertTrue($object->child2 instanceof \Yana\Util\Xml\Object);
        $this->assertObjectHasAttribute('@a', $object->child2);
        $this->assertSame("2", $object->child2->getAttribute('a'));
        $this->assertObjectHasAttribute('child3', $object->child2);
        $this->assertTrue($object->child2->child3 instanceof \Yana\Util\Xml\Object);
        $this->assertObjectHasAttribute('@a', $object->child2->child3);
        $this->assertObjectHasAttribute('@b', $object->child2->child3);
        $this->assertObjectHasAttribute('#pcdata', $object->child2->child3);
        $this->assertSame("4", $object->child2->child3->getAttribute('a'));
        $this->assertSame("5", $object->child2->child3->getAttribute('b'));
        $this->assertSame("3", $object->child2->child3->getPcdata());
        $this->assertSame("3", (string) $object->child2->child3);
        $this->assertObjectHasAttribute('child4', $object->child2);
        $this->assertInternalType('array', $object->child2->child4);
        $this->assertArrayHasKey('0', $object->child2->child4);
        $this->assertArrayHasKey('1', $object->child2->child4);
        $this->assertArrayHasKey('2', $object->child2->child4);
        $this->assertTrue($object->child2->child4[0] instanceof \Yana\Util\Xml\Object);
        $this->assertTrue($object->child2->child4[1] instanceof \Yana\Util\Xml\Object);
        $this->assertTrue($object->child2->child4[2] instanceof \Yana\Util\Xml\Object);
        $this->assertSame("6", $object->child2->child4[0]->getAttribute('a'));
        $this->assertSame("8", $object->child2->child4[1]->getAttribute('a'));
        $this->assertSame("10", $object->child2->child4[2]->getAttribute('a'));
        $this->assertSame("7", (string) $object->child2->child4[0]);
        $this->assertSame("9", (string) $object->child2->child4[1]);
        $this->assertSame("11", (string) $object->child2->child4[2]);
    }

    /**
     * @test
     */
    public function testToObjectEmpty()
    {
        $xmlSource = '<root/>';
        $this->_object = new \Yana\Util\XmlArray($xmlSource);
        $object = $this->_object->toObject();
        $this->assertTrue($object instanceof \Yana\Util\Xml\Object);
        $this->assertEquals(new \Yana\Util\Xml\Object(), $object);
    }

}
