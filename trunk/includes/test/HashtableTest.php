<?php
/**
 * PHPUnit test-case: Hashtable
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
 * Test class for Hashtable
 *
 * @package  test
 */
class HashtableTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * @var    array
     * @access protected
     */
    protected $array=array(
                           'ID1'=>'abc',
                           'ID2'=>'def',
                           'ID3'=> array(
                                       'test'=>'result'),
                           'ID4'=>true
                          );

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
     * get
     *
     * @test
     */
    public function testGet()
    {
        $get = Hashtable::get($this->array, '*');
        $this->assertType('array', $get, 'assert failed, value is not from type array');

        $get = Hashtable::get($this->array, 'ID2');
        $this->assertType('string', $get, 'assert failed, value is not from type string');
        //expected string "def"
        $this->assertEquals('def', $get, 'assert failed, expected result "def" has failed wrong value given');

        $get = Hashtable::get($this->array, 'ID3.test');
        $this->assertType('string', $get, 'assert failed, value is not from type string');
        //expected string "result"
        $this->assertEquals('result', $get, 'assert failed, expected result "result" has failed wrong value given');

        $get = Hashtable::get($this->array, 'ID3.idks');
        $this->assertType('null', $get, 'assert failed, value is not from type null');
        //expected null
        $this->assertEquals(null, $get, 'assert failed, expected result "result" has failed wrong value given');
    }

    /**
     * set by reference
     *
     * @test
     */
    public function testSetByReference()
    {
        $array = array(1, 2, 3);
        $setByReference = Hashtable::setByReference($this->array, 'ID2', $array);
        $this->assertTrue($setByReference, 'assert failed, set new key value has failed');

        $validate = Hashtable::get($this->array, 'ID2');
        $getAll = Hashtable::get($this->array, '*');

        $this->assertEquals($validate, $array, 'assert failed, vaules should be equal');
        $this->assertArrayHasKey('ID2', $getAll, 'assert failed, "setByReferences" has failed- key doesnt exist in array');

        $array[0] = 2;
        $validate = Hashtable::get($this->array, 'ID2.0');
        $this->assertEquals($validate, 2, 'assert failed, value is not a reference');

        $array = array('a' => 3);
        $setByReference = Hashtable::setByReference($this->array, '*', $array);
        $array['a'] = 4;
        $validate = Hashtable::get($this->array, 'a');
        $this->assertEquals($validate, 4, 'unable to set a value by reference using wildcard "*"');
        $this->assertEquals($this->array['a'], 4, 'array should be set to referenced value');

    }

    /**
     * SetByReferenceInvalidArgument
     *
     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    public function testSetByReferenceInvalidArgument()
    {
        $value = 'entry';
        $setByReference = Hashtable::setByReference($this->array, '*', $value);
    }

    /**
     * set
     * @todo set a value or array without key(or non existing key) doesnt work exemple : $set = Hashtable::set($this->array, '*', array);
     * @test
     */
    public function testSet()
    {
        $copyOfArray = $this->array;
        $set = Hashtable::set($this->array, 'ID1', 'work');
        $this->assertTrue($set, 'assert failed, set new value has failed');        
        $validate = Hashtable::get($this->array, '*');
        $this->assertArrayHasKey('ID1', $validate, 'assert failed, key doesnt exist on array');
        $this->assertEquals('work', $validate['ID1'], 'assert failed, "setByReferences" has failed, expected value doesn exist in key "ID5"');

        $array = array(1, 2, 3);
        $set = Hashtable::setByReference($copyOfArray, 'ID5', $array);
        //$array[] = 4;
        $this->assertTrue($set, 'assert failed, set new value has failed ');
        $get = Hashtable::get($copyOfArray, '*');
        $this->assertArrayHasKey('ID5', $get, 'assert failed, key doesnt exist on array');
        $this->assertEquals($array, $get['ID5'], 'assert failed, "setByReferences" has failed, expected value doesn exist in key "ID5"');
        //$this->assertEquals($copyOfArray, $array);
    }

    /**
     * set type
     *
     * test
     */
    public function testSetType()
    {
        $setType = Hashtable::setType($this->array, 'ID1', 'integer');
        $this->assertTrue($setType, 'assert failed, change typ for givin value has failed');
        $get = Hashtable::get($this->array, '*');
        $this->assertType('integer', $get['ID1'], 'assert failed, the value is not from type integer');

        $setType = Hashtable::setType($this->array, 'ID4', 'string');
        $get = Hashtable::get($this->array, '*');
        $this->assertTrue($setType, 'assert failed, set typ for value failed, value doesnt exist');
        $this->assertType('string', $get['ID4'], 'assert failed, the valie is not from type string');
    }

    /**
     * SetTypeInvalidArgument
     *
     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    public function testSetTypeInvalidArgument()
    {
        $typ = array();
        $setType = Hashtable::setType($this->array, 'ID4', $typ);
    }

    /**
     * exist
     *
     * @test
     */
    public function testExists()
    {
        $exist = Hashtable::exists($this->array, 'ID1');
        $this->assertTrue($exist, 'assert failed, key should be exist in givin array');

        $notExist = Hashtable::exists($this->array, 'ID9');
        $this->assertFalse($notExist, 'assert failed, key cant be exist in givin array');
    }

    /**
     * remove
     *
     * @test
     */
    public function testRemove()
    {
        //success remove by key
        $remove = Hashtable::remove($this->array, 'ID1');
        $this->assertTrue($remove, 'assert failed, remove key operation has failed');

        // success
        $remove = Hashtable::remove($this->array, 'ID3.test');
        $this->assertTrue($remove, 'assert failed, remove key operation has failed');

        //failed column doesnt exist
        $remove = Hashtable::remove($this->array, 'ID3.def');
        $this->assertFalse($remove, 'assert failed, removed "key" doesnt exist');

        //success remove all
        $remove = Hashtable::remove($this->array, '*');
        $this->assertTrue($remove, 'assert failed, remove array has failed');
    }

    /**
     * chnange case
     *
     * @test
     */
    public function testChangeCase()
    {
        // success default CASE_LOWER
        $changeCase = Hashtable::changeCase($this->array);
        $this->assertType('array', $changeCase, 'assert failed, value is not from type array');
        $this->assertNotEquals($changeCase, $this->array, 'assert failed, the variables cant be equal');

        // success CASE_LOWER
        $changeCase = Hashtable::changeCase($this->array, 0);
        $this->assertType('array', $changeCase, 'assert failed, value is not from type array');
        $this->assertNotEquals($changeCase, $this->array, 'assert failed, the variables cant be equal');

        // success CASE_LOWER
        $changeCase = Hashtable::changeCase($this->array, false);
        $this->assertType('array', $changeCase, 'assert failed, value is not from type array');
        $this->assertNotEquals($changeCase, $this->array, 'assert failed, the variables cant be equal');

        // success CASE_UPPER
        $changeCase = Hashtable::changeCase($this->array, CASE_UPPER);
        $this->assertType('array', $changeCase, 'assert failed, value is not from type array');
        $this->assertNotEquals($changeCase, $this->array, 'assert failed, the variables cant be equal');

        // success CASE_UPPER
        $changeCase = Hashtable::changeCase($this->array, 1);
        $this->assertType('array', $changeCase, 'assert failed, value is not from type array');
        $this->assertNotEquals($changeCase, $this->array, 'assert failed, the variables cant be equal');

        // success CASE_UPPER
        $changeCase = Hashtable::changeCase($this->array, true);
        $this->assertType('array', $changeCase, 'assert failed, value is not from type array');
        $this->assertNotEquals($changeCase, $this->array, 'assert failed, the variables cant be equal');
    }

    /**
     * merge
     *
     * @test
     */
    public function testMerge()
    {
        $array1 = array('abc' => 'rot',
                        'cdf' => 'jkl',
                        'test'=>'test',
                        'new'=>array('color'=>'ftf'),
                         2,
                         4);
                     
        $array2 = array('a',
                        'b',
                        'ssd' => 'fds',
                        'cdf' => 'jkl',
                        'test'=>'Test',
                        'new'=>array('color'=>'ere',
                                     'asa'=>'upper'),
                         4,
                        'old'=>array('gfg'=>'lower'));
        $a = array();
        $b = array();

        $merge = Hashtable::merge($array1, $array2);
        $this->assertNotEquals($merge, $array1, 'assert failed, the variables cant be equal');
        $this->assertNotEquals('test', $merge['test'], 'assert failed, the varialbles should not be equal');
        $this->assertEquals('Test', $merge['test'], 'assert failed, the varialbles should be equal');

        $merge = Hashtable::merge($array1, $b);
        $this->assertEquals($merge, $array1, 'assert failed, the varialbles should be equal');
        $merge = Hashtable::merge($a, $array2);
        $this->assertEquals($merge, $array2, 'assert failed, the varialbles should be equal');
    }

    public function test1()
    {
        $array1 = $this->array;
        $array2 = array('iop' => array('ads' => 'qwerty',
                                      'ggh' => 'lik'),
                        'lkjh' => 'red',
                        'nrub' => array(10,
                                           20,
                                           8,
                                           65),
                        'ID1' => 'ngr');

        $caseUpper = Hashtable::changeCase($array2, 1);
        $this->assertType('array', $caseUpper, 'assert failed, value is not from type array');
        $this->assertNotEquals($caseUpper, $array2, 'assert failed, the values cant be equal');

        $value = 'blue';
        $set = Hashtable::set($caseUpper, 'LKJH', $value);
        $this->assertTrue($set, 'assert failed , value is not set for giving key');

        $caseLower = Hashtable::changeCase($caseUpper, 0);
        $this->assertType('array', $caseLower, 'assert failed, value is not from type array');
        $this->assertNotEquals($caseLower, $array2, 'assert failed, the values cant be equal');

        // check if color sets to blue
        $get = Hashtable::get($caseLower, '*');
        $this->assertEquals('blue', $get['lkjh'], 'assert failed, the variables should be equal');

        // take a key from the org array and chack if exist in the actually
        $this->assertArrayHasKey('lkjh', $array2, 'assert failed, the key must have a match in validate array ');
        $this->assertArrayNotHasKey('id1', $array2, 'assert failed, the giving key doesnt exist in array');

        // set type for one key and try to write/ set a value with a different data type
        $setType = Hashtable::setType($caseLower, 'id1', 'integer');
        $this->assertTrue($setType, 'assert failed, the typ of the key doesnt change');
        $this->assertType('integer', $caseLower['id1'], 'assert failed, the value is not from type integer');
        
        $value = 'description';
        $set = Hashtable::set($caseLower, 'id1', $value);
        $this->assertTrue($set, 'assert failed, set new value for giving key has failed');
        $this->assertEquals($value, $caseLower['id1'], 'assert failed, the variables should be equal');

        // verifi with orginal array
        $this->assertNotEquals($caseLower, $array2, 'assert failed, the 2 variables cant be equal');

        $arrayBeforeRemove = $caseLower;
        // remove the key "iop"
        $remove = Hashtable::remove($caseLower, 'iop');
        $this->assertTrue($remove, 'assert failed, remove the key has failed');
        $this->assertNotEquals($arrayBeforeRemove, $caseLower, 'assert failed, the variables cant be equal');

        // remove "lkjh"
        $remove = Hashtable::remove($caseLower, 'lkjh');
        $this->assertTrue($remove, 'assert failed, remove all entries has failed');
        // remove "nrub"
        $remove = Hashtable::remove($caseLower, 'nrub');
        $this->assertTrue($remove, 'assert failed, remove all entries has failed');
        // remove "id1"
        $remove = Hashtable::remove($caseLower, 'id1');
        $this->assertTrue($remove, 'assert failed, remove all entries has failed');

        $this->assertType('array', $caseLower, 'assert failed, value is not from type array');        
        $this->assertEquals(count($caseLower), 0, 'assert failed, remove all entries from the array has failed');

        $marge = Hashtable::merge($array1, $caseLower);
        $this->assertEquals($array1, $marge, 'assert failed, the variables should be equal');
        $upperKeys = Hashtable::changeCase($marge, CASE_UPPER);
        $this->assertNotEquals($upperKeys, $marge, 'assert failed, the values are not equal');
    }
}
?>