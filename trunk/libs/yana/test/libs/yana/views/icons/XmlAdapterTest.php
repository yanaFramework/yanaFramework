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

namespace Yana\Views\Icons;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../include.php';

/**
 * @package test
 */
class XmlAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Icons\NullFile
     */
    protected $file;

    /**
     * @var \Yana\Views\Icons\XmlAdapter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->file = new \Yana\Files\NullFile(\CWD . '/resources/icons.xml');
        $this->object = new \Yana\Views\Icons\XmlAdapter($this->file);
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
    public function testOffsetExists()
    {
        $this->assertFalse($this->object->offsetExists('0'));
        $this->assertTrue($this->object->offsetExists('1'));
        $this->assertTrue($this->object->offsetExists('2'));
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $this->assertNull($this->object->offsetGet('0'));
        $this->assertTrue($this->object->offsetGet('1') instanceof \Yana\Views\Icons\IsFile);
        $this->assertTrue($this->object->offsetGet('2') instanceof \Yana\Views\Icons\IsFile);
        $this->assertSame('1', $this->object->offsetGet('1')->getId());
        $this->assertSame('2', $this->object->offsetGet('2')->getId());
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $entity = new \Yana\Views\Icons\File();
        $entity->setId('Test');
        $this->assertSame($entity, $this->object->offsetSet(null, $entity));
        $this->assertSame($entity, $this->object->offsetGet('Test'));
        $this->assertRegExp('/<file id="Test" path="" regex=""\/>/', $this->file->getContent());
    }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $entity = new \Yana\Views\Icons\File();
        $entity->setId('Test');
        $this->object->offsetSet('Test', $entity);
        $this->assertTrue($this->object->offsetExists('Test'));
        $this->assertNull($this->object->offsetUnset('Test'));
        $this->assertFalse($this->object->offsetExists('Test'));
        $this->assertNotRegExp('/<file id="Test" path="" regex=""\/>/', $this->file->getContent());
    }

    /**
     * @test
     */
    public function testCount()
    {
        $this->assertSame(2, $this->object->count());
    }

    /**
     * @test
     */
    public function testGetIds()
    {
        $this->assertEquals(array('1', '2'), $this->object->getIds());
    }

    /**
     * @test
     */
    public function testSaveEntity()
    {
        $entity = new \Yana\Views\Icons\File();
        $entity->setId('Test');
        $this->assertNull($this->object->saveEntity($entity));
        $this->assertSame($entity, $this->object->offsetGet('Test'));
        $this->assertRegExp('/<file id="Test" path="" regex=""\/>/', $this->file->getContent());
    }

    /**
     * @test
     */
    public function testGetAll()
    {
        $collection = new \Yana\Views\Icons\Collection();
        $collection->setItems(array('1' => $this->object->offsetGet('1'), '2' => $this->object->offsetGet('2')));
        $this->assertEquals($collection, $this->object->getAll());
    }

}
