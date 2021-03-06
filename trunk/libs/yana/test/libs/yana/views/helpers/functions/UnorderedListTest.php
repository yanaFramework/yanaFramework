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
class UnorderedListTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\Functions\UnorderedList
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
        $this->object = new \Yana\Views\Helpers\Functions\UnorderedList(new \Yana\Core\Dependencies\Container($configuration));
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
    public function test__invoke()
    {
        $expected = "";
        $this->assertSame($expected, $this->object->__invoke(array(), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @test
     */
    public function test__invokeWithValue()
    {
        $value = array(
            '1.html' => 'Link',
            'Menu 1' => array(
                '2_1.html' => '1) Entry',
                '2_2.html' => '2) Entry',
                'Menu 2' => array(
                    '2_3_1.html' => '1) Entry',
                    '2_3_2.html' => '2) Entry'
                ),
            ),
            'Menu 3' => array(
                '3_1.html' => '1) Entry',
                '3_2.html' => '2) Entry',
                '3_2.html' => '3) Entry'
            ),
        );
        $params = array('value' => $value, 'layout' => 1);
        $expected = '<ul class="gui_array_list">' .
            '<li class="gui_array_list">' .
            '<span class="gui_array_key">1.html:</span>' .
            '<span class="gui_array_value">Link</span>' .
            '</li>' .
            '<li class="gui_array_head" onmouseover="this.className=\'gui_array_head_open\'" ' .
            'onmouseout="this.className=\'gui_array_head\'">' .
            '<span class="gui_array_key">Menu 1</span>' .
            '<ul class="gui_array_list"><li class="gui_array_list">' .
            '<span class="gui_array_key">2_1.html:</span>' .
            '<span class="gui_array_value">1) Entry</span>' .
            '</li>' .
            '<li class="gui_array_list">' .
            '<span class="gui_array_key">2_2.html:</span>' .
            '<span class="gui_array_value">2) Entry</span>' .
            '</li>' .
            '<li class="gui_array_head" onmouseover="this.className=\'gui_array_head_open\'" ' .
            'onmouseout="this.className=\'gui_array_head\'">' .
            '<span class="gui_array_key">Menu 2</span>' .
            '<ul class="gui_array_list"><li class="gui_array_list">' .
            '<span class="gui_array_key">2_3_1.html:</span>' .
            '<span class="gui_array_value">1) Entry</span>' .
            '</li>' .
            '<li class="gui_array_list">' .
            '<span class="gui_array_key">2_3_2.html:</span>' .
            '<span class="gui_array_value">2) Entry</span>' .
            '</li>' .
            '</ul></li></ul></li>' .
            '<li class="gui_array_head" onmouseover="this.className=\'gui_array_head_open\'" ' .
            'onmouseout="this.className=\'gui_array_head\'">' .
            '<span class="gui_array_key">Menu 3</span>' .
            '<ul class="gui_array_list"><li class="gui_array_list">' .
            '<span class="gui_array_key">3_1.html:</span>' .
            '<span class="gui_array_value">1) Entry</span>' .
            '</li>' .
            '<li class="gui_array_list">' .
            '<span class="gui_array_key">3_2.html:</span>' .
            '<span class="gui_array_value">3) Entry</span>' .
            '</li></ul></li></ul>';
        $this->assertSame($expected, $this->object->__invoke($params, new \Smarty_Internal_Template("name", new \Smarty())));
    }

}
