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

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';

/**
 * Test class for Toolbox
 *
 * @package  test
 */
class ValidatorTest extends PHPUnit_Framework_TestCase
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
        $untaintInput = \Yana\Io\IntegerValidator::sanitize(60, 1);
        $this->assertType('int', $untaintInput);
        $this->assertEquals($untaintInput, 9, 'the integer value must have 1 digit');

        $untaintInput = \Yana\Io\IntegerValidator::sanitize(100, 2);
        $this->assertType('int', $untaintInput);
        $this->assertEquals($untaintInput, 99, 'the integer value must have 2 digits');
    }

    /**
     * @test
     */
    public function testIntegerValidatorValidate()
    {
        $this->assertTrue(\Yana\Io\IntegerValidator::validate(100));
        $this->assertTrue(\Yana\Io\IntegerValidator::validate("100"));
        $this->assertFalse(\Yana\Io\IntegerValidator::validate(100.05));
        $this->assertFalse(\Yana\Io\IntegerValidator::validate(100, 2));
        $this->assertTrue(\Yana\Io\IntegerValidator::validate(-100, 3));
        $this->assertFalse(\Yana\Io\IntegerValidator::validate(-100, 3, true));
    }

    /**
     * @test
     */
    public function testFloatValidatorValidate()
    {
        $this->assertTrue(\Yana\Io\FloatValidator::validate(100));
        $this->assertTrue(\Yana\Io\FloatValidator::validate(100.05));
        $this->assertTrue(\Yana\Io\FloatValidator::validate("100.05"));
        $this->assertFalse(\Yana\Io\FloatValidator::validate("100,05"));
        $this->assertFalse(\Yana\Io\FloatValidator::validate(100, 2));
        $this->assertTrue(\Yana\Io\FloatValidator::validate(-100, 3));
        $this->assertTrue(\Yana\Io\FloatValidator::validate(-100, 3));
        $this->assertFalse(\Yana\Io\FloatValidator::validate(-100, 3, true));
    }

    /**
     * @test
     */
    public function testIntegerValidatorToInteger()
    {
        $untaintInput = \Yana\Io\IntegerValidator::sanitize(100.05, 2);
        $this->assertType('int', $untaintInput);
        $this->assertEquals($untaintInput, 99, 'the integer value must have 2 digits');

        $untaintInput = \Yana\Io\IntegerValidator::sanitize(120.52, 3);
        $this->assertType('int', $untaintInput);
        $this->assertEquals($untaintInput, 121);
    }

    /**
     * @test
     */
    public function testFloatValidatorFixedPrecission()
    {
        // use float
        $untaintInput = \Yana\Io\FloatValidator::sanitize(-3.1, 1, 0);
        $this->assertEquals(-3.0, $untaintInput, 'the integer value must have 1 digit');

        $untaintInput = \Yana\Io\FloatValidator::sanitize(0.115, 0, 2);
        $this->assertEquals(.12, $untaintInput, 'the integer value must have 1 digit');

        $untaintInput = \Yana\Io\FloatValidator::sanitize(-3.5, 1, 0);
        $this->assertEquals(-4, $untaintInput, 'the integer value must have 1 digit');

        $untaintInput = \Yana\Io\FloatValidator::sanitize("+89,95", 4, 2);
        $this->assertEquals(89.95, $untaintInput, 'the integer value must have 2 digits');

        $untaintInput = \Yana\Io\FloatValidator::sanitize("-189,959", 0, 2);
        $this->assertEquals(-189.96, $untaintInput, 'the integer value must have 2 digits');

        $untaintInput = \Yana\Io\FloatValidator::sanitize(-3.33, 0, 2);
        $this->assertEquals(-3.33, $untaintInput, 'the integer value must have 1 digits');

        $untaintInput = \Yana\Io\FloatValidator::sanitize(-3.33, 0, 1);
        $this->assertEquals(-3.3, $untaintInput, 'the integer value must have 1 digit');
    }

    /**
     * @test
     */
    public function testArrayValidatorToSml()
    {
        $array = array('abc', 'def', 'ghi');
        $untaintInput = \Yana\Io\ArrayValidator::sanitize($array, 0, \Yana\Io\ArrayValidator::TO_SML);
        $validate = "<0>abc</0>\n<1>def</1>\n<2>ghi</2>\n";
        $this->assertEquals($validate, $untaintInput);
    }

    /**
     * @test
     */
    public function testArrayValidatorToXml()
    {
        $array = array('abc', 'def', 'ghi');
        $untaintInput = \Yana\Io\ArrayValidator::sanitize($array, 0, \Yana\Io\ArrayValidator::TO_XML);
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
        $untaintInput = \Yana\Io\ArrayValidator::sanitize($array, 1, \Yana\Io\ArrayValidator::TO_SML);
        $validate = "<0>abc</0>\n";
        $this->assertEquals($validate, $untaintInput);

    }

    /**
     * @test
     */
    public function testArrayValidatorValidate()
    {
        $array = array('abc', 'def', 'ghi');
        $this->assertTrue(\Yana\Io\ArrayValidator::validate($array));
        $this->assertFalse(\Yana\Io\ArrayValidator::validate($array, 2));
        $this->assertFalse(\Yana\Io\ArrayValidator::validate('invalid'));
    }

    /**
     * @test
     */
    public function testObjectValidatorSanitize()
    {
        $object = new self();
        $this->assertEquals($object, \Yana\Io\ObjectValidator::sanitize($object));
        $this->assertNull(\Yana\Io\ObjectValidator::sanitize('invalid'));
    }

    /**
     * @test
     */
    public function testObjectValidatorValidate()
    {
        $object = new self();
        $this->assertTrue(\Yana\Io\ObjectValidator::validate($object));
        $this->assertFalse(\Yana\Io\ObjectValidator::validate('invalid'));
    }

    /**
     * @test
     */
    public function testBooleanValidatorValidate()
    {
        // use a bool as data
        $this->assertTrue(\Yana\Io\BooleanValidator::validate(true));
        $this->assertTrue(\Yana\Io\BooleanValidator::validate(false));
        $this->assertFalse(\Yana\Io\BooleanValidator::validate("1"));
        $this->assertFalse(\Yana\Io\BooleanValidator::validate("0"));
        $this->assertFalse(\Yana\Io\BooleanValidator::validate(""));
    }

    /**
     * @test
     */
    public function testBooleanValidatorSanitize()
    {
        // use a bool as data
        $this->assertTrue(\Yana\Io\BooleanValidator::sanitize(true));
        $this->assertTrue(\Yana\Io\BooleanValidator::sanitize("1"));
        $this->assertFalse(\Yana\Io\BooleanValidator::sanitize(false));
        $this->assertFalse(\Yana\Io\BooleanValidator::sanitize("0"));
        $this->assertFalse(\Yana\Io\BooleanValidator::sanitize(""));
    }

    /**
     * @test
     */
    public function testMailValidatorSanitize()
    {
        $email = 'mail@domain.tld';
        $this->assertEquals($email, \Yana\Io\MailValidator::sanitize($email));
        $this->assertEquals($email, \Yana\Io\MailValidator::sanitize($email), 15);
        $this->assertEquals($email, \Yana\Io\MailValidator::sanitize(" $email "), 15);
        $this->assertNull(\Yana\Io\MailValidator::sanitize($email, 14));
        $this->assertNull(\Yana\Io\MailValidator::sanitize('mail@@domain.tld'));
        $this->assertNull(\Yana\Io\MailValidator::sanitize("mail\n@domain.tld"));
    }

    /**
     * @test
     */
    public function testMailValidatorValidate()
    {
        // use an mail as data
        $email = 'mail@domain.tld';
        $this->assertTrue(\Yana\Io\MailValidator::validate($email, 15));
        $this->assertFalse(\Yana\Io\MailValidator::validate($email, 14));
        $this->assertFalse(\Yana\Io\MailValidator::validate('mail@@domain.tld'));
        $this->assertFalse(\Yana\Io\MailValidator::validate("mail\n@domain.tld"));
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
        $untaintInput = \Yana\Io\StringValidator::sanitize($text, 1000, \Yana\Io\StringValidator::USERTEXT);
        $this->assertEquals(trim(str_replace("\n", "[br]", $text)), $untaintInput);

        // escape LINEBREAK
        $untaintInput = \Yana\Io\StringValidator::sanitize($text, 1000, \Yana\Io\StringValidator::LINEBREAK);
        $this->assertNotEquals(str_replace("\n", "[br]", $text), $untaintInput);

        // expected the same text string like on input
        $untaintInput = \Yana\Io\StringValidator::sanitize($text, 1000);
        $this->assertEquals($text, $untaintInput);

        $untaintInput = \Yana\Io\StringValidator::sanitize($text, 11);
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
        $untaintInput = \Yana\Io\StringValidator::sanitize($string, 40, \Yana\Io\StringValidator::TOKEN);
        $this->assertNotEquals($string, $untaintInput);
        $this->assertType('string', $untaintInput);
        $this->assertEquals('Integer &#36;placerat egestas cursus.', $untaintInput);

        $string = 'Integer placerat egestas cursus.';
        $untaintInput = \Yana\Io\StringValidator::sanitize($string, 2);
        $this->assertNotEquals($string, $untaintInput);
        $this->assertType('string', $untaintInput);

        $untaintInput = \Yana\Io\StringValidator::sanitize("1", 0, \Yana\Io\StringValidator::LINEBREAK);
        $this->assertEquals($untaintInput, '1');
        $this->assertType('string', $untaintInput);

        $untaintInput = \Yana\Io\StringValidator::sanitize('1', 0, \Yana\Io\StringValidator::LINEBREAK);
        $this->assertEquals($untaintInput, 1);
        $this->assertType('string', $untaintInput);

        // allow more digits than string have
        $untaintInput = \Yana\Io\StringValidator::sanitize($string, 100);
        $this->assertEquals($string, $untaintInput);
        $this->assertType('string', $untaintInput);

        // check stripping of white-space
        $untaintInput = \Yana\Io\StringValidator::sanitize("foo \n\x00bar");
        $this->assertEquals("foo \nbar", $untaintInput);
    }

    /**
     * @test
     */
    public function testStringValidatorValidate()
    {
        $this->assertFalse(\Yana\Io\StringValidator::validate(123));
        $this->assertTrue(\Yana\Io\StringValidator::validate("123"));
        $this->assertTrue(\Yana\Io\StringValidator::validate("123", 3));
        $this->assertFalse(\Yana\Io\StringValidator::validate("123", 2));
    }

    /**
     * @test
     */
    public function testIpValidatorSanitize()
    {
        // use an ip for data
        $ip = '127.0.0.1';
        $untaintInput = \Yana\Io\IpValidator::sanitize($ip);
        $this->assertEquals($ip, $untaintInput);
        $this->assertType('string', $ip);

        // expected null for a bad ip adress
        $this->assertNull(\Yana\Io\IpValidator::sanitize('1.2.3.4.5'));
        $this->assertNull(\Yana\Io\IpValidator::sanitize('1.2.3,4'));

        $ip = '127.0.0.1';
        $untaintInput = \Yana\Io\IpValidator::sanitize($ip);
        // expected true with 127.0.0.1
        $this->assertEquals($ip, $untaintInput);
    }

    /**
     * @test
     */
    public function testIpValidatorValidate()
    {
        $this->assertTrue(\Yana\Io\IpValidator::validate('1.2.3.4'));
        $this->assertFalse(\Yana\Io\IpValidator::validate("1.2.\n3.4"));
        $this->assertFalse(\Yana\Io\IpValidator::validate('1.2.3.4.5'));
        $this->assertFalse(\Yana\Io\IpValidator::validate('1.2.3,4'));
    }

    /**
     * @test
     */
    public function testUrlValidatorSanitize()
    {
        // use an url for data
        $url = 'http://www.test.de?&%20=0#x';
        $this->assertEquals($url, \Yana\Io\UrlValidator::sanitize($url));
        $url = 'www.test.de?&%20=0#x';

        // expected www
        $this->assertEquals('http://www.test.de', \Yana\Io\UrlValidator::sanitize($url, 18));

        // expected sanitized URL string
        $this->assertEquals('http://foobar', \Yana\Io\UrlValidator::sanitize("foo\n\x00bar"));
        // invalid URLs
        $this->assertEquals("", \Yana\Io\UrlValidator::sanitize("foo", 1));
        $this->assertEquals("", \Yana\Io\UrlValidator::sanitize(" "));
    }

    /**
     * @test
     */
    public function testNumberValidator()
    {
        $this->assertEquals(-3, \Yana\Io\IntegerValidator::sanitize(-3, 1));
        $this->assertEquals(+3, \Yana\Io\IntegerValidator::sanitize(3.2, 1));
        $this->assertEquals(+3, \Yana\Io\IntegerValidator::sanitize(3.4, 1));
        $this->assertEquals(+4, \Yana\Io\IntegerValidator::sanitize(3.5, 1));
        $this->assertEquals(+4, \Yana\Io\IntegerValidator::sanitize(3.6, 1));
        $this->assertEquals(+9, \Yana\Io\IntegerValidator::sanitize(9.9, 1));
        $this->assertEquals(+9, \Yana\Io\IntegerValidator::sanitize(10, 1));
        $this->assertEquals(11, \Yana\Io\IntegerValidator::sanitize(11.11, 2));
        $this->assertEquals(99, \Yana\Io\IntegerValidator::sanitize(111.11, 2));
        $this->assertEquals(-3.0, \Yana\Io\FloatValidator::sanitize(-3.1, 1, 0));
        $this->assertEquals(+3.0, \Yana\Io\FloatValidator::sanitize(3.4, 1, 0));
        $this->assertEquals(+4.0, \Yana\Io\FloatValidator::sanitize(3.5, 1, 0));
        $this->assertEquals(+3.2, \Yana\Io\FloatValidator::sanitize(3.21, 2, 1));
        $this->assertEquals(+9.9, \Yana\Io\FloatValidator::sanitize(13.5, 2, 1));
        $this->assertEquals(11.1, \Yana\Io\FloatValidator::sanitize(11.11, 3, 1));
        $this->assertEquals(99.9, \Yana\Io\FloatValidator::sanitize(111.11, 3, 1));
        $this->assertEquals(0.12, \Yana\Io\FloatValidator::sanitize(0.115, 0, 2));
        $this->assertEquals(5.12, \Yana\Io\FloatValidator::sanitize(5.115, 3, 2));
        $this->assertEquals("1", \Yana\Io\StringValidator::sanitize(1, 0, \Yana\Io\StringValidator::LINEBREAK));
        $this->assertEquals(1, \Yana\Io\IntegerValidator::sanitize("1"));
        $this->assertEquals(89.95, \Yana\Io\FloatValidator::sanitize("+89,95", 4, 2));
        $this->assertEquals(-189.96, \Yana\Io\FloatValidator::sanitize("-189,959", 0, 2));
    }

    /**
     * Array to XML conversion
     *
     * Calls function Hashtable::toXML().
     *
     * @test
     */
    public function testArrayToXML()
    {
        // read file and write to an array
        $array = array(
            1 => 'a',
            2 => array(
                'b',
                'c'
            ),
            3 => 1
        );
        $xml = Hashtable::toXML($array);

        // expected result
        $expected = "<array id=\"root\">\n\t<string id=\"1\">a</string>\n" .
            "\t<array id=\"2\">\n\t\t<string id=\"0\">b</string>\n" .
            "\t\t<string id=\"1\">c</string>\n\t</array>\n" .
            "\t<integer id=\"3\">1</integer>\n</array>\n";
        $encoding = iconv_get_encoding("internal_encoding");
        $expected = '<?xml version="1.0" encoding="' . $encoding . '"?>' . "\n" . $expected;

        // compare source and generated result
        $this->assertEquals($expected, $xml, "Round-trip decoding/encoding of source-document failed. The result is differs from the source file.");
    }

}

?>