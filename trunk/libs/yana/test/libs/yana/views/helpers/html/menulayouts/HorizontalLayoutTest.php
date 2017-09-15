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
class HorizontalLayoutTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var HorizontalLayout
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new HorizontalLayout;
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

        $expected1 = '<ul class="hmenu"><li onmouseover="yanaHMenu(this,true)" onmouseout="yanaHMenu(this,false)" class="hmenu">' .
            '<div class="menu_head">a</div>' .
            '<ul class="hmenu">' .
            '<li class="entry"><span class="gui_array_key">b:</span><span class="gui_array_value"><span class="icon_true">&nbsp;</span></span></li>' .
            '<li class="entry"><span class="gui_array_key">c:</span><span class="gui_array_value">1</span></li>' .
            '<li class="entry"><span class="gui_array_key">d:</span><span class="gui_array_value">1.5</span></li>' .
            '<li class="entry"><span class="gui_array_key">e:</span><span class="gui_array_value">Test</span></li>' .
            '<li class="entry"><span class="gui_array_key">f:</span><span class="gui_array_value">&lt;b&gt;b&lt;/b&gt;</span></li>' .
            '</ul></li></ul>';
        $this->assertSame($expected1, $html1);

        $html2 = $this->object->__invoke($array, KeyEnumeration::CONVERT_HREF, true);
        $this->assertInternalType('string', $html2);

        $expected2 = '<ul class="hmenu"><li onmouseover="yanaHMenu(this,true)" onmouseout="yanaHMenu(this,false)" class="hmenu">' .
            '<div class="menu_head">a</div>' .
            '<ul class="hmenu">' .
            '<li class="entry"><a href="b"><span class="icon_true">&nbsp;</span></a></li>' .
            '<li class="entry"><a href="c">1</a></li>' .
            '<li class="entry"><a href="d">1.5</a></li>' .
            '<li class="entry"><a href="e">Test</a></li>' .
            '<li class="entry"><a href="f"><b>b</b></a></li>' .
            '</ul></li></ul>';
        $this->assertSame($expected2, $html2);
    }

}
