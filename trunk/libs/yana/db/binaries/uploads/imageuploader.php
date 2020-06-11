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
     * $column = $db->getSchema()->getTable('my_table')->getColumn('my_file');
     * $fileId = \Yana\Db\Blob::getNewFileId($column);
     * // and assign it to your row to update/insert
     * $row['my_file'] = $fileId;
     *
     * // get image/thumbnail settings
     * $settings = $column->getImageSettings();
     *
     * // upload the image
     * $helper = new \Yana\Db\Binaries\Uploads\ImageUploader();
     * $file = new \Yana\Http\Uploads\File(
     *     $_FILES['my_file']['name'],
     *     $_FILES['my_file']['type'],
     *     $_FILES['my_file']['tmp_name'],
     *     $_FILES['my_file']['size'],
     *     $_FILES['my_file']['error']
     * );
     * $filename = $helper->upload($file, $fileId, $settings);
     * // and update/insert the row as usual
     * $db->insert("my_table", $row);
     * </code>}.
     *
     * We never just copy the original image file, we always re-render and store the result.
     * Why?
     *
     * <ol>
     * <li>So that all the meta-data (including geo-information) will always be removed from the image.</li>
     * <li>So that it is harder to upload a file as an image that isn't actually an image, even if the
     * attacker provides a fake mime-type.</li>
     * <li>So that even if the file actually is an image but contains malicious content in its meta-data
     * (some image types allow scripts to be included) this content gets removed as well, making it more
     * unlikely that a potentially vulnerable third party software ever gets to see it.</li>
     * </ol>
     *
     * {@internal{
     * We are perfectly aware that if the GD-library were to have a vulnerability related to the processing
     * of a certain image type, we might hereby expose it to user-land.
     * Except we don't know how likely this scenario is. It might never happen, it might happen tomorrow.
     *
     * What we DO know, however, is that the scenario of an entirely unrelated library having a security
     * flaw that could lead to that malicious file somehow getting included, executed, or distributed is much more likely.
     * We thus accept the minor risk and do what can be done to not have potentially malicious file content on our server
     * in the first place. }}
     *
     * @param   \Yana\Http\Uploads\IsFile   $file      representing one entry in global $_FILES array
     * @param   string                      $fileId    name of target file
     * @param   array                       $settings  int    width       image width in pixel,
     *                                                 int    height      image height in pixel,
     *                                                 bool   ratio       keep aspect ratio when resizing?,
     *                                                 string background  RGB color as hex-value (e.g. #ffffff)
     * @return  string
     * @throws  \Yana\Core\Exceptions\Files\InvalidImageException  when the uploaded file was no valid image
     */
    public function upload(\Yana\Http\Uploads\IsFile $file, string $fileId, array $settings)
    {
        $dir = $this->_getConfiguration()->getDirectory();
        $fileTempName = $this->_getTempName($file);
        $filename = $file->getName();

        // check mime-type of uploaded file
        $fileType = 'png';
        if ($file->getMimeType()) {
            if (!preg_match('/^image\/(jpe?g|png|gif)$/s', $file->getMimeType(), $match)) {
                $message = "The uploaded file has an invalid MIME-type. Must be jpg, png or gif.";
                $error = new \Yana\Core\Exceptions\Files\InvalidImageException($message);
                throw $error->setFilename($filename);
            }
            $fileType = $match[1];
            unset($match);
        }

        /* name of output files */
        $path = "{$dir}/{$fileId}";
        $thumbnailPath = "{$dir}/thumb.{$fileId}";

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
            $error = new \Yana\Core\Exceptions\Files\InvalidImageException($message);
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
        if (isset($settings['background']) && (is_array($settings['background']) || is_string($settings['background']))) {
            $background = $settings['background'];
            if (is_string($background) && preg_match('/#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i', (string) $background, $rgb)) {
                $background = array(
                    hexdec($rgb[1]),
                    hexdec($rgb[2]),
                    hexdec($rgb[3])
                );
            }
        }
        $this->_createImageAndThumbnail($image, $path, $thumbnailPath, $width, $height, $ratio, $background, $fileTempName);
        return "$path.jpg";
    }


    /**
     * Resize images and save files.
     *
     * @param  \Yana\Media\Image $image  $image            source
     * @param  string                    $path             where to store file
     * @param  string                    $thumbnailPath    where to store tumbnail
     * @param  int|NULL                  $width            resize to
     * @param  int|NULL                  $height           resize to
     * @param  bool                      $keepAspectRatio  for resizing
     * @param  array                     $background       RGB color
     * @param  string                    $fileTempName     of source
     * @codeCoverageIgnore
     */
    protected function _createImageAndThumbnail(\Yana\Media\Image $image, string $path, string $thumbnailPath, ?int $width, ?int $height, bool $keepAspectRatio, ?array $background, string $fileTempName)
    {
        // resize (if at least a width or height is given)
        if ($width || $height) {
            $image->createThumbnail($width, $height, $keepAspectRatio, $background);
        }
        /* We never keep the original image file, we always re-render and create our own.
         * Why?
         *
         * <ol>
         * <li>So that all the meta-data (including geo-information) will always be removed from the image.</li>
         * <li>So that it is harder to upload a file as an image that isn't actually an image, even if the
         * attacker provides a fake mime-type.</li>
         * <li>So that even if the file actually is an image but contains malicious content in its meta-data
         * (some image types allow scripts to be included here) this contents gets removed, so that a potentially
         * vulnerable third party software never gets to see it.</li>
         * </ol>
         *
         * We are perfectly aware that if the GD-library were to have a vulnerability related to the processing
         * of a certain image type, we are hereby exposing it to user-land.
         * Except we don't know how likely this scenario is. What we DO know, however, is that it is preferable
         * to do whatever necessary to not allow any potentially malicious file content to be stored on the server
         * in the first place, even if that means accepting a minor risk.
         * Just to prevent the much more likely scenario that an entirely unrelated library were to have a security
         * flaw that could lead to that malicious file somehow getting included, executed, or distributed.
         * We don't want that file on our server. Period.
         */
        $image->outputToFile($path, 'jpg');
        // create thumbnail
        $image->createThumbnail(100, 100, true, $background);
        $image->outputToFile($thumbnailPath, 'png');
    }
}

?>