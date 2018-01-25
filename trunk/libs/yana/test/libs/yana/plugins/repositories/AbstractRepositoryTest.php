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
class MyRepository extends \Yana\Plugins\Repositories\AbstractRepository
{
    public function addEvent(\Yana\Plugins\Configs\IsMethodConfiguration $method)
    {
        return $this;
    }

    public function addPlugin(\Yana\Plugins\Configs\ClassConfiguration $plugin)
    {
        return $this;
    }

    public function getSubscribers($eventName)
    {
        return array();
    }

    public function isEvent($method)
    {
        return true;
    }

    public function isPlugin($plugin)
    {
        return true;
    }

    public function subscribe(\Yana\Plugins\Configs\IsMethodConfiguration $event, \Yana\Plugins\Configs\IsClassConfiguration $subscriber)
    {
        return $this;
    }

    public function unsubscribe(\Yana\Plugins\Configs\IsMethodConfiguration $event, $subscriberId)
    {
        return $this;
    }

    public function getQueues()
    {
        return $this->_getQueues();
    }

    public function getQueue($name)
    {
        return $this->_getQueue($name);
    }
}

/**
 * @package  test
 * @ignore
 */
class AbstractRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var MyRepository
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Repositories\MyRepository();
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
    public function testGetPlugins()
    {
        $this->assertTrue($this->object->getPlugins() instanceof \Yana\Plugins\Configs\IsClassCollection);
    }

    /**
     * @test
     */
    public function testGetEvents()
    {
        $this->assertTrue($this->object->getEvents() instanceof \Yana\Plugins\Configs\MethodCollection);
    }

    /**
     * @test
     */
    public function testGetQueues()
    {
        $this->assertTrue($this->object->getQueues() instanceof \Yana\Plugins\Subscriptions\QueueCollection);
    }

    /**
     * @test
     */
    public function testGetQueue()
    {
        $queue = $this->object->getQueue("test");
        $this->assertTrue($queue instanceof \Yana\Plugins\Subscriptions\Queue);
    }

//    /**
//     * @test
//     */
//    public function testSerialize()
//    {
//        $this->assertInternalType('string', $this->object->serialize());
//    }
//
//    /**
//     * @test
//     */
//    public function testUnserialize()
//    {
//        $object = \unserialize(\serialize($this->object));
//        $this->assertEquals($object, $this->object);
//    }

}
