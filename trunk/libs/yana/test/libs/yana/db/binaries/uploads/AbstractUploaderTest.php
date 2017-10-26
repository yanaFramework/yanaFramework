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

namespace Yana\Db\Binaries\Uploads;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 */
class MyUploader extends \Yana\Db\Binaries\Uploads\AbstractUploader
{

    public function getConfiguration()
    {
        return $this->_getConfiguration();
    }

    public function getTempName(array $file)
    {
        return $this->_getTempName($file);
    }

    public function getOriginalName(array $file)
    {
        return $this->_getOriginalName($file);
    }

}

/**
 * @package  test
 */
class AbstractUploaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Binaries\Uploads\MyUploader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Binaries\Uploads\MyUploader();
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
    public function testGetConfiguration()
    {
        $this->assertTrue($this->object->getConfiguration() instanceof \Yana\Db\Binaries\IsConfiguration);
    }

    /**
     * @test
     */
    public function testGetTempName()
    {
        $file = array('name' => 'ignored', 'tmp_name' => 'Test', 'error' => UPLOAD_ERR_OK);
        $this->assertSame($file['tmp_name'], $this->object->getTempName($file));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testGetTempNameNameMissing()
    {
        $this->object->getTempName(array());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testGetTempNameTmpNameMissing()
    {
        $file = array('name' => 'ignored');
        $this->object->getTempName($file);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\SizeException
     */
    public function testGetTempNameInvalidSizeException()
    {
        $file = array('name' => 'ignored', 'error' => UPLOAD_ERR_INI_SIZE);
        $this->object->getTempName($file);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\UploadFailedException
     */
    public function testGetTempNameUploadFailedException()
    {
        $file = array('name' => 'ignored', 'error' => UPLOAD_ERR_FILE_TYPE);
        $this->object->getTempName($file);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotWriteableException
     */
    public function testGetTempNameNotWriteableException()
    {
        $file = array('name' => 'ignored', 'error' => UPLOAD_ERR_OTHER);
        $this->object->getTempName($file);
    }

    /**
     * @test
     */
    public function testGetOriginalName()
    {
        $file = array('name' => 'Test');
        $this->assertSame('Test', $this->object->getOriginalName($file));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testGetOriginalNameInvalidArgumentException()
    {
        $this->object->getOriginalName(array());
    }

}
