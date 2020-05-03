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
class MyFileUploader extends \Yana\Db\Binaries\Uploads\FileUploader
{
    protected function _createCompressedFile(string $path, string $filename, string $fileTempName, string $mimetype)
    {
    }

}

/**
 * @package  test
 */
class FileUploaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Binaries\Configuration
     */
    protected $configuration;

    /**
     * @var \Yana\Db\Binaries\Uploads\MyFileUploader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->configuration = new \Yana\Db\Binaries\Configuration();
        $this->configuration->setDirectory(CWD);
        $this->object = new \Yana\Db\Binaries\Uploads\MyFileUploader($this->configuration);
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
    public function testUpload()
    {
        $file = new \Yana\Http\Uploads\File('original', 'text/plain', __FILE__, 0, UPLOAD_ERR_OK);
        $this->assertSame($this->configuration->getDirectory() . '/Test.gz', $this->object->upload($file, 'Test'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\UploadFailedException
     */
    public function testUploadUploadFailedException()
    {
        $file = new \Yana\Http\Uploads\File('original', 'text/plain', 'nonexistingfile', 0, UPLOAD_ERR_OK);
        $this->object->upload($file, 'Test');
    }

}
