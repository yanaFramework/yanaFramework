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

namespace Yana\Views\Helpers\Blockfunctions;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';

/**
 * @package  test
 */
class LoopBlockTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\Blockfunctions\LoopBlock
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Views\Helpers\Blockfunctions\LoopBlock();
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
        $array = array('a' => 1, 'b' => array('foo', 'bar'), 'c' => 2);
        $content = '$key = $element' . "\n";
        $smarty = new \Smarty(); // will be ignored
        $template = new \Smarty_Internal_Template('test', $smarty); // will be ignored
        $param = array(
            'from' => $array,
            'key' => 'key',
            'item' => 'element'
        );
        /**
         * Smarty calls this function twice. Once for the opening tag, and once for the closing tag.
         * On the first call, $repeat is TRUE and $content is NULL, because Smarty hasn't seen the content of the loop-body yet.
         * Also, Smarty doesn't expect any output on the first call, so we are expected to return NULL.
         * On the second call, $repeat is FALSE and $content is the loop body.
         * We simulate that!
         */
        $repeat = true;
        // Opening tag
        $this->assertNull($this->object->__invoke($param, null, $template, $repeat));
        $repeat = false;
        // Closing tag
        $this->assertSame("a = 1\nb.0 = foo\nb.1 = bar\nc = 2\n", $this->object->__invoke($param, $content, $template, $repeat));
    }

}
