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

namespace Yana\Http\Uploads;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class FileTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Http\Uploads\File
     */
    protected $emptyFile;

    /**
     * @var \Yana\Http\Uploads\File
     */
    protected $filledFile;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->emptyFile = new \Yana\Http\Uploads\File("", "", "", 0, -1);
        $this->filledFile = new \Yana\Http\Uploads\File("Äöß-name", "Äöß-type", "Äöß-path", 1234, 5678);
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
    public function testGetName()
    {
        $this->assertEquals("", $this->emptyFile->getName());
        $this->assertEquals("Äöß-name", $this->filledFile->getName());
    }

    /**
     * @test
     */
    public function testGetMimeType()
    {
        $this->assertEquals("", $this->emptyFile->getMimeType());
        $this->assertEquals("Äöß-type", $this->filledFile->getMimeType());
    }

    /**
     * @test
     */
    public function testGetTemporaryPath()
    {
        $this->assertEquals("", $this->emptyFile->getTemporaryPath());
        $this->assertEquals("Äöß-path", $this->filledFile->getTemporaryPath());
    }

    /**
     * @test
     */
    public function testGetSizeInBytes()
    {
        $this->assertEquals(0, $this->emptyFile->getSizeInBytes());
        $this->assertEquals(1234, $this->filledFile->getSizeInBytes());
    }

    /**
     * @test
     */
    public function testGetErrorCode()
    {
        $this->assertEquals(-1, $this->emptyFile->getErrorCode());
        $this->assertEquals(5678, $this->filledFile->getErrorCode());
    }

    /**
     * @test
     */
    public function testIsOkay()
    {
        $this->assertFalse($this->emptyFile->isOkay());
        $file = new \Yana\Http\Uploads\File("", "", "", 0, \Yana\Http\Uploads\ErrorEnumeration::OK);
        $this->assertTrue($file->isOkay());
    }

    /**
     * @test
     */
    public function testIsFileTooBigByIni()
    {
        $this->assertFalse($this->emptyFile->isFileTooBigByIni());
        $file = new \Yana\Http\Uploads\File("", "", "", 0, \Yana\Http\Uploads\ErrorEnumeration::INI_SIZE);
        $this->assertTrue($file->isFileTooBigByIni());
    }

    /**
     * @test
     */
    public function testIsFileTooBigByForm()
    {
        $this->assertFalse($this->emptyFile->isFileTooBigByForm());
        $file = new \Yana\Http\Uploads\File("", "", "", 0, \Yana\Http\Uploads\ErrorEnumeration::FORM_SIZE);
        $this->assertTrue($file->isFileTooBigByForm());
    }

    /**
     * @test
     */
    public function testIsIncompleteUpload()
    {
        $this->assertFalse($this->emptyFile->isIncompleteUpload());
        $file = new \Yana\Http\Uploads\File("", "", "", 0, \Yana\Http\Uploads\ErrorEnumeration::PARTIAL);
        $this->assertTrue($file->isIncompleteUpload());
    }

    /**
     * @test
     */
    public function testIsNotUploaded()
    {
        $this->assertFalse($this->emptyFile->isNotUploaded());
        $file = new \Yana\Http\Uploads\File("", "", "", 0, \Yana\Http\Uploads\ErrorEnumeration::NO_FILE);
        $this->assertTrue($file->isNotUploaded());
    }

    /**
     * @test
     */
    public function testIsMissingTemporaryDirectory()
    {
        $this->assertFalse($this->emptyFile->isMissingTemporaryDirectory());
        $file = new \Yana\Http\Uploads\File("", "", "", 0, \Yana\Http\Uploads\ErrorEnumeration::NO_TMP_DIR);
        $this->assertTrue($file->isMissingTemporaryDirectory());
    }

    /**
     * @test
     */
    public function testIsUnableToWriteFile()
    {
        $this->assertFalse($this->emptyFile->isUnableToWriteFile());
        $file = new \Yana\Http\Uploads\File("", "", "", 0, \Yana\Http\Uploads\ErrorEnumeration::CANT_WRITE);
        $this->assertTrue($file->isUnableToWriteFile());
    }

    /**
     * @test
     */
    public function testIsExtensionError()
    {
        $this->assertFalse($this->emptyFile->isExtensionError());
        $file = new \Yana\Http\Uploads\File("", "", "", 0, \Yana\Http\Uploads\ErrorEnumeration::EXTENSION);
        $this->assertTrue($file->isExtensionError());
    }

}
