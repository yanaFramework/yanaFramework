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
     * @var \Yana\Db\Ddl\Field
     */
    protected $field;

    /**
     * @var \Yana\Db\Ddl\ForeignKey
     */
    protected $foreignkey;

    /**
     * @var \Yana\Db\Ddl\Form
     */
    protected $form;

    /**
     * @var \Yana\Db\Ddl\Functions\Object
     */
    protected $function;

    /**
     * @var \Yana\Db\Ddl\Functions\Implementation
     */
    protected $functionimplementation;

    /**
     * @var \Yana\Db\Ddl\Functions\Parameter
     */
    protected $functionparameter;

    /**
     * @var \Yana\Db\Ddl\Logs\Create
     */
    protected $logcreate;

    /**
     * @var \Yana\Db\Ddl\Logs\Drop
     */
    protected $logdrop;

    /**
     * @var \Yana\Db\Ddl\Logs\Rename
     */
    protected $logrename;

    /**
     * @var \Yana\Db\Ddl\Logs\Sql
     */
    protected $logsql;

    /**
     * @var \Yana\Db\Ddl\Logs\Update
     */
    protected $logupdate;

    /**
     * @var \Yana\Db\Ddl\Logs\Change
     */
    protected $logchange;

    /**
     * @var \Yana\Db\Ddl\Sequence
     */
    protected $sequence;

    /**
     * @var \Yana\Db\Ddl\Table
     */
    protected $table;

    /**
     * @var \Yana\Db\Ddl\Views\View
     */
    protected $view;

    /**
     * @var \Yana\Db\Ddl\Grant
     */
    protected $grant;

    /**
     * @var \Yana\Db\Ddl\IndexColumn
     */
    protected $indexcolumn;

    /**
     * @var \Yana\Db\Ddl\Views\Field
     */
    protected $viewfield;

    /**
     * @var \Yana\Db\Ddl\Event
     */
    protected $event;

    /**
     * @var \Yana\Db\Ddl\ChangeLog
     */
    protected $changelog;

    /**
     * @var \Yana\Db\Ddl\DatabaseInit
     */
    protected $init;

    /**
     * @var \Yana\Db\Ddl\Trigger
     */
    protected $trigger;

    /**
     * @var \Yana\Db\Ddl\Constraint
     */
    protected $constraint;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        chdir(CWD . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

        $this->database = new \Yana\Db\Ddl\Database();
        $this->table = new \Yana\Db\Ddl\Table('table');
        $this->field = new \Yana\Db\Ddl\Field('field');
        $this->foreignkey = new \Yana\Db\Ddl\ForeignKey('foreignkey');
        $this->form = new \Yana\Db\Ddl\Form('form');
        $this->function = new \Yana\Db\Ddl\Functions\Object('function');
        $this->functionimplementation = new \Yana\Db\Ddl\Functions\Implementation;
        $this->functionparameter = new \Yana\Db\Ddl\Functions\Parameter('param');
        $this->logcreate = new \Yana\Db\Ddl\Logs\Create('logcreate');
        $this->logdrop = new \Yana\Db\Ddl\Logs\Drop('logdrop');
        $this->logrename = new \Yana\Db\Ddl\Logs\Rename('logrename');
        $this->logsql = new \Yana\Db\Ddl\Logs\Sql();
        $this->logupdate = new \Yana\Db\Ddl\Logs\Update('logupdate');
        $this->logchange = new \Yana\Db\Ddl\Logs\Change();
        $this->sequence = new \Yana\Db\Ddl\Sequence('sequence');
        $this->view = new \Yana\Db\Ddl\Views\View('view');
        $this->grant = new \Yana\Db\Ddl\Grant();
        $this->changelog = new \Yana\Db\Ddl\ChangeLog();
        $this->indexcolumn = new \Yana\Db\Ddl\IndexColumn('indexColumn');
        $this->viewfield = new \Yana\Db\Ddl\Views\Field('viewfield');
        $this->event = new \Yana\Db\Ddl\Event('action');
        $this->init = new \Yana\Db\Ddl\DatabaseInit();
        $this->trigger = new \Yana\Db\Ddl\Trigger();
        $this->constraint = new \Yana\Db\Ddl\Constraint();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->database);
        unset($this->field);
        unset($this->foreignkey);
        unset($this->form);
        unset($this->function);
        unset($this->functionimplementation);
        unset($this->functionparameter);
        unset($this->logcreate);
        unset($this->logdrop);
        unset($this->logrename);
        unset($this->logsql);
        unset($this->logupdate);
        unset($this->sequence);
        unset($this->table);
        unset($this->view);
        unset($this->changelog);
        unset($this->indexcolumn);
        unset($this->viewfield);
        unset($this->event);
        unset($this->init);
        unset($this->trigger);
        unset($this->constraint);
        chdir(CWD);
    }

    /**
     * Data-provider for testTitle
     */
    public function dataTitle()
    {
        return array(
            array('database'),
            array('form'),
            array('function'),
            array('table'),
            array('view'),
            array('event')
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
     * get type
     *
     * @test
     */
    public function testGetType()
    {
        // \Yana\Db\Ddl\Logs\Create
        $get = $this->logcreate->getType();
        $this->assertEquals('create', $get, 'assert failed, "\Yana\Db\Ddl\Logs\Create" : expected value "create" - the values should be equal.');

        // \Yana\Db\Ddl\Logs\Rename
        $get = $this->logrename->getType();
        $this->assertEquals('rename', $get, 'assert failed, "\Yana\Db\Ddl\Logs\Rename" : expected value "rename" - the values should be equal.');

        // \Yana\Db\Ddl\Logs\Sql
        $get = $this->logsql->getType();
        $this->assertEquals('sql', $get, 'assert failed, "\Yana\Db\Ddl\Logs\Sql" : expected value "sql" - the values should be equal.');

         // \Yana\Db\Ddl\Logs\Update
        $get = $this->logupdate->getType();
        $this->assertEquals('update', $get, 'assert failed, "\Yana\Db\Ddl\Logs\Update" : expected value "update" - the values should be equal.');

        // \Yana\Db\Ddl\Logs\Drop
        $get = $this->logdrop->getType();
        $this->assertEquals('drop', $get, 'assert failed, "\Yana\Db\Ddl\Logs\Drop" : expected value "drop" - the values should be equal.');
    }

    /**
     * check types and params
     *
     * @test
     */
    public function testTypesAndParams()
    {
        $type = $this->logchange->getType();
        $this->assertNull($type, "Undefined type should be null.");

        $this->logchange->setType();
        $type = $this->logchange->getType();
        $message = "Attribute \Yana\Db\Ddl\ChangeLog::type should default to 'default'.";
        $this->assertEquals('default', $type, $message);

        $this->logchange->setType('Test');
        $type = $this->logchange->getType();
        $message = "\Yana\Db\Ddl\ChangeLog::getType should return same value as previously set by setType().";
        $this->assertEquals('Test', $type, $message);

        $expectedParams = array();
        $parameters = $this->logchange->getParameters();
        $message = "Empty parameter list should be returned as empty array.";
        $this->assertEquals($expectedParams, $parameters, $message);

        $expectedParams[] = 'test';
        $this->logchange->addParameter('test');
        $parameters = $this->logchange->getParameters();
        $message = "Unnamed parameter 'test' must be added.";
        $this->assertEquals($expectedParams, $parameters, $message);

        $expectedParams['Foo'] = 'bar';
        $this->logchange->addParameter('bar', 'Foo');
        $parameters = $this->logchange->getParameters();
        $message = "Named parameter 'Foo'='bar' must be added.";
        $this->assertEquals($expectedParams, $parameters, $message);

        $this->logchange->dropParameters();
        $expectedParams = array();
        $parameters = $this->logchange->getParameters();
        $message = "After calling dropParameters, getParameters must return an empty array.";
        $this->assertEquals($expectedParams, $parameters, $message);

        $this->logchange->setType("");
        $type = $this->logchange->getType();
        $this->assertNull($type, "Unable to unset type.");
    }

    /**
     * set type
     *
     * @test
     */
    public function testSetType()
    {
        // DDL FunctionParameter
        $this->functionparameter->setType('integer');
        $result = $this->functionparameter->getType();
        $this->assertEquals('integer', $result, 'assert failed, \Yana\Db\Ddl\Functions\Parameter : the expecting value of getType should be "integer" - the values should be equal');

        $this->functionparameter->setType('');
        $result = $this->functionparameter->getType();
        $this->assertEquals('', $result, 'assert failed, \Yana\Db\Ddl\Functions\Parameter : the expecting value of getType should be an empty result - the values should be equal');
    }

    /**
     * Mode
     *
     * @test
     */
    public function testMode()
    {
        // DDL FunctionParameter
        $this->functionparameter->setMode(0);
        $result = $this->functionparameter->getMode();
        $this->assertEquals(0, $result, 'assert failed, \Yana\Db\Ddl\Functions\Parameter : the value should be match the number 0');

        $this->functionparameter->setMode(2);
        $result = $this->functionparameter->getMode();
        $this->assertEquals(2, $result, 'assert failed, \Yana\Db\Ddl\Functions\Parameter : the value should be match the number 2');

        $this->functionparameter->setMode(1);
        $result = $this->functionparameter->getMode();
        $this->assertEquals(1, $result, 'assert failed, \Yana\Db\Ddl\Functions\Parameter : the value should be match the number 1');

        $this->functionparameter->setMode(20);
        $result = $this->functionparameter->getMode();
        $this->assertEquals(0, $result, 'assert failed, \Yana\Db\Ddl\Functions\Parameter : expected value is the default number 0 - only 0, 1, 2 numbers can be used in setMode by setting an other number the default must be choosen');
    }

    /**
     * Data-provider for testDescription
     */
    public function dataDescription()
    {
        return array(
            array('field'),
            array('database'),
            array('form'),
            array('function'),
            array('view'),
            array('table'),
            array('sequence'),
            array('logcreate'),
            array('logchange'),
            array('logsql')
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
     * Where
     *
     * @test
     */
    public function testWhere()
    {
        // DDL View
        $this->view->setWhere('where');
        $result = $this->view->getWhere();
        $this->assertEquals('where', $result, 'assert failed, \Yana\Db\Ddl\Views\View : "setWhere" expected "where" as value - the values should be equal');

        $this->view->setWhere('');
        $result = $this->view->getWhere();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Views\View : "setWhere" is expected null');
    }

    /**
     * check option
     *
     * @test
     */
    public function testCheckOption()
    {
        // DDL View
        $hasChecked = $this->view->hasCheckOption();
        $this->assertFalse($hasChecked, 'assert failed, "\Yana\Db\Ddl\Views\View" : false expected - no checkOption is set');

        $this->view->setCheckOption(\Yana\Db\Ddl\Views\ConstraintEnumeration::CASCADED);
        $result = $this->view->getCheckOption();
        $this->assertEquals(\Yana\Db\Ddl\Views\ConstraintEnumeration::CASCADED, $result, 'expected "1" as value - the values should be equal');

        $hasChecked = $this->view->hasCheckOption();
        $this->assertTrue($hasChecked, 'assert failed, "\Yana\Db\Ddl\Views\View" : true expected - checkOption is set ');

        $this->view->setCheckOption(\Yana\Db\Ddl\Views\ConstraintEnumeration::LOCAL);
        $result = $this->view->getCheckOption();
        $this->assertEquals(\Yana\Db\Ddl\Views\ConstraintEnumeration::LOCAL, $result, 'expected "2" as value - the values should be equal');

        $this->view->setCheckOption(\Yana\Db\Ddl\Views\ConstraintEnumeration::NONE);
        $result = $this->view->getCheckOption();
        $this->assertEquals(\Yana\Db\Ddl\Views\ConstraintEnumeration::NONE, $result, 'assert failed, \Yana\Db\Ddl\Column : expected "0" as value - the values should be equal');

        $this->view->setCheckOption(20);
        $result = $this->view->getCheckOption();
        $this->assertEquals(0, $result, 'assert failed, \Yana\Db\Ddl\Column : expected value is the default number 0 - only 0, 1, 2 numbers can be used in setCheckOption by setting an other number the default must be choosen');
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
     * subject
     *
     * @test
     */
    public function testSubject()
    {
        // DDL LogCreate
        $this->logcreate->setSubject('column');
        $result = $this->logcreate->getSubject();
        $this->assertEquals('column', $result, 'assert failed, \Yana\Db\Ddl\Logs\Create : the expected result should be the value "column" - the values should be equal');
    }

    /**
     * SQL
     *
     * @test
     */
    public function testSQL()
    {
        // DDL LogSql
        $this->logsql->setSQL('sql');
        $result = $this->logsql->getSQL();
        $this->assertEquals('sql', $result, 'assert failed, \Yana\Db\Ddl\Logs\Sql : the expected result should be the value "sql" - the values should be equal');

        $this->logsql->setSQL('');
        $result = $this->logsql->getSQL();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Logs\Sql : the value is expected null');

        // DDL DatabaseInit
        $this->init->setSQL('sql');
        $result = $this->init->getSQL();
        $this->assertEquals('sql', $result, 'assert failed, \Yana\Db\Ddl\DatabaseInit : the expected result should be the value "sql" - the values should be equal');

        $this->init->setSQL('');
        $result = $this->init->getSQL();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\DatabaseInit : the value is expected null');
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

       // ddl field
       $this->field->setReadonly(true);
       $result = $this->field->isReadonly();
       $this->assertTrue($result, 'assert failed, \Yana\Db\Ddl\Field : expected true - setReadonly was set with true');

       $this->field->setReadonly(false);
       $result = $this->field->isReadonly();
       $this->assertFalse($result, 'assert failed, \Yana\Db\Ddl\Field : expected false - setReadonly was set with false');

       // DDL View
       $this->view->setReadonly(true);
       $result = $this->view->isReadonly();
       $this->assertTrue($result, 'assert failed, \Yana\Db\Ddl\Views\View : expected true - setReadonly was set with true');

       $this->view->setReadonly(false);
       $result = $this->view->isReadonly();
       $this->assertFalse($result, 'assert failed, \Yana\Db\Ddl\Views\View : expected false - setReadonly was set with false');

       // DDL Table
       $this->table->setReadonly(true);
       $result = $this->table->isReadonly();
       $this->assertTrue($result, 'assert failed, \Yana\Db\Ddl\Table : expected true - setReadonly was set with true');

       $this->table->setReadonly(false);
       $result = $this->table->isReadonly();
       $this->assertFalse($result, 'assert failed, \Yana\Db\Ddl\Table : expected false - setReadonly was set with false');
    }

    /**
     *  Visible
     *
     * @test
     */
    public function testVisible()
    {
       // ddl field
       $this->field->setVisible(true);
       $result = $this->field->isVisible();
       $this->assertTrue($result, 'assert failed, \Yana\Db\Ddl\Field : expected true - setVisible was set with true');

       $this->field->setVisible(false);
       $result = $this->field->isVisible();
       $this->assertFalse($result, 'assert failed, \Yana\Db\Ddl\Field : expected false - setVisible was set with false');
    }

    /**
     * Cycle
     *
     * @test
     */
    public function testCycle()
    {
       // DDL Sequence
       $this->sequence->setCycle(true);
       $result = $this->sequence->isCycle();
       $this->assertTrue($result, 'assert failed, \Yana\Db\Ddl\Sequence : expected true - setCycle was set with true');

       $this->sequence->setCycle(false);
       $result = $this->sequence->isCycle();
       $this->assertFalse($result, 'assert failed, \Yana\Db\Ddl\Sequence : expected false - setCycle was set with false');
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
     * Length
     *
     * @test
     */
    public function testGetLength()
    {
        // \Yana\Db\Ddl\IndexColumn
        $this->indexcolumn->setLength(20);
        $length = $this->indexcolumn->getLength();
        $this->assertEquals(20, $length, 'assert failed, \Yana\Db\Ddl\IndexColumn: set und get do not match');

        $this->indexcolumn->setLength(0);
        $length = $this->indexcolumn->getLength();
        $this->assertNull($length, '\Yana\Db\Ddl\IndexColumn:setLength should return Null, if not set');
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

        // \Yana\Db\Ddl\Constraint
        $testArray1 = array("someConstraints 1", "someConstraints 2", "someConstraints 3");
        $this->constraint->setConstraint($testArray1[0]);
        $result1 = $this->constraint->getConstraint();
        $this->assertEquals($testArray1[0], $result1, '\Yana\Db\Ddl\Constraint::getConstraint failed');

        $this->constraint->setConstraint();
        $result1 = $this->constraint->getConstraint();
        $this->assertNull($result1, '\Yana\Db\Ddl\Constraint::getConstrain failed');
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
        // DDL Form
        $this->form->setTable('abcd');
        $result = $this->form->getTable();
        $this->assertEquals('abcd', $result, 'assert failed, \Yana\Db\Ddl\Form : expected "abcd" as value');

        $this->form->setTable('');
        $result = $this->form->getTable();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Form : expected null, non table is set');

        // \Yana\Db\Ddl\Views\Field
        $this->viewfield->setTable('abcd');
        $result = $this->viewfield->getTable();
        $this->assertEquals('abcd', $result, 'assert failed, \Yana\Db\Ddl\Views\Field : expected "abcd" as value');

        $this->viewfield->setTable('');
        $result = $this->viewfield->getTable();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Views\Field : expected null, non table is set');

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

        // magic Field
        $this->form->addField('magicField');
        $result = $this->form->magicField;
        $this->assertTrue($result instanceof \Yana\Db\Ddl\Field, 'assert failed, expected null - field was dropt before');

        // magic View
        $this->view->addField('magicViewfield');
        $result = $this->view->magicViewfield;
        $this->assertTrue($result instanceof \Yana\Db\Ddl\Views\Field, 'assert failed, expected null - view field was dropt before');

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
     * Tables
     *
     * @test
     */
    public function testTables()
    {
        // DDL View
        $array = array('one', 'two');
        $this->view->setTables($array);
        $get = $this->view->getTables($array);
        $this->assertEquals($array, $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : expected the same table as which was set with setTable, values should be equal');
    }

    /**
     * TablesInvalidArgumentException
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     *
     * @test
     */
    public function testSetTablesInvalidArgumentException()
    {
        // DDL View
        $this->view->setTables(array());
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
     * Query
     *
     * @test
     */
    public function testQuery()
    {
       // \Yana\Db\Ddl\Views\View
       $set = $this->view->setQuery('');
       $this->assertInternalType('array', $set, 'assert failed, the value is not from type array');
       $this->assertEquals(0, count($set), 'assert failed, expected an array with 0 entries , no query is set');

       $get = $this->view->getQueries();
       $this->assertInternalType('array', $get, 'assert failed, the value is not from type array');
       $this->assertEquals(0, count($get), 'assert failed, expected an array with 0 entries , no query is set');

       $get = $this->view->getQuery('mysql');
       $this->assertNull($get, 'assert failed, expected null , the key doesnt exist in array');

       $set = $this->view->setQuery('query', 'mysql');
       $this->assertArrayHasKey('mysql', $set, 'assert failed, "\Yana\Db\Ddl\Views\View" : the key "mysql" should be match the array key');
       $set = $this->view->setQuery('query', 'generic');
       $get = $this->view->getQuery('mysql');
       $this->assertEquals('query', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : the values should be equal');

       $get = $this->view->getQueries();
       $this->assertArrayHasKey('mysql', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : the key "mysql" should be match the array key');
       $this->assertArrayHasKey('generic', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : the key "generic" should be match the array key');
    }

    /**
     * addEntry
     *
     * @test
     */
    public function testAddEntry()
    {
        for ($i = 1; $i <10; $i++)
        {
            $nr = sprintf("%04d",$i);
            $log = new \Yana\Db\Ddl\Logs\Create('logcreate');
            $log->setName("name_" . $nr);
            $log->setVersion($nr);
            $this->changelog->addEntry($log);
        }

        $countAll = count($this->changelog->getEntries());
        $countV1 = count($this->changelog->getEntries("0004"));

        $this->assertEquals($countAll , 9, '\Yana\Db\Ddl\ChangeLog, adding Logs or retrieving them failed');
        $this->assertEquals($countV1, 5, 'assert failed, adding Logs with a Version number or retrieving them failed');
    }

    /**
     * dropEntries
     *
     * @test
     */
    public function testDropEntries()
    {
        for ($i = 1; $i <10; $i++)
        {
            $nr = sprintf("%04d",$i);
            $log = new \Yana\Db\Ddl\Logs\Create('logcreate');
            $log->setName("name_" . $nr);
            $log->setVersion($nr);
            $this->changelog->addEntry($log);
        }

        // let's be bad guys, dan drop everything again
        $this->changelog->dropEntries();
        $countAll = count($this->changelog->getEntries());

        $this->assertEquals($countAll , 0, '\Yana\Db\Ddl\ChangeLog, dropping the entries has failed');
    }

    /**
     * getEntries
     *
     * @test
     */
    public function testGetEntries()
    {
        // First: create a lot of different entries
        // Second: count them
        for ($i = 1; $i <30; $i++)
        {
            $nr = sprintf("%04d",$i);
            if ($i % 3 == 0) {
                $log = new \Yana\Db\Ddl\Logs\Create('logcreate');
                $log->setName("name_" . $nr);
            } else {
                $log = new \Yana\Db\Ddl\Logs\Sql();
            }
            $log->setVersion($nr);
            switch ($i % 3)
            {
                case 1:
                    $log->setDBMS('mysql');
                break;
                case 2:
                    $log->setDBMS('oracle');
                break;
            }
            $this->changelog->addEntry($log);
        }

        $countAll = count($this->changelog->getEntries(null));
        $this->assertEquals($countAll , 9, '\Yana\Db\Ddl\ChangeLog, dropping the entries has failed');

        $countAll = count($this->changelog->getEntries(null, 'mysql'));
        $this->assertEquals($countAll , 19, '\Yana\Db\Ddl\ChangeLog, dropping the entries has failed');

        $countAll = count($this->changelog->getEntries(null, 'oracle'));
        $this->assertEquals($countAll , 19, '\Yana\Db\Ddl\ChangeLog, dropping the entries has failed');

        // truncate list of changes
        $this->changelog->dropEntries();
        $countAll = count($this->changelog->getEntries());
        $this->assertEquals($countAll , 0, '\Yana\Db\Ddl\ChangeLog, dropping the entries has failed');
    }

    /**
     * add field to view
     *
     * @test
     */
    public function testAddViewField()
    {
        // DDL View
        $get = $this->view->getFields();
        $this->assertEquals(0, count($get), 'assert failed, the values should be equal, no fields found - "0" expected');

        $this->view->addField('name');
        $this->view->addField('abcd');
        $this->view->addField('qwerty');

        $get = $this->view->getFields();
        $this->assertInternalType('array', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : the value is not from type array');

        $this->assertArrayHasKey('name', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : expected true - the value should be match a key in array');
        $this->assertArrayHasKey('abcd', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : expected true - the value should be match a key in array');
        $this->assertArrayHasKey('qwerty', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : expected true - the value should be match a key in array');

        $get = $this->view->getField('abcd');
        $this->assertInternalType('object', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : the value is not from type object');
        $this->assertTrue($get instanceof \Yana\Db\Ddl\Views\Field, 'assert failed, "\Yana\Db\Ddl\Views\View" : the value should be an instance of \Yana\Db\Ddl\Views\Field');

        $this->view->dropField('abcd');
        try {
            $get = $this->view->getField('abcd');
            $this->fail("\Yana\Db\Ddl\Views\View::dropField didn't drop the Column");
        } catch (\Exception $e) {
            //success
        }
    }

    /**
     * add field to form
     *
     * @test
     */
    public function testAddFormField()
    {
        $get = $this->form->getFields();
        $this->assertEquals(0, count($get), 'assert failed, the values should be equal "0" expected');

        $this->form->addField('name');
        $this->form->addField('abcd');
        $this->form->addField('qwerty');

        $get = $this->form->getFields();
        $this->assertInternalType('array', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : the value is not from type array');

        $this->assertArrayHasKey('name', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : expected true - the value should be match a key in array');
        $this->assertArrayHasKey('abcd', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : expected true - the value should be match a key in array');
        $this->assertArrayHasKey('qwerty', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : expected true - the value should be match a key in array');

        $get = $this->form->getField('abcd');
        $this->assertInternalType('object', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : the value is not from type object');
        $this->assertTrue($get instanceof \Yana\Db\Ddl\Field, 'assert failed, "\Yana\Db\Ddl\Views\View" : the value should be an instance of \Yana\Db\Ddl\Field');

        $this->form->dropField('abcd');
        try {
            $this->form->dropField('abcd');
            $this->fail('Field was not deleted');
        } catch (\Yana\Core\Exceptions\NotFoundException $e) {
            // success
        }

    }

    /**
     * addFieldInvalidArgumentException
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     *
     * @test
     */
    public function testAddFieldInvalidArgumentException()
    {
        // DDL View
        $this->view->addField('');
    }

    /**
     * getFieldInvalidArgumentException4
     *
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     *
     * @test
     */
    public function testGetFieldNotFoundException()
    {
        //\Yana\Db\Ddl\Views\View
        $this->view->getField('nonexist');
    }

    /**
     * FieldAlreadyExistsException
     *
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     *
     * @test
     */
    public function testAddFieldAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->form->addField('field');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->form->addField('field');
    }

    /**
     * @test
     */
    public function testIsForm()
    {
        $this->assertFalse($this->form->isForm('non-existing-form'));
    }

    /**
     * getFormInvalidArgumentException
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     *
     * @test
     */
    public function testGetFormInvalidArgumentException()
    {
        $this->form->getForm('non-existing-form');
    }

    /**
     * get Query
     *
     * @test
     */
    public function testgetQuery()
    {
        $result = $this->view->getQueries();
        $this->assertTrue(empty($result), '\Yana\Db\Ddl\Views\View::getQueries queries should be void in the beginning');

        $this->view->setQuery("genericQuery");
        $this->view->setQuery("mysqlQuery", "mysql");
        $result = $this->view->getQueries();
        $this->assertTrue(count($result) == 2, '\Yana\Db\Ddl\Views\View::getQueries should return two different Query-Types');
        $result = $this->view->getQuery();
        $this->assertTrue(count($result) == 1, '\Yana\Db\Ddl\Views\View::getQueries should return the generic Query');
        $result = $this->view->getQuery('oracle');
        $this->assertNull($result, '\Yana\Db\Ddl\Views\View::getQueries should return no query because for this dbms there had been no query set');

        $this->view->dropQuery('mysql');
        $result = $this->view->getQueries();
        $this->assertTrue(count($result) == 1, '\Yana\Db\Ddl\Views\View::dropQueries should have dropped one of the Query-Types');
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
     * Sorting
     *
     * @test
     */
    public function testSorting()
    {
        $result = true;
        try {
            $this->indexcolumn->setSorting(4711);
        } catch (\Exception $e) {
            $result = false;
        }
        $this->assertFalse($result, "\Yana\Db\Ddl\IndexColumn::setSorting should not accept anything but Boolean");

        $this->indexcolumn->setSorting();
        $descending = $this->indexcolumn->isDescendingOrder();
        $ascending = $this->indexcolumn->isAscendingOrder();
        $this->assertFalse($descending, '\Yana\Db\Ddl\IndexColumn::isDescendingOrder setting or retrieving the sorting is misaligned');
        $this->assertTrue($ascending, '\Yana\Db\Ddl\IndexColumn::isAscendingOrder setting or retrieving the sorting is misaligned');

        $this->indexcolumn->setSorting(false);
        $descending = $this->indexcolumn->isDescendingOrder();
        $ascending = $this->indexcolumn->isAscendingOrder();
        $this->assertTrue($descending, '\Yana\Db\Ddl\IndexColumn::isDescendingOrder setting or retrieving the sorting is misaligned');
        $this->assertFalse($ascending, '\Yana\Db\Ddl\IndexColumn::isAscendingOrder setting or retrieving the sorting is misaligned');
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
        $this->assertCount(7, $get);
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
     * Title
     *
     * @test
     */
    public function testTitleField()
    {
        // ddl field
        $this->field->setTitle('abcd');
        $result = $this->field->getTitle();
        $this->assertEquals('abcd', $result, 'assert failed, \Yana\Db\Ddl\Field : expected "abcd" as value, the values should be equal');

        $this->field->setTitle('');
        $result = $this->field->getTitle();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Field : expected null, no label is set');
    }

    /**
     * Name
     *
     * @test
     */
    public function testSetName()
    {
        // \Yana\Db\Ddl\Logs\Create
        $this->logcreate->setName('name');
        $get = $this->logcreate->getName();
        $this->assertEquals('name', $get, 'assert failed, the values should be equal, "\Yana\Db\Ddl\Logs\Create" :expected "name" as value, the values should be equal');
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
     * Name
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testSetNameInvalidArgument1()
    {
        $this->logcreate->setName('');
    }

    /**
     * OldName
     *
     * @test
     */
    public function testOldName()
    {
        // \Yana\Db\Ddl\Logs\Rename
        $this->logrename->setOldName('name');
        $get = $this->logrename->getOldName();
        $this->assertEquals('name', $get, 'assert failed, the values should be equal, "\Yana\Db\Ddl\Logs\Rename" :expected "name" as value, the values should be equal');

        $this->logrename->setOldName('');
        $get = $this->logrename->getOldName();
        $this->assertNull($get, 'assert failed, "\Yana\Db\Ddl\Logs\Rename" : expected null, the OldName is not set');
    }

    /**
     * Order by
     *
     * @test
     */
    public function testOrderBy()
    {
        $array = array();
        // DDL View
        $this->view->setOrderBy(array('qwerty'));
        $get = $this->view->getOrderBy();
        $this->assertEquals(array('qwerty'), $get, 'assert failed, the values should be equal, "\Yana\Db\Ddl\Views\View" :the arrays should be match each other');
        $isDesc = $this->view->isDescendingOrder();
        $this->assertFalse($isDesc, 'assert failed, "\Yana\Db\Ddl\Views\View" : expected false, no descendingOrder is set');

        $this->view->setOrderBy($array, true);
        $get = $this->view->getOrderBy();
        $this->assertEquals(0, count($get), 'assert failed, the values should be equal, "\Yana\Db\Ddl\Views\View" :the array should be match each other');
        $isDesc = $this->view->isDescendingOrder();
        $this->assertTrue($isDesc, 'assert failed, "\Yana\Db\Ddl\Views\View" : expected true, descendingOrder is set');
    }

    /**
     * Property name
     *
     * @test
     */
    public function testPropertyName()
    {
        // \Yana\Db\Ddl\Logs\Update
        $this->logupdate->setPropertyName('property');
        $get = $this->logupdate->getPropertyName();
        $this->assertEquals('property', $get, 'assert failed, the values should be equal, "\Yana\Db\Ddl\Logs\Update" :expected value "property" ');

        $this->logupdate->setPropertyName('');
        $get = $this->logupdate->getPropertyName();
        $this->assertNull($get, 'assert failed, "\Yana\Db\Ddl\Logs\Update" : expected null, PropertyName is not set');
    }

    /**
     * Property value
     *
     * @test
     */
    public function testSetPropertyValue()
    {
        // \Yana\Db\Ddl\Logs\Update
        $this->logupdate->setPropertyValue('propertyValue');
        $get = $this->logupdate->getPropertyValue();
        $this->assertEquals('propertyValue', $get, 'assert failed, the values should be equal, "\Yana\Db\Ddl\Logs\Update" : expected "propertyValue" as value');

        $this->logupdate->setPropertyValue('');
        $get = $this->logupdate->getPropertyValue();
        $this->assertNull($get, 'assert failed, "\Yana\Db\Ddl\Logs\Update" : expected null, the PropertyValue is not set');
    }

    /**
     * Start
     *
     * @test
     */
    public function testStart()
    {
        $this->sequence->setStart(1);
        $get = $this->sequence->getStart();
        $this->assertEquals(1, $get, 'assert failed, \Yana\Db\Ddl\Sequence : expected "1" as number');

        $this->sequence->setStart(0);
        $get = $this->sequence->getStart();
        $this->assertNull($get, 'assert failed, \Yana\Db\Ddl\Sequence : expected null, start is not set');
    }

    /**
     * Start
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testSetStartInvalidArgument1()
    {
        $this->sequence->setMin(6);
        $this->sequence->setStart(5);
    }

    /**
     * Increment
     *
     * @test
     */
    public function testIncrement()
    {

        $get = $this->sequence->getIncrement();
        $this->assertEquals(1, $get, 'if not defined otherwise, Sequenz should iterate with 1-Steps');

        $this->sequence->setIncrement(2);
        $get = $this->sequence->getIncrement();
        $this->assertEquals(2, $get, 'assert failed, \Yana\Db\Ddl\Sequence : the values should be equal');

        try {
            $this->sequence->setIncrement(0);
            $this->fail("Increment value may not be set to '0'.");
        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {
            // success
        }
    }

    /**
     * Implementation
     *
     * @test
     */
    public function testImplementation()
    {
       $test3 = $this->function->getImplementation("mysql");
       $this->assertNull($test3, "\Yana\Db\Ddl\Functions\Object, no test implementations are set");

       $f1 = $this->function->addImplementation('mysql');
       $f2 = $this->function->addImplementation('oracle');

       $test1 = $this->function->getImplementations();
       $this->assertEquals(count($test1), 2, "\Yana\Db\Ddl\Functions\Object, a problem with reading/writing implementations has occured");

       $test2 = $this->function->getImplementation("mysql");
       $this->assertEquals(count($test2), 1, "\Yana\Db\Ddl\Functions\Object, a problem with reading specified implementations has occured");
    }

    /**
     * ImplementationAlreadyExistsException
     *
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     *
     * @test
     */
    public function testImplementationAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->function->addImplementation();
        } catch (\Yana\Core\Exceptions\AlreadyExistsException $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->function->addImplementation();
    }

    /**
     * Increment
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    function testSetIncrementInvalidArgument()
    {
         $this->sequence->setIncrement(0);
    }

    /**
     * Min
     *
     * @test
     */
    public function testMin()
    {
        $this->sequence->setMin();
        $get = $this->sequence->getMin();
        $this->assertEquals(null, $get, 'setMin() without arguments should reset the property.');

        $this->sequence->setMin(1);
        $get = $this->sequence->getMin();
        $this->assertEquals(1, $get, 'getMin() should return the same value as previously set by setMin().');

        $this->sequence->setStart(2);
        $this->sequence->setMin(2); // should succeed
        $get = $this->sequence->getMin();
        $this->assertEquals(2, $get, 'setMin() to lower boundary must succeed.');
        try {
            $this->sequence->setMin(3);
            $this->fail("Should not be able to set minimum higher than start value.");
        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {
            // success
        }
    }

    /**
     * Max
     *
     * @test
     */
    public function testMax()
    {
        $this->sequence->setMax();
        $get = $this->sequence->getMax();
        $this->assertEquals(null, $get, 'setMax() without arguments should reset the property.');

        $this->sequence->setMax(3);
        $get = $this->sequence->getMax();
        $this->assertEquals(3, $get, 'getMax() should return the same value as previously set by setMax().');

        $this->sequence->setStart(2);
        $this->sequence->setMax(2); // should succeed
        $get = $this->sequence->getMax();
        $this->assertEquals(2, $get, 'setMax() to lower boundary must succeed.');
        try {
            $this->sequence->setMax(1);
            $this->fail("Should not be able to set maximum lower than start value.");
        } catch (\Yana\Core\Exceptions\InvalidArgumentException $e) {
            // success
        }
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
     * Template
     *
     * @test
     */
    public function testTemplate()
    {
        // DDL Form
        $this->form->setTemplate('template');
        $result = $this->form->getTemplate();
        $this->assertEquals('template', $result, 'assert failed, \Yana\Db\Ddl\Form : expected value is "template"');

        $this->form->setTemplate('');
        $result = $this->form->getTemplate();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Form : expected null, non template is set');
    }

    /**
     * Language
     *
     * @test
     */
    public function testLanguage()
    {
        // DDL FunctionImplementation
        $this->functionimplementation->setLanguage('language');
        $validate = $this->functionimplementation->getLanguage();
        $this->assertEquals('language', $validate, '\Yana\Db\Ddl\Functions\Implementation : expected value is "language"');
    }

    /**
     * Code
     *
     * @test
     */
    public function testCode()
    {
        // DDL FunctionImplementation
        $this->functionimplementation->setCode('code');
        $validate = $this->functionimplementation->getCode();
        $this->assertEquals('code', $validate, '\Yana\Db\Ddl\Functions\Implementation : expected value is "code"');
    }

    /**
     * Return
     *
     * @test
     */
    public function testReturn()
    {
        // DDL FunctionImplementation
        $this->functionimplementation->setReturn('return');
        $validate = $this->functionimplementation->getReturn();
        $this->assertEquals('return', $validate, '\Yana\Db\Ddl\Functions\Implementation : expected "return" as value');

        $this->functionimplementation->setReturn('');
        $result = $this->functionimplementation->getReturn();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Functions\Implementation : expected null, the return is not set');
    }

    /**
     * parameter definition
     *
     * @test
     */
    public function testParameter()
    {
        // DDL FunctionImplementation
        $this->functionimplementation->addParameter('control');
        $valid = $this->functionimplementation->getParameters();
        $this->assertArrayHasKey('control', $valid, 'assert failed, \Yana\Db\Ddl\Functions\Implementation : expected "control" as value');

        $valid = $this->functionimplementation->getParameterNames();
        $this->assertEquals('control', $valid[0], 'assert failed, \Yana\Db\Ddl\Functions\Implementation : expected "control" as value');

        $this->functionimplementation->getParameter('control');
        $this->functionimplementation->addParameter('test');
        $this->functionimplementation->addParameter('new');

        $valid = $this->functionimplementation->getParameters();
        $this->assertArrayHasKey('control', $valid, 'assert failed, \Yana\Db\Ddl\Functions\Implementation : the value "control" should be match a key in array');
        $this->assertArrayHasKey('test', $valid, 'assert failed, \Yana\Db\Ddl\Functions\Implementation : the value "test" should be match a key in array');
        $this->assertArrayHasKey('new', $valid, 'assert failed, \Yana\Db\Ddl\Functions\Implementation : the value "new" should be match a key in array');

        $this->functionimplementation->dropParameter('test');
        $valid = $this->functionimplementation->getParameters();
        $this->assertArrayNotHasKey('test', $valid, 'assert failed, \Yana\Db\Ddl\Functions\Implementation : the value "test" should not be match a key in array');

        $name = 'a';
        $newParam = $this->functionimplementation->addParameter($name);
        $valid = $this->functionimplementation->getParameters();
        $this->assertArrayHasKey($name, $valid, 'assert failed, \Yana\Db\Ddl\Functions\Implementation : the value "name" should be match a key in array');

        $parameter = $this->functionimplementation->getParameter('b');
        $this->assertNull($parameter, 'function must return NULL for undefined parameter "b"');
    }

    /**
     * addParameterAlreadyExistsException
     *
     * @test
     *
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     */
    public function testAddParameterAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->functionimplementation->addParameter('parameter');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->functionimplementation->addParameter('parameter');
    }

    /**
     * Function: DBMS
     *
     * @test
     */
    public function testFunctionDBMS()
    {
        // DDL FunctionImplementation
        $implementation = $this->function->addImplementation('MsSqL');
        $validate = $implementation->getDBMS();
        $this->assertEquals('mssql', $validate, 'assert failed, \Yana\Db\Ddl\Functions\Implementation : expected "mssql", the values should be equal');

        $implementation = $this->function->addImplementation();
        $validate = $implementation->getDBMS();
        // expected generic
        $this->assertEquals('generic', $validate, 'assert failed, \Yana\Db\Ddl\Functions\Implementation : expected "generic", the values should be equal');
    }

    /**
     * Data-provider for testDBMS
     */
    public function dataDBMS()
    {
        return array(
            array('logsql'),
            array('logchange'),
            array('init'),
            array('trigger'),
            array('constraint')
        );
    }

    /**
     * DBMS
     *
     * @dataProvider dataDBMS
     * @param  string  $propertyName
     * @test
     */
    public function testDBMS($propertyName)
    {
        $object = $this->$propertyName;
        $object->setDBMS('mssql');
        $validate = $object->getDBMS();
        $this->assertEquals('mssql', $validate, get_class($object) . ': getDBMS() must return same value as set via setDBMS().');

        $object->setDBMS();
        $result = $object->getDBMS();
        // expected generic
        $this->assertEquals('generic', $result, get_class($object) . ': default DBMS should be "generic".');

        $object->setDBMS('');
        $result = $object->getDBMS();
        // expected generic
        $this->assertNull($result, get_class($object) . ': empty DBMS value should default to NULL.');
    }

    /**
     * user role
     *
     * @test
     */
    public function testRole()
    {
        // setter and getter
        $this->grant->setRole("test");
        $role = $this->grant->getRole();
        $this->assertEquals("test", $role, 'getRole() should return the same value as set with setRole().');

        // default value
        $this->grant->setRole();
        $role = $this->grant->getRole();
        $this->assertEquals(null, $role, 'User role should default to null.');
    }

    /**
     * user group
     *
     * @test
     */
    public function testUser()
    {
        // setter and getter
        $this->grant->setUser("test");
        $user = $this->grant->getUser();
        $this->assertEquals("test", $user, 'getUser() should return the same value as set with setUser().');

        // default value
        $this->grant->setUser();
        $user = $this->grant->getUser();
        $this->assertEquals(null, $user, 'User group should default to null.');
    }

    /**
     * security level
     *
     * @test
     */
    public function testLevel()
    {
        // setter and getter
        $this->grant->setLevel(0);
        $level = $this->grant->getLevel();
        $this->assertEquals(0, $level, 'getLevel() should return the same value as set with setLevel().');
        $this->grant->setLevel(100);
        $level = $this->grant->getLevel();
        $this->assertEquals(100, $level, 'getLevel() should return the same value as set with setLevel().');

        // default value
        $this->grant->setLevel();
        $level = $this->grant->getLevel();
        $this->assertEquals(null, $level, 'Security level should default to null.');
    }

    /**
     * security level exceeding lower bounds
     *
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testLevelInvalidArgument1()
    {
        $this->grant->setLevel(-1);
    }

    /**
     * security level exceeding upper bounds
     *
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testLevelInvalidArgument2()
    {
        $this->grant->setLevel(101);
    }

    /**
     * select statements
     *
     * @test
     */
    public function testSelect()
    {
        // set to false
        $this->grant->setSelect(false);
        $isSelectable = $this->grant->isSelectable();
        $this->assertFalse($isSelectable, 'isSelectable() should return the same value as set with setSelect().');

        // default value
        $this->grant->setSelect();
        $isSelectable = $this->grant->isSelectable();
        $this->assertTrue($isSelectable, 'Selectable should default to true.');
    }

    /**
     * insert statements
     *
     * @test
     */
    public function testInsert()
    {
        // set to false
        $this->grant->setInsert(false);
        $isInsertable = $this->grant->isInsertable();
        $this->assertFalse($isInsertable, 'isInsertable() should return the same value as set with setInsert().');

        // default value
        $this->grant->setInsert();
        $isInsertable = $this->grant->isInsertable();
        $this->assertTrue($isInsertable, 'Insertable should default to true.');
    }

    /**
     * update statements
     *
     * @test
     */
    public function testUpdate()
    {
        // set to false
        $this->grant->setUpdate(false);
        $isUpdatable = $this->grant->isUpdatable();
        $this->assertFalse($isUpdatable, 'isUpdatable() should return the same value as set with setUpdate().');

        // default value
        $this->grant->setUpdate();
        $isUpdatable = $this->grant->isUpdatable();
        $this->assertTrue($isUpdatable, 'Updatable should default to true.');
    }

    /**
     * delete statements
     *
     * @test
     */
    public function testDelete()
    {
        // set to false
        $this->grant->setDelete(false);
        $isDeletable = $this->grant->isDeletable();
        $this->assertFalse($isDeletable, 'isDeletable() should return the same value as set with setDelete().');

        // default value
        $this->grant->setDelete();
        $isDeletable = $this->grant->isDeletable();
        $this->assertTrue($isDeletable, 'Deletable should default to true.');
    }

    /**
     * grant option
     *
     * @test
     */
    public function testGrantable()
    {
        // set to false
        $this->grant->setGrantOption(false);
        $isGrantable = $this->grant->isGrantable();
        $this->assertFalse($isGrantable, 'isGrantable() should return the same value as set with setGrantOption().');

        // default value
        $this->grant->setGrantOption();
        $isGrantable = $this->grant->isGrantable();
        $this->assertTrue($isGrantable, 'Grantable should default to true.');
    }

    /**
     * event
     *
     * @test
     */
    public function testSetEvent()
    {
        $event = $this->form->addEvent('test');
        $event->setAction('bla');
        $getAll = $this->form->getEvents();
        $this->assertInternalType('array', $getAll, 'assert failed, the value is not from type array');
        $this->assertArrayHasKey('test', $getAll, 'assert failed, the value "test" should be match a key in array');

        $get = $this->form->getEvent('test');
        $this->assertEquals('bla', $get->getAction(), 'assert failed, expected value "bla"');

        $get = $this->form->dropEvent('test');
        $this->assertTrue($get, 'assert failed, event is not droped');

        $get = $this->form->dropEvent('test_foo_bar');
        $this->assertFalse($get, 'assert failed, event does not exist and can\'t be droped');

         $get = $this->form->getEvent('non-existing-event');
         $this->assertNull($get, 'assert failed, expected null for non-exist event');
    }

    /**
     * EventInvalidArgumentException
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     *
     * @test
     */
    public function testAddEventInvalidArgumentException()
    {
        $this->form->addEvent('');
    }

    /**
     * EventInvalidArgumentException
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     *
     * @test
     */
    public function testAddFieldEventInvalidArgumentException()
    {
        $this->field->addEvent('');
    }

    /**
     * Data-provider for testSetGrants
     */
    public function dataSetGrants()
    {
        return array(
            array('form'),
            array('field'),
            array('view'),
            array('table')
        );
    }

    /**
     * Grants
     *
     * @dataProvider dataSetGrants
     * @param  string  $propertyName
     * @test
     */
    public function testGrants($propertyName)
    {
        $object = $this->$propertyName;
        $grant = new \Yana\Db\Ddl\Grant();
        $grant2 = new \Yana\Db\Ddl\Grant();

        $grants = array($grant, $grant2);

        $object->setGrant($grant);
        $object->setGrant($grant2);

        $this->assertEquals($grants, $object->getGrants(), 'assert failed, the values should be equal, expected the same arrays');

        $add = $object->addGrant('user', 'role', 10);
        $this->assertTrue($add instanceof \Yana\Db\Ddl\Grant, 'Function addGrant() should return instance of \Yana\Db\Ddl\Grant.');

        $object->dropGrants();

        $get = $object->getGrants();
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
        $parentColumn = new \Yana\Db\Ddl\Column('Column_Parent');
        $parentForm = new \Yana\Db\Ddl\Form('someform');

        // \Yana\Db\Ddl\ChangeLog
        $childLog = new \Yana\Db\Ddl\ChangeLog($database);
        $parentLog = $childLog->getParent();
        $this->assertEquals($database, $parentLog, '\Yana\Db\Ddl\ChangeLog::getParent, the values should be equal');

        // \Yana\Db\Ddl\Column
        $childColumn = new \Yana\Db\Ddl\Column('column', $parentTable);
        $parentColumn = $childColumn->getParent();
        $this->assertEquals($parentTable, $parentColumn, '\Yana\Db\Ddl\Column::getParent, the values should be equal');

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

        // \Yana\Db\Ddl\Functions\Object
        $childFunction = new \Yana\Db\Ddl\Functions\Object('function', $database);
        $parentFunction = $childFunction->getParent();
        $this->assertEquals($database, $parentFunction, '\Yana\Db\Ddl\Functions\Object::getParent, the values should be equal');

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
     * Old property value
     *
     * @test
     */
    public function testOldPropertyValue()
    {
        // \Yana\Db\Ddl\Logs\Update
        $this->logupdate->setOldPropertyValue('name');
        $get = $this->logupdate->getOldPropertyValue();
        $this->assertEquals('name', $get, 'assert failed, the values should be equal, "\Yana\Db\Ddl\Logs\Rename" :expected value is "name"');

        $this->logupdate->setOldPropertyValue('');
        $get = $this->logupdate->getOldPropertyValue();
        $this->assertNull($get, 'assert failed, "\Yana\Db\Ddl\Logs\Rename" :expected null - OldPropertyValue is not set or empty');
    }

    /**
     * Handler
     *
     * @test
     */
    public function testSetHandler()
    {

        $result = $this->logsql->commitUpdate();
        $this->assertFalse($result, '\Yana\Db\Ddl\Logs\Sql::commitUpdate should return False, if no handler is defined');
        $result = $this->logupdate->commitUpdate();
        $this->assertFalse($result, '\Yana\Db\Ddl\Logs\Update::commitUpdate should return False, if no handler is defined');


        $function = create_function('', '');

        // DDL LogUpdate
        \Yana\Db\Ddl\Logs\Update::setHandler($function);
        $this->logupdate->commitUpdate();

        // DDL LogSql
         \Yana\Db\Ddl\Logs\Sql::setHandler($function);
         $this->logsql->commitUpdate();

        // DDL LogRename
        \Yana\Db\Ddl\Logs\Rename::setHandler($function);
        $this->logrename->commitUpdate();

        // \Yana\Db\Ddl\Logs\Create
        \Yana\Db\Ddl\Logs\Create::setHandler($function);
        $this->logcreate->commitUpdate();

        // \Yana\Db\Ddl\Logs\Drop
        \Yana\Db\Ddl\Logs\Drop::setHandler($function);
        $this->logdrop->commitUpdate();

        // \Yana\Db\Ddl\Logs\Change
        \Yana\Db\Ddl\Logs\Change::setHandler($function);
        $this->logchange->commitUpdate();

        \Yana\Db\Ddl\Logs\Change::setHandler($function, 'test');
        $this->logchange->setType('test');
        $this->logchange->commitUpdate();
    }

    /**
     * HandlerInvalidArgumentException
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     *
     * @test
     */
    public function testSetHandlerInvalidArgumentException()
    {
        // \Yana\Db\Ddl\Logs\Sql
        \Yana\Db\Ddl\Logs\Sql::setHandler('dummy');
    }

    /**
     * HandlerInvalidArgumentException1
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     *
     * @test
     */
    public function testSetHandlerInvalidArgumentException1()
    {
        // \Yana\Db\Ddl\Logs\Update
        \Yana\Db\Ddl\Logs\Update::setHandler('dummy');
    }

    /**
     * HandlerInvalidArgumentException2
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     *
     * @test
     */
    public function testSetHandlerInvalidArgumentException2()
    {
        // \Yana\Db\Ddl\Logs\Rename
        \Yana\Db\Ddl\Logs\Rename::setHandler('dummy');
    }

    /**
     * HandlerInvalidArgumentException3
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     *
     * @test
     */
    public function testSetHandlerInvalidArgumentException3()
    {
        // \Yana\Db\Ddl\Logs\Create
        \Yana\Db\Ddl\Logs\Create::setHandler('dummy');
    }

    /**
     * HandlerInvalidArgumentException4
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     *
     * @test
     */
    public function testSetHandlerInvalidArgumentException4()
    {
        // \Yana\Db\Ddl\Logs\Drop
        \Yana\Db\Ddl\Logs\Drop::setHandler('dummy');
    }

    /**
     * addColumn
     *
     * @test
     */
    public function testaddColumn()
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
     * Alias
     *
     * @test
     */
    public function testSetAlias()
    {
        // \Yana\Db\Ddl\Views\Field
        $this->viewfield->setAlias('abcd');
        $result = $this->viewfield->getAlias();
        $this->assertEquals('abcd', $result, 'assert failed, \Yana\Db\Ddl\Views\Field : alias is not set, values should be equal');

        $this->viewfield->setAlias('');
        $result = $this->viewfield->getAlias();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Views\Field : expected null - alis is not set');
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
     * Css class
     *
     * @test
     */
    public function testCssClass()
    {
        // \Yana\Db\Ddl\Field
        $this->field->setCssClass('cssclass');
        $result = $this->field->getCssClass();
        $this->assertEquals('cssclass', $result , 'assert failed, \Yana\Db\Ddl\Field : the value "cssclass" should be equal with the expecting value');

        $this->field->setCssClass('');
        $result = $this->field->getCssClass();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Field : expected null - cssclass is not set');
    }

    /**
     * TabIndex
     *
     * @test
     */
    public function testTabIndex()
    {
        // \Yana\Db\Ddl\Field
        $this->field->setTabIndex(4);
        $result = $this->field->getTabIndex();
        $this->assertEquals(4, $result , 'assert failed, \Yana\Db\Ddl\Field : the value "4" should be the same as the expected value');

        $this->field->setTabIndex();
        $result = $this->field->getTabIndex();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Field : expected null - tabIndex is not set');
    }

    /**
     * Action
     *
     * @test
     */
    public function testAction()
    {
        // \Yana\Db\Ddl\Event
        $this->event->setAction('action');
        $result = $this->event->getAction();
        $this->assertEquals('action', $result , 'assert failed, \Yana\Db\Ddl\Event : the value "action" should be the same as the expected value');

        $this->event->setAction();
        $result = $this->event->getAction();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Event : expected null - action is not set');
    }

    /**
     * Language
     *
     * @test
     */
    public function testLanguageFormAction()
    {
        // \Yana\Db\Ddl\Event
        $this->event->setLanguage('language');
        $result = $this->event->getLanguage();
        $this->assertEquals('language', $result , 'assert failed, \Yana\Db\Ddl\Event : the value "language" should be the same as the expected value');

        $this->event->setLanguage();
        $result = $this->event->getLanguage();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Event : expected null - language is not set');
    }

    /**
     * Label
     *
     * @test
     */
    public function testLabelFormAction()
    {
        // \Yana\Db\Ddl\Event
        $this->event->setLabel('label');
        $result = $this->event->getLabel();
        $this->assertEquals('label', $result , 'assert failed, \Yana\Db\Ddl\Event :the value "label" should be the same as the expected value');

        $this->event->setLabel();
        $result = $this->event->getLabel();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Event : expected null - label is not set');
    }

    /**
     * Icon
     *
     * @test
     */
    public function testIcon()
    {
        // \Yana\Db\Ddl\Event
        $icon = CWD.'resources/image/logo.png';
        $this->event->setIcon($icon);
        $get = $this->event->getIcon();
        $this->assertInternalType('string', $get, 'assert failed, "\Yana\Db\Ddl\Event:getIcon" the value should be from type string');
        $this->assertEquals($icon, $get, 'assert failed, "\Yana\Db\Ddl\Event:getIcon" the values should be equal - expected the same path to a file');

        $this->event->setIcon('');
        $result = $this->event->getIcon();
        $this->assertNull($result, 'assert failed, \Yana\Db\Ddl\Event : expected null - icon is not set');
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
     * hasAllInput
     *
     * @test
     */
    public function testHasAllInput()
    {
        $this->assertFalse($this->form->hasAllInput(), 'Setting "allinput" must default to false.');
        $this->form->setAllInput(true);
        $this->assertTrue($this->form->hasAllInput(), 'Setting "allinput" should allow value true.');
        $this->form->setAllInput(false);
        $this->assertFalse($this->form->hasAllInput(), 'Setting "allinput" should be reversible.');
    }

    /**
     * Old property value
     *
     * @test
     */
    public function testdropField()
    {
        $this->form->addField('foo');
        $get = $this->form->getField('foo');
        $get = $this->form->dropField('foo');
        $this->assertNull($get, 'assert failed, field is not droped"');
    }

    /**
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     *
     * @test
     */
    public function testdropFieldInvalidArgumentException()
    {
        $this->form->dropField('non-existing-field');
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

}

?>