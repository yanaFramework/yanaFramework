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

namespace Yana\Media;

/**
 * Brush wrapper class.
 *
 * This class is meant as an add-on to the framework's Image class.
 * It is intended to handle predefined brushes in PNG format, which reside in the "brushes"-directory.
 *
 * Brush images need to be 2-colored black/white images with color index 0 being black and index 1 being
 * the transparent color and width == height.
 *
 * @package     yana
 * @subpackage  utilities
 * @since       2.8.7
 * @see         Image
 */
class Brush extends \Yana\Core\Object
{

    /**
     * Name of brush image.
     *
     * This is part of the file name.
     *
     * @var  string
     */
    private $_brushname = null;

    /**
     * Global directory where brush-images are stored.
     *
     * @var  string
     */
    private static $_brushdir = null;

    /**
     * Integer index of image resource.
     *
     * @var  resource
     */
    private $_image = null;

    /**
     * Create a new instance of this class.
     *
     * The argument $brushname determines wich brush to take the input from.
     * Default is a single pixel.
     *
     * The brush name can be any name of an existing PNG image (without the file extension).
     *
     * Here are some examples:
     * <ul>
     * <li> airbrush </li>
     * <li> small circle </li>
     * <li> circle </li>
     * <li> dot </li>
     * <li> small star </li>
     * <li> star </li>
     * <li> square </li>
     * <li> point (default) </li>
     * </ul>
     *
     * @param   string  $brushname  see list
     * @throws  \Yana\Core\Exceptions\NotImplementedException  if the GD-library is not available
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException when the requested brush is not found
     */
    public function __construct($brushname = 'point')
    {
        assert('is_string($brushname); // Wrong type for argument 1. String expected');

        // Check if GD-libary is available and able to handle PNG images.
        if (!function_exists('imagecreate') || !function_exists('imagecreatefrompng')) {
            throw new \Yana\Core\Exceptions\NotImplementedException("Cannot create brush. This server is unable to handle PNG images.");
        }

        $brushFile = self::getDirectory() . str_replace(' ', '-', $brushname) . '.png';

        // check if file exists
        if (!file_exists($brushFile)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Invalid brush file. File '$brushname' not found.");
        }
        $this->_brushname = $brushname;
        $this->_image = imagecreatefrompng($brushFile);

        // check if file is a valid image
        if (!is_resource($this->_image)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("The brush '{$brushname}' is not a valid image-file.");
        }
        /* Set background transparent.
         *
         * The source must be a black-white image, where palette index 1 is the background color.
         */
        imagecolortransparent($this->_image, 1);
    }

    /**
     * Get name of this brush.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->_brushname;
    }

    /**
     * Set the directory that contains the brushes.
     *
     * This function will set the source directory for brushes.
     * The next time you create a Brush object, the png image
     * will automatically be searched for in the directory
     * you provided here.
     *
     * @param  string  $directory  new source directory
     */
    public static function setDirectory($directory)
    {
        assert('is_string($directory); // Invalid argument $directory: string expected');
        assert('is_dir($directory); // Invalid argument $directory: must be a valid path');
        self::$_brushdir = (string) $directory;
    }

    /**
     * Reset the path to the brush-directory to the default.
     *
     * By default this will look for files in the "brush" sub-directory at the path where this class is stored.
     */
    public static function resetDirectory()
    {
        self::$_brushdir = __DIR__ . '/brushes/';
    }

    /**
     * Get the directory that contains the brushes.
     *
     * This function will return the path to source directory for brushes.
     *
     * @return  string
     */
    public static function getDirectory()
    {
        if (is_null(self::$_brushdir)) {
            self::resetDirectory();
            assert('is_dir(self::$_brushdir);');
        }
        return self::$_brushdir;
    }

    /**
     * Returns brush's dimension in pixel or bool(false) on error.
     *
     * @return  int|bool(false)
     */
    public function getSize()
    {
        return imagesx($this->_image);
    }

    /**
     * Resize the brush.
     *
     * The argument $size is the new size in pixel.
     * Returns bool(false) on error.
     *
     * @param   int  $size  brush size in pixel
     * @return  \Yana\Media\Brush
     */
    public function setSize($size)
    {
        assert('is_int($size); // Invalid argument $size: int expected');
        assert('$size > 0; // Invalid argument $size: string expected');

        $currentSize = $this->getSize();

        if ($currentSize !== $size) { // if image already has the expected size, then there is nothing to do here
            $oldImage    = $this->_image;
            $this->_image = imagecreate($size, $size);
            imagepalettecopy($this->_image, $oldImage);
            imagefill($this->_image, 0, 0, 1);
            imagecopyresized($this->_image, $oldImage, 0, 0, 0, 0, $size, $size, $currentSize, $currentSize);
            imagecolortransparent($this->_image, 1);
            imagedestroy($oldImage);
        }
        return $this;
    }

