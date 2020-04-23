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

namespace Yana\Views\Helpers\PreFilters;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';

/**
 * @package test
 * @ignore
 */
class IncludeFilterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\PreFilters\IncludeFilter
     */
    protected $object;

    /**
     * @var \Yana\Core\Dependencies\IsApplicationContainer
     */
    protected $container;

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
        $this->object = new \Yana\Views\Helpers\PreFilters\IncludeFilter($this->container);
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
    public function test__invokeEmpty()
    {
        $template = new \Smarty_Internal_Template("name", new \Smarty());
        $template->assign('FILE_IS_INCLUDE', true);
        $this->assertSame("", $this->object->__invoke("", $template));
    }

    /**
     * @test
     */
    public function test__invokeWithBodyTag()
    {
        $template = new \Smarty_Internal_Template("name", new \Smarty());
        $template->assign('FILE_IS_INCLUDE', true);
        $this->assertSame("<div>b</div>", $this->object->__invoke("a<body>b</body>c", $template));
    }

    /**
     * @test
     */
    public function test__invokeWithAttributes()
    {
        $template = new \Smarty_Internal_Template("name", new \Smarty());
        $template->assign('FILE_IS_INCLUDE', true);
        $this->assertSame("<div attr>b</div>", $this->object->__invoke("a<body attr>b</body>c", $template));
    }

}
