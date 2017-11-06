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

namespace Yana\Plugins\Annotations;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Test class.
 * @ignore
 */
class MyReflectionClass
{

    public function reflection1()
    {

    }

    public function reflection2()
    {

    }
}

/**
 * @package  test
 */
class ReflectionClassTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Annotations\ReflectionClass
     */
    protected $className = 'Yana\Plugins\Annotations\MyReflectionClass';

    /**
     * @var \Yana\Plugins\Annotations\ReflectionClass
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Annotations\ReflectionClass($this->className);
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
    public function testGetClassName()
    {
        $this->assertSame($this->className, $this->object->getClassName());
    }

    /**
     * @test
     */
    public function testGetMethod()
    {
        $this->assertEquals(new \Yana\Plugins\Annotations\ReflectionMethod($this->className, 'reflection1'), $this->object->getMethod('reflection1'));
    }

    /**
     * @test
     */
    public function testGetMethods()
    {
        $expected = array(
            new \Yana\Plugins\Annotations\ReflectionMethod($this->className, 'reflection1'),
            new \Yana\Plugins\Annotations\ReflectionMethod($this->className, 'reflection2')
        );
        $this->assertEquals($expected, $this->object->getMethods());
    }

    /**
     * @test
     */
    public function testGetTitle()
    {
        $this->assertSame("PHPUnit test-case", $this->object->getTitle());
    }

    /**
     * @test
     */
    public function testGetText()
    {
        $text = $this->object->getText();
        $this->assertStringStartsWith("Software:  Yana PHP-Framework", $text);
    }

    /**
     * @test
     */
    public function testGetPageComment()
    {
        $text = $this->object->getPageComment();
        $this->assertContains(" * PHPUnit test-case", $text);
        $this->assertStringEndsWith(" */", $text);
    }

    /**
     * @test
     */
    public function testGetDocComment()
    {
        $this->assertContains(" * Test class.", $this->object->getDocComment());
        $this->assertContains("@ignore", $this->object->getDocComment());
        $this->assertStringEndsWith(" */", $this->object->getDocComment());
    }

    /**
     * @test
     */
    public function testGetLastModified()
    {
        $this->assertSame(filemtime(__FILE__), $this->object->getLastModified());
    }

    /**
     * @test
     */
    public function testGetDirectory()
    {
        $this->assertSame('libs/yana/plugins/annotations', $this->object->getDirectory());
    }

}
