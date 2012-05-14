<?php
/**
 * PHPUnit test-case: FileDb
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

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';

/**
 * Test class for DbStructure
 *
 * @package  test
 */
class DbStructureTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    DbStructure
     * @access protected
     */
    protected $object;

    /**
     * @var    String
     * @access protected
     */
    protected $databaseName;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->markTestSkipped(); // source class is deprecated
        DDL::setDirectory(CWD . '/resources/');
        $this->databaseName = 'check';
        try {
            $this->object = new DbStructure ( $this->databaseName );
            restore_error_handler();

        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        // intentionally left blank
    }

    /**
     * get database name
     *
     * @test
     */
    public function testGetDatabaseName()
    {
        $dbName = $this->object->getDatabaseName();
        $this->assertFalse(empty($dbName), 'could not retreive a valid databasename');
        $this->assertEquals($dbName, $this->databaseName, 'the databasename does not corresponds to the given database');
    }

    /**
     * read file
     *
     * @test
     */
    public function testRead()
    {
        $this->object->read();
        $test = $this->object->getStructure();
        $testKeys0 = array_keys($test);
        $this->assertContains('USE_STRICT', $testKeys0, 'USE_STRICT Tag is missing');
        $this->assertContains('READONLY', $testKeys0, 'READ_ONLY Tag is missing');
        $this->assertContains('TABLES', $testKeys0, 'TABLES Tag is missing');
        $testTables = $test['TABLES'];
        $testTablesKeys0 = array_keys($testTables);
        $this->assertContains('FT', $testTablesKeys0, 'Table in the check.db is missing');
        $this->assertContains('T', $testTablesKeys0, 'Table in the check.db is missing');
        $this->assertContains('I', $testTablesKeys0, 'Table in the check.db is missing');
    }

    /**
     * get structure
     *
     * @test
     */
    public function testGetStructure()
    {
        $content = $this->object->getStructure();
        // Should be empty at this stage
        $this->assertType('array', $content, 'getStructure result should be of the type array');
        $this->assertTrue(empty($content), 'getStructure result should be empty before a read');

    }

    /**
     * get source
     *
     * @test
     */
    public function testGetSource()
    {
        $source = $this->object->getSource();
        // is like XML
        $this->assertType('string', $source, 'getSource result should be of the type string');
        $this->assertFalse(empty($source), 'getSource result should not be empty');
        $message = "Returned source must be equal to original file in file system.";
        $this->assertEquals(file_get_contents($this->object->getPath()), $source, $message);
    }

    /**
     * is table
     *
     * @test
     */
    public function testIsTable()
    {
       $isTable = $this->object->isTable('FT');
       $this->assertFalse($isTable, 'isTable should be false before the read');

       $this->object->read();
       $isTable = $this->object->isTable('FT');
       $this->assertTrue($isTable, 'a table which was assumed to exist had not been found');
       $isTable = $this->object->isTable('NOTEXIST');
       $this->assertFalse($isTable, 'a table which was assumed not to exist had been found');
    }

    /**
     * add table
     *
     * @test
     */
    public function testAddTable()
    {
        // the result is true if the logging had been successful
        $addTable = $this->object->addTable("new");
        $this->assertTrue($addTable, 'adding a table could not proberly logged');
        // it is not possible to access the Table directly,
        $isTable = $this->object->isTable('new');
        $this->assertTrue($isTable, 'a table which was assumed to exist had not been found');
    }

    /**
     * rename table
     *
     * @test
     */
    public function testRenameTable()
    {
        $this->object->addTable("old");
        $this->object->renameTable("old", "new");
        $isTable = $this->object->isTable('old');
        $this->assertFalse($isTable, 'a renamed table is still available by the old name');
        $isTable = $this->object->isTable('new');
        $this->assertTrue($isTable, 'a renamed table is not available by the new name');
    }

    /**
     * drop table
     *
     * @test
     */
    public function testDropTable()
    {
        $this->object->addTable("goodbye");
        $isTable = $this->object->isTable('goodbye');
        $this->assertTrue($isTable, 'a test Table has not been added');
        $isTable = $this->object->dropTable('goodbye');
        $this->assertTrue($isTable, 'a table has not been dropped');
    }

    /**
     * is column
     *
     * @test
     */
    public function testIsColumn()
    {
       $this->object->read();
       $isTable = $this->object->isColumn("ft", "ftid");
       $this->assertTrue($isTable, 'a column in the test table had not been found');
       $isTable = $this->object->isColumn("ft", "ftabscence");
       $this->assertFalse($isTable, 'an absent column in the test table had falsely been declared as existing');
    }

    /**
     * add column
     *
     * @test
     */
    public function testAddColumn()
    {
        $this->object->read();
        // the column should not exist
        $isTable = $this->object->isColumn("ft", "thisisnew");
        $this->assertFalse($isTable, 'a column in the test was ought to be absent');

        $islogged = $this->object->addColumn("ft", "thisisnew");

        $isTable = $this->object->isColumn("ft", "thisisnew");
        $this->assertTrue($isTable, 'a column should have been added');

        $islogged = $this->object->renameColumn("ft", "thisisnew", "thisisbetter");

        $isTable = $this->object->isColumn("ft", "thisisnew");
        $this->assertFalse($isTable, 'a column was renamed and still is present under the old name');

        $isTable = $this->object->isColumn("ft", "thisisbetter");
        $this->assertTrue($isTable, 'a column was renamed and is not available under the new Name');

        $islogged = $this->object->dropColumn("ft", "thisisbetter");
        $isTable = $this->object->isColumn("ft", "thisisbetter");
        $this->assertFalse($isTable, 'a column was to be dropped but still is able to be adressed');
    }

    /**
     * all tests in context
     *
     * @test
     */
    public function testAll()
    {
        $db = new DbStructure('temp.sml');

        // "USE_STRICT"
        $db->setStrict(true);
        $result = $db->isStrict();
        $this->assertEquals(YANA_DB_STRICT, $result, 'set/get "strict" property failed');

        // "READONLY"
        $db->setReadonly(true);
        $result = $db->isReadonly();
        $this->assertTrue($result, 'set/get "readonly" property on database failed');

        $db->setReadonly(false);
        $result = $db->isReadonly();
        $this->assertFalse($result, 'set/get "readonly" property on database failed');

        // add "TABLE"
        $result = $db->addTable('TeSt');
        $this->assertTrue($result, 'add "table" property failed');

        // check "get table"
        $result = $db->getTables();
        $this->assertEquals($result, array('test'), 'list "tables" failed');

        // check "is table"
        $result = $db->isTable('Test');
        $this->assertTrue($result, 'is table" test failed');

        // "READONLY"
        $db->setReadonly(true, 'teST');
        $result = $db->isReadonly('teST');
        $this->assertTrue($result, 'set/get "readonly" property on table failed');

        $db->setReadonly(false, 'teST');
        $result = $db->isReadonly('teST');
        $this->assertFalse($result, 'set/get "readonly" property on table failed');

        // add "COLUMN"
        $result = $db->addColumn('teSt', 'iD');
        $this->assertTrue($result, 'add "column" property failed');

        // get "COLUMNS"
        $result = $db->getColumns('Test');
        $this->assertEquals($result, array('id'), 'list "columns" failed');

        // check "is column"
        $result = $db->isColumn('Test', 'Id');
        $this->assertTrue($result, '"is column" test failed');

        // "TYPE"
        $db->setType('Test', 'Id', 'Integer');
        $result = $db->getType('Test', 'Id');
        $this->assertEquals('integer', $result, 'set/get "type" property failed');

        // "DEFAULT"
        $db->setDefault('Test', 'Id', 1);
        $result = $db->getDefault('TesT', 'iD');
        $this->assertEquals($result, 1, 'list "columns" failed');

        // "DESCRIPTION"
        $db->setDescription('Test', 'Id', 'Foo');
        $result = $db->getDescription('TesT', 'iD');
        $this->assertEquals($result, 'Foo', 'set/get "description" property failed');

        // "LENGTH"
        $db->setLength('Test', 'Id', 8, 2);
        $result = $db->getLength('TesT', 'iD');
        $this->assertEquals($result, 8, 'set/get "length" property failed');

        // "PRECISION"
        $result = $db->getPrecision('TesT', 'Id');
        $this->assertEquals($result, 2, 'set/get "precision" property failed');

        // get columns by type
        $result = $db->getColumnsByType('Test', 'integer');
        $this->assertEquals($result, array('id'), 'list "columns by type" failed');

        // "UNIQUE"
        $db->setUnique('Test', 'Id', true);
        $result = $db->isUnique('TesT', 'iD');
        $this->assertTrue($result, 'set/get "unique" property failed');

        // get columns by unique constraint
        $result = $db->getUniqueConstraints('Test');
        $this->assertEquals($result, array('id'), 'list "columns by unique constraint" failed');

        // "REQUIRED"
        $db->setNullable('Test', 'Id', false);
        $result = $db->isNullable('TesT', 'iD');
        $this->assertFalse($result, 'set/get "required" property failed');

        // "REQUIRED" = AUTO
        $db->setAuto('Test', 'Id', true);
        $result = $db->isAutonumber('TesT', 'iD');
        $this->assertTrue($result, 'set/get "required" property to "auto" failed');

        // "READONLY"
        $db->setReadonly(true, 'Test', 'Id');
        $result = $db->isReadonly('TesT', 'iD');
        $this->assertTrue($result, 'set/get "readonly" property failed');
        $db->setReadonly(false, 'Test', 'Id');

        // "INDEX"
        $db->setIndex('Test', 'Id', true);
        $result = $db->hasIndex('TesT', 'iD');
        $this->assertTrue($result, 'set/get "index" property failed');

        // get columns which have an index
        $result = $db->getIndexes('Test');
        $this->assertEquals($result, array('id'), 'list "columns with index" failed');

        $db->setIndex('Test', 'Id', false);
        $result = $db->hasIndex('TesT', 'iD');
        $this->assertFalse($result, 'set/get "index" property failed');

        // "DISPLAY.READONLY"
        $db->setEditable(true, 'Test', 'Id', 'eDit');
        $result = $db->isEditable('TesT', 'iD', 'eDit');
        $this->assertTrue($result, 'set/get "display.readonly" property failed');

        // "DISPLAY.HIDDEN"
        $db->setVisible(true, 'Test', 'Id');
        $result = $db->isVisible('TesT', 'iD');
        $this->assertTrue($result, 'set/get "display.hidden" property failed');
        $db->setVisible(false, 'Test', 'Id');
        $result = $db->isVisible('TesT', 'iD');
        $this->assertFalse($result, 'set/get "display.hidden" property failed');

        // "PRIMARY_KEY"
        $db->setPrimaryKey('Test', 'Id', true);
        $result = $db->isPrimaryKey('TesT', 'iD');
        $this->assertTrue($result, 'set/get "primary key" property failed');

        $result = $db->getPrimaryKey('TesT', 'iD');
        $this->assertEquals($result, 'id', 'set/get "primary key" property failed');

        // get Files
        $db->addColumn('teSt', 'my_File');
        $db->setType('Test', 'my_fIle', 'file');
        $result = $db->getFiles('TesT', 'iD');
        $this->assertEquals($result, array('my_file'), 'list "columns of type image or file" failed');

        // get/set Foreign keys
        $db->addTable('ftab');
        $db->addColumn('ftab', 'fid');
        $db->setPrimaryKey('ftab', 'fid');

        $result = $db->setForeignKey('teSt', 'id', 'fTab');
        $this->assertTrue($result, 'set "foreign key" failed');

        $result = $db->isForeignKey('teSt', 'id');
        $this->assertTrue($result, 'is "foreign key" failed');

        $result = $db->getTableByForeignKey('teSt', 'id');
        $this->assertEquals($result, 'ftab', 'get "table by foreign key" failed');

        $result = $db->getForeignKeys('test');
        $this->assertType('array', $result, 'assert failed, the value should be of type array');
        $this->assertTrue(count($result)!=0, 'assert failed, the given array must have an entrie');
        foreach ($result as $key)
        {
            $this->assertEquals('ftab', $key, 'assert failed, the expected foreignkey should be exist in array');
        }
        // image settings
        $db->addColumn('test', 'iMG');
        $db->setType('tEsT', 'imG', 'image');
        $set = array();
        $set['size'] = 50000;
        $set['width'] = 300;
        $set['height'] = 200;
        $set['ratio'] = true;
        $set['background'] = array(255, 255, 255);

        $result = $db->setImageSettings('test', 'IMG', $set);
        $this->assertTrue($result, 'set "image settings" failed');

        $result = $db->getImageSettings('teSt', 'Img');
        $this->assertEquals($result, $set, 'get "image settings" failed');

        if ($db->getImageSettings('teSt', 'Img') !== $set) {
        return 'set "image settings" failed';
        }

        // "CONSTRAINT"
        $constraint = '$PERMISSION > 100 && $VALUE > 0';
        $result = $db->setConstraint($constraint, 'test', 'id');
        $this->assertTrue($result, 'set "constraint" failed');

        $result = $db->getConstraint('test');
        $this->assertEquals($result['ID'], $constraint, 'get "constraint" failed');

        // "TRIGGER"
        $result = $db->setTrigger($constraint, 'before_insert', 'test', 'id');
        $this->assertTrue($result, 'set "trigger" failed');

        $result = $db->getTrigger('before_insert', 'test');
        $this->assertEquals($result[0], $constraint, 'get "constraint" failed');
    }

    /**
     * test init
     *
     * @test
     */
    public function testInit()
    {
        $testString1 = 'this is a test 1';
        $testString2 = 'this is a test 2';
        $this->object->read();
        $this->object->setInit("ft",array($testString1, $testString2));
        $erg = $this->object->getInit("ft");
        $this->assertContains($testString1, $erg, 'Initialation failed');
        $this->object->setInit("ft");
        $erg = $this->object->getInit("ft");
        $this->assertFalse($erg, 'Reseting initialation failed');
    }

    /**
     * test file list
     *
     * @test
     */
    public function testGetFileList()
    {
        $dir = DbStructure::getDirectory();
        $this->assertTrue(is_dir($dir), 'Base directory is not valid.');
        $expected = array();
        foreach (glob("$dir/*.config") as $file)
        {
            $expected[] = basename($file, '.config');
        }
        $result = DbStructure::getListOfFiles();

        $this->assertEquals($result, $expected, 'List of config files is missing values.');
    }

    /**
     * test1
     *
     * @test
     */
    public function test1()
    {
        $db = new DbStructure('temp.sml');
        
        // "USE_STRICT"
        $db->setStrict(true);
        $result = $db->isStrict();
        $this->assertEquals(YANA_DB_STRICT, $result, 'set/get "strict" property failed');

        // "READONLY"
        $db->setReadonly(true);
        $result = $db->isReadonly();
        $this->assertTrue($result, 'set/get "readonly" property on database failed');

        $db->setReadonly(false);
        $result = $db->isReadonly();
        $this->assertFalse($result, 'set/get "readonly" property on database failed');

        // add "TABLE"
        $result = $db->addTable('TeSt');
        $this->assertTrue($result, 'add "table" property failed');

        // check "get table"
        $result = $db->getTables();
        $this->assertEquals($result, array('test'), 'list "tables" failed');

        // check "is table"
        $result = $db->isTable('Test');
        $this->assertTrue($result, 'is table" test failed');
        
        // "READONLY"
        $db->setReadonly(true, 'teST');
        $result = $db->isReadonly('teST');
        $this->assertTrue($result, 'set/get "readonly" property on table failed');

        $db->setReadonly(false, 'teST');
        $result = $db->isReadonly('teST');
        $this->assertFalse($result, 'set/get "readonly" property on table failed');

        // add "COLUMN"
        $result = $db->addColumn('teSt', 'iD');
        $this->assertTrue($result, 'add "column" property failed');

        // get "COLUMNS"
        $result = $db->getColumns('Test');
        $this->assertEquals($result, array('id'), 'list "columns" failed');

        // "ZEROFILL"
        $db->setZerofill('teSt', 'iD', false);
        $result = $db->isZerofill('teSt', 'iD');
        $this->assertFalse($result, 'set/get "zerofill" property on table failed');

        $db->setZerofill('teSt', 'iD', true);
        $result = $db->isZerofill('teSt', 'iD');
        $this->assertTrue($result, 'set/get "zerofill" property on table must be set');

        // "Unsigned"
        $db->setUnsigned('teSt', 'iD', false);
        $result = $db->isUnsigned('teSt', 'iD');
        $this->assertTrue($result, 'set/get "unsigned" property on table failed');

        $db->setUnsigned('teSt', 'iD', true);
        $result = $db->isUnsigned('teSt', 'iD');
        $this->assertTrue($result, 'set/get "unsigned" property on table must be set');

        $db->setType('teSt', 'iD', 'string');
        $result = $db->isNumber('teSt', 'iD');
        $this->assertFalse($result, 'set/get "unsigned" property on table must be set');

        $db->setType('teSt', 'iD', 'integer');
        $result = $db->isNumber('teSt', 'iD');
        $this->assertTrue($result, 'set/get "unsigned" property on table must be set');

        $result = $db->setAssociation('foo', 'test');
        $this->assertTrue($result, 'assert failed, the association is not set');
        $result = $db->hasAssociation('test');
        $this->assertTrue($result, 'the table test has an association');
        $result = $db->getAssociation('test');
        $this->assertEquals('foo', $result, 'assert failed, no association is given');
        $result = $db->unsetAssociation('test');
        $this->assertTrue($result, 'assert failed, the association is not unseted');

        $result = $db->setProfile('test', 'id');
        $this->assertTrue($result, 'assert failed, the profile is not set');
        $result = $db->getProfile('test');
        $this->assertEquals('id', $result, 'assert failed, expected "id" as result');

        $result = $db->setProfile('test');
        $this->assertTrue($result, 'assert failed, the profile is not unset');
        $result = $db->getProfile('test');
        $this->assertFalse($result, 'assert failed, no profile is set');
       
        // add "COLUMN"
        $result = $db->addColumn('teSt', 'image');
        $this->assertTrue($result, 'add "column" property failed');
        $result = $db->setType('teSt', 'image', 'image');
        $this->assertTrue($result, 'set "type" property failed');

        $result = $db->isScalar('teSt', 'image');
        $this->assertFalse($result, 'property is not a scalar value');

        $result = $db->isScalar('teSt', 'number');
        $this->assertTrue($result, 'is "scalar" property failed');

        // add "COLUMN"
        $result = $db->addColumn('teSt', 'number');
        $this->assertTrue($result, 'add "column" property failed');
        $result = $db->setType('teSt', 'number', 'integer');
        $this->assertTrue($result, 'set "type" property failed');

        $result = $db->setNumericArray('teSt', 'number');
        $this->assertTrue($result, 'set "numericArray" property failed');
        $result = $db->isNumericArray('teSt', 'number');
        $this->assertTrue($result, 'is "numericArray" property failed');

        $result = $db->includeFile('check');
        $this->assertTrue($result, 'assert failed, include file has failed');

        $result = $db->setAction('test', 'id', 'delete', 'DEFAULT', 'foo', 'bar');
        $result = $db->getAction('test', 'id');
        $this->assertEquals('delete', $result, 'assert failed, given action must be equal');
        $result = $db->getActions('test');
        $expectedResult = array_pop($result);
        $this->assertEquals('delete', $expectedResult, 'assert failed, given action must be equal');
        $result = $db->getTitle('test', 'id');
        $this->assertEquals('bar', $result, 'assert failed,  given title must be equal');
        $result = $db->getLinkText('test', 'id');
        $this->assertEquals('foo', $result, 'assert failed,  given linktext must be equal');
    }
}
?>
