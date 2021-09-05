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
declare(strict_types=1);

namespace Yana\Media;

/**
 * Image wrapper class
 *
 * This class is an OO wrapper for PHP's image handling functions.
 * Note that this class requires the GD library to be installed in order to work correctly.
 * Not all functions are available in all version of this library.
 * But the class will automatically try a fall-back to an alternative function where possible.
 *
 * Also note, that this class will NOT output any error messages unless a fatal error is encountered
 * and instead will report error to the logs.
 * This is a intended behaviour, as any output (e.g. an error message) from the script would result
 * in a broken image file, when the image is directly put to the user's browser.
 *
 * So when you are working with this class: in case you encounter an error and want to know
 * the cause of it, please activate the program's logging feature via the administrator's
 * menu, reproduce the error and see the logs for the entry that describes the problem.
 *
 * The following image types are currently supported:
 * <ul>
 * <li> bmp </li>
 * <li> gif </li>
 * <li> jpeg </li>
 * <li> png </li>
 * </ul>
 *
 * @package     yana
 * @subpackage  media
 * @since       2.8.7
 */
class Image extends \Yana\Core\StdObject
{

    /**
     * Path to image file.
     *
     * @var string
     */
    private $_path = null;

    /**
     * True if the path points to a file.
     *
     * @var bool
     */
    private $_exists = false;

    /**
     * Integer identifying the image resource.
     *
     * @var ressource
     */
    private $_image = null;

    /**
     * False if the path points to a valid image.
     *
     * @var false
     */
    private $_isBroken = false;

    /**
     * Image Gamma setting.
     *
     * @var float
     */
    private $_gamma = 1.0;

    /**
     * Line width in pixel.
     *
     * @var int
     */
    private $_lineWidth = 1;

    /**
     * Line style settings.
     *
     * See setter function for details.
     * Set to NULL to force default settings.
     *
     * @var array
     */
    private $_lineStyle = null;

    /**
     * True to produce an interlaced image (certain image types only, e.g. GIF).
     *
     * @var bool
     */
    private $_interlace = false;

    /**
     * True when an alpha-channel should be added (certain image types only, e.g. PNG).
     *
     * @var bool
     */
    private $_alpha = false;

    /**
     * True when an alpha-channel should be added (certain image types only, e.g. PNG).
     *
     * @var \Yana\Media\Brush
     */
    private $_brush = null;

    /**
     * Is set to the initial transparency color of the image (if any).
     *
     * @var int
     */
    private $_transparency = null;

    /**
     * Current background color.
     *
     * @var int
     */
    private $_backgroundColor = null;

    /**
     * Mapping of image types to: mime-type and function names.
     *
     * @var array
     */
    private $_mapping  = array(
        'png'  => array('image/png',          'imagepng',  'imagecreatefrompng' ),
        'jpg'  => array('image/jpeg',         'imagejpeg', 'imagecreatefromjpeg'),
        'jpeg' => array('image/jpeg',         'imagejpeg', 'imagecreatefromjpeg'),
        'gif'  => array('image/gif',          'imagegif',  'imagecreatefromgif' ),
        'wbmp' => array('image/vnd.wap.wbmp', 'imagewbmp', 'imagecreatefromwbmp'),
        'bmp'  => array('image/bmp',          'imagebmp',  'imagecreatefrombmp'),
        'webp' => array('image/webp',         'imagewebp', 'imagecreatefromwebp'),
        'xbm'  => array('image/x-xbitmap',    'imagexbm',  'imagecreatefromxbm'),
        'xpm'  => array('image/x-xpixmap',    'imagexpm',  'imagecreatefromxpm')
    );

    /**#@+
     * Color.
     *
     * These standard palette colors are added for the users convenience.
     * The class itself initializes, but doesn't use them.
     * Note: if the palette does not have the exact color, a similar color is used instead.
     *
     * @var  int
     */

    /** aqua = #00ffff */
    public $aqua    = null;
    /** black = #000000 */
    public $black   = null;
    /** blue = #0000ff */
    public $blue    = null;
    /** fuchsia = #ff00ff */
    public $fuchsia = null;
    /** gray = #808080 */
    public $gray    = null;
    /** grey = #808080 */
    public $grey    = null;
    /** green = #008000 */
    public $green   = null;
    /** lime = #00ff00 */
    public $lime    = null;
    /** maroon = #800000 */
    public $maroon  = null;
    /** navy = #008080 */
    public $navy    = null;
    /** olive = #808000 */
    public $olive   = null;
    /** purple = #800080 */
    public $purple  = null;
    /** red = #ff0000 */
    public $red     = null;
    /** silver = #c0c0c0 */
    public $silver  = null;
    /** teal = #008080 */
    public $teal    = null;
    /** white = #ffffff */
    public $white   = null;
    /** yellow = #ffff00 */
    public $yellow  = null;

    /**#@-*/

    /**
     * Create a new instance of this class.
     *
     * The argument $filename determines wich file to take the input from.
     * If $filename is not provided, an empty truecolor image with white background and
     * a dimension of 300x200px is created.
     *
     * The argument $imageType can be on of the following.
     *
     * <ul>
     * <li> bmp </li>
     * <li> gif </li>
     * <li> jpeg </li>
     * <li> png </li>
     * </ul>
     *
     * If no image type is set, the function will try
     * to determine the correct type of the file automatically,
     * 1st by checking the file extension and in case this
     * did not work 2nd by checking the file header.
     * If all this fails it writes an entry to the logs
     * and creates an error image instead.
     *
     * NOTE: It DOES NOT produce an error message, as this
     * would result in a broken image, if the image was printed!
     *
     * To check wether or not an image is broken, use
     * <code>
     * if ($myImage->isBroken() === true) {
     *     // handle broken image
     * }
     * </code>
     *
     * @param   string  $filename    name of the source file
     * @param   string  $imageType   type of the image
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the image is not valid
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException   when the image file is not found
     * @throws  \Yana\Media\GdlException                        when the GD-library is not found
     */
    public function __construct(?string $filename = null, ?string $imageType = null)
    {
        if (!function_exists('imagecreate')) {
            // @codeCoverageIgnoreStart
            $message = "The GD library does not seem to be installed. Without this library this framework will be unable to create images. " .
                    "Please update your configuration!";
            throw new \Yana\Media\GdlException($message, \Yana\Log\TypeEnumeration::ERROR);
            // @codeCoverageIgnoreEnd
        }

        /* if no filename is provided, create an empty truecolor image */
        if (is_null($filename)) {

            $this->_createImage();
            /* end of process*/

        /* otherwise try to load the file */
        } else {

            /* 1 check input */
            if (file_exists($filename)) {
                $this->_path = $filename;
                $this->_exists = true;
            }
            if (is_string($imageType)) {
                $imageType = mb_strtolower($imageType);
            }

            /* 2 create image resource */
            try {
                /* load image resource */
                $this->_image = $this->_loadImage($filename, $imageType);
                /* make image truecolor */
                $this->_initializeColorPalette();

            } catch (\Exception $e) {

                \Yana\Log\LogManager::getLogger()->addLog($e->getMessage(), $e->getCode());
                $this->_createErrorImage();
            }
        } /* end if */
        assert(is_resource($this->_image));

        /* 3 get initial transparency */
        if (is_null($this->_transparency)) {
            $this->_transparency = imagecolortransparent($this->_image);
            if ($this->_transparency === -1) {
                $this->_transparency = null;
            } else {
                $this->_transparency = imagecolorsforindex($this->_image, $this->_transparency);
            }
        }

    }

    /**
     * Attempts to detect the image type based on the file name.
     *
     * If the path is invalid, it will return an empty string.
     *
     * @param   string  $path  to image file
     * @return  string
     */
    protected function _detectImageTypeByFilePath(string $path): string
    {
        assert(!isset($pathInfo), 'cannot redeclare variable $imageType');
        $imageType = "";
        assert(!isset($pathInfo), 'cannot redeclare variable $pathInfo');
        $pathInfo = pathinfo($path);
        assert(!isset($fileExtension), 'cannot redeclare variable $fileExtension');
        $fileExtension = isset($pathInfo['extension']) ? mb_strtolower((string) $pathInfo['extension']) : '';
        if (isset($this->_mapping[$fileExtension])) {
            $imageType = $fileExtension;
        }
        return $imageType;
    }

    /**
     * Try to load the image by guessing the image type.
     *
     * This function will get the file contents and pass them to a function that will try to determine
     * the file type by looking at the file header.
     *
     * @param   string  $path  to image file
     * @return  resource|NULL
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the image is not valid
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException   when the image file is not found
     */
    protected function _loadImageByFileHeader(string $path)
    {
        if (!file_exists($path)) {
            // @codeCoverageIgnoreStart
            // should not be reachable because the callee already checked that, but just in case ...
            throw new \Yana\Core\Exceptions\Files\NotFoundException("Image file not found: " . $path);
            // @codeCoverageIgnoreEnd
        }

        /**
         * {@internal
         *
         * The function imagecreatefromstring() can only handle
         * truecolor images. If the source is a GIF image, this
         * won't work.
         *
         * So before passing the string to this function let's
         * first try the original gif function, which works
         * well. Only if it fails, then we assume the file is
         * not a GIF and will try imagecreatefromstring()
         * instead.
         *
         * }}
         */
        $image = @imagecreatefromgif($path);
        if (!is_resource($image)) {
            $fileContent = file_get_contents($path);
            if ($fileContent !== false) {
                $image = @imagecreatefromstring($fileContent);
            }
        }

        if (!is_resource($image)) {
            throw new \Yana\Core\Exceptions\InvalidArgumentException("Not a valid image: " . $path);
        }
        return $image;
    }

    /**
     * Load image from file path.
     *
     * @param   string       $path       to image file
     * @param   string|null  $imageType  file type as by file extension like "png" or "gif", also second half of mime-type "image/png"
     * @return  resource
     * @throws  \Yana\Core\Exceptions\Files\InvalidTypeException   when the image type given is not recognized
     * @throws  \Yana\Core\Exceptions\Files\InvalidImageException  when image file is found but could not be loaded
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException     when the image is not valid
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException      when the image file is not found
     */
    protected function _loadImage(string $path, ?string $imageType)
    {
        /* determine image type by filename */
        if (is_null($imageType)) {
            $imageType = $this->_detectImageTypeByFilePath($path);
        }

        assert(!isset($image), 'cannot redeclare variable $image');
        $image = null;

        /* determine image type by header */
        if (!$imageType) {

            $image = $this->_loadImageByFileHeader($path); // may throw exception

        /* create image from file */
        } elseif (isset($this->_mapping[$imageType])) {

            $functionName = $this->_mapping[$imageType][2];
            $image  = @$functionName($this->getPath());

        /* error: image type unsupported */
        } else {

            throw new \Yana\Core\Exceptions\Files\InvalidTypeException("Image type '{$imageType}' is unsupported.", \Yana\Log\TypeEnumeration::WARNING);
        }

        /* check if result is valid */
        if (!\is_resource($image)) {

            throw new \Yana\Core\Exceptions\Files\InvalidImageException("The file '{$path}' was not recognized as a valid " .
                    ((!empty($imageType)) ? $imageType . " " : "") . "image.", \Yana\Log\TypeEnumeration::WARNING);
        }

        return $image;
    }

