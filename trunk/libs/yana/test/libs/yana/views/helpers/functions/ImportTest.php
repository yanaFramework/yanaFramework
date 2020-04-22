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
class ImportTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Dependencies\IsApplicationContainer
     */
    protected $container;

    /**
     * @var \Yana\Views\Helpers\Functions\Import
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
        $this->container = new \Yana\Core\Dependencies\Container($configuration);
        $this->object = new \Yana\Views\Helpers\Functions\Import($this->container);
        $view = $this->container->getView();
        $view->setModifier('replaceToken', function ($token) { return $token; });
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $view = $this->container->getView();
        $view->unsetModifier('replaceToken');
    }

    /**
     * @covers Yana\Views\Helpers\Functions\Import::__invoke
     * @test
     */
    public function test__invoke()
    {
        $this->assertSame("", $this->object->__invoke(array(), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @covers Yana\Views\Helpers\Functions\Import::__invoke
     * @test
     */
    public function test__invokeWithFile()
    {
        $fileName = \CWD . '/resources/importtest.txt';
        $expected = \str_replace(array('{$FILE_IS_INCLUDE}', '{$a}'), array('1', ''), file_get_contents($fileName));
        $this->assertEquals($expected, $this->object->__invoke(array("file" => $fileName), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @covers Yana\Views\Helpers\Functions\Import::__invoke
     * @test
     */
    public function test__invokeWithTemplate()
    {
        $fileName = \CWD . '/resources/importtest.txt';
        $expected = \str_replace(array('{$FILE_IS_INCLUDE}', '{$a}'), array('1', ''), file_get_contents($fileName));
        $this->assertEquals($expected, $this->object->__invoke(array("template" => $fileName), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @covers Yana\Views\Helpers\Functions\Import::__invoke
     * @test
     */
    public function test__invokeWithParams()
    {
        $fileName = \CWD . '/resources/importtest.txt';
        $expected = \str_replace(array('{$FILE_IS_INCLUDE}', '{$a}'), array('1', 'Test!'), file_get_contents($fileName));
        $this->assertEquals($expected, $this->object->__invoke(array("template" => $fileName, 'a' => 'Test!'), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @covers Yana\Views\Helpers\Functions\Import::__invoke
     * @test
     * @expectedException \SmartyException
     */
    public function test__invokeWithId()
    {
        $this->object->__invoke(array("id" => "test"), new \Smarty_Internal_Template("name", new \Smarty()));
    }

}
