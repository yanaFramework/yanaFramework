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
 */
class DoctrineMapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Factories\DoctrineMapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Factories\DoctrineMapper();
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
    public function testCreateColumnNotNull()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("integer");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $info->setNotnull(true);
        $this->object->createColumn($table, $info, $name);
        $this->assertFalse($table->getColumn("column")->isNullable());
    }

    /**
     * @test
     */
    public function testCreateColumnNotNull2()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("integer");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $info->setNotnull(false);
        $this->object->createColumn($table, $info, $name);
        $this->assertTrue($table->getColumn("column")->isNullable());
    }

    /**
     * @test
     */
    public function testCreateColumnBlob()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("blob");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('text', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnText()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("text");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('text', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnString()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("string");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('string', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnLongString()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("string");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $info->setLength(257);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('text', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnHtml()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "myColumnHtml";
        $type = \Doctrine\DBAL\Types\Type::getType("string");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $info->setLength(257);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('html', $table->getColumn("mycolumnhtml")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnBool()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("boolean");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('bool', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnFloat()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("decimal");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('float', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnFloat2()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("float");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('float', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnTimestamp()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("datetimetz");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('time', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnTime()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("datetime");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('time', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnDate()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("date");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('date', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnInteger()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("integer");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('integer', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnInteger2Bool()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("integer");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $info->setLength(1)->setPrecision(0);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('bool', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnTime2String()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("time");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('string', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnArray()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("array");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('string', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnArray2()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "arrayColumn";
        $type = \Doctrine\DBAL\Types\Type::getType("text");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('array', $table->getColumn("arrayColumn")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnSimpleArray()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("simple_array");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('list', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnJson()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("json");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('array', $table->getColumn("column")->getType());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testCreateColumnNotImplementedException()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("dateinterval");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $this->object->createColumn($table, $info, $name);
    }

    /**
     * @test
     */
    public function testCreateColumnLength()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("decimal");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $info->setLength(10)->setPrecision(2);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame(10, $table->getColumn("column")->getLength());
        $this->assertSame(2, $table->getColumn("column")->getPrecision());
    }

    /**
     * @test
     */
    public function testCreateColumnDefault()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("decimal");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $info->setDefault(-1);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertEquals(-1, $table->getColumn("column")->getDefault());
    }

    /**
     * @test
     */
    public function testCreateColumnUnsigned()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("decimal");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $info->setUnsigned(true);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertTrue($table->getColumn("column")->isUnsigned());
    }

    /**
     * @test
     */
    public function testCreateColumnFixed()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("decimal");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $info->setFixed(true);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertTrue($table->getColumn("column")->isFixed());
    }

    /**
     * @test
     */
    public function testCreateColumnAutoincrement()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("decimal");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $info->setAutoincrement(true);
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertTrue($table->getColumn("column")->isAutoIncrement());
    }

    /**
     * @test
     */
    public function testCreateColumnComment()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "column";
        $type = \Doctrine\DBAL\Types\Type::getType("string");
        $info = new \Doctrine\DBAL\Schema\Column($name, $type);
        $info->setComment('MyComment');
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('MyComment', $table->getColumn("column")->getDescription());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testCreateIndexPrimaryNotImplementedException()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "name";
        $info = new \Doctrine\DBAL\Schema\Index($name, array('a', 'b'), true, true);
        $this->object->createIndex($table, $info, $name);
    }

    /**
     * @test
     */
    public function testCreateIndexPrimary()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "name";
        $info = new \Doctrine\DBAL\Schema\Index($name, array('a'), true, true);
        $table->addColumn('a', 'integer');
        $this->object->createIndex($table, $info, $name);
        $this->assertSame('a', $table->getPrimaryKey());
    }

    /**
     * @test
     */
    public function testCreateIndexUnique()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "name";
        $info = new \Doctrine\DBAL\Schema\Index($name, array('a', 'c'), true);
        $table->addColumn('a', 'integer');
        $table->addColumn('b', 'integer');
        $table->addColumn('c', 'integer');
        $this->object->createIndex($table, $info, $name);
        $this->assertTrue($table->getIndex($name)->isUnique());
    }

    /**
     * @test
     * @expectedException Yana\Core\Exceptions\NotFoundException
     */
    public function testCreateForeignKeyNotFoundException()
    {
        $name = "name";
        $table = new \Yana\Db\Ddl\Table('test');
        $table->addColumn('a', 'integer');
        $table->addColumn('b', 'integer');
        $info = new \Doctrine\DBAL\Schema\ForeignKeyConstraint(array('a', 'b'), 'test', array('a', 'b'));
        $info->setLocalTable(new \Doctrine\DBAL\Schema\Table($table->getName()));
        $this->object->createConstraint($table, $info, $name);
    }

    /**
     * @test
     */
    public function testCreateForeignKey()
    {
        $name = "name";
        $database = new \Yana\Db\Ddl\Database();
        $table = $database->addTable('test');
        $table->addColumn('a', 'integer');
        $table->addColumn('b', 'integer');
        $table->addColumn('c', 'integer');
        $table->addColumn('d', 'integer');
        $info = new \Doctrine\DBAL\Schema\ForeignKeyConstraint(array('a', 'b'), 'test', array('c', 'd'));
        $info->setLocalTable(new \Doctrine\DBAL\Schema\Table($table->getName()));
        $this->object->createConstraint($table, $info, $name);
        $foreignKey = $table->getForeignKey($name);
        $this->assertSame($table->getName(), $foreignKey->getSourceTable());
        $this->assertSame($table->getName(), $foreignKey->getTargetTable());
        $this->assertSame(array('a' => 'c', 'b' => 'd'), $foreignKey->getColumns());
    }

    /**
     * @test
     */
    public function testCreateForeignKeyNoActionSetDefault()
    {
        $name = "name";
        $database = new \Yana\Db\Ddl\Database();
        $table = $database->addTable('test');
        $table->addColumn('a', 'integer');
        $table->addColumn('b', 'integer');
        $options = array(
            'onUpdate' => 'NO ACTION',
            'onDelete' => 'SET DEFAULT'
        );
        $info = new \Doctrine\DBAL\Schema\ForeignKeyConstraint(array('a'), 'test', array('b'), $name, $options);
        $info->setLocalTable(new \Doctrine\DBAL\Schema\Table($table->getName()));
        $this->object->createConstraint($table, $info, $name);
        $foreignKey = $table->getForeignKey($name);
        $this->assertSame(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION, $foreignKey->getOnUpdate());
        $this->assertSame(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT, $foreignKey->getOnDelete());
    }

    /**
     * @test
     */
    public function testCreateForeignKeyCascadeRestrict()
    {
        $name = "name";
        $database = new \Yana\Db\Ddl\Database();
        $table = $database->addTable('test');
        $table->addColumn('a', 'integer');
        $table->addColumn('b', 'integer');
        $fields = array('a', 'b');
        $options = array(
            'onUpdate' => 'CASCADE',
            'onDelete' => 'RESTRICT' // Doctrine maps this to NO ACTION
        );
        $info = new \Doctrine\DBAL\Schema\ForeignKeyConstraint($fields, 'test', $fields, $name, $options);
        $info->setLocalTable(new \Doctrine\DBAL\Schema\Table($table->getName()));
        $this->object->createConstraint($table, $info, $name);
        $foreignKey = $table->getForeignKey($name);
        $this->assertFalse($foreignKey->isDeferrable());
        $this->assertSame(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE, $foreignKey->getOnUpdate());
        $this->assertSame(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION, $foreignKey->getOnDelete());
    }

    /**
     * @test
     */
    public function testCreateForeignKeyCascadeSetNull()
    {
        $name = "name";
        $database = new \Yana\Db\Ddl\Database();
        $table = $database->addTable('test');
        $table->addColumn('a', 'integer');
        $table->addColumn('b', 'integer');
        $fields = array('a', 'b');
        $options = array(
            'onUpdate' => 'SET NULL'
        );
        $info = new \Doctrine\DBAL\Schema\ForeignKeyConstraint($fields, 'test', $fields, $name, $options);
        $info->setLocalTable(new \Doctrine\DBAL\Schema\Table($table->getName()));
        $this->object->createConstraint($table, $info, $name);
        $foreignKey = $table->getForeignKey($name);
        $this->assertFalse($foreignKey->isDeferrable());
        $this->assertSame(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL, $foreignKey->getOnUpdate());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidSyntaxException
     */
    public function testCreateConstraintForeignKeyInvalidSyntaxException()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "name";
        $info = new \Doctrine\DBAL\Schema\ForeignKeyConstraint(array(), 'test', array());
        $info->setLocalTable(new \Doctrine\DBAL\Schema\Table($table->getName()));
        $this->object->createConstraint($table, $info, $name);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidSyntaxException
     */
    public function testCreateConstraintForeignKeyInvalidSyntaxException2()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "name";
        $info = new \Doctrine\DBAL\Schema\ForeignKeyConstraint(array('a', 'b'), 'test', array('a'));
        $info->setLocalTable(new \Doctrine\DBAL\Schema\Table($table->getName()));
        $this->object->createConstraint($table, $info, $name);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidSyntaxException
     */
    public function testCreateConstraintForeignKeyInvalidSyntaxException3()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = new \Doctrine\DBAL\Schema\ForeignKeyConstraint(array('a'), '', array('a'));
        $name = "name";
        $this->object->createConstraint($table, $info, $name);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testCreateIndexInvalidArgumentException()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array();
        $name = "name";
        $info = new \Doctrine\DBAL\Schema\Index($name, array());
        $this->object->createIndex($table, $info, $name);
    }

    /**
     * @test
     */
    public function testCreateIndex()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "name";
        $table->addColumn('a', 'integer');
        $table->addColumn('b', 'integer');
        $table->addColumn('c', 'integer');
        $info = new \Doctrine\DBAL\Schema\Index($name, array('a', 'b', 'c'));
        $this->assertSame($this->object, $this->object->createIndex($table, $info, $name));
        $columns = $table->getIndex($name)->getColumns();
        $this->assertCount(3, $columns);
    }

    /**
     * @test
     */
    public function testCreateIndexFulltext()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $name = "name";
        $table->addColumn('a', 'integer');
        $table->addColumn('b', 'integer');
        $table->addColumn('c', 'integer');
        $info = new \Doctrine\DBAL\Schema\Index($name, array('a', 'b', 'c'));
        $info->addFlag('FullText');
        $this->assertSame($this->object, $this->object->createIndex($table, $info, $name));
        $this->assertTrue($table->getIndex($name)->isFulltext());
    }

    /**
     * @test
     */
    public function testCreateSequence()
    {
        $database = new \Yana\Db\Ddl\Database('test');
        $name = "name";
        $info = new \Doctrine\DBAL\Schema\Sequence($name, 1, 2);
        $this->assertSame($this->object, $this->object->createSequence($database, $info, $name));
        $this->assertSame(2, $database->getSequence("name")->getStart());
    }

}
