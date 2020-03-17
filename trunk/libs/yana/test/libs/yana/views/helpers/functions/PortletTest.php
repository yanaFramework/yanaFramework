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

namespace Yana\Views\Helpers\Functions;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';

/**
 * @package  test
 */
class PortletTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Dependencies\IsViewContainer
     */
    protected $container;

    /**
     * @var \Yana\Views\Helpers\Functions\Portlet
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
        $this->container = new \Yana\Core\Dependencies\Container($configuration);
        $this->object = new \Yana\Views\Helpers\Functions\Portlet($this->container);
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
        $params = array('action' => 'myAction', 'id' => 'myId', 'title' => 'myTitle', 'args' => 'myArgs');
        $template = new \Smarty_Internal_Template("name", new \Smarty());
        $string = $this->object->__invoke($params, $template);
        $url = (new \Yana\Views\Helpers\Formatters\UrlFormatter())->__invoke('action=myAction');
        $expected = "<script type=\"text/javascript\">yanaPortlet('$url', 'myId', 'myArgs', 'myTitle')</script>" .
            "<noscript><iframe class=\"yana_portlet\" src=\"{$url}&amp;myArgs\"></iframe></noscript>";
        $this->assertSame($expected, $string);
    }

    /**
     * @test
     */
    public function test__invokeEmptyArgs()
    {
        $template = new \Smarty_Internal_Template("name", new \Smarty());
        $this->assertSame("", $this->object->__invoke(array(), $template));
    }

    /**
     * @test
     */
    public function test__invokeJustAction()
    {
        $params = array('action' => 'myAction');
        $template = new \Smarty_Internal_Template("name", new \Smarty());
        $string = $this->object->__invoke($params, $template);
        $url = (new \Yana\Views\Helpers\Formatters\UrlFormatter())->__invoke('action=myAction');
        $expected = "<script type=\"text/javascript\">yanaPortlet('$url', '___invoke_%s', '', '')</script>" .
            "<noscript><iframe class=\"yana_portlet\" src=\"{$url}&amp;\"></iframe></noscript>";
        $this->assertStringMatchesFormat($expected, $string);
    }

}
