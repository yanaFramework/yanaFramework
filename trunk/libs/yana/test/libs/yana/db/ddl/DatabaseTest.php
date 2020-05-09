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
 * DDL test-case
 *
 * @package  test
 */
class DatabaseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Database
     */
    protected $database;

    /**
     * @var \Yana\Db\Ddl\ForeignKey
     */
    protected $foreignkey;

    /**
     * @var \Yana\Db\Ddl\Table
     */
    protected $table;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        chdir(CWD . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

        \Yana\Db\Ddl\Database::setCache(new \Yana\Data\Adapters\ArrayAdapter());
        $this->database = new \Yana\Db\Ddl\Database('Database', CWD . '/resources/check.db.xml');
        $this->table = new \Yana\Db\Ddl\Table('table');
        $this->foreignkey = new \Yana\Db\Ddl\ForeignKey('foreignkey');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->database->setModified(true); // setting the file to modified forces the instance cache to be cleared
        unset($this->database); // this doesn't kill all references, UNLESS the files was previously set to modified
        unset($this->foreignkey);
        unset($this->table);
        chdir(CWD);
    }

    /**
     * Included another database file must not overwrite defined forms and tables.
     *
     * @test
     */
    public function testLoadIncludesOverwriteCheck()
    {
        $xddl = new \Yana\Files\XDDL(CWD . '/resources/testinclude.db.xml');
        $this->database = $xddl->toDatabase();
        $this->assertSame(array('testinclude2'), $this->database->getIncludes());
        $this->assertFalse($this->database->isForm('a'));
        $this->assertTrue($this->database->isForm('b'));
        $this->assertTrue($this->database->isForm('c'));
        $this->assertTrue($this->database->isTable('a'));
        $this->assertTrue($this->database->isTable('b'));
        $this->assertTrue($this->database->isTable('c'));
    }

    /**
     * When we load a database file, then include said file from another file,
     * both files must still work.
     *
     * @test
     */
    public function testLoadIncludesOverwriteCheck2()
    {
        $xddl = new \Yana\Files\XDDL(CWD . '/resources/testinclude2.db.xml');
        $database1 = $xddl->toDatabase();
        $xddl = new \Yana\Files\XDDL(CWD . '/resources/testinclude.db.xml');
        $this->database = $xddl->toDatabase();
        $this->assertSame(array('testinclude2'), $this->database->getIncludes());
        $this->assertFalse($this->database->isForm('a'));
        $this->assertTrue($this->database->isForm('b'));
        $this->assertTrue($this->database->isForm('c'));
        $this->assertTrue($this->database->isTable('a'));
        $this->assertTrue($this->database->isTable('b'));
        $this->assertTrue($this->database->isTable('c'));
    }

    /**
     * Data-provider for testTitle
     */
    public function dataTitle()
    {
        return array(
            array('database'),
            array('table')
        );
    }

    /**
     * title
     *
     * @dataProvider dataTitle
     * @param  string  $propertyName
     * @test
     */
    public function testTitle($propertyName)
    {
        $object = $this->$propertyName;
        $testTitle = "some Title";
        $object->setTitle($testTitle);
        $getTitle = $object->getTitle();
        $this->assertEquals($getTitle, $testTitle, get_class($object) . ': title assignment failed.');

        $object->setTitle();
        $getTitle = $object->getTitle();
        $this->assertNull($getTitle, get_class($object) . ': unable to unset title.');
    }

    /**
     * Data-provider for testDescription
     */
    public function dataDescription()
    {
        return array(
            array('database'),
            array('table')
        );
    }

    /**
     * description
     *
     * @dataProvider dataDescription
     * @test
     * @param  string  $propertyName
     */
    public function testDescription($propertyName)
    {
        $object = $this->$propertyName;
        $object->setDescription('description');
        $result = $object->getDescription();
        $this->assertEquals('description', $result, 'expected value is "description"  - the values should be equal');

        $object->setDescription('');
        $result = $object->getDescription();
        $this->assertNull($result, 'the description is expected null');
    }

    /**
     * source-table
     *
     * @test
     */
    public function testGetSourceTable()
    {
        // DDL INDEX
        $index = new \Yana\Db\Ddl\Index('index');
        $sourceTable = $index->getSourceTable();
        $this->assertNull($sourceTable, 'assert failed, the value expected null');

        // DDL ForeignKey
        $sourceTable = $this->foreignkey->getSourceTable();
        $this->assertNull($sourceTable, 'assert failed, the value expected null');
    }

    /**
     * readonly
     *
     * @test
     */
    public function testReadonly()
    {
       // ddl database
       $this->database->setReadonly(true);
       $result = $this->database->isReadonly();
       $this->assertTrue($result, 'assert failed, \Yana\Db\Ddl\Database : expected true - setReadonly was set with true');

       $this->database->setReadonly(false);
       $result = $this->database->isReadonly();
       $this->assertFalse($result, 'assert failed, \Yana\Db\Ddl\Database : expected false - setReadonly was set with false');

       // DDL Table
       $this->table->setReadonly(true);
       $result = $this->table->isReadonly();
       $this->assertTrue($result, 'assert failed, \Yana\Db\Ddl\Table : expected true - setReadonly was set with true');

       $this->table->setReadonly(false);
       $result = $this->table->isReadonly();
       $this->assertFalse($result, 'assert failed, \Yana\Db\Ddl\Table : expected false - setReadonly was set with false');
    }

    /**
     * Deferrable
     *
     * @test
     */
    public function testDeferrable()
    {
       // ddl field
       $this->foreignkey->setDeferrable(true);
       $result = $this->foreignkey->isDeferrable();
       $this->assertTrue($result, 'assert failed, \Yana\Db\Ddl\ForeignKey : expected true - setDeferrable was set with true');

       $this->foreignkey->setDeferrable(false);
       $result = $this->foreignkey->isDeferrable();
       $this->assertFalse($result, 'assert failed, \Yana\Db\Ddl\ForeignKey : expected false - setDeferrable was set with false');
    }

    /**
     * is foreign-key
     *
     * @test
     */
    public function testIsForeignKey()
    {
        // create a target-table
        $newTableA = $this->database->addTable("someTable");
        $newTableB = $this->database->addTable("otherTable");
        $ColumnA = $newTableA->addColumn("firstCol", "integer");
        $ColumnB = $newTableB->addColumn("someCol", "integer");
        $ColumnC = $newTableB->addColumn("someMoreCol", "integer");
        $newTableA->setPrimaryKey("firstCol");
        $foreign = $newTableB->addForeignKey("someTable");
        $foreign->setColumn("someCol");
        $valid = $ColumnB->isForeignKey();
        $this->assertTrue($valid, '\Yana\Db\Ddl\Column::isForeignKey - key expected ');
        $valid = $ColumnC->isForeignKey();
        $this->assertFalse($valid, '\Yana\Db\Ddl\Column::isForeignKey - key expected ');
    }

    /**
     * getConstraint
     *
     * @test
     */
    public function testConstraint()
    {
        // DDL Table
        $testArray1 = array("someConstraints 1", "someConstraints 2", "someConstraints 3");
        $this->table->addConstraint($testArray1[0]);
        $this->table->addConstraint($testArray1[1]);
        $this->table->addConstraint($testArray1[2]);
        $result1 = $this->table->getConstraints();

        $this->assertEquals($result1[0]->getConstraint(), $testArray1[0], '\Yana\Db\Ddl\Table::getConstraints failed, both arrays should be equal');

        $testArray2 = array("someMoreConstraints 1", "someMoreConstraints 2", "someMoreConstraints 3");
        $this->table->addConstraint($testArray2[0], "", "mysql");
        $this->table->addConstraint($testArray2[1], "", "mysql");
        $this->table->addConstraint($testArray2[2], "", "mysql");
        $result1 = $this->table->getConstraints("mysql");
        $this->assertEquals($result1[1]->getConstraint(), $testArray2[1], '\Yana\Db\Ddl\Table::getConstraints failed, both arrays should be equal');
        //$this->table->dropConstraints();
        $this->table->addConstraint("someDifferentConstraints 1", "name", "mysql");
        $this->table->addConstraint("someDifferentConstraints 2", "name", "mysql");
        $result1 = $this->table->getConstraint("name", "mysql");
        $this->assertEquals($result1->getConstraint(), "someDifferentConstraints 1", '\Yana\Db\Ddl\Table::getConstraints failed');

        $result1 = $this->table->getConstraint("name2", "mysql");
        $this->assertNull($result1, '\Yana\Db\Ddl\Table::getConstraints failed');

        $get = $this->table->getConstraints("oracle");
        $this->assertEquals(array(), $get, '\Yana\Db\Ddl\Table::getConstraints - "oracle" doesnt exist in array');

        $this->table->dropConstraints();
        $get = $this->table->getConstraints();
        $this->assertEquals(array(), $get, '\Yana\Db\Ddl\Table::getConstraints list should be empty after droping constraints.');

        // get Unique-Constraints
        $result1 = $this->table->getUniqueConstraints();
        $uniqueCol = $this->table->addColumn('unique','integer');
        $uniqueCol->setUnique();
        $result2 = $this->table->getUniqueConstraints();
        $this->assertInternalType('array',$result1, '\Yana\Db\Ddl\Table::');
        $this->assertInternalType('array',$result2, '\Yana\Db\Ddl\Table::');
        $this->assertTrue(empty($result1), '\Yana\Db\Ddl\Table::');
        $this->assertFalse(empty($result2), '\Yana\Db\Ddl\Table::');
    }

    /**
     * Includes
     *
     * @test
     */
    public function testIncludes()
    {
        $array = array('first');
        // ddl database
        $this->database->setIncludes($array);
        $result = $this->database->getIncludes();
        $this->assertEquals($array, $result, 'assert failed, \Yana\Db\Ddl\Database : expected an array with one entire "first", values should be equal');
        $next = 'second';
        $add = $this->database->addInclude($next);
        $result = $this->database->getIncludes();
        $this->assertEquals('second', $result[1], 'assert failed, \Yana\Db\Ddl\Database : the value "second" should be match a value in array, values should be equal');
    }

    /**
     * Charset
     *
     * @test
     */
    public function testCharset()
    {
        // ddl database
        $this->database->setCharset('charset');
        $result = $this->database->getCharset();
        $this->assertEquals('charset', $result, 'assert failed, \Yana\Db\Ddl\Database : expected "charset" as value');

        $this->database->setCharset();
        $result = $this->database->getCharset();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Database : expected null, the charset is empty');
    }

    /**
     * DataSource
     *
     * @test
     */
    public function testDataSource()
    {
        // ddl database
        $this->database->setDataSource('dataSource');
        $result = $this->database->getDataSource();
        $this->assertEquals('dataSource', $result, 'assert failed, \Yana\Db\Ddl\Database : expected "dataSource" as value');

        $this->database->setDataSource();
        $result = $this->database->getDataSource();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Database : expected null, the DataSource is empty');
    }

    /**
     * Table
     *
     * @test
     */
    public function testSetTable()
    {
        // \Yana\Db\Ddl\Database
        $valid = $this->database->isTable('newtable');
        $this->assertFalse($valid, 'assert failed, expected false, the value "newtable" is not a table');

        $add = $this->database->addTable('newtable');
        $this->assertTrue($add instanceof \Yana\Db\Ddl\Table, 'assert failed, the value should be an instanceof \Yana\Db\Ddl\Table');
        $getAll = $this->database->getTables();
        $this->assertArrayHasKey('newtable', $getAll, 'assert failed, the value should be match a key in array');
        $result = $this->database->getTable('newtable');
        $this->assertInternalType('object', $result, 'assert failed, the value should be from type object');

        $valid = $this->database->isTable('newtable');
        $this->assertTrue($valid, 'assert failed, expected true, the value "newtable" is a Table');

        $newTable = $this->database->addTable("someTable");
        $retTable = $this->database->getTable("someTable");
        $this->assertNotNull($retTable, 'getTable : expected null, non table is set');
        $retTable = $this->database->getTable("otherTable");
        $this->assertNull($retTable, 'getTable : expected null, non table is set');

        $tables = $this->database->getTableNames();
        $this->assertContains('newtable', $tables, 'assert failed, the value should be match a key in array');
        $this->assertContains('sometable', $tables, 'assert failed, the value should be match a key in array');

        // null expected
        $drop = $this->database->dropTable('newtable');
        $get = $this->database->getTable('newtable');
        $this->assertNull($get, 'assert failed, expected null - table was dropt before');
    }

    /**
     * Magic Getter
     *
     * @test
     */
    public function testMagicGet()
    {
        // magic Database
        $this->database->addTable('myTable');
        $result = $this->database->myTable;
        $this->assertTrue($result instanceof \Yana\Db\Ddl\Table, 'assert failed, expected null - table was dropt before');

        // magic Table
        $this->database->myTable->addColumn('magic', 'integer');
        $result = $this->database->myTable->magic;
        $this->assertTrue($result instanceof \Yana\Db\Ddl\Column, 'assert failed, expected null - column was dropt before');

        // magic Form
        $this->database->addForm('magicForm');
        $result = $this->database->magicForm;
        $this->assertTrue($result instanceof \Yana\Db\Ddl\Form, 'assert failed, expected null - form was dropt before');

        // magic View
        $this->database->addView('magicView');
        $result = $this->database->magicView;
        $this->assertTrue($result instanceof \Yana\Db\Ddl\Views\View, 'assert failed, expected null - view was dropt before');

    }

    /**
     * add already existing table
     *
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     *
     * @test
     */
    public function testAddTableAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->database->addTable('table');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->database->addTable('table');
    }

    /**
     * drop non-existing table
     *
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     *
     * @test
     */
    public function testDropTableNotFoundException()
    {
        // \Yana\Db\Ddl\Database
        $this->database->dropTable('no_table');
    }

    /**
     * View
     *
     * @test
     */
    public function testView()
    {
        // DDL Database
        $valid = $this->database->isView('qwerty');
        $this->assertFalse($valid, 'assert failed, the value should be false, "qwerty" is not a view');

        $add = $this->database->addView('qwerty');
        $this->assertInternalType('object', $add, 'assert failed, the value should be from type object');
        $this->assertEquals('qwerty', $add->getName(), 'assert failed, the values should be equal, "qwerty" is a view');

        $add = $this->database->addView('trewq');
        $this->assertInternalType('object', $add, 'assert failed, the value should be from type object');
        $this->assertEquals('trewq', $add->getName(), 'assert failed, the values should be equal, "trewq" is a view');

        $get = $this->database->getView('qwerty');
        $this->assertInternalType('object', $get, 'assert failed, the value should be from type object');
        $this->assertEquals('qwerty', $get->getName(), 'assert failed, the values should be equal, the name of the view should be the same as expected');

        $getAll = $this->database->getViews();
        $this->assertInternalType('array', $getAll, 'assert failed, the value should be from type array');
        $this->assertArrayHasKey('qwerty', $getAll, 'assert failed, the value should be match a entry in array');
        $this->assertArrayHasKey('trewq', $getAll, 'assert failed, the value should be match a entry in array');

        $getNames = $this->database->getViewNames();
        $this->assertInternalType('array', $getNames, 'assert failed, the values should be from type array');
        $this->assertTrue(in_array('qwerty', $getNames), 'assert failed, the value should be match a entry in array');
        $this->assertTrue(in_array('trewq', $getNames), 'assert failed, the value should be match a entry in array');

        $valid = $this->database->isView('qwerty');
        $this->assertTrue($valid, 'assert failed, the value should be true');

        $drop = $this->database->dropView('qwerty');
        $nonexist = $this->database->getView('qwerty');
        $this->assertNull($nonexist, 'assert failed, the value should be null, the view was dropt before');
    }

    /**
     * addViewAlreadyExistsException
     *
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     *
     * @test
     */
    public function testAddViewAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->database->addView('view');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->database->addView('view');
    }

    /**
     * drop non-existing view
     *
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     *
     * @test
     */
    public function testDropViewNotFoundException()
    {
        // \Yana\Db\Ddl\Database
        $this->database->dropView('a');
    }

    /**
     * Function
     *
     * @test
     */
    public function testSetFunction()
    {
        // DDL Database
        $valid = $this->database->isFunction('qwerty');
        $this->assertFalse($valid, 'assert failed, "\Yana\Db\Ddl\Database" the value should be false, "qwerty" is not a function');

        $add = $this->database->addFunction('qwerty');
        $this->assertInternalType('object', $add, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('qwerty', $add->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, "qwerty" is a function');

        $add = $this->database->addFunction('trewq');
        $this->assertInternalType('object', $add, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('trewq', $add->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $get = $this->database->getFunction('qwerty');
        $this->assertInternalType('object', $get, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('qwerty', $get->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $getAll = $this->database->getFunctions();
        $this->assertInternalType('array', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type array');
        $this->assertArrayHasKey('qwerty', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');
        $this->assertArrayHasKey('trewq', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');

        $getNames = $this->database->getFunctionNames();
        $this->assertInternalType('array', $getNames, 'assert failed, "\Yana\Db\Ddl\Database" the values should be from type array');
        $this->assertTrue(in_array('qwerty', $getNames), 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');
        $this->assertTrue(in_array('trewq', $getNames), 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');

        $valid = $this->database->isFunction('qwerty');
        $this->assertTrue($valid, 'assert failed, "\Yana\Db\Ddl\Database" the value should be true, "qwerty" is a function');

        $drop = $this->database->dropFunction('qwerty');
        $nonexist = $this->database->getFunction('qwerty');
        $this->assertNull($nonexist, 'assert failed, "\Yana\Db\Ddl\Database" the value should be null, "qwerty" was dropt before');
    }

    /**
     * addFunctionAlreadyExistsException
     *
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     *
     * @test
     */
    public function testAddFunctionAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->database->addFunction('function');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->database->addFunction('function');
    }

    /**
     * drop non-existing function
     *
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     *
     * @test
     */
    public function testDropFunctionNotFoundException()
    {
        // \Yana\Db\Ddl\Database
        $this->database->dropFunction('gert');
    }

    /**
     * Sequence
     *
     * @test
     */
    public function testSequence()
    {
        // DDL Database
        $valid = $this->database->isSequence('qwerty');
        $this->assertFalse($valid, 'assert failed, "\Yana\Db\Ddl\Database" the value should be false, "qwerty" is not a sequence');

        $add = $this->database->addSequence('qwerty');
        $this->assertInternalType('object', $add, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('qwerty', $add->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $add = $this->database->addSequence('trewq');
        $this->assertInternalType('object', $add, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('trewq', $add->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $get = $this->database->getSequence('qwerty');
        $this->assertInternalType('object', $get, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('qwerty', $get->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $getAll = $this->database->getSequences();
        $this->assertInternalType('array', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type array');
        $this->assertArrayHasKey('qwerty', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');
        $this->assertArrayHasKey('trewq', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');

        $getNames = $this->database->getSequenceNames();
        $this->assertInternalType('array', $getNames, 'assert failed, "\Yana\Db\Ddl\Database" the values should be from type array');
        $this->assertTrue(in_array('qwerty', $getNames), 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');
        $this->assertTrue(in_array('trewq', $getNames), 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');

        $valid = $this->database->isSequence('qwerty');
        $this->assertTrue($valid, 'assert failed, "\Yana\Db\Ddl\Database" the value should be true, "qwerty" is a sequence');

        $drop = $this->database->dropSequence('qwerty');
        $nonexist = $this->database->getSequence('qwerty');
        $this->assertNull($nonexist, 'assert failed, "\Yana\Db\Ddl\Database" the value should be null, "qwerty" was removed before');
    }

    /**
     * addSequenceAlreadyExistsException
     *
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     *
     * @test
     */
    public function testAddSequenceAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->database->addSequence('sequence');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->database->addSequence('sequence');
    }

    /**
     * drop non-existing sequence
     *
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     *
     * @test
     */
    public function testDropSequenceNotFoundException()
    {
        // \Yana\Db\Ddl\Database
        $this->database->dropSequence('no_sequence');
    }

    /**
     * dropInit
     *
     * @test
     */
    public function testDropInit()
    {
        // ddl database
        $this->database->dropInit();
        $init = $this->database->getInit();
        $this->assertTrue(empty($init), 'Initialization list should be empty after droping contents');
    }

    /**
     * addInit
     *
     * @test
     */
    public function testAddInit()
    {
        $get = $this->database->getInit('oracle');
        $this->assertInternalType('array', $get, 'assert failed, the value should be from type array');
        $this->assertEquals(0, count($get), 'assert failed, the values should be equal');

        $dbms = 'mysql';
        $sql = 'select * from users';
        $this->database->addInit($sql, $dbms);
        $get = $this->database->getInit($dbms);
        $this->assertEquals($sql, $get[0], 'assert failed, the values should be equal');

        $get = $this->database->getInit('oracle');
        $this->assertInternalType('array', $get, 'assert failed, the value should be from type array');
        $this->assertEquals(0, count($get), 'assert failed, the values should be equal');
    }

    /**
     * @test
     */
    public function testGetListOfFiles()
    {
        $get = \Yana\Db\Ddl\DDL::getListOfFiles();
        $this->assertNotContains('config/db/user.db.xml', $get);
        $this->assertContains('user', $get);
        $this->assertInternalType('array', $get, 'assert failed, the value should be from type array');
    }

    /**
     * @test
     */
    public function testGetListOfFilesFullFilenames()
    {
        $get = \Yana\Db\Ddl\DDL::getListOfFiles(true);
        $this->assertCount(10, $get);
        foreach ($get as $file)
        {
            $this->assertFileExists($file);
        }
        $this->assertInternalType('array', $get);
        $this->assertNotContains('user', $get);
    }

    /**
     * Form
     *
     * @test
     */
    public function testSetForm()
    {
         // DDL Database
        $valid = $this->database->isForm('qwerty');
        $this->assertFalse($valid, 'assert failed, "\Yana\Db\Ddl\Database" the value should be false, "qwerty" is not a form');

        $add = $this->database->addForm('qwerty');
        $this->assertInternalType('object', $add, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('qwerty', $add->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $add = $this->database->addForm('trewq');
        $this->assertInternalType('object', $add, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('trewq', $add->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $get = $this->database->getForm('qwerty');
        $this->assertInternalType('object', $get, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('qwerty', $get->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $getAll = $this->database->getForms();
        $this->assertInternalType('array', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type array');
        $this->assertArrayHasKey('qwerty', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');
        $this->assertArrayHasKey('trewq', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');

        $getNames = $this->database->getFormNames();
        $this->assertInternalType('array', $getNames, 'assert failed, "\Yana\Db\Ddl\Database" the values should be from type array');
        $this->assertTrue(in_array('qwerty', $getNames), 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');
        $this->assertTrue(in_array('trewq', $getNames), 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');

        $valid = $this->database->isForm('qwerty');
        $this->assertTrue($valid, 'assert failed, "\Yana\Db\Ddl\Database" the value should be true, "qwerty" is a form');

        $drop = $this->database->dropForm('qwerty');
        $nonexist = $this->database->getForm('qwerty');
        $this->assertNull($nonexist, 'assert failed, "\Yana\Db\Ddl\Database" the value should be null, "qwerty" was dropt before');
    }

    /**
     * addFormAlreadyExistsException
     *
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     *
     * @test
     */
    public function testAddFormAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->database->addForm('form');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->database->addForm('form');
    }

    /**
     * dropFormInvalidArgumentException
     *
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     *
     * @test
     */
    public function testDropFormInvalidArgumentException1()
    {
        // \Yana\Db\Ddl\Database
        $this->database->dropForm('gert');
    }

    /**
     * Column
     *
     * @test
     */
    public function testSetColumn()
    {
        // DDL ForeignKey
        $this->foreignkey->setColumn('test', 'qwertz');
        $get = $this->foreignkey->getColumns();
        $this->assertArrayHasKey('test', $get, 'assert failed, the values should be equal,  the value should be match a key in array');

        // the appending test can not be done with the attributes of the class,
        // because the lack the parents
        $tableTest = $this->database->addTable('testSetColumn');
    }

    /**
     * Columns
     *
     * @test
     */
    public function testColumns()
    {
        $array = array('column1', 'column2', 'column3');
        // DDL ForeignKey
        $this->foreignkey->setColumns($array);
        $result = $this->foreignkey->getColumns();
        $this->assertEquals($array, $result, 'assert failed, \Yana\Db\Ddl\ForeignKey : the values shoud be equal, expected the same array which was set at the begining');

        $testTable = new \Yana\Db\Ddl\Table('testTable');
        $testForeignKey = new \Yana\Db\Ddl\ForeignKey('testKey', $testTable);

        // negativer Test
        try {
            $testForeignKey->setColumns($array);
            $this->fail("\Yana\Db\Ddl\ForeignKey::setCoLumns should fail, if one of the Columns in the Targettable does not exists");
        } catch (\Exception $e) {
            //success
        }
    }

    /**
     * Name
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testSetNameInvalidArgument()
    {
        // DDL Object exception
        $new = new \Yana\Db\Ddl\Index(' 123df');
    }

    /**
     * TargetTable
     *
     * @test
     */
    public function testTargetTable()
    {
        // DDL ForeignKey
        $this->foreignkey->setTargetTable('targetTable');
        $result = $this->foreignkey->getTargetTable();
        $this->assertEquals('targettable', $result, 'getTargetTable() did not return expected value');

        $this->foreignkey->setTargetTable('');
        $result = $this->foreignkey->getTargetTable();
        $this->assertNull($result, 'reset of target table failed');
    }

    /**
     * Match
     *
     * @test
     */
    public function testMatch()
    {
        // DDL ForeignKey
        $this->foreignkey->setMatch(2);
        $result = $this->foreignkey->getMatch();
        $message = 'assert failed, \Yana\Db\Ddl\ForeignKey : expected value is the number 2';
        $this->assertEquals(\Yana\Db\Ddl\KeyMatchStrategyEnumeration::SIMPLE, $result, $message);

        $this->foreignkey->setMatch(12);
        $result = $this->foreignkey->getMatch();
        // expected default 0
        $message = 'assert failed, \Yana\Db\Ddl\ForeignKey : expected 0 as value, the 0 number will be choosen when the number ' .
            'by setMatch does not match the numbers 0, 1, 2';
        $this->assertEquals(\Yana\Db\Ddl\KeyMatchStrategyEnumeration::SIMPLE, $result, $message);
    }

    /**
     * @test
     */
    public function testGrants()
    {
        $grant = new \Yana\Db\Ddl\Grant();
        $grant2 = new \Yana\Db\Ddl\Grant();

        $grants = array($grant, $grant2);

        $this->table->setGrant($grant);
        $this->table->setGrant($grant2);

        $this->assertEquals($grants, $this->table->getGrants(), 'assert failed, the values should be equal, expected the same arrays');

        $add = $this->table->addGrant('user', 'role', 10);
        $this->assertTrue($add instanceof \Yana\Db\Ddl\Grant, 'Function addGrant() should return instance of \Yana\Db\Ddl\Grant.');

        $this->table->dropGrants();

        $get = $this->table->getGrants();
        $this->assertEquals(array(), $get, 'Function getGrants() should return an empty array after calling dropGrants().');
    }

    /**
     * parent
     *
     * @test
     */
    public function testParent()
    {
        $get = $this->foreignkey->getParent();
        $this->assertNull($get, 'assert failed, expected null - no parent is set');

        $database = new \Yana\Db\Ddl\Database();
        $parentTable = new \Yana\Db\Ddl\Table('table');
        $parentForm = new \Yana\Db\Ddl\Form('someform');

        // \Yana\Db\Ddl\ForeignKey
        $childForeignkey = new \Yana\Db\Ddl\ForeignKey('column', $parentTable);
        $parentForeignkey = $childForeignkey->getParent();
        $this->assertEquals($parentTable, $parentForeignkey, '\Yana\Db\Ddl\ForeignKey::getParent, the values should be equal');

        // \Yana\Db\Ddl\Form
        $childForm = new \Yana\Db\Ddl\Form('form', $database);
        $parentForm = $childForm->getParent();
        $this->assertEquals($database, $parentForm, '\Yana\Db\Ddl\Form::getParent, the values should be equal');
        $parentForm = $childForm->getDatabase();
        $this->assertEquals($database, $parentForm, '\Yana\Db\Ddl\Form::getParent, the values should be equal');

        // \Yana\Db\Ddl\Form sub-form
        $subForm = $childForm->addForm('subform');
        $parentForm = $subForm->getParent();
        $this->assertEquals($parentForm, $childForm, '\Yana\Db\Ddl\Form::getParent, the values should be equal');
        $parentDatabase = $subForm->getDatabase();
        $this->assertEquals($database, $parentDatabase, '\Yana\Db\Ddl\Form::getDatabase, the values should be equal');

        // \Yana\Db\Ddl\Index
        $childIndex = new \Yana\Db\Ddl\Index('index', $parentTable);
        $parentIndex = $childIndex->getParent();
        $this->assertEquals($parentTable, $parentIndex, '\Yana\Db\Ddl\Index::getParent, the values should be equal');

        // \Yana\Db\Ddl\Views\View
        $childView = new \Yana\Db\Ddl\Views\View('view', $database);
        $parentView = $childView->getParent();
        $this->assertEquals($database, $parentView, '\Yana\Db\Ddl\Views\View::getParent, the values should be equal');

        // \Yana\Db\Ddl\Table
        $childTable = new \Yana\Db\Ddl\Table('table', $database);
        $parentTable = $childTable->getParent();
        $this->assertEquals($database, $parentTable, '\Yana\Db\Ddl\Table::getParent, the values should be equal');
    }

    /**
     * on-delete action
     *
     * @test
     */
    public function testOnDelete()
    {
        $this->foreignkey->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION);
        $get = $this->foreignkey->getOnDelete();
        $message = 'assert failed, expected value is "0" - the values should be equal';
        $this->assertEquals(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION, $get, $message);

        $this->foreignkey->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT);
        $get = $this->foreignkey->getOnDelete();
        $message ='assert failed, expected value is "1" - the values should be equal';
        $this->assertEquals(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT, $get, $message);

        $this->foreignkey->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE);
        $get = $this->foreignkey->getOnDelete();
        $message = 'assert failed, expected value is "2" - the values should be equal';
        $this->assertEquals(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE, $get, $message);

        $this->foreignkey->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL);
        $get = $this->foreignkey->getOnDelete();
        $message = 'assert failed, expected value is "3" - the values should be equal';
        $this->assertEquals(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL, $get, $message);

        $this->foreignkey->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT);
        $get = $this->foreignkey->getOnDelete();
        $message = 'assert failed, expected value is "4" - the values should be equal';
        $this->assertEquals(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT, $get, $message);

        $this->foreignkey->setOnDelete(14);
        $get = $this->foreignkey->getOnDelete();
        $message = 'assert failed, expected value is "0" - only numbers between 0-4 can be set ' .
            'otherwise the default value "0" will be set';
        $this->assertEquals(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION, $get, $message);
    }

    /**
     * on-delete action
     *
     * @test
     */
    public function testOnUpdate()
    {
        $this->foreignkey->setOnUpdate(0);
        $get = $this->foreignkey->getOnUpdate();
        $this->assertEquals(0, $get, 'assert failed, expected value is "0" - the values should be equal');

        $this->foreignkey->setOnUpdate(1);
        $get = $this->foreignkey->getOnUpdate();
        $this->assertEquals(1, $get, 'assert failed, expected value is "1" - the values should be equal');

        $this->foreignkey->setOnUpdate(2);
        $get = $this->foreignkey->getOnUpdate();
        $this->assertEquals(2, $get, 'assert failed, expected value is "2" - the values should be equal');

        $this->foreignkey->setOnUpdate(3);
        $get = $this->foreignkey->getOnUpdate();
        $this->assertEquals(3, $get, 'assert failed, expected value is "3" - the values should be equal');

        $this->foreignkey->setOnUpdate(4);
        $get = $this->foreignkey->getOnUpdate();
        $this->assertEquals(4, $get, 'assert failed, expected value is "4" - the values should be equal');

        $this->foreignkey->setOnUpdate(14);
        $get = $this->foreignkey->getOnUpdate();
        $this->assertEquals(0, $get, 'assert failed, expected value is "0" - only numbers between 0-4 can be set otherwise the default value "0" will be set');
    }

    /**
     * addColumn
     *
     * @test
     */
    public function testAddColumn()
    {
        // \Yana\Db\Ddl\Table
        $newColumns = array('description', 'number', 'image');
        $add = $this->table->addColumn($newColumns[0], 'string');
        $add2 = $this->table->addColumn($newColumns[1], 'integer');
        $this->assertTrue($add instanceof \Yana\Db\Ddl\Column, 'assert failed, the value should be an instance of \Yana\Db\Ddl\Column');

        $result1 = $this->table->getColumn('number');
        $this->assertTrue($result1 instanceof \Yana\Db\Ddl\Column, 'assert failed, the value should be an instace of \Yana\Db\Ddl\Column');
        $result1 = $this->table->getColumn('gibbsganich');
        $this->assertNull($result1, '\Yana\Db\Ddl\Table if you try to get a notexisting column, you should get null as result');

        $result1 = $this->table->getColumnsByType('integer');
        $this->assertEquals(count($result1), 1, '\Yana\Db\Ddl\Table::getColumnsByType does not match');

        $result1 = $this->table->getColumns();
        $this->assertEquals(count($result1), 2, '\Yana\Db\Ddl\Table::getColumns does not match');

        $result1 = $this->table->getColumnNames();
        $this->assertTrue(in_array($newColumns[0],$result1), '\Yana\Db\Ddl\Table::getColumns does not match');
        $this->assertTrue(in_array($newColumns[1],$result1), '\Yana\Db\Ddl\Table::getColumns does not match');

        $add3 = $this->table->addColumn($newColumns[2], 'image');
        $result1 = $this->table->getFileColumns();
        $result2 = array();
        foreach ($result1 as $s)
        {
            $result2[] = $s->getName();
        }
        $this->assertFalse(in_array($newColumns[0],$result2), '\Yana\Db\Ddl\Table::getFileColumns does not match');
        $this->assertTrue(in_array($newColumns[2],$result2), '\Yana\Db\Ddl\Table::getFileColumns does not match');

        $checkprofile = $this->table->hasProfile();
        $this->assertFalse($checkprofile, 'assert failed, the tables doesnt have a profile');

        $set = $this->table->setProfile(true);
        $get = $this->table->getColumns();
        $this->assertArrayHasKey('profile_id', $get, 'assert failed, the "profile_id" should be exist in array');
        $valid2 = $this->table->hasProfile();
        $this->assertTrue($valid2, 'assert failed, the tables allready have a profile');

        $set = $this->table->setProfile(false);
        $get = $this->table->getColumns();
        $this->assertArrayNotHasKey('profile_id', $get, 'assert failed, the "profile_id" should not be exist in array');

        $authorLog = $this->table->hasAuthorLog();
        $this->assertFalse($authorLog, 'assert failed, the tables doesnt have a authorLog');

        $get1 = $this->table->hasAuthorLog();
        $get2 = $this->table->hasAuthorLog(false);
        $this->assertFalse($get1, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');
        $this->assertFalse($get2, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');

        // check if column time_modified exist - expected false
        $this->table->setAuthorLog(true, false);
        $get1 = $this->table->hasAuthorLog();
        $get2 = $this->table->hasAuthorLog(false);
        $result1 = $this->table->getColumn('user_created');
        $result2 = $this->table->getColumn('user_modified');
        $this->assertNotNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');
        $this->assertFalse($get1, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');
        $this->assertTrue($get2, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');

        // check if column time_created exist - expected true
        $this->table->setAuthorLog(true, true);
        $get1 = $this->table->hasAuthorLog();
        $get2 = $this->table->hasAuthorLog(false);
        $result1 = $this->table->getColumn('user_created');
        $result2 = $this->table->getColumn('user_modified');
        $this->assertNotNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNotNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');
        $this->assertTrue($get1, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');
        $this->assertTrue($get2, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');

        // check if column time_created exist - expected true
        $this->table->setAuthorLog(false, true);
        $result1 = $this->table->getColumn('user_created');
        $result2 = $this->table->getColumn('user_modified');
        $this->assertNotNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');

        // check if column time_created exist - expected true
        $this->table->setAuthorLog(false, false);
        $result1 = $this->table->getColumn('user_created');
        $result2 = $this->table->getColumn('user_modified');
        $this->assertNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');
    }

    /**
     * getSchemaName
     *
     * @test
     */
    public function testGetSchemaName()
    {
        // \Yana\Db\Ddl\Table
        $get = $this->table->getSchemaName();
        $this->assertNull($get, 'assert failed, expected null');
    }

    /**
     * VersionCheck
     *
     *
     * @test
     */
    public function testSetVersionCheck()
    {
        // \Yana\Db\Ddl\Table

        $get1 = $this->table->hasVersionCheck();
        $get2 = $this->table->hasVersionCheck(false);
        $this->assertFalse($get1, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');
        $this->assertFalse($get2, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');

        // check if column time_modified exist - expected false
        $this->table->setVersionCheck(true, false);
        $get1 = $this->table->hasVersionCheck();
        $get2 = $this->table->hasVersionCheck(false);
        $result1 = $this->table->getColumn('time_created');
        $result2 = $this->table->getColumn('time_modified');
        $this->assertNotNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');
        $this->assertFalse($get1, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');
        $this->assertTrue($get2, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');

        // check if column time_created exist - expected true
        $this->table->setVersionCheck(true, true);
        $get1 = $this->table->hasVersionCheck();
        $get2 = $this->table->hasVersionCheck(false);
        $result1 = $this->table->getColumn('time_created');
        $result2 = $this->table->getColumn('time_modified');
        $this->assertNotNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNotNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');
        $this->assertTrue($get1, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');
        $this->assertTrue($get2, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');

        // check if column time_created exist - expected true
        $this->table->setVersionCheck(false, true);
        $result1 = $this->table->getColumn('time_created');
        $result2 = $this->table->getColumn('time_modified');
        $this->assertNotNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');

        // check if column time_created exist - expected true
        $this->table->setVersionCheck(false, false);
        $result1 = $this->table->getColumn('time_created');
        $result2 = $this->table->getColumn('time_modified');
        $this->assertNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');
    }

    /**
     * drop non-existing column
     *
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     *
     * @test
     */
    public function testdropColumnNotFoundException()
    {
        // \Yana\Db\Ddl\Table
        $this->table->dropColumn('test');
    }

    /**
     * Foreign-key
     *
     * @test
     */
    public function testAddForeignKey()
    {
        // \Yana\Db\Ddl\Table
        $table = $this->database->addTable('table');
        $table_target = $this->database->addTable('table_target');
        $table_target->addColumn('testcolumn_target','integer');
        $table_target->setPrimaryKey('testcolumn_target');
        $table->addColumn('testcolumn','integer');
        $fk = $table->addForeignKey('table_target', 'cfkey');
        $fk->setColumn('testcolumn');
        $getAll = $table->getForeignKeys();
        $this->assertInternalType('array', $getAll, 'assert failed the values is not an array');

        foreach($getAll as $key =>$value)
        {
            $this->assertTrue($value instanceof \Yana\Db\Ddl\ForeignKey, 'assert failed, the value should be an instance of \Yana\Db\Ddl\ForeignKey');
        }

        $cfkey = $table->getForeignKey('cfkey');

        $this->assertNotNull($cfkey, 'ForeignKey was not retrieved by constraint Name');
    }

    /**
     * Primary-key
     *
     * @test
     */
    public function testGetPrimaryKey()
    {
        $this->assertNull($this->table->getPrimaryKey());
    }

    /**
     * Primary-key with non-existing column
     *
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     *
     * @test
     */
    public function testSetPrimaryKeyNotFoundException()
    {
        // \Yana\Db\Ddl\Table
        $this->table->setPrimaryKey('no_column');
    }

    /**
     * Inheritance
     *
     * @test
     */
    public function testInheritance()
    {
        // \Yana\Db\Ddl\Table
        $this->table->setInheritance('inheritance');
        $get = $this->table->getInheritance();
        $this->assertEquals('inheritance', $get, 'assert failed, the values should be equal');
        $this->table->setInheritance('');
        $get = $this->table->getInheritance();
        $this->assertNull($get, 'assert failed, expected null');
    }

    /**
     * addIndex
     *
     * @test
     */
    public function testAddIndex()
    {
        $this->table->addIndex('test');
        $index = $this->table->getIndex('test');
        $this->assertTrue($index instanceof \Yana\Db\Ddl\Index, 'Method getIndex() should return \Yana\Db\Ddl\Index objects.');
        $index = $this->table->getIndex('non-existing-index');
        $this->assertNull($index, 'Search for non-existing index must return NULL.');

        $this->table->addIndex('othertest');
        $index = $this->table->getIndex('othertest');
        $this->assertTrue($index instanceof \Yana\Db\Ddl\Index, 'Method getIndex() should return \Yana\Db\Ddl\Index objects.');

        // add two more anonymous indexes
        $this->table->addIndex();
        $this->table->addIndex();

        $indexes = $this->table->getIndexes();
        $this->assertArrayHasKey('test', $indexes, 'Expected index "test" not found.');
        $this->assertArrayHasKey('othertest', $indexes, 'Expected index "othertest" not found.');
        $this->assertArrayHasKey(0, $indexes, 'Anonymous index "0" not found.');
        $this->assertArrayHasKey(1, $indexes, 'Anonymous index "1" not found.');
        $this->assertEquals(4, count($indexes), 'Unexpected number of indexes.');
    }

    /**
     * IndexAlreadyExistsException
     * @test
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     */
    public function testAddIndexAlreadyExistsException()
    {
        $this->table->addColumn('column', 'string');
        try {
            // supposed to succeed
            $this->table->addIndex('column', 'index');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->table->addIndex('column', 'index');
    }

    /**
     * ColumnAlreadyExistsException
     * @test
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     */
    public function testAddColumnAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->table->addColumn('column', 'string');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->table->addColumn('column', 'string');
    }

    /**
     * Trigger
     *
     * @test
     */
    public function testTrigger()
    {

        $testArray1 = array("sometrigger 1", "sometrigger 2", "sometrigger 3");
        // \Yana\Db\Ddl\Table

        // \Yana\Db\Ddl\Table::setTriggerBeforeInsert
        $trigger = $this->table->setTriggerBeforeInsert($testArray1[0]);
        $this->assertTrue($trigger->isBefore(), "\Yana\Db\Ddl\Trigger::isBefore returns wrong value");
        $this->assertFalse($trigger->isAfter(), "\Yana\Db\Ddl\Trigger::isAfter returns wrong value");
        $this->assertFalse($trigger->isInstead(), "\Yana\Db\Ddl\Trigger::isInstead returns wrong value");
        $this->assertTrue($trigger->isInsert(), "\Yana\Db\Ddl\Trigger::isInsert returns wrong value");
        $this->assertFalse($trigger->isUpdate(), "\Yana\Db\Ddl\Trigger::isUpdate returns wrong value");
        $this->assertFalse($trigger->isDelete(), "\Yana\Db\Ddl\Trigger::isDelete returns wrong value");
        $this->table->setTriggerBeforeInsert($testArray1[1]);
        $this->table->setTriggerBeforeInsert($testArray1[2]);
        $get = $this->table->getTriggerBeforeInsert();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, the arrays should be equal');
        $get = $this->table->getTriggerBeforeInsert('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->table);
        $this->table = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerBeforeUpdate
        $this->table->setTriggerBeforeUpdate($testArray1[0]);
        $trigger = $this->table->setTriggerBeforeUpdate($testArray1[1]);
        $this->table->setTriggerBeforeUpdate($testArray1[2]);
        $get = $this->table->getTriggerBeforeUpdate();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerBeforeUpdate, the arrays should be equal');
        $get = $this->table->getTriggerBeforeUpdate('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->table);
        $this->table = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerBeforeDelete
        $this->table->setTriggerBeforeDelete($testArray1[0]);
        $this->table->setTriggerBeforeDelete($testArray1[1]);
        $this->table->setTriggerBeforeDelete($testArray1[2]);
        $get = $this->table->getTriggerBeforeDelete();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerBeforeDelete, the arrays should be equal');
        $get = $this->table->getTriggerBeforeDelete('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->table);
        $this->table = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerAfterInsert
        $this->table->setTriggerAfterInsert($testArray1[0]);
        $this->table->setTriggerAfterInsert($testArray1[1]);
        $this->table->setTriggerAfterInsert($testArray1[2]);
        $get = $this->table->getTriggerAfterInsert();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerAfterInsert, the arrays should be equal');
        $get = $this->table->getTriggerAfterInsert('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->table);
        $this->table = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerAfterUpdate
        $this->table->setTriggerAfterUpdate($testArray1[0]);
        $trigger = $this->table->setTriggerAfterUpdate($testArray1[1]);
        $this->assertFalse($trigger->isBefore(), "\Yana\Db\Ddl\Trigger::isBefore returns wrong value");
        $this->assertTrue($trigger->isAfter(), "\Yana\Db\Ddl\Trigger::isAfter returns wrong value");
        $this->assertFalse($trigger->isInstead(), "\Yana\Db\Ddl\Trigger::isInstead returns wrong value");
        $this->assertFalse($trigger->isInsert(), "\Yana\Db\Ddl\Trigger::isInsert returns wrong value");
        $this->assertTrue($trigger->isUpdate(), "\Yana\Db\Ddl\Trigger::isUpdate returns wrong value");
        $this->assertFalse($trigger->isDelete(), "\Yana\Db\Ddl\Trigger::isDelete returns wrong value");
        $this->table->setTriggerAfterUpdate($testArray1[2]);
        $get = $this->table->getTriggerAfterUpdate();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerAfterUpdate, the arrays should be equal');
        $get = $this->table->getTriggerAfterUpdate('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->table);
        $this->table = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerAfterDelete
        $this->table->setTriggerAfterDelete($testArray1[0]);
        $this->table->setTriggerAfterDelete($testArray1[1]);
        $this->table->setTriggerAfterDelete($testArray1[2]);
        $get = $this->table->getTriggerAfterDelete();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerAfterDelete, the arrays should be equal');
        $get = $this->table->getTriggerAfterDelete('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->table);
        $this->table = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerInsteadInsert
        $this->table->setTriggerInsteadInsert($testArray1[0]);
        $this->table->setTriggerInsteadInsert($testArray1[1]);
        $this->table->setTriggerInsteadInsert($testArray1[2]);
        $get = $this->table->getTriggerInsteadInsert();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerInsteadInsert, the arrays should be equal');
        $get = $this->table->getTriggerInsteadInsert('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerInsteadInsert, expected null - trigger "mysql" does not exist');
        unset ($this->table);
        $this->table = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerInsteadUpdate
        $this->table->setTriggerInsteadUpdate($testArray1[0]);
        $this->table->setTriggerInsteadUpdate($testArray1[1]);
        $this->table->setTriggerInsteadUpdate($testArray1[2]);
        $get = $this->table->getTriggerInsteadUpdate();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerInsteadUpdate, the arrays should be equal');
        $get = $this->table->getTriggerInsteadUpdate('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerInsteadUpdate, expected null - trigger "mysql" does not exist');
        unset ($this->table);
        $this->table = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerInsteadDelete
        $this->table->setTriggerInsteadDelete($testArray1[0]);
        $this->table->setTriggerInsteadDelete($testArray1[1]);
        $this->table->setTriggerInsteadDelete($testArray1[2]);
        $get = $this->table->getTriggerInsteadDelete();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerInsteadDelete, the arrays should be equal');
        $get = $this->table->getTriggerInsteadDelete('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerInsteadDelete, expected null - trigger "mysql" does not exist');
        unset ($this->table);
        $this->table = new \Yana\Db\Ddl\Table('table');


        // set the same with name

//        // \Yana\Db\Ddl\Table::setTriggerBeforeInsert
//        $this->table->setTriggerBeforeInsert($testArray1[0], 'generic', 'test1');
//        $this->table->setTriggerBeforeInsert($testArray1[1], 'generic', 'test2');
//        $this->table->setTriggerBeforeInsert($testArray1[2], 'generic', 'test3');
//        $get = $this->table->getTriggerBeforeInsert();
//        $this->assertEquals('sometrigger 1', $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, the value "sometrigger 1" must be in array');
//        $this->assertEquals('sometrigger 2', $testArray1[1], '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, the value "sometrigger 2" must be in array');
//        $this->assertEquals('sometrigger 3', $testArray1[2], '\Yana\Db\Ddl\Table::setTriggerBeforeInsert , the value "sometrigger 3" must be in array');
//        unset ($this->table);
//        $this->table = new \Yana\Db\Ddl\Table('table');
//
//        // \Yana\Db\Ddl\Table::setTriggerBeforeUpdate
//        $this->table->setTriggerBeforeUpdate($testArray1[0], 'generic', 'test1');
//        $this->table->setTriggerBeforeUpdate($testArray1[1], 'generic', 'test2');
//        $this->table->setTriggerBeforeUpdate($testArray1[2], 'generic', 'test3');
//        $get = $this->table->getTriggerBeforeUpdate();
//        $this->assertEquals('sometrigger 1', $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerBeforeUpdate, the value "sometrigger 1" must be in array');
//        $this->assertEquals('sometrigger 2', $testArray1[1], '\Yana\Db\Ddl\Table::setTriggerBeforeUpdate, the value "sometrigger 2" must be in array');
//        $this->assertEquals('sometrigger 3', $testArray1[2], '\Yana\Db\Ddl\Table::setTriggerBeforeUpdate, the value "sometrigger 3" must be in array');
//        unset ($this->table);
//        $this->table = new \Yana\Db\Ddl\Table('table');
//
//        // \Yana\Db\Ddl\Table::setTriggerBeforeDelete
//        $this->table->setTriggerBeforeDelete($testArray1[0], 'generic', 'test1');
//        $this->table->setTriggerBeforeDelete($testArray1[1], 'generic', 'test2');
//        $this->table->setTriggerBeforeDelete($testArray1[2], 'generic', 'test3');
//        $get = $this->table->getTriggerBeforeDelete();
//        $this->assertEquals('sometrigger 1', $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerBeforeDelete, the value "sometrigger 1" must be in array');
//        $this->assertEquals('sometrigger 2', $testArray1[1], '\Yana\Db\Ddl\Table::setTriggerBeforeDelete, the value "sometrigger 2" must be in array');
//        $this->assertEquals('sometrigger 3', $testArray1[2], '\Yana\Db\Ddl\Table::setTriggerBeforeDelete, the value "sometrigger 3" must be in array');
//        unset ($this->table);
//        $this->table = new \Yana\Db\Ddl\Table('table');
//
//        // \Yana\Db\Ddl\Table::setTriggerAfterInsert
//        $this->table->setTriggerAfterInsert($testArray1[0], 'generic', 'test1');
//        $this->table->setTriggerAfterInsert($testArray1[1], 'generic', 'test2');
//        $this->table->setTriggerAfterInsert($testArray1[2], 'generic', 'test3');
//        $get = $this->table->getTriggerAfterInsert();
//        $this->assertEquals('sometrigger 1', $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerAfterInsert, the value "sometrigger 1" must be in array');
//        $this->assertEquals('sometrigger 2', $testArray1[1], '\Yana\Db\Ddl\Table::setTriggerAfterInsert, the value "sometrigger 2" must be in array');
//        $this->assertEquals('sometrigger 3', $testArray1[2], '\Yana\Db\Ddl\Table::setTriggerAfterInsert, the value "sometrigger 3" must be in array');
//        unset ($this->table);
//        $this->table = new \Yana\Db\Ddl\Table('table');
//
//        // \Yana\Db\Ddl\Table::setTriggerAfterUpdate
//        $this->table->setTriggerAfterUpdate($testArray1[0], 'generic', 'test1');
//        $this->table->setTriggerAfterUpdate($testArray1[1], 'generic', 'test2');
//        $this->table->setTriggerAfterUpdate($testArray1[2], 'generic', 'test3');
//        $get = $this->table->getTriggerAfterUpdate();
//        $this->assertEquals('sometrigger 1', $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerAfterUpdate, the value "sometrigger 1" must be in array');
//        $this->assertEquals('sometrigger 2', $testArray1[1], '\Yana\Db\Ddl\Table::setTriggerAfterUpdate, the value "sometrigger 2" must be in array');
//        $this->assertEquals('sometrigger 3', $testArray1[2], '\Yana\Db\Ddl\Table::setTriggerAfterUpdate, the value "sometrigger 3" must be in array');
//        unset ($this->table);
//        $this->table = new \Yana\Db\Ddl\Table('table');
//
//        // \Yana\Db\Ddl\Table::setTriggerAfterDelete
//        $this->table->setTriggerAfterDelete($testArray1[0], 'generic', 'test1');
//        $this->table->setTriggerAfterDelete($testArray1[1], 'generic', 'test2');
//        $this->table->setTriggerAfterDelete($testArray1[2], 'generic', 'test3');
//        $get = $this->table->getTriggerAfterDelete();
//        $this->assertEquals('sometrigger 1', $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerAfterDelete, the value "sometrigger 1" must be in array');
//        $this->assertEquals('sometrigger 2', $testArray1[1], '\Yana\Db\Ddl\Table::setTriggerAfterDelete, the value "sometrigger 2" must be in array');
//        $this->assertEquals('sometrigger 3', $testArray1[2], '\Yana\Db\Ddl\Table::setTriggerAfterDelete, the value "sometrigger 3" must be in array');
//
//         // \Yana\Db\Ddl\Trigger::setTriggerBeforeInsert
//        $this->trigger->setTriggerBeforeInsert($testArray1[0]);
//        $get = $this->trigger->getTriggerBeforeInsert();
//        $this->assertEquals($testArray1[0], $get, '\Yana\Db\Ddl\Trigger::setTriggerBeforeInsert, the values must be equal');
//
//        $this->trigger->setTriggerBeforeInsert();
//        $get = $this->trigger->getTriggerBeforeInsert();
//        $this->assertNull($get, '\Yana\Db\Ddl\Trigger::setTriggerBeforeInsert, expected null - trigger is not set');
//
//        // \Yana\Db\Ddl\Trigger::setTriggerBeforeUpdate
//        $this->trigger->setTriggerBeforeUpdate($testArray1[0], 'generic', 'test1');
//        $get = $this->trigger->getTriggerBeforeUpdate();
//        $this->assertEquals($testArray1[0], $get, '\Yana\Db\Ddl\Trigger::setTriggerBeforeUpdate the values must be equal');
//
//        $this->trigger->setTriggerBeforeUpdate();
//        $get = $this->trigger->getTriggerBeforeUpdate();
//        $this->assertNull($get, '\Yana\Db\Ddl\Trigger::setTriggerBeforeUpdate, expected null - trigger is not set');
//
//        // \Yana\Db\Ddl\Trigger::setTriggerBeforeDelete
//        $this->trigger->setTriggerBeforeDelete($testArray1[0], 'generic', 'test1');
//        $get = $this->trigger->getTriggerBeforeDelete();
//        $this->assertEquals($testArray1[0], $get, '\Yana\Db\Ddl\Trigger::setTriggerBeforeDelete the values must be equal');
//
//        $this->trigger->setTriggerBeforeDelete();
//        $get = $this->trigger->getTriggerBeforeDelete();
//        $this->assertNull($get, '\Yana\Db\Ddl\Trigger::setTriggerBeforeDelete, expected null - trigger is not set');
//
//        // \Yana\Db\Ddl\Trigger::setTriggerAfterInsert
//        $this->trigger->setTriggerAfterInsert($testArray1[0], 'generic', 'test1');
//        $get = $this->trigger->getTriggerAfterInsert();
//        $this->assertEquals($testArray1[0], $get, '\Yana\Db\Ddl\Trigger::setTriggerAfterInsert the values must be equal');
//
//        $this->trigger->setTriggerAfterInsert();
//        $get = $this->trigger->getTriggerAfterInsert();
//        $this->assertNull($get, '\Yana\Db\Ddl\Trigger::setTriggerAfterInsert, expected null - trigger is not set');
//
//        // \Yana\Db\Ddl\Trigger::setTriggerAfterUpdate
//        $this->trigger->setTriggerAfterUpdate($testArray1[0], 'generic', 'test1');
//        $get = $this->trigger->getTriggerAfterUpdate();
//        $this->assertEquals($testArray1[0], $get, '\Yana\Db\Ddl\Trigger::setTriggerAfterUpdate the values must be equal');
//
//        $this->trigger->setTriggerAfterUpdate();
//        $get = $this->trigger->getTriggerAfterUpdate();
//        $this->assertNull($get, '\Yana\Db\Ddl\Trigger::setTriggerAfterUpdate, expected null - trigger is not set');
//
//        // \Yana\Db\Ddl\Trigger::setTriggerAfterDelete
//        $this->trigger->setTriggerAfterDelete($testArray1[0], 'generic', 'test1');
//        $get = $this->trigger->getTriggerAfterDelete();
//        $this->assertEquals($testArray1[0], $get, '\Yana\Db\Ddl\Trigger::setTriggerAfterDelete the values must be equal');
//
//        $this->trigger->setTriggerAfterDelete();
//        $get = $this->trigger->getTriggerAfterDelete();
//        $this->assertNull($get, '\Yana\Db\Ddl\Trigger::setTriggerAfterDelete, expected null - trigger is not set');
    }

    /**
     * ChangeLog
     *
     * @test
     */
    public function testChangeLog()
    {
        $result = $this->database->getChangeLog();
        $this->assertTrue($result instanceof \Yana\Db\Ddl\ChangeLog, 'assert failed, \Yana\Db\Ddl\Database : the value should be an instance of \Yana\Db\Ddl\ChangeLog');
    }

    /**
     * getTableByForeignKey
     *
     * @test
     */
    public function testGetTableByForeignKey()
    {
        // \Yana\Db\Ddl\Table

        // create a target-table
        $newTableA = $this->database->addTable("someTable");
        $newTableB = $this->database->addTable("otherTable");
        $ColumnA = $newTableA->addColumn("firstCol", "integer");
        $ColumnB = $newTableB->addColumn("someCol", "integer");
        $ColumnC = $newTableB->addColumn("someMoreCol", "integer");
        $newTableA->setPrimaryKey("firstCol");
        $fk = $newTableB->addForeignKey("someTable");
        $fk->setColumn('someCol');
        $get = $newTableB->getTableByForeignKey('someCol');
        $this->assertEquals('sometable', $get, 'assert failed, Function getTableByForeignKey() must return name of target table.');
    }

    /**
     * getTableByForeignKeyInvalidArgumentException
     *
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     *
     * @test
     */
    public function testGetTableByForeignKeyInvalidArgumentException()
    {
        // \Yana\Db\Ddl\Table
        $this->table->getTableByForeignKey('nonexist');
    }

    /**
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     *
     * @test
     */
    public function testgetColumnByForeignKeyInvalidArgumentException()
    {
        // \Yana\Db\Ddl\Table
        $this->table->getColumnByForeignKey('foo_bar');
    }

    /**
     * @test
     */
    public function testgetColumnByForeignKey()
    {
         // create a da tabase with tables (columns)
        $db = new \Yana\Db\Ddl\Database('foobar');

        /* create table "foo_department" and columns */
        $table = $db->addTable('foo_department');
        $id = $table->addColumn('id', 'integer');
        $id->setAutoIncrement(true);
        $table->setPrimaryKey('id');

        /* create table "foo_department" and columns */
        $table = $db->addTable('bar_department');
        $id = $table->addColumn('id', 'integer');
        $id->setAutoIncrement(true);
        $table->setPrimaryKey('id');

        $fk = $table->addColumn('foo_department_id', 'integer');
        $fk = $table->addForeignKey('foo_department');
        $fk->setColumn('foo_department_id');

        // \Yana\Db\Ddl\Table
        $result = $table->getColumnByForeignKey('foo_department_id');
        $this->assertTrue($result instanceof \Yana\Db\Ddl\Column, 'assert failed, the expected value should be an instance of \Yana\Db\Ddl\Column');
    }

    /**
     * @test
     */
    public function testIsModified()
    {
        $this->assertFalse($this->database->isModified());
    }

    /**
     * @test
     */
    public function testSetModified()
    {
        $this->assertTrue($this->database->setModified(true)->isModified());
        $this->assertFalse($this->database->setModified(false)->isModified());
    }

    /**
     * @test
     */
    public function testLoadIncludes()
    {
        $this->assertFalse($this->database->isTable('Test'));
        $this->database->addInclude("test");
        $this->assertNull($this->database->loadIncludes());
        $this->assertTrue($this->database->isTable('Test'));
        $this->assertTrue($this->database->isView('Test_view'));
        $this->assertTrue($this->database->isForm('Test_default'));
        $this->assertTrue($this->database->isFunction('Test_function'));
        $this->assertTrue($this->database->isSequence('Test_sequence'));
    }

    /**
     * @test
     */
    public function testLoadIncludesEmpty()
    {
        $database = new \Yana\Db\Ddl\Database();
        $database->addInclude("no-such-file");
        $this->assertNull($database->loadIncludes());
    }

    /**
     * @test
     */
    public function testLoadIncludesSame()
    {
        $this->database->addInclude("check");
        $this->assertNull($this->database->loadIncludes());
    }

    /**
     * @test
     */
    public function testLoadIncludesTwice()
    {
        $this->assertFalse($this->database->isTable('Test'));
        $this->database->addInclude("test");
        $this->assertNull($this->database->loadIncludes());
        $this->assertNull($this->database->loadIncludes());
        $this->assertTrue($this->database->isTable('Test'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testLoadIncludesNotFoundException()
    {
        $this->database->addInclude("no-such-file");
        $this->database->loadIncludes();
    }

}

?>