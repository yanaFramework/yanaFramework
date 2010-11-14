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
class ToolboxTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var  string
     */
    protected $dir = 'resources/';

    /**
     * Create a new instance.
     *
     * @access public
     */
    public function  __construct()
    {
        parent::__construct();
        $this->dir = CWD . $this->dir;
    }

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
     * dirlist
     *
     * @test
     */
    public function testDirList()
    {
        // read all txt entries
        $dirList = dirlist($this->dir, '*.txt');
        $expected = array();
        foreach (glob($this->dir . '/*.txt') as $path)
        {
            $expected[] = basename($path);
        }
        $this->assertType('array', $dirList, 'assert failed, value is not of type array');
        $this->assertGreaterThanOrEqual(1, count($dirList), 'assert failed, the value must be 1 or higher');
        $this->assertEquals($expected, $dirList, 'directory listing with filter *.txt should match directory contents');

        // read without set a filter
        $dirList = dirlist($this->dir);
        $expected = array();
        foreach (scandir($this->dir) as $path)
        {
            if (!is_dir($path)) {
                $expected[] = $path;
            }
        }
        $this->assertType('array', $dirList, 'assert failed, value is not of type array');
        $this->assertGreaterThanOrEqual(1, count($dirList), 'assert failed, the value must be 1 or higher');
        $this->assertEquals($expected, $dirList, 'directory listing should match directory contents');

        // choose more file types
        $dirList = dirlist($this->dir, '*.txt');
        $expected = array();
        foreach (glob($this->dir . '/*.txt') as $path)
        {
            $expected[] = basename($path);
        }
        foreach (glob($this->dir . '/*.xml') as $path)
        {
            $expected[] = basename($path);
        }
        foreach (glob($this->dir . '/*.dat') as $path)
        {
            $expected[] = basename($path);
        }
        sort($expected);
        $dirList = dirlist($this->dir, '*.txt|*.xml|*.dat');
        $this->assertType('array', $dirList, 'assert failed, value is not of type array');
        $this->assertGreaterThanOrEqual(1, count($dirList), 'assert failed, the value must be 1 or higher');
        $this->assertEquals($expected, $dirList, 'directory listing with filter *.txt, *.xml, *.dat should match directory contents');

        // set switch to yana_get_all
        $dirList = dirlist($this->dir, '', YANA_GET_ALL);
        $expected = array();
        foreach (scandir($this->dir) as $path)
        {
            if ($path[0] !== '.') {
                $expected[] = basename($path);
            }
        }
        $this->assertType('array', $dirList, 'assert failed, value is not of type array');
        $this->assertGreaterThanOrEqual(1, count($dirList), 'assert failed, the value must be 1 or higher');
        $this->assertGreaterThanOrEqual(13, count($dirList), 'assert failed, the value must be 13 or higher');
        $this->assertEquals($expected, $dirList, 'directory listing without filter should match directory contents');

        // set switch to yana_get_files
        $dirList = dirlist($this->dir, '', YANA_GET_FILES);
        $expected = array();
        foreach (scandir($this->dir) as $path)
        {
            if (is_file($this->dir . $path)) {
                $expected[] = basename($path);
            }
        }
        $this->assertType('array', $dirList, 'assert failed, value is not of type array');
        $this->assertGreaterThanOrEqual(1, count($dirList), 'assert failed, the value must be 1 or higher');
        $this->assertGreaterThanOrEqual(10, count($dirList), 'assert failed, the value must be 10 or higher');
        $this->assertEquals($expected, $dirList, 'directory listing for files should match directory contents');

        // set switch to yana_get_dirs
        $dirList = dirlist($this->dir, '', YANA_GET_DIRS);
        $expected = array();
        foreach (scandir($this->dir) as $path)
        {
            if (is_dir($this->dir . $path) && $path[0] !== '.') {
                $expected[] = $path;
            }
        }
        $this->assertType('array', $dirList, 'assert failed, value is not of type array');
        $this->assertGreaterThanOrEqual(1, count($dirList), 'assert failed, the value needs to be 1 or higher');
        $this->assertGreaterThanOrEqual(3, count($dirList), 'assert failed, the value needs to be 3 or higher');
        $this->assertEquals($expected, $dirList, 'directory listing for direcories should match directory contents');
    }

    /**
     * DirListInvalidArgument
     *
     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    public function testDirListInvalidArgument()
    {
        $dirList = dirlist('newresource');
    }

    /**
     * qSearchArray
     * @todo problem with searching with follow exemple: qSearchArray(array('test'), 'test');
     * @test
     */
    function testqSearchArray()
    {
        // try search for a word in a array with 1 entrie
        $array = array('test');
        $array1 = array('abcd', 'abc');
        $array2 = array('abcdabcd', 'abc');
        $array3 = array('c', 'y');

        // expecting result is "test"
        $result = Hashtable::quickSearch($array, 'test');
        $this->assertEquals($result, 0, 'Testing array with 1 element. Needle expected to be found at index 0.');

        // expecting result is "test"
        $result = Hashtable::quickSearch($array, 'non existing key');
        $this->assertFalse($result, 'Testing array with 1 element. Needle should not be found.');

        $result = Hashtable::quickSearch($array1, 'abcd');
        $this->assertType('integer', $result, 'assert failed, value is not of type integer');
        //expected integer 0 - searching word is in the row[0]
        $this->assertEquals($result, 0, 'assert failed, the variables should be equal');
        $this->assertEquals('abcd', $array1[$result], 'assert failed, the values should be equal');
        // expected true
        $this->assertTrue($array1[$result] === 'abcd', 'asert failed, the result must be equal (true)');

        $result = Hashtable::quickSearch($array2, 'abcdabcd');
        $this->assertType('integer', $result, 'assert failed, values is not of type integer');
        // expected integer 0
        $this->assertEquals(0, $result, 'assert failed, the valus must be equal');
        $this->assertEquals('abcdabcd', $array2[$result], 'assert failed, the values should be equal');
        // expected true
        $this->assertTrue($array2[$result] === 'abcdabcd', 'asert failed, the result must be equal (true)');


        $result = Hashtable::quickSearch($array3, 'y');
        $this->assertType('integer', $result, 'assert failed, value is not of type integer');
        //expected integer 1
        $this->assertEquals($result, 1, 'assert failed, the variables should be equal');
        $this->assertEquals('y', $array3[$result], 'assert failed, the values should be equal');
        // expected true
        $this->assertTrue($array3[$result] === 'y', 'asert failed, the result must be equal (true)');
    }

    /**
     * untaintInput
     *
     * @test
     */
    function testuntaintInput()
    {
        // use an integer
        $untaintInput = untaintInput(60, 'int', 1);
        $this->assertType('int', $untaintInput, 'assert failed, value is not of type integer');
        $this->assertEquals($untaintInput, 9, 'assert failed, the integer value must have 1 digit');

        $untaintInput = untaintInput(60, 'int', 2);
        $this->assertType('int', $untaintInput, 'assert failed, value is not of type integer');
        $this->assertEquals($untaintInput, 60, 'assert failed, the integer value must have 2 digits');

        $untaintInput = untaintInput(100.05, 'int', 2);
        $this->assertType('int', $untaintInput, 'assert failed, value is not of type integer');
        $this->assertEquals($untaintInput, 99, 'assert failed, the integer value must have 2 digits');

        $untaintInput = untaintInput(120.52, 'int', 2);
        $this->assertType('int', $untaintInput, 'assert failed, value is not of type integer');
        $this->assertEquals($untaintInput, 99, 'assert failed, the integer value must have 2 digits');

        // use float
        $untaintInput = untaintInput(-3.1,   "float", 1, YANA_ESCAPE_NONE, false, 0);
        $this->assertType('float', $untaintInput, 'assert failed, value is not of type float');
        $this->assertEquals($untaintInput, -3.0, 'assert failed, the integer value must have 1 digit');

        $untaintInput = untaintInput(0.115,  "float", 0, YANA_ESCAPE_NONE, false, 2);
        $this->assertType('float', $untaintInput, 'assert failed, value is not of type float');
        $this->assertEquals($untaintInput, .12, 'assert failed, the integer value must have 1 digit');

        $untaintInput = untaintInput(-3.5,   "float", 1, YANA_ESCAPE_NONE, false, 0);
        $this->assertType('float', $untaintInput, 'assert failed, value is not of type float');
        $this->assertEquals($untaintInput, -4, 'assert failed, the integer value must have 1 digit');

        $untaintInput = untaintInput("+89,95","float", 4, YANA_ESCAPE_NONE, false, 2);
        $this->assertEquals($untaintInput, 89.95, 'assert failed, the integer value must have 2 digits');
        $this->assertType('float', $untaintInput, 'assert failed, value is not of type float');

        $untaintInput = untaintInput("-189,959","float", 0, YANA_ESCAPE_NONE, false, 2);
        $this->assertEquals($untaintInput, -189.96, 'assert failed, the integer value must have 2 digits');
        $this->assertType('float', $untaintInput, 'assert failed, value is not of type float');

        $untaintInput = untaintInput(-3.33,   "float", 0, YANA_ESCAPE_NONE, false, 2);
        $this->assertType('float', $untaintInput, 'assert failed, value is not of type float');
        $this->assertEquals($untaintInput, -3.33, 'assert failed, the integer value must have 1 digits');

        $untaintInput = untaintInput(-3.33,   "float", 0, YANA_ESCAPE_NONE, false, 1);
        $this->assertType('float', $untaintInput, 'assert failed, value is not of type float');
        $this->assertNotEquals($untaintInput, -3.33, 'assert failed, the integer value must have 1 digit');
        $this->assertEquals($untaintInput, -3.3, 'assert failed, the integer value must have 1 digit');

        // use an array as data
        $array = array('abcd', 'abc', 'gfhjk');

        $untaintInput = untaintInput($array, 'array', 11);
        $validate='<0>abcd</0>';
        $this->assertEquals($validate, $untaintInput, 'assert failed, the values should be equal');
        $this->assertType('string', $untaintInput, 'assert failed, value is not of type string');
        $this->assertNotEquals($array, $untaintInput, 'assert failed, that two variables cant be equal');

        // @todo cant use or try to do case with exemple: case is_array($value) on line 627 see line 483
        // (to do this block they need to set $test=true, withot set the type or using encodes)

        // use an object as data
        $object = new Image();
        $untaintInput = untaintInput($object, 'object');
        $this->assertType('object', $untaintInput, 'assert failed, value is not of type object');
        $this->assertEquals($object, $untaintInput, 'assert failed, the two varialbles should be equal');

        // use a bool as data
        $untaintInput = untaintInput(true, 'bool');
        $this->assertTrue($untaintInput, 'assert failed, the result should be a bool true');
        $this->assertEquals(true, $untaintInput, 'assert failed, the two variables should be equal');

        $untaintInput = untaintInput(false, 'bool');
        $this->assertFalse($untaintInput, 'assert failed, the result should be a bool false');
        $this->assertEquals(false, $untaintInput, 'assert failed, the two variables should be equal');

        // use an mail as data
        $email = 'mail@domain.tld';
        $untaintInput = untaintInput($email, 'mail');
        $this->assertEquals($email, $untaintInput, 'assert failed, the two variables should be equal');
        $this->assertType('string', $untaintInput, 'assert failed, value ist not of type string');

        $email = 'mail@@domain.tld';
        $untaintInput = untaintInput($email, 'mail');
        $this->assertNotEquals($email, $untaintInput, 'assert failed, the two variables cant be equal, bad "value format for mail typ"');
        $this->assertType('string', $untaintInput, 'assert failed, value ist not of type string');

        $text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                 Nullam placerat, leo sit amet volutpat ullamcorper,
                 sem tellus pellentesque leo, ac tempus massa nulla in ligula.
                 Integer placerat egestas cursus. Suspendisse ut porttitor elit.
                 Nullam at nisi ut odio viverra euismod. In faucibus fermentum auctor.
                 Phasellus rutrum consectetur massa, sed mattis ante imperdiet ultricies.
                 Aliquam sapien odio, elementum et sagittis non, adipiscing quis tellus. ';
        $untaintInput = untaintInput($text, 'text', 1000, YANA_ESCAPE_USERTEXT);
        $this->assertNotEquals($text, $untaintInput, 'assert failed, the two variables cant be equal');
        $this->assertType('string', $untaintInput, 'assert failed, value is not of type string');

        // escape YANA_ESCAPE_LINEBREAK
        $untaintInput = untaintInput($text, 'text', 1000, YANA_ESCAPE_LINEBREAK);
        $this->assertNotEquals($text, $untaintInput, 'assert failed, the two variables cant be equal');
        $this->assertType('string', $untaintInput, 'assert failed, value is not of type string');

        // expected the same text string like on input
        $untaintInput = untaintInput($text, 'text', 1000, YANA_ESCAPE_NONE);
        $this->assertEquals($text, $untaintInput, 'assert failed, the two variables should be equal');
        $this->assertType('string', $untaintInput, 'assert failed, value is not of type string');

        $untaintInput = untaintInput($text, 'text', 11);
        $this->assertNotEquals($text, $untaintInput, 'assert failed, the values cant be equal');
        $this->assertType('string', $untaintInput, 'assert failed, value is not of type string');
        // verify with 11 first digits of text (expecting Lorem ipsum)
        $this->assertEquals('Lorem ipsum', $untaintInput, 'assert failed, the values should be equal');

        // use a string as data
        $string ='Integer $placerat egestas cursus.';
        $untaintInput = untaintInput($string, 'string', 40, YANA_ESCAPE_TOKEN);
        $this->assertNotEquals($string, $untaintInput, 'assert failed, the values cant be equal');
        $this->assertType('string', $untaintInput, 'assert failed, value is not of type stirng');
        $this->assertEquals('Integer &#36;placerat egestas cursus.', $untaintInput, 'assert failed, the values should be equal');

        $string ='Integer placerat egestas cursus.';
        $untaintInput = untaintInput($string, 'string', 2);
        $this->assertNotEquals($string, $untaintInput, 'assert failed, the values cant be equal');
        $this->assertType('string', $untaintInput, 'assert failed, value is not of type stirng');

        $untaintInput = untaintInput("1",    "string", 0, YANA_ESCAPE_LINEBREAK);
        $this->assertEquals($untaintInput, '1', 'assert failed, the variable should be equal');
        $this->assertType('string', $untaintInput, 'assert failed, value is not of type string');

        $untaintInput = untaintInput('1',    "string", 0, YANA_ESCAPE_LINEBREAK);
        $this->assertEquals($untaintInput, 1, 'assert failed, the variable should be equal');
        $this->assertType('string', $untaintInput, 'assert failed, value is not of type string');

        // allow more digits than string have
        $untaintInput = untaintInput($string, 'string', 100);
        $this->assertEquals($string, $untaintInput, 'assert failed, the values should be equal');
        $this->assertType('string', $untaintInput, 'assert failed, value is not of type stirng');

        // use a time as data
        // 01.01.2005 13:15:00
        $timestamp = '1104585300';
        $untaintInput = untaintInput($timestamp, 'timestamp');
        $this->assertEquals($timestamp ,$untaintInput, 'assert failed, the variables should be equal');
        $this->assertType('integer', $untaintInput, 'assert failed, value is not of type integer');

        $timestamp = '11045m85300p';
        $untaintInput = untaintInput($timestamp, 'timestamp');
        $this->assertNotEquals($timestamp ,$untaintInput, 'assert failed, the variables cant be equal');
        $this->assertType('integer', $untaintInput, 'assert failed, value is not from type integer');
        // validate with 11045
        $this->assertEquals(11045 ,$untaintInput, 'assert failed, the variables should be equal');

        // use an ip for data
        $ip = '127.0.0.1';
        $untaintInput = untaintInput($ip, 'inet');
        $this->assertEquals($ip ,$untaintInput, 'assert failed, the variables must be equal');
        $this->assertType('string', $ip, 'assert failed, value is not of type string');
        $ip = $ip.'yana';
        $untaintInput = untaintInput($ip, 'inet');
        $this->assertNotEquals($ip ,$untaintInput, 'assert failed, the variables are not equal');
        // expected null for a bad ip adress
        $this->assertNull($untaintInput, 'assert failed, the given ip adress is incorect');
        $ip = '127.0.0.1';
        $untaintInput = untaintInput($ip, 'inet');
        // expected true with 127.0.0.1
        $this->assertEquals($ip, $untaintInput, 'assert failed, the variables should be equal');

        // use an url for data
        $url = 'http://www.test.de?&%20=0#x';
        $untaintInput = untaintInput($url, 'url');
        $this->assertEquals($url, $untaintInput, 'assert failed, the two variables should be equal');
        $this->assertType('string', $untaintInput, 'assert failed, value is not of type string');
        $url = 'www.test.de?&%20=0#x';
        $untaintInput = untaintInput($url, 'url', 18);
        $this->assertType('string', $untaintInput, 'assert failed, value is not of type string');
        $this->assertNotEquals($url, $untaintInput, 'assert failed, the two variables can\'t be equal');
        // expected www
        $this->assertEquals('http://www.test.de', $untaintInput, 'assert failed, the variables should be equal');
    }

    /**
     * untaintInput standard processing
     *
     * @test
     */
    function  testuntaintInput1()
    {
        $this->assertEquals(-3, untaintInput(-3, "int", 1));
        $this->assertEquals(+3, untaintInput(3.2, "int", 1));
        $this->assertEquals(+3, untaintInput(3.4, "int", 1));
        $this->assertEquals(+4, untaintInput(3.5, "int", 1));
        $this->assertEquals(+4, untaintInput(3.6, "int", 1));
        $this->assertEquals(+9, untaintInput(9.9, "int", 1));
        $this->assertEquals(+9, untaintInput(10, "int", 1));
        $this->assertEquals(11, untaintInput(11.11, "int", 2));
        $this->assertEquals(99, untaintInput(111.11, "int", 2));
        $this->assertEquals(-3.0, untaintInput(-3.1, "float", 1, YANA_ESCAPE_NONE, false, 0));
        $this->assertEquals(+3.0, untaintInput(3.4, "float", 1, YANA_ESCAPE_NONE, false, 0));
        $this->assertEquals(+4.0, untaintInput(3.5, "float", 1, YANA_ESCAPE_NONE, false, 0));
        $this->assertEquals(+3.2, untaintInput(3.21, "float", 2, YANA_ESCAPE_NONE, false, 1));
        $this->assertEquals(+9.9, untaintInput(13.5, "float", 2, YANA_ESCAPE_NONE, false, 1));
        $this->assertEquals(11.1, untaintInput(11.11, "float", 3, YANA_ESCAPE_NONE, false, 1));
        $this->assertEquals(99.9, untaintInput(111.11, "float", 3, YANA_ESCAPE_NONE, false, 1));
        $this->assertEquals(0.12, untaintInput(0.115, "float", 0, YANA_ESCAPE_NONE, false, 2));
        $this->assertEquals(5.12, untaintInput(5.115, "float", 3, YANA_ESCAPE_NONE, false, 2));
        $this->assertEquals("1", untaintInput("1", "string", 0, YANA_ESCAPE_LINEBREAK));
        $this->assertEquals(1, untaintInput("1", "int", 0, YANA_ESCAPE_LINEBREAK));
        $this->assertEquals(89.95, untaintInput("+89,95","float", 4, YANA_ESCAPE_NONE, false, 2));
        $this->assertEquals(-189.96, untaintInput("-189,959","float", 0, YANA_ESCAPE_NONE, false, 2));
    }

    /**
     * clone array
     *
     * @test
     */
    function testCloneArray()
    {
        $array1 = array('adc',
                        'def',
                        'ghjk',
                        'pio',
                        'tqasd');
        $array2 = array('adc'=>array('def',
                                      'ghjk',
                                      'pio',
                                      'tqasd'));

        $result = Hashtable::cloneArray($array1);
        $this->assertType('array', $result, 'assert failed, value is not of type array');
        $this->assertEquals($array1, $result, 'assert failed, the variables should be equal');

        $result = Hashtable::cloneArray($array2);
        $this->assertType('array', $result, 'assert failed, value is not of type array');
        $this->assertEquals($array2, $result, 'assert failed, the variables should be equal');
    }

    /**
     * Array to XML conversion
     *
     * Calls function Hashtable::toXML().
     *
     * @test
     */
    function testArrayToXML()
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