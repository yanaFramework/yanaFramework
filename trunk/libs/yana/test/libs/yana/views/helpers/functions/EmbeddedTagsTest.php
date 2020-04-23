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
 * @covers \Yana\Views\Helpers\Functions\EmbeddedTags
 */
class EmbeddedTagsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Dependencies\IsApplicationContainer
     */
    protected $container;

    /**
     * @var \Yana\Views\Helpers\Functions\EmbeddedTags
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
        $this->object = new \Yana\Views\Helpers\Functions\EmbeddedTags($this->container);
        $view = $this->container->getView();
        $view->unsetModifier('replaceToken');
        $view->setModifier('replaceToken', function ($token) { return $token; });
        $this->container->getRegistry()->setVar('PROFILE.EMBTAG', array('x', 'y', 'z'));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $view = $this->container->getView();
        $view->unsetModifier('replaceToken');
        $view->clearCache();
    }

    /**
     * @test
     */
    public function testGetTemplateName()
    {
        $this->assertSame("id:GUI_EMBEDDED_TAGS", $this->object->getTemplateName());
    }

    /**
     * @test
     */
    public function testSetTemplateName()
    {
        $this->assertSame("Test!", $this->object->setTemplateName("Test!")->getTemplateName());
    }

    /**
     * @test
     */
    public function test__invoke()
    {
        $template = "string:{foreach from=\$TAGS item=t}{\$t},{/foreach}|{foreach from=\$USER_DEFINED item=u}{\$u},{/foreach}";
        $this->object->setTemplateName($template);
        $this->assertSame("b,i,u,h,emp,c,small,big,hide,code,img,url,mail,color,mark,smilies,|x,y,z,", $this->object->__invoke(array(), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @test
     */
    public function test__invokeWithShow()
    {
        $template = "string:{foreach from=\$TAGS item=t}{\$t},{/foreach}|{foreach from=\$USER_DEFINED item=u}{\$u},{/foreach}";
        $this->object->setTemplateName($template);
        $expected = "a,b,c,|x,y,z,";
        $this->assertSame($expected, $this->object->__invoke(array("show" => "a,b,c"), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @test
     */
    public function test__invokeWithInvalidShow()
    {
        $template = "string:{foreach from=\$TAGS item=t}{\$t},{/foreach}|{foreach from=\$USER_DEFINED item=u}{\$u},{/foreach}";
        $this->object->setTemplateName($template);
        $expected = "";
        $this->assertSame($expected, $this->object->__invoke(array("show" => ".."), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @test
     */
    public function test__invokeWithHide()
    {
        $template = "string:{foreach from=\$TAGS item=t}{\$t},{/foreach}|{foreach from=\$USER_DEFINED item=u}{\$u},{/foreach}";
        $this->object->setTemplateName($template);
        $expected = "i,u,h,emp,small,big,hide,code,img,url,mail,color,mark,smilies,|x,y,z,";
        $this->assertSame($expected, $this->object->__invoke(array("hide" => "a,b,c"), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @test
     */
    public function test__invokeWithInvalidHide()
    {
        $template = "string:{foreach from=\$TAGS item=t}{\$t},{/foreach}|{foreach from=\$USER_DEFINED item=u}{\$u},{/foreach}";
        $this->object->setTemplateName($template);
        $expected = "";
        $this->assertSame($expected, $this->object->__invoke(array("hide" => ".."), new \Smarty_Internal_Template("name", new \Smarty())));
    }

    /**
     * @test
     */
    public function test__invokeWithShowAndHide()
    {
        $template = "string:{foreach from=\$TAGS item=t}{\$t},{/foreach}|{foreach from=\$USER_DEFINED item=u}{\$u},{/foreach}";
        $this->object->setTemplateName($template);
        $expected = "a,c,|x,y,z,";
        $this->assertSame($expected, $this->object->__invoke(array("show" => "a,b,c", "hide" => "b,e,f"), new \Smarty_Internal_Template("name", new \Smarty())));
    }

}