    /**
     * Initialize color palette.
     *
     * This converts the image to true color and initializes the class color properties.
     *
     * @throws  \Yana\Core\Exceptions\Files\InvalidImageException  when the palette could not be set because the image wasn't recognized
     */
    protected function _initializeColorPalette()
    {
        if (!$this->isTruecolor()) {

            $width  = $this->getWidth();
            $height = $this->getHeight();

            if (!\is_int($height) || !\is_int($width) || $height < 1 || $width < 1) {
                // @codeCoverageIgnoreStart
                // should not be reachable because the callee already ensured this function would not be called with an invalid width, but just in case ...
                throw new \Yana\Core\Exceptions\Files\InvalidImageException("Not a valid image.", \Yana\Log\TypeEnumeration::WARNING);
                // @codeCoverageIgnoreEnd
            }
            $oldImage = $this->_image;
            $this->_createImage($width, $height); // This will also call $this->_initColors() - no reason to do it again.
            imagecopy($this->_image, $oldImage, 0, 0, 0, 0, $width, $height);
            imagedestroy($oldImage);

        } else {
            $this->_initColors();
        }
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function getPath(): string
    {
        if ($this->_exists) {
            return $this->_path;
        } else {
            return '';
        }
    }

    /**
     * Returns bool(true), if the image exists.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return (bool) $this->_exists;
    }

    /**
     * Returns bool(true) if the image type was not recognized.
     *
     * @return bool
     */
    public function isBroken(): bool
    {
        assert(is_bool($this->_isBroken), 'is_bool($this->_isBroken)');
        return $this->_isBroken || !is_resource($this->_image);
    }

    /**
     * Check if image is truecolor.
     *
     * Returns bool(true) if the image is truecolor and
     * bool(false) otherwise.
     *
     * Truecolor images are e.g. JPEG images while
     * GIF is not. Some functions won't work with
     * non-truecolor images.
     *
     * @return  bool
     */
    public function isTruecolor(): bool
    {
        return !$this->isBroken() && imageistruecolor($this->_image);
    }

    /**
     * Clone this object.
     *
     * Creates a copy of this object.
     * You are encouraged to reimplement this for each subclass.
     *
     * @return  \Yana\Media\Image
     */
    public function __clone()
    {
        parent::__clone();

        if (!$this->isBroken()) {
            $width = (int) $this->getWidth();
            $height = (int) $this->getHeight();

            /* create new image */
            $copiedImage = imagecreatetruecolor($width, $height);

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
    public function equalsResoure($resource): bool
    {
        assert(is_resource($resource), 'Wrong type for argument 1. Resource expected');
        return (is_resource($resource) && $this->_image === $resource);
    }

    /**
     * Get the image resource.
     *
     * This returns the image resource of the object,
     * or NULL on error.
     *
     * @return  resource|NULL
     */
    public function getResource()
    {
        /**
         * error - broken image
         */
        if (!is_resource($this->_image) || $this->isBroken()) {
            return NULL;
        }

        return $this->_image;
    }

    /**
     * Produces a new image.
     *
     * @param   int  $width      horizontal dimension in pixel
     * @param   int  $height     vertical dimension in pixel
     * @ignore
     */
    private function _createImage(int $width = 300, int $height = 200)
    {
        assert($width > 0, 'Width must be greater 0.');
        assert($height > 0, 'Height must be greater 0.');

        /* backup background color */
        assert(!isset($backgroundColor), 'Cannot redeclare var $backgroundColor');
        $backgroundColor = null;
        if (!is_null($this->_backgroundColor)) {
            $backgroundColor = $this->getColorValues($this->_backgroundColor);
        }

        /* backup transparency color */
        if (is_resource($this->_image) && is_null($this->_transparency)) {
            assert(!isset($transparentColor), 'Cannot redeclare var $transparentColor');
            $transparentColor = imagecolortransparent($this->_image);
            $this->_transparency = null;
            if ($transparentColor !== -1) {
                $this->_transparency = imagecolorsforindex($this->_image, $transparentColor);
            }
            unset($transparentColor);
        }

        /* create new image */
        $this->_image = imagecreatetruecolor($width, $height);

        /* copy line width */
        if ($this->_lineWidth > 1) {
            $this->setLineWidth($this->_lineWidth);
        }

        /* copy line style */
        if (is_array($this->_lineStyle)) {
            imagesetstyle($this->_image, $this->_lineStyle);
        }

        /* copy transparency */
        if (is_array($this->_transparency)) {
            $this->setTransparency($this->_transparency);
        }

        /* copy interlacing */
        if ($this->_interlace === true) {
            $this->enableInterlace(true);
        }

        /* copy brush */
        if ($this->_brush instanceof \Yana\Media\Brush) {
            $this->setBrush($this->_brush);
        }

        /* initialize reserved palette colors */
        $this->_initColors();

        /* copy background color */
        if (!empty($backgroundColor)) {
            $this->_backgroundColor = (int) $this->getColor(
                (int) $backgroundColor['red'], (int) $backgroundColor['green'], (int) $backgroundColor['blue'], (float) ($backgroundColor['alpha'] / 127)
            );
        } else {
            $this->setBackgroundColor();
        }

        imagefill($this->_image, 0, 0, (int) $this->_backgroundColor);
    }

    /**
     * Initializes reserved palette colors.
     */
    private function _initColors()
    {
        /* define some popular colors */
        $this->aqua    = (int) $this->getColor(0,   255, 255);
        $this->black   = (int) $this->getColor(0,   0,   0);
        $this->blue    = (int) $this->getColor(0,   0,   255);
        $this->fuchsia = (int) $this->getColor(255, 0,   255);
        $this->gray    = (int) $this->getColor(128, 128, 128);
        $this->grey    = $this->gray;
        $this->green   = (int) $this->getColor(0,   128, 0);
        $this->lime    = (int) $this->getColor(0,   255, 0);
        $this->maroon  = (int) $this->getColor(128, 0,   0);
        $this->navy    = (int) $this->getColor(0,   0,   128);
        $this->olive   = (int) $this->getColor(128, 128, 0);
        $this->purple  = (int) $this->getColor(128, 0,   128);
        $this->red     = (int) $this->getColor(255, 0,   0);
        $this->silver  = (int) $this->getColor(192, 192, 192);
        $this->teal    = (int) $this->getColor(0,   128, 128);
        $this->white   = (int) $this->getColor(255, 255, 255);
        $this->yellow  = (int) $this->getColor(255, 255, 0);

        /* initialize background color */
        if (is_null($this->_backgroundColor)) {
            $this->setBackgroundColor();
        }
    }

    /**
     * Produces a new image.
     *
     * The old image is deleted and replaced by a new one.
     * Be warned: all previous changes to the image will be lost!
     */
    public function clearCanvas()
    {
        if (is_resource($this->_image)) {
            $oldImage = $this->_image;
            $this->_createImage((int) imagesx($oldImage), (int) imagesy($oldImage));
            imagedestroy($oldImage);
        }
    }

    /**
     * Produces an error image.
     */
    private function _createErrorImage()
    {
        $this->_isBroken = true;

        $this->_createImage(100, 30);
        imagefilledrectangle($this->_image, 0, 0, 150, 30, $this->white);
        imagestring($this->_image, 1, 5, 5, "Error loading image", $this->black);
    }

    /**
     * Get image width.
     *
     * Returns the image's horizontal dimension in pixel or NULL on error.
     *
     * @return  int|NULL
     */
    public function getWidth(): ?int
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return NULL;
        }

        return imagesx($this->_image);
    }

    /**
     * Get image height.
     *
     * Returns the image's vertical dimension in pixel or NULL on error.
     *
     * @return  int|NULL
     */
    public function getHeight(): ?int
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return NULL;
        }

