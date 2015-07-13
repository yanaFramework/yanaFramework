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

namespace Yana\Files\Decoders;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../../include.php';

/**
 * Test class for SXML
 *
 * @package  test
 */
class SXMLTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var    SXML
     * @access protected
     */
    protected $object = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->object = new SXML();
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
     * get file
     *
     * @test
     */
    public function testGetFile()
    {
        $testSource = "<root>\n\t<tag attr=\"1\">\n\t\t<attr>2</attr>\n\t</tag>\n\t" .
            "<tag attr=\"2\">2</tag>\n</root>\n";
        $expected = array('tag' => array(array('@attr' => 1, 'attr' => 2), array('@attr' => 2, '#pcdata' => 2)));
        $value = $this->object->getFile(explode("\n", $testSource));
        $this->assertEquals($expected, $value);
    }

    /**
     * encode file
     *
     * @test
     */
    public function testEncode()
    {
        $expected = "<root>\n\t<tag attr=\"1\">\n\t\t<attr>2</attr>\n\t</tag>\n\t" .
            "<tag attr=\"2\">2</tag>\n</root>\n";
        $array = array('tag' => array(array('@attr' => 1, 'attr' => 2), array('@attr' => 2, '#pcdata' => 2)));
        $actual = $this->object->encode($array, 'root');
        $this->assertEquals($expected, $actual, 'Calling encode(decode($xml)) must not change the input.');
    }

    /**
     * decode file
     *
     * @test
     */
    public function testDecode()
    {
        $testSource = "<root>\n\t<tag attr=\"1\">\n\t\t<attr>2</attr>\n\t</tag>\n\t" .
            "<tag attr=\"2\">2</tag>\n</root>\n";
        $expected = array('tag' => array(array('@attr' => 1, 'attr' => 2), array('@attr' => 2, '#pcdata' => 2)));
        $value = $this->object->decode($testSource);
        $this->assertEquals($expected, $value);
    }

    /**
     * Decode Invalid Argument
     *
     * @expectedException  PHPUnit_Framework_Error
     * @test
     */
    function testDecodeInvalidArgument()
    {
        $testArray = $this->object->decode(array());
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
        $array = $this->object->decode($expected);
        $actual = $this->object->encode($array);
        $this->assertEquals($expected, $actual, 'Calling encode(decode($xml)) must not change the input.');
    }
}
?>