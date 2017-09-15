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

namespace Yana\Views\Helpers\Html\MenuLayouts;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../../include.php';

/**
 * @package  test
 */
class SimpleLayoutTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SimpleLayout
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new SimpleLayout;
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
        $array = array(
            'a' => array(
                'b' => true,
                'c' => 1,
                'd' => 1.5,
                'e' => 'Test',
                'f' => '<b>b</b>',
            )
        );
        $html1 = $this->object->__invoke($array, KeyEnumeration::DONT_CONVERT_HREF, false);
        $this->assertInternalType('string', $html1);

        $expected1 = '<ul class="gui_array_list">' .
            '<li class="gui_array_head" onmouseover="this.className=\'gui_array_head_open\'" onmouseout="this.className=\'gui_array_head\'">' .
            '<span class="gui_array_key">a</span><ul class="gui_array_list">' .
            '<li class="gui_array_list"><span class="gui_array_key">b:</span><span class="gui_array_value"><span class="icon_true">&nbsp;</span></span></li>' .
            '<li class="gui_array_list"><span class="gui_array_key">c:</span><span class="gui_array_value">1</span></li>' .
            '<li class="gui_array_list"><span class="gui_array_key">d:</span><span class="gui_array_value">1.5</span></li>' .
            '<li class="gui_array_list"><span class="gui_array_key">e:</span><span class="gui_array_value">Test</span></li>' .
            '<li class="gui_array_list"><span class="gui_array_key">f:</span><span class="gui_array_value">&lt;b&gt;b&lt;/b&gt;</span></li>' .
            '</ul></li></ul>';
        $this->assertSame($expected1, $html1);

        $html2 = $this->object->__invoke($array, KeyEnumeration::CONVERT_HREF, true);
        $this->assertInternalType('string', $html2);

        $expected2 = '<ul class="gui_array_list">' .
            '<li class="gui_array_head" onmouseover="this.className=\'gui_array_head_open\'" onmouseout="this.className=\'gui_array_head\'">' .
            '<span class="gui_array_key">a</span><ul class="gui_array_list">' .
            '<li class="gui_array_list"><a href="b"><span class="gui_array_value"><span class="icon_true">&nbsp;</span></span></a></li>' .
            '<li class="gui_array_list"><a href="c"><span class="gui_array_value">1</span></a></li>' .
            '<li class="gui_array_list"><a href="d"><span class="gui_array_value">1.5</span></a></li>' .
            '<li class="gui_array_list"><a href="e"><span class="gui_array_value">Test</span></a></li>' .
            '<li class="gui_array_list"><a href="f"><span class="gui_array_value"><b>b</b></span></a></li>' .
            '</ul></li></ul>';
        $this->assertSame($expected2, $html2);
    }

}
