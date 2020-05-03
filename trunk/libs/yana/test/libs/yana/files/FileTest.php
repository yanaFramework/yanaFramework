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

namespace Yana\Files;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../include.php';

/**
 * @package  test
 */
class FileTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  File
     */
    protected $object;

    /**
     * @var  string
     */
    protected $source = 'resources/file.txt';

    /**
     * @var  string
     */
    protected $file = 'resources/file_copy.txt';

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
     * @expectedException \Yana\Core\Exceptions\Files\NotWriteableException
     * @test
     */
    public function testWriteNotWriteableException()
    {
        // try with non existing path
        $newFile = new \Yana\Files\File(CWD . 'resources/nonExistfile.txt');
        $newFile->write();
    }

    /**
     * Write modified file
     *
     * Function write() should prevent you from overwriting a file which has recently
     * been modified by some third-party.
     *
     * @expectedException  \Yana\Core\Exceptions\Files\UncleanWriteException
     * @test
     */
    public function testWriteModifiedFile()
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
        $filesize = $this->object->_getFilesize();
        $this->assertInternalType('integer', $filesize, 'not valid type for "$filesize" expecting "integer"');

        // try with non existing path
        $newFile = new \Yana\Files\File('resources/nonExistfile.txt');
        $getFileSize = $newFile->_getFilesize();
        $this->assertSame(0, $getFileSize, 'assert failed, source doesnt exist');
    }

    /**
     * @test
     */
    public function testFailSafeWrite()
    {
        $failSafeWrite = $this->object->failSafeWrite();
        $this->assertTrue($failSafeWrite, 'safe write failed');
    }

    /**
     * @test
     */
    public function testCopy()
    {
        $this->object->copy(CWD . $this->file, true, false, 0777);
        $this->assertTrue(is_file(CWD . $this->file), 'copy failed');
        unlink(CWD . $this->file);
    }

    /**
     * @test
     */
    public function testCopyWithoutFileName()
    {
        $dir = CWD . 'cache/';
        $fileName = $dir . 'file.txt';
        $this->assertNull($this->object->copy($dir, false, false, 0777));
        $this->assertTrue(is_file($fileName), 'copy failed');
        unlink($fileName);
    }

    /**
     * @test
     */
    public function testCopyRecursive()
    {
        $dir = CWD . 'cache/test/';
        $fileName = $dir . 'file.txt';
        $this->assertNull($this->object->copy($dir, false, true, 0777));
        $this->assertTrue(is_file($fileName), 'copy failed');
        unlink($fileName);
        rmdir($dir);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotFoundException
     */
    public function testCopyNotFoundException()
    {
        $dir = CWD . 'cache/test/';
        $fileName = $dir . 'file.txt';
        $this->object->copy($dir, false, false, 0777);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotWriteableException
     */
    public function testCopyNotWriteableException()
    {
        clearstatcache(true, $this->object->getPath());
        $this->object->copy($this->object->getPath(), true);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotWriteableException
     */
    public function testCopyNotWriteableException2()
    {
        $path = $this->object->getPath();
        \chmod($path, 0444);
        $handle = \fopen($path, 'r');
        try {
            $this->object->copy($this->object->getPath(), true);
        } catch (Exception $e) {
            throw $e;
        } finally {
            \chmod($path, 0777);
            fclose($handle);
        }
    }

    /**
     * Copy
     *
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\AlreadyExistsException
     */
    public function testCopyAlreadyExistsException()
    {
        $dir = dirname($this->object->getPath()) . '/';
        $this->object->copy($dir, false);
    }

    /**
     * Copy with empty argument
     *
     * @expectedException  \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testCopyEmptyArgument()
    {
        $this->object->copy('', true, false, 0777);
    }

    /**
     * Copy Invalid Argument
     *
     * @expectedException  \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testCopyInvalidArgumentUpper()
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
    public function testCopyInvalidArgumentLower()
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
        $delete = $this->object->delete();
        $this->assertTrue($delete, 'delete failed');
        $this->assertTrue(!\file_exists($this->object->getPath()));
    }

    /**
     * Delete
     *
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotWriteableException
     */
    public function testDeleteNotWriteableException()
    {
        $h = \fopen($this->object->getPath(), 'w');
        try {
            $this->object->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Invalid Argument
     *
     * @test
     */
    public function testDeleteInvalidArgument()
    {
        // try with non existing path
        $newFile = new \Yana\Files\File('resources/nonExistfile.txt');
        $delete = $newFile->delete();
        $this->assertTrue($delete, 'assert failed, source doesnt exist');
    }

    /**
     * Create
     *
     * @test
     */
    public function testCreate()
    {
        if ($this->object->exists()) {
            $this->object->delete();
        }
        $this->object->create();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotWriteableException
     */
    public function testCreateNotWriteableException()
    {
        if ($this->object->exists()) {
            $this->object->delete();
        }
        $u = umask(0777);
        try {
            $this->object->create();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            umask($u);
            chmod($this->object->getPath(), 0777);
        }
    }

    /**
     * Create
     *
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\AlreadyExistsException
     */
    public function testCreateAlreadyExistsException()
    {
        if ($this->object->exists(CWD . $this->file)) {
            $this->object->delete();
        }
        $this->object->create(CWD . $this->file);
        $this->object->create(CWD . $this->file);
    }

}
