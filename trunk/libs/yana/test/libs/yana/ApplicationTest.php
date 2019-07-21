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

namespace Yana;

/**
 * @ignore
 */
require_once __DIR__ . '/../../include.php';

/**
 * Test implementation
 *
 * @package  test
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Dependencies\IsApplicationContainer
     */
    protected $container;

    /**
     * @var \Yana\Application
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
        $this->object = new \Yana\Application($this->container);
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
    public function testGetReport()
    {
        $report = $this->object->getReport();
        $this->assertTrue($report instanceof \Yana\Report\IsReport);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testConnectNotFoundException()
    {
        $this->object->connect('no-such-database');
    }

    /**
     * @test
     */
    public function testConnect()
    {
        $this->assertTrue($this->object->connect('user') instanceof \Yana\Db\IsConnection);
    }

    /**
     * @test
     */
    public function testGetCache()
    {
        $this->assertSame($this->container->getCache(), $this->object->getCache());
    }

    /**
     * @test
     */
    public function testExecute()
    {
        $this->assertTrue($this->object->execute());
    }

    /**
     * @test
     */
    public function testGetSecurity()
    {
        $this->assertSame($this->container->getSecurity(), $this->object->getSecurity());
    }

    /**
     * @test
     */
    public function testGetRegistry()
    {
        $this->assertSame($this->container->getRegistry(), $this->object->getRegistry());
    }

    /**
     * @test
     */
    public function testGetPlugins()
    {
        $this->assertSame($this->container->getPlugins(), $this->object->getPlugins());
    }

    /**
     * @test
     */
    public function testGetView()
    {
        $this->assertSame($this->container->getView(), $this->object->getView());
    }

    /**
     * @test
     */
    public function testGetLanguage()
    {
        $this->assertSame($this->container->getLanguage(), $this->object->getLanguage());
    }

    /**
     * @test
     */
    public function testGetSkin()
    {
        $this->assertSame($this->container->getSkin(), $this->object->getSkin());
    }

    /**
     * @test
     */
    public function testGetProfileId()
    {
        $this->assertSame($this->container->getProfileId(), $this->object->getProfileId());
    }

    /**
     * @test
     */
    public function testGetRequest()
    {
        $this->assertSame($this->container->getRequest(), $this->object->getRequest());
    }

    /**
     * @test
     */
    public function testIsVar()
    {
        $this->assertFalse($this->object->isVar('Test'));
        $this->assertTrue($this->object->setVar('Test', false)->isVar('Test'));
    }

    /**
     * @test
     */
    public function testGetVar()
    {
        $this->assertNull($this->object->getVar('Test'));
    }

    /**
     * @test
     */
    public function testGetVars()
    {
        $this->assertInternalType('array', $this->object->getVars());
        $this->assertArrayHasKey('DEFAULT', $this->object->getVars());
    }

    /**
     * @test
     */
    public function testSetVarByReference()
    {
        $test = true;
        $test = $this->object->setVarByReference('test', $test)->getVar('test');
        $test = false;
        $this->assertFalse($this->object->getVar('test'));
    }

    /**
     * @test
     */
    public function testSetVarsByReference()
    {
        $test = array(1 => 1, 2 => 2);
        $test = $this->object->setVarsByReference($test)->getVars();
        $test[3] = 3;
        $this->assertEquals($test, $this->object->getVars());
    }

    /**
     * @test
     */
    public function testSetVar()
    {
        $this->assertSame(123, $this->object->setVar('Test', 123)->getVar('Test'));
    }

    /**
     * @test
     */
    public function testSetVars()
    {
        $vars = array('Test1' => 123, 'Test2' => 456);
        $this->assertSame($vars, $this->object->setVars($vars)->getVars());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetResourceNotFoundException()
    {
        $this->object->getResource('no.such.resource');
    }

    /**
     * @test
     */
    public function testGetResource()
    {
        $this->assertTrue($this->object->getResource('system:/config') instanceof \Yana\Files\Dir);
    }

    /**
     * @covers Yana\Application::exitTo
     * @todo   Implement testExitTo().
     */
    public function testExitTo()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Application::outputResults
     * @todo   Implement testOutputResults().
     */
    public function testOutputResults()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @test
     */
    public function testGetDefault()
    {
        $this->assertNull($this->object->getDefault('no-such-default'));
        $this->assertSame('default', $this->object->getDefault('profile'));
    }

    /**
     * @covers Yana\Application::clearCache
     * @todo   Implement testClearCache().
     */
    public function testClearCache()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @test
     */
    public function testGetLogger()
    {
        $this->assertSame($this->container->getLogger(), $this->object->getLogger());
    }

    /**
     * @covers Yana\Application::refreshSettings
     * @todo   Implement testRefreshSettings().
     */
    public function testRefreshSettings()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @test
     */
    public function testBuildApplicationMenu()
    {
        $this->assertEquals($this->object->buildApplicationMenu(), $this->container->getMenuBuilder()->buildMenu());
    }

}
