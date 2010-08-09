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
    protected $object;

    /**
     * @var     string
     * @access  protected
     */
    protected $path = 'resources/fileread.txt';

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
        $this->object = new TextFile(CWD . $this->path);
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
    }

    /**
     * @todo Implement testSetContent().
     */
    public function testSetContent()
    {
        $content = "a\nb";
        $this->object->setContent($content);
        $line1 = $this->object->getLine(0);
        $line2 = $this->object->getLine(1);
        $newContent = $this->object->getContent();
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
        $get = $this->object->getLine(1);
        $valid = 'the second entry.';
        $this->assertEquals($get, $valid, 'assert failed, the two variables are equal');

        $get = $this->object->getLine(3);
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
        $getBefore = $this->object->getContent();
        $this->assertType('string', $getBefore, 'there is no content');

        $content = 'this is the yana description';
        $this->object->appendLine($content);
        $result = $getBefore . "\n" . $content;

        $getAfter = $this->object->getContent();

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
        $this->object->setLine(1, $line);
        $result = $this->object->getLine(1);
        $this->assertEquals($line, $result, 'getLine() should return the value previously set by setLine()');
    }

    /**
     * write
     *
     * @test
     */
    public function testWrite()
    {
        $getBefore = $this->object->getContent();

        $content = 'das steht in dem file';
        $insert = $this->object->setContent($content);
        $this->object->write();

        $getAfter = file_get_contents($this->object->getPath());
        file_put_contents($this->object->getPath(), $getBefore);

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
       $this->object->setContent($content);
       $this->object->removeLine(0);
       $this->assertEquals($this->object->getContent(), '', 'remove() failed');
    }

    /**
     * remove line error handling
     *
     * remove a nonexisting key
     *
     * @expectedException OutOfBoundsException
     * @test
     */
    public function testRemoveLineOutOfBounds()
    {
       $this->object->removeLine(2);
    }

    /**
     * Length
     *
     * @test
     */
    public function testLength()
    {
       $length = $this->object->length();
       $this->assertType('integer', $length, 'not valid type "integer"');

       // try with non existing path
       $newFile = new TextFile('resources/nonExistfile.txt');
       $length = $newFile->length();
       $this->assertEquals($length, 0, 'assert failed, source doesnt exist');
       unset($newFile);
    }
}
?>