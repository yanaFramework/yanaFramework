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

namespace Yana\Plugins\Subscriptions;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class QueueTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Subscriptions\Queue
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Subscriptions\Queue();
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
    public function testGetSubscribers()
    {
        $this->assertSame(array(), $this->object->getSubscribers());
    }

    /**
     * @test
     */
    public function testSubscribe()
    {
        $class1 = new \Yana\Plugins\Configs\ClassConfiguration();
        $class1->setId('Class 1')->setPriority(\Yana\Plugins\PriorityEnumeration::LOW);
        $class2 = new \Yana\Plugins\Configs\ClassConfiguration();
        $class2->setId('Class 2')->setPriority(\Yana\Plugins\PriorityEnumeration::HIGH);
        $this->assertSame(array($class1->getId()), $this->object->subscribe($class1)->getSubscribers());
        $this->assertSame(array($class2->getId(), $class1->getId()), $this->object->subscribe($class2)->getSubscribers());
        $this->assertSame(array($class2->getId(), $class1->getId()), $this->object->subscribe($class1)->subscribe($class1)->getSubscribers());
    }

    /**
     * @test
     */
    public function testUnsubscribe()
    {
        $class1 = new \Yana\Plugins\Configs\ClassConfiguration();
        $class1->setId('Class 1');
        $this->assertSame(array($class1->getId()), $this->object->subscribe($class1)->getSubscribers());
        $this->assertSame(array(), $this->object->unsubscribe('Class 1')->getSubscribers());
    }

}
