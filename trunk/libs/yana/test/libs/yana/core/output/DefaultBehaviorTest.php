<?php
/**
 * YANA library
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

namespace Yana\Core\Output;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyDefaultBehavior extends \Yana\Core\Output\DefaultBehavior
{
    public $printed = "";
    public $template = null;
    protected function _printTemplate(\Yana\Views\Templates\IsTemplate $template)
    {
        $this->template = $template;
    }
    protected function _printText($text)
    {
        $this->printed = $text;
    }

}

/**
 * @package  test
 */
class DefaultBehaviorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Dependencies\IsApplicationContainer
     */
    protected $container;

    /**
     * @var \Yana\Core\Output\MyDefaultBehavior
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $configurationFactory = new \Yana\ConfigurationFactory();
        $configuration = $configurationFactory->loadConfiguration(CWD . 'resources/system.config.xml');
        $configuration->configdrive = YANA_INSTALL_DIR . 'config/system.drive.xml';
        $this->container = new \Yana\Core\Dependencies\Container($configuration);
        $this->object = new \Yana\Core\Output\MyDefaultBehavior($this->container);
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
    public function testOutputAsJson()
    {
        $this->assertNull($this->object->outputAsJson(array(123)));
        $this->assertSame('[123]', $this->object->printed);
    }

    /**
     * @test
     */
    public function testOutputAsMessage()
    {
        $this->container->getLanguage()->getTranslations()->setLoaded('message');
        $this->assertSame($this->container->getDefault('homepage'), $this->object->outputAsMessage());
    }

    /**
     * @test
     */
    public function testOutputAsTemplate()
    {
        $this->container->getLanguage()->getTranslations()->setLoaded('message');
        $this->assertNull($this->object->outputAsTemplate('sitemap'));
        $this->assertNotEmpty($this->object->template);
        $this->assertTrue($this->object->template->getVar('STDOUT') instanceof \Yana\Log\ViewHelpers\MessageCollection);
    }

    /**
     * @test
     */
    public function testOutputAsTemplateStdOut()
    {
        $_SESSION['STDOUT'] = __FUNCTION__;
        $this->container->getLanguage()->getTranslations()->setLoaded('message');
        $this->assertNull($this->object->outputAsTemplate('sitemap'));
        $this->assertNotEmpty($this->object->template);
        $this->assertSame(__FUNCTION__, $this->object->template->getVar('STDOUT'));
    }

    /**
     * @test
     */
    public function testOutputResults()
    {
        $this->assertNull($this->object->outputResults());
    }

}
