<?php
/**
 * PHPUnit test-case.
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

namespace Yana\Media;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../include.php';

/**
 * @package  test
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Media\Image
     */
    private $_image;

    /**
     * @var  \Yana\Media\Image
     */
    private $_emptyImage;

    /**
     * @var  \Yana\Media\Image
     */
    private $_dummyImage;

    /**
     * @var  \Yana\Media\Image
     */
    private $_brokenImage;

    /**
     * @var  \Yana\Media\Image
     */
    private $_invalidImage;

    /**
     * @var  string
     */
    private $_imageSource = 'resources/image/logo.png';

    /**
     * @var  string
     */
    private $_dummySource = 'resources/image/test3.png';

    /**
     * @var  string
     */
    private $_invalidSource = 'resources/file.txt';

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        // intentionally left blank
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
//        $this->markTestSkipped('Binaries are broken. Test needs refactoring');
//        return;
        $this->_image = new Image(CWD . $this->_imageSource);
        $this->_emptyImage = new Image();
        $this->_dummyImage = new Image(CWD . $this->_dummySource);
        $this->_brokenImage = new Image('nonexist.png');
        $this->_invalidImage = new Image(CWD . $this->_invalidSource);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        if (isset($this->_image)) {
            $this->_image->__destruct();
        }
        if (isset($this->_emptyImage)) {
            $this->_emptyImage->__destruct();
        }
        if (isset($this->_dummyImage)) {
            $this->_dummyImage->__destruct();
        }
        if (isset($this->_brokenImage)) {
            $this->_brokenImage->__destruct();
        }
        if (isset($this->_invalidImage)) {
            $this->_invalidImage->__destruct();
        }
        return;
    }

    /**
     * @test
     */
    public function testSetBrushDirectory()
    {
        $default = Brush::getDirectory();
        Brush::setDirectory(__DIR__);
        $this->assertEquals(__DIR__, Brush::getDirectory());
        Brush::setDirectory($default);
        $this->assertEquals($default, Brush::getDirectory());
    }

    /**
     * get path
     *
     * Expected test result : string
     *
     * @test
     */
    public function testGetPath()
    {
        $path = $this->_image->getPath();
        $this->assertInternalType('string', $path, 'assert failed, return value of getPath() has wrong type');
        $this->assertTrue(is_file($path), 'assert failed,path doesnt exist');

        $noExist = $this->_emptyImage->getPath();
        $this->assertFalse($noExist, 'assert failed , path doesnt exist');
    }

    /**
     * exists
     *
     * Expected test result : true
     *
     * @test
     */
    public function testExists()
    {
        $exists = $this->_image->exists();
        $this->assertTrue($exists, 'assert failed, image doesnt exists');

        $noExist = $this->_brokenImage->exists();
        $this->assertFalse($noExist, 'assert failed , image exist');
    }

    /**
     * is broken
     *
     * Expected test result : false
     *
     * @test
     */
    public function testIsBroken()
    {
        $isbroken = $this->_brokenImage->isBroken();
        $this->assertTrue($isbroken, 'assert failed, the image is not broken');

        $broken = $this->_image->isBroken();
        // expecting false
        $this->assertFalse($broken, 'assert failed - the image is broken');
    }

    /**
     * is true color
     *
     * Expected test result : true
     *
     * @test
     */
    public function testIsTruecolor()
    {
        $truecolor = $this->_brokenImage->IsTruecolor();
        $this->assertFalse($truecolor, 'assert failed, the image is broken');

        $truecolor = $this->_image->isTruecolor();
        $this->assertTrue($truecolor, 'assert "isTruecolor()" failed - image is not true color');
    }

    /**
     * clone object
     *
     * Expected test result : object
     *
     * @test
     */
    public function testCloneObject()
    {
        $cloneObject = clone $this->_image;
        $this->assertInternalType('object', $cloneObject, 'assert failed , value is not from type object');

        $clone = clone $this->_brokenImage;
        $this->assertEquals($this->_brokenImage, $clone,  'assert failed , the two variables are not equal');
        $this->assertInternalType('object', $clone, 'assert failed, value is not from type object');
        unset($clone);
    }

    /**
     * equals
     *
     * Expected test result : false
     *
     * @test
     */
    public function testEquals()
    {
        $cloneObject = clone $this->_image;
        $equals = $this->_image->equals($cloneObject);
        $this->assertFalse($equals, 'assert "equals()" failed,  the objects are equal');
    }

    /**
     * equals resource
     *
     * Expected test result : true | true
     *
     * @test
     */
    public function testEqualsResoure()
    {
        $resource = $this->_image->getResource();
        $this->assertInternalType('resource', $resource, 'assert failed, value is not from type resource');
        $equalsResource = $this->_image->equalsResoure($resource);
        $this->assertTrue($equalsResource, 'assert "equalsResoure()" failed, invalid resource');

        $resource = $this->_emptyImage->getResource();
        $equalsResource = $this->_image->equalsResoure($resource);
        $this->assertFalse($equalsResource, 'assert "equalsResoure()" failed , valid resource');
    }

    /**
     * get resource
     *
     * Expected test result : resource
     *
     * @test
     */
    public function testGetResource()
    {
        $resource = $this->_image->getResource();
        $this->assertInternalType('resource', $resource, 'assert failed, value is not from type resource');

        $resource = $this->_brokenImage->getResource();
        $this->assertFalse($resource, 'assert failed, invalid resource');
    }

    /**
     * clear canvas
     *
     * @test
     */
    public function testClearCanvas()
    {
        // intentionally left blank
        // destroy old image and make a new one
    }

    /**
     * get width
     *
     * Expected test result : integer
     *
     * @test
     */
    public function testGetWidth()
    {
        $width = $this->_image->getWidth();
        $this->assertInternalType('integer', $width, 'assert "getWidth()" failed, value is not from type integer');

        $width = $this->_brokenImage->getWidth();
        $this->assertFalse($width, 'assert failed , image is broken');
    }

    /**
     * get height
     *
     * Expected test result : integer
     *
     * @test
     */
    public function testGetHeight()
    {
        $height = $this->_image->getHeight();
        $this->assertInternalType('integer', $height, 'assert "getHeight()" failed, value is not from type integer');

        $height = $this->_brokenImage->getHeight();
        $this->assertFalse($height, 'assert failed , image is broken');
    }

    /**
     * draw point
     *
     * Expected test result : true
     *
     * @test
     * @dataProvider  providerDrawPoint
     */
    public function testDrawPoint()
    {
        $x = $y = 10;
        $color = $this->_image->black;
        $drawPoint = $this->_image->drawPoint($x, $y, $color);
        $this->assertTrue($drawPoint, 'assert "drawPoint()" failed, point is not set');

        $drawPoint = $this->_image->drawPoint($x, $y);
        $this->assertTrue($drawPoint, 'assert "drawPoint()" failed, point is not set');

        $drawPoint = $this->_brokenImage->drawPoint($x, $y);
        $this->assertFalse($drawPoint, 'assert "drawPoint()" failed , image is broken');
    }

    /**
     * draw line
     *
     * Expected test result : true
     *
     * @test
     */
    public function testDrawLine()
    {
        $drawLine = $this->_image->drawLine(15, 15, 15, 80, $this->_image->black);
        $this->assertTrue($drawLine, 'assert "drawLine()" failed, line is not set');

        $drawLine = $this->_image->drawLine(15, 15, 15, 80);
        $this->assertTrue($drawLine, 'assert "drawLine()" failed, line is not set');

        $drawLine = $this->_brokenImage->drawLine(15, 15, 15, 80, $this->_image->black);
        $this->assertFalse($drawLine, 'assert "drawLine()" failed , image is broken');
    }

    /**
     * draw string
     *
     * Expected test result : true
     *
     * @test
     */
    public function testDrawString()
    {
        $drawString = $this->_brokenImage->drawString('Yana description', 50, 20);
        $this->assertFalse($drawString, 'assert drawString() failed ,  image is broken');

        $drawString = $this->_image->drawString('Yana description', 50, 20, $this->_image->getColor(0, 0, 255), 3, true);
        $this->assertTrue($drawString, 'assert "drawString()" failed, string is not set');

        $drawString = $this->_image->drawString('Yana description', null, null, $this->_image->getColor(0, 0, 255), 3, false);
        $this->assertTrue($drawString, 'assert "drawString()" failed, string is not set');
    }

    /**
     * draw formattet string
     *
     * Expected test result : true
     *
     * @test
     */
    public function testDrawFormattedString()
    {
        $drawFormattedString = $this->_brokenImage->drawFormattedString('another description on the other site');
        $this->assertFalse($drawFormattedString, 'assert "drawFormattedString()" failed, image is broken');

        $drawFormattedString = $this->_image->drawFormattedString('another description on the other site', 150, 80, $this->_image->getColor(0, 0, 255), 'tahoma', 12, 15);
        $this->assertTrue($drawFormattedString, 'assert "drawFormattedString()" failed, formattedString is not set');

        $drawFormattedString = $this->_image->drawFormattedString('another description on the other site');
        $this->assertTrue($drawFormattedString, 'assert "drawFormattedString()" failed, formattedString is not set');
    }

    /**
     * Draw Formatted String Invalid Argument
     *
     * @expectedException \PHPUnit_Framework_Error
     * @test
     */
    public function testDrawFormattedStringInvalidArgument()
    {
        $this->_image->drawFormattedString('test', 150, 80, $this->_image->getColor(0, 0, 255), 'Not a font', 12, 15);
    }

    /**
     * draw ellipse
     *
     * Expected test result : true
     *
     * @test
     */
    public function testDrawEllipse()
    {
        $drawEllipse = $this->_brokenImage->drawEllipse(280, 200, 50);
        $this->assertFalse($drawEllipse, 'assert "drawEllipse()" failed , image is broken');

        $drawEllipse = $this->_image->drawEllipse(280, 200, 50, null, $this->_image->getColor(255, 0, 0), $this->_image->getColor(255, 0, 0, 0));
        $this->assertTrue($drawEllipse, 'assert "drawEllipse()" failed, ellipse is not set');

        $drawEllipse = $this->_image->drawEllipse(280, 200, 50);
        $this->assertTrue($drawEllipse, 'assert "drawEllipse()" failed, ellipse is not set');
    }

    /**
     * draw rectangle
     *
     * Expected test result : true
     *
     * @test
     */
    public function testDrawRectangle()
    {
        $drawRectangle = $this->_brokenImage->drawRectangle(280, 200, 50);
        $this->assertFalse($drawRectangle, 'assert "drawRectangle()" failed,image is broken');

        $drawRectangle = $this->_image->drawRectangle(10, 10, 30, null, $this->_image->getColor(255, 0, 160));
        $this->assertTrue($drawRectangle, 'assert "drawRectangle()" failed, rectangle is not set');

        $drawRectangle = $this->_image->drawRectangle(10, 10, 30);
        $this->assertTrue($drawRectangle, 'assert "drawRectangle()" failed, rectangle is not set');
    }

    /**
     * draw polygon
     *
     * Expected test result : true
     *
     * @test
     */
    public function testDrawPolygon()
    {
        $points = array(
                    0 => array( 20, 0  ),
                    1 => array( 40, 20 ),
                    2 => array( 0, 20  )
                );
        $drawPolygon = $this->_brokenImage->drawPolygon($points, 53, 80, $this->_image->black, $this->_image->black);
        $this->assertFalse($drawPolygon, 'assert "drawPolygon()" failed , image is broken');

        $drawPolygon = $this->_image->drawPolygon($points, 53, 80, $this->_image->getColor(0, 255, 0), $this->_image->getColor(50, 220, 50));
        $this->assertTrue($drawPolygon, 'assert "drawPolygon()" failed, polygon is not set');

        // try the same without set color
        $drawPolygon = $this->_image->drawPolygon($points, 53, 80);
        $this->assertTrue($drawPolygon, 'assert "drawPolygon()" failed, polygon is not set');

        $points = array();
        $drawPolygon = $this->_image->drawPolygon($points, 530000, 80, $this->_image->black, $this->_image->blue);
        $this->assertFalse($drawPolygon, 'Cannot draw polygon outside of canvas');
    }

    /**
     * fill
     *
     * Expected test result : true
     *
     * @test
     */
    public function testFill()
    {
        $fill = $this->_brokenImage->fill($this->_image->blue, 10, 10, $this->_image->black);
        $this->assertFalse($fill, 'assert "fill()" failed, image is broken');

        $fill = $this->_image->fill($this->_image->getColor(0, 255, 0), 10, 10, $this->_image->getColor(255, 0, 0));
        $this->assertTrue($fill, 'assert "fill()" failed, fill with a color is not set');
    }

    /**
     * enable alpha
     *
     * Expected test result : true
     *
     * @test
     */
    public function testEnableAlpha()
    {
        $enableAlpha = $this->_brokenImage->enableAlpha(true, null);
        $this->assertFalse($enableAlpha, 'assert "enableAlpha()" failed, image is broken');

        // enabled
        $enableAlpha = $this->_image->enableAlpha(true, null);
        $this->assertTrue($enableAlpha, 'assert "enableAlpha()" failed, enableAlpha is not set');

        // disabled
        $disableAlpha = $this->_image->enableAlpha(false, null);
        $this->assertTrue($disableAlpha, 'assert "enableAlpha()" failed, enableAlpha is set');
    }

    /**
     * enable antialias
     *
     * Expected test result : true | true
     *
     * @test
     */
    public function testEnableAntialias()
    {
        $enableAntialias = $this->_brokenImage->enableAntialias();
        $this->assertFalse($enableAntialias, 'assert "enableAntialias()" failed, image is broken');

        $enableAntialias = $this->_image->enableAntialias();
        $this->assertTrue($enableAntialias, 'assert "enableAntialias()" failed, enableAntialias is not set');

        $disableAntialias = $this->_image->enableAntialias(false);
        $this->assertTrue($disableAntialias, 'assert "enableAntialias()" failed, enableAntialias with param "false" is not set');
    }

    /**
     * get font width
     *
     * Expected test result : integer
     *
     * @test
     */
    public function testGetFontWidth()
    {
        $fontWidth = $this->_image->getFontWidth(3);
        $this->assertInternalType('integer', $fontWidth, 'assert failed - "$fontWidth" is not from type integer');
    }

    /**
     * get font height
     *
     * Expected test result : integer
     *
     * @test
     */
    public function testGetFontHeight()
    {
        $fontHeight = $this->_image->getFontHeight(3);
        $this->assertInternalType('integer', $fontHeight, 'assert failed, value is not from type integer');
    }

    /**
     * get color values
     *
     * Expected test result : integer | array
     *
     * @test
     */
    public function testGetColorValues()
    {
        // broken
        $isBroken = $this->_invalidImage->getColorValues($this->_image->black);
        $this->assertFalse($isBroken, 'assert "getColorValues()" failed, image is broken');

        $color = $this->_image->getColor(153, 50, 204);
        $this->assertInternalType('integer', $color, 'assert "getColor()" failed, value is not from type integer');
        $colorValues = $this->_image->getColorValues($color);
        $this->assertInternalType('array', $colorValues, 'assert "getColorValues()" failed, value is not from type array');
    }

    /**
     * Get Color Values Invalid Argument
     *
     * @expectedException \PHPUnit_Framework_Error
     * @test
     */
    public function testGetColorValuesInvalidArgument()
    {
        $this->_image->getColorValues(-1);
    }

    /**
     * get color at
     *
     * Expected test result : integer
     *
     * @test
     */
    public function testGetColorAt()
    {
        // broken
        $isBroken = $this->_invalidImage->getColorAt(80, 80);
        $this->assertFalse($isBroken, 'assert "getColorAt()" failed, image is broken');

        $colorAt = $this->_image->getColorAt(80, 80);
        $this->assertInternalType('integer', $colorAt, 'assert "getColorAt()" failed, value is not from type integer');

        $colorAt = $this->_image->getColorAt(-80, 80);
        $this->assertFalse($colorAt, 'assert failed - the first argument must be > than 0 ');

        $colorAt = $this->_image->getColorAt(80, -80);
        $this->assertFalse($colorAt, 'assert failed - the second argument must be > than 0 ');

        $colorAt = $this->_image->getColorAt(-80, -80);
        $this->assertFalse($colorAt, 'assert failed - both arguments must be > than 0 ');

        $colorAt = $this->_image->getColorAt(950, 650);
        $this->assertFalse($colorAt, 'assert failed - one or both values are bigger than the image');
    }
    /**
     * get size
     *
     * Expected test result : array
     *
     * @test
     */
    public function testGetSize()
    {
        $size = $this->_image->getSize(CWD . $this->_imageSource);
        $this->assertInternalType('array', $size , 'assert "getSize()" failed, value is not from type array');
    }

    /**
     * Get Size Invalid Argument
     *
     * @expectedException \PHPUnit_Framework_Error
     * @test
     */
    public function testGetSizeInvalidArgument()
    {
        $size = $this->_image->getSize($this->_image->black);
        $this->assertFalse($size, 'assert failed, first argument must be a string');
    }

    /**
     * get color
     *
     * Expected test result : integer
     *
     * @test
     */
    public function testGetColor()
    {
        $color = $this->_image->getColor(153, 50, 204);
        $this->assertInternalType('integer', $color, 'assert "getColor()" failed, value is not from type integer');
    }

    /**
     * get line width
     *
     * Expected test result : true | integer
     *
     * @test
     */
    public function testGetLineWidth()
    {
        // broken
        $isBroken = $this->_invalidImage->getLineWidth();
        $this->assertFalse($isBroken, 'assert "getLineWidth()" failed, image is broken');

        $setLine = $this->_image->setLineWidth(100);
        $this->assertTrue($setLine, 'assert "setLineWidth()" failed');

        $lineWidth = $this->_image->getLineWidth();
        $this->assertInternalType('integer', $lineWidth, 'assert "getLineWidth()" failed, value is not from type integer');
    }

    /**
     * set line width
     *
     * Expected test result : true
     *
     * @test
     */
    public function testSetLineWidth()
    {
        // broken
        $isBroken = $this->_invalidImage->setLineWidth(80);
        $this->assertFalse($isBroken, 'assert "setLineWidth()" failed, image is broken');

        $setLineWidth = $this->_image->setLineWidth(80);
        $this->assertTrue($setLineWidth, 'assert "setLineWidth()" failed, LineWidth is not set');

        // param value = 0
        $setLineWidth = $this->_image->setLineWidth(0);
        $this->assertFalse($setLineWidth, 'assert "SetLineWidth()" failed, the first argument must be in excess of 0');
    }

    /**
     * set line style
     *
     * Expected test result :true | true
     *
     * @test
     */
    public function testSetLineStyle()
    {
        // broken
        $isBroken = $this->_invalidImage->setLineStyle(255, 180);
        $this->assertFalse($isBroken, 'assert "setLineStyle()" failed, image is broken');

        $withParam = $this->_image->setLineStyle(255, 180);
        $this->assertTrue($withParam, 'assert "setLineStyle()" failed, lineStyle is not set');

        $setLineStyle = $this->_image->setLineStyle();
        $this->assertTrue($setLineStyle, 'assert "setLineStyle()" failed, lineStyle is not set');
    }

    /**
     * replace index color
     *
     * @test
     */
    public function testReplaceIndexColor()
    {
        // broken
        $isBroken = $this->_invalidImage->replaceIndexColor(0, $this->_image->black);
        $this->assertFalse($isBroken, 'assert "replaceIndexColor()" failed, image is broken');

        // expecting false
        $replaceIndexColor = $this->_image->replaceIndexColor(2, array('red'=>0,'green'=>255,'blue'=>100));
        $this->assertFalse($replaceIndexColor, 'Should not be able to replace palette color on true-color images.');

        $this->_image->reduceColorDepth(255);

        $replaceIndexColor = $this->_image->replaceIndexColor(2, array('red'=>0,'green'=>255,'blue'=>100));
        $this->assertTrue($replaceIndexColor, 'assert "replaceIndexColor()" failed');

        // expecting false
        $replaceIndexColor = $this->_image->replaceIndexColor(2, array('red'=>321,'green'=>255,'blue'=>100));
        $this->assertFalse($replaceIndexColor, 'assert "replaceIndexColor()" failed, the integer value of red need to be beetwen 0 and 255');

        // expecting false
        $replaceIndexColor = $this->_image->replaceIndexColor(2, array('red'=>0,'green'=>280,'blue'=>100));
        $this->assertFalse($replaceIndexColor, 'assert "replaceIndexColor()" failed, the integer value of green need to be beetwen 0 and 255');

        // expecting false
        $replaceIndexColor = $this->_image->replaceIndexColor(2, array('red'=>0,'green'=>255,'blue'=>600));
        $this->assertFalse($replaceIndexColor, 'assert "replaceIndexColor()" failed, the integer value of blue need to be beetwen 0 and 255');

        // expecting false
        $replaceIndexColor = $this->_image->replaceIndexColor(2, array('red'=>-20,'green'=>320,'blue'=>600));
        $this->assertFalse($replaceIndexColor, 'assert "replaceIndexColor()" failed, the integer values of "RGB" need to be beetwen 0 and 255');

    }

    /**
     * Replace Index Color Invalid Argument
     *
     * @expectedException \Yana\Core\Exceptions\OutOfBoundsException
     * @test
     */
    public function testReplaceIndexColorInvalidArgument()
    {
        // expecting false
        $this->_image->reduceColorDepth(255);

        $replaceIndexColor = $this->_image->replaceIndexColor(-1, 0);
        $this->assertFalse($replaceIndexColor, 'assert failed, first argument must be a valid color');
    }

    /**
     * Replace Index Color Invalid Argument
     *
     * @expectedException \PHPUnit_Framework_Error
     * @test
     */
    public function testReplaceIndexColorInvalidArgument2()
    {
        // expecting false
        $this->_image->reduceColorDepth(255);

        $replaceIndexColor = $this->_image->replaceIndexColor(0, -1);
        $this->assertFalse($replaceIndexColor, 'assert failed, second argument must be a valid color');
    }

    /**
     * replace color
     *
     * @test
     */
    public function testReplaceColor()
    {
        // broken
        $isBroken = $this->_invalidImage->replaceColor(1, $this->_image->yellow);
        $this->assertFalse($isBroken, 'assert "replaceColor()" failed, image is broken');

        $replaceColor = $this->_image->replaceColor(1, $this->_image->yellow);
        $this->assertTrue($replaceColor, 'assert "replaceColor()" failed');
    }

    /**
     * set brush
     *
     * Expected test result : false
     *
     * @test
     */
    public function testSetBrush()
    {
        // broken
        $isBroken = $this->_invalidImage->setBrush('small star');
        $this->assertFalse($isBroken, 'assert "setBrush()" failed , image is broken');

        $setBrush = $this->_image->setBrush('small star');
        // expected false
        $this->assertFalse($setBrush, 'assert "setBrush()" failed');

        // new brush created
        $setBrush = $this->_image->setBrush(CWD . 'resources/brush/small-star.png');
        $this->assertTrue($setBrush, 'assert "setBrush()" failed, brush is not set');
    }

    /**
     * set background color
     *
     * Expected test result : true
     *
     * @test
     */
    public function testSetBackgroundColor()
    {
        $color = $this->_image->getColor(153, 50, 204);
        $setBackgroundColor = $this->_image->setBackgroundColor($color);
        $this->assertTrue($setBackgroundColor, 'backgroundcolor is not set to $color');

        $setBackgroundColor = $this->_image->setBackgroundColor();
        $this->assertTrue($setBackgroundColor, 'backgroundcolor is not reset');

        $setBackgroundColor = $this->_image->setBackgroundColor($color, false);
        $this->assertTrue($setBackgroundColor, 'backgroundcolor is not replaced with $color');
    }

    /**
     * Set Background Color Invalid Argument
     *
     * @expectedException \PHPUnit_Framework_Error
     * @test
     */
    public function testSetBackgroundColorInvalidArgument()
    {
        $setBackgroundColor = $this->_image->setBackgroundColor(-1, true);
        $this->assertFalse($setBackgroundColor, 'First argument must be a valid color.');
    }

    /**
     * get background color
     *
     * Expected test result : integer
     *
     * @test
     */
    public function testGetBackgroundColor()
    {
        // broken
        $isBroken = $this->_invalidImage->getBackgroundColor();
        $this->assertFalse($isBroken, 'assert "getBackgroundColor()" failed, image is broken');

        // set color
        $color = $this->_image->getColor(153, 50, 204);
        $setBgColor = $this->_image->setBackgroundColor($color);

        $backgroundColor = $this->_image->getBackgroundColor();
        $this->assertInternalType('integer', $backgroundColor, 'assert "getBackgroundColor()"failed, value is not from type integer');
    }

    /**
     * is interlaced
     *
     * Expected test result : false
     *
     * @test
     */
    public function testIsInterlaced()
    {
        // broken
        $isBroken = $this->_invalidImage->isInterlaced();
        $this->assertFalse($isBroken, 'assert "isInterlaced()" failed, image is broken');

        $interlaced = $this->_image->isInterlaced();
        $this->assertFalse($interlaced, 'assert "isInterlaced" failed,  assert is true');
    }

    /**
     * enable interlance
     *
     * Expected test result : true
     *
     * @test
     */
    public function testEnableInterlace()
    {
        // broken
        $isBroken = $this->_invalidImage->enableInterlace(true);
        $this->assertFalse($isBroken, 'assert "enableInterlace()" failed, image is broken');

        $enableInterlace = $this->_image->enableInterlace(true);
        $this->assertTrue($enableInterlace, 'assert "enableInterlace()" failed, assert is false');

        $disableInterlace = $this->_image->enableInterlace(false);
        $this->assertFalse($disableInterlace, 'assert "enableInterlace()" failed, assert is true');
    }

    /**
     * has alpha
     *
     * Expected test result : false
     *
     * @test
     */
    public function testHasAlpha()
    {
        // broken
        $isBroken = $this->_invalidImage->hasAlpha();
        $this->assertFalse($isBroken, 'assert "hasAlpha()" failed, image is broken');

        $hasAlpha = $this->_image->hasAlpha();
        // expected false for non exist alpha
        $this->assertFalse($hasAlpha, 'assert "hasAlpha()" failed, assert is true');
    }

    /**
     * set gamma
     *
     * Expected test result : true
     *
     * @test
     */
    public function testSetGamma()
    {
        // broken
        $isBroken = $this->_invalidImage->setGamma(3.0);
        $this->assertFalse($isBroken, 'assert "setGamma()" failed, image is broken');

        $setGamma = $this->_image->setGamma(3.0);
        $this->assertTrue($setGamma, 'assert "setGamma()" failed, assert is false');

        $setGamma = $this->_image->setGamma(11.0);
        $this->assertFalse($setGamma, 'The argument needs to be an integer betwen 0.1 and 10.0');

        $setGamma = $this->_image->setGamma(-1.0);
        $this->assertFalse($setGamma, 'The argument needs to be an integer betwen 0.1 and 10.0');
    }

    /**
     * rotate
     *
     * Expected test result : true
     *
     * @test
     */
    public function testRotate()
    {
        // broken
        $isBroken = $this->_invalidImage->rotate(12.54);
        $this->assertFalse($isBroken, 'assert "rotate()" failed, image is broken');

        $rotate = $this->_image->rotate(12.54);
        $this->assertTrue($rotate, 'assert "rotate()" failed, assert is false');
    }

    /**
     * Rotate Invalid Argument
     *
     * @expectedException \PHPUnit_Framework_Error
     * @test
     */
    public function testRotateInvalidArgument()
    {
        $rotate = $this->_image->rotate(MAX_INT);
        $this->assertFalse($rotate, 'assert failed, first argument must have a float value');
    }

    /**
     * resize canvas
     *
     * Expected test result : true
     *
     * @test
     */
    public function testResizeCanvas()
    {
        // broken
        $isBroken = $this->_invalidImage->resizeCanvas(100, 100, 20, 20);
        $this->assertFalse($isBroken, 'assert "resizeCanvas()" failed , image is broken');

        $resizeCanvas = $this->_image->resizeCanvas();
        $this->assertTrue($resizeCanvas, 'assert "resizeCanvas()" failed, assert is false');

        $resizeCanvas = $this->_image->resizeCanvas(100, 100, 20, 20);
        $this->assertTrue($resizeCanvas, 'assert "resizeCanvas()" failed, assert is false');

        // expected true negativ values will be converted to positive
        $firstResizeCanvas = $this->_image->resizeCanvas(100, 100, -5, -10, array('red' =>230, 'green' => 120, 'blue' => 98));
        $this->assertTrue($firstResizeCanvas, 'assert "resizeCanvas()" failed, assert is false');

        // expected true - set the last param as an integer
        $secondResizeCanvas = $this->_image->resizeCanvas(100, 100, -5, -10, $this->_image->blue);
        $this->assertTrue($secondResizeCanvas, 'assert "resizeCanvas()" failed, assert is false');
    }

    /**
     * ResizeCanvas Invalid Argument
     *
     * @test
     */
    public function testResizeCanvasInvalidArgument()
    {
        $resizeCanvas = $this->_image->resizeCanvas(0);
        $this->assertFalse($resizeCanvas, 'First argument must be an integer value greater 0.');
    }

    /**
     * ResizeCanvas Invalid Argument 2
     *
     * @test
     */
    public function testResizeCanvasInvalidArgument2()
    {
        $resizeCanvas = $this->_image->resizeCanvas(1, 0);
        $this->assertFalse($resizeCanvas, 'Second argument must be an integer value greater 0.');
    }

    /**
     * ResizeCanvas Invalid Argument 3
     *
     * @test
     */
    public function testResizeCanvasInvalidArgument3()
    {
        $resizeCanvas = $this->_image->resizeCanvas(1, 1, 2);
        $this->assertFalse($resizeCanvas, 'Left padding may not exceed canvas size.');
    }

    /**
     * ResizeCanvas Invalid Argument 4
     *
     * @test
     */
    public function testResizeCanvasInvalidArgument4()
    {
        $resizeCanvas = $this->_image->resizeCanvas(1, 1, 0, 2);
        $this->assertFalse($resizeCanvas, 'Top padding may not exceed canvas size.');
    }

    /**
     * ResizeCanvas Invalid Argument 5
     *
     * @test
     */
    public function testResizeCanvasInvalidArgument5()
    {
        $resizeCanvas = $this->_image->resizeCanvas(100, 100, 20, 20, array('red' =>230, 'green' => 120));
        $this->assertFalse($resizeCanvas, 'Fifth argument must be an array with values red, green, blue.');
    }

    /**
     * resize image
     *
     * Expected test result : true
     *
     * @test
     */
    public function testResizeImage()
    {
        $resizeImage = $this->_image->resizeImage(150, 150);
        $this->assertTrue($resizeImage, 'assert "resizeImage()" failed, assert is false');
    }

    /**
     * resize
     *
     * Expected test result : true
     *
     * @test
     */
    public function testResize()
    {
        // broken
        $isBroken = $this->_invalidImage->resize(100, 50);
        $this->assertFalse($isBroken, 'assert "resize()" failed, image is broken');

        $resize = $this->_image->resize(150, 50);
        $this->assertTrue($resize, 'assert "resize()" failed, assert is false');

        //expected false
        $resize = $this->_image->resize();
        $this->assertFalse($resize, 'assert "resize()" failed, both values cant be NULL');

        //try with the first argument
        $resize = $this->_image->resize(150);
        $this->assertTrue($resize, 'assert "resize()" failed, assert is false');

        //try with the second argument
        $resize = $this->_image->resize(null, 50);
        $this->assertTrue($resize, 'assert "resize()" failed, assert is false');
    }

    /**
     * get transparency
     *
     * Expected test result : integer
     *
     * @test
     */
    public function testGetTransparency()
    {
        // broken
        $isBroken = $this->_invalidImage->getTransparency();
        $this->assertFalse($isBroken, 'assert "getTransparency()" failed, image is broken');

        $transparency = $this->_image->getTransparency();
        $this->assertInternalType('integer', $transparency, 'assert "getTransparency()" failed, value is from type integer');
    }

    /**
     * set transparency
     *
     * Expected test result : integer | true
     *
     * @test
     */
    public function testSetTransparency()
    {
        // broken
        $isBroken = $this->_invalidImage->setTransparency($this->_image->black);
        $this->assertFalse($isBroken, 'assert "setTransparency()" failed, image is broken');

        $color = $this->_image->getColor(0, 205, 0);
        $this->assertInternalType('integer', $color, 'assert "getColor()" failed, value is not from type integer');
        $setTransparency = $this->_image->setTransparency($color);
        $this->assertTrue($setTransparency, 'assert "setTransparency()" failed, assert is false');

        // set beackgroundcolor when no param given
        $setTransparency = $this->_image->setTransparency();
        $this->assertTrue($setTransparency, 'assert "setTransparency()" failed, assert is false');
    }

    /**
     * get palette size
     *
     * Expected test result : integer
     *
     * @test
     */
    public function testGetPaletteSize()
    {
        // broken
        $isBroken = $this->_invalidImage->getPaletteSize();
        $this->assertFalse($isBroken, 'assert "getPaletteSize()" failed, image is broken');

        $paletteSize = $this->_image->getPaletteSize();
        $this->assertInternalType('integer', $paletteSize, 'assert "getPaletteSize()" failed, value is not from type integer');
    }

    /**
     * reduce color depth
     *
     * Expected test result : true
     *
     * @test
     */
    public function testReduceColorDepth()
    {
        // broken
        $isBroken = $this->_invalidImage->reduceColorDepth(100);
        $this->assertFalse($isBroken, 'assert "reduceColorDepth()" failed, image is broken');

        $reduceColorDepth = $this->_image->reduceColorDepth(100);
        $this->assertTrue($reduceColorDepth, 'assert "reduceColorDepth()" failed, assert is false');

        $reduceColorDepth = $this->_image->reduceColorDepth(257);
        $this->assertFalse($reduceColorDepth, 'First argument must be an integer betwen 2-256.');

        $reduceColorDepth = $this->_image->reduceColorDepth(1 ,true);
        $this->assertFalse($reduceColorDepth, 'First argument must be a positive integer.');
    }

    /**
     * copy region
     *
     * Expected test result : true
     *
     * @test
     */
    public function testCopyRegion()
    {
        // broken
        $isBroken = $this->_invalidImage->copyRegion($this->_dummyImage, 30, 30, 120, 120, 30, 30);
        $this->assertFalse($isBroken, 'assert "copyRegion()" failed, image is broken');

       $copyRegion = $this->_image->copyRegion($this->_dummyImage, 30, 30, 120, 120, 30, 30);
       $this->assertTrue($copyRegion, 'assert "copyRegion()" failed, assert is false');
    }

    /**
     * to gray scale
     *
     * Expected test result : true
     *
     * @test
     */
    public function testToGrayscale()
    {
        // broken
        $isBroken = $this->_invalidImage->toGrayscale();
        $this->assertFalse($isBroken, 'assert "toGrayscale()" failed, image is broken');

        $toGrayscale = $this->_image->toGrayscale();
        $this->assertTrue($toGrayscale, 'assert "toGrayscale()" failed, assert is false');
    }

    /**
     * to grey scale
     *
     * Expected test result : true
     *
     * @test
     */
    public function testToGreyscale()
    {
        $toGreyscale = $this->_image->toGreyscale();
        $this->assertTrue($toGreyscale, 'assert "toGreyscale()" failed, assert is false');
    }

    /**
     * monochromatic
     *
     * Expected test result : true
     *
     * @test
     */
    public function testMonochromatic()
    {
        // broken
        $isBroken = $this->_invalidImage->monochromatic(155, 48, 255);
        $this->assertFalse($isBroken, 'assert "monochromatic()" failed, image is broken');

        $monochromatic = $this->_image->monochromatic(155, 48, 255);
        $this->assertTrue($monochromatic, 'assert "monochromatic()" falied, assert is false');
    }

    /**
     * Monochromatic Invalid Argument
     *
     * @expectedException \PHPUnit_Framework_Error
     * @test
     */
    public function testMonochromaticInvalidArgument()
    {
        $this->_image->monochromatic(500, 48, 255);
    }

    /**
     * Monochromatic Invalid Argument1
     *
     * @expectedException \PHPUnit_Framework_Error
     * @test
     */
    public function testMonochromaticInvalidArgument1()
    {
        $this->_image->monochromatic(155, -25, 255);
    }

    /**
     * brightness
     *
     * Expected test result : true
     *
     * @test
     */
    public function testBrightness()
    {
        // broken
        $isBroken = $this->_invalidImage->brightness(0.4);
        $this->assertFalse($isBroken, 'assert "brightness()" failed, image is broken');

        $brightness = $this->_image->brightness(0.4);
        $this->assertTrue($brightness, 'assert "brightness()" failed, assert is false');

        $brightness = $this->_image->brightness(0.0);
        $this->assertTrue($brightness, 'assert "brightness()" failed, assert is false');
    }

    /**
     * Brightness Invalid Argument
     *
     * @test
     */
    public function testBrightnessInvalidArgument()
    {
        $this->assertFalse($this->_image->brightness(-1));
    }

    /**
     * Brightness Invalid Argument1
     *
     * @test
     */
    public function testBrightnessInvalidArgument1()
    {
        $this->assertFalse($this->_image->brightness(5));
    }


    /**
     * contrast
     *
     * Expected test result : true
     *
     * @test
     */
    public function testContrast()
    {
        // broken
        $isBroken = $this->_invalidImage->contrast(0.4);
        $this->assertFalse($isBroken, 'assert "contrast()" failed, image is broken');

        $contrast = $this->_image->contrast(0.4);
        $this->assertTrue($contrast, 'assert "contrast()" failed, assert is false');

        $contrast = $this->_image->contrast(0.0);
        $this->assertTrue($contrast, 'assert "contrast()" failed, assert is false');
    }

    /**
     * Contrast Invalid Argument
     *
     * @test
     */
    public function testContrastInvalidArgument()
    {
        $this->assertFalse($this->_image->contrast(-3));
    }

    /**
     * Contrast Invalid Argument1
     *
     * @test
     */
    public function testContrastInvalidArgument1()
    {
        $this->assertFalse($this->_image->contrast(2));
    }


    /**
     * negate
     *
     * Expected test result : true
     *
     * @test
     */
    public function testNegate()
    {
        // broken
        $isBroken = $this->_invalidImage->negate();
        $this->assertFalse($isBroken, 'assert "negate()" failed, image is broken');

        $negate = $this->_image->negate();
        $this->assertTrue($negate, 'assert "negate()" failed, assert is false');
    }

    /**
     * apply filter
     *
     * @test
     */
    public function testApplyFilter()
    {
        // broken
        $isBroken = $this->_invalidImage->applyFilter(IMG_FILTER_NEGATE);
        $this->assertFalse($isBroken, 'assert "applyFilter()" failed, image is broken');

        // set filter with no args
        $filter = $this->_image->applyFilter(IMG_FILTER_NEGATE);
        $this->assertTrue($filter, 'assert "applyFilter()" failed, filter is not set');

        // set filter with 1 arg
        $filter = $this->_image->applyFilter(IMG_FILTER_SMOOTH, 0.5);
        $this->assertTrue($filter, 'assert "applyFilter()" failed, filter is not set');

        // set filter with 3 args
        $filter = $this->_image->applyFilter(IMG_FILTER_COLORIZE, 100, 100, 100);
        $this->assertTrue($filter, 'assert "applyFilter()" failed, filter is not set');

        // set filter with 4 args
        $filter = $this->_image->applyFilter(IMG_FILTER_COLORIZE, 100, 100, 100, 100);
        $this->assertTrue($filter, 'assert "applyFilter()" failed, filter is not set');
    }

    /**
     * colorize
     *
     * Expected test result : true
     *
     * @test
     */
    public function testColorize()
    {
        // broken
        $isBroken = $this->_invalidImage->colorize(255, 50, 150);
        $this->assertFalse($isBroken, 'assert "colorize()" failed, image is broken');

        $colorize = $this->_image->colorize(255, 50, 150);
        $this->assertTrue($colorize, 'assert "colorize()" failed, assert is false');
    }

    /**
     * Colorize Invalid Argument
     *
     * @expectedException \PHPUnit_Framework_Error
     * @test
     */
    public function testColorizeInvalidArgument()
    {
        $this->_image->colorize(256, 50, 150);
    }

    /**
     * multiply
     *
     * Expected test result : true
     *
     * @test
     */
    public function testMultiply()
    {
        // broken
        $isBroken = $this->_invalidImage->multiply(200, 255, 255);
        $this->assertFalse($isBroken, 'assert "multiply()" failed, image is broken');

        $multiply = $this->_image->multiply(200, 255, 255);
        $this->assertTrue($multiply, 'assert "multiply()" failed, assert is false');
    }

    /**
     * Multiply Invalid Argument
     *
     * @expectedException \PHPUnit_Framework_Error
     * @test
     */
    public function testMultiplyInvalidArgument()
    {
        $this->_image->multiply(-255, 50, 150);
    }

    /**
     * Multiply Invalid Argument1
     *
     * @expectedException \PHPUnit_Framework_Error
     * @test
     */
    public function testMultiplyInvalidArgument1()
    {
        $this->_image->multiply(255, 500, 150);
    }

    /**
     * blur
     *
     * Expected test result : true
     *
     * @test
     */
    public function testBlur()
    {
        // broken
        $isBroken = $this->_invalidImage->blur(0.95);
        $this->assertFalse($isBroken, 'assert "blur()" failed, image is broken');

        $blur = $this->_image->blur(0.95);
        $this->assertTrue($blur, 'assert "blur()" failed, assert is false');
    }

    /**
     * Blur Invalid Argument
     *
     * @test
     */
    public function testBlurInvalidArgument()
    {
        $this->assertFalse($this->_image->blur(2.0));
    }

    /**
     * Blur Invalid Argument1
     *
     * @test
     */
    public function testBlurInvalidArgument1()
    {
        $this->assertFalse($this->_image->blur(-1));
    }

    /**
     * sharpen
     *
     * Expected test result : true
     *
     * @test
     */
    public function testSharpen()
    {
        // broken
        $isBroken = $this->_invalidImage->sharpen(0.62);
        $this->assertFalse($isBroken, 'assert "sharpen()" failed, image is broken');

        $sharpen = $this->_image->sharpen(0.62);
        $this->assertTrue($sharpen, 'assert "sharpen()" failed, assert is false');

        $sharpen = $this->_image->sharpen(0);
        $this->assertTrue($sharpen, 'assert "sharpen()" failed, assert is false');

    }

    /**
     * Sharpen Invalid Argument
     *
     * @test
     */
    public function testSharpenInvalidArgument()
    {
        $sharpen = $this->_image->sharpen(2);
        $this->assertFalse($sharpen, 'assert failed, first argument must be between 0 and 1');
    }

    /**
     * flip x
     *
     * Expected test result : true
     *
     * @test
     */
    public function testFlipX()
    {
        // broken
        $isBroken = $this->_invalidImage->flipX();
        $this->assertFalse($isBroken, 'assert "flipX()" failed, image is broken');

        $flipX = $this->_image->flipX();
        $this->assertTrue($flipX, 'assert "flipX()" failed, assert is false');
    }

    /**
     * flip y
     *
     * Expected test result : true
     *
     * @test
     */
    public function testFlipY()
    {
        // broken
        $isBroken = $this->_invalidImage->flipY();
        $this->assertFalse($isBroken, 'assert "flipY()" failed, image is broken');

        $flipY = $this->_image->flipY();
        $this->assertTrue($flipY, 'assert "flipY()" failed, assert is false');
    }

    /**
     * copy palette
     *
     * @test
     */
    public function testCopyPalette()
    {
        // with image file given
        $copyPalette = $this->_image->copyPalette($this->_dummyImage);
        $this->assertTrue($copyPalette, 'assert "copyPalette()" failed, the palette cant be copied');

        // with object given
        $copyPalette = $this->_image->copyPalette($this->_image);
        $this->assertTrue($copyPalette, 'assert "copyPalette()" failed, the palette cant be copied');
    }

    /**
     * output to screen
     *
     * @ignore
     * @test
     */
    public function testOutputToScreen()
    {
      // intentionally left blank
    }

    /**
     * Output To Screen Invalid Argument
     *
     * @test
     */
    public function testOutputToScreenInvalidArgument()
    {
        $outputToScreen = $this->_image->outputToScreen($this->_dummySource);
        $this->assertFalse($outputToScreen, 'assert "outputToScreen()" failed, assert is true');
    }

    /**
     * output to file
     *
     * Expected test result : string
     *
     * @test
     */
    public function testOutputToFile()
    {
        $filename = dirname(CWD . $this->_imageSource) . DIRECTORY_SEPARATOR . 'testimage';
        $outputToFile = $this->_image->outputToFile($filename, 'png');
        $isFile = is_file($outputToFile);
        $isDeleted = unlink($outputToFile);
        $this->assertInternalType('string', $outputToFile, 'unable to create output file');
        $this->assertTrue($isFile, 'returned image-path is not a valid');
        $this->assertTrue($isDeleted, 'unable to delete file');
    }

    /**
     * compare image
     *
     * Expected test result : float
     *
     * @test
     */
    public function testCompareImage()
    {
        $compareImage = $this->_image->compareImage($this->_dummyImage);
        $this->assertInternalType('float', $compareImage, 'assert "compareImage()" failed, value is not from type float');
    }

    /**
     * Compare Image Invalid Argument
     *
     * @test
     */
    public function testCompareImageInvalidArgument()
    {
        $nonExistPath = 'nonexist.png';
        $this->_image->compareImage($nonExistPath);
    }

    /**
     * test 1
     *
     * Expected test result, see: resources/image/test1.png
     *
     * @test
     */
    public function test1()
    {
        $color = $this->_image->getColorAt(90, 90);
        $this->assertInternalType('integer', $color, 'assert failed "$color" is not from type integer');

        $setTransparency0 = $this->_image->setTransparency($color);
        $this->assertTrue($setTransparency0, 'assert failed "$setTransparency0" is false');

        $setBackgroundColor = $this->_image->setBackgroundColor($this->_image->white);
        $this->assertTrue($setBackgroundColor, 'assert failed "$setBackgroundColor" is false');

        $flipY0 = $this->_image->flipY();
        $this->assertTrue($flipY0, 'assert failed "$flipY0" is false');

        $flipY1 = $this->_image->flipY();
        $this->assertTrue($flipY1, 'assert failed "$flipY1" is false');

        $flipX0 = $this->_image->flipX();
        $this->assertTrue($flipX0, 'assert failed "$flipX0" is false');

        $flipX1 = $this->_image->flipX();
        $this->assertTrue($flipX1, 'assert failed "$flipX1" is false');

        $sharpen = $this->_image->sharpen(0.85);
        $this->assertTrue($sharpen, 'assert failed "$sharpen" is false');

        /**
         * compare test results
         *
         * Compare resulting image of test with expected result.
         * We grant a 5% tolerance.
         */
        $expectedResult = new Image(CWD . 'resources/image/test1.png');
        $difference = $expectedResult->compareImage($this->_image);
        $this->assertLessThan(0.05, $difference, 'assert failed, image does not match expected result');
    }

    /**
     * test 2
     *
     * Expected test result, see: resources/image/test2.png
     *
     * @test
     */
    public function test2()
    {
        $enableAntialias0 = $this->_emptyImage->enableAntialias();
        $this->assertTrue($enableAntialias0, 'assert failed "enableAntialias0" is false');

        $enableInterlace0 = $this->_emptyImage->enableInterlace();
        $this->assertTrue($enableInterlace0, 'assert failed "$enableInterlace0" is false');

        $setLineWidth0 = $this->_emptyImage->setLineWidth(3);
        $this->assertTrue($setLineWidth0, 'assert failed "$setLineWidth0"');

        $r = $this->_emptyImage->red;
        $l = $this->_emptyImage->lime;
        $w = $this->_emptyImage->getBackgroundColor();
        $this->assertInternalType('integer', $w, 'asssert failed "$w" is not from typ integer');

        $setLineStyle0 = $this->_emptyImage->setLineStyle($r, $l, $w);
        $this->assertTrue($setLineStyle0, 'assert failed "$setLineStyle0" is false');

        // the following should not be visible
        $drawString0 = $this->_emptyImage->drawString('fill error', 20, 40, $this->_emptyImage->red);
        $this->assertTrue($drawString0, 'assert failed "$drawString0" is false');

        // paint it with background color
        $fill0 = $this->_emptyImage->fill($this->_emptyImage->yellow, 0, 0, $this->_emptyImage->black);
        $this->assertTrue($fill0, 'assert failed "$fill0" is false');

        $fill1 = $this->_emptyImage->fill($this->_emptyImage->getBackgroundColor(), 0, 0, $this->_emptyImage->black);
        $this->assertTrue($fill1, 'assert failed "$fill1" is false');

        // the following should not be visible
        $drawString1 = $this->_emptyImage->drawString('canvas error', 20, 60, $this->_emptyImage->red);
        $this->assertTrue($drawString1, 'assert failed "$drawString1" is false');
        $clearCanvas = $this->_emptyImage->clearCanvas();


        // this should do nothing
        $fill2 = $this->_emptyImage->fill($this->_emptyImage->red, 0, 0, $this->_emptyImage->getBackgroundColor());
        $this->assertTrue($fill2, 'assert failed "$fill2" is false');

        // draw some objects
        $drawEllipse0 = $this->_emptyImage->drawEllipse(30, 30, 50, null, $this->_emptyImage->black, $this->_emptyImage->white, -30, 150);
        $this->assertTrue($drawEllipse0, 'assert failed "$drawEllipse0" is false');

        $rotate0 = $this->_emptyImage->rotate(-30.0);
        $this->assertTrue($rotate0, 'assert failed "$rotate0" is false');

        $drawEllipse1 = $this->_emptyImage->drawEllipse(110, 40, 50, 10, $this->_emptyImage->black, $this->_emptyImage->white, 180, 0);
        $this->assertTrue($drawEllipse1, 'assert failed "$drawEllipse1" is false');

        $drawEllipse2 = $this->_emptyImage->drawEllipse(110, 50, 20, null, IMG_COLOR_STYLED);
        $this->assertTrue($drawEllipse2, 'assert failed "$drawEllipse2" is false');

        $drawEllipse3 = $this->_emptyImage->drawEllipse(110, 50, 12, null, null, $this->_emptyImage->black);
        $this->assertTrue($drawEllipse3, 'assert failed "$drawEllipse3" is false');

        $setBackgroundColor0 = $this->_emptyImage->setBackgroundColor($this->_emptyImage->lime);
        $this->assertTrue($setBackgroundColor0, 'assert failed "$setBackgroundColor0" is false');

        $drawEllipse4 = $this->_emptyImage->drawEllipse(75, 75, 150, null, $this->_emptyImage->blue);
        $this->assertTrue($drawEllipse4, 'assert failed "$drawEllipse4" is false');

        $points = array(
            0 => array( 20, 0  ),
            1 => array( 40, 20 ),
            2 => array( 0, 20  )
        );
        $drawPolygon0 = $this->_emptyImage->drawPolygon($points, 53, 80, $this->_emptyImage->black, $this->_emptyImage->fuchsia);
        $this->assertTrue($drawPolygon0, 'assert failed "$drawPolygon0" is false');

        // set transparency
        $setTransparency1 = $this->_emptyImage->setTransparency($this->_emptyImage->lime);
        $this->assertTrue($setTransparency1, 'assert failed "$setTransparency1" is false');

        // resize the image
        $resize0 = $this->_emptyImage->resize(400);
        $this->assertTrue($resize0, 'assert failed "$resize0" is false');

        $drawString2 = $this->_emptyImage->drawString('don\'t keep smiling!', 50, 200);
        $this->assertTrue($drawString2, 'assert failed "$drawString2" is false');

        $h = $this->_emptyImage->getFontHeight(2);
        $this->assertInternalType('integer', $h, 'assert failed "$h" is not from type integer');

        $w = $this->_emptyImage->getFontWidth(2);
        $this->assertInternalType('integer', $w, 'assert failed "$w" is not from type integer');

        $drawString3 = $this->_emptyImage->drawString('_____', 50, 200 - (int) floor($h / 3), $this->_emptyImage->red);
        $this->assertTrue($drawString3, 'assert failed "$drawString3" is false');

        $drawString4 = $this->_emptyImage->drawString('____________', 50 + ($w * 6), 202, $this->_emptyImage->red);
        $this->assertTrue($drawEllipse4, 'assert failed "$drawString4" is false');

        // draw some other objects
        $drawRectangle0 = $this->_emptyImage->drawRectangle(50, 150, 100, 10, $this->_emptyImage->black, $this->_emptyImage->white);
        $this->assertTrue($drawRectangle0, 'assert failed "$drawRectangle0" is false');

        $setLineWidth1 = $this->_emptyImage->setLineWidth(2);
        $this->assertTrue($setLineWidth1, 'assert failed "$setLineWidth1" is false');

        $drawRectangle1 = $this->_emptyImage->drawRectangle(75, 160, 50, 8, $this->_emptyImage->black, $this->_emptyImage->lime);
        $this->assertTrue($drawRectangle1, 'assert failed "$drawRectangle1" is false');

        $setLineWidth2 = $this->_emptyImage->setLineWidth(1);
        $this->assertTrue($setLineWidth2, 'assert failed "$setLineWidth2" is false');

        $drawRectangle2 = $this->_emptyImage->drawRectangle(85, 168, 30, 4, $this->_emptyImage->black, $this->_emptyImage->maroon);
        $this->assertTrue($drawRectangle2, 'assert failed "$drawRectangle2" is false');

        // this line should be dashed
        $setLineWidth3 = $this->_emptyImage->setLineWidth(10);
        $this->assertTrue($setLineWidth3, 'assert failed "$setLineWidth3" is false');

        $drawLine0 = $this->_emptyImage->drawLine(70, 50, 40, 80, IMG_COLOR_STYLED);
        $this->assertTrue($drawLine0, 'assert failed "$drawLine0" is false');

        $setGamma0 = $this->_emptyImage->setGamma(0.5);
        $this->assertTrue($setGamma0, 'assert failed "$setGamma0" is false');

        $fill3 = $this->_emptyImage->fill($this->_emptyImage->yellow, 100, 100);
        $this->assertTrue($fill3, 'assert failed "$fill3" is false');

        $enableAntialias1 = $this->_emptyImage->enableAntialias();
        $this->assertTrue($enableAntialias1, 'assert failed "$enableAntialias1" is false');

        $drawPoint0 = $this->_emptyImage->drawPoint(90, 126, IMG_COLOR_STYLEDBRUSHED);
        $this->assertTrue($drawPoint0, 'assert failed "$drawPoint0" is false');

        $drawPoint1 = $this->_emptyImage->drawPoint(105, 126, IMG_COLOR_BRUSHED);
        $this->assertTrue($drawPoint1, 'assert failed "$drawPoint1" is false');

        $replaceColor = $this->_emptyImage->replaceColor($this->_emptyImage->fuchsia, $this->_emptyImage->silver);
        $this->assertTrue($replaceColor, 'assert failed "$replaceColor" is false');

        $replaceColor0 = $this->_emptyImage->replaceColor($this->_emptyImage->lime, $this->_emptyImage->white);
        $this->assertTrue($replaceColor0, 'assert failed "$replaceColor0" is false');

        $drawFormattedString0 = $this->_emptyImage->drawFormattedString('Hello World!', 50, 40, $this->_emptyImage->black, null, 14, 5);
        $this->assertTrue($drawFormattedString0, 'assert failed "$drawFormattedString0" is false');

        $negate0 = $this->_emptyImage->negate();
        $this->assertTrue($negate0, 'assert failed "$negate0" is false');

        /**
         * compare test results
         *
         * Compare resulting image of test with expected result.
         * We grant a 5% tolerance.
         */
        $expectedResult = new \Yana\Media\Image(CWD . 'resources/image/test2.png');
        $difference = $expectedResult->compareImage($this->_emptyImage);
        $this->assertLessThan(0.05, $difference, 'assert failed, image does not match expected result');
    }

    /**
     * test 3
     *
     * Expected test result, see: resources/image/test3.png
     *
     * @test
     */
    public function test3()
    {
        // set brush
        $brush = new Brush('small star');

        $brush->setColor(128, 128, 128)->setSize(8);

        $setBackgroundColor = $this->_emptyImage->setBackgroundColor($this->_emptyImage->yellow);
        $this->assertTrue($setBackgroundColor, 'unable to set background color');

        $setBrush0 = $this->_emptyImage->setBrush($brush);
        $this->assertTrue($setBrush0, 'unable to select new brush for image');

        $drawPoint = $this->_emptyImage->drawPoint(150, 100, IMG_COLOR_BRUSHED);
        $this->assertTrue($drawPoint, 'unable to draw Point');

        $drawLine = $this->_emptyImage->drawLine(150, 100, 154, 104, IMG_COLOR_BRUSHED);
        $this->assertTrue($drawLine, 'unable to draw line');

        $drawRectangle3 = $this->_emptyImage->drawRectangle(10, 10, 60, 60, $this->_emptyImage->black, $this->_emptyImage->blue);
        $this->assertTrue($drawRectangle3, 'assert failed "$drawRectangle3" is false');

        $drawEllipse5 = $this->_emptyImage->drawEllipse(155, 65, 100, 50, $this->_emptyImage->black, $this->_emptyImage->fuchsia);
        $this->assertTrue($drawEllipse5, 'assert failed "$drawEllipse5" is false');

        /**
         * compare test results
         *
         * Compare resulting image of test with expected result.
         * We grant a 5% tolerance.
         */
        $expectedResult = new Image(CWD . 'resources/image/test3.png');
        $difference = $expectedResult->compareImage($this->_emptyImage);
        $this->assertLessThan(0.05, $difference, 'assert failed, image does not match expected result');
    }

    /**
     * test 4
     *
     * Expected test result, see: resources/image/test4.png
     *
     * @test
     */
    public function test4()
    {
        // 1) identical
        $image = new Image(CWD . 'resources/image/test4.png');
        $imageSame = clone $image;
        $compareImage = $image->compareImage($imageSame);
        unset($image, $imageSame);
        $this->assertLessThan(0.0001, $compareImage, 'assert failed, images should be equal');

        // 2) different
        $imageBlack = new Image(CWD . 'resources/image/test4.png');
        $imageWhite = clone $imageBlack;
        $imageWhite->negate();
        $compareImage = $imageBlack->compareImage($imageWhite);
        unset($imageBlack, $imageWhite);
        $this->assertGreaterThan(0.9999, $compareImage, 'assert failed, images should NOT be equal');
    }

}
