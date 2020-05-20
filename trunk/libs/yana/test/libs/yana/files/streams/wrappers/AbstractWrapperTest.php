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

namespace Yana\Files\Streams\Wrappers;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyAbstractWrapper extends \Yana\Files\Streams\Wrappers\AbstractWrapper
{
    // intentionally left blank
}

/**
 * @package  test
 */
class AbstractWrapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Files\Streams\Wrappers\AbstractWrapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Files\Streams\Wrappers\MyAbstractWrapper();
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
    public function test__call()
    {
        $this->object->setResource(123);
        $this->assertSame(123, $this->object->stream_cast(''));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\UndefinedMethodException
     */
    public function test__callUndefinedMethodException()
    {
        $this->assertFalse($this->object->noSuchThing());
    }

    /**
     * @test
     */
    public function test__get()
    {
        $this->assertFalse($this->object->context);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\UndefinedPropertyException
     */
    public function test__getUndefinedPropertyException()
    {
        $this->object->noSuchThing;
    }

    /**
     * @test
     */
    public function test__set()
    {
        $this->assertSame(123, $this->object->context = 123);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\UndefinedPropertyException
     */
    public function test__setUndefinedPropertyException()
    {
        $this->object->noSuchThing = 1;
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testRenameFileOrDirectory()
    {
        $this->object->renameFileOrDirectory('', '');
    }

    /**
     * @test
     */
    public function testSetResource()
    {
        $this->assertNull($this->object->setResource(123));
        $this->assertSame(123, $this->object->getResource(''));
    }

    /**
     * @test
     */
    public function testGetResource()
    {
        $this->assertFalse($this->object->getResource(''));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testCloseFile()
    {
        $this->object->closeFile();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testIsEndOfFile()
    {
        $this->object->isEndOfFile();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testFlushFileContents()
    {
        $this->object->flushFileContents();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testLockFile()
    {
        $this->object->lockFile(0);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testSetMetaData()
    {
        $this->object->lockFile(0);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testOpenFile()
    {
        $test = "";
        $this->object->openFile('', '', 0, $test);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testReadFile()
    {
        $this->object->readFile(0);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testSeekInFile()
    {
        $this->object->seekInFile(0);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testSetOption()
    {
        $this->object->setOption(0, 0, 0);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testGetFileStats()
    {
        $this->object->getFileStats();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testGetPositionInFile()
    {
        $this->object->getPositionInFile();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testTruncateFile()
    {
        $this->object->truncateFile(0);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testWriteFile()
    {
        $this->object->writeFile('');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testRemoveFile()
    {
        $this->object->removeFile('');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testGetUrlStats()
    {
        $this->object->getUrlStats('', 0);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testCloseDirectory()
    {
        $this->object->closeDirectory();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testOpenDirectory()
    {
        $this->object->openDirectory('', 0);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testReadDirectory()
    {
        $this->object->readDirectory();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testRewindDirectory()
    {
        $this->object->rewindDirectory();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testMakeDirectory()
    {
        $this->object->makeDirectory('', '', 0);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testRemoveDirectory()
    {
        $this->object->removeDirectory('', 0);
    }

}
