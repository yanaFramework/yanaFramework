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
declare(strict_types=1);

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
    protected $object;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        chdir(CWD . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

        \Yana\Db\Ddl\Database::setCache(new \Yana\Data\Adapters\ArrayAdapter());
        $this->object = new \Yana\Db\Ddl\Database('Database', CWD . '/resources/check.db.xml');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->object->setModified(true); // setting the file to modified forces the instance cache to be cleared
        unset($this->object); // this doesn't kill all references, UNLESS the files was previously set to modified
        chdir(CWD);
    }

    /**
     * @test
     */
    public function testGetNameFromPath()
    {
        $this->object = new \Yana\Db\Ddl\Database('', CWD . '/resources/check.db.xml');
        $this->assertSame('check', $this->object->getName());
    }

    /**
     * Included another database file must not overwrite defined forms and tables.
     *
     * @test
     */
    public function testLoadIncludesOverwriteCheck()
    {
        $xddl = new \Yana\Files\XDDL(CWD . '/resources/testinclude.db.xml');
        $this->object = $xddl->toDatabase();
        $this->assertSame(array('testinclude2'), $this->object->getIncludes());
        $this->assertFalse($this->object->isForm('a'));
        $this->assertTrue($this->object->isForm('b'));
        $this->assertTrue($this->object->isForm('c'));
        $this->assertTrue($this->object->isTable('a'));
        $this->assertTrue($this->object->isTable('b'));
        $this->assertTrue($this->object->isTable('c'));
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
        $this->object = $xddl->toDatabase();
        $this->assertSame(array('testinclude2'), $this->object->getIncludes());
        $this->assertFalse($this->object->isForm('a'));
        $this->assertTrue($this->object->isForm('b'));
        $this->assertTrue($this->object->isForm('c'));
        $this->assertTrue($this->object->isTable('a'));
        $this->assertTrue($this->object->isTable('b'));
        $this->assertTrue($this->object->isTable('c'));
    }

    /**
     * @test
     */
    public function testTitle()
    {
        $testTitle = "some Title";
        $this->object->setTitle($testTitle);
        $getTitle = $this->object->getTitle();
        $this->assertEquals($getTitle, $testTitle, get_class($this->object) . ': title assignment failed.');

        $this->object->setTitle();
        $getTitle = $this->object->getTitle();
        $this->assertNull($getTitle, get_class($this->object) . ': unable to unset title.');
    }

    /**
     * @test
     */
    public function testDescription()
    {
        $this->object->setDescription('description');
        $result = $this->object->getDescription();
        $this->assertEquals('description', $result, 'expected value is "description"  - the values should be equal');

        $this->object->setDescription('');
        $result = $this->object->getDescription();
        $this->assertNull($result, 'the description is expected null');
    }

    /**
     * readonly
     *
     * @test
     */
    public function testReadonly()
    {
       // ddl database
       $this->object->setReadonly(true);
       $result = $this->object->isReadonly();
       $this->assertTrue($result, 'assert failed, \Yana\Db\Ddl\Database : expected true - setReadonly was set with true');

       $this->object->setReadonly(false);
       $result = $this->object->isReadonly();
       $this->assertFalse($result, 'assert failed, \Yana\Db\Ddl\Database : expected false - setReadonly was set with false');
    }

    /**
     * @test
     */
    public function testIsForeignKey()
    {
        // create a target-table
        $newTableA = $this->object->addTable("someTable");
        $newTableB = $this->object->addTable("otherTable");
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
     * Includes
     *
     * @test
     */
    public function testIncludes()
    {
        $array = array('first');
        // ddl database
        $this->object->setIncludes($array);
        $result = $this->object->getIncludes();
        $this->assertEquals($array, $result, 'assert failed, \Yana\Db\Ddl\Database : expected an array with one entire "first", values should be equal');
        $next = 'second';
        $add = $this->object->addInclude($next);
        $result = $this->object->getIncludes();
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
        $this->object->setCharset('charset');
        $result = $this->object->getCharset();
        $this->assertEquals('charset', $result, 'assert failed, \Yana\Db\Ddl\Database : expected "charset" as value');

        $this->object->setCharset();
        $result = $this->object->getCharset();
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
        $this->object->setDataSource('dataSource');
        $result = $this->object->getDataSource();
        $this->assertEquals('dataSource', $result, 'assert failed, \Yana\Db\Ddl\Database : expected "dataSource" as value');

        $this->object->setDataSource();
        $result = $this->object->getDataSource();
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
        $valid = $this->object->isTable('newtable');
        $this->assertFalse($valid, 'assert failed, expected false, the value "newtable" is not a table');

        $add = $this->object->addTable('newtable');
        $this->assertTrue($add instanceof \Yana\Db\Ddl\Table, 'assert failed, the value should be an instanceof \Yana\Db\Ddl\Table');
        $getAll = $this->object->getTables();
        $this->assertArrayHasKey('newtable', $getAll, 'assert failed, the value should be match a key in array');
        $result = $this->object->getTable('newtable');
        $this->assertInternalType('object', $result, 'assert failed, the value should be from type object');

        $valid = $this->object->isTable('newtable');
        $this->assertTrue($valid, 'assert failed, expected true, the value "newtable" is a Table');

        $newTable = $this->object->addTable("someTable");
        $retTable = $this->object->getTable("someTable");
        $this->assertNotNull($retTable, 'getTable : expected null, non table is set');
        $retTable = $this->object->getTable("otherTable");
        $this->assertNull($retTable, 'getTable : expected null, non table is set');

        $tables = $this->object->getTableNames();
        $this->assertContains('newtable', $tables, 'assert failed, the value should be match a key in array');
        $this->assertContains('sometable', $tables, 'assert failed, the value should be match a key in array');

        // null expected
        $drop = $this->object->dropTable('newtable');
        $get = $this->object->getTable('newtable');
        $this->assertNull($get, 'assert failed, expected null - table was dropt before');
    }

    /**
     * @test
     */
    public function test__isset()
    {
        $this->assertFalse(isset($this->object->noSuchThing));
        $this->object->addTable('noSuchThing');
        $this->assertTrue(isset($this->object->noSuchThing));
    }

    /**
     * @test
     */
    public function test__get()
    {
        $this->assertNull($this->object->noSuchThing);
    }

    /**
     * @test
     */
    public function test__getTable()
    {
        $this->object->addTable('magicTable');
        $this->assertTrue($this->object->magicTable instanceof \Yana\Db\Ddl\Table);

        // magic Column
        $this->object->magicTable->addColumn('magicColumn', 'integer');
        $this->assertTrue($this->object->magicTable->magicColumn instanceof \Yana\Db\Ddl\Column);
    }

    /**
     * @test
     */
    public function test__getForm()
    {
        $this->object->addForm('magicForm');
        $this->assertTrue($this->object->magicForm instanceof \Yana\Db\Ddl\Form);
    }

    /**
     * @test
     */
    public function test__getView()
    {
        $this->object->addView('magicView');
        $this->assertTrue($this->object->magicView instanceof \Yana\Db\Ddl\Views\View);
    }

    /**
     * @test
     */
    public function test__getFunction()
    {
        $this->object->addFunction('magicFunction');
        $this->assertTrue($this->object->magicFunction instanceof \Yana\Db\Ddl\Functions\Definition);
    }

    /**
     * @test
     */
    public function test__getSequence()
    {
        $this->object->addSequence('magicSequence');
        $this->assertTrue($this->object->magicSequence instanceof \Yana\Db\Ddl\Sequence);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     */
    public function testAddTableAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->object->addTable('table');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->object->addTable('table');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testDropTableNotFoundException()
    {
        $this->object->dropTable('no-such-table');
    }

    /**
     * @test
     */
    public function testView()
    {
        // DDL Database
        $valid = $this->object->isView('qwerty');
        $this->assertFalse($valid, 'assert failed, the value should be false, "qwerty" is not a view');

        $add = $this->object->addView('qwerty');
        $this->assertInternalType('object', $add, 'assert failed, the value should be from type object');
        $this->assertEquals('qwerty', $add->getName(), 'assert failed, the values should be equal, "qwerty" is a view');

        $add = $this->object->addView('trewq');
        $this->assertInternalType('object', $add, 'assert failed, the value should be from type object');
        $this->assertEquals('trewq', $add->getName(), 'assert failed, the values should be equal, "trewq" is a view');

        $get = $this->object->getView('qwerty');
        $this->assertInternalType('object', $get, 'assert failed, the value should be from type object');
        $this->assertEquals('qwerty', $get->getName(), 'assert failed, the values should be equal, the name of the view should be the same as expected');

        $getAll = $this->object->getViews();
        $this->assertInternalType('array', $getAll, 'assert failed, the value should be from type array');
        $this->assertArrayHasKey('qwerty', $getAll, 'assert failed, the value should be match a entry in array');
        $this->assertArrayHasKey('trewq', $getAll, 'assert failed, the value should be match a entry in array');

        $getNames = $this->object->getViewNames();
        $this->assertInternalType('array', $getNames, 'assert failed, the values should be from type array');
        $this->assertTrue(in_array('qwerty', $getNames), 'assert failed, the value should be match a entry in array');
        $this->assertTrue(in_array('trewq', $getNames), 'assert failed, the value should be match a entry in array');

        $valid = $this->object->isView('qwerty');
        $this->assertTrue($valid, 'assert failed, the value should be true');

        $drop = $this->object->dropView('qwerty');
        $nonexist = $this->object->getView('qwerty');
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
            $this->object->addView('view');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->object->addView('view');
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
        $this->object->dropView('a');
    }

    /**
     * Function
     *
     * @test
     */
    public function testSetFunction()
    {
        // DDL Database
        $valid = $this->object->isFunction('qwerty');
        $this->assertFalse($valid, 'assert failed, "\Yana\Db\Ddl\Database" the value should be false, "qwerty" is not a function');

        $add = $this->object->addFunction('qwerty');
        $this->assertInternalType('object', $add, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('qwerty', $add->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, "qwerty" is a function');

        $add = $this->object->addFunction('trewq');
        $this->assertInternalType('object', $add, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('trewq', $add->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $get = $this->object->getFunction('qwerty');
        $this->assertInternalType('object', $get, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('qwerty', $get->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $getAll = $this->object->getFunctions();
        $this->assertInternalType('array', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type array');
        $this->assertArrayHasKey('qwerty', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');
        $this->assertArrayHasKey('trewq', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');

        $getNames = $this->object->getFunctionNames();
        $this->assertInternalType('array', $getNames, 'assert failed, "\Yana\Db\Ddl\Database" the values should be from type array');
        $this->assertTrue(in_array('qwerty', $getNames), 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');
        $this->assertTrue(in_array('trewq', $getNames), 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');

        $valid = $this->object->isFunction('qwerty');
        $this->assertTrue($valid, 'assert failed, "\Yana\Db\Ddl\Database" the value should be true, "qwerty" is a function');

        $drop = $this->object->dropFunction('qwerty');
        $nonexist = $this->object->getFunction('qwerty');
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
            $this->object->addFunction('function');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->object->addFunction('function');
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
        $this->object->dropFunction('gert');
    }

    /**
     * Sequence
     *
     * @test
     */
    public function testSequence()
    {
        // DDL Database
        $valid = $this->object->isSequence('qwerty');
        $this->assertFalse($valid, 'assert failed, "\Yana\Db\Ddl\Database" the value should be false, "qwerty" is not a sequence');

        $add = $this->object->addSequence('qwerty');
        $this->assertInternalType('object', $add, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('qwerty', $add->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $add = $this->object->addSequence('trewq');
        $this->assertInternalType('object', $add, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('trewq', $add->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $get = $this->object->getSequence('qwerty');
        $this->assertInternalType('object', $get, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('qwerty', $get->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $getAll = $this->object->getSequences();
        $this->assertInternalType('array', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type array');
        $this->assertArrayHasKey('qwerty', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');
        $this->assertArrayHasKey('trewq', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');

        $getNames = $this->object->getSequenceNames();
        $this->assertInternalType('array', $getNames, 'assert failed, "\Yana\Db\Ddl\Database" the values should be from type array');
        $this->assertTrue(in_array('qwerty', $getNames), 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');
        $this->assertTrue(in_array('trewq', $getNames), 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');

        $valid = $this->object->isSequence('qwerty');
        $this->assertTrue($valid, 'assert failed, "\Yana\Db\Ddl\Database" the value should be true, "qwerty" is a sequence');

        $drop = $this->object->dropSequence('qwerty');
        $nonexist = $this->object->getSequence('qwerty');
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
            $this->object->addSequence('sequence');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->object->addSequence('sequence');
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
        $this->object->dropSequence('no_sequence');
    }

    /**
     * dropInit
     *
     * @test
     */
    public function testDropInit()
    {
        // ddl database
        $this->object->dropInit();
        $init = $this->object->getInit();
        $this->assertTrue(empty($init), 'Initialization list should be empty after droping contents');
    }

    /**
     * addInit
     *
     * @test
     */
    public function testAddInit()
    {
        $get = $this->object->getInit('oracle');
        $this->assertInternalType('array', $get, 'assert failed, the value should be from type array');
        $this->assertEquals(0, count($get), 'assert failed, the values should be equal');

        $dbms = 'mysql';
        $sql = 'select * from users';
        $this->object->addInit($sql, $dbms);
        $get = $this->object->getInit($dbms);
        $this->assertEquals($sql, $get[0], 'assert failed, the values should be equal');

        $get = $this->object->getInit('oracle');
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
        $valid = $this->object->isForm('qwerty');
        $this->assertFalse($valid, 'assert failed, "\Yana\Db\Ddl\Database" the value should be false, "qwerty" is not a form');

        $add = $this->object->addForm('qwerty');
        $this->assertInternalType('object', $add, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('qwerty', $add->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $add = $this->object->addForm('trewq');
        $this->assertInternalType('object', $add, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('trewq', $add->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $get = $this->object->getForm('qwerty');
        $this->assertInternalType('object', $get, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type object');
        $this->assertEquals('qwerty', $get->getName(), 'assert failed, "\Yana\Db\Ddl\Database" the values should be equal, the name of the view should be the same as expected');

        $getAll = $this->object->getForms();
        $this->assertInternalType('array', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be from type array');
        $this->assertArrayHasKey('qwerty', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');
        $this->assertArrayHasKey('trewq', $getAll, 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');

        $getNames = $this->object->getFormNames();
        $this->assertInternalType('array', $getNames, 'assert failed, "\Yana\Db\Ddl\Database" the values should be from type array');
        $this->assertTrue(in_array('qwerty', $getNames), 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');
        $this->assertTrue(in_array('trewq', $getNames), 'assert failed, "\Yana\Db\Ddl\Database" the value should be match a entry in array');

        $valid = $this->object->isForm('qwerty');
        $this->assertTrue($valid, 'assert failed, "\Yana\Db\Ddl\Database" the value should be true, "qwerty" is a form');

        $drop = $this->object->dropForm('qwerty');
        $nonexist = $this->object->getForm('qwerty');
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
            $this->object->addForm('form');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->object->addForm('form');
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
        $this->object->dropForm('gert');
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
     * Foreign-key
     *
     * @test
     */
    public function testAddForeignKey()
    {
        // \Yana\Db\Ddl\Table
        $table = $this->object->addTable('table');
        $table_target = $this->object->addTable('table_target');
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
     * ChangeLog
     *
     * @test
     */
    public function testChangeLog()
    {
        $result = $this->object->getChangeLog();
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
        $newTableA = $this->object->addTable("someTable");
        $newTableB = $this->object->addTable("otherTable");
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
        $this->assertFalse($this->object->isModified());
    }

    /**
     * @test
     */
    public function testSetModified()
    {
        $this->assertTrue($this->object->setModified(true)->isModified());
        $this->assertFalse($this->object->setModified(false)->isModified());
    }

    /**
     * @test
     */
    public function testLoadIncludes()
    {
        $this->assertFalse($this->object->isTable('Test'));
        $this->object->addInclude("test");
        $this->assertNull($this->object->loadIncludes());
        $this->assertTrue($this->object->isTable('Test'));
        $this->assertTrue($this->object->isView('Test_view'));
        $this->assertTrue($this->object->isForm('Test_default'));
        $this->assertTrue($this->object->isFunction('Test_function'));
        $this->assertTrue($this->object->isSequence('Test_sequence'));
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
        $this->object->addInclude("check");
        $this->assertNull($this->object->loadIncludes());
    }

    /**
     * @test
     */
    public function testLoadIncludesTwice()
    {
        $this->assertFalse($this->object->isTable('Test'));
        $this->object->addInclude("test");
        $this->assertNull($this->object->loadIncludes());
        $this->assertNull($this->object->loadIncludes());
        $this->assertTrue($this->object->isTable('Test'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testLoadIncludesNotFoundException()
    {
        $this->object->addInclude("no-such-file");
        $this->object->loadIncludes();
    }

}

?>