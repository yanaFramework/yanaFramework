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

namespace Yana\Db\Ddl\Factories;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyMdb2Wrapper extends \Yana\Db\Ddl\Factories\Mdb2Wrapper
{

    /**
     * Test table has no sequences. We need to fake this.
     *
     * @return  array
     */
    public function listSequences()
    {
        $result = array(
            'MySequence1' => array('start' => '2'),
            'MySequence2' => array('start' => '3'),
        );
        return $result;
    }
}

/**
 * @package  test
 * @ignore
 */
class MyMdb2Worker extends \Yana\Db\Ddl\Factories\Mdb2Worker
{
    public function createColumns(\Yana\Db\Ddl\Table $table, $tableName)
    {
        parent::_createColumns($table, $tableName);
    }

    public function createConstraints(\Yana\Db\Ddl\Table $table, $tableName)
    {
        parent::_createConstraints($table, $tableName);
    }

    public function createIndexes(\Yana\Db\Ddl\Table $table, $tableName)
    {
        parent::_createIndexes($table, $tableName);
    }

    public function createSequences(\Yana\Db\Ddl\Database $database)
    {
        parent::_createSequences($database);
    }

}

/**
 * @package  test
 */
class Mdb2WorkerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Factories\MyMdb2Worker
     */
    protected $object;

    /**
     * @var \Yana\Db\Ddl\Factories\MyMdb2Wrapper
     */
    protected $wrapper;

    /**
     * @var \Yana\Db\Ddl\Factories\Mdb2Mapper
     */
    protected $mapper;

    /**
     * @return bool
     */
    protected function isAvailable()
    {
        $factory = new \Yana\Db\Mdb2\ConnectionFactory();
        return $factory->isAvailable($factory->getDsn());
    }

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        if (!\Yana\Db\Mdb2\ConnectionFactory::isMdb2Available() || !$this->isAvailable()) {
            $this->markTestSkipped();
        }
        try {
            $factory = new \Yana\Db\Mdb2\ConnectionFactory();
            $this->wrapper = new \Yana\Db\Ddl\Factories\MyMdb2Wrapper($factory->getConnection());
            $this->mapper = new \Yana\Db\Ddl\Factories\Mdb2Mapper();
            $this->object = new \Yana\Db\Ddl\Factories\MyMdb2Worker($this->mapper, $this->wrapper);

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
    public function testCreateColumns()
    {
        $table = new \Yana\Db\Ddl\Table('t');
        $this->object->createColumns($table, 't');
        $this->assertEquals(array('tid', 'tvalue', 'ta', 'tb', 'tf', 'ti', 'ftid'), $table->getColumnNames());
        $this->assertSame(32, $table->getColumn('tid')->getLength());
        $this->assertEquals(0, $table->getColumn('tid')->getPrecision());
        $this->assertFalse($table->getColumn('tid')->isNullable());
        $this->assertSame('string', $table->getColumn('tid')->getType());
        $this->assertSame('integer', $table->getColumn('tvalue')->getType());
        $this->assertSame('0', $table->getColumn('tvalue')->getDefault());
        $this->assertSame('text', $table->getColumn('ta')->getType());
        $this->assertSame('bool', $table->getColumn('tb')->getType());
        $this->assertSame('float', $table->getColumn('tf')->getType());
        $this->assertTrue($table->getColumn('tf')->isUnsigned());
        $this->assertSame('integer', $table->getColumn('ti')->getType());
        $this->assertTrue($table->getColumn('ti')->isUnsigned());
        $this->assertSame('integer', $table->getColumn('ftid')->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnsFt()
    {
        $table = new \Yana\Db\Ddl\Table('ft');
        $this->object->createColumns($table, 'ft');
        $this->assertEquals(array('ftid', 'ftvalue', 'array'), $table->getColumnNames());
        $this->assertSame('integer', $table->getColumn('ftid')->getType());
        $this->assertFalse($table->getColumn('ftid')->isNullable());
        $this->assertSame('integer', $table->getColumn('ftvalue')->getType());
        $this->assertTrue($table->getColumn('ftvalue')->isNullable());
        $this->assertSame('array', $table->getColumn('array')->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnsI()
    {
        $table = new \Yana\Db\Ddl\Table('i');
        $this->object->createColumns($table, 'i');
        $this->assertEquals(array('iid', 'ta'), $table->getColumnNames());
        $this->assertSame('string', $table->getColumn('iid')->getType());
        $this->assertSame(32, $table->getColumn('iid')->getLength());
        $this->assertFalse($table->getColumn('iid')->isNullable());
        $this->assertSame('text', $table->getColumn('ta')->getType());
    }

    /**
     * @test
     */
    public function testCreateConstraints()
    {
        $database = new \Yana\Db\Ddl\Database(\YANA_DATABASE_NAME);
        $table = $database->addTable('t');
        $this->object->createColumns($table, 't');
        $fTable = $database->addTable('ft');
        $this->object->createColumns($fTable, 'ft');
        $this->object->createConstraints($table, 't');
    }

    /**
     * @test
     */
    public function testCreateIndexes()
    {
        $table = new \Yana\Db\Ddl\Table('t');
        $this->object->createColumns($table, 't');
        $this->object->createIndexes($table, 't');
        $indexes = $table->getIndexes();
        $this->assertGreaterThanOrEqual(1, count($indexes));
    }

    /**
     * @test
     */
    public function testCreateDatabase()
    {
        $database = $this->object->createDatabase();
        $this->assertTrue($database instanceof \Yana\Db\Ddl\Database);
        $this->assertSame(\YANA_DATABASE_NAME, $database->getName());
        $this->assertTrue($database->isTable('t'));
        $this->assertTrue($database->getTable('t')->isColumn('tid'));
        $this->assertTrue($database->getTable('t')->getColumn('tid')->isPrimaryKey());
    }

    /**
     * @test
     */
    public function testCreateSequences()
    {
        $database = new \Yana\Db\Ddl\Database(__FUNCTION__);
        $this->object->createSequences($database);
        $this->object->createSequences($database); // Must not throw exception
        $sequences = $database->getSequences();
        $this->assertCount(2, $sequences);
        $this->assertArrayHasKey('mysequence1', $sequences);
        $this->assertArrayHasKey('mysequence2', $sequences);
        $this->assertSame('mysequence1', $sequences['mysequence1']->getName());
        $this->assertSame('mysequence2', $sequences['mysequence2']->getName());
        $this->assertSame(2, $sequences['mysequence1']->getStart());
        $this->assertSame(3, $sequences['mysequence2']->getStart());
    }

}
