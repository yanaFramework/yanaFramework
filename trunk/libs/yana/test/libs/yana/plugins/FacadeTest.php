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

namespace Yana\Plugins;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class FacadeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Facade
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Facade($this->_buildDependencies());
    }

    private function _buildDependencies()
    {
        return new \Yana\Plugins\Dependencies\Container(new \Yana\Security\Sessions\NullWrapper(), array());
    }

    private function _builApplication()
    {
        $configurationFactory = new \Yana\ConfigurationFactory();
        $configuration = $configurationFactory->loadConfiguration(CWD . 'resources/system.config.xml');
        $configuration->configdrive = YANA_INSTALL_DIR . 'config/system.drive.xml';
        $container = new \Yana\Core\Dependencies\Container($configuration);
        return new \Yana\Application($container);
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
     * @runInSeparateProcess
     */
    public function testSendEvent()
    {
        $this->assertNull($this->object->sendEvent("sitemap", array(), $this->_builApplication()));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidActionException
     */
    public function testSendEventInvalidActionException()
    {
        $this->object->sendEvent("", array(), $this->_builApplication());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testGetLastEvent()
    {
        $this->assertSame("", $this->object->getLastEvent());
    }

    /**
     * @test
     */
    public function testIsActive()
    {
        $this->assertFalse($this->object->isActive('noplugin'));
    }

    /**
     * @test
     */
    public function testActivate()
    {
        $this->object->deactivate('helloworld');
        $this->assertTrue($this->object->activate('helloworld')->isActive('helloworld'));
        $this->assertTrue($this->object->activate('no-such-plugin')->isActive('no-such-plugin'));
    }

    /**
     * @test
     */
    public function testDeactivate()
    {
        $this->object->activate('helloworld');
        $this->assertFalse($this->object->deactivate('helloworld')->isActive('helloworld'));
        $this->assertFalse($this->object->activate('no-such-plugin')->deactivate('no-such-plugin')->isActive('no-such-plugin'));
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testSetPluginDirectory()
    {
        $configDirectory = new \Yana\Files\Dir(CWD . '/resources/plugins/');
        $this->assertNull(\Yana\Plugins\Facade::setPluginDirectory($configDirectory));
        $this->assertSame($configDirectory, $this->object->getPluginDirectory());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testSetPluginDirectoryNotFoundException()
    {
        $configDirectory = new \Yana\Files\Dir('no-such-directory');
        \Yana\Plugins\Facade::setPluginDirectory($configDirectory);
    }

    /**
     * @test
     */
    public function testGetPluginConfigurations()
    {
        $configs = $this->object->getPluginConfigurations();
        $this->assertTrue($configs instanceof \Yana\Plugins\Configs\IsClassCollection);
        $this->assertGreaterThan(0, $configs->count());
    }

    /**
     * @test
     */
    public function testGetLastResult()
    {
        $this->assertNull($this->object->getLastResult());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testGetFirstEvent()
    {
        $this->assertSame("", $this->object->getFirstEvent());
    }

    /**
     * @test
     */
    public function testGetNextEvent()
    {
        $this->assertNull($this->object->getNextEvent());
    }

    /**
     * @test
     */
    public function testIsActiveByDefault()
    {
        $this->assertFalse($this->object->isActiveByDefault("no-such-plugin"));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function test__getNotFoundException()
    {
        $this->object->__get('no such resource');
    }

    /**
     * @test
     */
    public function test__toString()
    {
        $this->assertRegExp('/(Plugin "\w+":\s+(- \w+ = \w+\s*)+)+/s', $this->object->__toString());
    }

    /**
     * @test
     */
    public function testGetPluginDirectory()
    {
        $this->assertStringEndsWith('plugins/', $this->object->getPluginDirectory()->getPath());
    }

    /**
     * @test
     */
    public function testGetPluginConfiguration()
    {
        $pluginName = 'no_such_plugin';
        $className = \Yana\Plugins\PluginNameMapper::toClassNameWithNamespace($pluginName);
        $this->assertEquals(new \Yana\Plugins\Configs\ClassConfiguration($className), $this->object->getPluginConfiguration($pluginName));
        $this->assertSame('config', $this->object->getPluginConfiguration('config')->getId());
    }

    /**
     * @test
     */
    public function testGetPluginNames()
    {
        $this->assertNotEmpty($this->object->getPluginNames());

        $previousKey = 0;
        foreach ($this->object->getPluginNames() as $key => $name)
        {
            $this->assertInternalType('int', $key);
            $this->assertInternalType('string', $name);
            $this->assertNotEmpty($name);
            $this->assertSame($previousKey++, $key);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testGetEventType()
    {
        $this->assertSame('default', $this->object->getEventType());
        $this->assertSame('default', $this->object->getEventType('no such event'));
        $this->assertSame('config', $this->object->getEventType('index'));

        $defaultEvent = array(
            \Yana\Plugins\Annotations\Enumeration::TYPE => 'Test'
        );
        $dependencies = new \Yana\Plugins\Dependencies\Container(new \Yana\Security\Sessions\NullWrapper(), $defaultEvent);
        $this->object = new \Yana\Plugins\Facade($dependencies);
        $this->assertSame('Test', $this->object->getEventType('no such event'));
    }

    /**
     * @test
     */
    public function testGetEventConfiguration()
    {
        $this->assertNull($this->object->getEventConfiguration('no such event'));
    }

    /**
     * @test
     */
    public function testGetEventConfigurations()
    {
        $this->assertTrue($this->object->getEventConfigurations() instanceof \Yana\Plugins\Configs\MethodCollection);
        $this->assertNotEmpty($this->object->getEventConfigurations());

        foreach ($this->object->getEventConfigurations() as $key => $method)
        {
            $this->assertInternalType('string', $key);
            $this->assertNotEmpty($key);
            $this->assertTrue($method instanceof \Yana\Plugins\Configs\IsMethodConfiguration);
        }
    }

    /**
     * @test
     */
    public function testIsEvent()
    {
        $this->assertFalse($this->object->isEvent('No-such-event'));
    }

    /**
     * @test
     */
    public function testIsLoaded()
    {
        $this->assertFalse($this->object->isLoaded('No-such-plugin'));
    }

    /**
     * @test
     */
    public function testGetReport()
    {
        $this->assertTrue($this->object instanceof \Yana\Report\IsReportable);
        $this->assertTrue($this->object->getReport() instanceof \Yana\Report\IsReport);
    }

    /**
     * @test
     */
    public function testRefreshPluginFile()
    {
        $repository = $this->object->rebuildPluginRepository();
        $this->assertSame($this->object->getPluginConfigurations(), $repository->getPlugins());
        $this->assertSame($this->object->getEventConfigurations(), $repository->getEvents());
    }

}

?>