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
class DirTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Dir
     */
    protected $existingDir;

    /**
     * @var Dir
     */
    protected $nonExistingDir;

    /**
     * @var string
     */
    protected $existingPath = 'resources';

    /**
     * @var string
     */
    protected $nonExistingPath = 'newresource';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {        
        $this->existingDir = new \Yana\Files\Dir(CWD . $this->existingPath);
        $this->nonExistingDir = new \Yana\Files\Dir(CWD . $this->nonExistingPath);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        if ($this->nonExistingDir->exists()) {
            $this->nonExistingDir->delete(true);
        }
    }

    /**
     * read
     *
     * @test
     */
    public function testRead()
    {
        try {
            $this->existingDir->read();
        } catch (\Exception $e) {
            $this->fail("Unable to read directory: " . $e->getMessage());
        }
    }

    /**
     * read from path that does'nt exist
     *
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testReadNotFoundException()
    {
        $this->nonExistingDir->read();
        $this->fail('Must throw exception when trying to read a path that does not exist.');

    }

    /**
     * get content
     *
     * @test
     */
    public function testGetContent()
    {
        // get list of all files
        $get = $this->existingDir->getContent();
        $this->assertInternalType('array', $get, 'assert "$get" failed , value is not from type array');

        // get file name by position 3 
        $selectedGet = $this->existingDir->getContent(3);
        $this->assertInternalType('string', $selectedGet, 'assert "$selectedGet" failed, value is not from type string');
    }

    /**
     * @test
     */
    public function testGetContentNotFoundException()
    {
        $this->assertSame(array(), $this->nonExistingDir->getContent());
    }

    /**
     * get filter
     *
     * @test
     */
    public function testGetFilter()
    {
        $getfilter = $this->existingDir->getFilter();
        $this->assertInternalType('string', $getfilter, 'assert "$getfilter" failed, value is not from type string');
    }

    /**
     * set filter
     *
     * @test
     */
    public function testSetFilter()
    {
        $filter = 'txt';
        // set filter
        $this->existingDir->setFilter($filter);
        $getFilter = $this->existingDir->getFilter();
        $this->assertEquals($filter, $getFilter, 'getFilter() is expected to return the value previously set with setFilter()');

        // check if selected file is into the dir list
        $fileList = scandir($this->existingDir->getPath());
        foreach ($this->existingDir->getContent() as $textFile)
        {
            $this->assertContains($textFile, $fileList, 'returned filename is not found in directory');
        }
    }

    /**
     * create
     * 
     * @test
     */
    public function testCreate()
    {
        $this->nonExistingDir->create();
        $this->assertTrue(is_dir($this->nonExistingDir->getPath()), 'Directory was not created.');
        $this->nonExistingDir->delete();
        $this->assertFalse(is_dir($this->nonExistingDir->getPath()), 'Directory was not deleted.');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\AlreadyExistsException
     */
    public function testCreateAlreadyExistsException()
    {
        $this->existingDir->create();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotWriteableException
     */
    public function testCreateNotWriteableException()
    {
        $dir = new \Yana\Files\Dir($_SERVER["DOCUMENT_ROOT"] . '/..');
        $dir->create();
    }
    
    /**
     * Create Invalid Argument
     *
     * @expectedException  \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testCreateInvalidArgumentLowerBounds()
    {
        $this->nonExistingDir->create(0);
        $this->fail('Function create must not accept int < 1 as argument');
    }

    /**
     * Create Invalid Argument
     *
     * @expectedException  \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testCreateInvalidArgumentUpperBounds()
    {
        $this->nonExistingDir->create(01000);
        $this->fail('Function create must not accept int < 0777 as argument');
    }

    /**
     * delete
     * 
     * @test
     */
    public function testDelete()
    {
        mkdir($this->nonExistingDir->getPath());
        $this->assertFalse($this->nonExistingDir->delete()->exists(), 'unable to delete directory');
        $this->assertFalse(is_dir($this->nonExistingDir->getPath()));
    }

    /**
     * delete
     * 
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotFoundException
     */
    public function testDeleteNotFoundException()
    {
        $this->nonExistingDir->delete();
    }

    /**
     * 
     * @test
     */
    public function testDeleteNotWriteableException()
    {
        mkdir($this->nonExistingDir->getPath());
        touch($this->nonExistingDir->getPath() . '/test.txt');
        try {
            $this->nonExistingDir->delete();
        } catch (\Yana\Core\Exceptions\Files\NotWriteableException $e) {
            // intentionally left blank
        }
        unlink($this->nonExistingDir->getPath() . '/test.txt');
        rmdir($this->nonExistingDir->getPath());
        if (!isset($e)) {
            $this->fail("Failed to assert that exception of type \Yana\Core\Exceptions\Files\NotWriteableException is thrown.");
        }
    }

    /**
     * delete
     * 
     * @test
     */
    public function testDeleteRecursive()
    {
        mkdir($this->nonExistingDir->getPath());
        touch($this->nonExistingDir->getPath() . '/test.txt');
        $this->assertSame($this->nonExistingDir, $this->nonExistingDir->delete(true));
        $this->assertFalse(is_dir($this->nonExistingDir->getPath()));
    }

    /**
     * to string
     * 
     * @test
     */
    public function testToString()
    {
        $getAll = $this->existingDir->getContent();
        $this->assertInternalType('array', $getAll, '"$getAll" is not of type array - assert failed');

        $toString = (string) $this->existingDir;
        $this->assertInternalType('string', $toString, '"toString" is not of type string - assert failed');
    }

    /**
     * to string
     * 
     * @test
     */
    public function testToStringNotExists()
    {
        $this->assertSame("Directory " . $this->nonExistingDir->getPath() . " does not exist\n", (string) $this->nonExistingDir);
    }

    /**
     * to string
     * 
     * @test
     */
    public function testToStringNotLoaded()
    {
        $this->assertSame("Directory " . $this->existingDir->getPath() . " is not loaded\n", (string) $this->existingDir);
    }

    /**
     * is empty
     *
     * @test
     */
    public function testIsEmpty()
    {  
       $isEmpty = $this->existingDir->isEmpty();
       // expected true
       $this->assertTrue($isEmpty, 'assert "$isEmpty" failed -  value is false');

       $getSelected = $this->existingDir->getContent();
       $this->assertInternalType('array', $getSelected, '"getSelected" is not from type array - assert failed');
       $empty = $this->existingDir->isEmpty();
       //expected false
       $this->assertFalse($empty, 'assert "$empty" failed - value is true' );
    }

    /**
     * length
     *
     * @test
     */
    public function testLength()
    {
        $length = $this->existingDir->length();
        //expected 0 
        $this->assertEquals(0, $length, 'assert failed, expecting result for length() need to be 0');

        $this->existingDir->getContent();
        $testLength = $this->existingDir->length();
        $this->assertNotEquals($length, $testLength, 'assert failed, the two variables cant be valid');

        $getSelected = $this->existingDir->getContent($testLength);
        $this->assertInternalType('null', $getSelected, 'assert "$getSelected" failed, value is not from type null');
        
        //expected last file
        $testLength -= 1;
        $getLast = $this->existingDir->getContent($testLength);
        $this->assertInternalType('string', $getLast, 'assert "getLast" failed, value is not from type string');
    }

    /**
     * dirlist
     *
     * @test
     */
    public function testDirlist()
    {
        // read all txt entries
        $dirList = $this->existingDir->listFiles('*.txt');
        $expected = array();
        foreach (glob($this->existingDir->getPath() . '/*.txt') as $path)
        {
            $expected[] = basename($path);
        }
        $this->assertInternalType('array', $dirList);
        $this->assertGreaterThanOrEqual(1, count($dirList));
        $this->assertEquals($expected, $dirList, 'directory listing with filter *.txt should match directory contents');

        // read without set a filter
        $dirList = $this->existingDir->setFilter()->listFiles();
        $expected = array();
        foreach (scandir($this->existingDir->getPath()) as $path)
        {
            if (!is_dir($this->existingDir->getPath() . '/' . $path)) {
                $expected[] = $path;
            }
        }
        $this->assertInternalType('array', $dirList);
        $this->assertGreaterThanOrEqual(1, count($dirList));
        $this->assertEquals($expected, $dirList, 'directory listing should match directory contents');

        // choose more file types
        $dirList = $this->existingDir->listFiles('*.txt|*.xml|*.dat');
        $expected = array();
        foreach (glob($this->existingDir->getPath() . '/*.{txt,xml,dat}', GLOB_BRACE) as $path)
        {
            $expected[] = basename($path);
        }
        sort($expected);
        $this->assertInternalType('array', $dirList);
        $this->assertGreaterThanOrEqual(1, count($dirList));
        $this->assertEquals($expected, $dirList, 'directory listing with filter *.txt, *.xml, *.dat should match directory contents');
    }

    /**
     * get size
     *
     * @test
     */
    public function testGetSize()
    {
        $size = $this->existingDir->getSize(false);
        $scanDirSize = 0;
        // reduce by 2 for bogus-entries '.' and '..'
        foreach (scandir(CWD . $this->existingPath) as $file)
        {
            $file = CWD . $this->existingPath . DIRECTORY_SEPARATOR . $file;
            if (is_file($file)) {
                $scanDirSize += filesize($file);
            }
        }
        $this->assertInternalType('int', $size, 'expecting getSize() to return result of type integer');
        $this->assertEquals($size, $scanDirSize, "Size does not match the size of the files in the directory");
    }

    /**
     * GetSize Invalid Argument1
     *
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotFoundException
     */
    public function testGetSizeNotFoundException()
    {
        // try with non exist Dir
        $newDir = new \Yana\Files\Dir('nonexistDir');
        $newDir->getSize();
    }

    /**
     * exists
     *
     * @test
     */
    public function testExists()
    {
        $exist = $this->existingDir->exists();
        $this->assertTrue($exist, 'exists() is expected to return true on existing directories');

        $exist = $this->nonExistingDir->exists();
        $this->assertFalse($exist, 'exists() is expected to return false on non-existing directories');
    }

    /**
     * copy
     *
     * @test
     */
    public function testCopy()
    {
        $destDir = $this->nonExistingDir->getPath();
        assert(!is_dir($destDir), 'Target-directory already exists!');
        $this->existingDir->copy($destDir, true, 0766, false, '*.txt|*.sml');
        $this->assertTrue(is_dir($destDir), 'Copied directory does not exist.');
    }

    /**
     * Copy Invalid Argument
     *
     * @expectedException  \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testCopyInvalidArgumentUpper()
    {
        $this->nonExistingDir->copy($this->nonExistingDir->getPath(), true, 01000);
        $this->fail('Third argument must not be bigger than 0777.');
    }

    /**
     * Copy Invalid Argument
     *
     * @expectedException  \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testCopyInvalidArgumentLower()
    {
        $this->nonExistingDir->copy($this->nonExistingDir->getPath(), true, 0);
        $this->fail('Third argument must not be smaller than 1.');
    }

    /**
     * Copy Invalid Argument 2
     *
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     * @test
     */
    public function testCopyAlreadyExists()
    {
        $this->existingDir->copy($this->existingDir->getPath(), false);
        $this->fail('May not overwrite an existing directory.');
    }

}
