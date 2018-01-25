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

namespace Yana\Plugins\Repositories;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Repositories\Repository
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Repositories\Repository();
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
    public function testIsPlugin()
    {
        $this->assertFalse($this->object->isPlugin("no such plugin"));
    }

    /**
     * @test
     */
    public function testAddPlugin()
    {
        $plugin = new \Yana\Plugins\Configs\ClassConfiguration();
        $plugin->setClassName('Test');
        $this->assertSame($plugin, $this->object->addPlugin($plugin)->getPlugins()->offsetGet('test'));
        $this->assertTrue($this->object->isPlugin("test"));
    }

    /**
     * @test
     */
    public function testIsEvent()
    {
        $this->assertFalse($this->object->isEvent("no such method"));
    }

    /**
     * @test
     */
    public function testAddEvent()
    {
        $method = new \Yana\Plugins\Configs\MethodConfiguration();
        $method->setMethodName('Test');
        $this->assertSame($method, $this->object->addEvent($method)->getEvents()->offsetGet('test'));
        $this->assertTrue($this->object->isEvent("test"));
    }

    /**
     * @test
     */
    public function testGetSubscribers()
    {
        $this->assertCount(0, $this->object->getSubscribers('test'));
    }

    /**
     * @test
     */
    public function testSubscribe()
    {
        $plugin = new \Yana\Plugins\Configs\ClassConfiguration();
        $plugin->setId('my_test')->setPriority(3);
        $method = new \Yana\Plugins\Configs\MethodConfiguration();
        $method->setMethodName('Test');
        $this->assertEquals(array('my_test'), $this->object->subscribe($method, $plugin)->getSubscribers('test'));
    }

    /**
     * @test
     */
    public function testUnsubscribe()
    {
        $plugin = new \Yana\Plugins\Configs\ClassConfiguration();
        $plugin->setId('my_test');
        $method = new \Yana\Plugins\Configs\MethodConfiguration();
        $method->setMethodName('Test');
        $this->assertSame(array(), $this->object->subscribe($method, $plugin)->unsubscribe($method, 'my_test')->getSubscribers('test'));
    }

}
