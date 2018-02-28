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
        $this->object->deactivate('helloworld');
        $this->assertTrue($this->object->activate('helloworld')->isActive('helloworld'));
    }

    /**
     * @test
     */
    public function testDeactivate()
    {
        $this->object->activate('helloworld');
        $this->assertFalse($this->object->deactivate('helloworld')->isActive('helloworld'));
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
     */
    public function testGetLastEvent()
    {
        $this->assertSame("", $this->object->getLastEvent());
    }

    /**
     * @test
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
     * @todo   Implement testSendEvent().
     */
    public function testSendEvent()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}

?>
