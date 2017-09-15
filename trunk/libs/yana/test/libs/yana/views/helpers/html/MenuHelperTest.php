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

namespace Yana\Views\Helpers\Html;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';

/**
 * @package  test
 */
class MenuHelperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\Html\MenuHelper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = \Yana\Views\Helpers\Html\MenuHelper::factory();
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
    public function testFactory()
    {
        $o1 = \Yana\Views\Helpers\Html\MenuHelper::factory(\Yana\Views\Helpers\Html\MenuLayouts\LayoutEnumeration::SIMPLE);
        $o2 = \Yana\Views\Helpers\Html\MenuHelper::factory(\Yana\Views\Helpers\Html\MenuLayouts\LayoutEnumeration::VERTICAL);
        $o3 = \Yana\Views\Helpers\Html\MenuHelper::factory(\Yana\Views\Helpers\Html\MenuLayouts\LayoutEnumeration::HORIZONTAL);
        $e1 = new \Yana\Views\Helpers\Html\MenuHelper(new \Yana\Views\Helpers\Html\MenuLayouts\SimpleLayout());
        $e2 = new \Yana\Views\Helpers\Html\MenuHelper(new \Yana\Views\Helpers\Html\MenuLayouts\VerticalLayout());
        $e3 = new \Yana\Views\Helpers\Html\MenuHelper(new \Yana\Views\Helpers\Html\MenuLayouts\HorizontalLayout());
        $this->assertEquals($e1, $o1);
        $this->assertEquals($e2, $o2);
        $this->assertEquals($e3, $o3);
    }

    /**
     * @test
     */
    public function testUseKeys()
    {
        $this->assertSame(\Yana\Views\Helpers\Html\MenuLayouts\KeyEnumeration::DONT_CONVERT_HREF, $this->object->useKeys());
    }

    /**
     * @test
     */
    public function testSetUseKeys()
    {
        $value1 = \Yana\Views\Helpers\Html\MenuLayouts\KeyEnumeration::CONVERT_HREF;
        $this->assertSame($value1, $this->object->setUseKeys($value1)->useKeys());
        $value2 = \Yana\Views\Helpers\Html\MenuLayouts\KeyEnumeration::DONT_PRINT_KEYS;
        $this->assertSame($value2, $this->object->setUseKeys($value2)->useKeys());
        $value3 = \Yana\Views\Helpers\Html\MenuLayouts\KeyEnumeration::DONT_CONVERT_HREF;
        $this->assertSame($value3, $this->object->setUseKeys($value3)->useKeys());
    }

    /**
     * @test
     */
    public function testAllowHtml()
    {
        $this->assertFalse($this->object->allowHtml());
    }

    /**
     * @test
     */
    public function testSetAllowHtml()
    {
        $this->assertTrue($this->object->setAllowHtml(true)->allowHtml());
        $this->assertFalse($this->object->setAllowHtml(false)->allowHtml());
    }

    /**
     * @test
     */
    public function test__invoke()
    {
        $this->assertInternalType('string', $this->object->__invoke(array()));
    }

}
