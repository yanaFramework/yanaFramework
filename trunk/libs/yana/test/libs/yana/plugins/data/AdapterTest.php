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

namespace Yana\Plugins\Data;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class AdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Data\Adapter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        try {
            $schema = \Yana\Files\XDDL::getDatabase('plugins');
            restore_error_handler();
            $this->connection = new \Yana\Db\FileDb\NullConnection($schema);
            $this->object = new \Yana\Plugins\Data\Adapter($this->connection);

        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
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
    public function testOffsetGet()
    {
        $this->assertFalse($this->object->offsetExists('non-existing-plugin'));
        $this->assertTrue($this->object->offsetExists('test'));
        $this->assertTrue($this->object->offsetGet('test')->isActive());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testOffsetGetNotFoundException()
    {
        $this->object->offsetGet('non-existing-plugin');
    }

    /**
     * @test
     */
    public function testSaveEntity()
    {
        $entity = $this->object->offsetGet("test");
        $this->assertTrue($entity->isActive());
        $entity->setActive(false);
        $this->object->saveEntity($entity);
        $entity2 = $this->object->offsetGet('test');
        $this->assertSame($entity->isActive(), $entity2->isActive());
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $entity = new \Yana\Plugins\Data\Entity();
        $entity->setId("MyTest")->setActive(true);
        $this->object->offsetSet(null, $entity);
        $actual = $this->object->offsetGet("mytest");
        $this->assertEquals(\mb_strtoupper($entity->getId()), $actual->getId());
        $this->assertTrue($actual->isActive());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $this->object->offsetSet(null, new \Yana\Security\Data\Users\Entity("test"));
    }

}
