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
declare(strict_types=1);

namespace Yana\Media;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../include.php';

/**
 * @package  test
 */
class BrushTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Media\Brush
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Media\Brush('point');
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
     * @expectedException \Yana\Core\Exceptions\Files\NotFoundException
     */
    public function test__constructNotFoundException()
    {
        new \Yana\Media\Brush('no-such-brush');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function test__constructInvalidArgumentException()
    {
        $directory = \Yana\Media\Brush::getDirectory();
        \Yana\Media\Brush::setDirectory(\CWD . '/resources/brush/');
        try {
            new \Yana\Media\Brush('invalid');
        } catch (\Exception $e) {
            \Yana\Media\Brush::setDirectory($directory);
            throw $e;
        }
    }

    /**
     * @test
     */
    public function testGetName()
    {
        $this->assertSame('point', $this->object->getName());
    }

    /**
     * @test
     */
    public function testSetDirectory()
    {
        $directory = \Yana\Media\Brush::getDirectory();
        $this->assertNull(\Yana\Media\Brush::setDirectory(\CWD));
        $this->assertSame(CWD, \Yana\Media\Brush::getDirectory());
        $this->assertNull(\Yana\Media\Brush::setDirectory($directory));
    }

    /**
     * @test
     */
    public function testResetDirectory()
    {
        $directory = \Yana\Media\Brush::getDirectory();
        $this->assertNull(\Yana\Media\Brush::setDirectory(\CWD));
        \Yana\Media\Brush::resetDirectory();
        $this->assertNotSame(CWD, \Yana\Media\Brush::getDirectory());
        $this->assertStringEndsWith(DIRECTORY_SEPARATOR . 'brushes' . DIRECTORY_SEPARATOR, \Yana\Media\Brush::getDirectory());
        $this->assertNull(\Yana\Media\Brush::setDirectory($directory));
    }

    /**
     * @covers Yana\Media\Brush::getDirectory
     * @todo   Implement testGetDirectory().
     */
    public function testGetDirectory()
    {
        $directory = \Yana\Media\Brush::getDirectory();
        $this->assertInternalType('string', $directory);
        $this->assertTrue(\is_dir($directory));
    }

    /**
     * @test
     */
    public function testGetSize()
    {
        $this->assertSame(1, $this->object->getSize());
    }

    /**
     * @test
     */
    public function testSetSize()
    {
        $this->assertSame(2, $this->object->setSize(2)->getSize());
    }

    /**
     * @test
     */
    public function testSetColor()
    {
        $this->assertSame(array('red' => 1, 'green' => 2, 'blue' => 3, 'alpha' => 127), $this->object->setColor(1, 2, 3, 1.0)->getColor());
    }

    /**
     * @test
     */
    public function testGetColor()
    {
        $this->assertSame(array('red' => 0, 'green' => 0, 'blue' => 0, 'alpha' => 0), $this->object->getColor());
    }

    /**
     * @test
     */
    public function test__toString()
    {
        $this->assertSame('point', (string) $this->object);
    }

    /**
     * @test
     */
    public function testEquals()
    {
        $pointBrush = new \Yana\Media\Brush('point');
        $otherBrush = new \Yana\Media\Brush('star');
        $this->assertFalse($this->object->equals($otherBrush));
        $this->assertTrue($this->object->equals($pointBrush));
    }

    /**
     * @test
     */
    public function testEqualsResoure()
    {
        $pointBrush = new \Yana\Media\Brush('point');
        $otherBrush = new \Yana\Media\Brush('star');
        $this->assertFalse($this->object->equalsResoure($otherBrush->getResource()));
        $this->assertFalse($this->object->equalsResoure($pointBrush->getResource()));
        $this->assertTrue($this->object->equalsResoure($this->object->getResource()));
    }

    /**
     * @test
     */
    public function testGetResource()
    {
        $this->assertTrue(\is_resource($this->object->getResource()));
    }

}
