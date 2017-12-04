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
class ConverterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testToEmpty()
    {
        $xmlSource = new \SimpleXMLElement('<root>bar</root>');
        $this->assertSame("bar", \Yana\Util\Xml\Converter::convertXmlToAssociativeArray($xmlSource));
        $this->assertSame("bar", \Yana\Util\Xml\Converter::convertXmlToNumericArray($xmlSource));
        $this->assertEquals(new \Yana\Util\Xml\Object(), \Yana\Util\Xml\Converter::convertXmlToObject($xmlSource));
    }

    /**
     * @test
     */
    public function testConvertXmlToNumericArray()
    {
        $xmlSource = new \SimpleXMLElement('<root><child1>1</child1></root>');
        $array = \Yana\Util\Xml\Converter::convertXmlToNumericArray($xmlSource);
        $this->assertInternalType('array', $array);
        $expected = array("#tag" => "root", array("#tag" => "child1", "#pcdata" => "1"));
        $this->assertEquals($expected, $array);
    }

    /**
     * @test
     */
    public function testConvertXmlToNumericArray2()
    {
        $xmlSource = new \SimpleXMLElement('<root><child1>1</child1><child2 a="2"><child3 a="4" b="5">3</child3>' .
            '<child4 a="6">7</child4><child4 a="8">9</child4><child4 a="10">11</child4></child2></root>');
        $array = \Yana\Util\Xml\Converter::convertXmlToNumericArray($xmlSource);
        $expected = array(
            "#tag" => "root",
            array('#tag' => "child1", "#pcdata" => "1"),
            array(
                "#tag" => "child2",
                "@a" => "2",
                array(
                    "#tag" => "child3",
                    "@a" => "4",
                    "@b" => "5",
                    "#pcdata" => "3",
                ),
                array(
                    "#tag" => "child4",
                    "@a" => "6", "#pcdata" => "7"
                ),
                array(
                    "#tag" => "child4",
                    "@a" => "8", "#pcdata" => "9"
                ),
                array(
                    "#tag" => "child4",
                    "@a" => "10", "#pcdata" => "11"
                )
            )
        );
        $this->assertEquals($expected, $array);
    }

    /**
     * @test
     */
    public function testConvertXmlToAssociativeArray()
    {
        $xmlSource = new \SimpleXMLElement('<root><child1>1</child1></root>');
        $array = \Yana\Util\Xml\Converter::convertXmlToAssociativeArray($xmlSource);
        $this->assertInternalType('array', $array);
        $expected = array("child1" => "1");
        $this->assertEquals($expected, $array);
    }

    /**
     * @test
     */
    public function testToArrayAsAssociativeArray2()
    {
        $xmlSource = new \SimpleXMLElement('<root><child1>1</child1><child2 a="2"><child3 a="4" b="5">3</child3>' .
            '<child4 a="6">7</child4><child4 a="8">9</child4><child4 a="10">11</child4></child2></root>');
        $array = \Yana\Util\Xml\Converter::convertXmlToAssociativeArray($xmlSource);
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
    public function testConvertXmlToObject()
    {
        $xmlSource = new \SimpleXMLElement('<root><child1>1</child1><child2 a="2"><child3 a="4" b="5">3</child3>' .
            '<child4 a="6">7</child4><child4 a="8">9</child4><child4 a="10">11</child4></child2></root>');
        $object = \Yana\Util\Xml\Converter::convertXmlToObject($xmlSource);
        $this->assertTrue($object instanceof \Yana\Util\Xml\IsObject);
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
    public function testConvertObjectToAssociativeArray()
    {
        $object = new \Yana\Util\Xml\Object();
        $object->child1 = "1";

        $child2 = new \Yana\Util\Xml\Object();
        $child2->addAttribute("a", "2");
        $object->child2 = $child2;

        $child3 = new \Yana\Util\Xml\Object();
        $child3->setPcData("3")->addAttribute("a", "4")->addAttribute("b", "5");
        $child2->child3 = $child3;

        $child41 = new \Yana\Util\Xml\Object();
        $child41->addAttribute("a", "6")->setPcData("7");
        $child42 = new \Yana\Util\Xml\Object();
        $child42->addAttribute("a", "8")->setPcData("9");
        $child43 = new \Yana\Util\Xml\Object();
        $child43->addAttribute("a", "10")->setPcData("11");
        $child2->child4 = array($child41, $child42, $child43);
        $array = \Yana\Util\Xml\Converter::convertObjectToAssociativeArray($object);
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
}
