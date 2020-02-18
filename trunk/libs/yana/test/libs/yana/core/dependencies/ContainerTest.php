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
declare(strict_types=1);

namespace Yana\Core\Dependencies;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Dependencies\Container
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
        $this->object = new \Yana\Core\Dependencies\Container($configuration);
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
    public function testGetApplicationUrlParameters()
    {
        $this->assertSame('?id=default', $this->object->getApplicationUrlParameters());
    }

    /**
     * @test
     */
    public function testGetPluginAdapter()
    {
        $adapter = $this->object->getPluginAdapter();
        $this->assertTrue($adapter instanceof \Yana\Plugins\Data\IsAdapter);
    }

    /**
     * @test
     */
    public function testGetRequest()
    {
        $this->assertTrue($this->object->getRequest() instanceof \Yana\Http\Facade);
    }

    /**
     * @test
     */
    public function testGetCache()
    {
        $this->assertTrue($this->object->getCache() instanceof \Yana\Data\Adapters\IsDataAdapter);
    }

    /**
     * @test
     */
    public function testGetExceptionLogger()
    {
        $this->object->getRegistry()->setVar('LANGUAGEDIR', \YANA_INSTALL_DIR . $this->object->getRegistry()->getVar('LANGUAGEDIR'));
        $this->assertTrue($this->object->getExceptionLogger() instanceof \Yana\Log\IsLogger);
    }

    /**
     * @test
     */
    public function testGetSecurity()
    {
        $this->assertTrue($this->object->getSecurity() instanceof \Yana\Security\IsFacade);
    }

    /**
     * @test
     */
    public function testGetRegistry()
    {
        $this->assertTrue($this->object->getRegistry() instanceof \Yana\VDrive\IsRegistry);
    }

    /**
     * @test
     */
    public function testGetPlugins()
    {
        $this->assertTrue($this->object->getPlugins() instanceof \Yana\Plugins\Facade);
    }

    /**
     * @test
     */
    public function testGetView()
    {
        $this->assertTrue($this->object->getView() instanceof \Yana\Views\Managers\IsManager);
    }

    /**
     * @test
     */
    public function testGetLanguage()
    {
        $this->object->getRegistry()->setVar('LANGUAGEDIR', \YANA_INSTALL_DIR . $this->object->getRegistry()->getVar('LANGUAGEDIR'));
        $this->assertTrue($this->object->getLanguage() instanceof \Yana\Translations\Facade);
    }

    /**
     * @test
     */
    public function testGetSkin()
    {
        $this->assertTrue($this->object->getSkin() instanceof \Yana\Views\Skins\IsSkin);
    }

    /**
     * @test
     */
    public function testGetProfileId()
    {
        $profileId = $this->object->getProfileId();
        $this->assertSame("default", $profileId);
        $this->assertSame($profileId, $this->object->getProfileId());
    }

    /**
     * @test
     */
    public function testGetLogger()
    {
        $this->assertTrue($this->object->getLogger() instanceof \Yana\Log\IsLogHandler);
    }

    /**
     * @test
     */
    public function testGetIconLoader()
    {
        $this->assertTrue($this->object->getIconLoader() instanceof \Yana\Views\Icons\IsLoader);
    }

    /**
     * @test
     */
    public function testGetDefaultUser()
    {
        $this->assertSame($this->object->getDefault('user'), $this->object->getDefaultUser());
    }

    /**
     * @test
     */
    public function testGetDefaultUserRequirements()
    {
        $this->assertSame(array(), $this->object->getDefaultUserRequirements());
    }

    /**
     * @test
     */
    public function testGetDefaultEvent()
    {
        $this->assertSame($this->object->getDefault('event'), $this->object->getDefaultEvent());
    }

    /**
     * @test
     */
    public function testGetEventConfigurationsForPlugins()
    {
        $this->assertTrue($this->object->getEventConfigurationsForPlugins() instanceof \Yana\Plugins\Configs\MethodCollection);
    }

    /**
     * @test
     */
    public function testGetLastPluginAction()
    {
        $this->assertSame($this->object->getPlugins()->getLastEvent(), $this->object->getLastPluginAction());
    }

    /**
     * @test
     */
    public function testGetMenuBuilder()
    {
        $this->assertTrue($this->object->getMenuBuilder() instanceof \Yana\Plugins\Menus\IsCacheableBuilder);
    }

    /**
     * @test
     */
    public function testGetUrlFormatter()
    {
        $this->assertTrue($this->object->getUrlFormatter() instanceof \Yana\Views\Helpers\Formatters\UrlFormatter);
    }

}
