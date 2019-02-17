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
class MyDoctrineWorker extends \Yana\Db\Ddl\Factories\DoctrineWorker
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
class DoctrineWorkerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Factories\MyDoctrineWorker
     */
    protected $object;

    /**
     * @var \Yana\Db\Ddl\Factories\DoctrineWrapper
     */
    protected $wrapper;

    /**
     * @var \Yana\Db\Ddl\Factories\DoctrineMapper
     */
    protected $mapper;

    /**
     * @return bool
     */
    protected function isAvailable()
    {
        $factory = new \Yana\Db\Doctrine\ConnectionFactory();
        return $factory->isAvailable($factory->getDsn());
    }

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        if (!$this->isAvailable()) {
            $this->markTestSkipped();
        }
        try {
            $factory = new \Yana\Db\Doctrine\ConnectionFactory();
            $this->wrapper = new \Yana\Db\Ddl\Factories\DoctrineWrapper($factory->getConnection());
            $this->mapper = new \Yana\Db\Ddl\Factories\DoctrineMapper();
            $this->object = new \Yana\Db\Ddl\Factories\MyDoctrineWorker($this->mapper, $this->wrapper);

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
        $this->assertCount(1, $indexes);
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

}
