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

namespace Yana\Data\Adapters;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class MemCacheAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Data\Adapters\MemCacheAdapter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!\class_exists('\Memcache')) {
            $this->markTestSkipped();
            return;
        }
        $memCache = new \Memcache();
        $memCacheServer = new \Yana\Data\Adapters\MemCache\Server();
        $wrapper = new \Yana\Data\Adapters\MemCache\MemcacheWrapper($memCache);
        $wrapper->addServer($memCacheServer);

        $prefix = __CLASS__;
        $lifetime = 0;

        try {
            $this->object = new \Yana\Data\Adapters\MemCacheAdapter($wrapper, $prefix, $lifetime);
        } catch (\Yana\Data\Adapters\MemCache\ServerNotAvailableException $e) {
            $this->markTestSkipped($e->getMessage());
            return;
        }
        $memCache->flush();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        if ($this->object) {
            foreach ($this->object->getIds() as $offset)
            {
                $this->object->offsetUnset($offset);
            }
        }
    }

    /**
     * @expectedException \Yana\Data\Adapters\MemCache\ServerNotAvailableException
     * @test
     */
    public function testConstruct()
    {
        $memCache = new \Memcache();
        $wrapper = new \Yana\Data\Adapters\MemCache\MemcacheWrapper($memCache);
        new \Yana\Data\Adapters\MemCacheAdapter($wrapper);
    }

    /**
     * @test
     */
    public function testGetIds()
    {
        $this->assertEquals(array(), $this->object->getIds());
        $this->object[1] = true;
        $this->assertEquals(array(1), $this->object->getIds());
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $this->assertEquals(null, $this->object->offsetGet('non-existing-offest'));
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $this->object->offsetSet('existing-offest', 1);
        $this->assertTrue($this->object->offsetExists('existing-offest'));
        $this->assertEquals(1, $this->object->offsetGet('existing-offest')); 
   }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $this->assertFalse($this->object->offsetExists('new-offest'));
        $this->object->offsetSet('new-offest', true);
        $this->assertTrue($this->object->offsetExists('new-offest'));
        $this->object->offsetUnset('new-offest');
        $this->assertFalse($this->object->offsetExists('new-offest'));
    }

}
