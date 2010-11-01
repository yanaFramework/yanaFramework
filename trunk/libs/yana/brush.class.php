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

/**
 * Brush wrapper class
 *
 * This class is meant as an add-on to the framework's
 * Image class. It is intended to handle predefined brushes
 * in PNG format, which reside in the directory
 * "common_files/brush/*.png" to ease work with images.
 *
 * Brush images need to be 2-colored black/white images
 * with color index 0 being black and index 1 being
 * the transparent color and width == height.
 *
 * @access      public
 * @package     yana
 * @subpackage  utilities
 * @since       2.8.7
 * @see         Image
 */
class Brush extends Object
{
    /**#@+
     * @access  private
     * @ignore
     */

    private $brushname = null;
    private static $brushdir = null;
    private $image = null;

    /**#@-*/

    /**
     * create a new instance of this class
     *
     * The argument $brushname determines wich brush to take the input from.
     * If $brushname is not provided, a 1px square is used.
     *
     * The brush name can be any name of an existing PNG image (without the file extension),
     * that must reside in the "common_files/brush/*.png" directory.
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
     * @param  string  $brushname  see list
     */
    public function __construct($brushname = null)
    {
        assert('is_null($brushname) || is_string($brushname); // Wrong type for argument 1. String expected');

        global $YANA;

        /**
         * Set Brush directory
         */
        if (is_null(self::$brushdir)) {
            if (isset($YANA)) {
                self::$brushdir = $YANA->getVar('BRUSHDIR');
            } else {
                self::$brushdir = 'common_files/brush/';
            }
        }

        /**
         * Check if GD-libary is available and able to handle PNG images
         *
         * In case of an error a log entry is created.
         * Note: no error is triggered, since this might result in a broken image,
         * when calling Image::outputToScreen().
         */
        if (!function_exists('imagecreate') || !function_exists('imagecreatefrompng')) {
            Log::report("Cannot create brush. This server is unable to handle PNG images.");

        /* try to load the file */
        } else {

            /* 1 check input */
            if (is_null($brushname)) {
                $brushname = 'point';
            }
            if (!is_string($brushname)) {
                $brushFile = null;
            } else {
                $brushFile = self::$brushdir . str_replace(' ', '-', $brushname) . '.png';
            }

            /* 2 create image resource */
            if (!is_string($brushFile) || !file_exists($brushFile)) {
                trigger_error("The brush '{$brushname}' does not exist.", E_USER_NOTICE);
            } else {
                $this->brushname = $brushname;
                $this->image     = imagecreatefrompng($brushFile);
            }

            /* check if result is valid */
            if (!is_resource($this->image)) {
                trigger_error("The brush {$brushname} is invalid.", E_USER_WARNING);
            } else {
                imagecolortransparent($this->image, 1);
            }
        } /* end of section "create image resource" */
    }

    /**
     * get name of this brush
     *
     * Returns bool(false) on error.
     *
     * @access  public
     * @return  string
     */
    public function getName()
    {
        if (!is_string($this->brushname)) {
            return false;
        } else {
            return $this->brushname;
        }
    }

    /**
     * set the directory that contains the brushes
     *
     * This function will set the source directory for brushes.
     * The next time you create a Brush object, the png image
     * will automatically be searched for in the directory
     * you provided here.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @access  public
     * @static
     * @param   string  $directory  new source directory
     * @return  bool
     * @since   2.8.8
     */
    public static function setSourceDirectory($directory)
    {
        assert('is_string($directory); // Wrong type for argument 1. String expected');
        if (!is_dir($directory)) {
            trigger_error("Not a directory '$directory'.", E_USER_WARNING);
            return false;

        } else {
            self::$brushdir = $directory;
            return true;
        }
    }

    /**
     * get brush size
     *
     * Returns the brush's dimension in pixel or bool(false) on error.
     *
     * @access  public
     * @return  int|bool(false)
     */
    public function getSize()
    {
        if (!is_resource($this->image)) {
            return false;

        } elseif (!function_exists('imagesx')) {
            return false;

        } else {
            return imagesx($this->image);

        }
    }

    /**
     * Resize the brush
     *
     * This resizes the brush.
     *
     * The argument $size is the new size in pixel.
     *
     * Returns bool(false) on error.
     *
     * @access  public
     * @param   int  $size  brush size in pixel
     * @return  bool
     */
    public function setSize($size)
    {
        assert('is_int($size); // Wrong type for argument 1. Integer expected');

        if (!is_resource($this->image)) {
            return false;
        } elseif (!function_exists('imagepalettecopy')) {
            return false;

        } else {

            $currentSize = $this->getSize();

            /* argument 1 */
            if ($size < 1) {
                trigger_error("Invalid value for argument 1. Size must be greater 0.", E_USER_WARNING);
                return false;
            }

            if ($currentSize === $size) {

                /* if image already has the expected size, then there is nothing to do here */
                return true;

            } else {

                $oldImage    = $this->image;
                $this->image = imagecreate($size, $size);
                imagepalettecopy($this->image, $oldImage);
                imagefill($this->image, 0, 0, 1);
                imagecopyresized($this->image, $oldImage, 0, 0, 0, 0, $size, $size, $currentSize, $currentSize);
                imagecolortransparent($this->image, 1);
                imagedestroy($oldImage);
                return true;

            }
        }
    }

