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

namespace Yana\Plugins\Loaders;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyLoader extends \Yana\Plugins\Loaders\AbstractPluginLoader
{
    /**
     * 
     * @param type $name
     */
    public function loadPlugin($name)
    {
        return new \Yana\Plugins\NullPlugin();
    }

}

/**
 * @package  test
 * @ignore
 */
class MyExceptionLoader extends \Yana\Plugins\Loaders\AbstractPluginLoader
{
    /**
     * 
     * @param type $name
     */
    public function loadPlugin($name)
    {
        throw new \Yana\Core\Exceptions\NotFoundException();
    }

}

/**
 * @package  test
 * @ignore
 */
class AbstractLoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AbstractPluginLoader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $appContainer = new \Yana\Core\Dependencies\Container(new \Yana\Util\Xml\Object());
        $container = new \Yana\Plugins\Dependencies\PluginContainer(new \Yana\Application($appContainer), new \Yana\Security\Sessions\NullWrapper());
        $this->object = new \Yana\Plugins\Loaders\MyLoader(new \Yana\Files\Dir(CWD . '/resources'), $container);
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
    public function testIsLoaded()
    {
        $this->assertFalse($this->object->isLoaded('test'));
    }

    /**
     * @test
     */
    public function testIsInstalled()
    {
        $this->assertFalse($this->object->isInstalled('test'));
    }

    /**
     * @test
     */
    public function testLoadPlugins()
    {
        $plugins = $this->object->loadPlugins(array('test1', 'test2', 'test1'));
        $expected = new \Yana\Plugins\Collection();
        $expected->setItems(array('test1' => new \Yana\Plugins\NullPlugin(), 'test2' => new \Yana\Plugins\NullPlugin()));
        $this->assertEquals($expected, $plugins);
        $this->assertTrue($this->object->isLoaded('test1'));
        $this->assertTrue($this->object->isInstalled('test1'));
    }

    /**
     * @test
     */
    public function testLoadPluginsWithException()
    {
        $appContainer = new \Yana\Core\Dependencies\Container(new \Yana\Util\Xml\Object());
        $container = new \Yana\Plugins\Dependencies\PluginContainer(new \Yana\Application($appContainer), new \Yana\Security\Sessions\NullWrapper());
        $this->object = new \Yana\Plugins\Loaders\MyExceptionLoader(new \Yana\Files\Dir(CWD . '/resources'), $container);

        $plugins = $this->object->loadPlugins(array('test1', 'test2', 'test1'));
        $expected = new \Yana\Plugins\Collection();
        $this->assertEquals($expected, $plugins);
        $this->assertTrue($this->object->isLoaded('test1'));
        $this->assertFalse($this->object->isInstalled('test1'));
    }

    /**
     * @test
     */
    public function testUnserialize()
    {
        $this->assertNull($this->object->unserialize($this->object->serialize()));
    }

    /**
     * @test
     */
    public function testSerialize()
    {
        $serialized = $this->object->serialize();
        $this->assertInternalType('string', $serialized);
        $unserialized = \unserialize($serialized);
        $this->assertTrue($unserialized[0] instanceof \Yana\Files\IsDir);
        $this->assertTrue($unserialized[1] instanceof \Yana\Plugins\Dependencies\IsPluginContainer);
    }

}
