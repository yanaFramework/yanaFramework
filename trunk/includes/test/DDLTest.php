<?php
/**
 * PHPUnit test-case: DDL
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
 * Test class for DDL
 *
 * @package  test
 */
class DDLTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    XDDL
     * @access protected
     */
    protected $file;

    /**
     * @var    DDLDatabase
     * @access protected
     */
    protected $object;

    /**
     * @var    string
     * @access protected
     */
    protected $path = 'resources/test.db.xml';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->file = new XDDL(CWD . $this->path);
        $this->file->read();
        $this->object = $this->file->toDatabase();
        $this->object->setModified();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        $this->object->__destruct();
        unset($this->object);
    }

    /**
     * encode to XML
     *
     * @covers toSimpleXML
     * @covers toXML
     */
    public function testEncode()
    {
        // set to modified (otherwise function would return result from cache)
        $this->object->setModified();
        // get result
        $xddl = $this->file->toSimpleXML();
        $xml = $xddl->asXML();

        // get same result from cache (must be equal)
        $cachedXML = $this->file->toXML();
        $this->assertEquals($cachedXML, $xml);

        // add header in xml
        $dtd = '<!DOCTYPE  database SYSTEM "resources/dtd/database.dtd">';
        $encoding = iconv_get_encoding("internal_encoding");
        $replacement = '<?xml version="1.0" encoding="' . $encoding . '"?>' . "\n" . $dtd;
        $pattern = '/(\<\?xml[\d\D]*\?\>)/';

        // replace entitie
        $expected = preg_replace($pattern, $replacement, $xml);
        $expected = str_replace('&gt;', '>', $expected);
        // validate XML
        $domDocument = new DomDocument();
        $domDocument->loadXML($expected);
//        $domDocument->save('test.xml');
        $isValid = $domDocument->validate();

        $message = "Round-trip decoding/encoding of source-document failed. " .
            "The result is not valid.";
        $this->assertTrue($isValid, $message);

        $xml = $expected;
        // original document
        $source = file_get_contents(CWD . 'resources/testxml.db.xml');
        // trim white-space
        $source = trim($source);
        $source = preg_replace('/^\s+/m', '', $source);
        $source = preg_replace('/\s+$/m', '', $source);
        $source = preg_replace('/[\n\r\f]/', '', $source);
        // trim white-space
        $xml = trim($xml);
        $xml = preg_replace('/^\s+/m', '', $xml);
        $xml = preg_replace('/\s+$/m', '', $xml);
        $xml = preg_replace('/[\n\r\f]/', '', $xml);
        // compare source and generated result
        $message = "Round-trip decoding/encoding of source-document failed. " .
            "The result differs from the source file.";
        $this->assertEquals($source, $xml, $message);
    }

    /**
     * @todo Implement testIncludeFile().
     */
    public function testIncludeFile()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * test list of includes
     *
     * @test
     */
    public function testIncludes()
    {
        $includes = array('check', 'blog');
        $this->object->setIncludes($includes);

        $getIncludes = $this->object->getIncludes($includes);
        $message = "Expecting getIncludes() to return same value as has been set by setIncludes()";
        $this->assertEquals($includes, $getIncludes, $message);
        $xml = (string) $this->object;
        $message = "Expecting XML-output to contain added includes.";
        $this->assertRegExp('/<include>check<\/include>/', $xml, $message);

        $this->assertTrue($this->object->isTable("Foo"), "Included XDDL file has not been loaded.");

        $this->object->setIncludes();
        $getIncludes = $this->object->getIncludes();
        $message = "Expecting getIncludes() to return empty array after entries have been removed.";
        $this->assertEquals(array(), $getIncludes, $message);
    }

    /**
     * get database name
     *
     * @test
     */
    public function testGetName()
    {
        // expect name of database to be set to "testDB" in source-file
        $name = $this->object->getName();
        $this->assertEquals('testdb', $name, 'Database-name should match name-attribute in source file.');

        // test default value
        $this->object->setName();
        $name = $this->object->getName();
        $this->assertEquals(null, $name, 'Database-name should be empty after reset.');
    }

    /**
     * set database name
     *
     * @test
     */
    public function testSetName()
    {
        $this->object->setName("foo");
        $name = $this->object->getName();
        $this->assertEquals($name, 'foo', 'Database-name should be set to given value.');
    }

    /**
     * get description
     *
     * @test
     */
    public function testGetDescription()
    {
        $this->object->setDescription();
        $description = $this->object->getDescription();
        $this->assertEquals($description, null, 'Description should default to null.');
    }

    /**
     * set description
     *
     * @test
     */
    public function testSetDescription()
    {
        $this->object->setDescription('test');
        $description = $this->object->getDescription();
        $this->assertEquals($description, 'test', 'getDescription() should return the value previously set by setDescription().');
    }

    /**
     * get charset
     *
     * @test
     */
    public function testGetCharset()
    {
        // expect charset of database to be set to "utf8" in source-file
        $charset = $this->object->getCharset();
        $this->assertEquals('utf8', $charset, 'Charset should match charset-attribute in source file.');

        // test default value
        $this->object->setCharset();
        $charset = $this->object->getCharset();
        $this->assertEquals(null, $charset, 'Charset should default to NULL.');
    }

    /**
     * set charset
     *
     * @test
     */
    public function testSetCharset()
    {
        // test default value
        $this->object->setCharset('latin1');
        $charset = $this->object->getCharset();
        $this->assertEquals('latin1', $charset, 'getCharset() should return the value previously set by setCharset().');
    }

    /**
     * get data-source
     *
     * @test
     */
    public function testGetDataSource()
    {
        // expect charset of database to be set to "utf8" in source-file
        $datasource = $this->object->getDataSource();
        $this->assertEquals('test', $datasource, 'Datasource should match datasource-attribute in source file.');
    }

    /**
     * set data-source
     *
     * @test
     */
    public function testSetDataSource()
    {
        // test default value
        $this->object->setDataSource();
        $datasource = $this->object->getDataSource();
        $this->assertEquals(null, $datasource, 'Datasource should default to NULL.');

        // test set value
        $this->object->setDataSource('foo');
        $datasource = $this->object->getDataSource();
        $this->assertEquals('foo', $datasource, 'getDataSource() should return the value previously set by setDataSource().');
    }

    /**
     * check read-only flag
     *
     * @test
     */
    public function testIsReadonly()
    {
        // expect read-only property of database to be set to "no" in source-file
        $isReadonly = $this->object->isReadonly();
        $this->assertFalse($isReadonly, 'Readonly should match readonly-attribute in source file.');
    }

    /**
     * set read-only flag
     *
     * @test
     */
    public function testSetReadonly()
    {
        // test default value
        $this->object->setReadonly();
        $isReadonly = $this->object->isReadonly();
        $this->assertFalse($isReadonly, 'Readonly should default to "no".');

        // test set value
        $this->object->setReadonly(true);
        $isReadonly = $this->object->isReadonly();
        $this->assertTrue($isReadonly, 'isReadonly() should return the value previously set by setReadonly().');
    }

    /**
     * get table by name
     *
     * @test
     */
    public function testGetTable()
    {
        $table = $this->object->getTable('tesT');
        $this->assertTrue($table instanceof DDLTable, "Unable to deserialize table.");
        $name = $table->getName();
        $this->assertEquals('test', $name, "Expecting returned table to have same name as requested.");
    }

    /**
     * add table
     *
     * @test
     */
    public function testAddTable()
    {
        $name = "new_table";
        $table = $this->object->addTable($name);
        $this->assertType('DDLTable', $table, "Expected addTable() to return an instance of DDLTable.");
        $this->assertEquals($name, $table->getName(), "Expected returned table to have given name.");
    }

    /**
     * get tables
     *
     * @test
     */
    public function testGetTables()
    {
        $array = $this->object->getTables();
        $this->assertFalse(empty($array), "Returned list of tables should not be empty");
        foreach ($array as $item)
        {
            $this->assertType('DDLTable', $item, "Every returned table is expected to be an instance of DDLTable.");
        }
    }

    /**
     * get table-names
     *
     * @test
     */
    public function testGetTableNames()
    {
        $array = $this->object->getTableNames();
        $this->assertFalse(empty($array), "Returned list of tables should not be empty");
        foreach ($array as $item)
        {
            $exists = $this->object->isTable($item);
            $this->assertTrue($exists, "Every element of returned list is expected to be the name of an existing table.");
        }
    }

    /**
     * get view
     *
     * @test
     */
    public function testGetView()
    {
        $name = "test_view";
        $view = $this->object->getView($name);
        $this->assertType('DDLView', $view, "Expected getView() to return an instance of DDLView.");
        $this->assertEquals($name, $view->getName(), "Expected returned view to have given name.");
    }

    /**
     * add view
     *
     * @test
     */
    public function testAddView()
    {
        $name = "new_view";
        $view = $this->object->addView($name);
        $this->assertType('DDLView', $view, "Expected addView() to return an instance of DDLView.");
        $this->assertEquals($name, $view->getName(), "Expected returned view to have given name.");
    }

    /**
     * get views
     *
     * @test
     */
    public function testGetViews()
    {
        $array = $this->object->getViews();
        $this->assertFalse(empty($array), "Returned list of views should not be empty");
        foreach ($array as $item)
        {
            $this->assertType('DDLView', $item, "Every returned view is expected to be an instance of DDLView.");
        }
    }

    /**
     * get view-names
     *
     * @test
     */
    public function testGetViewNames()
    {
        $array = $this->object->getViewNames();
        $this->assertFalse(empty($array), "Returned list of views should not be empty");
        foreach ($array as $item)
        {
            $exists = $this->object->isView($item);
            $this->assertTrue($exists, "Every element of returned list is expected to be the name of an existing view.");
        }
    }

    /**
     * get function
     *
     * @test
     */
    public function testGetFunction()
    {
        $name = "test_function";
        $function = $this->object->getFunction($name);
        $this->assertType('DDLFunction', $function, "Expected getFunction() to return an instance of DDLFunction.");
        $this->assertEquals($name, $function->getName(), "Expected returned function to have given name.");
    }

    /**
     * add function
     *
     * @test
     */
    public function testAddFunction()
    {
        $name = "new_function";
        $function = $this->object->addFunction($name);
        $this->assertType('DDLFunction', $function, "Expected addFunction() to return an instance of DDLFunction.");
        $this->assertEquals($name, $function->getName(), "Expected returned function to have given name.");
    }

    /**
     * get functions
     *
     * @test
     */
    public function testGetFunctions()
    {
        $array = $this->object->getFunctions();
        $this->assertFalse(empty($array), "Returned list of functions should not be empty");
        foreach ($array as $item)
        {
            $this->assertType('DDLFunction', $item, "Every returned function is expected to be an instance of DDLFunction.");
        }
    }

    /**
     * get function-names
     *
     * @test
     */
    public function testGetFunctionNames()
    {
        $array = $this->object->getFunctionNames();
        $this->assertFalse(empty($array), "Returned list of functions should not be empty");
        foreach ($array as $item)
        {
            $exists = $this->object->isFunction($item);
            $this->assertTrue($exists, "Every element of returned list is expected to be the name of an existing function.");
        }
    }

    /**
     * get sequence
     *
     * @test
     */
    public function testGetSequence()
    {
        $name = "test_sequence";
        $view = $this->object->getSequence($name);
        $this->assertType('DDLSequence', $view, "Expected getSequence() to return an instance of DDLSequence.");
        $this->assertEquals($name, $view->getName(), "Expected returned sequence to have given name.");
    }

    /**
     * add sequence
     *
     * @test
     */
    public function testAddSequence()
    {
        $name = "new_sequence";
        $sequence = $this->object->addSequence($name);
        $this->assertType('DDLSequence', $sequence, "Expected addSequence() to return an instance of DDLSequence.");
        $this->assertEquals($name, $sequence->getName(), "Expected returned sequence to have given name.");
    }

    /**
     * get sequences
     *
     * @test
     */
    public function testGetSequences()
    {
        $array = $this->object->getSequences();
        $this->assertFalse(empty($array), "Returned list of sequences should not be empty");
        foreach ($array as $item)
        {
            $this->assertType('DDLSequence', $item, "Every returned sequence is expected to be an instance of DDLSequence.");
        }
    }

    /**
     * get sequence-names
     *
     * @test
     */
    public function testGetSequenceNames()
    {
        $array = $this->object->getSequenceNames();
        $this->assertFalse(empty($array), "Returned list of sequences should not be empty");
        foreach ($array as $item)
        {
            $exists = $this->object->isSequence($item);
            $this->assertTrue($exists, "Every element of returned list is expected to be the name of an existing sequence.");
        }
    }

    /**
     * get initialization
     *
     * @test
     */
    public function testGetInit()
    {
        $test = array(1, 4);
        $init = $this->object->getInit();
        $this->assertEquals($test, $init, "Expecting matching initialization tags for empty parameter to be 1st and 4th.");

        $init = $this->object->getInit("generic");
        $this->assertEquals($test, $init, "Expecting matching initialization tags for 'gemeric' parameter to be 1st and 4th.");

        $test = array(1, 2, 4);
        $init = $this->object->getInit("mysql");
        $this->assertEquals($test, $init, "Expecting matching initialization tags for 'mysql' parameter to be 1st, 2nd and 4th.");
    }

    /**
     * drop initialization statements
     *
     * @test
     */
    public function testdropInit()
    {
        $this->object->dropInit();
        $init = $this->object->getInit();
        $this->assertEquals(array(), $init, "Expect list of initialization statements to be empty after calling dropInit().");
    }

    /**
     * add init
     *
     * @test
     */
    public function testAddInit()
    {
        $expected = $this->object->getInit();
        $newInit = '1';
        $expected[] = $newInit;
        $this->object->addInit($newInit);
        $modified = $this->object->getInit();

        $this->assertEquals($expected, $modified, "Expect list of initialization statements to be to contain the added statement.");
    }

    /**
     * add include
     *
     * @test
     */
    public function testAddInclude()
    {
        $expected = $this->object->getIncludes();
        $newInclude = 'file';
        $expected[] = $newInclude;
        $this->object->addInclude($newInclude);
        $modified = $this->object->getIncludes();

        $this->assertEquals($expected, $modified, "Expect list of included files to contain the added file.");
    }

    /**
     * is table
     *
     * @test
     */
    public function testIsTable()
    {
        $isTable = $this->object->isTable('tesT');
        $this->assertTrue($isTable, "Expect 'test' to be identified as a table, as defined in the source file.");
        $isTable = $this->object->isTable('no_table');
        $this->assertFalse($isTable, "Expect 'no_table' to be identified as not being a table.");
    }

    /**
     * is view
     *
     * @test
     */
    public function testIsView()
    {
        $isView = $this->object->isView("Test_View");
        $this->assertTrue($isView, "Expect 'test_view' to be identified as a view, as defined in the source file.");

        $isView = $this->object->isView("no_view");
        $this->assertFalse($isView, "Expect 'no_view' to be identified as not being a view.");
    }

    /**
     * is function
     *
     * @test
     */
    public function testIsFunction()
    {
        $isFunction = $this->object->isFunction("Test_Function");
        $this->assertTrue($isFunction, "Expect 'test_function' to be identified as a function, as defined in the source file.");

        $isFunction = $this->object->isFunction("no_function");
        $this->assertFalse($isFunction, "Expect 'no_function' to be identified as not being a view.");
    }

    /**
     * is form
     *
     * @test
     */
    public function testIsForm()
    {
        $isForm = $this->object->isForm("Test_New");
        $this->assertTrue($isForm, "Expect 'test_new' to be identified as a form, as defined in the source file.");

        $isForm = $this->object->isForm("no_form");
        $this->assertFalse($isForm, "Expect 'no_form' to be identified as not being a form.");
    }

    /**
     * is sequence
     *
     * @test
     */
    public function testIsSequence()
    {
        $isSequence = $this->object->isSequence("Test_Sequence");
        $this->assertTrue($isSequence, "Expect 'test_sequence' to be identified as a sequence, as defined in the source file.");

        $isSequence = $this->object->isSequence("no_sequence");
        $this->assertFalse($isSequence, "Expect 'no_sequence' to be identified as not being a sequence.");
    }

    /**
     * get form
     *
     * @test
     */
    public function testGetForm()
    {
        $name = "test_new";
        $form = $this->object->getForm($name);
        $this->assertType('DDLForm', $form, "Expected getForm() to return an instance of DDLForm.");
        $this->assertEquals($name, $form->getName(), "Expected returned form to have given name.");
    }

    /**
     * get forms
     *
     * @test
     */
    public function testGetForms()
    {
        $array = $this->object->getForms();
        $this->assertFalse(empty($array), "Returned list of forms should not be empty");
        foreach ($array as $item)
        {
            $this->assertType('DDLForm', $item, "Every returned form is expected to be an instance of DDLForm.");
        }
    }

    /**
     * get form-names
     *
     * @test
     */
    public function testGetFormNames()
    {
        $array = $this->object->getFormNames();
        $this->assertFalse(empty($array), "Returned list of forms should not be empty");
        foreach ($array as $item)
        {
            $exists = $this->object->isForm($item);
            $this->assertTrue($exists, "Every element of returned list is expected to be the name of an existing form.");
        }
    }

    /**
     * add form
     *
     * @test
     */
    public function testAddForm()
    {
        $name = "new_form";
        $form = $this->object->addForm($name);
        $this->assertType('DDLForm', $form, "Expected addForm() to return an instance of DDLForm.");
        $this->assertEquals($name, $form->getName(), "Expected returned form to have given name.");
    }

    /**
     * drop table
     *
     * @test
     */
    public function testDropTable()
    {
        $name = "Test";
        $this->object->dropTable($name);
        $isTable = $this->object->isTable($name);
        $this->assertFalse($isTable, "The table '$name' should have been deleted.");
    }

    /**
     * drop view
     *
     * @test
     */
    public function testDropView()
    {
        $name = "Test_View";
        $this->object->dropView($name);
        $isView = $this->object->isView($name);
        $this->assertFalse($isView, "The view '$name' should have been deleted.");
    }

    /**
     * drop form
     *
     * @test
     */
    public function testDropForm()
    {
        $name = "Test_New";
        $this->object->dropForm($name);
        $isForm = $this->object->isForm($name);
        $this->assertFalse($isForm, "The form '$name' should have been deleted.");
    }

    /**
     * drop function
     *
     * @test
     */
    public function testDropFunction()
    {
        $name = "Test_Function";
        $this->object->dropFunction($name);
        $isFunction = $this->object->isFunction($name);
        $this->assertFalse($isFunction, "The function '$name' should have been deleted.");
    }

    /**
     * drop sequence
     *
     * @test
     */
    public function testDropSequence()
    {
        $name = "Test_Sequence";
        $this->object->dropSequence($name);
        $isSequence = $this->object->isSequence($name);
        $this->assertFalse($isSequence, "The sequence '$name' should have been deleted.");
    }

    /**
     * get viewElements
     *
     * @test
     */
    public function testGetViewElements()
    {
        $name = "test_view";
        $nameColumn = "test_title";
        $nameAlias = "bar";
        $nameTable = "test";
        $viewQueryMysqlGoal = "Select Test_title as bar, Test_id as id from Test where Test_id > 5";
        $view = $this->object->getView($name);
        $this->assertTrue($view instanceof DDLView, "Unable to deserialize view.");
        $viewQueryMysql = $view->getQuery("mysql");
        $viewField = $view->getField($nameColumn);
        $viewFieldName = $viewField->getName();
        $viewFieldAlias = $viewField->getAlias();
        $viewFieldTable = $viewField->getTable();

        $this->assertEquals($viewQueryMysql, $viewQueryMysqlGoal, "Expected returned query for mysql in View to have given value.");
        $this->assertEquals($nameColumn, $viewFieldName, "Expected returned viewField to have given name.");
        $this->assertEquals($viewFieldAlias, $nameAlias, "Expected returned viewField to have given alias.");
        $this->assertEquals($viewFieldTable, $nameTable, "Expected returned viewField to have given table.");

        $className = get_class($view);
        $classNameColumn = get_class($viewField);
    }

    /**
     * get viewElements
     *
     * @test
     */
    public function testGetFormElements()
    {
        $form = $this->object->getForm("test_edit");
        $this->assertTrue($form instanceof DDLForm, "Expected returned className does not match.");

        $formName = $form->getName();
        $this->assertEquals($formName, "test_edit", "Expected returned form to have given value.");
    }


    /**
     * get tableElements
     *
     * @test
     */
    public function testGetTableElements()
    {

        // Second Table
        $name = "testcmt";
        $nameEvent = "onedit";
        $table = $this->object->getTable($name);
        $this->assertTrue($table instanceof DDLTable, "Unable to deserialize table '$name'.");

        $tableColumn1 = $table->getColumn("test_author");
        $tableColumn2 = $table->getColumn("Testcmt_created");
        $tableColumnNames = $table->getColumnNames();
        $tableColumnByType = $table->getColumnsByType("timestamp");
        $tableColumnForeignKey = $table->getForeignKeys();
        $tableColumnPrimaryKey = $table->getPrimaryKey();

        // Test der einzelnen Columns
        $columnName1 = $tableColumn1->getName();
        $this->assertEquals($columnName1, "test_author", "Expected returned columnName does not match.");
        $columnType1 = $tableColumn1->getType();
        $this->assertEquals($columnType1, "string", "Expected returned columnType does not match.");


        $columnName2 = $tableColumn2->getName();
        $this->assertEquals($columnName2, "testcmt_created", "Expected returned columnName does not match.");
        $columnType2 = $tableColumn2->getType();
        $this->assertEquals($columnType2, "timestamp", "Expected returned columnType does not match.");

        // foreign key
        $foreignKey = $table->getForeignKey("testforeign");
        $targetTable = $foreignKey->getTargetTable();
        $sourceTable = $foreignKey->getSourceTable();

        $columns = $foreignKey->getColumns();
        $this->assertTrue(isset($columns['testcmt_id']), "Expected foreignKey for column 'testcmt_id' does not exists.");
        $this->assertEquals(isset($columns['testcmt_id']), "test_id", "Expected returned sourceTable in foreignKey does not match.");

        // First Table
        $name = "test";
        $table = $this->object->getTable($name);
        $columnNames = array("test_id", "test_title", "test_text", "test_created", "test_author");

        // these grants will be tested
        // just check against the modified Grants
        $allGrants = array('isSelectable', 'isInsertable', 'isUpdatable', 'isDeletable', 'isGrantable');
        $modifiedGrants = array();
        $modifiedGrants['test_created'] = array('isSelectable', 'isUpdatable', 'isDeletable', 'isGrantable');
        $modifiedGrants['test_id'] = array('isSelectable', 'isUpdatable', 'isDeletable', 'isGrantable');
        $modifiedGrants['test_title'] = array('isSelectable', 'isUpdatable', 'isDeletable', 'isGrantable');
        $modifiedGrants['test_text'] = array('isSelectable', 'isUpdatable', 'isDeletable', 'isGrantable');
        $modifiedGrants['test_author'] = array('isSelectable', 'isUpdatable', 'isDeletable', 'isGrantable');

        $columnLength = array(8,80,3000,null,80);
        $columnAutoInc = array(true,false,false,false,false);
        $columnPrimKey = array(true,false,false,false,false);


        $tableColumn[0] = $table->getColumn($columnNames[0]);
        $tableColumn[1] = $table->getColumn($columnNames[1]);
        $tableColumn[2] = $table->getColumn($columnNames[2]);
        $tableColumn[3] = $table->getColumn($columnNames[3]);
        $tableColumn[4] = $table->getColumn($columnNames[4]);

        foreach ($tableColumn as $key => $column)
        {
            $nameOfColumn = $columnNames[$key];
            $this->assertEquals($column->getName(), $nameOfColumn, "Expected name " . $nameOfColumn . " does not match.");

            $grants = $column->getGrants();
            // intentionally, you would expect one or no entry for grants
            foreach((array) $grants as $grant)
            {
                // test each grant
                foreach ($allGrants as $grantFunction)
                {
                    // grants are true by default
                    $expectedValue = true;
                    if (isset($modifiedGrants[$nameOfColumn]))
                    {
                        if (in_array($grantFunction, $modifiedGrants[$nameOfColumn]))  {
                            $expectedValue = false;
                        }
                    }

                    $this->assertEquals($grant->$grantFunction(), $expectedValue, "In Column ". $nameOfColumn ." " . $grantFunction . " does not match.");
                }
            }
            $this->assertEquals($column->getLength(), $columnLength[$key], "In Column ". $nameOfColumn ." columnlength does not match expected Value");
            $this->assertEquals($column->isAutoIncrement(), $columnAutoInc[$key], "In Column ". $nameOfColumn ." autoincrement does not match expected Value");

            $this->assertEquals($column->isPrimaryKey(), $columnPrimKey[$key], "In Column ". $nameOfColumn ." primary Key does not match expected Value");
        }
    }
}
?>