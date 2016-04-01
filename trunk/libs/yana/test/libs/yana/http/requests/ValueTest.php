<?php
/**
 * PHPUnit test-case: DbInfoColumn
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

namespace Yana\Http\Requests;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ValueTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Http\Requests\Value
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = null;
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
    public function testIs()
    {
        $value = "ßTest123\n";
        $object = new \Yana\Http\Requests\Value($value);
        $this->assertTrue($object->is($value));
        $this->assertFalse($object->is(trim($value)));
        $number = 123.4;
        $objectNumber = new \Yana\Http\Requests\Value((string) $number);
        $this->assertTrue($objectNumber->is($number));
    }

    /**
     * @test
     */
    public function testIsNotNull()
    {
        $value = "ßTest123\n";
        $object = new \Yana\Http\Requests\Value($value);
        $this->assertTrue($object->isNotNull());
        $objectNull = new \Yana\Http\Requests\Value(null);
        $this->assertFalse($objectNull->isNotNull());
    }

    /**
     * @test
     */
    public function testIsNull()
    {
        $object = new \Yana\Http\Requests\Value(null);
        $this->assertTrue($object->isNull());
        $value = "ßTest123\n";
        $objectNull = new \Yana\Http\Requests\Value($value);
        $this->assertFalse($objectNull->isNull());
    }

    /**
     * @test
     */
    public function testAsUnsafeString()
    {
        $value = "ß<Test a=\"b\">123\n";
        $object = new \Yana\Http\Requests\Value($value);
        $this->assertEquals($value, $object->asUnsafeString($value));
    }

    /**
     * @test
     */
    public function testAsSafeString()
    {
        $value = "ß<Test a=\"b\">123\n";
        $valueSafe = "ß123\n";
        $object = new \Yana\Http\Requests\Value($value);
        $this->assertNotEquals($value, $object->asSafeString($value));
        $this->assertEquals($valueSafe, $object->asSafeString($value));
    }

    /**
     * @test
     */
    public function testAsOneLineString()
    {
        $value = "ß<Test a=\"b\">1234567890123456789012345678901234567890123456789012345678901234567890123456789\n";
        $valueSafe = "ß1234567890123456789012345678901234567890123456789012345678901234567890123456789";
        $object = new \Yana\Http\Requests\Value($value);
        $this->assertNotEquals($value, $object->asOneLineString($value));
        $this->assertEquals($valueSafe, $object->asOneLineString($value));
    }

    /**
     * @test
     */
    public function testAsOutputString()
    {
        $value = "ß<Test a=\"b\">1234567890123456789012345678901234567890123456789012345678901234567890123456789\n";
        $valueSafe = "ß&lt;Test a=&quot;b&quot;&gt;12345678901234567890123456789012345678901234567890123456789012345678901234[wbr]56789[br]";
        $object = new \Yana\Http\Requests\Value($value);
        $this->assertNotEquals($value, $object->asOutputString($value));
        $this->assertEquals($valueSafe, $object->asOutputString($value));
    }

    /**
     * @test
     */
    public function testAsFloat()
    {
        $value = "123.4";
        $object = new \Yana\Http\Requests\Value($value);
        $this->assertEquals(\floatval($value), $object->asFloat());
        $valueInvalid = "abc";
        $objectInvalid = new \Yana\Http\Requests\Value($valueInvalid);
        $this->assertEquals(0.0, $objectInvalid->asFloat());
    }

    /**
     * @test
     */
    public function testAsInt()
    {
        $value = "123.4";
        $object = new \Yana\Http\Requests\Value($value);
        $this->assertEquals(\intval($value), $object->asInt());
        $valueInvalid = "abc";
        $objectInvalid = new \Yana\Http\Requests\Value($valueInvalid);
        $this->assertEquals(0, $objectInvalid->asInt());
    }

    /**
     * @test
     */
    public function testAsBool()
    {
        $object1 = new \Yana\Http\Requests\Value("1");
        $object2 = new \Yana\Http\Requests\Value("true");
        $object3 = new \Yana\Http\Requests\Value("yes");
        $object4 = new \Yana\Http\Requests\Value("-1");
        $object5 = new \Yana\Http\Requests\Value(null);
        $object6 = new \Yana\Http\Requests\Value("");
        $object7 = new \Yana\Http\Requests\Value("truely");
        $this->assertTrue($object1->asBool());
        $this->assertTrue($object2->asBool());
        $this->assertTrue($object3->asBool());
        $this->assertFalse($object4->asBool());
        $this->assertFalse($object5->asBool());
        $this->assertFalse($object6->asBool());
        $this->assertFalse($object7->asBool());
    }

    /**
     * @test
     */
    public function testAsBic()
    {
        $object1 = new \Yana\Http\Requests\Value("XXXXXXXX012");
        $object2 = new \Yana\Http\Requests\Value("XXXXXXXXX0X");
        $object3 = new \Yana\Http\Requests\Value(null);
        $object4 = new \Yana\Http\Requests\Value("");
        $this->assertEquals("XXXXXXXX012", $object1->asBic());
        $this->assertNull($object2->asBic());
        $this->assertNull($object3->asBic());
        $this->assertNull($object4->asBic());
    }

    /**
     * @test
     */
    public function testAsIban()
    {
        $object1 = new \Yana\Http\Requests\Value("MT84MALT011000012345MTLCAST001S");
        $object2 = new \Yana\Http\Requests\Value("MT84MALT011000012345MTLCAST001A");
        $object3 = new \Yana\Http\Requests\Value(null);
        $object4 = new \Yana\Http\Requests\Value("");
        $this->assertEquals("MT84MALT011000012345MTLCAST001S", $object1->asIban());
        $this->assertNull($object2->asIban());
        $this->assertNull($object3->asIban());
        $this->assertNull($object4->asIban());
    }

    /**
     * @test
     */
    public function testAsIp()
    {
        $object1 = new \Yana\Http\Requests\Value("127.0.0.1");
        $object2 = new \Yana\Http\Requests\Value("2001:db8::1");
        $object3 = new \Yana\Http\Requests\Value("::1");
        $object4 = new \Yana\Http\Requests\Value(null);
        $object5 = new \Yana\Http\Requests\Value("2001::1x");
        $object6 = new \Yana\Http\Requests\Value("2001:db8:0:0:0:0:2:12345");
        $object7 = new \Yana\Http\Requests\Value("");
        $this->assertEquals("127.0.0.1", $object1->asIp());
        $this->assertEquals("2001:db8::1", $object2->asIp());
        $this->assertEquals("::1", $object3->asIp());
        $this->assertNull($object4->asIp());
        $this->assertNull($object5->asIp());
        $this->assertNull($object6->asIp());
        $this->assertNull($object7->asIp());
    }

    /**
     * @test
     */
    public function testAsMail()
    {
        $object1 = new \Yana\Http\Requests\Value("a@b.c");
        $object2 = new \Yana\Http\Requests\Value("a@[127.0.0.1]");
        $object3 = new \Yana\Http\Requests\Value("a@[IPv6:::1]");
        $object4 = new \Yana\Http\Requests\Value(null);
        $object5 = new \Yana\Http\Requests\Value("abc");
        $object6 = new \Yana\Http\Requests\Value("a b@c.d");
        $object7 = new \Yana\Http\Requests\Value("");
        $this->assertEquals("a@b.c", $object1->asMail());
        $this->assertEquals("a@[127.0.0.1]", $object2->asMail());
        $this->assertEquals("a@[IPv6:::1]", $object3->asMail());
        $this->assertNull($object4->asMail());
        $this->assertNull($object5->asMail());
        $this->assertNull($object6->asMail());
        $this->assertNull($object7->asMail());
    }

    /**
     * @test
     */
    public function testAsUrl()
    {
        $object1 = new \Yana\Http\Requests\Value("abcß");
        $object2 = new \Yana\Http\Requests\Value("http://abc.de");
        $object3 = new \Yana\Http\Requests\Value("http://abc.de/test?a=b#c1234");
        $object4 = new \Yana\Http\Requests\Value("http\n://abc .\nde");
        $object5 = new \Yana\Http\Requests\Value(null);
        $object6 = new \Yana\Http\Requests\Value("file:///abc");
        $object7 = new \Yana\Http\Requests\Value("javascript://test");
        $object8 = new \Yana\Http\Requests\Value("<b>abc</b>");
        $this->assertEquals("http://abc", $object1->asUrl());
        $this->assertEquals("http://abc.de", $object2->asUrl());
        $this->assertEquals("http://abc.de/test?a=b#c1234", $object3->asUrl());
        $this->assertEquals("http://abc.de", $object4->asUrl());
        $this->assertNull($object5->asUrl());
        $this->assertNull($object6->asUrl());
        $this->assertNull($object7->asUrl());
        $this->assertNull($object8->asUrl());
    }

    /**
     * @test
     */
    public function testAsUnsafeArray()
    {
        $object1 = new \Yana\Http\Requests\Value(array('1', 'a', '#äß §'));
        $object2 = new \Yana\Http\Requests\Value(array());
        $object3 = new \Yana\Http\Requests\Value("abcß");
        $object4 = new \Yana\Http\Requests\Value(null);
        $this->assertEquals(array('1', 'a', '#äß §'), $object1->asUnsafeArray());
        $this->assertEquals(array(), $object2->asUnsafeArray());
        $this->assertEquals(array("abcß"), $object3->asUnsafeArray());
        $this->assertEquals(array(), $object4->asUnsafeArray());
    }

    /**
     * @test
     */
    public function testAll()
    {
        $object1 = new \Yana\Http\Requests\Value(array('1', 'a', '#äß §'));
        $object2 = new \Yana\Http\Requests\Value(array());
        $object3 = new \Yana\Http\Requests\Value("abcß");
        $object4 = new \Yana\Http\Requests\Value(null);
        $this->assertTrue($object1->all() instanceof \Yana\Http\Requests\ValueWrapper);
        $this->assertTrue($object2->all() instanceof \Yana\Http\Requests\ValueWrapper);
        $this->assertTrue($object3->all() instanceof \Yana\Http\Requests\ValueWrapper);
        $this->assertTrue($object4->all() instanceof \Yana\Http\Requests\ValueWrapper);
    }

    /**
     * @test
     */
    public function testIsScalar()
    {
        $object1 = new \Yana\Http\Requests\Value(array('1', 'a', '#äß §'));
        $object2 = new \Yana\Http\Requests\Value("abcß");
        $object3 = new \Yana\Http\Requests\Value(null);
        $this->assertFalse($object1->isScalar());
        $this->assertTrue($object2->isScalar());
        $this->assertFalse($object3->isScalar());
    }

    /**
     * @test
     */
    public function testIsArray()
    {
        $object1 = new \Yana\Http\Requests\Value(array());
        $object2 = new \Yana\Http\Requests\Value("");
        $object3 = new \Yana\Http\Requests\Value(null);
        $this->assertTrue($object1->isArray());
        $this->assertFalse($object2->isArray());
        $this->assertFalse($object3->isArray());
    }

    /**
     * @test
     */
    public function testIsEmpty()
    {
        $object1 = new \Yana\Http\Requests\Value(array());
        $object2 = new \Yana\Http\Requests\Value("");
        $object3 = new \Yana\Http\Requests\Value(null);
        $object4 = new \Yana\Http\Requests\Value("0");
        $object5 = new \Yana\Http\Requests\Value(array("1"));
        $object6 = new \Yana\Http\Requests\Value("1");
        $this->assertTrue($object1->isEmpty());
        $this->assertTrue($object2->isEmpty());
        $this->assertTrue($object3->isEmpty());
        $this->assertTrue($object4->isEmpty());
        $this->assertFalse($object5->isEmpty());
        $this->assertFalse($object6->isEmpty());
    }

    /**
     * @test
     */
    public function testIsNumeric()
    {
        $object1 = new \Yana\Http\Requests\Value("1");
        $object2 = new \Yana\Http\Requests\Value("0");
        $object3 = new \Yana\Http\Requests\Value("1.0");
        $object4 = new \Yana\Http\Requests\Value("1.2");
        $object5 = new \Yana\Http\Requests\Value("abc123");
        $object6 = new \Yana\Http\Requests\Value(null);
        $this->assertTrue($object1->isNumeric());
        $this->assertTrue($object2->isNumeric());
        $this->assertTrue($object3->isNumeric());
        $this->assertTrue($object4->isNumeric());
        $this->assertFalse($object5->isNumeric());
        $this->assertFalse($object6->isNumeric());
    }

    /**
     * @test
     */
    public function testIsInt()
    {
        $object1 = new \Yana\Http\Requests\Value("-1");
        $object2 = new \Yana\Http\Requests\Value("+0");
        $object3 = new \Yana\Http\Requests\Value("1.0");
        $object4 = new \Yana\Http\Requests\Value("1.2");
        $object5 = new \Yana\Http\Requests\Value("abc123");
        $object6 = new \Yana\Http\Requests\Value(null);
        $this->assertTrue($object1->isInt());
        $this->assertTrue($object2->isInt());
        $this->assertFalse($object3->isInt());
        $this->assertFalse($object4->isInt());
        $this->assertFalse($object5->isInt());
        $this->assertFalse($object6->isInt());
    }

    /**
     * @test
     */
    public function testIsFloat()
    {
        $object1 = new \Yana\Http\Requests\Value("-1");
        $object2 = new \Yana\Http\Requests\Value("+0");
        $object3 = new \Yana\Http\Requests\Value("-1.0");
        $object4 = new \Yana\Http\Requests\Value("+1.2");
        $object5 = new \Yana\Http\Requests\Value("abc123");
        $object6 = new \Yana\Http\Requests\Value(null);
        $this->assertTrue($object1->isFloat());
        $this->assertTrue($object2->isFloat());
        $this->assertTrue($object3->isFloat());
        $this->assertTrue($object4->isFloat());
        $this->assertFalse($object5->isFloat());
        $this->assertFalse($object6->isFloat());
    }

    /**
     * @test
     */
    public function testIsString()
    {
        $object1 = new \Yana\Http\Requests\Value("a #äß\n");
        $object2 = new \Yana\Http\Requests\Value("0");
        $object3 = new \Yana\Http\Requests\Value("");
        $object4 = new \Yana\Http\Requests\Value(null);
        $object5 = new \Yana\Http\Requests\Value(array());
        $this->assertTrue($object1->isString());
        $this->assertTrue($object2->isString());
        $this->assertTrue($object3->isString());
        $this->assertFalse($object4->isString());
        $this->assertFalse($object5->isString());
    }

    /**
     * @test
     */
    public function testIsBool()
    {
        $object1 = new \Yana\Http\Requests\Value("true");
        $object2 = new \Yana\Http\Requests\Value("0");
        $object3 = new \Yana\Http\Requests\Value("");
        $object4 = new \Yana\Http\Requests\Value("abc");
        $object5 = new \Yana\Http\Requests\Value(array());
        $object6 = new \Yana\Http\Requests\Value(null);
        $this->assertTrue($object1->isBool());
        $this->assertTrue($object2->isBool());
        $this->assertTrue($object3->isBool());
        $this->assertFalse($object4->isBool());
        $this->assertFalse($object5->isBool());
        $this->assertFalse($object6->isBool());
    }

    /**
     * @test
     */
    public function testIsBic()
    {
        $object1 = new \Yana\Http\Requests\Value("XXXXXXXX012");
        $object2 = new \Yana\Http\Requests\Value("XXXXXXXXX0X");
        $object3 = new \Yana\Http\Requests\Value(null);
        $object4 = new \Yana\Http\Requests\Value("");
        $this->assertTrue($object1->isBic());
        $this->assertFalse($object2->isBic());
        $this->assertFalse($object3->isBic());
        $this->assertFalse($object4->isBic());
    }

    /**
     * @test
     */
    public function testIsIban()
    {
        $object1 = new \Yana\Http\Requests\Value("MT84MALT011000012345MTLCAST001S");
        $object2 = new \Yana\Http\Requests\Value("MT84MALT011000012345MTLCAST001A");
        $object3 = new \Yana\Http\Requests\Value(null);
        $object4 = new \Yana\Http\Requests\Value("");
        $this->assertTrue($object1->isIban());
        $this->assertFalse($object2->isIban());
        $this->assertFalse($object3->isIban());
        $this->assertFalse($object4->isIban());
    }

    /**
     * @test
     */
    public function testIsIp()
    {
        $object1 = new \Yana\Http\Requests\Value("127.0.0.1");
        $object2 = new \Yana\Http\Requests\Value("2001:db8::1");
        $object3 = new \Yana\Http\Requests\Value("::1");
        $object4 = new \Yana\Http\Requests\Value(null);
        $object5 = new \Yana\Http\Requests\Value("2001::1x");
        $object6 = new \Yana\Http\Requests\Value("2001:db8:0:0:0:0:2:12345");
        $object7 = new \Yana\Http\Requests\Value("");
        $this->assertTrue($object1->isIp());
        $this->assertTrue($object2->isIp());
        $this->assertTrue($object3->isIp());
        $this->assertFalse($object4->isIp());
        $this->assertFalse($object5->isIp());
        $this->assertFalse($object6->isIp());
        $this->assertFalse($object7->isIp());
    }

    /**
     * @test
     */
    public function testIsMail()
    {
        $object1 = new \Yana\Http\Requests\Value("a@b.c");
        $object2 = new \Yana\Http\Requests\Value("a@[127.0.0.1]");
        $object3 = new \Yana\Http\Requests\Value("a@[IPv6:::1]");
        $object4 = new \Yana\Http\Requests\Value(null);
        $object5 = new \Yana\Http\Requests\Value("abc");
        $object6 = new \Yana\Http\Requests\Value("a b@c.d");
        $object7 = new \Yana\Http\Requests\Value("");
        $this->assertTrue($object1->isMail());
        $this->assertTrue($object2->isMail());
        $this->assertTrue($object3->isMail());
        $this->assertFalse($object4->isMail());
        $this->assertFalse($object5->isMail());
        $this->assertFalse($object6->isMail());
        $this->assertFalse($object7->isMail());
    }

    /**
     * @test
     */
    public function testIsUrl()
    {
        $object1 = new \Yana\Http\Requests\Value("abcß");
        $object2 = new \Yana\Http\Requests\Value("http://abc.de");
        $object3 = new \Yana\Http\Requests\Value("http://abc.de/test?a=b#c1234");
        $object4 = new \Yana\Http\Requests\Value("http\n://abc .\nde"); // not valid unless previously sanitized to remove line-break
        $object5 = new \Yana\Http\Requests\Value(null);
        $object6 = new \Yana\Http\Requests\Value("file:///abc");
        $object7 = new \Yana\Http\Requests\Value("javascript://test");
        $object8 = new \Yana\Http\Requests\Value("<b>abc</b>");
        $this->assertFalse($object1->isUrl());
        $this->assertTrue($object2->isUrl());
        $this->assertTrue($object3->isUrl());
        $this->assertFalse($object4->isUrl());
        $this->assertFalse($object5->isUrl());
        $this->assertFalse($object6->isUrl());
        $this->assertFalse($object7->isUrl());
        $this->assertFalse($object8->isUrl());
    }

}
