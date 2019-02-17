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

namespace Yana\Db\Ddl;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class IndexTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Table
     */
    protected $table;

    /**
     * @var \Yana\Db\Ddl\Index
     */
    protected $index;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->table = new \Yana\Db\Ddl\Table('table');
        $this->index = new \Yana\Db\Ddl\Index('index', $this->table);
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
    public function testGetParent()
    {
        $this->assertSame($this->table, $this->index->getParent());
        $index = new \Yana\Db\Ddl\Index('empty');
        $this->assertNull($index->getParent());
    }

    /**
     * @test
     */
    public function testGetTitle()
    {
        $this->assertNull($this->index->getTitle());
    }

    /**
     * @test
     */
    public function testSetTitle()
    {
        $this->assertEquals('test', $this->index->setTitle('test')->getTitle());
        $this->assertNull($this->index->setTitle('')->getTitle());
    }

    /**
     * @test
     */
    public function testGetDescription()
    {
        $this->assertNull($this->index->getDescription());
    }

    /**
     * @test
     */
    public function testSetDescription()
    {
        $this->assertEquals('test', $this->index->setDescription('test')->getDescription());
        $this->assertNull($this->index->setDescription('')->getDescription());
    }

    /**
     * @test
     */
    public function testGetSourceTable()
    {
        $this->assertSame($this->table->getName(), $this->index->getSourceTable());
        $index = new \Yana\Db\Ddl\Index('empty');
        $this->assertNull($index->getSourceTable());
    }

    /**
     * @test
     */
    public function testGetColumns()
    {
        $this->assertEquals(array(), $this->index->getColumns());
    }

    /**
     * @test
     */
    public function testAddColumn()
    {
        $someNames = array("someName_1", "someName_2");
        $this->table->addColumn($someNames[0], 'integer');
        $this->table->addColumn($someNames[1], 'integer');

        $results = array();
        /* @var $results[0] \Yana\Db\Ddl\IndexColumn */
        $results["somename_1"] = $this->index->addColumn($someNames[0]);
        $this->assertInstanceOf('\Yana\Db\Ddl\IndexColumn', $results["somename_1"]);
        $this->assertSame("somename_1", $results["somename_1"]->getName());
        /* @var $results[0] \Yana\Db\Ddl\IndexColumn */
        $results["somename_2"] = $this->index->addColumn($someNames[1]);
        $columns = $this->index->getColumns();
        $this->assertCount(2, $columns);
        $this->assertEquals($results, $columns);
    }

    /**
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     * @test
     */
    public function testAddColumnNotFoundException()
    {
         $this->index->addColumn('');
    }

    /**
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     * @test
     */
    public function testAddColumnNotFoundException2()
    {
         $this->index->addColumn('NoSuchColumn');
    }

    /**
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testAddColumnInvalidArgumentException()
    {
        $index = new \Yana\Db\Ddl\Index('empty');
        $index->addColumn('invalid name');
    }

    /**
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     * @test
     */
    public function testAddColumnAlreadyExistsException()
    {
        try {
            $this->table->addColumn('test', 'integer');
            $this->index->addColumn('test');
        } catch (\Exception $e) {
            $this->fail("Must not raise exception: " . $e->getMessage());
        }
        $this->index->addColumn('test'); // this should raise the expected exception
    }

    /**
     * @test
     */
    public function testDropColumn()
    {
        $this->table->addColumn('test', 'integer');
        $this->index->addColumn('Test');
        $this->assertCount(1, $this->index->getColumns());
        $this->assertCount(0, $this->index->dropColumn('Test')->getColumns());
    }

    /**
     * @test
     */
    public function testIsUnique()
    {
       $this->assertFalse($this->index->isUnique());
    }

    /**
     * @test
     */
    public function testSetUnique()
    {
       $this->assertTrue($this->index->setUnique(true)->isUnique());
       $this->assertFalse($this->index->setUnique(false)->isUnique());
    }

    /**
     * @test
     */
    public function testIsClustered()
    {
       $this->assertFalse($this->index->isClustered());
    }

    /**
     * @test
     */
    public function testSetClustered()
    {
       $this->assertTrue($this->index->setClustered(true)->isClustered());
       $this->assertFalse($this->index->setClustered(false)->isClustered());
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $xml = new \SimpleXMLElement('<index name="Test"/>');
        $index = \Yana\Db\Ddl\Index::unserializeFromXDDL($xml);
        $this->assertTrue($index instanceof \Yana\Db\Ddl\Index);
        $this->assertSame('test', $index->getName());
    }

    /**
     * @test
     */
    public function testSetName()
    {
        $this->assertEquals('name', $this->index->setName('name')->getName());
        $this->assertNull($this->index->setName('')->getName());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetNameInvalidArgumentException()
    {
        $this->index->setName('invalid name');
    }

    /**
     * @test
     */
    public function testIsFulltext()
    {
        $this->assertFalse($this->index->isFulltext());
    }

    /**
     * @test
     */
    public function testSetFulltext()
    {
        $this->assertTrue($this->index->setFulltext(true)->isFulltext());
        $this->assertFalse($this->index->setFulltext(false)->isFulltext());
    }

}