    /**
     * set the color of this brush
     *
     * This function sets the color of the brush
     * to a certain value, where the input is
     * the red, green and blue values of this color.
     *
     * The arguments $r, $g and $b need to be integer
     * values between 0 and 255. For example,
     * $r = 0 is interpreted as "0% red" and $r = 255
     * is interpreted as "100% red".
     *
     * @access  public
     * @param   int    $r        red value
     * @param   int    $g        green value
     * @param   int    $b        blue value
     * @return  string
     */
    public function setColor($r, $g, $b)
    {
        assert('is_int($r); // Wrong type for argument 1. Integer expected');
        assert('is_int($g); // Wrong type for argument 2. Integer expected');
        assert('is_int($b); // Wrong type for argument 3. Integer expected');

        /*
         * check arguments
         */
        for ($i = 0; $i < 3; $i++)
        {
            $test = func_get_arg($i);
            if ($test < 0 || $test > 255) {
                $message = "Invalid value for argument {$i}. " .
                    "Must be between 0 and 255, found '{$test}' instead.";
                trigger_error($message, E_USER_WARNING);
                return false;
            }
        }
        unset($test, $i);

        /* is image? */
        if (!is_resource($this->image)) {
            return false;
        }

        /* GD library is there? */
        if (!function_exists('imagecolorset')) {
            return false;
        }

        /* set color */
        imagecolorset($this->image, 0, $r, $g, $b);
        return true;
    }

    /**
     * get the color of this brush
     *
     * Returns an associative array of the red, green and blue
     * values of the current brush color.
     * Returns bool(false) on error.
     *
     * @access   public
     * @return   array
     */
    public function getColor()
    {
        if (!is_resource($this->image)) {
            return false;
        } elseif (!function_exists('imagecolorsforindex')) {
            return false;
        } else {
            return imagecolorsforindex($this->image, 0);
        }
    }

    /**
     * get a string representation of this object
     *
     * This function is intended to be called when the object
     * is used in a string context.
     *
     * @access   public
     * @return   string
     */
    public function toString()
    {
        $name = $this->getName();
        if (is_string($name)) {
            return $name;
        } else {
            return "untitled brush";
        }
    }

    /**
     * clone this object
     *
     * Creates a copy of this object.
     * You are encouraged to reimplement this for each subclass.
     *
     * @access public
     * @return Object
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

            imagecopy($copiedImage, $this->image, 0, 0, 0, 0, $width, $height);
            $this->image = $copiedImage;
        }
    }

    /**
     * compare with another object
     *
     * Returns bool(true) if this object and $anotherObject
     * have an image resource that is the same.
     *
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @param   object  $anotherObject  any object or var you want to compare
     * @return  bool
     */
    public function equals(object $anotherObject)
    {
        if ($anotherObject instanceof $this) {
            return ( $this->image === $anotherObject->getResource() );
        } else {
            return false;
        }
    }

    /**
     * compare with another resource
     *
     * Returns bool(true) if the given parameter is an image resource and
     * is identical to the image resource of this object.
     *
     * Returns bool(false) otherwise.
     *
     * @access  public
     * @param   resource  $resource  any other resource
     * @return  bool
     * @since   3.1.0
     */
    public function equalsResoure($resource)
    {
        assert('is_resource($resource); // Wrong type for argument 1. Resource expected');
        if (is_resource($resource) && $this->image === $resource) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get the image resource
     *
     * This returns the image resource of the object,
     * or bool(false) on error.
     *
     * @access public
     * @return resource|bool(false)
     */
    public function getResource()
    {
        /**
         * error - broken image
         */
        if (!is_resource($this->image)) {
            return false;

        } else {
            return $this->image;
        }
    }

    /**
     * Destructor
     *
     * Automatically free memory for the image if object gets deleted.
     * Note that this is a PHP 5 feature. In PHP 4 you had to call
     * this function by hand.
     *
     * @access public
     * @ignore
     */
    public function __destruct()
    {
        if (function_exists('imagedestroy')) {
            if (is_resource($this->image)) {
                imagedestroy($this->image);
            }
        }
    }

}

?>