        return imagesy($this->_image);
    }

    /**
     * Draw a point (aka paint a pixel).
     *
     * This paints the pixel at position ($x px, $y px) with a $color.
     * This color defaults to black.
     *
     * @param   int  $x      horizontal position (left value)
     * @param   int  $y      vertical position (top value)
     * @param   int  $color  the point color
     * @return  bool
     */
    public function drawPoint(int $x, int $y, ?int $color = null): bool
    {
        assert($x >= 0, '$x must not be negative.');
        assert($y >= 0, '$y must not be negative.');

        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /* argument 3 */
        if (is_null($color)) {
            $color = $this->black;
        }

        return (bool) imagesetpixel($this->_image, $x, $y, $color);
    }

    /**
     * Draw a line.
     *
     * This draws a straight line at position ($x px, $y px).
     *
     * The line has the color set by the argument $color.
     * This defaults to black.
     *
     * @param   int  $x1     horizontal position (start)
     * @param   int  $y1     vertical position (start)
     * @param   int  $x2     horizontal position (end)
     * @param   int  $y2     vertical position (end)
     * @param   int  $color  the line color
     * @return  bool
     */
    public function drawLine(int $x1, int $y1, int $x2, int $y2, ?int $color = null): bool
    {
        assert($x1 >= 0, '$x1 must not be negative.');
        assert($y1 >= 0, '$y1 must not be negative.');
        assert($x2 >= 0, '$x2 must not be negative.');
        assert($y2 >= 0, '$y2 must not be negative.');

        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /* argument 5 */
        if (is_null($color)) {
            $color = $this->black;
        }

        return (bool) imageline($this->_image, $x1, $y1, $x2, $y2, $color);
    }

    /**
     * Draws a text string at position ($x px, $y px).
     *
     * The string has the color set by the argument $color.
     * This defaults to black.
     *
     * The $color is an integer value, that you can get via
     * the function Image::getColor().
     *
     * In addition you may also use on of the predefined colors:
     * aqua, black, blue, fuchsia, gray, green, lime, maroon,
     * navy, olive, purple, red, silver, teal, white, or yellow.
     *
     * Example:
     * <code>
     * $image = new Image();
     * $image->drawString('Hello World!', 0, 0, $image->black);
     * </code>
     *
     * The argument $font determines the font type and size.
     * The values 1-5 are used for a predefined system font.
     * Where 1 is the smallest font-size and 5 is the largest.
     * The font itself belongs to the sans-serif family.
     * Note that you can load custom fonts using the PHP function
     * imageloadfont().
     * The argument $font defaults to 2, which is a 10px font.
     *
     * The argument $asVerticalString is used to switch from printing text horizontal
     * to vertical. Note that vertical strings are rotated 90° left (anticlockwise).
     * This means they will start at ($x px, $y px) upwards, so you should make sure,
     * there is enough space for the string on your image above the starting point.
     *
     * All arguments except $text may be skipped by assigning the NULL value.
     *
     * @param   string    $text              the text to draw
     * @param   int       $x                 horizontal position
     * @param   int       $y                 vertical position
     * @param   int       $color             the text color
     * @param   int       $font              font size (1 through 5)
     * @param   bool      $asVerticalString  switch between horizontal and vertical print
     * @return  bool
     */
    public function drawString(string $text, int $x = 0, int $y = 0, ?int $color = null, int $font = 2, bool $asVerticalString = false): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        if (is_null($color)) {
            $color = $this->black;
        }

        if ($asVerticalString) {
            return (bool) imagestringup($this->_image, $font, $x, $y, $text, $color);
        } else {
            return (bool) imagestring($this->_image, $font, $x, $y, $text, $color);
        }
    }

    /**
     * Draw a formatted text string with a true-type font.
     *
     * This is the same as Image::drawString() except, that it allows
     * some true-type font of your choosing to be used, accepts a custom
     * font size and allows to freely choose an angle of rotation.
     *
     * The rotation is performed anticlockwise.
     * To create a text that flows straight up, set $angle to 90.
     *
     * The default font is 'tahoma'.
     * The path to the font file needs to start with a '/' sign.
     *
     * Note that $fontfile may also be just the name of a font, like 'tahoma',
     * 'arial', or 'helvetica'.
     * If the name instead of the file is provided, this function will try
     * to use a true-type font of the same name, which is installed on the
     * current system. Of course this requires that the font actually IS
     * installed on the system.
     *
     * This function requires GD and FreeType libraries.
     *
     * @param   string    $text       the text to draw
     * @param   int       $x          horizontal position
     * @param   int       $y          vertical position
     * @param   int       $color      the text color
     * @param   string    $fontfile   path and name of a true-type (*.ttf) font
     * @param   int       $fontsize   size
     * @param   int       $angle      rotation 0 through 360 degrees
     * @return  bool
     */
    public function drawFormattedString(string $text, int $x = 0, ?int $y = null, ?int $color = null, ?string $fontfile = null, int $fontsize = 10, int $angle = 0): bool
    {
        /*
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /* argument 3 */
        if (is_null($y)) {
            $y = $fontsize;
        }

        /* argument 4 */
        if (is_null($color)) {
            $color = $this->black;
        }

        /* argument 5 */
        if (is_null($fontfile)) {
            $fontfile = 'tahoma';
        }

        /* set path on Windows-systems */
        if (is_string($fontfile) && !is_file($fontfile)) {
            // Note: $_SERVER global var has different contents depending on from where it was called.
            $winDir =  isset($_SERVER['WINDIR']) ? (string) $_SERVER['WINDIR'] : // web client call
                isset($_SERVER['windir']) ? (string) $_SERVER['windir'] : null; // command line call (i.e. unit test)
            if (is_string($winDir)) {
                $fontfile = $winDir . DIRECTORY_SEPARATOR . 'Fonts' . DIRECTORY_SEPARATOR . $fontfile . '.ttf';
            }
        }

        return (bool) imagettftext($this->_image, $fontsize, $angle, $x, $y, $color, $fontfile, $text);
    }

    /**
     * Draw an ellipse.
     *
     * This draws an ellipse at position ($x px, $y px).
     *
     * With the dimensions $width px to $height px.
     * Note that you can set $width = $height to create a circle.
     * Setting $height = NULL does the same.
     *
     * The $color is an integer value, that you can get via
     * the function Image::getColor().
     *
     * In addition you may also use on of the predefined colors:
     * aqua, black, blue, fuchsia, gray, green, lime, maroon,
     * navy, olive, purple, red, silver, teal, white, or yellow.
     *
     * Example:
     * <code>
     * $image = new Image();
     * $image->drawEllipse(20, 20, 30, null, $image->black);
     * </code>
     *
     * The same applies to $fillColor. When provided, a filled
     * object is created. You can create object that are not filled,
     * by setting this to NULL.
     *
     * Setting $start and $end will create an arcus, that starts at
     * $start degrees and goes to $end degrees. Both values can be NULLed.
     * Note that $start and $end can also be negative.
     *
     * @param   int  $x          horizontal position
     * @param   int  $y          vertical position
     * @param   int  $width      horizontal dimension in pixel
     * @param   int  $height     vertical dimension in pixel
     * @param   int  $color      the color of the contour line
     * @param   int  $fillColor  the color to flood fill the ellipse with
     * @param   int  $start      angle in degrees (start)
     * @param   int  $end        angle in degrees (end)
     * @return  bool
     */
    public function drawEllipse(int $x, int $y, int $width, ?int $height = null, ?int $color = null, ?int $fillColor = null, ?int $start = null, ?int $end = null): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /* argument 4 */
        if (is_null($height)) {
            $height = $width;
        }

        /* argument 5 */
        if (is_null($color) && is_null($fillColor)) {
            $color = $this->black;
        }

        /* create ellipse */
        if (is_null($start) || is_null($end)) {

            /* filling */
            if (!is_null($fillColor)) {
                imagefilledellipse($this->_image, $x, $y, $width, $height, $fillColor);
            }

            /* contour */
            if (!is_null($color)) {
                imageellipse($this->_image, $x, $y, $width, $height, $color);
            }

            return true;

        /* create arc */
        } else {

            /* filling */
            if (!is_null($fillColor)) {
                imagefilledarc($this->_image, $x, $y, $width, $height, $start, $end, $fillColor, IMG_ARC_PIE);
            }

            /* contour */
            if (!is_null($color)) {
                imagearc($this->_image, $x, $y, $width, $height, $start, $end, $color);
            }

            return true;
        }
    }

    /**
     * Draws a rectangle at position ($x px, $y px).
     *
     * With the dimensions $width px to $height px.
     * Note that you can set $width = $height to create a square.
     * Setting $height = NULL does the same.
     *
     * The $color is an integer value, that you can get via
     * the function Image::getColor().
     *
     * In addition you may also use on of the predefined colors:
     * aqua, black, blue, fuchsia, gray, green, lime, maroon,
     * navy, olive, purple, red, silver, teal, white, or yellow.
     *
     * Example:
     * <code>
     * $image = new Image();
     * $image->drawRectangle(10, 10, 30, null, $image->black);
     * </code>
     *
     * The same applies to $fillColor. When provided, a filled
     * object is created. You can create object that are not filled,
     * by setting this to NULL.
     *
     * @param   int  $x          horicontal position in pixel
     * @param   int  $y          vertical position in pixel
     * @param   int  $width      horizontal dimension in pixel
     * @param   int  $height     vertical dimension in pixel
     * @param   int  $color      color of contour line
     * @param   int  $fillColor  inner color
     * @return  bool
     */
    public function drawRectangle(int $x, int $y, int $width, ?int $height = null, ?int $color = null, ?int $fillColor = null): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /* argument 4 */
        if (is_null($height)) {
            $height = $width;
        }

        /* argument 5 */
        if (is_null($color) && is_null($fillColor)) {
            $color = $this->black;
        }

        /* calculation */
        $width  += $x;
        $height += $y;

        /* filling */
        if (!is_null($fillColor)) {
            imagefilledrectangle($this->_image, $x, $y, $width, $height, $fillColor);
        }

        /* contour */
        if (!is_null($color)) {
            imagerectangle($this->_image, $x, $y, $width, $height, $color);
        }

        return true;
    }

    /**
     * Draws a polygon at position ($x px, $y px).
     *
     * $points is a two dimensional array of the vertices.
     * Example:
     * <code>
     * $points = array(
     * 0 => array( 10, 0  ),
     * 1 => array( 20, 10 ),
     * 2 => array(  0, 10 )
     * );
     * $image = new Image();
     * $image->drawPolygon($points);
     * $image->outputToScreen();
     * </code>
     * (The code above will output a triangle.)
     *
     * The $color is an integer value, that you can get via
     * the function Image::getColor().
     *
     * In addition you may also use on of the predefined colors:
     * aqua, black, blue, fuchsia, gray, green, lime, maroon,
     * navy, olive, purple, red, silver, teal, white, or yellow.
     *
     * Example:
     * <code>
     * $image = new Image();
     * $image->drawPolygon($points, $image->black);
     * </code>
     *
     * The same applies to $fillColor. When provided, a filled
     * object is created. You can create object that are not filled,
     * by setting this to NULL.
     *
     * @param   array  $points     a list of vertices
     * @param   int    $x          horicontal position
     * @param   int    $y          vertical position
     * @param   int    $color      color of contour line
     * @param   int    $fillColor  inner color
     * @return  bool
     */
    public function drawPolygon(array $points, int $x = 0, int $y = 0, ?int $color = null, ?int $fillColor = null): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /* argument 4 */
        if (is_null($color)) {
            if (is_null($fillColor)) {
                $color = $this->black;
            }
        }

        $mergedPoints = array();
        /* calculation */
        assert(!isset($point), 'cannot redeclare variable $point');
        foreach ($points as $point)
        {
            assert(is_array($point) && count($point) === 2 && is_int($point[0]) && is_int($point[1]), 'Invalid value for argument 1.');
            $mergedPoints[] = $point[0] + $x;
            $mergedPoints[] = $point[1] + $y;
        }
        $count = count($points);
        unset($points, $point);

        /* need at least 3 vertices */
        if ($count < 3) {
            return false;
        }

        /* filling */
        if (!is_null($fillColor)) {
            imagefilledpolygon($this->_image, $mergedPoints, $count, $fillColor);
        }

        /* contour */
        if (!is_null($color)) {
            imagepolygon($this->_image, $mergedPoints, $count, $color);
        }

        return true;
    }

    /**
     * Fill with a color.
     *
     * This does a flood fill at position ($x px, $y px).
     *
     * The $fillColor is an integer value, that you can get via
     * the function Image::getColor().
     *
     * In addition you may also use on of the predefined colors:
     * aqua, black, blue, fuchsia, gray, green, lime, maroon,
     * navy, olive, purple, red, silver, teal, white, or yellow.
     *
     * Example:
     * <code>
     * $image = new Image();
     * $image->fill($image->white);
     * </code>
     *
     * If $borderColor is defined, flood fill will stop at the
     * this color, otherwise it will stop at any color that is
     * different from pixel ($x px, $y px).
     *
     * @param   int  $fillColor    the filled area will get this color
     * @param   int  $x            horicontal position
     * @param   int  $y            vertical position
     * @param   int  $borderColor  flood fill will stop at this color
     * @return  bool
     */
    public function fill(int $fillColor, int $x = 0, int $y = 0, ?int $borderColor = null): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;

        }

        if (is_null($borderColor)) {
            return imagefill($this->_image, $x, $y, $fillColor);
        } else {
            return imagefilltoborder($this->_image, $x, $y, $borderColor, $fillColor);
        }
    }

    /**
     * Enable / disable alpha blending.
     *
     * Enables alpha blending if set to bool(true) and
     * disables it when set to bool(false).
     *
     * Works only with truecolor images.
     *
     * If $saveAlpha is true and the output is a PNG image,
     * the alpha channel information will get saved with the
     * file, otherwise not. Note that this setting only affects
     * PNG images.
     *
     * You should be aware, that IE 6.0 and other older browsers
     * do not support alpha channels in PNG images by default.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   bool  $isEnabled  on / off
     * @param   bool  $saveAlpha  on / off
     * @return  bool
     */
    public function enableAlpha(bool $isEnabled = true, ?bool $saveAlpha = null): bool
    {
        if (is_null($saveAlpha)) {
            $saveAlpha = ( $isEnabled === true );
        }

        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /* Enable/Disable Alpha-blending */
        $isSuccess = imagealphablending($this->_image, (bool) $isEnabled);
        if ($isSuccess) {
            $this->_alpha = (bool) $isEnabled;
            if (is_bool($saveAlpha)) {
                imagesavealpha($this->_image, $saveAlpha);
            }
        }
        return $isSuccess;
    }

    /**
     * Enable / disable antialiasing.
     *
     * Enables antialiasing if set to bool(true) and
     * disables it when set to bool(false).
     *
     * Works only with truecolor images.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   bool  $isEnabled  on / off
     * @return  bool
     */
    public function enableAntialias(bool $isEnabled = true): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken() || !\function_exists('imageantialias')) { // prior to PHP 7.2 this function wasn't always available
            return false;
        }

        return imageantialias($this->_image, (bool) $isEnabled);
    }

    /**
     * Get font width.
     *
     * This is an OO-style alias of PHP's imagefontheight() function.
     * See the PHP manual for a full description.
     *
     * The argument $font determines the font type.
     * The values 1-5 are used for a predefined system font.
     * Where 1 is the smallest font-size and 5 is the largest.
     * The font itself belongs to the sans-serif family.
     * Note that you can load custom fonts using the PHP function
     * imageloadfont().
     *
     * This function is usefull to calculate how much space a string
     * is going to take on the picture when using this font.
     *
     * @param   int  $font   a font resource
     * @return  int
     */
    public static function getFontWidth(int $font): int
    {
        return imagefontwidth($font);
    }

    /**
     * Get font height.
     *
     * This is an OO-style alias of PHP's imagefontheight() function.
     * See the PHP manual for a full description.
     *
     * The argument $font determines the font type.
     * The values 1-5 are used for a predefined system font.
     * Where 1 is the smallest font-size and 5 is the largest.
     * The font itself belongs to the sans-serif family.
     * Note that you can load custom fonts using the PHP function
     * imageloadfont().
     *
     * This function is usefull to calculate how much space a string
     * is going to take on the picture when using this font.
     *
     * @param   int  $font   a font resource
     * @return  int
     */
    public static function getFontHeight(int $font): int
    {
        return imagefontheight($font);
    }

    /**
     * Get color values (red,green,blue,alpha).
     *
     * This is an OO-style alias of PHP's imagecolorsforindex() function.
     * See the PHP manual for a full description.
     *
     * Example:
     * <code>
     * $image = new Image();
     * print_r($image->getColorValues($image->white));
     * </code>
     * Will output:
     * <code>
     * Array
     * (
     *     [red] => 255
     *     [green] => 255
     *     [blue] => 255
     *     [alpha] => 0
     * )
     * </code>
     *
     * @param   int  $color   a color resource
     * @return  array|NULL
     */
    public function getColorValues(int $color): ?array
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return NULL;
        }

        return imagecolorsforindex($this->_image, $color);
    }

    /**
     * Get color at pixel ($x,$y).
     *
     * This is an OO-style alias of PHP's imagecolorat() function.
     * See the PHP manual for a full description.
     *
     * This function returns NULL on error.
     *
     * @param   int  $x  horicontal position
     * @param   int  $y  vertical position
     * @return  int|NULL
     */
    public function getColorAt(int $x, int $y): ?int
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return NULL;

        } elseif ($x < 0 || $y < 0) {
            return NULL;

        } elseif ($x > $this->getWidth() || $y > $this->getHeight()) {
            return NULL;

        }
        $color = imagecolorat($this->_image, $x, $y);
        if (is_int($color) && $this->isTruecolor()) {
            $r = ($color >> 16) & 0xFF;
            $g = ($color >> 8) & 0xFF;
            $b = $color & 0xFF;
            assert(is_int($r) && is_int($g) && is_int($b), 'is_int($r) && is_int($g) && is_int($b)');
            $color = $this->getColor($r, $g, $b);
        }
        return $color;
    }

    /**
     * Get image info.
     *
     * This is an OO-style alias of PHP's getimagesize() function.
     * See the PHP manual for a full description.
     *
     * The following two lines are equal:
     * <code>
     * $imgInfo = Image::getSize('foo.png');
     * $imgInfo = getimagesize('foo.png');
     * </code>
     *
     * Just choose the style that you prefer.
     *
     * @param   string  $filename  relative file path
     * @return  array
     */
    public static function getSize(string $filename): array
    {
        return is_file($filename) ? getimagesize($filename) : array();
    }

    /**
     * Get a color for the current index.
     *
     * This is an OO-style alias of PHP's imagecolorallocate() function.
     * See the PHP manual for a full description.
     *
     * The $opacity parameter is available as of PHP 4.3.2.
     * It is a float between 0.0 (completely opaque) and 1.0
     * (completely transparent).
     * You may translate this to 0% through 100% opacity.
     * Note that opacity only applies when alpha blending has
     * has not been disabled and the underlying function is
     * available.
     *
     * If the current palette already has the specified color,
     * the existing color will be returned.
     * Otherwise the new color will get appended to the palette.
     *
     * Returns NULL on error.
     *
     * @param   int    $r        red value
     * @param   int    $g        green value
     * @param   int    $b        blue value
     * @param   float  $opacity  alpha value
     * @return  int|NULL
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function getColor(int $r, int $g, int $b, ?float $opacity = null): ?int
    {
        assert($r >= 0 && $r <= 255, 'Invalid argument $r: must be in range [0,255].');
        assert($g >= 0 && $g <= 255, 'Invalid argument $g: must be in range [0,255].');
        assert($b >= 0 && $b <= 255, 'Invalid argument $b: must be in range [0,255].');
        assert(!$opacity || ($opacity >= 0.0 && $opacity <= 1.0), 'Invalid argument $opacity: must be in range [0.0,1.0].');

        if ($this->isBroken()) {
            return NULL;
        }

        if (!is_null($opacity) && $opacity > 0.0 && $opacity <= 1.0) {
            $opacity = (int) floor($opacity * 127);

            /* check if color already exists */
            $color = imagecolorexactalpha($this->_image, $r, $g, $b, $opacity);

            /* if color is not found, allocate a new color */
            if ($color <= -1) {
                $color = imagecolorallocatealpha($this->_image, $r, $g, $b, $opacity);
            }
            /* and if that didn't work, return NULL */
            if ($color <= -1) {
                $color = NULL;
            }
            return $color;

        } else {

            /* check if color already exists */
            $color = imagecolorexact($this->_image, $r, $g, $b);

            /* if color is not found, allocate a new color */
            if ($color <= -1) {
                $color = imagecolorallocate($this->_image, $r, $g, $b);
            }
            /* and if that didn't work, return NULL */
            if ($color <= -1) {
                $color = NULL;
            }
            return $color;
        }
    }

    /**
     * Get current line width in pixel as an integer value.
     *
     * Returns NULL on error.
     *
     * @return  int|NULL
     */
    public function getLineWidth(): ?int
    {
        /**
         * error - broken image
         */
        if ($this->isBroken() || !is_int($this->_lineWidth)) {
            return NULL;
        }

        return $this->_lineWidth;
    }

    /**
     * Set line width.
     *
     * Sets the line width of the current brush to $width.
     * The setting will apply to whatever you draw, until you
     * call this function again.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   int  $width  size in pixel
     * @return  bool
     */
    public function setLineWidth(int $width): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /* argument 1 */
        if ($width < 1) {
            return false;
        }

        assert(!isset($success), 'cannot redeclare variable $success');
        $success = (bool) imagesetthickness($this->_image, $width);
        if ($success) {
            $this->_lineWidth = $width;
        }
        return $success;
    }

    /**
     * Set line style.
     *
     * This is an OO-style alias of PHP's imagesetstyle() function.
     * See the PHP manual for details.
     *
     * Example:
     * <code>
     * $r = $image->red;
     * $w = $image->white;
     * // dotted line
     * $image->setLineStyle($r, $w);
     * // 3px dashed line
     * $image->setLineStyle($r, $r, $r, $w, $w, $w);
     * </code>
     *
     * To reset the line style, call this function with no arguments.
     *
     * @return  bool
     */
    public function setLineStyle(): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /* reset line style */
        if (func_num_args() === 0) {
            imagesetstyle($this->_image, array($this->black));
            $this->_lineStyle = null;
            return true;
        }

        /* get line style */
        $style = array();
        for ($i = 0; $i < func_num_args(); $i++)
        {
            $color = func_get_arg($i);
            assert(is_int($color), 'Wrong type for argument ' . $i . '. Integer expected');
            $style[] = (int) $color;
        }

        /* set line style */
        assert(!isset($success), 'cannot redeclare variable $success');
        $success = (bool) imagesetstyle($this->_image, $style);
        if ($success) {
            $this->_lineStyle = $style;
        }
        return $success;
    }

    /**
     * Replace one palette color by another.
     *
     * This replaces the palette color with the index $replacedColor
     * by the color with the index $newColor.
     * Note that $newColor can also be an associative array with
     * the keys 'red', 'green' and 'blue'.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * Note that this only works on palette (non-truecolor)
     * images and thus returns false if the image is truecolor.
     * Also note, that all images are converted to truecolor by
     * default.
     *
     * Examples:
     * <code>
     * $image->replaceIndexColor(0, $image->blue);
     * $array = $image->getColorValues($image->red);
     * $image->replaceIndexColor(1, $array);
     * $image->replaceIndexColor(2, array('red'=>0,'green'=>255,'blue'=>100));
     * </code>
     *
     * @param   int        $replacedColor   index of replaced color
     * @param   array|int  $newColor        the color that should be assigned
     * @return  bool
     * @throws  \Yana\Core\Exceptions\OutOfBoundsException  when replaced color is not in image palette
     */
    public function replaceIndexColor(int $replacedColor, $newColor): bool
    {
        assert(is_int($newColor) || is_array($newColor), 'Wrong type for argument 2. Array or Integer expected');

        /*
         * error - broken image
         */
        if ($this->isBroken() || $this->isTruecolor()) {
            return false;
        }

        /*
         *  argument 1 - index out of bounds
         */
        if ($replacedColor < 0 || $replacedColor > imagecolorstotal($this->_image)) {
            throw new \Yana\Core\Exceptions\OutOfBoundsException("Replaced color is not in image palette.", \Yana\Log\TypeEnumeration::WARNING);
        }

        /* argument 2 */
        $red = $newColor['red'];
        $green = $newColor['green'];
        $blue = $newColor['blue'];
        if (is_array($newColor) && isset($red) && isset($green) && isset($blue)) {
            $color          = array();
            $color['red']   = (int) $newColor['red'];
            $color['green'] = (int) $newColor['green'];
            $color['blue']  = (int) $newColor['blue'];
            if ($color['red'] < 0   || $color['red']   > 255) {
                return false;
            } elseif ($color['green'] < 0 || $color['green'] > 255) {
                return false;
            } elseif ($color['blue'] < 0  || $color['blue']  > 255) {
                return false;
            } else {
                // intentionally left blank
            }
        } else {
            $color = imagecolorsforindex($this->_image, $newColor);
        }

        assert(!isset($success), 'cannot redeclare variable $success');
        $success = is_array($color);
        if ($success) {
            imagecolorset($this->_image, $replacedColor, $color['red'], $color['green'], $color['blue']);
        }
        return $success;
    }

    /**
     * Replaces a color by a new one.
     *
     * To be more technical, this is done by setting the replaced
     * color as transparent, setting the new color as background
     * and merging the results to a new image.
     * Then reseting transparency and background color to the
     * previously set values.
     *
     * This may take some time - if you prefer a more performant
     * solution, see Image::replaceIndexColor() instead.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   int  $replacedColor   index of replaced color
     * @param   int  $newColor        index of the new color
     * @return  bool
     * @see     Image::replaceIndexColor()
     */
    public function replaceColor(int $replacedColor, int $newColor): bool
    {
        assert($replacedColor > 0, 'Wrong type for argument 1. Positive integer expected');
        assert($newColor > 0, 'Wrong type for argument 2. Positive integer expected');

        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /**
         * backup old values
         */
        $oldBackgroundColor = $this->_backgroundColor;
        $oldImage = $this->_image;
        $width = (int) $this->getWidth();
        $height = (int) $this->getHeight();

        /**
         * replace colors
         */
        imagecolortransparent($oldImage, $replacedColor);
        $this->_backgroundColor = $newColor;
        $this->_createImage($width, $height);
        imagecopyresized($this->_image, $oldImage, 0, 0, 0, 0, $width, $height, $width, $height);
        imagedestroy($oldImage);

        /**
         * restore backup
         */
        $this->_backgroundColor = $oldBackgroundColor;

        return true;
    }

    /**
     * Set current brush.
     *
     * Sets the brush used by imageline(), imagepolygon() et cetera
     * to the image $brush.
     *
     * Note! This only SETS the brush. To actually USE the brush, you need
     * to draw using the special color constants IMG_COLOR_BRUSHED or IMG_COLOR_STYLEDBRUSHED.
     *
     * Note further that whenever you change the brush (resize it or change its color) you have to
     * reapply the brush prior to use.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   \Yana\Media\Brush  $brush  a brush resource
     * @return  bool
     */
    public function setBrush(\Yana\Media\Brush $brush): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        $resource = $brush->getResource();
        $test = imagesetbrush($this->_image, $resource);

        if ($test) {
            $this->_brush = $brush;
        }
        return (bool) $test;
    }

    /**
     * Set current background color.
     *
     * If you don't specify a background color, than the color
     * is reset.
     *
     * By default the background color will be set to the transparent
     * color of the image if it has any. Otherwise it defaults to white.
     *
     * If the argument $replaceOldColor is set to true, it will replace
     * the all pixel of the previous background color with the new one.
     * It it is set to false, the new background color will get appended
     * and all pixel of the old color will remain as is.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   int   $backgroundColor  index of new background color
     * @param   bool  $replaceOldColor  set true for replace old color , false otherweise
     * @return  bool
     */
    public function setBackgroundColor(?int $backgroundColor = null, bool $replaceOldColor = true): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /* initialize background color */
        if (is_null($backgroundColor) && is_null($this->_backgroundColor)) {
            $color = $this->getTransparency();

            if (is_int($color) && $color > -1) {
                $this->_backgroundColor = $color;
            } else {
                $this->_backgroundColor = $this->getColor(254, 254, 254, 1.0);
            }
            return true;

        /* set background color */
        } elseif (is_null($this->_backgroundColor)) {
            $this->_backgroundColor = $backgroundColor;
            return true;

        /* get background color setting */
        } elseif (is_null($backgroundColor)) {
            $backgroundColor = $this->white;
        }

        /* replace background color */
        if ($replaceOldColor) {
            $test = $this->replaceColor($this->_backgroundColor, $backgroundColor);
            if ($test) {
                $this->_backgroundColor = $backgroundColor;
            }
            return (bool) $test;
        } else {
            $this->_backgroundColor = $backgroundColor;
            return true;
        }
    }

    /**
     * Get current background color.
     *
     * Returns the current background color as an integer or NULL on error.
     *
     * @return  int|NULL
     */
    public function getBackgroundColor(): ?int
    {
        /**
         * error - broken image
         */
        if ($this->isBroken() || !is_int($this->_backgroundColor)) {
            return NULL;
        }

        return $this->_backgroundColor;
    }

    /**
     * Check if image is interlaced.
     *
     * This returns bool(true) if interlacing is turned on for the current image.
     * Returns bool(false) otherwise.
     *
     * @return  bool
     */
    public function isInterlaced(): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken() || !is_bool($this->_interlace)) {
            return false;
        }

        return $this->_interlace;
    }

    /**
     * Switch interlacing on / off.
     *
     * If $isInterlaced is true, interlacing is set to on,
     * otherwise set to off.
     *
     * If the image is a JPEG, this setting will set the output
     * to be a progressive image.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   bool  $isInterlaced   on / off
     * @return  bool
     */
    public function enableInterlace(bool $isInterlaced = true): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        $interlace = imageinterlace($this->_image, $isInterlaced ? 1 : 0) === 1;
        $this->_interlace = $interlace;
        return $interlace;
    }

    /**
     * Check if image has alpha channel.
     *
     * This returns bool(true) if alpha channel is turned on for the current image.
     * Returns bool(false) otherwise.
     *
     * Compatibility note:
     * This function has been renamed in version 2.8.8
     * from "hasAlphaChannel()" to "hasAlpha()".
     *
     * @return  bool
     */
    public function hasAlpha(): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken() || !is_bool($this->_alpha)) {
            return false;
        }

        return $this->_alpha;
    }

    /**
     * Set gamma correction.
     *
     * Gamma can be any positive float 0.1 trough 10.0
     * The base is always 1.0 (100%). So e.g. calling setGamma(0.1)
     * sets the gamma of the image to 10% and calling setGamma(2.0)
     * sets the gamma to 200%.
     * To reset the gamma value call setGamma(1.0).
     *
     * This function only works on truecolor images.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   float  $gamma  effect strength
     * @return  bool
     */
    public function setGamma(float $gamma): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /* argument 1 */
        if ($gamma < 0.1 || $gamma > 10.0) {
            return false;
        }

        $test = imagegammacorrect($this->_image, $this->_gamma, $gamma);

        if ($test == true) {
            $this->_gamma = $gamma;
        }
        return (bool) $test;
    }

    /**
     * Rotate the image.
     *
     * This rotates the image anticlockwise by $angle degrees.
     *
     * @param   float  $angle   angle of rotation in degree
     * @return  bool
     */
    public function rotate(float $angle): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        $bgColor = (int) $this->getBackgroundColor();
        $newImage = imagerotate($this->_image, (float) $angle, $bgColor);
        $width = (int) $this->getWidth();
        $height = (int) $this->getHeight();

        /**
         * The background color could be fully transparent, in which case drawing a transparent rectangle would not actually erase the old image.
         * So we first create a background with a color which we know to be 100% opaque: either black, or white.
         *
         * We guess whether the background color that would provide the better contrast is black or white, based on the top-left corner pixel.
         * Because, if the picture to rotate is a white rectangle, rotating a white rectangle on a white background would do nothing.
         */
        $fgColor = $this->getColorAt(0, 0) !== $this->white ? $this->white : $this->black;
        $this->drawRectangle(0, 0, $width, $height, null, $fgColor);
        /* Now we replace it with the actual background color. If that color is transparent, this will do nothing */
        $this->drawRectangle(0, 0, $width, $height, null, $bgColor);

        imagecopyresized($this->_image, $newImage, 0, 0, 0, 0, $width, $height, $width, $height);
        return true;
    }

    /**
     * Resize the canvas.
     *
     * This resizes the canvas to $width x $height.
     *
     * You may set either $width or $height to NULL to get a proportional resize.
     * If $width or $height is smaller than the current value, the parts of the
     * image that do not fit will get cut.
     *
     * The $paddingLeft and $paddingTop parameters set left and top padding for the canvas.
     * Setting both to 0 will position the left-top corner of the original image
     * at the point (0, 0) on the new image. If the original image was bigger than
     * the new, the parts at the right and the bottom the are outside the canvas will
     * get cut.
     * Note that you may use negative numbers here. Still you may not completely move
     * the image outside the canvas.
     *
     * If $left and/or $top are not provided, the image will get copied to the center
     * of the canvas.
     *
     * Examples:
     * <code>
     * // original image is 300x200px
     * // this will return true, but does nothing at all
     * $image->resizeCanvas(300);
     * // resize to 150x100px
     * $image->resizeCanvas(150);
     * // same effect
     * $image->resizeCanvas(null, 100);
     * // cut 20px off the top
     * // the image is centered horizontally
     * $image->resizeCanvas(null, null, null, -20);
     * // cut 50px off the left
     * // the image is centered vertically
     * $image->resizeCanvas(null, null, -50);
     * // cut 50px off the right
     * // the image is centered vertically
     * $image->resizeCanvas(null, null, 50);
     * // combination of all
     * $image->resizeCanvas(200, 100, 10, -5);
     * // these will return false
     * $image->resizeCanvas(0);
     * $image->resizeCanvas(-1);
     * // padding value out of range
     * $image->resizeCanvas(null, null, 301);
     * $image->resizeCanvas(null, null, 300);
     * $image->resizeCanvas(null, null, null, -200);
     * </code>
     *
     * Returns bool(false) on error.
     *
     * @param   int       $width        horizontal dimension in pixel
     * @param   int       $height       vertical dimension in pixel
     * @param   int       $paddingLeft  horizontal offset
     * @param   int       $paddingTop   vertical offset
     * @param   int|array $canvasColor  array of red, green, yellow values (0 through 255) or an integer value to
     *                                  identify the canvas color, defaults to background color
     * @return  bool
     * @since   2.8.8
     */
    public function resizeCanvas(?int $width = null, ?int $height = null, ?int $paddingLeft = null, ?int $paddingTop = null, $canvasColor = null): bool
    {
        assert(is_null($canvasColor) || is_array($canvasColor) || (is_int($canvasColor) && $canvasColor > 0),
            'Wrong type for argument 5. Integer or array expected');

        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        $currentHeight = (int) $this->getHeight();
        $currentWidth  = (int) $this->getWidth();

        if (is_null($width) && is_null($height)) {
            $width  = $currentWidth;
            $height = $currentHeight;
        }

        /* proportional image scaling */
        if (is_null($width) && is_int($height) && $currentHeight > 0) {

            $width = (int) floor(($height * $currentWidth) / $currentHeight);
            assert(is_int($width), 'is_int($width)');

        }

        if ((int) $width < 1) {
            \Yana\Log\LogManager::getLogger()->addLog(
                "Invalid value for argument 1. Width cannot be 0 or negative.", \Yana\Log\TypeEnumeration::WARNING
            );
            return false;
        }

        /* argument 2 */
        if (is_null($height) && is_int($width) && $currentWidth > 0) {

            /* proportional image scaling */
            $height = (int) floor(($width * $currentHeight) / $currentWidth);
            assert(is_int($height), 'is_int($height)');

        }
        if ((int) $height < 1) {
            \Yana\Log\LogManager::getLogger()->addLog(
                "Invalid value for argument 2. Height cannot be 0 or negative.", \Yana\Log\TypeEnumeration::WARNING
            );
            return false;
        }

        /* argument 3 */
        if (is_null($paddingLeft)) {

            /* horizontally center image */
            $paddingLeft = round(( $width - $currentWidth )  / 2);
            assert(is_numeric($paddingLeft), 'Unexpected result: $paddingLeft');

        }
        if (abs($paddingLeft) >= $width) {
            $message = "Invalid value for argument 3. Left offset {$paddingLeft}px is bigger than image width ".
                "{$currentWidth}px.";
            \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);
            return false;
        }

        /* argument 4 */
        if (is_null($paddingTop)) {

            /* horizontally center image */
            $paddingTop = round(( $height - $currentHeight ) / 2);
            assert(is_numeric($paddingTop), 'Unexpected result: $paddingTop');

        }
        if (abs($paddingTop) >= $height) {
            $message = "Invalid value for argument 4. Top offset {$paddingTop}px is bigger than image height ".
                "{$currentHeight}px.";
            \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);
            return false;
        }

        if ($paddingLeft < 0) {
            $srcLeft  = (int) abs($paddingLeft);
            $dstLeft  = 0;
            $srcWidth = $currentWidth - $srcLeft;
        } elseif ($paddingLeft > 0) {
            $srcLeft  = 0;
            $dstLeft  = (int) abs($paddingLeft);
            $srcWidth = $currentWidth;
        } else {
            $srcLeft  = 0;
            $dstLeft  = 0;
            $srcWidth = $currentWidth;
        }

        if ($paddingTop < 0) {
            $srcTop    = (int) abs($paddingTop);
            $dstTop    = 0;
            $srcHeight = $currentHeight - $srcTop;
        } elseif ($paddingTop > 0) {
            $srcTop    = 0;
            $dstTop    = (int) abs($paddingTop);
            $srcHeight = $currentHeight;
        } else {
            $srcTop    = 0;
            $dstTop    = 0;
            $srcHeight = $currentHeight;
        }

        /* argument 5 */
        if (is_array($canvasColor)) {
            if (count($canvasColor) !== 3 && count($canvasColor) !== 4) {
                $message = "Invalid value for argument 5. Color needs to have red, green and blue values.";
                \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);
                return false;
            } else {
                $color = $canvasColor;
                $canvasColor = $this->getColor((int) array_shift($color), (int) array_shift($color), (int) array_shift($color), (float) array_shift($color));
                unset ($color);
            }
            if (!is_int($canvasColor)) {
                // @codeCoverageIgnoreStart
                /**
                 * This line should be unreachable, because getColor() always allocates a new color as necessary and this should never fail for as long as
                 * the image is a valid resource - and we already checked that it is. BUT just in case the code above changed or something entirely
                 * unexpected happened it is always better to be safe than sorry.
                 */
                $message = "Invalid value for argument 5. The argument is not a color.";
                \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);
                return false;
                // @codeCoverageIgnoreEnd
            }
        }

        if ($currentWidth !== $width || $currentHeight !== $height || $paddingTop != 0 || $paddingLeft != 0) {

            $oldImage = $this->_image;
            $this->_createImage($width, $height);

            if (is_int($canvasColor)) {
                /* This always works, even if the image is a palette image and the color index is not part of the palette.
                 * The GD library in that case silently converts the image to a truecolor image ... and then the color index is valid.
                 *
                 * I don't say that's a good idea (in fact, I think that's a terrible design choice). But that's the way it is.
                 */
                $this->fill($canvasColor, 0, 0);
            }

            imagecopy($this->_image, $oldImage, $dstLeft, $dstTop, $srcLeft, $srcTop, $srcWidth, $srcHeight);
            imagedestroy($oldImage);
        }
        return true;
    }

    /**
     * Resize the image.
     *
     * alias of Image::resize()
     *
     * @param   int  $width      horizontal dimension in pixel
     * @param   int  $height     vertical dimension in pixel
     * @return  bool
     * @since   2.8.8
     */
    public function resizeImage(?int $width = null, ?int $height = null): bool
    {
        return $this->resize($width, $height);
    }

    /**
     * Resize the image.
     *
     * This resizes the image to $width x $height.
     *
     * You may set either $width or $height to NULL to get a proportional resize.
     *
     * Returns bool(false) on error.
     *
     * IMPORTANT NOTE: this function resets some settings.
     * E.g. transparency, color depth a.s.o. will be lost.
     *
     * @param   int  $width      horizontal dimension in pixel
     * @param   int  $height     vertical dimension in pixel
     * @return  bool
     */
    public function resize(?int $width = null, ?int $height = null): bool
    {
        if ($this->isBroken() || (is_null($width) && is_null($height))) {
            return false;
        }

        $currentHeight = (int) $this->getHeight();
        $currentWidth  = (int) $this->getWidth();

        /* argument 1 */
        if (is_null($width)) {

            /* proportional image scaling */
            $width = (int) floor(($height * $currentWidth) / $currentHeight);
            assert(is_int($width), 'is_int($width)');

        }
        if ($width < 1) {
            $message = "Invalid value for argument 1. Width cannot be 0 or negative.";
            \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);
            return false;
        }

        /* argument 2 */
        if (is_null($height)) {

            /* proportional image scaling */
            $height = (int) floor(($width * $currentHeight) / $currentWidth);
            assert(is_int($height), 'is_int($height)');

        }
        if ($height < 1) {
            $message = "Invalid value for argument 2. Height cannot be 0 or negative.";
            \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);
            return false;
        }

        if ($currentWidth !== $width || $currentHeight !== $height) {

            $oldImage = $this->_image;
            $this->_createImage($width, $height);
            $w = $width;
            $h = $height;
            if (is_null($this->_transparency)) {
                imagecopyresampled($this->_image, $oldImage, 0, 0, 0, 0, $w, $h, $currentWidth, $currentHeight);
            } else {
                imagecopyresized($this->_image, $oldImage, 0, 0, 0, 0, $w, $h, $currentWidth, $currentHeight);
            }
            imagedestroy($oldImage);
        }
        return true;
    }

    /**
     * Get current transparency color.
     *
     * Returns NULL on error.
     *
     * @return  int|NULL
     */
    public function getTransparency(): ?int
    {
        /*
         * error - broken image
         */
        if ($this->isBroken()) {
            return NULL;
        }

        $transparency = imagecolortransparent($this->_image);
        if ($transparency === -1) {
            return NULL;
        } else {
            return $transparency;
        }
    }

    /**
     * Set transparency to a color.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * The $transparency color is an integer value, that you can get via
     * the function Image::getColor().
     *
     * In addition you may also use on of the predefined colors:
     * aqua, black, blue, fuchsia, gray, green, lime, maroon,
     * navy, olive, purple, red, silver, teal, white, or yellow.
     *
     * Example:
     * <code>
     * $image = new Image();
     * $image->setTransparency($image->white);
     * </code>
     *
     * If $transparency is not set, the current background color
     * will be used instead.
     *
     * @param   int|array    $transparency  new transparent color
     * @return  bool
     */
    public function setTransparency($transparency = null): bool
    {
        assert(is_array($transparency) || is_null($transparency) || is_int($transparency), 'Wrong type for argument 1. Integer or array expected');

        /*
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /* argument 1 */
        if (is_null($transparency)) {
            $transparency = $this->_backgroundColor;
        }

        if (is_int($transparency)) { // Check if color exists.
            $color = $transparency;
            $array = imagecolorsforindex($this->_image, $transparency);
            if (!is_array($array)) {
                return false;
            }

        } elseif (is_array($transparency) && isset($transparency['red']) && isset($transparency['green']) && isset($transparency['blue'])) {

            $red = $transparency['red'];
            $green = $transparency['green'];
            $blue =  $transparency['blue'];
            $color = imagecolorexactalpha($this->_image, $red, (int) $green, (int) $blue, 127);
            if ($color == -1) {
                $color = imagecolorexact($this->_image, $red, (int) $green, (int) $blue);
            }
            if ($color == -1) {
                $color = $this->getColor((int) $red, (int) $green, (int) $blue);
            }
            $array = $transparency;
        } else {
            return false;
        }

        $success = (imagecolortransparent($this->_image) === $color || imagecolortransparent($this->_image, (int) $color) != -1);
        if ($success) {
            $this->_transparency = $array;
        }
        return $success;
    }

    /**
     * Get number of colors in palette.
     *
     * Returns bool(false) on error.
     *
     * For palette images the number of colors is the number of colors used in the current image.
     * For truecolor images the maximum number of colors is returned, which is always 16 million.
     *
     * Note that if you reduce the color depth using $image->reduceColorDepth($number),
     * This function may NOT return $number, but instead the number of colors that are actually used
     * in the image, which may be less then $number.
     *
     * @return  int|NULL
     */
    public function getPaletteSize(): ?int
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return NULL;
        }

        $count = imagecolorstotal($this->_image);

        /* truecolor images should have 16 mio colors - but PHP returns 0 */
        if ($count < 1) {
            return 16000000;
        } else {
            return $count;
        }
    }

    /**
     * Reduce color depth to value.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * Reduces the color depth of the current image to $ammount colors.
     * The argument $ammount is an integer 2 through 256.
     *
     * The argument $dither triggers whether or not dithering is used
     * while reducing the colors for the current image.
     *
     * Kindly note that due to the fact that we are always allocating
     * at least 16 basic colors plus the background and (if one was provided)
     * a transparent color index, the color depth of the image will be reduced,
     * but the image palette will never have less than 16 colors.
     *
     * @param   int     $ammount    effect strength
     * @param   bool    $dither     on / off
     * @return  bool
     */
    public function reduceColorDepth(int $ammount, bool $dither = true): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /* argument 1 */
        if ($ammount < 2 || $ammount > 256) {
            return false;
        }

        /* backup background color */
        $backgroundColor = null;
        if (!is_null($this->_backgroundColor)) {
            $backgroundColor = $this->getColorValues($this->_backgroundColor);
        }

        /* convert palette */
        $test = imagetruecolortopalette($this->_image, $dither, $ammount);

        /* restore backup */
        if ($test) {
            if (!is_null($this->_transparency)) {
                $red = $this->_transparency['red'];
                $green = $this->_transparency['green'];
                $blue = $this->_transparency['blue'];
                $color = imagecolorresolve($this->_image, $red, $green, $blue);
                $color = imagecolortransparent($this->_image, $color);
                if ($color != -1) {
                    $this->_transparency = $this->getColorValues($color);
                }
                unset ($red, $green, $blue);
            }
            /* copy background color */
            if (!empty($backgroundColor)) {
                $red   = (int) $backgroundColor['red'];
                $green = (int) $backgroundColor['green'];
                $blue  = (int) $backgroundColor['blue'];
                $alpha = (int) $backgroundColor['alpha'];
                $this->_backgroundColor = imagecolorresolvealpha($this->_image, $red, $green, $blue, $alpha);
            }
            $this->_initColors(); // This will allocate 16 basic colors to be used as short-hands
        }
        return (bool) $test;
    }

    /**
     * Copy one portion of an image to another.
     *
     * This is an OO-style alias of PHP's imagecopymerge() function.
     * See the PHP manual for a full description.
     *
     * The argument $sourceImage can be another Image object,
     * a filename, or an image resource.
     *
     * If $width and / or $height is NULL or not provided,
     * then they are set to cover the full size of the image.
     *
     * If $sourceX and / or $sourceY is NULL or not provided,
     * then they are set to 0 (the upper left corner).
     *
     * If $destX and / or $destY is NULL or not provided,
     * then they are set to the same values as $sourceX and $sourceY.
     *
     * The $opacity parameter is available for truecolor images
     * only.
     * It is a float between 0.0 (completely opaque) and 1.0
     * (completely transparent).
     * You may translate this to 0% through 100% opacity.
     * Note that opacity only applies when alpha blending has
     * has not been disabled and the underlying function is
     * available.
     *
     * To copy the whole source image to the current image, just write:
     * <code>
     * $image->copyRegion($anotherImage);
     * // $image now is a copy of $anotherImage
     * </code>
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   \Yana\Media\Image  $sourceImage  filename or image resource
     * @param   int                $sourceX      horizontal position in pixel
     * @param   int                $sourceY      vertical position in pixel
     * @param   int                $width        horizontal dimension in pixel
     * @param   int                $height       vertical dimension in pixel
     * @param   int                $destX        horizontal position in pixel
     * @param   int                $destY        vertical position in pixel
     * @param   float              $opacity      alpha value in percent
     * @return  bool
     */
    public function copyRegion(\Yana\Media\Image $sourceImage, int $sourceX = 0, int $sourceY = 0, ?int $width = null, ?int $height = null, ?int $destX = null, ?int $destY = null, ?float $opacity = null): bool
    {
        assert(!$opacity || ($opacity >= 0.0 && $opacity <= 1.0), 'Invalid argument $opacity: must be in range [0.0,1.0].');
        /**
         * error - broken image
         */
        if ($this->isBroken() || $sourceImage->isBroken()) {
            return false;
        }
        $resource = $sourceImage->getResource();

        /* argument 4 */
        if (is_null($width)) {
            $width = imagesx($resource) - $sourceX;
        }

        /* argument 5 */
        if (is_null($height)) {
            $height = imagesy($resource) - $sourceY;
        }

        /* argument 6 */
        if (is_null($destX)) {
            $destX = $sourceX;
        }

        /* argument 7 */
        if (is_null($destY)) {
            $destY = $sourceY;
        }

        /* argument 8 */
        if (!is_null($opacity)) {
            $opacity = (int) round($opacity * 100);
        }

        if (is_int($opacity) && $opacity >= 0 && $opacity <= 100 && $this->isTruecolor()) {
            return imagecopymerge($this->_image, $resource, $destX, $destY, $sourceX, $sourceY, $width, $height, $opacity);

        } else {
            return imagecopy($this->_image, $resource, $destX, $destY, $sourceX, $sourceY, $width, $height);
        }
    }

    /**
     * Convert a colored image to grayscale.
     *
     * This converts the palette colors to gray values.
     *
     * @see     Image::toGrayscale()
     * @return  bool
     */
    public function toGrayscale(): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        return imagefilter($this->_image, IMG_FILTER_GRAYSCALE);
    }

    /**
     * Alias of Image::toGrayscale().
     */
    public function toGreyscale(): bool
    {
        return $this->toGrayscale();
    }

    /**
     * Create a monochromatic image.
     *
     * This produces a monochromatic version of the image,
     * shaded in the color provided.
     *
     * The arguments $r, $g, $b can be any integer of 0 through 255.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   int    $r        red value
     * @param   int    $g        green value
     * @param   int    $b        blue value
     * @return  bool
     */
    public function monochromatic(int $r, int $g, int $b): bool
    {
        assert($r >= 0 && $r <= 255, 'Invalid argument $r: must be in range [0,255].');
        assert($g >= 0 && $g <= 255, 'Invalid argument $g: must be in range [0,255].');
        assert($b >= 0 && $b <= 255, 'Invalid argument $b: must be in range [0,255].');

        if ($this->isBroken()) {
            return false;
        }

        if ($this->isTruecolor()) {
            $this->reduceColorDepth(256, false);
        }

        /*
         * Credit for the following algorithm for PHP 4
         * goes to santibari at fibertel dot com, who
         * proposed this one at php.net
         */

        /*
         * We will create a monochromatic palette based on
         * the input color
         * which will go from black to white
         * Input color luminosity: this is equivalent to the
         * position of the input color in the monochromatic
         * palette
         */
        $lum_inp = round(255 * ($r + $g + $b) / 765); /* 765=255*3 */

        /*
         * We fill the palette entry with the input color at its
         * corresponding position
         */
        $pal[$lum_inp]['r'] = $r;
        $pal[$lum_inp]['g'] = $g;
        $pal[$lum_inp]['b'] = $b;

        /*
         * Now we complete the palette, first we'll do it to
         * the black,and then to the white.
         */

        /*
         * FROM input to black:
         * how many colors between black and input
         */
        $stepsToBlack = $lum_inp;

        /* The step size for each component */
        if ($stepsToBlack) {
            $stepSizeRed   = $r / $stepsToBlack;
            $stepSizeGreen = $g / $stepsToBlack;
            $stepSizeBlue  = $b / $stepsToBlack;
        }

        for ($i = $stepsToBlack; $i >= 0; $i--)
        {
            $pal[$stepsToBlack-$i]['r'] = (int) ($r - round($stepSizeRed   * $i));
            $pal[$stepsToBlack-$i]['g'] = (int) ($g - round($stepSizeGreen * $i));
            $pal[$stepsToBlack-$i]['b'] = (int) ($b - round($stepSizeBlue  * $i));
        }

        /**
         * FROM input to white:
         * how many colors between input and white
         */
        $stepsToWhite = 255 - $lum_inp;

        if ($stepsToWhite) {
            $stepSizeRed   = (255 - $r) / $stepsToWhite;
            $stepSizeGreen = (255 - $g) / $stepsToWhite;
            $stepSizeBlue  = (255 - $b) / $stepsToWhite;
        } else {
            $stepSizeRed = $stepSizeGreen = $stepSizeBlue = 0;
        }

        /* The step size for each component */
        for ($i = ( $lum_inp + 1 ); $i <= 255; $i++)
        {
            $pal[$i]['r'] = (int) ($r + round($stepSizeRed   * ($i-$lum_inp)));
            $pal[$i]['g'] = (int) ($g + round($stepSizeGreen * ($i-$lum_inp)));
            $pal[$i]['b'] = (int) ($b + round($stepSizeBlue  * ($i-$lum_inp)));
        }
        /* End of palette creation */

        /*
         * Now,let's change the original palette into the one we
         * created
         */
        for ($c = 0; $c < $this->getPaletteSize(); $c++)
        {
            $col = imagecolorsforindex($this->_image, $c);
            $lum_src = round(255 * ( $col['red']+$col['green']+$col['blue'] ) / 765);
            $col_out = $pal[$lum_src];
            imagecolorset($this->_image, $c, $col_out['r'], $col_out['g'], $col_out['b']);
        }
        return true;
    }

    /**
     * Brighten / darken the image.
     *
     * Adds or removes white from the image.
     *
     * The argument $amount is a float between -1.0 and 1.0.
     * Which you might translate to -100% through 100%.
     *
     * This has the same effect as calling Image::colorize($r, $g, $b),
     * with $r, $b, $g being identical values.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   float    $amount    effect strength
     * @return  bool
     */
    public function brightness(float $amount): bool
    {
        /* argument 1 */
        if ($amount < 0.0 || $amount > 1.0) {
            $message = "Invalid argument 1. Must be between 0 and 1.";
            \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::WARNING);
            return false;
        }

        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        if ($amount == 0.0) {
            return true;
        } else {
            $amount = (int) round($amount * 255);
            return $this->colorize($amount, $amount, $amount);
        }
    }

    /**
     * Raise / lower contrast of the image.
     *
     * Adds or removes grey from the image.
     *
     * The argument $ammount is a float between -1.0 and 1.0.
     * Which you might translate to -100% through 100%.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   float  $amount  effect strength
     * @return  bool
     */
    public function contrast(float $amount): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        /* argument 1 */
        if ($amount < -1.0 || $amount > 1.0) {
            \Yana\Log\LogManager::getLogger()->addLog(
                "Invalid argument 1. Must be between -1 and 1.", \Yana\Log\TypeEnumeration::WARNING
            );
            return false;
        }

        $amount = - $amount;

        if ($amount == 0.0) {
            return true;
        }

        $amount = (int) ( round(255 * $amount) - 127 );
        return imagefilter($this->_image, IMG_FILTER_CONTRAST, $amount);
    }

    /**
     * Procude negative image.
     *
     * This function negates all colors.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @return  bool
     */
    public function negate(): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        return imagefilter($this->_image, IMG_FILTER_NEGATE);
    }

    /**
     * Apply a filter.
     *
     * This is an OO-style alias of PHP's imagefilter() function.
     * See the PHP manual for a full description.
     *
     * $filter can be (among others) one of the following:
     * <ul>
     * <li> IMG_FILTER_BRIGHTNESS     (see also: {@link Image::brightness()})                     </li>
     * <li> IMG_FILTER_CONTRAST       (see also: {@link Image::contrast()})                       </li>
     * <li> IMG_FILTER_COLORIZE       (see also: {@link Image::colorize()})                       </li>
     * <li> IMG_FILTER_EDGEDETECT                                                                 </li>
     * <li> IMG_FILTER_EMBOSS                                                                     </li>
     * <li> IMG_FILTER_GAUSSIAN_BLUR  (see also: {@link Image::blur()}, {@link Image::sharpen()}) </li>
     * <li> IMG_FILTER_GRAYSCALE      (see also: {@link Image::toGrayscale()})                    </li>
     * <li> IMG_FILTER_SELECTIVE_BLUR                                                             </li>
     * <li> IMG_FILTER_MEAN_REMOVAL                                                               </li>
     * <li> IMG_FILTER_NEGATE         (see also: {@link Image::negate()})                         </li>
     * <li> IMG_FILTER_SMOOTH         (see also: {@link Image::blur()}, {@link Image::sharpen()}) </li>
     * <li> IMG_FILTER_PIXELATE                                                                   </li>
     * <li> IMG_FILTER_SCATTER                                                                    </li>
     * </ul>
     *
     *
     * param   mixed  $arg1  depends on filter
     * param   mixed  $arg2  depends on filter
     * param   mixed  $arg3  depends on filter
     * param   mixed  $arg4  depends on filter
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   int    $filter   a constant (see list)
     * @return  bool
     *
     * @see     Image::blur()
     * @see     Image::brightness()
     * @see     Image::colorize()
     * @see     Image::contrast()
     * @see     Image::negate()
     * @see     Image::sharpen()
     * @see     Image::toGrayscale()
     */
    public function applyFilter(int $filter): bool
    {
        if ($this->isBroken() || !function_exists('imagefilter')) {
            return false;
        }
        switch (func_num_args())
        {
            case 1:
                return imagefilter($this->_image, $filter);
            case 2:
                $arg1 = func_get_arg(1);
                return imagefilter($this->_image, $filter, $arg1);
            case 3:
                $arg1 = func_get_arg(1);
                $arg2 = func_get_arg(2);
                return imagefilter($this->_image, $filter, $arg1, $arg2);
            case 4:
                $arg1 = func_get_arg(1);
                $arg2 = func_get_arg(2);
                $arg3 = func_get_arg(3);
                return imagefilter($this->_image, $filter, $arg1, $arg2, $arg3);
            case 5:
                $arg1 = func_get_arg(1);
                $arg2 = func_get_arg(2);
                $arg3 = func_get_arg(3);
                $arg4 = func_get_arg(4);
                return imagefilter($this->_image, $filter, $arg1, $arg2, $arg3, $arg4);
            default:
                return false;
        }
    }

    /**
     * Colorize the image.
     *
     * This adds the specified color to the image,
     * shaded in the color provided.
     *
     * The arguments $r, $g, $b can be any integer of -255 through 255.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   int  $r  red value
     * @param   int  $g  green value
     * @param   int  $b  blue value
     * @return  bool
     */
    public function colorize(int $r, int $g, int $b): bool
    {
        assert($r >= 0 && $r <= 255, 'Invalid argument $r: must be in range [0,255].');
        assert($g >= 0 && $g <= 255, 'Invalid argument $g: must be in range [0,255].');
        assert($b >= 0 && $b <= 255, 'Invalid argument $b: must be in range [0,255].');

        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        if ($r == $g && $g == $b) {
            return imagefilter($this->_image, IMG_FILTER_BRIGHTNESS, $r);
        } else {
            return imagefilter($this->_image, IMG_FILTER_COLORIZE, $r, $g, $b);
        }
    }

    /**
     * Multiply the palette values with a color.
     *
     * The color provided multiplies with each color in the palette.
     * For example, this is usefull to filter colors.
     *
     * Extract the green channel of an image:
     * <code>
     * $image->multiply(0, 255, 0);
     * </code>
     *
     * Removing some red from an image will remove a red stitch
     * and shift the colors towards a cold turquoise:
     * <code>
     * $image->multiply(200, 255, 255);
     * </code>
     *
     * Shifting cold blue colors towards warm orange colors:
     * <code>
     * $image->multiply(255, 200, 150);
     * </code>
     *
     * The arguments $r, $g, $b can be any integer of 0 through 255.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   int    $r        red value
     * @param   int    $g        green value
     * @param   int    $b        blue value
     * @return  bool
     */
    public function multiply(int $r, int $g, int $b): bool
    {
        assert($r >= 0 && $r <= 255, 'Invalid argument $r: must be in range [0,255].');
        assert($g >= 0 && $g <= 255, 'Invalid argument $g: must be in range [0,255].');
        assert($b >= 0 && $b <= 255, 'Invalid argument $b: must be in range [0,255].');

        if ($this->isBroken()) {
            return false;
        }

        if ($this->isTruecolor()) {
            $this->reduceColorDepth(256, false);
        }

        $r++;
        $g++;
        $b++;

        for ($i = 0; $i < $this->getPaletteSize(); $i++)
        {
            $color     = imagecolorsforindex($this->_image, $i);
            $color['red']   = (int) round(sqrt($color['red']   * $r));
            $color['green'] = (int) round(sqrt($color['green'] * $g));
            $color['blue']  = (int) round(sqrt($color['blue']  * $b));
            $color['red']   = (($color['red']   > 255) ? 255 : (($color['red']   < 0) ? 0 : $color['red']));
            $color['green'] = (($color['green'] > 255) ? 255 : (($color['green'] < 0) ? 0 : $color['green']));
            $color['blue']  = (($color['blue']  > 255) ? 255 : (($color['blue']  < 0) ? 0 : $color['blue']));
            imagecolorset($this->_image, $i, $color['red'], $color['green'], $color['blue']);
        }
        return true;
    }

    /**
     * Blur the image.
     *
     * This makes the image look smoother.
     * The argument $ammount is any float 0.0 through 1.0,
     * which translates to 0% through 100%.
     *
     * @param   float  $ammount  effect strength
     * @return  bool
     */
    public function blur(float $ammount): bool
    {
        /* argument 1 */
        if ($ammount < 0.0 || $ammount > 1.0) {
            \Yana\Log\LogManager::getLogger()->addLog(
                "Invalid argument 1. Must be between 0 and 1.", \Yana\Log\TypeEnumeration::WARNING
            );
            return false;
        } else {
            /* settype to FLOAT */
            $ammount = (float) $ammount;
        }

        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        $pct = (int) floor(15 - ($ammount * 15));
        imagefilter($this->_image, IMG_FILTER_SMOOTH, $pct);
        return true;
    }

    /**
     * Sharpen the image.
     *
     * This sharpens the image.
     * The argument $ammount is any float 0.0 through 1.0,
     * which translates to 0% through 100%.
     *
     * @param   float  $ammount  effect strength
     * @return  bool
     */
    public function sharpen(float $ammount): bool
    {
        /* argument 1 */
        if ($ammount < 0.0 || $ammount > 1.0) {
            \Yana\Log\LogManager::getLogger()->addLog(
                "Invalid argument 1. Must be between 0 and 1.", \Yana\Log\TypeEnumeration::WARNING
            );
            return false;
        } else {
            /* settype to FLOAT */
            $ammount = (float) $ammount;
        }

        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        $width  = (int) $this->getWidth();
        $height = (int) $this->getHeight();

        /**
         * This is an implementation for PHP 5
         */
        if ($ammount == 0) {
            return true;

        } else {

            $tempImage = imagecreatetruecolor($width, $height);
            imagecopy($tempImage, $this->_image, 0, 0, 0, 0, $width, $height);
            imagefilter($tempImage, IMG_FILTER_EDGEDETECT);
            $pct = (int) ($ammount * 20);
            imagecolortransparent($tempImage, imagecolorat($tempImage, 0, 0));
            imagecopymerge($this->_image, $tempImage, 0, 0, 0, 0, $width, $height, $pct);
            imagefilter($this->_image, IMG_FILTER_CONTRAST, - (int) ($pct / 2));
            return true;
        }
    }

    /**
     * Flip the image horizontally.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * {@internal
     *
     * This function has linear complexity: O( (n*c) /2 )
     * With n being the width in pixel and c being a constant.
     *
     * }}
     *
     * @return  bool
     */
    public function flipX(): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }

        $width  = (int) $this->getWidth();
        $height = (int) $this->getHeight();

        /* iterate through stripes
         * and copy left to right
         */
        $tempImage = imagecreatetruecolor(1, $height);

        assert(!isset($left), '!isset($left)');
        for ($left = 0; $left < floor($width / 2); $left++)
        {
            /*
             * this is as simple as exchanging two vars:
             * $temp  = $left;
             * $left  = $right;
             * $right = $temp;
             */

            /*
             *  just the same as flipY()
             *  destination = source    (   x       y   x       y  width  height )
             */
            $right = $width -$left -1;
            imagecopy($tempImage,   $this->_image, 0,      0,  $left,  0, 1, $height);
            imagecopy($this->_image, $this->_image, $left,  0,  $right, 0, 1, $height);
            imagecopy($this->_image, $tempImage,   $right, 0,       0, 0, 1, $height);
        }
        unset($left, $right);
        imagedestroy($tempImage);

        return true;
    }

    /**
     * Flip the image vertically.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * {@internal
     *
     * This function has linear complexity: O( (n*c) /2 )
     * With n being the height in pixel and c being a constant.
     *
     * }}
     *
     * @return  bool
     */
    public function flipY(): bool
    {
        /**
         * error - broken image
         */
        if ($this->isBroken()) {
            return false;
        }
        $width  = (int) $this->getWidth();
        $height = (int) $this->getHeight();

        $tempImage = imagecreatetruecolor($width, 1);

        assert(!isset($top), '!isset($top)');
        for ($top = 0; $top < floor($height / 2); $top++)
        {
            /*
             * this is as simple as exchanging two vars:
             * $temp   = $top;
             * $top    = $bottom;
             * $bottom = $temp;
             */

            $bottom = $height -$top -1;

            /* 1)
             * backup top line to temp image
             * $temp = $top;
             *
             *        destination = source    (   x   y        x  y        width  height )
             */
            imagecopy($tempImage,   $this->_image, 0,  0,       0, $top,    $width, 1);
            /* 2)
             * copy bottom line to top line
             * $top = $bottom;
             *
             *        destination = source    (   x   y        x  y        width  height )
             */
            imagecopy($this->_image, $this->_image, 0, $top,     0, $bottom, $width, 1);
            /* 3)
             * copy top line to bottom line from temp image
             * $bottom = $temp;
             *
             *        destination = source    (   x   y       x   y        width  height )
             */
            imagecopy($this->_image, $tempImage,   0, $bottom,  0, 0,       $width, 1);
        }
        unset($top, $bottom);
        imagedestroy($tempImage);

        return true;
    }

    /**
     * Copy palette.
     *
     * This copies the palette from the source image
     * to this image.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   \Yana\Media\Image  $sourceImage  the image to copy the palette from
     * @return  bool
     */
    public function copyPalette(\Yana\Media\Image $sourceImage): bool
    {
        $success = false;
        if (is_resource($sourceImage->_image)) {
            imagepalettecopy($this->_image, $sourceImage->_image);
            $success = true;
        }
        return $success;
    }

    /**
     * Output image to browser.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * The optional argument $imageType can be used to set a prefered image type.
     *
     * If no prefered image type is set, or the prefered image type is not available,
     * then this function will automatically try to create a PNG image.
     * If PNG is not available it will automatically fall back.
     * PNG will fall back to JPEG, JPEG to GIF, GIF to BMP.
     * If nothing of the above worked it gives up and returns bool(false).
     *
     * @param   string  $imageType  can be on of "png", "jpg", "gif", "bmp"
     * @return  bool
     * @codeCoverageIgnore
     * @throws  \Yana\Core\Exceptions\Files\InvalidTypeException  when the image type given is not recognized
     */
    public function outputToScreen(?string $imageType = null): bool
    {
        /**
         * If headers are already sent, first try to erase the output buffer.
         * Only if this does not work, throw an error.
         */
        if (headers_sent()) {
            if (ob_get_length()) {
                /* clean buffer (try to repair) */
                $content = ob_get_clean();
                /* still there? */
                if (headers_sent()) {
                    print $content;
                    \Yana\Log\LogManager::getLogger()->addLog(
                        "Unable to output image. Headers already sent.", \Yana\Log\TypeEnumeration::WARNING
                    );
                    return false;
                }
            } else {
                \Yana\Log\LogManager::getLogger()->addLog(
                    "Unable to output image. Headers already sent.", \Yana\Log\TypeEnumeration::WARNING
                );
                return false;
            }
        }
        if (is_string($imageType)) {
            $imageType = mb_strtolower($imageType);
        }

        /* prefered image type */
        if (!empty($imageType)) {
            if (!isset($this->_mapping[$imageType])) {
                throw new \Yana\Core\Exceptions\Files\InvalidTypeException("Image type '{$imageType}' is unsupported.", \Yana\Log\TypeEnumeration::WARNING);
            }
            array_unshift($this->_mapping, $this->_mapping[$imageType]);
        }

        /* fall back */
        foreach ($this->_mapping as $map)
        {
            $mimeType     = $map[0];
            $functionName = $map[1];
            if (function_exists($functionName)) {
                header("Content-type: {$mimeType}");
                $functionName($this->_image);
                return true;
            }
        }

        /* none of the above worked */
        throw new \Yana\Core\Exceptions\Files\InvalidTypeException("Image type '{$imageType}' is unsupported.", \Yana\Log\TypeEnumeration::WARNING);
    }

    /**
     * Output image to a file.
     *
     * Returns the name of the output file on success and bool(false) on error.
     *
     * You may leave off the file extension. If so the function will determine
     * the correct extension by itself and append it automatically to the argument
     * $filename.
     *
     * If the file already exists, it will get replaced.
     *
     * The optional argument $imageType can be used to set a prefered image type.
     *
     * If no prefered image type is set, or the prefered image type is not available,
     * then this function will automatically try to create a PNG image.
     * If PNG is not available it will automatically try fall back to another type.
     * PNG will fall back to JPEG, JPEG to GIF, GIF to BMP.
     * Only if nothing of the above worked, it will give up and NULL.
     * Otherwise the filename is returned.
     *
     * @param   string  $filename   name of the output file
     * @param   string  $imageType  can be one of "png", "jpg", "gif", "bmp"
     * @return  string|NULL
     * @throws  \Yana\Core\Exceptions\Files\InvalidTypeException  when the image type given is not recognized
     */
    public function outputToFile(string $filename, string $imageType): string
    {
        if (is_string($imageType)) {
            $imageType = mb_strtolower($imageType);
        }

        /* prefered image type */
        if (!isset($this->_mapping[$imageType])) {
            throw new \Yana\Core\Exceptions\Files\InvalidTypeException("Image type '{$imageType}' is unsupported.", \Yana\Log\TypeEnumeration::WARNING);
        }

        $functionName = $this->_mapping[$imageType][1];
        assert(\function_exists($functionName));

        if (!\Yana\Util\Strings::endsWith($filename, $imageType)) {
            $filename .= '.' . $imageType;
        }
        $functionName($this->_image, $filename);
        return $filename;
    }

    /**
     * Create thumbnail from image file.
     *
     * The image is resized to the dimensions given by the arguments $width x $height.
     * If you set one of these to NULL, the other one will be determined automatically.
     * If you leave both to NULL, the image will not be resized at all.
     *
     * The default thumbnail-size is 100x100 pixel.
     *
     * The argument $keepAspectRatio triggers how the image is resized.
     * If it is false, the image will be stretched or compressed to the dimensions
     * of the output image. If it is true, will be resized proportionally and the canvas
     * will expanded to the size of the output instead, if necessary.
     *
     * When using the setting true (which is the default), you may provide a
     * background color for the canvas using the argument $backgroundColor.
     * To do so, provide an array with the red, green and blue values.
     * Example: array(255, 255, 255) is "white".
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * Example:
     * <code>
     * // resize to 150px x 200px
     * $width = 150;
     * $height = 200;
     * // leave aspect-ratio untouched
     * $ratio = true;
     * // set background color to gray
     * $color = array(80, 80, 80);
     *
     * // call method
     * $image->createThumbnail($width, $height, $ratio, $color);
     * </code>
     *
     * @param   int     $width            horizontal dimension of the output image in pixel
     * @param   int     $height           vertical dimension of the output image in pixel
     * @param   bool    $keepAspectRatio  vertical dimension of the output image in pixel
     * @param   array   $backgroundColor  array of red, green, blue values (0 through 255)
     *                                    to identify the background color, defaults to white
     * @return  bool
     * @since   3.1.0
     */
    public function createThumbnail(?int $width = 100, ?int $height = 100, bool $keepAspectRatio = true, array $backgroundColor = null): bool
    {
        /**
         * Check if the "broken" flag has been set.
         * If it is set, the file is invalid.
         */
        if ($this->isBroken()) {
            return false;
        }

        /**
         * stretch the image to size
         *
         * Note: will NOT keep the proportion of width to height.
         */
        if (!$keepAspectRatio) {
            return (bool) $this->resizeImage($width, $height);
        }

        /**
         * Note: this resizes the image,
         * while keeping the proportion of width to height.
         */
        if ($width > $this->getWidth() && $height > $this->getHeight()) {
            /**
             * image is smaller than maximum dimensions
             *
             * To increase the size of a smaller image does'nt look good
             * and should be avoided (at least for automated image handling).
             */
        } elseif ($this->getWidth() > $this->getHeight()) {
            /* resize image by width */
            $this->resizeImage($width);
        } else {
            /* resize image by height */
            $this->resizeImage(null, $height);
        }

        /**
         * resize the canvas
         *
         * Note: this enlarges the canvas to the full size,
         * if the image is smaller, so all images will use
         * the same size.
         */
        return (bool) $this->resizeCanvas($width, $height, null, null, $backgroundColor);
    }

    /**
     * Compare this image to another.
     *
     * This function compare the image to another image on a pixel by pixel
     * basis. It returns the difference between booth images in percent.
     *
     * The result is returned as a float value, where 0.0 translates to 0%
     * (images are exactly the same) and 1.0 translates to 100% (images are
     * totally different).
     *
     * On error the function returns bool(false).
     *
     * I would suggest to consider two images equal, if the difference is not
     * greater than 1% (or in other words, if the images are 99% the same).
     * Images with less than 10% difference should be similar. E.g. one has
     * sharper edges, or one is lighter than the other.
     * Any value higher than 10% should indicate that both images are
     * different.
     *
     * WARNING: since this function compares every pixel it is very slow for
     * large images. It's use is suggested for testing and debugging issues
     * only.
     *
     * @param   \Yana\Media\Image|string  $comparedImage  filename or image object
     * @return  float|NULL
     * @since   2.9.9
     */
    public function compareImage($comparedImage)
    {
        /* argument 1 */
        if (is_string($comparedImage) && is_file($comparedImage)) {
            /* is file name */
            $otherImage = new self($comparedImage);
        } elseif (is_object($comparedImage) && $comparedImage instanceof $this) {
            /* is object */
            $otherImage = clone $comparedImage;
        } else {
            /* invalid value */
            \Yana\Log\LogManager::getLogger()->addLog(
                "Argument 1 is invalid. The source is not a valid image.", \Yana\Log\TypeEnumeration::WARNING
            );
            return NULL;
        }

        if (!is_resource($this->_image)) { // This should not be reachable, but just in case we are wrong
            // @codeCoverageIgnoreStart
            return NULL;
            // @codeCoverageIgnoreEnd
        } else {
            /* all fine - proceed */
            $thisImage = clone $this;
        }

        /* result */
        $difference = 0.0;

        /* cache */
        $colors = array();

        /* resize */
        $h = (int) $this->getHeight();
        $w = (int) $this->getWidth();
        if ($w <= 0 || $h <= 0) { // It should be impossible to create an image with height or width <= 0, but just in case.
            // @codeCoverageIgnoreStart
            return NULL;
            // @codeCoverageIgnoreEnd
        } elseif ($h !== $otherImage->getHeight() || $w !== $otherImage->getWidth()) {
            $otherImage->resize($w, $h);
        }

        /* merge images */
        $thisImage->negate();
        $otherImage->copyRegion($thisImage, 0, 0, null, null, null, null, 0.5);

        /* loop through pixel */
        for ($x = 0; $x < $w; $x++)
        {
            $diff = 0.0;
            for ($y = 0; $y < $h; $y++)
            {
                $color = imagecolorat($otherImage->_image, $x, $y);
                if (!isset($colors[$color])) {
                    $colors[$color] = imagecolorsforindex($otherImage->_image, $color);
                }
                $color = $colors[$color];
                $a = (abs($color['red'] - 127) / 128);
                $b = (abs($color['green'] - 127) / 128);
                $c = (abs($color['blue'] - 127) / 128);
                $diff += ($a + $b + $c) / 3;
            }
            assert($h != 0, 'Division by zero');
            $difference += $diff / $h;
        }
        $otherImage->__destruct();

        assert($w != 0, 'Division by zero');
        return ($difference / $w);
    }

    /**
     * Get a string representation of this object.
     *
     * This function is intended to be called when the object
     * is used in a string context.
     *
     * @return  string
     */
    public function __toString()
    {
        $filename = $this->getPath();
        if (is_string($filename) && $filename > "") {
            return $filename;

        /*
         * error - broken image
         */
        } elseif ($this->isBroken()) {
            return "broken image";

        } else {
            return ( ($this->isTruecolor()) ? 'truecolor' : 'palette' ) . "-image(" . (int) $this->getWidth() . "px," .
                (int) $this->getHeight() . "px)";
        }
    }

    /**
     * Destructor.
     *
     * Automatically free memory for the image if object gets deleted.
     *
     * @ignore
     */
    public function __destruct()
    {
        if (function_exists('imagedestroy')) {
            if (is_resource($this->_image)) {
                imagedestroy($this->_image);
            }
            if (is_resource($this->_brush)) {
                imagedestroy($this->_brush);
            }
        }
    }

}

?>