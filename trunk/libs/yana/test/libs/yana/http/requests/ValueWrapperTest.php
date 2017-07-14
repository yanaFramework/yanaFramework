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

namespace Yana\Http\Requests;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ValueWrapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Http\Requests\ValueWrapper
     */
    protected $emptyWrapper;

    /**
     * @var \Yana\Http\Requests\ValueWrapper
     */
    protected $filledWrapper;

    /**
     * @var array
     */
    protected $values;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->emptyWrapper = new \Yana\Http\Requests\ValueWrapper();
        $this->values = array('Value' => '1234', 'unsafe' => \YANA_LEFT_DELIMITER . 'test' . \YANA_RIGHT_DELIMITER);
        $this->filledWrapper = new \Yana\Http\Requests\ValueWrapper($this->values);
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
    public function testHas()
    {
        $this->assertFalse($this->emptyWrapper->has('value'));
        $this->assertTrue($this->filledWrapper->has('Value'));
        $this->assertTrue($this->filledWrapper->has('value'));
    }

    /**
     * @test
     */
    public function testValue()
    {
        $this->assertTrue($this->emptyWrapper->value('value')->isNull());
        $this->assertTrue($this->emptyWrapper->value('value', 'default')->is('default'));
        $this->assertTrue($this->filledWrapper->value('Value')->is($this->values['Value']));
        $this->assertTrue($this->filledWrapper->value('value')->is($this->values['Value']));
    }

    /**
     * @test
     */
    public function testIsEmpty()
    {
        $this->assertTrue($this->emptyWrapper->isEmpty());
        $this->assertFalse($this->filledWrapper->isEmpty());
    }

    /**
     * @test
     */
    public function testAsUnsafeArray()
    {
        $this->assertEquals(array(), $this->emptyWrapper->asUnsafeArray());
        $this->assertEquals(\array_change_key_case($this->values), $this->filledWrapper->asUnsafeArray());
    }

    /**
     * @test
     */
    public function testAsArrayOfStrings()
    {
        $this->assertEquals(array(), $this->emptyWrapper->asArrayOfStrings());
        $expected = array('value' => '1234', 'unsafe' => '&#' . ord(\YANA_LEFT_DELIMITER) . ';test&#' . ord(\YANA_RIGHT_DELIMITER) . ';');
        $this->assertEquals($expected, $this->filledWrapper->asArrayOfStrings());
    }

}
