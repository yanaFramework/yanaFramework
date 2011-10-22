<?php
/**
 * PHPUnit test-case: FileReadonly
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
 * Test class for FileReadonly
 *
 * @package  test
 */
class FileReadonlyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    FileReadonly
     * @access protected
     */
    protected $object = null;

    /**
     * @var    resource
     * @access protected
     */
    protected $fileHandle = null;

    /**
     * @var    string
     * @access protected
     */
    protected $path = 'resources/fileread.txt';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->object = new FileReadonly(CWD . $this->path);
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
        if (isset($this->fileHandle)) {
            flock($this->fileHandle, LOCK_UN);
            fclose($this->fileHandle);
        }
    }

    /**
     * read
     *
     * @test
     * @expectedException  NotFoundException
     */
    public function testReadNotFoundException()
    {
        $nonExistFile = new FileReadonly('resources/nonExistfile.txt');
        $nonExistFile->read();
    }

    /**
     * read
     *
     * @test
     */
    public function testRead()
    {
        $this->object->read();
        $content = $this->object->getContent();
        $this->assertTrue(!empty($content), 'Unable to read content from file');
    }

    /**
     * read
     *
     * @test
     * @expectedException  NotReadableException
     */
    public function testReadLockedFile()
    {
        $path = $this->object->getPath();
        $read = $this->object->read();
        $this->fileHandle = fopen($path, 'rw');
        flock($this->fileHandle, LOCK_EX);
        $this->object->read(); // throws exception
        $this->fail("Function read() should be aware of locked file resource.");
    }

    /**
     * fail safe read
     *
     * @test
     */
    public function testFailSafeRead()
    {
        $this->object->failSafeRead();
        $content = $this->object->getContent();
        $this->assertTrue(!empty($content), 'Unable to read content from file');
    }

    /**
     * read
     *
     * @test
     * @expectedException  NotReadableException
     */
    public function testFailSafeReadLockedFile()
    {
        $path = $this->object->getPath();
        $read = $this->object->read();
        $this->fileHandle = fopen($path, 'rw');
        flock($this->fileHandle, LOCK_EX);
        $this->object->failSafeRead(); // throws exception
        $this->fail("Function failsafeRead() should be aware of locked file resource.");
    }

    /**
     * get content
     *
     * @test
     */
    public function testGetContent()
    {
        $fileContent = $this->object->getContent();
        $this->assertType('string', $fileContent, '"$fileContent" is not from type string');

        $nonExistFile = new FileReadonly('resources/nonExistfile.txt');
        $fileContent = $nonExistFile->getContent();
        $this->assertEquals(mb_strlen($fileContent), 0, 'assert failed , expected result is 0');
        unset($nonExistFile);
    }

    /**
     * is empty
     *
     * @test
     */
    public function testIsEmpty()
    {
        $empty = $this->object->isEmpty();
        // expected for not empty and loaded
        $this->assertFalse($empty, 'assert failed - "$empty" is true');

        $nonExistFile = new FileReadonly('resources/nonExistfile.txt');
        $empty = $nonExistFile->isEmpty();
        $this->assertTrue($empty, 'assert failed, source is empty');
        unset($nonExistFile);
    }

    /**
     * get crc32
     *
     * @test
     */
    public function testGetCrc32()
    {
        $crc = $this->object->getCrc32();
        $this->assertType('integer', $crc, 'Returned checksum must be an integer.');
        
        // valid 
        $validcrc = crc32(file_get_contents($this->object->getPath()));
        $this->assertEquals($crc, $validcrc, 'Returned checksum is not valid.');
    }

     /**
     * GetCrc32 Invalid Argument
     *
     * @expectedException  PHPUnit_Framework_Error
     * @test
     */
    function testGetCrc32InvalidArgument()

    {
        $crc = $this->object->getCrc32('readonly.txt');
        $this->assertFalse($crc, 'assert failed, first argument must be a string, and a filename');
    }

    /**
     * get md5
     *
     * @test
     */
    public function testGetMd5()
    {
        $md5 = $this->object->getMd5();
        $this->assertType('string', $md5, 'getMd5() is expected to return a string');

        // valid
        $validmd5 = md5_file($this->object->getPath());
        $this->assertEquals($md5, $validmd5, 'MD5 checksum return by getMD5() should match the result of md5_file()');
    }

    /**
     * Get MD5 Invalid file object
     *
     * @test
     */
    public function testGetMd5NonExistingfile()
    {
        $nonExistFile = new FileReadonly('resources/nonExistfile.txt');
        $md5 = $nonExistFile->getMd5();
        $this->assertFalse($md5, 'assert failed, source is empty');
        unset($nonExistFile);
    }

    /**
     * Get MD5 Invalid Argument
     *
     * @expectedException  PHPUnit_Framework_Error
     * @test
     */
    function testGetMd5InvalidArgument()

    {
        $md5 = $this->object->getMd5('readonly.txt');
        $this->assertFalse($md5, 'assert failed, first argument must be a string, and a filename');
    }
}
?>