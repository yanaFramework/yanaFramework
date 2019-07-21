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
namespace Yana\Plugins\Dependencies;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class MenuContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Dependencies\IsApplicationContainer
     */
    protected $container;

    /**
     * @var \Yana\Plugins\Dependencies\MenuContainer
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
        $this->object = new \Yana\Plugins\Dependencies\MenuContainer($this->container);
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
    public function testGetTranslationFacade()
    {
        $this->assertSame($this->container->getLanguage(), $this->object->getTranslationFacade());
    }

    /**
     * @test
     */
    public function testGetSecurityFacade()
    {
        $this->assertSame($this->container->getSecurity(), $this->object->getSecurityFacade());
    }

    /**
     * @test
     */
    public function testIsDefaultProfile()
    {
        $this->assertTrue($this->object->isDefaultProfile());
    }

    /**
     * @test
     */
    public function testGetPluginFacade()
    {
        $this->assertSame($this->container->getPlugins(), $this->object->getPluginFacade());
    }

    /**
     * @test
     */
    public function testGetUrlFormatter()
    {
        $this->assertEquals(new \Yana\Views\Helpers\Formatters\UrlFormatter(), $this->object->getUrlFormatter());
    }

}
