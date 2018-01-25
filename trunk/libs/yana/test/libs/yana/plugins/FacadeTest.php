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
        $this->object = \Yana\Plugins\Facade::getInstance();
    }

    private function _buildDependencies()
    {
        $dependencies = new \Yana\Plugins\Dependencies\Container(new \Yana\Security\Sessions\NullWrapper(), array());
        $this->object->attachDependencies($dependencies);
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
        $this->_buildDependencies();
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
    public function testSetPluginDirectory()
    {
        $configDirectory = new \Yana\Files\Dir(__DIR__);
        $this->assertNull(\Yana\Plugins\Facade::setPluginDirectory($configDirectory));
        $this->assertSame($configDirectory, $this->object->getPluginDirectory());
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
     * @todo   Implement testSendEvent().
     */
    public function testSendEvent()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
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
