<?php
/**
 * PHPUnit test-case.
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

namespace Yana\Db\Helpers\Triggers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 */
class TriggerCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var TriggerCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Helpers\Triggers\TriggerCollection();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @return  array
     */
    public function provider()
    {
        $container = new \Yana\Db\Helpers\Triggers\Container(new \Yana\Db\Ddl\Table("test"), new \Yana\Db\Queries\Insert(new \Yana\Db\NullConnection()));
        return array(
            array('ad', new \Yana\Db\Helpers\Triggers\AfterDelete($container)),
            array('ai', new \Yana\Db\Helpers\Triggers\AfterInsert($container)),
            array('au', new \Yana\Db\Helpers\Triggers\AfterUpdate($container)),
            array('bd', new \Yana\Db\Helpers\Triggers\BeforeDelete($container)),
            array('bi', new \Yana\Db\Helpers\Triggers\BeforeInsert($container)),
            array('bu', new \Yana\Db\Helpers\Triggers\BeforeUpdate($container))
        );
    }

    /**
     * @param  string                               $key
     * @param  \Yana\Db\Helpers\Triggers\IsTrigger  $value
     * @dataProvider  provider
     * @test
     */
    public function testOffsetSet($key, $value)
    {
        $this->object->offsetSet($key, $value);
        $this->assertTrue($this->object[$key] instanceof \Yana\Db\Helpers\Triggers\IsTrigger);
        $this->assertSame($this->object[$key], $value);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $this->object[] = new \Yana\Core\StdObject();
    }

    /**
     * @test
     */
    public function test__invoke()
    {
        foreach ($this->provider() as $params)
        {
            $this->object->offsetSet($params[0], $params[1]);
        }
        $this->assertNull($this->object->__invoke()); // Must not throw exception
    }

    /**
     * @test
     */
    public function test__construct()
    {
        $items = array();
        foreach ($this->provider() as $params)
        {
            $items[$params[0]] = $params[1];
        }
        $object = new \Yana\Db\Helpers\Triggers\TriggerCollection($items);
        $this->assertCount(6, $object);
    }

}
