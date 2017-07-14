<?php
/**
 * PHPUnit test-case: SML
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

namespace Yana\Files;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../include.php';

/**
 * Test class for SXML
 *
 * @package  test
 */
class SXMLTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Files\SXML
     */
    protected $object = null;

    /**
     * @var  string
     */
    protected $source = 'resources/test.sxml';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new SXML(CWD . $this->source);
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
     * get file
     *
     * @test
     */
    public function testGetFile()
    {
        $testLower = array("tag" => "text", "othertag" => "more text");
        $testUpper = array_change_key_case($testLower, CASE_UPPER);
        $test1 = SXML::getFile(CWD . $this->source, CASE_UPPER);
        $this->assertEquals($testUpper, $test1, "Test: get SXML-file (Upper-case) failed");
        $test2 = SXML::getFile(CWD . $this->source, CASE_LOWER);
        $this->assertEquals($testLower, $test2, "Test: get SXML-file (Upper-case) failed");
    }

    /**
     * Get File Invalid Argument
     *
     * @expectedException  PHPUnit_Framework_Error
     * @test
     */
    function testGetFileInvalidArgument()
    {
        $test1 = SXML::getFile(array(), CASE_UPPER);
        $this->assertInternalType('array', $test1, 'assert failed, the value is not from type array');
    }

    /**
     * encode file
     *
     * @test
     */
    public function testEncode()
    {
        $testSource = file_get_contents(CWD . $this->source);
        $testArray = array("tag" => "text", "othertag" => "more text");
        $testString = SXML::encode($testArray, "root");
        $this->assertEquals(preg_replace('/\s/', '', $testString), preg_replace('/\s/', '', $testSource), "Test: encode SXML-file failed");

        // try with upper case / lower case
        $upperCaseEncode = SXML::encode($testArray, 'tree', CASE_UPPER);
        $lowerCaseEncode = SXML::encode($testArray, 'tree', CASE_LOWER);
        $this->assertNotEquals($upperCaseEncode, $lowerCaseEncode, 'assert failed, the variables are not equal');

        $numericArray = array(1 => "text", 2 => "more text");
        $encode = SXML::encode($numericArray, 'tree', CASE_UPPER);
        $this->assertNotEquals($upperCaseEncode, $numericArray, 'assert failed, the variables are not equal');

    }

    /**
     * decode file
     *
     * @test
     */
    public function testDecode()
    {
        $testSource = file_get_contents(CWD . $this->source);
        $testArray1 = array("tag" => "text", "othertag" => "more text");
        $testArray2 = SXML::decode($testSource);
        $this->assertEquals($testArray1, $testArray2, "Test: decode SXML-file failed");
    }

    /**
     * Decode Invalid Argument
     *
     * @expectedException  PHPUnit_Framework_Error
     * @test
     */
    function testDecodeInvalidArgument()
    {
        $testArray = SXML::decode(array());
        $this->assertInternalType('null', $testArray, 'assert failed, first argument need to be a string');
    }

    /**
     * round-trip test
     *
     * Must be able to decode an XML-string to an array and back without changing it.
     *
     * @test
     */
    public function testRoundtrip()
    {
        $expected = "<root>\n\t<tag attr=\"1\">\n\t\t<attr>2</attr>\n\t</tag>\n\t" .
            "<tag attr=\"2\">2</tag>\n</root>\n";
        $decoder = new Decoders\SXML();
        $array = $decoder->decode($expected);
        $actual = $decoder->encode($array);
        $this->assertEquals($expected, $actual, 'Calling encode(decode($xml)) must not change the input.');
    }
}
?>