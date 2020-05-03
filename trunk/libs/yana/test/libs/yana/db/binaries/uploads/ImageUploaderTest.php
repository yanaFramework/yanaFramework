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
class MyImageUploader extends \Yana\Db\Binaries\Uploads\ImageUploader
{
    public $params;

    protected function _createImageAndThumbnail(\Yana\Media\Image $image, $path, $thumbnailPath, $width, $height, $keepAspectRatio, array $background, $fileEnding, $fileTempName)
    {
        $this->params = \func_get_args();
    }

}

/**
 * @package  test
 */
class ImageUploaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Binaries\Configuration
     */
    protected $configuration;

    /**
     * @var \Yana\Db\Binaries\Uploads\MyImageUploader
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
        $this->object = new \Yana\Db\Binaries\Uploads\MyImageUploader($this->configuration);
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
     * @expectedException \Yana\Core\Exceptions\Files\InvalidImageException
     */
    public function testUploadInvalidMimeType()
    {
        $file = new \Yana\Http\Uploads\File('original', 'text/plain', __FILE__, 0, UPLOAD_ERR_OK);
        $settings = array();
        $this->object->upload($file, 'Test', $settings);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\InvalidImageException
     */
    public function testUploadFileIsNotAnImage()
    {
        $file = new \Yana\Http\Uploads\File('original', 'image/png', __FILE__, 0, UPLOAD_ERR_OK);
        $settings = array();
        $this->object->upload($file, 'Test', $settings);
    }

    /**
     * @test
     */
    public function testUpload()
    {
        $file = new \Yana\Http\Uploads\File('original', 'image/png', \CWD . 'resources/image/logo.png', 0, UPLOAD_ERR_OK);
        $settings = array(
            'width' => 200,
            'height' => 100,
            'ratio' => true,
            'background' => '#aabbcc'
        );
        $dir = $this->configuration->getDirectory();
        $this->assertSame($dir . '/Test.png', $this->object->upload($file, 'Test', $settings));
        $this->assertSame($dir . '/Test', $this->object->params[1], 'Image target path');
        $this->assertSame($dir . '/thumb.Test', $this->object->params[2], 'Thumbnail target path');
        $this->assertSame(200, $this->object->params[3], 'Setting width');
        $this->assertSame(100, $this->object->params[4], 'Setting height');
        $this->assertSame(true, $this->object->params[5], 'Setting aspect-ratio');
        $this->assertSame(array(170, 187, 204), $this->object->params[6], 'Setting background');
        $this->assertSame('png', $this->object->params[7], 'File type');
        $this->assertSame(\CWD . 'resources/image/logo.png', $this->object->params[8], 'Temporary file path');
    }

}
