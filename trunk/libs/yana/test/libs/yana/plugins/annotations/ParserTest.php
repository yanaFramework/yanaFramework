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

namespace Yana\Plugins\Annotations;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Annotations\Parser
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $text = '
            /**
             * Äöß.
             *
             * @access public
             * @param string $test1
             * @param array  $test2
             * {@test  Key1: Value, Key2: Value,
             *        Key3: Value}
             * {@test1}
             * @test2  Key1: Value, Key2: Value, Key3: Value
             * @test3 \Name\Space\ClassName
             * @test4 Key1: \Name\Space\ClassName
             * @test5
             * {@test6 Key: 4}
             * {@test6 Key: 5 }
             * {@test6 value }
             * {@test6 Key:}
             * {@test6}
             * {@test7 Key1:, Key2: value1, value2, value3, Key3: test }
             * @ignore
             * {invalid@nonsense}
             * {@missingbracket
             *@this wont work
             * @findme
             */
        ';
        $this->object = new \Yana\Plugins\Annotations\Parser($text);
    }

    /**
     * @test
     */
    public function testGetText()
    {
        $object = new Parser();
        $this->assertEquals('', $object->getText());
    }

    /**
     * @test
     */
    public function testSetText()
    {
        $text = "Test";
        $this->assertEquals($text, $this->object->setText($text)->getText());
    }

    /**
     * @test
     */
    public function testGetTag()
    {
        $this->assertEquals('public', $this->object->getTag('access'));
    }

    /**
     * @test
     */
    public function testGetTagComplex()
    {
        $array = array(
            'Key1' => 'Value',
            'Key2' => 'Value',
            'Key3' => 'Value'
        );
        $this->assertEquals($array, $this->object->getTag('test'));
        $this->assertEquals($array, $this->object->getTag('test2'));
    }

    /**
     * @test
     */
    public function testGetTagEmpty()
    {
        $this->assertTrue($this->object->getTag('test5'));
    }

    /**
     * @test
     */
    public function testGetTagDefault()
    {
        $this->assertSame('default', $this->object->getTag('nosuchtag', 'default'));
    }

    /**
     * @test
     */
    public function testGetIngore()
    {
        $this->assertTrue($this->object->getTag('ignore'));
    }

    /**
     * @test
     */
    public function testEmptySimpleTag()
    {
        $this->assertTrue($this->object->getTag('test5'));
    }

    /**
     * @test
     */
    public function testGetTagWithNamespace()
    {
        $array = array(
            'Key1' => '\Name\Space\ClassName'
        );
        $this->assertEquals($array, $this->object->getTag('test4'));
    }

    /**
     * @test
     */
    public function testGetTags()
    {
        $array = array(
            'string $test1',
            'array  $test2'
        );
        $this->assertEquals($array, $this->object->getTags('param'));
    }

    /**
     * @test
     */
    public function testGetTagsComplex()
    {
        $array = array(
            array('Key' => '4'),
            array('Key' => '5'),
            'value',
            array('Key' => true),
            true
        );
        $this->assertEquals($array, $this->object->getTags('test6'));
    }

    /**
     * @test
     */
    public function testGetTagsComplex2()
    {
        $this->assertEquals(array(array('Key1' => true, 'Key2' => 'value1, value2, value3', 'Key3' => 'test')), $this->object->getTags('test7'));
    }

    /**
     * @test
     */
    public function testGetTagsInvalidExamples()
    {
        $this->assertSame(array(), $this->object->getTags('nonsense'));
        $this->assertSame(array(), $this->object->getTags('missingbracket'));
        $this->assertSame(array(), $this->object->getTags('this'));
        $this->assertSame(array(true), $this->object->getTags('findme'));
    }

}