    /**
     * Set the color of this brush.
     *
     * This function sets the color to a certain value, where the input is
     * the red, green and blue values of this color.
     * The palette index is detected automatically.
     *
     * @param   int  $red    0 - 255 (255 = 100% red)
     * @param   int  $green  0 - 255 (255 = 100% green)
     * @param   int  $blue   0 - 255 (255 = 100% blue)
     * @return  \Yana\Media\Brush
     */
    public function setColor($red, $green, $blue)
    {
        assert('is_int($red); // Wrong type for argument 1. Integer expected');
        assert('is_int($green); // Wrong type for argument 2. Integer expected');
        assert('is_int($blue); // Wrong type for argument 3. Integer expected');
        assert('$red >= 0 && $red <= 255; // Invalid argument $red: must be in range [0,255].');
        assert('$green >= 0 && $green <= 255; // Invalid argument $green: must be in range [0,255].');
        assert('$blue >= 0 && $blue <= 255; // Invalid argument $blue: must be in range [0,255].');
        imagecolorset($this->_image, 0, $red, $green, $blue);
        return $this;
    }

    /**
     * Get the color of this brush.
     *
     * Returns an associative array of the red, green and blue
     * values of the current brush color.
     * Returns bool(false) on error.
     *
     * @return   array
     */
    public function getColor()
    {
        return imagecolorsforindex($this->_image, 0);
    }

    /**
     * Get a string representation of this object.
     *
     * This function is intended to be called when the object
     * is used in a string context.
     *
     * @return   string
     */
    public function __toString()
    {
        $name = $this->getName();
        if (is_string($name)) {
            return $name;
        } else {
            return "untitled brush";
        }
    }

    /**
     * Clone this object.
     *
     * Creates a copy of this object.
     * You are encouraged to reimplement this for each subclass.
     *
     * @return Brush
     */
    public function __clone()
    {
        parent::__clone();

        if (!$this->isBroken()) {
            $width = $this->getWidth();
            $height = $this->getHeight();

            /* create new image */
            if (function_exists('imagecreatetruecolor')) {
                $copiedImage = imagecreatetruecolor($width, $height);
            } elseif (function_exists('imagecreate')) {
                $copiedImage = imagecreate($width, $height);
            } else {
                return false;
            }

            imagecopy($copiedImage, $this->_image, 0, 0, 0, 0, $width, $height);
            $this->_image = $copiedImage;
        }
    }

    /**
     * Compare with another object.
     *
     * Returns bool(true) if this object and $anotherObject have an image resource that is the same.
     * Returns bool(false) otherwise.
     *
     * @param   \Yana\Core\IsObject  $anotherObject  any object or var you want to compare
     * @return  bool
     */
    public function equals(\Yana\Core\IsObject $anotherObject)
    {
        return $anotherObject instanceof $this && $this->_image === $anotherObject->getResource();
    }

    /**
     * Compare with another resource.
     *
     * Returns bool(true) if the given parameter is an image resource and
     * is identical to the image resource of this object.
     *
     * Returns bool(false) otherwise.
     *
     * @param   resource  $resource  any other resource
     * @return  bool
     * @since   3.1.0
     */
    public function equalsResoure($resource)
    {
        assert('is_resource($resource); // Wrong type for argument 1. Resource expected');
        return is_resource($resource) && $this->_image === $resource;
    }

    /**
     * Get the image resource.
     *
     * This returns the image resource of the object,
     * or bool(false) on error.
     *
     * @return resource|bool(false)
     */
    public function getResource()
    {
        /**
         * error - broken image
         */
        if (!is_resource($this->_image)) {
            return false;

        } else {
            return $this->_image;
        }
    }

    /**
     * Destructor.
     *
     * Automatically free memory for the image if object gets deleted.
     * Note that this is a PHP 5 feature. In PHP 4 you had to call
     * this function by hand.
     *
     * @ignore
     */
    public function __destruct()
    {
        if (is_resource($this->_image)) {
            imagedestroy($this->_image);
        }
    }

}

?>