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
class TimeSelectorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\Functions\TimeSelector
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
        $this->object = new \Yana\Views\Helpers\Functions\TimeSelector(new \Yana\Core\Dependencies\Container($configuration));
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
    public function test__invokeWithParams()
    {
        $params = array(
            'name' => 'Name',
            'id' => 'Id',
            'attr' => 'Attr',
            'ignored' => 'this must not make a difference'
        );
        $expected = '/<select class="" id="Id_hour" name="Name\\[hour\\]" Attr>' .
            '(<option value="\\d{1,2}"( selected="selected")?>\\d\\d<\/option>){24}' .
            '<\\/select>:<select class="" id="Id_minute" name="Name\\[minute\\]" Attr>' .
            '(<option value="\\d{1,2}"( selected="selected")?>\\d\\d<\\/option>){60}<\\/select>/';
        $this->assertRegExp($expected, $this->object->__invoke($params, new \Smarty_Internal_Template("name", new \Smarty())));
    }

}
