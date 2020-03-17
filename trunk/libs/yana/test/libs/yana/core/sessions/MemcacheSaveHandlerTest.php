<?php
/**
 * YANA library
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

namespace Yana\Core\Sessions;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class MemcacheSaveHandlerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Data\Adapters\MemCache\IsWrapper
     */
    protected $memCache;

    /**
     * @var \Yana\Core\Sessions\MemcacheSaveHandler
     */
    protected $adapter;

    /**
     * @var \Yana\Core\Sessions\MemcacheSaveHandler
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
        $this->memCache = new \Yana\Data\Adapters\MemCache\MemcacheWrapper($memCache);
        $this->memCache->addServer($memCacheServer);

        $prefix = __CLASS__;
        $lifetime = 0;

        try {
            $this->adapter = new \Yana\Data\Adapters\MemCacheAdapter($this->memCache, $prefix, $lifetime);
        } catch (\Yana\Data\Adapters\MemCache\ServerNotAvailableException $e) {
            $this->markTestSkipped($e->getMessage());
            return;
        }
        $memCache->flush();

        $this->adapter = new \Yana\Data\Adapters\MemCacheAdapter($this->memCache);
        $this->object = new \Yana\Core\Sessions\MemcacheSaveHandler($this->adapter);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        if ($this->adapter) {
            foreach ($this->adapter->getIds() as $offset)
            {
                $this->adapter->offsetUnset($offset);
            }
        }
    }

    /**
     * @test
     */
    public function testOpen()
    {
        $this->assertTrue($this->object->open("", ""));
    }

    /**
     * @test
     */
    public function testClose()
    {
        $this->assertTrue($this->object->close());
    }

    /**
     * @test
     */
    public function testRead()
    {
        $this->assertEquals(null, $this->object->read('non-existing-offest'));
    }

    /**
     * @test
     */
    public function testWrite()
    {
        $this->object->write('existing-offest', "1");
        $this->assertEquals("1", $this->object->read('existing-offest')); 
    }

    /**
     * @test
     */
    public function testDestroy()
    {
        $this->assertNull($this->object->read('new-offest'));
        $this->object->write('new-offest', "1");
        $this->assertSame("1", $this->object->read('new-offest'));
        $this->object->destroy('new-offest');
        $this->assertNull($this->object->read('new-offest'));
    }

    /**
     * @test
     */
    public function testGc()
    {
        $this->assertTrue($this->object->gc(0));
    }

}
