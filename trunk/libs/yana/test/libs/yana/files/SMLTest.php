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

namespace Yana\Files;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../include.php';

/**
 *  SML test-case
 *
 * @package  test
 */
class SMLTest extends \PHPUnit_Framework_TestCase
{

    /**
     * SML instance to test
     *
     * @var \Yana\Files\SML
     */
    public $instance = null;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->instance = new SML(CWD . 'resources/test.sml', CASE_UPPER);
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
     * get var
     *
     * @test
     * @expectedException  PHPUnit_Framework_Error
     */
    public function testGetVarInvalidArgument()
    {
        // this is supposed to procude an E_USER_WARNING and return bool(false)
        $this->instance->getVar(1);
    }

    /**
     * get var by reference
     *
     * @test
     */
    public function testGetVarByReference()
    {
        $test1 = $this->instance->getVarByReference();
        $this->assertType('array', $test1, '"assert failed , value is not from type array');

        $test2 = $this->instance->getVarByReference('array');
        $this->assertEquals(count($test2), 2, 'assert failed , expected array with 2 values');
    }

    /**
     * get var
     *
     * @test
     */
    public function testGetVar()
    {
        $test = & $this->instance->getVarsByReference();
        $test['FOO'] = 'bar';
        $test['foo'] = 'error';
        unset($test);

        $test2 = $this->instance->getVar('foo');
        $this->assertEquals($test2, 'bar', '"set on reference" test failed.');
    }

    /**
     * exist after reset
     *
     * @test
     */
    public function testExistAfterReset()
    {
        $test = & $this->instance->getVarsByReference();
        $test['FOO'] = 'bar';
        $test['foo'] = 'error';
        unset($test);

        // result should be false
        $this->instance->reset();
        $test = $this->instance->exists('foo');
        $this->assertFalse($test, '"reset" test failed.');
    }

    /**
     * decode
     *
     * @test
     */
    public function testdecode()
    {
        // the following returns true
        $inputBool = true;
        $encoded = $this->instance->encode($inputBool, 'MY_VAR');
        $decode = $this->instance->decode($encoded);
        $this->assertEquals($encoded, $decode['MY_VAR'], 'assert failed, the two variables are equal');
    }

    /**
     * decode Invalid Argument
     *
     * @expectedException  PHPUnit_Framework_Error
     * @test
     */
    public function testdecodeInvalidArgument()
    {
        $decode = $this->instance->decode(541);
        $this->assertType('null', $decode, 'assert failed, first argument must be a string');
    }

    /**
     * get file content
     *
     * @test
     */
    public function testGetFileContent()
    {
        $this->instance->setVars(array('fo' => 'bar', 'FOO' => 'FOO', 'TEST' => 'description'));
        $valid = mb_strlen((string) $this->instance);
        $getFileContent = $this->instance->getFileContent();
        $this->assertType('string', $getFileContent, 'assert failed, value is not from type string');
        $this->assertEquals(mb_strlen($getFileContent), $valid, 'assert failed, expected that the 2 variables are equal');
    }

    /**
     * length
     *
     * @test
     */
    public function testLength()
    {
        $this->instance->reset();
        $this->instance->setVars(array('foo' => 'bar'));
        $test = $this->instance->length();

        // result should be 1
        $this->assertEquals($test, 1, '"length" test failed.');

        $this->instance->reset();
        $lenght = $this->instance->length('foo');
        $this->assertEquals($lenght, 0, 'assert failed , the content is empty');
    }

    /**
     * remove
     *
     * @test
     */
    public function testremove()
    {
        $get = $this->instance->getVars();
        $remove = $this->instance->remove();
        $this->assertTrue($remove, 'assert failed, content removed failed');
        $valid = $this->instance->getVars();
        $this->assertNotEquals($remove, $valid, 'assert failed, the 2 variables are not equal - remove funcion failed');

        $set = $this->instance->setVars(array('fo' => 'bar', 'FOO' => 'FOO', 'TEST' => 'description'));
        $get = $this->instance->getVars();
        $remove = $this->instance->remove('FOO');
        $this->assertTrue($remove, 'assert failed, removed entry by "key" failed');
        $valid = $this->instance->getVars();
        $this->assertNotEquals($get, $valid, 'assert failed, the two variables are not equal - removed entry by "key" failed');
    }

    /**
     * setVarByReference
     *
     * @test
     */
    public function testsetVarByReference()
    {
        $test = 1;
        $this->instance->setVarByReference('foo', $test);
        $get = $this->instance->getVar('FOO');
        $this->assertEquals($get, $test, 'assert failed, value is not set');
        $test = 2;
        $get = $this->instance->getVar('FOO');
        $this->assertEquals($get, $test, 'assert failed, value is not set');

        $test = array(1 => 1);
        $this->instance->setVarByReference('FOO', $test);
        $get = $this->instance->getVar('FOO');
        $this->assertEquals($get, $test, 'assert failed, value is not set');
        $test[1] = 2;
        $get = $this->instance->getVar('FOO');
        $this->assertEquals($get, $test, 'assert failed, value is not set');

        $modified = array('foo' => 'yana');
        $this->instance->setVarByReference('FOO', $modified);
        $other = $this->instance->getVar('foo');
        $this->assertNotEquals($other, $get, 'assert failed, value is not set');
    }

    /**
     * To String
     *
     * @test
     */
    public function testToString()
    {
        $nonExist = new SML('resources/nonexist.sml');
        $toString = (string) $nonExist;
        $this->assertEquals("", $toString, 'Should return empty string if file does not exist');

        $string = (string) $this->instance;
        $this->assertType('string', $string, 'assert failed, valueis not from type string');
        $this->assertNotEquals(0, mb_strlen($string), 'assert failed , value is not empty');
    }

    /**
     *  test1
     *
     *  @test
     */
    public function test1()
    {
        $sml = new SML(CWD . 'resources/test.sml', CASE_UPPER);

        // supposed to return the whole array by reference
        $test = & $sml->getVarByReference();
        $this->assertType('array', $test, 'assert failed, get by reference" test failed');

        $test['FOO'] = 'bar';
        $test['foo'] = 'error';
        unset($test);

        // result should be 'bar' (not 'error')
        $test = $sml->getVar('foo');
        $this->assertEquals($test, 'bar', 'assert failed, set on reference" test failed.');

        // result should be true
        $test = $sml->exists('foo');
        $this->assertTrue($test, 'assert failed, "exists" test failed.');

        // result should be false
        $sml->reset();
        $test = $sml->exists('foo');
        $this->assertFalse($test, 'assert failed, "reset" test failed.');

        $sml->setVars(array('foo' => 'bar'));
        $test = $sml->length();

        // result should be 1
        $this->assertEquals($test, 1, 'assert faield, "length" test failed.');
    }

}

?>