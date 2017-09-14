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

namespace Yana\Data\Adapters\MemCache;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 */
class MemcacheWrapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Data\Adapters\MemCache\MemcacheWrapper
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
        if (@$memCache->getStats() === false) {
            $this->markTestSkipped();
            return;
        }
        $memCache->flush();

        $this->object = $wrapper;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        if (isset($this->object)) {
            $this->object->__destruct();
        }
    }

    /**
     * @test
     */
    public function testGetVar()
    {
        $this->assertFalse($this->object->getVar('non-existing-must-return-false'));
    }

    /**
     * @test
     */
    public function testGetVars()
    {
        $this->assertEmpty($this->object->getVars(array('non-existing')));
        $this->object->setVar('key1', 1);
        $this->object->setVar('key2', 2);
        $this->assertEquals(array('key2' => 2, 'key1' => 1), $this->object->getVars(array('key2', 'key1')));
        $this->object->unsetVar('key1');
        $this->object->unsetVar('key2');
    }

    /**
     * @test
     */
    public function testSetVar()
    {
        $this->object->setVar('key', 1);
        $this->assertEquals(1, $this->object->getVar('key'));
        $this->object->unsetVar('key');
    }

    /**
     * @test
     */
    public function testUnsetVar()
    {
        $this->assertFalse($this->object->getVar('key'));
        $this->object->setVar('key', 1);
        $this->assertEquals(1, $this->object->getVar('key'));
        $this->object->unsetVar('key');
        $this->assertFalse($this->object->getVar('key'));
    }

    /**
     * @test
     */
    public function testAddServer()
    {
        $memCacheServer = new \Yana\Data\Adapters\MemCache\Server();
        $this->assertTrue($this->object->addServer($memCacheServer));
        $memCacheServer = new \Yana\Data\Adapters\MemCache\Server('non-existing-host', 1234);
        $this->assertTrue($this->object->addServer($memCacheServer));
    }

    /**
     * @test
     */
    public function testGetStats()
    {
        $memCacheServer = new \Yana\Data\Adapters\MemCache\Server('non-existing-host', 1234);
        $this->assertTrue($this->object->addServer($memCacheServer));
        $key1 = $memCacheServer->getHostName() . ':' . $memCacheServer->getPort();

        $memCacheServer = new \Yana\Data\Adapters\MemCache\Server();
        $key2 = $memCacheServer->getHostName() . ':' . $memCacheServer->getPort();

        $stats = $this->object->getStats();
        $this->assertArrayHasKey($key1, $stats);
        $this->assertArrayHasKey($key2, $stats);
        $this->assertInternalType('array', $stats[$key2]);
        $this->assertFalse($stats[$key1]);
    }

}
