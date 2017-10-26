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
    protected function _createImageAndThumbnail(\Yana\Media\Image $image, $path, $thumbnailPath, $width, $height, $keepAspectRatio, array $background, $fileEnding, $fileTempName)
    {
    }

}

/**
 * @package  test
 */
class ImageUploaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Binaries\Uploads\ImageUploader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Binaries\Uploads\MyImageUploader();
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
        $file = array(
            'error' => \UPLOAD_ERR_OK,
            'name' => 'original',
            'tmp_name' => __FILE__,
            'type' => 'text/plain'
        );
        $settings = array();
        $this->object->upload($file, 'Test', $settings);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\InvalidImageException
     */
    public function testUploadFileIsNotAnImage()
    {
        $file = array(
            'error' => \UPLOAD_ERR_OK,
            'name' => 'original',
            'tmp_name' => __FILE__,
            'type' => 'image/png'
        );
        $settings = array();
        $this->object->upload($file, 'Test', $settings);
    }

}
