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

namespace Yana\Db\Helpers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ValueSanitizerWorkerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Helpers\ValueSanitizerWorker
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
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
    public function testAsArray()
    {
        $value = array(1, 2, 3);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asArray());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsArrayInvalidValueException()
    {
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker(1);
        $this->object->asArray();
    }

    /**
     * @test
     */
    public function testAsBool()
    {
        $value = "yes";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertTrue($this->object->asBool());
    }

    /**
     * @test
     */
    public function testAsBoolTrue()
    {
        $value = true;
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertTrue($this->object->asBool());
    }

    /**
     * @test
     */
    public function testAsBool1()
    {
        $value = 1;
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertTrue($this->object->asBool());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsBoolInvalidValueException()
    {
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker(3);
        $this->object->asBool();
    }

    /**
     * @test
     */
    public function testAsColor()
    {
        $value = '#012345';
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asColor());
    }

    /**
     * @test
     */
    public function testAsColor1()
    {
        $value = '#ffffff';
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame(\strtoupper($value), $this->object->asColor());
    }

    /**
     * @test
     */
    public function testAsColor2()
    {
        $value = '#000000';
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asColor());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsColorInvalidValueException()
    {
        $value = ' 123456';
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asColor();
    }

    /**
     * @test
     */
    public function testAsDateString()
    {
        $value = array(
            'month' => '1',
            'day' => '30',
            'year' => '2000'
        );
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame('2000-01-30', $this->object->asDateString());
    }

    /**
     * @test
     */
    public function testAsDateString1()
    {
        $value = '2000-01-30';
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asDateString());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsDateStringInvalidValueException()
    {
        $value = '2000/01/30';
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asDateString();
    }

    /**
     * @test
     */
    public function testAsEnumeration()
    {
        $value = 'Test';
        $enumerationItems = array($value);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asEnumeration($enumerationItems));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsEnumerationInvalidValueException()
    {
        $value = 1;
        $enumerationItems = array(2);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asEnumeration($enumerationItems);
    }

    /**
     * @test
     */
    public function testAsFileId()
    {
        $value = array();
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertNull($this->object->asFileId());
    }

    /**
     * @test
     */
    public function testAsFileIdObject()
    {
        $value = new \Yana\Http\Uploads\File("name", "mimeType", "temporaryPath", 1, 0);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asFileId();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotFoundException
     */
    public function testAsFileIdObjectNotFoundException()
    {
        $value = new \Yana\Http\Uploads\File("name", "mimeType", "temporaryPath", 1, \UPLOAD_ERR_NO_FILE);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertNull($this->object->asFileId());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\SizeException
     */
    public function testAsFileIdObjectSizeException()
    {
        $value = new \Yana\Http\Uploads\File("name", "mimeType", "temporaryPath", 2, 0);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertNull($this->object->asFileId(1));
    }

    /**
     * @test
     */
    public function testAsFileIdString()
    {
        $value = "File Name.txt";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame("Name", $this->object->asFileId());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\DeletedException
     */
    public function testAsFileIdDeletedException()
    {
        $value = "1";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asFileId();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotFoundException
     */
    public function testAsFileIdNotFoundException()
    {
        $value = array("error" => \UPLOAD_ERR_NO_FILE);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asFileId();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\SizeException
     */
    public function testAsFileIdSizeException()
    {
        $value = array('size' => 2);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asFileId(1);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsFileIdInvalidValueException()
    {
        $value = 1;
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asFileId();
    }

    /**
     * @test
     */
    public function testAsRangeValue()
    {
        $value = "1";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame(1.0, $this->object->asRangeValue(1.0, 1.0));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsRangeValueInvalidValueException1()
    {
        $value = "0.9999";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asRangeValue(1.0, 1.0);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsRangeValueInvalidValueException2()
    {
        $value = "1.0001";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asRangeValue(1.0, 1.0);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsRangeValueInvalidValueException3()
    {
        $value = "abc";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asRangeValue(1.0, 1.0);
    }

    /**
     * @test
     */
    public function testAsFloat()
    {
        $value = "-9.25";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame(-9.0, $this->object->asFloat(1));
        $this->assertSame(-9.3, $this->object->asFloat(2, 1));
        $this->assertSame(-9.25, $this->object->asFloat(1, 2));
    }

    /**
     * @test
     */
    public function testAsFloatNegative()
    {
        $value = "9.25";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame(9.0, $this->object->asFloat(1, 0, true));
        $this->assertSame(9.3, $this->object->asFloat(2, 1, true));
        $this->assertSame(9.25, $this->object->asFloat(2, 2, true));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsFloatInvalidValueException()
    {
        $value = "-9.25";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asFloat(1, 1, true);
    }

    /**
     * @test
     */
    public function testAsHtmlString()
    {
        $value = "Test<b>Test</b>";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame("Test&lt;b&gt;Test&lt;/b&gt;", $this->object->asHtmlString(0));
        $this->assertSame("Test&lt;b&gt;Test&lt;/b&gt;", $this->object->asHtmlString(27));
        $this->assertSame("Test&", $this->object->asHtmlString(5));
        $this->assertSame("Test", $this->object->asHtmlString(4));
        $this->assertSame("Tes", $this->object->asHtmlString(3));
        $this->assertSame("Te", $this->object->asHtmlString(2));
        $this->assertSame("T", $this->object->asHtmlString(1));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsHtmlStringInvalidValueException()
    {
        $value = 123;
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asHtmlString();
    }

    /**
     * @test
     */
    public function testAsIpAddress()
    {
        $value = "192.168.0.1";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asIpAddress());
    }

    /**
     * @test
     */
    public function testAsIpAddressV6()
    {
        $value = "101:0db8:85a3:08d3:1319:8a2e:0370:7344";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asIpAddress());
    }

    /**
     * @test
     */
    public function testAsIpAddressV62()
    {
        $value = "101::1";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asIpAddress());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsIpAddressInvalidValueException()
    {
        $value = "123";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asIpAddress();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsIpAddressInvalidValueException2()
    {
        $value = "0.1.2.3";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asIpAddress();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsIpAddressInvalidValueException3()
    {
        $value = "127.0.0.1";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asIpAddress();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsIpAddressInvalidValueException4()
    {
        $value = "::1";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asIpAddress();
    }

    /**
     * @test
     */
    public function testAsInteger()
    {
        $value = "-123";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame((int) $value, $this->object->asInteger());
        $this->assertSame((int) $value, $this->object->asInteger(4));
        $this->assertSame((int) $value, $this->object->asInteger(3));
    }

    /**
     * @test
     */
    public function testAsInteger2()
    {
        $value = "123";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame((int) $value, $this->object->asInteger(0, true));
        $this->assertSame((int) $value, $this->object->asInteger(4, true));
        $this->assertSame((int) $value, $this->object->asInteger(3, true));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsIntegerInvalidValueException()
    {
        $value = "abc";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asInteger();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsIntegerInvalidValueException2()
    {
        $value = "123";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asInteger(2);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsIntegerInvalidValueException3()
    {
        $value = "-123";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asInteger(3, true);
    }

    /**
     * @test
     */
    public function testAsListOfValues()
    {
        $value = array("a" => 1, "b" => 2);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertEquals(\array_values($value), $this->object->asListOfValues());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsListOfValuesInvalidValueException()
    {
        $value = 1;
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asListOfValues();
    }

    /**
     * @test
     */
    public function testAsMailAddress()
    {
        $value = 'a@b.c';
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asMailAddress());
        $this->assertSame($value, $this->object->asMailAddress(5));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsMailAddressInvalidValueException()
    {
        $value = 'a@b.c';
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asMailAddress(4));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsMailAddressInvalidValueException2()
    {
        $value = 'abc';
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asMailAddress());
    }

    /**
     * @test
     */
    public function testAsSetOfEnumerationItems()
    {
        $value = array(1, 2);
        $enumerationItems = array(0, 1, 2, 3);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asSetOfEnumerationItems($enumerationItems));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsSetOfEnumerationItemsInvalidValueException()
    {
        $value = array(1, 4);
        $enumerationItems = array(0, 1, 2, 3);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asSetOfEnumerationItems($enumerationItems);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsSetOfEnumerationItemsInvalidValueException1()
    {
        $value = 123;
        $enumerationItems = array(0, 1, 2, 3);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asSetOfEnumerationItems($enumerationItems);
    }

    /**
     * @test
     */
    public function testAsPassword()
    {
        $value = "Password1";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame(md5($value), $this->object->asPassword());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsPasswordInvalidValueException()
    {
        $value = 1;
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asPassword();
    }

    /**
     * @test
     */
    public function testAsString()
    {
        $value = "Test\nTest";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame("Test Test", $this->object->asString());
        $this->assertSame("Test", $this->object->asString(4));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsStringInvalidValueException()
    {
        $value = 1;
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asString();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsTextInvalidValueException()
    {
        $value = 1;
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asText();
    }

    /**
     * @test
     */
    public function testAsText()
    {
        $value = "Test\nTest";
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame("Test[br]Test", $this->object->asText());
        $this->assertSame("Test", $this->object->asText(4));
    }

    /**
     * @test
     */
    public function testAsTimeString()
    {
        $value = array(
            'month' => '1',
            'day' => '30',
            'year' => '2000',
            'hour' => '23',
            'minute' => '45'
        );
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertStringStartsWith('2000-01-30 23:45:00', $this->object->asTimeString());
    }

    /**
     * @test
     */
    public function testAsTimeString2()
    {
        $value = '2000-01-30 23:45:00';
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asTimeString());
    }

    /**
     * @test
     */
    public function testAsTimeString3()
    {
        $value = array(
            'month' => '1',
            'day' => '30',
            'year' => '2000',
            'hour' => '24',
            'minute' => '59'
        );
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame('2000-01-31 00:59:00', $this->object->asTimeString());
    }

    /**
     * @test
     */
    public function testAsTimeString4()
    {
        $value = mktime(0, 0, 0, 1, 30, 2000);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame('2000-01-30 00:00:00', $this->object->asTimeString());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsTimeStringInvalidValueException()
    {
        $value = 'abc';
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asTimeString();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsTimeStringInvalidValueException2()
    {
        $value = '2000-01-30 23:45:00+00:00';
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asTimeString();
    }

    /**
     * @test
     */
    public function testAsTimestamp()
    {
        $value = mktime(0, 0, 0, 1, 30, 2000);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asTimeStamp());
    }

    /**
     * @test
     */
    public function testAsTimestamp2()
    {
        $value = array(
            'month' => '1',
            'day' => '30',
            'year' => '2000',
            'hour' => '23',
            'minute' => '45'
        );
        $expected = mktime(23, 45, 0, 1, 30, 2000);
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($expected, $this->object->asTimeStamp());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsTimestampInvalidValueException()
    {
        $value = array(
            'month' => '1',
            'day' => '30',
            'year' => '2000',
            'hour' => '23',
        );
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asTimeStamp();
    }

    /**
     * @test
     */
    public function testAsUrl()
    {
        $value = 'https://yanaframework.net/';
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->assertSame($value, $this->object->asUrl());
        $this->assertSame(substr($value, 0, 26), $this->object->asUrl(26));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\InvalidValueException
     */
    public function testAsUrlInvalidValueException()
    {
        $value = 'mooooo';
        $this->object = new \Yana\Db\Helpers\ValueSanitizerWorker($value);
        $this->object->asUrl(26);
    }

}
