<?php
/**
 * PHPUnit test-case.
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

namespace Yana\Util;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../include.php';

/**
 * @package  test
 */
class HashtableTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  array
     */
    protected $array = array(
        'ID1' => 'abc',
        'ID2' => 'def',
        'ID3' => array(
            'test' => 'result'),
        'ID4' => true
    );

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        // intentionally left blank
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
     * @test
     */
    public function testGet()
    {
        $get = Hashtable::get($this->array, '*');
        $this->assertInternalType('array', $get);

        $get = Hashtable::get($this->array, 'ID2');
        $this->assertInternalType('string', $get);
        //expected string "def"
        $this->assertEquals('def', $get, 'expected result "def" has failed wrong value given');

        $get = Hashtable::get($this->array, 'ID3.test');
        $this->assertInternalType('string', $get);
        //expected string "result"
        $this->assertEquals('result', $get, 'expected result "result" has failed wrong value given');

        $get = Hashtable::get($this->array, 'ID3.idks');
        $this->assertInternalType('null', $get);
        //expected null
        $this->assertEquals(null, $get, 'expected result "result" has failed wrong value given');
    }

    /**
     * @test
     */
    public function testSetByReference()
    {
        $array = array(1, 2, 3);
        Hashtable::setByReference($this->array, 'ID2', $array);

        $validate = Hashtable::get($this->array, 'ID2');
        $getAll = Hashtable::get($this->array, '*');

        $this->assertEquals($validate, $array, 'vaules should be equal');
        $this->assertArrayHasKey('ID2', $getAll, '"setByReferences" has failed- key doesnt exist in array');

        $array[0] = 2;
        $validate = Hashtable::get($this->array, 'ID2.0');
        $this->assertEquals($validate, 2, 'value is not a reference');

        $array = array('a' => 3);
        Hashtable::setByReference($this->array, '*', $array);
        $array['a'] = 4;
        $validate = Hashtable::get($this->array, 'a');
        $this->assertEquals($validate, 4, 'unable to set a value by reference using wildcard "*"');
        $this->assertEquals($this->array['a'], 4, 'array should be set to referenced value');

    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    public function testSetByReferenceInvalidArgument()
    {
        $value = 'entry';
        Hashtable::setByReference($this->array, '*', $value);
    }

    /**
     * @test
     */
    public function testSet()
    {
        $copyOfArray = $this->array;
        Hashtable::set($this->array, 'ID1', 'work');
        $validate = Hashtable::get($this->array, '*');
        $this->assertArrayHasKey('ID1', $validate);
        $this->assertEquals('work', $validate['ID1']);

        Hashtable::set($this->array, 'non-existing', 'test');
        $validate = Hashtable::get($this->array, '*');
        $this->assertArrayHasKey('non-existing', $validate);
        $this->assertEquals('test', $validate['non-existing']);

        $array = array(1, 2, 3);
        Hashtable::setByReference($copyOfArray, 'ID5', $array);
        $get = Hashtable::get($copyOfArray, '*');
        $this->assertArrayHasKey('ID5', $get);
        $this->assertEquals($array, $get['ID5'], '"setByReferences" has failed, expected value doesn exist in key "ID5"');
    }

    /**
     * @test
     */
    public function testSetType()
    {
        $setType = Hashtable::setType($this->array, 'ID1', 'integer');
        $this->assertTrue($setType, 'change type for given value has failed');
        $get = Hashtable::get($this->array, '*');
        $this->assertInternalType('integer', $get['ID1']);

        $setType = Hashtable::setType($this->array, 'ID4', 'string');
        $get = Hashtable::get($this->array, '*');
        $this->assertTrue($setType, 'set type for value failed, value doesnt exist');
        $this->assertInternalType('string', $get['ID4']);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    public function testSetTypeInvalidArgument()
    {
        $typ = array();
        $setType = Hashtable::setType($this->array, 'ID4', $typ);
    }

    /**
     * @test
     */
    public function testExists()
    {
        $exist = Hashtable::exists($this->array, 'ID1');
        $this->assertTrue($exist, 'key should be exist in given array');

        $notExist = Hashtable::exists($this->array, 'ID9');
        $this->assertFalse($notExist, 'key cant be exist in given array');
    }

    /**
     * @test
     */
    public function testRemove()
    {
        //success remove by key
        $remove = Hashtable::remove($this->array, 'ID1');
        $this->assertTrue($remove, 'remove key operation has failed');

        // success
        $remove = Hashtable::remove($this->array, 'ID3.test');
        $this->assertTrue($remove, 'remove key operation has failed');

        //failed column doesnt exist
        $remove = Hashtable::remove($this->array, 'ID3.def');
        $this->assertFalse($remove, 'removed "key" doesnt exist');

        //success remove all
        $remove = Hashtable::remove($this->array, '*');
        $this->assertTrue($remove, 'remove array has failed');
    }

    /**
     * @test
     */
    public function testChangeCase()
    {
        // success default CASE_LOWER
        $changeCase = Hashtable::changeCase($this->array);
        $this->assertInternalType('array', $changeCase);
        $this->assertNotEquals($changeCase, $this->array);

        // success CASE_LOWER
        $changeCase = Hashtable::changeCase($this->array, 0);
        $this->assertInternalType('array', $changeCase);
        $this->assertNotEquals($changeCase, $this->array);

        // success CASE_UPPER
        $changeCase = Hashtable::changeCase($this->array, CASE_UPPER);
        $this->assertInternalType('array', $changeCase);
        $this->assertNotEquals($changeCase, $this->array);

        // success CASE_UPPER
        $changeCase = Hashtable::changeCase($this->array, 1);
        $this->assertInternalType('array', $changeCase);
        $this->assertNotEquals($changeCase, $this->array);
    }

    /**
     * @test
     */
    public function testMerge()
    {
        $array1 = array(
            'abc' => 'rot',
            'cdf' => 'jkl',
            'test' => 'test',
            'new' => array('color' => 'ftf'),
            2,
            4
        );

        $array2 = array(
            'a',
            'b',
            'ssd' => 'fds',
            'cdf' => 'jkl',
            'test' => 'Test',
            'new' => array(
                'color' => 'ere',
                'asa' => 'upper'
            ),
            4,
            'old' => array('gfg' => 'lower')
        );
        $a = array();
        $b = array();

        $merge = Hashtable::merge($array1, $array2);
        $this->assertNotEquals($merge, $array1);
        $this->assertNotEquals('test', $merge['test']);
        $this->assertEquals('Test', $merge['test']);

        $merge = Hashtable::merge($array1, $b);
        $this->assertEquals($merge, $array1);
        $merge = Hashtable::merge($a, $array2);
        $this->assertEquals($merge, $array2);
    }

    /**
     * @test
     */
    public function testCloneArray()
    {
        $array1 = array(
            'adc',
            'def',
            'ghjk',
            'pio',
            'tqasd'
        );
        $array2 = array(
            'adc' => array(
                'def',
                'ghjk',
                'pio',
                'tqasd'
            )
        );

        $result = Hashtable::cloneArray($array1);
        $this->assertInternalType('array', $result);
        $this->assertEquals($array1, $result);

        $result = Hashtable::cloneArray($array2);
        $this->assertInternalType('array', $result);
        $this->assertEquals($array2, $result);
    }

    /**
     * Tests: get, set, merge, changeType, changeCase and remove.
     *
     * @test
     */
    public function testCombined()
    {
        $array1 = $this->array;
        $array2 = array(
            'iop' => array(
                'ads' => 'qwerty',
                'ggh' => 'lik'
            ),
            'lkjh' => 'red',
            'nrub' => array(
                10,
                20,
                8,
                65
            ),
            'ID1' => 'ngr'
        );

        $caseUpper = Hashtable::changeCase($array2, 1);
        $this->assertInternalType('array', $caseUpper);
        $this->assertNotEquals($caseUpper, $array2);

        $value = 'blue';
        Hashtable::set($caseUpper, 'LKJH', $value);

        $caseLower = Hashtable::changeCase($caseUpper, 0);
        $this->assertInternalType('array', $caseLower);
        $this->assertNotEquals($caseLower, $array2);

        // check if color sets to blue
        $get = Hashtable::get($caseLower, '*');
        $this->assertEquals('blue', $get['lkjh']);

        // take a key from the org array and chack if exist in the actually
        $this->assertArrayHasKey('lkjh', $array2, 'the key must have a match in validate array');
        $this->assertArrayNotHasKey('id1', $array2, 'the giving key doesnt exist in array');

        // set type for one key and try to write/ set a value with a different data type
        $setType = Hashtable::setType($caseLower, 'id1', 'integer');
        $this->assertTrue($setType, 'the typ of the key doesnt change');
        $this->assertInternalType('integer', $caseLower['id1'], 'the value is not from type integer');

        $value = 'description';
        Hashtable::set($caseLower, 'id1', $value);
        $this->assertEquals($value, $caseLower['id1']);

        // verifi with orginal array
        $this->assertNotEquals($caseLower, $array2);

        $arrayBeforeRemove = $caseLower;
        // remove the key "iop"
        $remove = Hashtable::remove($caseLower, 'iop');
        $this->assertTrue($remove, 'remove the key has failed');
        $this->assertNotEquals($arrayBeforeRemove, $caseLower);

        // remove "lkjh"
        $remove = Hashtable::remove($caseLower, 'lkjh');
        $this->assertTrue($remove, 'remove all entries has failed');
        // remove "nrub"
        $remove = Hashtable::remove($caseLower, 'nrub');
        $this->assertTrue($remove, 'remove all entries has failed');
        // remove "id1"
        $remove = Hashtable::remove($caseLower, 'id1');
        $this->assertTrue($remove, 'remove all entries has failed');

        $this->assertInternalType('array', $caseLower);
        $this->assertEquals(count($caseLower), 0, 'remove all entries from the array has failed');

        $merge = Hashtable::merge($array1, $caseLower);
        $this->assertEquals($array1, $merge);
        $upperKeys = Hashtable::changeCase($merge, CASE_UPPER);
        $this->assertNotEquals($upperKeys, $merge);
    }

    /**
     * @test
     */
    public function testToXMLWithNull()
    {
        $this->assertSame("", \Yana\Util\Hashtable::toXML(null));
    }

    /**
     * @test
     */
    public function testToXMLUpperCase()
    {
        $array = array(
            'a' => 1,
            'B' => array(
                'b',
                'c'
            ),
            'cD' => 4
        );
        $encoding = iconv_get_encoding("internal_encoding");
        $expected = '<?xml version="1.0" encoding="' . $encoding . '"?><array id="XML"><integer id="A">1</integer>' .
            '<array id="B"><string id="0">b</string><string id="1">c</string></array><integer id="CD">4</integer></array>';
        $xml = Hashtable::toXML($array, 'xml', CASE_UPPER);
        $this->assertSame($expected, \preg_replace('/[\r\f\n\t]/s', '', $xml));
    }

    /**
     * @test
     */
    public function testToXMLLowerCase()
    {
        $array = array(
            'a' => 1,
            'B' => array(
                'b',
                'c'
            ),
            'cD' => 4
        );
        $encoding = iconv_get_encoding("internal_encoding");
        $expected = '<?xml version="1.0" encoding="' . $encoding . '"?><array id="xml"><integer id="a">1</integer>' .
            '<array id="b"><string id="0">b</string><string id="1">c</string></array><integer id="cd">4</integer></array>';
        $xml = Hashtable::toXML($array, 'xml', CASE_LOWER);
        $this->assertSame($expected, \preg_replace('/[\r\f\n\t]/s', '', $xml));
    }

    /**
     * Array to XML conversion
     *
     * Calls function Hashtable::toXML().
     *
     * @test
     */
    public function testToXML()
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

    /**
     * @test
     */
    public function testToXMLBool()
    {
        // read file and write to an array
        $array = array(
            'Bool1' => true,
            'Bool2' => false
        );

        $encoding = iconv_get_encoding("internal_encoding");
        $expected = '<?xml version="1.0" encoding="' . $encoding . '"?><array id="xml"><boolean id="Bool1">true</boolean>' .
            '<boolean id="Bool2">false</boolean></array>';
        $xml = Hashtable::toXML($array, 'xml');
        $this->assertSame($expected, \preg_replace('/[\r\f\n\t]/s', '', $xml));
    }

    /**
     * @test
     */
    public function testToXMLObject()
    {
        $object = new \stdClass();
        $object->string = 'test';
        $object->BOOL = true;
        $object->Integer = 1;
        $array = array(
            'o' => $object
        );

        $encoding = iconv_get_encoding("internal_encoding");
        $expected = '<?xml version="1.0" encoding="' . $encoding . '"?><array id="xml"><object id="o" class="stdClass">' .
            '<string id="string">test</string><boolean id="BOOL">true</boolean><integer id="Integer">1</integer></object></array>';
        $xml = Hashtable::toXML($array, 'xml');
        $this->assertSame($expected, \preg_replace('/[\r\f\n\t]/s', '', $xml));
    }

    /**
     * @test
     */
    public function testQuickSearch()
    {
        $array = array('test');
        $this->assertSame(0, Hashtable::quickSearch($array, 'test'));
    }

    /**
     * @test
     */
    public function testQuickSearch2()
    {
        $array = array('test');
        $this->assertFalse(Hashtable::quickSearch($array, 'no such key'));
    }

    /**
     * @test
     */
    public function testQuickSearch3()
    {
        $array = array('abcd', 'abc');

        $result = Hashtable::quickSearch($array, 'abcd');
        $this->assertSame(0, $result);
    }

    /**
     * @test
     */
    public function testQuickSearch4()
    {
        $array = array('abcdabcd', 'abc');

        $result = Hashtable::quickSearch($array, 'abcdabcd');
        $this->assertSame(0, $result);

    }

    /**
     * @test
     */
    public function testQuickSearch5()
    {
        $array = array('c', 'y', 'd');
        $result = Hashtable::quickSearch($array, 'y');
        $this->assertSame(1, $result);
    }

}
