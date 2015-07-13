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

namespace Yana\VDrive;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * Test implementation for class Registry
 *
 * @package test
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
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
        $this->registry = new \Yana\VDrive\Registry(CWD . $this->path);
        $this->registry->setAsGlobal();
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
     * retrieves vars from registry
     *
     * @test
     */
    public function testGetVars()
    {
       $var = $this->registry->getVars();
       $this->assertInternalType('array', $var, 'assert failed, the value should be of type array');
    }

    /**
     * retrieves vars from registry
     *
     * @test
     */
    public function testGetVar()
    {
       $this->assertFalse($this->registry->isVar('unknown.key'));
       $vars =& $this->registry->getVarsByReference();
       $vars['unknown'] = array('key' => 'Test');
       $this->assertEquals('Test', $this->registry->getVar('unknown.key')); // This must use the cache
    }

    /**
     * check for existence.
     *
     * @test
     */
    public function testIsVar()
    {
       $this->assertFalse($this->registry->isVar('my.key'));
       $this->assertFalse($this->registry->isVar('my key'));
       $this->registry->setVar('my key', 'test');
       $this->assertTrue($this->registry->isVar('my key'));
       $this->registry->setVar('my.key', 'test');
       $this->assertTrue($this->registry->isVar('my.key'));
    }

    /**
     * sets var on registry
     *
     * @test
     */
    public function testSetVar()
    {
        // check only if the element is set
        $this->registry->setVar('RULES', 'skin/rules/');
        $this->assertEquals('skin/rules/', $this->registry->getVar('RULES'));
    }

    /**
     * sets var on registry
     *
     * @test
     */
    public function testSetVars()
    {
        // check only if the element is set
        $this->assertEquals('skin/rules/', $this->registry->setVars(array('RULES' => 'skin/rules/'))->getVar('RULES'));
    }

    /**
     * merges the value at adresse $key with the provided array data
     *
     * @test
     */
    public function testMerge()
    {
        $key = 'RULES';
        $array1 = array(0 => 'a', 1 => 'a');
        $this->registry->setVar($key, $array1);
        $array2 = array(1 => 'b', 2 => 'c');
        $this->registry->mergeVars($key, $array2);
        $this->assertEquals(array('a', 'b', 'c'), $this->registry->getVar('RULES'));
    }

    /**
     * @test
     */
    public function testMergeVars()
    {
        // get all elements of the registry path
        $getAll = $this->registry->getVars();
        $this->assertInternalType('array', $getAll, 'assert failed, the value is not of type array');
        $this->assertNotEquals(0, count($getAll), 'assert failed , the array can not be empty');

        // select one and manipulate the entries
        $get = $this->registry->getVar('SKINDIR');
        $this->assertEquals('skins/', $get, 'assert faield, the expected value should be match the given');

        // set a new value
        $key = 'NEW_YANA';
        $this->registry->setVar($key, array('foo/.bar'));
        $getVar = $this->registry->getVar($key);
        $this->assertEquals('foo/.bar', $getVar[0], 'the given value is different too the expected - proppably setVar has failed');

        // try to manipulate the new value
        $array = array('foo/', 'bar/');
        $this->registry->mergeVars($key, $array);
        $this->assertEquals($array, $this->registry->getVar($key));

        // check for modifications
        $getVar = $this->registry->getVar($key);
        $this->assertInternalType('array', $getVar, 'assert failed, the expected value should be of type array');
        $this->assertEquals('foo/', $getVar[0], 'assert failed, the expected value must be "foo/"');
        $this->assertEquals('bar/', $getVar[1], 'assert failed, the expected value must be "bar/"');

        // try too add foobar into new_yana
        $this->registry->mergeVars($key, array('foo'), false);
        $getVar = $this->registry->getVar($key);

        // expected false because overwrite is set to false
        $this->assertFalse(in_array('foo', $getVar), 'assert failed, the value is not in array');

        // merge the NEW_YANA value with new entry "foobar" expected to overwrite the first argument
        $this->registry->mergeVars($key, array('foobar'), true);
        $getVar = $this->registry->getVar($key);

        // expected true because overwrite is set to true | the first arrgument in array will be overwrite
        $this->assertTrue(in_array('foobar', $getVar), 'assert failed, expected the "foobar" value in given array');
        $this->assertFalse(in_array('foo/', $getVar), 'assert failed, the "foo/" value should not be in given array');

        // set foo/ bar/ and foobar into NEW_YANA
        $array = array('foo/', 'bar/', 'foobar');
        $this->registry->mergeVars($key, $array);
        $getVar = $this->registry->getVar($key);

        // expected true because overwrite is set to true | the first arrgument in array will be overwrite
        $this->assertTrue(in_array('foobar', $getVar), 'assert failed, expected the "foobar" value in given array');
        $this->assertTrue(in_array('foo/', $getVar), 'assert failed, expected the "foo/" value in given array');
        $this->assertTrue(in_array('bar/', $getVar), 'assert failed, expected the "bar/" value in given array');

        // unset the NEW_YANA value
        $this->registry->unsetVar($key);
        $this->assertArrayNotHasKey('NEW_YANA', $this->registry->getVars(), 'assert failed, the "NEW_YANA" value does not exist');

        //check if the unseted key still exist
        $getVar = $this->registry->getVar($key);
        $this->assertFalse($getVar, 'assert failed, key does not exist');
    }

    /**
     * @test
     */
    public function testUnsetVars()
    {
        // set a new key and value of type integer
        $this->registry->setVar('bar', '150');
        $this->registry->setType('bar', 'integer');
        $getVarRef = $this->registry->getVarByReference('bar');
        $this->assertEquals(150, $getVarRef, 'assert failed, the given value should be an integer "150"');

        // verify the instance
        $getInstance = Registry::getGlobalInstance();
        $this->assertInternalType('object', $getInstance, 'assert failed, the value should be of type object');
        $this->assertTrue($getInstance instanceof Registry, 'assert failed, the expected value should be an instance of Registry');
        $this->assertEquals($this->registry, $getInstance, 'assert failed, sdfsd');

        // unset all
        $this->registry->unsetVars();
        // check if all entries all removed
        $get = $this->registry->getVars();
        $this->assertEquals(0, count($get), 'assert failed, expected an empty array');

        // set a new key
        $array = array('ID1'=>array('ID2'=>'foo'), 'ID3');
        $this->registry->setVar('BAR', $array);
        $this->assertEquals($array, $this->registry->getVar('BAR'));

        // get the value of the the key
        $getValue = $this->registry->getVarByReference('BAR.ID1.ID2');
        $this->assertEquals('foo', $getValue, 'assert failed, expected "foo" as value');
    }

}

?>