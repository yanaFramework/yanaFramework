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
class SMLTest extends \PHPUnit_Framework_TestCase
{

    /**
     * SML instance to test
     *
     * @var \Yana\Files\Decoders\SML
     */
    public $object = null;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->object = new SML();
    }

    /**
     * Cleans up the environment after running a test.
     *
     * @ignore
     */
    protected function tearDown()
    {
        // intentionally left blank
    }

    /**
     * @test
     */
    public function testEncode()
    {
        // the following returns true
        $input = array("test" => 1);
        $encoded = $this->object->encode($input, "root", CASE_UPPER);
        $expected = "<ROOT>\n\t<TEST>1</TEST>\n</ROOT>\n";
        $this->assertEquals($expected, $encoded);
    }

    /**
     * @test
     */
    public function testGetFile()
    {
        // the following returns true
        $expected = array("ROOT" => array("TEST" => 1));
        $input = array("<Root>", "<test>1</test>", "</Root>");
        $result = $this->object->getFile($input, CASE_UPPER);
        $this->assertEquals($expected, $result);
    }

    /**
     * decode
     *
     * @test
     */
    public function testDecode()
    {
        // the following returns true
        $inputBool = true;
        $encoded = $this->object->encode($inputBool, 'MY_VAR');
        $decode = $this->object->decode($encoded);
        $this->assertEquals($inputBool, $decode['MY_VAR'], 'assert failed, the two variables are equal');
    }

    /**
     * decode Invalid Argument
     *
     * @expectedException  PHPUnit_Framework_Error
     * @test
     */
    public function testDecodeInvalidArgument()
    {
        $decode = $this->object->decode(541);
        $this->assertInternalType('null', $decode, 'assert failed, first argument must be a string');
    }

}
