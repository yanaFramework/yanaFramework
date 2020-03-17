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
declare(strict_types=1);

namespace Yana\Db\Sources;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class AdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Sources\Adapter
     */
    protected $object;

    /**
     * @var \Yana\Db\IsConnection
     */
    protected $connection;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $factory = new \Yana\Db\SchemaFactory();
        $this->connection = new \Yana\Db\FileDb\NullConnection($factory->createSchema("datasources"));
        $this->object = new \Yana\Db\Sources\Adapter($this->connection);
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
    public function testCount()
    {
        $this->assertSame(2, $this->object->count());
    }

    /**
     * @test
     */
    public function testGetIds()
    {
        $this->assertSame(array(1 => 1, 2 => 2), $this->object->getIds());
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $this->assertSame("test1", $this->object->offsetGet(1)->getName());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testOffsetGetNotFoundException()
    {
        $this->object->offsetGet("-1");
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $entity = new \Yana\Plugins\Data\Entity();
        $this->object->offsetSet(null, $entity);
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $entity = new \Yana\Db\Sources\Entity();
        $entity->setName("test")->setDbms("mssql")->setDatabase("test")->setHost("test")->setUser("test")->setPassword("test");
        $this->assertSame($entity, $this->object->offsetSet(null, $entity));
    }

    /**
     * @test
     */
    public function testGetFromDataSourceName()
    {
        $this->assertSame("test1", $this->object->getFromDataSourceName("test1")->getName());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetFromDataSourceNameNotFoundException()
    {
        $this->object->getFromDataSourceName("no-such-datasource");
    }

}
