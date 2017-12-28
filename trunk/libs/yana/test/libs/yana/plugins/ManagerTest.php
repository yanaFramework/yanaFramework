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
class ManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Manager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = \Yana\Plugins\Manager::getInstance();
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
    public function testIsActive()
    {
        $this->assertFalse($this->object->isActive('noplugin'));
    }

    /**
     * @test
     */
    public function testActivate()
    {
        $this->object->deactive('helloworld');
        $this->assertTrue($this->object->activate('helloworld')->isActive('helloworld'));
    }

    /**
     * @test
     */
    public function testDeactivate()
    {
        $this->object->activate('helloworld');
        $this->assertFalse($this->object->deactive('helloworld')->isActive('helloworld'));
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testGetDependencies()
    {
        $this->assertNull($this->object->getDependencies());
    }

    /**
     * @test
     */
    public function testAttachDependencies()
    {
        $dependencies = new \Yana\Plugins\Dependencies\Container(new \Yana\Security\Sessions\Wrapper(), array());
        $this->assertSame($dependencies, $this->object->attachDependencies($dependencies)->getDependencies());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testSetPath()
    {
        $configFile = new \Yana\Files\Text(__FILE__);
        $configDirectory = new \Yana\Files\Dir(__DIR__);
        $this->assertNull(\Yana\Plugins\Manager::setPath($configFile, $configDirectory));
        $this->assertSame($configFile, \Yana\Plugins\Manager::getConfigFilePath());
        $this->assertSame($configDirectory, $this->object->getPluginDir());
    }

    /**
     * @test
     */
    public function testGetConfigFilePath()
    {
        $this->assertTrue(\Yana\Plugins\Manager::getConfigFilePath() instanceof \Yana\Files\File);
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
     * @covers Yana\Plugins\Manager::broadcastEvent
     * @todo   Implement testBroadcastEvent().
     */
    public function testBroadcastEvent()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::getLastResult
     * @todo   Implement testGetLastResult().
     */
    public function testGetLastResult()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::getLastEvent
     * @todo   Implement testGetLastEvent().
     */
    public function testGetLastEvent()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::getFirstEvent
     * @todo   Implement testGetFirstEvent().
     */
    public function testGetFirstEvent()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::getNextEvent
     * @todo   Implement testGetNextEvent().
     */
    public function testGetNextEvent()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::refreshPluginFile
     * @todo   Implement testRefreshPluginFile().
     */
    public function testRefreshPluginFile()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::isDefaultActive
     * @todo   Implement testIsDefaultActive().
     */
    public function testIsDefaultActive()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::get
     * @todo   Implement testGet().
     */
    public function testGet()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::__get
     * @todo   Implement test__get().
     */
    public function test__get()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::isInstalled
     * @todo   Implement testIsInstalled().
     */
    public function testIsInstalled()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::__toString
     * @todo   Implement test__toString().
     */
    public function test__toString()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::getPluginDir
     * @todo   Implement testGetPluginDir().
     */
    public function testGetPluginDir()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::getPluginConfiguration
     * @todo   Implement testGetPluginConfiguration().
     */
    public function testGetPluginConfiguration()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::getPluginNames
     * @todo   Implement testGetPluginNames().
     */
    public function testGetPluginNames()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::getEventType
     * @todo   Implement testGetEventType().
     */
    public function testGetEventType()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::getEventConfiguration
     * @todo   Implement testGetEventConfiguration().
     */
    public function testGetEventConfiguration()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::getEventConfigurations
     * @todo   Implement testGetEventConfigurations().
     */
    public function testGetEventConfigurations()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::isEvent
     * @todo   Implement testIsEvent().
     */
    public function testIsEvent()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::isLoaded
     * @todo   Implement testIsLoaded().
     */
    public function testIsLoaded()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::getReport
     * @todo   Implement testGetReport().
     */
    public function testGetReport()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::__sleep
     * @todo   Implement test__sleep().
     */
    public function test__sleep()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::attachLogger
     * @todo   Implement testAttachLogger().
     */
    public function testAttachLogger()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Plugins\Manager::getLogger
     * @todo   Implement testGetLogger().
     */
    public function testGetLogger()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}

?>
