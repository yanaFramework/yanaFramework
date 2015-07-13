<?php
/**
 * PHPUnit test-case: File
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
 * Test class for File
 *
 * @package  test
 *
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /** @var    File */ protected $object;
    /** @var  string */ protected $source = 'resources/file.txt';
    /** @var  string */ protected $file = 'resources/file_copy.txt';

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
        $this->object = new File(CWD . $this->source);
        // reload the file -> read is calling 
        $this->object->reset();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        if (file_exists(CWD . $this->file)) {
            unlink(CWD . $this->file);
        }
    }

    /**
     * Write Invalid Argument
     *
     * @expectedException \Yana\Core\Exceptions\NotWriteableException
     * @test
     */
    function testWriteNotWriteableException()
    {
        // try with non existing path
        $newFile = new File(CWD . 'resources/nonExistfile.txt');
        $newFile->write();
    }

    /**
     * Write modified file
     *
     * Function write() should prevent you from overwriting a file which has recently
     * been modified by some third-party.
     *
     * @expectedException  PHPUnit_Framework_Error
     * @test
     */
    function testWriteModifiedFile()
    {
        touch(CWD . $this->source);
        $this->object->write();
    }

    /**
     * Get File Size
     *
     * @test
     */
    public function testGetFilesize()
    {
        $filesize = $this->object->getFilesize();
        $this->assertInternalType('integer', $filesize, 'not valid type for "$filesize" expecting "integer"');

        // try with non existing path
        $newFile = new File('resources/nonExistfile.txt');
        $getFileSize = $newFile->getFilesize();
        $this->assertFalse($getFileSize, 'assert failed, source doesnt exist');
        unset($newFile);
    }

    /**
     * Fail Safe Write
     *
     * @test
     */
    public function testFailSafeWrite()
    {
        $failSafeWrite = $this->object->failSafeWrite();
        $this->assertTrue($failSafeWrite, 'safe write failed');
    }

    /**
     * Copy
     *
     * @test
     */
    public function testCopy()
    {
      $this->object->copy(CWD . $this->file, true, false, 0777);
      $this->assertTrue(is_file(CWD . $this->file), 'copy failed');
      unlink(CWD . $this->file);
    }

    /**
     * Copy with empty argument
     *
     * @expectedException  \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    function testCopyEmptyArgument()
    {
        $this->object->copy('', true, false, 0777);
    }

    /**
     * Copy Invalid Argument
     *
     * @expectedException  \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    function testCopyInvalidArgumentUpper()
    {
        $this->object->copy(CWD . $this->file, true, false, 01000);
        $this->fail('Must not accept argument mode with value > 0777');
    }

    /**
     * Copy Invalid Argument
     *
     * @expectedException  \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    function testCopyInvalidArgumentLower()
    {
        $this->object->copy(CWD . $this->file, true, false, 0);
        $this->fail('Must not accept argument mode with value < 1');
    }

    /**
     * Delete
     *
     * @test
     */
    public function testDelete()
    {
        $delete = $this->object->delete(CWD . $this->file);
        $this->assertTrue($delete, 'delete faild or file not exist');
    }

    /**
     * Delete Invalid Argument
     *
     * @expectedException  PHPUnit_Framework_Error
     * @test
     */
    function testDeleteInvalidArgument()
    {
        // try with non existing path
        $newFile = new File('resources/nonExistfile.txt');
        $delete = $newFile->delete();
        $this->assertFalse($delete, 'assert failed, source doesnt exist');
        unset($newFile);
    }

    /**
     * Create
     *
     * @test
     */
    public function testCreate()
    {
        if ($this->object->exists(CWD . $this->file)) {
            $this->object->delete();
        }
        $this->object->create(CWD . $this->file);
    }
}
?>