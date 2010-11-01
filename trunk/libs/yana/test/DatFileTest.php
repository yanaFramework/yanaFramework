<?php
/**
 * PHPUnit test-case: DatFile
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
 * Test class for DatFile
 * 
 * @package  test
 */
class DatFileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    DatFile
     * @access protected
     */
    protected $object;
    /**
     * @var    string
     * @access protected
     */
    protected $source = 'resources/test.dat';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     * 
     * @access protected
     */
    protected function setUp()
    {
        $this->object = new DatFile(CWD . $this->source);
        $this->object->read();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    { 
     $this->object->reset();
        // intentionally left blank
    }

    /**
     * get lines
     *
     * @test
     */
    public function testGetLines()
    {
        $validate = $this->object->getLines();
        $this->assertType('array', $validate, 'getLines() is expected to return a value of type array.');
        $testArray = array('name' => 'a', 'test' => 'ewa', 'soMething' => 2);
        $this->object->removeLine();
        $this->object->appendLine($testArray);

        $testArray = array(array_change_key_case($testArray, CASE_UPPER));
        $rows = $this->object->getLines();
        $this->assertEquals($rows, $testArray, 'getLines() should return an array of arrays containing the previously set row.');
    }

    /**
     * get line
     *
     * @test
     */
    public function testGetLine()
    {
        $testArray = array('name' => 'a', 'test' => 'ewa', 'soMething'=>2);
        $this->object->appendLine($testArray);

        $testArray = array_change_key_case($testArray, CASE_UPPER);
        $selectrow = $this->object->getLine(0);
        $this->assertEquals($selectrow, $testArray, 'two variables "$selectrow" and "$testArrayOne" are not equal - update "row 0" has been failed');
    }

    /**
     * Get Invalid Argument
     *
     * @expectedException  PHPUnit_Framework_Error
     * @test
     */
    function testGetLineInvalidArgument()
    {
        $selectrow = $this->object->getLine(10);
        $this->assertType('array', $selectrow, 'assert failed, value is not from type array');
        $this->assertEquals(count($selectrow), 0, 'assert failed, there is no line 10 in file');
    }

    /**
     * Get Invalid Argument1
     *
     * @expectedException  PHPUnit_Framework_Error
     * @test
     */
    function testGetLineInvalidArgument1()
    {
        $selectrow = $this->object->getLine(-10);
        $this->assertFalse($selectrow, 'assert failed, first argument must be a possitive integer');
    }
    
    /**
     * append line
     * 
     * @test
     */
    public function testAppendLine()
    {
        $this->object->removeLine();
        $dataset = array('eins' => 1, 'zwei' => 2, 'next' => 'back', 'NeW' => 'olD');
        $this->object->appendLine($dataset);

        //check valid data set
        $dataset = array_change_key_case($dataset, CASE_UPPER);
        $valid = $this->object->getLine(0);
        $this->assertEquals($valid, $dataset, "Unable to append first line");
        
        $test1 = array('1' => 'a', 'TEST' => 'true');
        $this->object->appendLine($test1);

        $this->assertNotEquals($this->object->getLine(1), $test1, "Second prepended line should not match second line");
        $this->assertEquals($this->object->getLine(0), $test1, "Expected line to be prepended to top of file.");

        $this->object->appendLine($test1, true);
        $this->assertNotEquals($this->object->getLine(1), $test1, "Second appended line should match second line");
    }

    /**
     * set line
     * 
     * @test
     */
    public function testSetLine()
    {
        $newEntry = array('test' => 'update1', 'old' => 'gh1', 'somethig' => '1new');          
        $this->object->setLine(0, $newEntry);

        $newEntry = array_change_key_case($newEntry, CASE_UPPER);
        $getRow = $this->object->getLine(0);
        $this->assertEquals($getRow, $newEntry, 'two variables "$getRow" and "$newEntry" are not equal - update "row 0" has been failed');
    }

    /**
     * set line
     *
     * @expectedException  PHPUnit_Framework_Error
     * @test
     */
    public function testSetLineInvalidArgument()
    {
        // line 2 doesnt exist
        $this->object->setLine(2, $newEntry);
    }

    /**
     * remove
     * 
     * @test
     */
    public function testRemoveLine()
    {
        $selectFirstRow = $this->object->getLine(0);

        $this->object->removeLine(0);

        // select first row after remove
        $rowSelect = $this->object->getLine(0);
        $this->assertNotEquals($selectFirstRow, $rowSelect, 'two variables "$selectFirstRow" and "$rowSelect" are equal - remove "row 0" has been failed');

        $this->object->removeLine();
        $this->assertEquals($this->object->getContent(), '', 'File is truncated and should be empty.');
    }

    /**
     * remove
     *
     * @expectedException  OutOfBoundsException
     * @test
     */
    public function testRemoveLineInvalidArgument()
    {
        // line 2 doesnt exist
        $tryToRemove = $this->object->removeLine(2);
        $this->assertFalse($tryToRemove, 'assert "remove()" failed, line 2 doesnt exist');
    }

    /**
     * length
     * 
     * @test
     */
    public function testLength()
    {
        $this->object->reset();
        
        // result should be 2
        $this->assertEquals($this->object->length(), 2, '"length" test failed.');
        
        //add new row and try again 
        $dataset = array('eins' => 'bla', 'drei' => 'bla', 'next' => 'bla');
        $this->object->appendLine($dataset);
        
        // result should be 3
        $this->assertEquals($this->object->length(), 3, '"length" test failed.');
    }
}
?>