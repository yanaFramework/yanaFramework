<?php
/**
 * PHPUnit test-case: TextFile
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
 * Test class for TextFile
 *
 * @package  test
 */
class TextFileTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var     FileReadonly
     * @access  protected
     */
    protected $_object;

    /**
     * @var     string
     * @access  protected
     */
    protected $_path = 'resources/fileread.txt';

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        // intentionally left blank
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->_object = new TextFile(CWD . $this->_path);
        $this->_object->read();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        $this->_object->reset();
    }

    /**
     * @todo Implement testSetContent().
     */
    public function testSetContent()
    {
        $content = "a\nb";
        $this->_object->setContent($content);
        $line1 = $this->_object->getLine(0);
        $line2 = $this->_object->getLine(1);
        $newContent = $this->_object->getContent();
        $this->assertEquals($newContent, $content, 'expecting getContent() to return same value as previously set by setContent()');
        $this->assertEquals('a', $line1, 'expecting getLine() to return first line as set by setContent()');
        $this->assertEquals('b', $line2, 'expecting getLine() to return any line as set by setContent()');
    }

    /**
     * get line
     *
     * @test
     */
    public function testGetLine()
    {
        $get = $this->_object->getLine(1);
        $valid = 'the second entry.';
        $this->assertEquals($get, $valid, 'assert failed, the two variables are equal');

        $get = $this->_object->getLine(3);
        $this->assertFalse($get, 'assert failed, no entry for expected line');

        $nonExistFile = new TextFile('resources/nonExistfile.txt');
        $get = $nonExistFile->getLine(1);
        $this->assertEquals(mb_strlen($get), 0, 'assert failed , expected result is 0');
        unset($nonExistFile);
    }

    /**
     * append line
     *
     * @test
     */
    public function testAppendLine()
    {
        $getBefore = $this->_object->getContent();
        $this->assertType('string', $getBefore, 'there is no content');

        $content = 'this is the yana description';
        $this->_object->appendLine($content);
        $result = $getBefore . "\n" . $content;

        $getAfter = $this->_object->getContent();

        $this->assertNotEquals($getBefore, $getAfter, 'two variables "$getBefore" and "$getAfter" are equal - insert has been failed');
        $this->assertEquals($result, $getAfter, 'File content should match prior content plus appended line.');
    }

    /**
     * set line content
     *
     * @test
     */
    public function testSetLine()
    {
        $line = 'a';
        $this->_object->setLine(1, $line);
        $result = $this->_object->getLine(1);
        $this->assertEquals($line, $result, 'getLine() should return the value previously set by setLine()');
    }

    /**
     * remove line error handling
     *
     * remove a nonexisting key
     *
     * @expectedException \Yana\Core\Exceptions\OutOfBoundsException
     * @test
     */
    public function testSetLineOutOfBounds()
    {
       $this->_object->setLine(-1, 'a');
    }

    /**
     * write
     *
     * @test
     */
    public function testWrite()
    {
        $getBefore = $this->_object->getContent();

        $content = 'das steht in dem file';
        $insert = $this->_object->setContent($content);
        $this->_object->write();

        $getAfter = file_get_contents($this->_object->getPath());
        file_put_contents($this->_object->getPath(), $getBefore);

        $this->assertNotEquals($getBefore, $getAfter, 'Write failed. File has not changed.');
        $this->assertEquals($content, $getAfter, 'Write failed. File should contain the previously set contents.');
    }

    /**
     * remove line
     *
     * @test
     */
    public function testRemoveLine()
    {
       $content = 'das ist das haus vom nikolaus';
       $this->_object->setContent($content);
       $this->_object->removeLine(0);
       $this->assertEquals($this->_object->getContent(), '', 'remove() failed');
    }

    /**
     * remove line error handling
     *
     * remove a nonexisting key
     *
     * @expectedException \Yana\Core\Exceptions\OutOfBoundsException
     * @test
     */
    public function testRemoveLineOutOfBounds()
    {
       $this->_object->removeLine(2);
    }

    /**
     * Length
     *
     * @test
     */
    public function testLength()
    {
       $length = $this->_object->length();
       $this->assertType('integer', $length, 'not valid type "integer"');

       // try with non existing path
       $newFile = new TextFile('resources/nonExistfile.txt');
       $length = $newFile->length();
       $this->assertEquals($length, 0, 'assert failed, source doesnt exist');
       unset($newFile);
    }

}

?>