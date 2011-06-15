<?php
/**
 * PHPUnit test-case: Toolbox
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

namespace Yana\Io;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';

/**
 * Test class for Toolbox
 *
 * @package  test
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        // intentionally left blank
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
     * @test
     */
    public function testIntegerValidatorLength()
    {
        // use an integer
        $untaintInput = IntegerValidator::sanitize(60, 1);
        $this->assertType('int', $untaintInput);
        $this->assertEquals($untaintInput, 9, 'the integer value must have 1 digit');

        $untaintInput = IntegerValidator::sanitize(100, 2);
        $this->assertType('int', $untaintInput);
        $this->assertEquals($untaintInput, 99, 'the integer value must have 2 digits');
    }

    /**
     * @test
     */
    public function testIntegerValidatorValidate()
    {
        $this->assertTrue(IntegerValidator::validate(100));
        $this->assertTrue(IntegerValidator::validate("100"));
        $this->assertFalse(IntegerValidator::validate(100.05));
        $this->assertFalse(IntegerValidator::validate(100, 2));
        $this->assertTrue(IntegerValidator::validate(-100, 3));
        $this->assertFalse(IntegerValidator::validate(-100, 3, true));
    }

    /**
     * @test
     */
    public function testFloatValidatorValidate()
    {
        $this->assertTrue(FloatValidator::validate(100));
        $this->assertTrue(FloatValidator::validate(100.05));
        $this->assertTrue(FloatValidator::validate("100.05"));
        $this->assertFalse(FloatValidator::validate("100,05"));
        $this->assertFalse(FloatValidator::validate(100, 2));
        $this->assertTrue(FloatValidator::validate(-100, 3));
        $this->assertTrue(FloatValidator::validate(-100, 3));
        $this->assertFalse(FloatValidator::validate(-100, 3, true));
    }

    /**
     * @test
     */
    public function testIntegerValidatorToInteger()
    {
        $untaintInput = IntegerValidator::sanitize(100.05, 2);
        $this->assertType('int', $untaintInput);
        $this->assertEquals($untaintInput, 99, 'the integer value must have 2 digits');

        $untaintInput = IntegerValidator::sanitize(120.52, 3);
        $this->assertType('int', $untaintInput);
        $this->assertEquals($untaintInput, 121);
    }

    /**
     * @test
     */
    public function testFloatValidatorFixedPrecission()
    {
        // use float
        $untaintInput = FloatValidator::sanitize(-3.1, 1, 0);
        $this->assertEquals(-3.0, $untaintInput, 'the integer value must have 1 digit');

        $untaintInput = FloatValidator::sanitize(0.115, 0, 2);
        $this->assertEquals(.12, $untaintInput, 'the integer value must have 1 digit');

        $untaintInput = FloatValidator::sanitize(-3.5, 1, 0);
        $this->assertEquals(-4, $untaintInput, 'the integer value must have 1 digit');

        $untaintInput = FloatValidator::sanitize("+89,95", 4, 2);
        $this->assertEquals(89.95, $untaintInput, 'the integer value must have 2 digits');

        $untaintInput = FloatValidator::sanitize("-189,959", 0, 2);
        $this->assertEquals(-189.96, $untaintInput, 'the integer value must have 2 digits');

        $untaintInput = FloatValidator::sanitize(-3.33, 0, 2);
        $this->assertEquals(-3.33, $untaintInput, 'the integer value must have 1 digits');

        $untaintInput = FloatValidator::sanitize(-3.33, 0, 1);
        $this->assertEquals(-3.3, $untaintInput, 'the integer value must have 1 digit');
    }

    /**
     * @test
     */
    public function testArrayValidatorToSml()
    {
        $array = array('abc', 'def', 'ghi');
        $untaintInput = ArrayValidator::sanitize($array, 0, ArrayValidator::TO_SML);
        $validate = "<0>abc</0>\n<1>def</1>\n<2>ghi</2>\n";
        $this->assertEquals($validate, $untaintInput);
    }

    /**
     * @test
     */
    public function testArrayValidatorToXml()
    {
        $array = array('abc', 'def', 'ghi');
        $untaintInput = ArrayValidator::sanitize($array, 0, ArrayValidator::TO_XML);
        $validate = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
            "<array id=\"root\">\n\t<string id=\"0\">abc</string>\n\t<string id=\"1\">def</string>\n" .
            "\t<string id=\"2\">ghi</string>\n</array>\n";
        $this->assertEquals($validate, $untaintInput);

    }

    /**
     * @test
     */
    public function testArrayValidatorSanitize()
    {
        $array = array('abc', 'def', 'ghi');
        $untaintInput = ArrayValidator::sanitize($array, 1, ArrayValidator::TO_SML);
        $validate = "<0>abc</0>\n";
        $this->assertEquals($validate, $untaintInput);

    }

    /**
     * @test
     */
    public function testArrayValidatorValidate()
    {
        $array = array('abc', 'def', 'ghi');
        $this->assertTrue(ArrayValidator::validate($array));
        $this->assertFalse(ArrayValidator::validate($array, 2));
        $this->assertFalse(ArrayValidator::validate('invalid'));
    }

    /**
     * @test
     */
    public function testObjectValidatorSanitize()
    {
        $object = new self();
        $this->assertEquals($object, ObjectValidator::sanitize($object));
        $this->assertNull(ObjectValidator::sanitize('invalid'));
    }

    /**
     * @test
     */
    public function testObjectValidatorValidate()
    {
        $object = new self();
        $this->assertTrue(ObjectValidator::validate($object));
        $this->assertFalse(ObjectValidator::validate('invalid'));
    }

    /**
     * @test
     */
    public function testBooleanValidatorValidate()
    {
        // use a bool as data
        $this->assertTrue(BooleanValidator::validate(true));
        $this->assertTrue(BooleanValidator::validate(false));
        $this->assertFalse(BooleanValidator::validate("1"));
        $this->assertFalse(BooleanValidator::validate("0"));
        $this->assertFalse(BooleanValidator::validate(""));
    }

    /**
     * @test
     */
    public function testBooleanValidatorSanitize()
    {
        // use a bool as data
        $this->assertTrue(BooleanValidator::sanitize(true));
        $this->assertTrue(BooleanValidator::sanitize("1"));
        $this->assertFalse(BooleanValidator::sanitize(false));
        $this->assertFalse(BooleanValidator::sanitize("0"));
        $this->assertFalse(BooleanValidator::sanitize(""));
    }

    /**
     * @test
     */
    public function testMailValidatorSanitize()
    {
        $email = 'mail@domain.tld';
        $this->assertEquals($email, MailValidator::sanitize($email));
        $this->assertEquals($email, MailValidator::sanitize($email), 15);
        $this->assertEquals($email, MailValidator::sanitize(" $email "), 15);
        $this->assertNull(MailValidator::sanitize($email, 14));
        $this->assertNull(MailValidator::sanitize('mail@@domain.tld'));
        $this->assertNull(MailValidator::sanitize("mail\n@domain.tld"));
    }

    /**
     * @test
     */
    public function testMailValidatorValidate()
    {
        // use an mail as data
        $email = 'mail@domain.tld';
        $this->assertTrue(MailValidator::validate($email, 15));
        $this->assertFalse(MailValidator::validate($email, 14));
        $this->assertFalse(MailValidator::validate('mail@@domain.tld'));
        $this->assertFalse(MailValidator::validate("mail\n@domain.tld"));
    }

    /**
     * @test
     */
    public function testStringValidatorText()
    {
        $text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                 Nullam placerat, leo sit amet volutpat ullamcorper,
                 sem tellus pellentesque leo, ac tempus massa nulla in ligula.
                 Integer placerat egestas cursus. Suspendisse ut porttitor elit.
                 Nullam at nisi ut odio viverra euismod. In faucibus fermentum auctor.
                 Phasellus rutrum consectetur massa, sed mattis ante imperdiet ultricies.
                 Aliquam sapien odio, elementum et sagittis non, adipiscing quis tellus. ';
        $untaintInput = StringValidator::sanitize($text, 1000, StringValidator::USERTEXT);
        $this->assertEquals(trim(str_replace("\n", "[br]", $text)), $untaintInput);

        // escape LINEBREAK
        $untaintInput = StringValidator::sanitize($text, 1000, StringValidator::LINEBREAK);
        $this->assertNotEquals(str_replace("\n", "[br]", $text), $untaintInput);

        // expected the same text string like on input
        $untaintInput = StringValidator::sanitize($text, 1000);
        $this->assertEquals($text, $untaintInput);

        $untaintInput = StringValidator::sanitize($text, 11);
        // verify with 11 first digits of text (expecting Lorem ipsum)
        $this->assertEquals('Lorem ipsum', $untaintInput);
    }

    /**
     * @test
     */
    public function testStringValidatorSanitize()
    {
        // use a string as data
        $string = 'Integer $placerat egestas cursus.';
        $untaintInput = StringValidator::sanitize($string, 40, StringValidator::TOKEN);
        $this->assertNotEquals($string, $untaintInput);
        $this->assertType('string', $untaintInput);
        $this->assertEquals('Integer &#36;placerat egestas cursus.', $untaintInput);

        $string = 'Integer placerat egestas cursus.';
        $untaintInput = StringValidator::sanitize($string, 2);
        $this->assertNotEquals($string, $untaintInput);
        $this->assertType('string', $untaintInput);

        $untaintInput = StringValidator::sanitize("1", 0, StringValidator::LINEBREAK);
        $this->assertEquals($untaintInput, '1');
        $this->assertType('string', $untaintInput);

        $untaintInput = StringValidator::sanitize('1', 0, StringValidator::LINEBREAK);
        $this->assertEquals($untaintInput, 1);
        $this->assertType('string', $untaintInput);

        // allow more digits than string have
        $untaintInput = StringValidator::sanitize($string, 100);
        $this->assertEquals($string, $untaintInput);
        $this->assertType('string', $untaintInput);

        // check stripping of white-space
        $untaintInput = StringValidator::sanitize("foo \n\x00bar");
        $this->assertEquals("foo \nbar", $untaintInput);
    }

    /**
     * @test
     */
    public function testStringValidatorValidate()
    {
        $this->assertFalse(StringValidator::validate(123));
        $this->assertTrue(StringValidator::validate("123"));
        $this->assertTrue(StringValidator::validate("123", 3));
        $this->assertFalse(StringValidator::validate("123", 2));
    }

    /**
     * @test
     */
    public function testIpValidatorSanitize()
    {
        // use an ip for data
        $ip = '127.0.0.1';
        $untaintInput = IpValidator::sanitize($ip);
        $this->assertEquals($ip, $untaintInput);
        $this->assertType('string', $ip);

        // expected null for a bad ip adress
        $this->assertNull(IpValidator::sanitize('1.2.3.4.5'));
        $this->assertNull(IpValidator::sanitize('1.2.3,4'));

        $ip = '127.0.0.1';
        $untaintInput = IpValidator::sanitize($ip);
        // expected true with 127.0.0.1
        $this->assertEquals($ip, $untaintInput);
    }

    /**
     * @test
     */
    public function testIpValidatorValidate()
    {
        $this->assertTrue(IpValidator::validate('1.2.3.4'));
        $this->assertFalse(IpValidator::validate("1.2.\n3.4"));
        $this->assertFalse(IpValidator::validate('1.2.3.4.5'));
        $this->assertFalse(IpValidator::validate('1.2.3,4'));
    }

    /**
     * @test
     */
    public function testUrlValidatorSanitize()
    {
        // use an url for data
        $url = 'http://www.test.de?&%20=0#x';
        $this->assertEquals($url, UrlValidator::sanitize($url));
        $url = 'www.test.de?&%20=0#x';

        // expected www
        $this->assertEquals('http://www.test.de', UrlValidator::sanitize($url, 18));

        // expected sanitized URL string
        $this->assertEquals('http://foobar', UrlValidator::sanitize("foo\n\x00bar"));
        // invalid URLs
        $this->assertEquals("", UrlValidator::sanitize("foo", 1));
        $this->assertEquals("", UrlValidator::sanitize(" "));
    }

    /**
     * @test
     */
    public function testNumberValidator()
    {
        $this->assertEquals(-3, IntegerValidator::sanitize(-3, 1));
        $this->assertEquals(+3, IntegerValidator::sanitize(3.2, 1));
        $this->assertEquals(+3, IntegerValidator::sanitize(3.4, 1));
        $this->assertEquals(+4, IntegerValidator::sanitize(3.5, 1));
        $this->assertEquals(+4, IntegerValidator::sanitize(3.6, 1));
        $this->assertEquals(+9, IntegerValidator::sanitize(9.9, 1));
        $this->assertEquals(+9, IntegerValidator::sanitize(10, 1));
        $this->assertEquals(11, IntegerValidator::sanitize(11.11, 2));
        $this->assertEquals(99, IntegerValidator::sanitize(111.11, 2));
        $this->assertEquals(-3.0, FloatValidator::sanitize(-3.1, 1, 0));
        $this->assertEquals(+3.0, FloatValidator::sanitize(3.4, 1, 0));
        $this->assertEquals(+4.0, FloatValidator::sanitize(3.5, 1, 0));
        $this->assertEquals(+3.2, FloatValidator::sanitize(3.21, 2, 1));
        $this->assertEquals(+9.9, FloatValidator::sanitize(13.5, 2, 1));
        $this->assertEquals(11.1, FloatValidator::sanitize(11.11, 3, 1));
        $this->assertEquals(99.9, FloatValidator::sanitize(111.11, 3, 1));
        $this->assertEquals(0.12, FloatValidator::sanitize(0.115, 0, 2));
        $this->assertEquals(5.12, FloatValidator::sanitize(5.115, 3, 2));
        $this->assertEquals("1", StringValidator::sanitize(1, 0, StringValidator::LINEBREAK));
        $this->assertEquals(1, IntegerValidator::sanitize("1"));
        $this->assertEquals(89.95, FloatValidator::sanitize("+89,95", 4, 2));
        $this->assertEquals(-189.96, FloatValidator::sanitize("-189,959", 0, 2));
    }

}

?>