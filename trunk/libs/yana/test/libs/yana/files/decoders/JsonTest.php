<?php
/**
 * PHPUnit test-case
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
 * @package  test
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Files\Decoders\Json
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Files\Decoders\Json();
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
    public function testGetFile()
    {
        $expected = array("a" => "Test", "B" => 123);
        $test = $this->object->getFile(\CWD . '/resources/test.json');
        $this->assertEquals($expected, $test);
    }

    /**
     * @test
     */
    public function testGetFileCaseUpper()
    {
        $expected = array("A" => "Test", "B" => 123);
        $test = $this->object->getFile(\CWD . '/resources/test.json', \CASE_UPPER);
        $this->assertEquals($expected, $test);
    }

    /**
     * @test
     */
    public function testGetFileCaseLower()
    {
        $expected = array("a" => "Test", "b" => 123);
        $test = $this->object->getFile(\CWD . '/resources/test.json', \CASE_LOWER);
        $this->assertEquals($expected, $test);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testGetFileInvalidArgumentException()
    {
        $this->object->getFile('invalidArgument');
    }

    /**
     * @test
     */
    public function testGetFileFromString()
    {
        $expected = array("test" => 1);
        $input = explode("\n", json_encode($expected));
        $test = $this->object->getFile($input);
        $this->assertEquals($expected, $test);
    }

    /**
     * @test
     */
    public function testEncode()
    {
        $value = array('test' => 1);
        $expected = json_encode($value);
        $test = $this->object->encode($value);
        $this->assertEquals($expected, $test);
    }

    /**
     * @test
     */
    public function testEncodeWithName()
    {
        $name = __FUNCTION__;
        $value = array('Test' => 1);
        $expected = json_encode(array($name => $value));
        $test = $this->object->encode($value, $name);
        $this->assertEquals($expected, $test);
    }

    /**
     * @test
     */
    public function testEncodeCaseUpper()
    {
        $name = __FUNCTION__;
        $value = array('Test' => 1);
        $expected = json_encode(array(\strtoupper($name) => \array_change_key_case($value, \CASE_UPPER)));
        $test = $this->object->encode($value, $name, \CASE_UPPER);
        $this->assertEquals($expected, $test);
    }

    /**
     * @test
     */
    public function testDecode()
    {
        $expected = array('Test' => 1);
        $input = json_encode($expected);
        $test = $this->object->decode($input);
        $this->assertEquals($expected, $test);
    }

}
