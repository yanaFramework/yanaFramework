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
class Mdb2MapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Factories\Mdb2Mapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Factories\Mdb2Mapper();
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
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testCreateColumnInvalidArgumentException()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array();
        $name = "column";
        $this->object->createColumn($table, $info, $name);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testCreateColumnNotImplementedException()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'no-such-type');
        $name = "column";
        $this->object->createColumn($table, $info, $name);
    }

    /**
     * @test
     */
    public function testCreateColumnNotNull()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'text', 'notnull' => '1');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertFalse($table->getColumn("column")->isNullable());
    }

    /**
     * @test
     */
    public function testCreateColumnNotNull2()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'text', 'notnull' => '0');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertTrue($table->getColumn("column")->isNullable());
    }

    /**
     * @test
     */
    public function testCreateColumnBlob()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'blob');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('text', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnClob()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'clob');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('text', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnString()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'text');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('string', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnText()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'text', 'length' => '257');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('text', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnHtml()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'text', 'length' => '257');
        $name = "myHtmlColumn";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('html', $table->getColumn("myhtmlcolumn")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnBool()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'bool');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('bool', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnBool2()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'boolean');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('bool', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnFloat()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'decimal');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('float', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnFloat2()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'float');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('float', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnTimestamp()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'timestamp');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('timestamp', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnTime()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'timestamp', 'nativetype' => 'datetime');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('time', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnDate()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'date');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('date', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnInt()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'int');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('integer', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnInteger()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'integer');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('integer', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnInteger2Bool()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'integer', 'length' => '1');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('bool', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnTime2String()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'time');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertSame('string', $table->getColumn("column")->getType());
    }

    /**
     * @test
     */
    public function testCreateColumnLength()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'decimal', 'length' => '2, 10');
        $name = "column";
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
        $info = array('type' => 'int', 'default' => '-1');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertEquals(-1, $table->getColumn("column")->getDefault());
    }

    /**
     * @test
     */
    public function testCreateColumnUnsigned()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'int', 'unsigned' => '1');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertTrue($table->getColumn("column")->isUnsigned());
    }

    /**
     * @test
     */
    public function testCreateColumnFixed()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'int', 'fixed' => '1');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertTrue($table->getColumn("column")->isFixed());
    }

    /**
     * @test
     */
    public function testCreateColumnAutoincrement()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('type' => 'int', 'autoincrement' => '1');
        $name = "column";
        $this->assertSame($this->object, $this->object->createColumn($table, $info, $name));
        $this->assertTrue($table->getColumn("column")->isAutoIncrement());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testCreateConstraintNotImplementedException()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array();
        $name = "name";
        $this->object->createConstraint($table, $info, $name);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testCreateConstraintPrimaryNotImplementedException()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('primary' => '1', 'fields' => array('a' => array(), 'b' => array()));
        $name = "name";
        $this->object->createConstraint($table, $info, $name);
    }

    /**
     * @test
     */
    public function testCreateConstraintPrimary()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('primary' => '1', 'fields' => array('a' => array()));
        $name = "name";
        $table->addColumn('a', 'integer');
        $this->object->createConstraint($table, $info, $name);
        $this->assertSame('a', $table->getPrimaryKey());
    }

    /**
     * @test
     */
    public function testCreateConstraintCheck()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('check' => '1');
        $name = "name";
        $this->assertSame($this->object, $this->object->createConstraint($table, $info, $name));
    }

    /**
     * @test
     */
    public function testCreateConstraintUnique()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('unique' => '1', 'fields' => array('a', 'c'));
        $name = "name";
        $table->addColumn('a', 'integer');
        $table->addColumn('b', 'integer');
        $table->addColumn('c', 'integer');
        $this->object->createConstraint($table, $info, $name);
        $this->assertTrue($table->getColumn('a')->isUnique());
        $this->assertFalse($table->getColumn('b')->isUnique());
        $this->assertTrue($table->getColumn('c')->isUnique());
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
        $fields = array('a' => array(), 'b' => array());
        $info = array('foreign' => '1', 'fields' => $fields, 'references' => array('table' => 'test', 'fields' => $fields));
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
        $info = array(
            'foreign' => '1',
            'fields' => array('a' => array(), 'b' => array()),
            'references' => array('table' => 'test', 'fields' => array('c' => array(), 'd' => array()))
        );
        $this->object->createConstraint($table, $info, $name);
        $foreignKey = $table->getForeignKey($name);
        $this->assertSame($table->getName(), $foreignKey->getSourceTable());
        $this->assertSame($table->getName(), $foreignKey->getTargetTable());
        $this->assertSame(array('a' => 'c', 'b' => 'd'), $foreignKey->getColumns());
    }

    /**
     * @test
     */
    public function testCreateForeignKeyNoActionSetDefaultDeferrable()
    {
        $name = "name";
        $database = new \Yana\Db\Ddl\Database();
        $table = $database->addTable('test');
        $table->addColumn('a', 'integer');
        $table->addColumn('b', 'integer');
        $fields = array('a' => array(), 'b' => array());
        $info = array(
            'foreign' => '1',
            'fields' => $fields,
            'references' => array('table' => 'test', 'fields' => $fields),
            'deferrable' => '1',
            'onupdate' => 'NO ACTION',
            'ondelete' => 'SET DEFAULT'
        );
        $this->object->createConstraint($table, $info, $name);
        $foreignKey = $table->getForeignKey($name);
        $this->assertTrue($foreignKey->isDeferrable());
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
        $fields = array('a' => array(), 'b' => array());
        $info = array(
            'foreign' => '1',
            'fields' => $fields,
            'references' => array('table' => 'test', 'fields' => $fields),
            'deferrable' => '0',
            'onupdate' => 'CASCADE',
            'ondelete' => 'RESTRICT'
        );
        $this->object->createConstraint($table, $info, $name);
        $foreignKey = $table->getForeignKey($name);
        $this->assertFalse($foreignKey->isDeferrable());
        $this->assertSame(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE, $foreignKey->getOnUpdate());
        $this->assertSame(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT, $foreignKey->getOnDelete());
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
        $fields = array('a' => array(), 'b' => array());
        $info = array(
            'foreign' => '1',
            'fields' => $fields,
            'references' => array('table' => 'test', 'fields' => $fields),
            'deferrable' => '0',
            'onupdate' => 'SET NULL',
        );
        $this->object->createConstraint($table, $info, $name);
        $foreignKey = $table->getForeignKey($name);
        $this->assertFalse($foreignKey->isDeferrable());
        $this->assertSame(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL, $foreignKey->getOnUpdate());
    }

    /**
     * @test
     */
    public function testCreateForeignKeyMatchSimple()
    {
        $name = "name";
        $database = new \Yana\Db\Ddl\Database();
        $table = $database->addTable('test');
        $table->addColumn('a', 'integer');
        $table->addColumn('b', 'integer');
        $fields = array('a' => array(), 'b' => array());
        $info = array(
            'foreign' => '1',
            'fields' => $fields,
            'references' => array('table' => 'test', 'fields' => $fields),
            'deferrable' => '0',
            'match' => 'SIMPLE',
        );
        $this->object->createConstraint($table, $info, $name);
        $foreignKey = $table->getForeignKey($name);
        $this->assertSame(\Yana\Db\Ddl\KeyMatchStrategyEnumeration::SIMPLE, $foreignKey->getMatch());
    }

    /**
     * @test
     */
    public function testCreateForeignKeyMatchPartial()
    {
        $name = "name";
        $database = new \Yana\Db\Ddl\Database();
        $table = $database->addTable('test');
        $table->addColumn('a', 'integer');
        $table->addColumn('b', 'integer');
        $fields = array('a' => array(), 'b' => array());
        $info = array(
            'foreign' => '1',
            'fields' => $fields,
            'references' => array('table' => 'test', 'fields' => $fields),
            'deferrable' => '0',
            'match' => 'PARTIAL',
        );
        $this->object->createConstraint($table, $info, $name);
        $foreignKey = $table->getForeignKey($name);
        $this->assertSame(\Yana\Db\Ddl\KeyMatchStrategyEnumeration::PARTIAL, $foreignKey->getMatch());
    }

    /**
     * @test
     */
    public function testCreateForeignKeyMatchFull()
    {
        $name = "name";
        $database = new \Yana\Db\Ddl\Database();
        $table = $database->addTable('test');
        $table->addColumn('a', 'integer');
        $table->addColumn('b', 'integer');
        $fields = array('a' => array(), 'b' => array());
        $info = array(
            'foreign' => '1',
            'fields' => $fields,
            'references' => array('table' => 'test', 'fields' => $fields),
            'deferrable' => '0',
            'match' => 'FULL',
        );
        $this->object->createConstraint($table, $info, $name);
        $foreignKey = $table->getForeignKey($name);
        $this->assertSame(\Yana\Db\Ddl\KeyMatchStrategyEnumeration::FULL, $foreignKey->getMatch());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidSyntaxException
     */
    public function testCreateConstraintForeignKeyInvalidSyntaxException()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('foreign' => '1');
        $name = "name";
        $this->object->createConstraint($table, $info, $name);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidSyntaxException
     */
    public function testCreateConstraintForeignKeyInvalidSyntaxException2()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('foreign' => '1', 'fields' => array('a', 'b'), 'references' => array('fields' => array('a')));
        $name = "name";
        $this->object->createConstraint($table, $info, $name);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidSyntaxException
     */
    public function testCreateConstraintForeignKeyInvalidSyntaxException3()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('foreign' => '1', 'fields' => array('a', 'b'), 'references' => array('fields' => array('a', 'b')));
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
        $this->object->createIndex($table, $info, $name);
    }

    /**
     * @test
     */
    public function testCreateIndex()
    {
        $table = new \Yana\Db\Ddl\Table('test');
        $info = array('fields' => array('a' => array('sorting' => 'ascending'), 'b' => array('sorting' => 'descending'), 'c' => array()));
        $name = "name";
        $table->addColumn('a', 'integer');
        $table->addColumn('b', 'integer');
        $table->addColumn('c', 'integer');
        $this->assertSame($this->object, $this->object->createIndex($table, $info, $name));
        $columns = $table->getIndex($name)->getColumns();
        $this->assertTrue($columns['a']->isAscendingOrder());
        $this->assertFalse($columns['b']->isAscendingOrder());
        $this->assertTrue($columns['c']->isAscendingOrder());
    }

    /**
     * @test
     */
    public function testCreateSequence()
    {
        $database = new \Yana\Db\Ddl\Database('test');
        $info = array("start" => 2);
        $name = "name";
        $this->assertSame($this->object, $this->object->createSequence($database, $info, $name));
        $this->assertSame(2, $database->getSequence("name")->getStart());
    }

}
