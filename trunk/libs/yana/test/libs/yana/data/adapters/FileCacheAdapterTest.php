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
class FileCacheAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Data\Adapters\FileCacheAdapter
     */
    protected $object;

    /**
     * @var \Yana\Files\Dir
     */
    protected $dir;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $stream = new \Yana\Files\Streams\Stream();
        $stream->registerWrapper('null', 'Null');
        $this->dir = new \Yana\Files\Dir('null://test');
        $this->object = new \Yana\Data\Adapters\FileCacheAdapter($this->dir);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $stream = new \Yana\Files\Streams\Stream();
        $stream->unregisterWrapper('null');
    }

    /**
     * @test
     */
    public function testGetIds()
    {
        $this->assertEquals(array(), $this->object->getIds());
    }

    /**
     * @test
     */
    public function testSaveEntity()
    {
        $entity = new \Yana\Security\Data\Users\Entity("Test");
        $this->assertNull($this->object->saveEntity($entity));
//        $this->assertSame($entity, $this->object->offsetGet('Test'));
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $this->assertNull($this->object->offsetGet('Test'));
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $entity = new \Yana\Security\Data\Users\Entity("Test");
        $this->assertSame($entity, $this->object->offsetSet(null, $entity));
    }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $this->assertNull($this->object->offsetUnset('Test'));
    }

}
