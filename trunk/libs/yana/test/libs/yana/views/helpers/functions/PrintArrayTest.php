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
declare(strict_types=1);

namespace Yana\Views\Helpers\Functions;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';

/**
 * @package  test
 */
class PrintArrayTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\Functions\PrintArray
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!\class_exists('\Smarty') || !\class_exists('\Smarty_Internal_Template')) {
            $this->markTestSkipped();
        }
        $configurationFactory = new \Yana\ConfigurationFactory();
        $configuration = $configurationFactory->loadConfiguration(CWD . 'resources/system.config.xml');
        $configuration->configdrive = YANA_INSTALL_DIR . 'config/system.drive.xml';
        $this->object = new \Yana\Views\Helpers\Functions\PrintArray(new \Yana\Core\Dependencies\Container($configuration));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @covers Yana\Views\Helpers\Functions\PrintArray::__invoke
     * @test
     */
    public function test__invokeEmpty()
    {
        $expected = "";
        $this->assertSame($expected, $this->object->__invoke(array(), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @covers Yana\Views\Helpers\Functions\PrintArray::__invoke
     * @test
     */
    public function test__invokeWithInvalidDataType()
    {
        $expected = "1";
        $this->assertSame($expected, $this->object->__invoke(array('value' => 1), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @covers Yana\Views\Helpers\Functions\PrintArray::__invoke
     * @test
     */
    public function test__invokeWithArray()
    {
        $expected = '<pre>' .
            '<span style="color: #35a;">&lt;0&gt;</span>1<span style="color: #35a;">&lt;/0&gt;</span>' . "\n" .
            '<span style="color: #35a;">&lt;1&gt;</span>2<span style="color: #35a;">&lt;/1&gt;</span>' . "\n" .
            '</pre>';
        $this->assertSame($expected, $this->object->__invoke(array('value' => array(1, 2)), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @covers Yana\Views\Helpers\Functions\PrintArray::__invoke
     * @test
     */
    public function test__invokeWithString()
    {
        $expected = '<pre>' .
            '<span style="color: #35a;">&lt;0&gt;</span>1<span style="color: #35a;">&lt;/0&gt;</span>' . "\n" .
            '<span style="color: #35a;">&lt;1&gt;</span>2<span style="color: #35a;">&lt;/1&gt;</span>' . "\n" .
            '</pre>';
        $this->assertSame($expected, $this->object->__invoke(array('value' => "<0>1</0>\n<1>2</1>"), new \Smarty_Internal_Template("name", new \Smarty())));
    }

}
