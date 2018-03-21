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

namespace Yana\Plugins\Events;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyDispatcher extends \Yana\Plugins\Events\Dispatcher
{

    /**
     * Always returns bool(false).
     *
     * @param   \Yana\IsPlugin                               $subscriber  implements event handler
     * @param   \Yana\Plugins\Configs\IsMethodConfiguration  $event       describes the call interface of the event
     * @return  bool
     */
    protected function _sendEvent(\Yana\IsPlugin $subscriber, \Yana\Plugins\Configs\IsMethodConfiguration $event)
    {
        return false;
    }
    
}

/**
 * @package  test
 * @ignore
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Events\NullDispatcher
     */
    protected $object;

    /**
     * @var \Yana\Plugins\Events\MyDispatcher
     */
    protected $objectNegative;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Events\NullDispatcher();
        $this->objectNegative = new \Yana\Plugins\Events\MyDispatcher();
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
    public function testSendEventEmpty()
    {
        $this->assertTrue($this->object->sendEvent(new \Yana\Plugins\Collection(), new \Yana\Plugins\Configs\MethodConfiguration()));
    }

    /**
     * @test
     */
    public function testSendEvent()
    {
        $collection = new \Yana\Plugins\Collection();
        $collection[] = new \Yana\Plugins\NullPlugin();
        $event = new \Yana\Plugins\Configs\MethodConfiguration();
        $event->setMethodName('Test');
        $this->assertTrue($this->object->sendEvent($collection, $event));
        $this->assertFalse($this->objectNegative->sendEvent($collection, $event));
    }

    /**
     * @test
     */
    public function testGetLastResult()
    {
        $this->assertNull($this->object->getLastResult());

        $collection = new \Yana\Plugins\Collection();
        $collection[] = new \Yana\Plugins\NullPlugin();
        $event = new \Yana\Plugins\Configs\MethodConfiguration();
        $event->setMethodName('Test');
        $this->object->sendEvent($collection, $event);
        $this->objectNegative->sendEvent($collection, $event);
        $this->assertTrue($this->object->getLastResult());
        $this->assertFalse($this->objectNegative->getLastResult());
    }

    /**
     * @test
     */
    public function testGetLastEvent()
    {
        $this->assertSame("", $this->object->getLastEvent());

        $collection = new \Yana\Plugins\Collection();
        $collection[] = new \Yana\Plugins\NullPlugin();
        $event = new \Yana\Plugins\Configs\MethodConfiguration();
        $event->setMethodName('Test');
        $this->object->sendEvent($collection, $event);
        $this->objectNegative->sendEvent($collection, $event);
        $event->setMethodName('Test2');
        $this->object->sendEvent($collection, $event);
        $this->objectNegative->sendEvent($collection, $event);
        $this->assertSame("Test2", $this->object->getLastEvent());
        $this->assertSame("Test2", $this->objectNegative->getLastEvent());
    }

    /**
     * @test
     */
    public function testGetFirstEvent()
    {
        $this->assertSame("", $this->object->getFirstEvent());

        $collection = new \Yana\Plugins\Collection();
        $collection[] = new \Yana\Plugins\NullPlugin();
        $event = new \Yana\Plugins\Configs\MethodConfiguration();
        $event->setMethodName('Test');
        $this->object->sendEvent($collection, $event);
        $this->objectNegative->sendEvent($collection, $event);
        $event->setMethodName('Test2');
        $this->object->sendEvent($collection, $event);
        $this->objectNegative->sendEvent($collection, $event);
        $this->assertSame("Test", $this->object->getFirstEvent());
        $this->assertSame("Test", $this->objectNegative->getFirstEvent());
    }

    /**
     * @test
     */
    public function testSerialize()
    {
        $newInstance = \unserialize(\serialize($this->object));
        $this->assertEquals(new \Yana\Plugins\Events\NullDispatcher(), $newInstance);
    }

}
