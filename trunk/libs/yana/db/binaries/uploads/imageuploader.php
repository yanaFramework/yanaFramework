<?php
/**
 * YANA library
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
 * @package  yana
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

namespace Yana\Db\Binaries\Uploads;

/**
 * Handle image uploads.
 *
 * @package     yana
 * @subpackage  db
 * @ignore
 */
class ImageUploader extends \Yana\Db\Binaries\Uploads\AbstractUploader
{

    /**
     * The purpose of this method is to store images in a file pool.
     *
     * If the file already exists, it will get replaced.
     *
     * Returns the path to the uploaded file.
     *
     * PHP-Code:
     * <code>
     * // get a random filename
     * $column = $db->schema->{'my_table'}->{'my_file'};
     * $fileId = \Yana\Db\Blob::getNewFileId($column);
     * // and assign it to your row to update/insert
     * $row['my_file'] = $fileId;
     *
     * // get image/thumbnail settings
     * $settings = $column->getImageSettings();
     *
     * // upload the image
     * $helper = new \Yana\Db\Binaries\Uploads\ImageUploader();
     * $filename = $helper->upload($_FILES['my_file'], $fileId, $settings);
     * // and update/insert the row as usual
     * $db->insert("my_table", $row);
     * </code>}.
     *
     * @param   array   $file      item taken from array $_FILES
     * @param   string  $fileId    name of target file
     * @param   array   $settings  int    width       image width in pixel,
     *                             int    height      image height in pixel,
     *                             bool   ratio       keep aspect ratio when resizing?,
     *                             string background  RGB color as hex-value (e.g. #ffffff)
     * @return  string
     * @throws  \Yana\Core\Exceptions\Files\InvalidImageException  when the uploaded file was no valid image
     */
    public function upload(array $file, $fileId, array $settings)
    {
        $dir = $this->_getConfiguration()->getDirectory();
        $fileTempName = $this->_getTempName($file);
        $filename = $this->_getOriginalName($file);

        // check mime-type of uploaded file
        $fileType = 'png';
        if (!empty($file['type'])) {
            if (!preg_match('/^image\/(\w+)$/s', $file['type'], $fileType)) {
                $message = "The uploaded file has an invalid MIME-type.";
                $level = UPLOAD_ERR_FILE_TYPE;
                $error = new \Yana\Core\Exceptions\Files\InvalidImageException($message, $level);
                throw $error->setFilename($filename);
            }
            $fileType = $fileType[1];
        }

        /* name of output files */
        $path = "{$dir}/{$fileId}";
        $thumbnailPath = "{$dir}/thumb.{$fileId}";
        assert('is_string($path); // Wrong argument type for argument 2. String expected');
        assert('is_string($thumbnailPath); // Wrong argument type for argument 3. String expected');

        /*
         * process image
         *
         * Note: this also checks the file content and will
         * set a "broken" flag if the image is not valid.
         */
        $image = new \Yana\Media\Image($fileTempName, $fileType);
        /*
         * check if the "broken" flag has been set
         */
        if ($image->isBroken()) {
            $message = "The uploaded file was not a valid image.";
            $level = UPLOAD_ERR_FILE_TYPE;
            $error = new \Yana\Core\Exceptions\Files\InvalidImageException($message, $level);
            throw $error->setFilename($filename);
        }
        $width = $height = $background = null; // init image settings
        if (!empty($settings['width'])) {
            $width = (int) $settings['width'];
        }
        if (!empty($settings['width'])) {
            $height = $settings['height'];
        }
        $ratio = !empty($settings['ratio']);
        if (!empty($settings['background'])) {
            $background = $settings['background'];
            if (preg_match('/#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i', "$background", $rgb)) {
                $background = array(
                    hexdec($rgb[1]),
                    hexdec($rgb[2]),
                    hexdec($rgb[3])
                );
            }
        }
        $this->_createImageAndThumbnail($image, $path, $thumbnailPath, $width, $height, $ratio, (array) $background, $fileType, $fileTempName);
        return "$path.$fileType";
    }


    /**
     * Resize images and save files.
     *
     * @param  \Yana\Media\Image $image  $image            source
     * @param  string                    $path             where to store file
     * @param  string                    $thumbnailPath    where to store tumbnail
     * @param  int                       $width            resize to
     * @param  int                       $height           resize to
     * @param  bool                      $keepAspectRatio  for resizing
     * @param  array                     $background       RGB color
     * @param  string                    $fileEnding       of target
     * @param  string                    $fileTempName     of source
     * @codeCoverageIgnore
     */
    protected function _createImageAndThumbnail(\Yana\Media\Image $image, $path, $thumbnailPath, $width, $height, $keepAspectRatio, array $background, $fileEnding, $fileTempName)
    {
        // resize (if at least a width or height is given)
        if ($width || $height) {
            $image->createThumbnail($width, $height, $keepAspectRatio, $background);
            $image->outputToFile($path, $fileEnding);
        } else {
            move_uploaded_file($fileTempName, "$path.$fileEnding");
        }
        // create thumbnail
        $image->createThumbnail(100, 100, true, $background);
        $image->outputToFile($thumbnailPath, 'png');
    }
}

?>