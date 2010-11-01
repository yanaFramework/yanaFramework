<?php
/**
 * PHPUnit test-case: Registry
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
 * Test implementation for class Registry
 *
 * @package test
 */
class RegistryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Registry
     * @access protected
     */
    protected $registry;

    /**
     * @var    Registry
     *
     * @access protected
     */
    protected $path = 'resources/my.drive.xml';

    /**
     * @var    Registry
     *
     * @access protected
     */
    protected $baseDir = '/resources/';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->registry = new Registry(CWD.$this->path);
        $set = $this->registry->setAsGlobal();
        $this->registry->read();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        unset($this->registry);
    }

    /**
     * retrieves var from registry
     *
     * @test
     */
    public function testGetVar()
    {
       $var = $this->registry->getVar();
       $this->assertType('array', $var, 'assert failed, the value should be of type array');
    }

    /**
     * sets var on registry
     *
     * @covers Registry::setVarByReference()
     * @covers Registry::setVar()
     *
     * @test
     */
    public function testSetVar()
    {
        // check only if the element is set
        $set = $this->registry->setVar('RULES', 'skin/rules/');
        $this->assertTrue($set, 'assert failed, the variable should be set');
    }

    /**
     * merges the value at adresse $key with the provided array data
     *
     * @test
     */
    public function testMerge()
    {
        $key = 'RULES';
        $array = array('plugin');
        $merge = $this->registry->mergeVars($key, $array);
        $this->assertTrue($merge, 'assert failed, the variable has been not updated');
    }

    /**
     * test
     *
     * @test
     */
    public function test()
    {
        // get all elements of the registry path
        $getAll = $this->registry->getVar();
        $this->assertType('array', $getAll, 'assert failed, the value is not of type array');
        $this->assertNotEquals(0, count($getAll), 'assert failed , the array can not be empty');

        // select one and manipulate the entries
        $get = $this->registry->getVar('SKINDIR');
        $this->assertEquals('skins/', $get, 'assert faield, the expected value should be match the given');

        // set a new value
        $key = 'NEW_YANA';
        $set = $this->registry->setVar($key, array('foo/.bar'));
        $this->assertTrue($set, 'assert failed, the new value "NEW_YANA" is not set');
        $getVar = $this->registry->getVar($key);
        $this->assertEquals('foo/.bar', $getVar[0], 'the given value is different too the expected - proppably setVar has failed');

        // try to manipulate the new value
        $array = array('foo/', 'bar/');
        $merge = $this->registry->mergeVars($key, $array);
        $this->assertTrue($merge, 'assert failed, the update to the "NEW_YANA" var has failed');

        // check for modifications
        $getVar = $this->registry->getVar($key);
        $this->assertType('array', $getVar, 'assert failed, the expected value should be of type array');
        $this->assertEquals('foo/', $getVar[0], 'assert failed, the expected value must be "foo/"');
        $this->assertEquals('bar/', $getVar[1], 'assert failed, the expected value must be "bar/"');

        // try too add foobar into new_yana
        $merge = $this->registry->mergeVars($key, array('foobar'), false);
        $this->assertTrue($merge, 'assert failed, the value "foobar" is not merge with "NEW_YANA"');
        $getVar = $this->registry->getVar($key);

        // expected false because overwrite is set to false
        $this->assertFalse(in_array('foobar', $getVar), 'assert failed, the value is not in array');

        // merge the NEW_YANA value with new entry "foobar" expected to overwride the first argument
        $merge = $this->registry->mergeVars($key, array('foobar'), true);
        $this->assertTrue($merge, 'assert failed, the value "foobar" is not merge with "NEW_YANA"');
        $getVar = $this->registry->getVar($key);

        // expected true because overwrite is set to true | the first arrgument in array will be overwrite
        $this->assertTrue(in_array('foobar', $getVar), 'assert failed, expected the "foobar" value in given array');
        $this->assertFalse(in_array('foo/', $getVar), 'assert failed, the "foo/" value should not be in given array');

        // set foo/ bar/ and foobar into NEW_YANA
        $array = array('foo/', 'bar/', 'foobar');
        $merge = $this->registry->mergeVars($key, $array);
        $this->assertTrue($merge, 'assert failed, the values are not merge with "NEW_YANA"');
        $getVar = $this->registry->getVar($key);

        // expected true because overwrite is set to true | the first arrgument in array will be overwrite
        $this->assertTrue(in_array('foobar', $getVar), 'assert failed, expected the "foobar" value in given array');
        $this->assertTrue(in_array('foo/', $getVar), 'assert failed, expected the "foo/" value in given array');
        $this->assertTrue(in_array('bar/', $getVar), 'assert failed, expected the "bar/" value in given array');

        // unset the NEW_YANA value
        $unset = $this->registry->unsetVar($key);
        $this->assertTrue($unset, 'assert failed, unset var "NEW_YANA" has failed');
        $this->assertArrayNotHasKey('NEW_YANA', $this->registry->getVar(), 'assert failed, the "NEW_YANA" value does not exist');

        //check if the unseted key still exist
        $getVar = $this->registry->getVar($key);
        $this->assertFalse($getVar, 'assert failed, key does not exist');
    }

    /**
     * test2
     *
     * @test
     */
    public function test2()
    {
        // set a new key and value of type integer
        $setVar = $this->registry->setVar('bar', 150);
        $this->assertTrue($setVar, 'assert failed, the value is not set');
        $setType = $this->registry->setType('bar', 'integer');
        $this->assertTrue($setType, 'assert failed, the variable type is not set');
        $getVarRef = $this->registry->getVarByReference('bar');
        $this->assertEquals(150, $getVarRef, 'assert failed, the given value should be an integer "150"');

        // verify the instance
        $getInstance = Registry::getGlobalInstance();
        $this->assertType('object', $getInstance, 'assert failed, the value should be of type object');
        $this->assertTrue($getInstance instanceof Registry, 'assert failed, the expected value should be an instance of Registry');
        $this->assertEquals($this->registry, $getInstance, 'assert failed, sdfsd');

        // unset all
        $unset = $this->registry->unsetVar('*');
        $this->assertTrue($unset, 'assert failed, unset has failed');
        // check if all entries all removed
        $get = $this->registry->getVar();
        $this->assertEquals(0, count($get), 'assert failed, expected an empty array');

        // set a new key
        $array = array('ID1'=>array('ID2'=>'foo'), 'ID3');
        $set = $this->registry->setVar('BAR', $array);
        $this->assertTrue($set, 'assert failed, the new value is not set');

        // get the value of the the key
        $getValue = $this->registry->getVarByReference('BAR.ID1.ID2');
        $this->assertEquals('foo', $getValue, 'assert failed, expected "foo" as value');
    }
}
?>