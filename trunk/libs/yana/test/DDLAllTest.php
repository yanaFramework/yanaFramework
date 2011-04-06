<?php
/**
 * PHPUnit test-case: DDL ALL
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
 * DDL test-case
 *
 * @package  test
 */
class DDLAllTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * @access  protected
     */

    /** @var DDLColumn                 */ protected $ddlcolumn;
    /** @var DDLDatabase               */ protected $ddldatabase;
    /** @var DDLField                  */ protected $ddlfield;
    /** @var DDLForeignKey             */ protected $ddlforeignkey;
    /** @var DDLForm                   */ protected $ddlform;
    /** @var DDLFunction               */ protected $ddlfunction;
    /** @var DDLFunctionImplementation */ protected $ddlfunctionimplementation;
    /** @var DDLFunctionParameter      */ protected $ddlfunctionparameter;
    /** @var DDLIndex                  */ protected $ddlindex;
    /** @var DDLLogCreate              */ protected $ddllogcreate;
    /** @var DDLLogDrop                */ protected $ddllogdrop;
    /** @var DDLLogRename              */ protected $ddllogrename;
    /** @var DDLLogSql                 */ protected $ddllogsql;
    /** @var DDLLogUpdate              */ protected $ddllogupdate;
    /** @var DDLLogChange              */ protected $ddllogchange;
    /** @var DDLSequence               */ protected $ddlsequence;
    /** @var DDLTable                  */ protected $ddltable;
    /** @var DDLView                   */ protected $ddlview;
    /** @var DDLGrant                  */ protected $ddlgrant;
    /** @var DDLIndexColumn            */ protected $ddlindexcolumn;
    /** @var DDLViewField              */ protected $ddlviewfield;
    /** @var DDLEvent                  */ protected $ddlevent;
    /** @var DDLChangeLog              */ protected $ddlchangelog;
    /** @var DDLDatabaseInit           */ protected $ddldatabaseinit;
    /** @var DDLTrigger                */ protected $ddltrigger;
    /** @var DDLConstraint             */ protected $ddlconstraint;

    /**#@-*/

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        chdir(CWD . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

        $this->ddldatabase = new DDLDatabase();
        $this->ddltable = new DDLTable('table');
        $this->ddlcolumn = new DDLColumn('column');
        $this->ddlfield = new DDLField('field');
        $this->ddlforeignkey = new DDLForeignKey('foreignkey');
        $this->ddlform = new DDLForm('form');
        $this->ddlfunction = new DDLFunction('function');
        $this->ddlfunctionimplementation = new DDLFunctionImplementation;
        $this->ddlfunctionparameter = new DDLFunctionParameter('param');
        $this->ddlindex = new DDLIndex('index', $this->ddltable);
        $this->ddllogcreate = new DDLLogCreate('logcreate');
        $this->ddllogdrop = new DDLLogDrop('logdrop');
        $this->ddllogrename = new DDLLogRename('logrename');
        $this->ddllogsql = new DDLLogSql();
        $this->ddllogupdate = new DDLLogUpdate('logupdate');
        $this->ddllogchange = new DDLLogChange();
        $this->ddlsequence = new DDLSequence('sequence');
        $this->ddlview = new DDLView('view');
        $this->ddlgrant = new DDLGrant();
        $this->ddlchangelog = new DDLChangeLog();
        $this->ddlindexcolumn = new DDLIndexColumn('indexColumn');
        $this->ddlviewfield = new DDLViewField('viewfield');
        $this->ddlevent = new DDLEvent('action');
        $this->ddldatabaseinit = new DDLDatabaseInit();
        $this->ddltrigger = new DDLTrigger();
        $this->ddlconstraint = new DDLConstraint();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @covers DDLColumn
     * @covers DDLDatabase
     * @covers DDLField
     * @covers DDLForeignKey
     * @covers DDLForm
     * @covers DDLFunction
     * @covers DDLFunctionImplementation
     * @covers DDLFunctionParameter
     * @covers DDLIndex
     * @covers DDLLogCreate
     * @covers DDLLogDrop
     * @covers DDLLogRename
     * @covers DDLLogSql
     * @covers DDLLogUpdate
     * @covers DDLSequence
     * @covers DDLTable
     * @covers DDLView
     * @covers DDLChangeLog
     * @covers DDLIndexColumn
     * @covers DDLViewField
     * @covers DDLEvent
     * @covers DDLDatabaseInit
     * @covers DDLTrigger
     * @covers DDLConstraint
     *
     * @access protected
     */
    protected function tearDown()
    {
        unset($this->ddlcolumn);
        unset($this->ddldatabase);
        unset($this->ddlfield);
        unset($this->ddlforeignkey);
        unset($this->ddlform);
        unset($this->ddlfunction);
        unset($this->ddlfunctionimplementation);
        unset($this->ddlfunctionparameter);
        unset($this->ddlindex);
        unset($this->ddllogcreate);
        unset($this->ddllogdrop);
        unset($this->ddllogrename);
        unset($this->ddllogsql);
        unset($this->ddllogupdate);
        unset($this->ddlsequence);
        unset($this->ddltable);
        unset($this->ddlview);
        unset($this->ddlchangelog);
        unset($this->ddlindexcolumn);
        unset($this->ddlviewfield);
        unset($this->ddlevent);
        unset($this->ddldatabaseinit);
        unset($this->ddltrigger);
        unset($this->ddlconstraint);
        chdir(CWD);
    }

    /**
     * Data-provider for testTitle
     */
    public function dataTitle()
    {
        return array(
            array('ddlcolumn'),
            array('ddldatabase'),
            array('ddlform'),
            array('ddlfunction'),
            array('ddlindex'),
            array('ddltable'),
            array('ddlview'),
            array('ddlevent')
        );
    }

    /**
     * title
     *
     * @covers DDLColumn::getTitle
     * @covers DDLColumn::setTitle
     * @covers DDLDatabase::getTitle
     * @covers DDLDatabase::setTitle
     * @covers DDLForm::getTitle
     * @covers DDLForm::setTitle
     * @covers DDLFunction::getTitle
     * @covers DDLFunction::setTitle
     * @covers DDLIndex::getTitle
     * @covers DDLIndex::setTitle
     * @covers DDLTable::getTitle
     * @covers DDLTable::setTitle
     * @covers DDLView::getTitle
     * @covers DDLView::setTitle
     * @covers DDLEvent::getTitle
     * @covers DDLEvent::setTitle
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
     * range
     *
     * @covers DDLColumn::getMin
     * @covers DDLColumn::getMax
     * @covers DDLColumn::getStep
     * @covers DDLColumn::setRange
     *
     * @test
     */
    public function testRange()
    {
        $this->assertNull($this->ddlcolumn->getRangeMin(), "Min must default to null.");
        $this->assertNull($this->ddlcolumn->getRangeMax(), "Max must default to null.");
        $this->assertNull($this->ddlcolumn->getRangeStep(), "Step must default to null.");

        $this->ddlcolumn->setRange(0.0, 100.0, 0.5);
        $rangeMin = $this->ddlcolumn->getRangeMin();
        $rangeMax = $this->ddlcolumn->getRangeMax();
        $rangeStep = $this->ddlcolumn->getRangeStep();
        $this->assertEquals(0.0, $rangeMin, "Unable to set min attribute.");
        $this->assertEquals(100.0, $rangeMax, "Unable to set max attribute.");
        $this->assertEquals(0.5, $rangeStep, "Unable to set step attribute.");
    }

    /**
     * get type
     *
     * @covers DDLLogColumn::getType
     * @covers DDLLogCreate::getType
     * @covers DDLLogRename::getType
     * @covers DDLLogSql::getType
     * @covers DDLLogUpdate::getType
     * @covers DDLLogDrop::getType
     *
     * @test
     */
    public function testGetType()
    {
        // DDLLogCreate
        $get = $this->ddlcolumn->getType();
        $this->assertNull($get, 'DDLColumn should return Null, if not defined');

        // DDLLogCreate
        $get = $this->ddllogcreate->getType();
        $this->assertEquals('create', $get, 'assert failed, "DDLLogCreate" : expected value "create" - the values should be equal.');

        // DDLLogRename
        $get = $this->ddllogrename->getType();
        $this->assertEquals('rename', $get, 'assert failed, "DDLLogRename" : expected value "rename" - the values should be equal.');

        // DDLLogSql
        $get = $this->ddllogsql->getType();
        $this->assertEquals('sql', $get, 'assert failed, "DDLLogSql" : expected value "sql" - the values should be equal.');

         // DDLLogUpdate
        $get = $this->ddllogupdate->getType();
        $this->assertEquals('update', $get, 'assert failed, "DDLLogUpdate" : expected value "update" - the values should be equal.');

        // DDLLogDrop
        $get = $this->ddllogdrop->getType();
        $this->assertEquals('drop', $get, 'assert failed, "DDLLogDrop" : expected value "drop" - the values should be equal.');
    }

    /**
     * check types and params
     *
     * @covers DDLLogChange::getType
     * @covers DDLLogChange::setType
     * @covers DDLLogChange::addParameter
     * @covers DDLLogChange::getParameters
     * @covers DDLLogChange::dropParameters
     *
     * @test
     */
    public function testTypesAndParams()
    {
        $type = $this->ddllogchange->getType();
        $this->assertNull($type, "Undefined type should be null.");

        $this->ddllogchange->setType();
        $type = $this->ddllogchange->getType();
        $message = "Attribute DDLLogChange::type should default to 'default'.";
        $this->assertEquals('default', $type, $message);

        $this->ddllogchange->setType('Test');
        $type = $this->ddllogchange->getType();
        $message = "DDLLogChange::getType should return same value as previously set by setType().";
        $this->assertEquals('Test', $type, $message);

        $expectedParams = array();
        $parameters = $this->ddllogchange->getParameters();
        $message = "Empty parameter list should be returned as empty array.";
        $this->assertEquals($expectedParams, $parameters, $message);

        $expectedParams[] = 'test';
        $this->ddllogchange->addParameter('test');
        $parameters = $this->ddllogchange->getParameters();
        $message = "Unnamed parameter 'test' must be added.";
        $this->assertEquals($expectedParams, $parameters, $message);

        $expectedParams['Foo'] = 'bar';
        $this->ddllogchange->addParameter('bar', 'Foo');
        $parameters = $this->ddllogchange->getParameters();
        $message = "Named parameter 'Foo'='bar' must be added.";
        $this->assertEquals($expectedParams, $parameters, $message);

        $this->ddllogchange->dropParameters();
        $expectedParams = array();
        $parameters = $this->ddllogchange->getParameters();
        $message = "After calling dropParameters, getParameters must return an empty array.";
        $this->assertEquals($expectedParams, $parameters, $message);

        $this->ddllogchange->setType("");
        $type = $this->ddllogchange->getType();
        $this->assertNull($type, "Unable to unset type.");
    }

    /**
     * get supported type
     *
     * @covers DDLColumn::getSupportedTypes
     * @test
     */
    public function getSupportedTypes()
    {
        $getSupported = $this->ddlcolumn->getSupportedTypes();
        $this->assertContains("bool", $getSupported, "supported types should at least contain bool, integer and text");
        $this->assertContains("integer", $getSupported, "supported types should at least contain bool, integer and text");
        $this->assertContains("text", $getSupported, "supported types should at least contain bool, integer and text");
    }

    /**
     * set type
     *
     * @covers DDLColumn::getType
     * @covers DDLColumn::setType
     * @covers DDLFunctionParameter::getType
     * @covers DDLFunctionParameter::setType
     *
     * @test
     */
    public function testSetType()
    {
        // DDL Column
        $this->ddlcolumn->setType('string');
        $validate = $this->ddlcolumn->getType();
        $this->assertEquals('string', $validate, 'DDLColumn : the expecting value of getType should be "string" - the values should be equal');

        // DDL FunctionParameter
        $this->ddlfunctionparameter->setType('integer');
        $result = $this->ddlfunctionparameter->getType();
        $this->assertEquals('integer', $result, 'assert failed, DDLFunctionParameter : the expecting value of getType should be "integer" - the values should be equal');

        $this->ddlfunctionparameter->setType('');
        $result = $this->ddlfunctionparameter->getType();
        $this->assertEquals('', $result, 'assert failed, DDLFunctionParameter : the expecting value of getType should be an empty result - the values should be equal');
    }

    /**
     * Mode
     *
     * @covers DDLFunctionParameter::getMode
     * @covers DDLFunctionParameter::setMode
     *
     * @test
     */
    public function testMode()
    {
        // DDL FunctionParameter
        $this->ddlfunctionparameter->setMode(0);
        $result = $this->ddlfunctionparameter->getMode();
        $this->assertEquals(0, $result, 'assert failed, DDLFunctionParameter : the value should be match the number 0');

        $this->ddlfunctionparameter->setMode(2);
        $result = $this->ddlfunctionparameter->getMode();
        $this->assertEquals(2, $result, 'assert failed, DDLFunctionParameter : the value should be match the number 2');

        $this->ddlfunctionparameter->setMode(1);
        $result = $this->ddlfunctionparameter->getMode();
        $this->assertEquals(1, $result, 'assert failed, DDLFunctionParameter : the value should be match the number 1');

        $this->ddlfunctionparameter->setMode(20);
        $result = $this->ddlfunctionparameter->getMode();
        $this->assertEquals(0, $result, 'assert failed, DDLFunctionParameter : expected value is the default number 0 - only 0, 1, 2 numbers can be used in setMode by setting an other number the default must be choosen');
    }

    /**
     * Data-provider for testDescription
     */
    public function dataDescription()
    {
        return array(
            array('ddlcolumn'),
            array('ddlfield'),
            array('ddldatabase'),
            array('ddlform'),
            array('ddlfunction'),
            array('ddlview'),
            array('ddltable'),
            array('ddlsequence'),
            array('ddllogcreate'),
            array('ddlindex'),
            array('ddllogchange'),
            array('ddllogsql')
        );
    }

    /**
     *  description
     *
     * @covers DDLColumn::getDescription
     * @covers DDLColumn::setDescription
     * @covers DDLField::getDescription
     * @covers DDLField::setDescription
     * @covers DDLDatabase::getDescription
     * @covers DDLDatabase::setDescription
     * @covers DDLForm::getDescription
     * @covers DDLForm::setDescription
     * @covers DDLFunction::getDescription
     * @covers DDLFunction::setDescription
     * @covers DDLView::getDescription
     * @covers DDLView::setDescription
     * @covers DDLTable::getDescription
     * @covers DDLTable::setDescription
     * @covers DDLSequence::getDescription
     * @covers DDLSequence::setDescription
     * @covers DDLLogCreate::getDescription
     * @covers DDLLogCreate::setDescription
     * @covers DDLLogChange::getDescription
     * @covers DDLLogChange::setDescription
     * @covers DDLLogSql::getDescription
     * @covers DDLLogSql::setDescription
     * @covers DDLIndex::getDescription
     * @covers DDLIndex::setDescription
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
        $this->assertEquals('description', $result, 'assert failed, DDLColumn : expected value is "description"  - the values should be equal');

        $object->setDescription('');
        $result = $object->getDescription();
        $this->assertNull($result, 'assert failed, DDLColumn : the description is expected null');
    }

    /**
     * Where
     *
     * @covers DDLView::getWhere
     * @covers DDLView::setWhere
     *
     * @test
     */
    public function testWhere()
    {
        // DDL View
        $this->ddlview->setWhere('where');
        $result = $this->ddlview->getWhere();
        $this->assertEquals('where', $result, 'assert failed, DDLView : "setWhere" expected "where" as value - the values should be equal');

        $this->ddlview->setWhere('');
        $result = $this->ddlview->getWhere();
        $this->assertNull($result, 'assert failed, DDLView : "setWhere" is expected null');
    }

    /**
     * check option
     *
     * @covers DDLView::getCheckOption
     * @covers DDLView::setCheckOption
     * @covers DDLView::hasCheckOption
     * @test
     */
    public function testCheckOption()
    {
        // DDL View
        $hasChecked = $this->ddlview->hasCheckOption();
        $this->assertFalse($hasChecked, 'assert failed, "DDLView" : false expected - no checkOption is set');

        $this->ddlview->setCheckOption(DDLViewConstraintEnumeration::CASCADED);
        $result = $this->ddlview->getCheckOption();
        $this->assertEquals(DDLViewConstraintEnumeration::CASCADED, $result, 'assert failed, DDLColumn : expected "1" as value - the values should be equal');

        $hasChecked = $this->ddlview->hasCheckOption();
        $this->assertTrue($hasChecked, 'assert failed, "DDLView" : true expected - checkOption is set ');

        $this->ddlview->setCheckOption(DDLViewConstraintEnumeration::LOCAL);
        $result = $this->ddlview->getCheckOption();
        $this->assertEquals(DDLViewConstraintEnumeration::LOCAL, $result, 'assert failed, DDLColumn : expected "2" as value - the values should be equal');

        $this->ddlview->setCheckOption(DDLViewConstraintEnumeration::NONE);
        $result = $this->ddlview->getCheckOption();
        $this->assertEquals(DDLViewConstraintEnumeration::NONE, $result, 'assert failed, DDLColumn : expected "0" as value - the values should be equal');

        $this->ddlview->setCheckOption(20);
        $result = $this->ddlview->getCheckOption();
        $this->assertEquals(0, $result, 'assert failed, DDLColumn : expected value is the default number 0 - only 0, 1, 2 numbers can be used in setCheckOption by setting an other number the default must be choosen');
    }

    /**
     * source-table
     *
     * @covers DDLINDEX::getSourceTable
     * @covers DDLForeignKey::getSourceTable
     *
     * @test
     */
    public function testGetSourceTable()
    {
        // DDL INDEX
        $ddlindex = new DDLIndex('index');
        $sourceTable = $ddlindex->getSourceTable();
        $this->assertNull($sourceTable, 'assert failed, the value expected null');

        // DDL ForeignKey
        $sourceTable = $this->ddlforeignkey->getSourceTable();
        $this->assertNull($sourceTable, 'assert failed, the value expected null');
    }

    /**
     * subject
     *
     * @covers DDLLogCreate::getSubject
     * @covers DDLLogCreate::setSubject
     *
     * @test
     */
    public function testSubject()
    {
        // DDL LogCreate
        $this->ddllogcreate->setSubject('column');
        $result = $this->ddllogcreate->getSubject();
        $this->assertEquals('column', $result, 'assert failed, DDLLogCreate : the expected result should be the value "column" - the values should be equal');
    }

    /**
     * SQL
     *
     * @covers DDLLogSql::getSQL
     * @covers DDLLogSql::setSQL
     * @covers DDLDatabaseInit::getSQL
     * @covers DDLDatabaseInit::setSQL
     *
     * @test
     */
    public function testSQL()
    {
        // DDL LogSql
        $this->ddllogsql->setSQL('sql');
        $result = $this->ddllogsql->getSQL();
        $this->assertEquals('sql', $result, 'assert failed, DDLLogSql : the expected result should be the value "sql" - the values should be equal');

        $this->ddllogsql->setSQL('');
        $result = $this->ddllogsql->getSQL();
        $this->assertNull($result, 'assert failed, DDLLogSql : the value is expected null');

        // DDL DatabaseInit
        $this->ddldatabaseinit->setSQL('sql');
        $result = $this->ddldatabaseinit->getSQL();
        $this->assertEquals('sql', $result, 'assert failed, DDLDatabaseInit : the expected result should be the value "sql" - the values should be equal');

        $this->ddldatabaseinit->setSQL('');
        $result = $this->ddldatabaseinit->getSQL();
        $this->assertNull($result, 'assert failed, DDLDatabaseInit : the value is expected null');
    }

    /**
     * readonly
     *
     * @covers DDLColumn::isReadonly
     * @covers DDLColumn::setReadonly
     * @covers DDLDatabase::isReadonly
     * @covers DDLDatabase::setReadonly
     * @covers DDLField::isReadonly
     * @covers DDLField::setReadonly
     * @covers DDLView::isReadonly
     * @covers DDLView::setReadonly
     * @covers DDLTable::isReadonly
     * @covers DDLTable::setReadonly
     *
     * @test
     */
    public function testReadonly()
    {
       //ddl column
       $this->ddlcolumn->setReadonly(true);
       $result = $this->ddlcolumn->isReadonly();
       $this->assertTrue($result, 'assert failed, DDLColumn : expected true - setReadonly was set with true');

       $this->ddlcolumn->setReadonly(false);
       $result = $this->ddlcolumn->isReadonly();
       $this->assertFalse($result, 'assert failed, DDLColumn : expected false - setReadonly was set with false');

       // ddl database
       $this->ddldatabase->setReadonly(true);
       $result = $this->ddldatabase->isReadonly();
       $this->assertTrue($result, 'assert failed, DDLDatabase : expected true - setReadonly was set with true');

       $this->ddldatabase->setReadonly(false);
       $result = $this->ddldatabase->isReadonly();
       $this->assertFalse($result, 'assert failed, DDLDatabase : expected false - setReadonly was set with false');

       // ddl field
       $this->ddlfield->setReadonly(true);
       $result = $this->ddlfield->isReadonly();
       $this->assertTrue($result, 'assert failed, DDLField : expected true - setReadonly was set with true');

       $this->ddlfield->setReadonly(false);
       $result = $this->ddlfield->isReadonly();
       $this->assertFalse($result, 'assert failed, DDLField : expected false - setReadonly was set with false');

       // DDL View
       $this->ddlview->setReadonly(true);
       $result = $this->ddlview->isReadonly();
       $this->assertTrue($result, 'assert failed, DDLView : expected true - setReadonly was set with true');

       $this->ddlview->setReadonly(false);
       $result = $this->ddlview->isReadonly();
       $this->assertFalse($result, 'assert failed, DDLView : expected false - setReadonly was set with false');

       // DDL Table
       $this->ddltable->setReadonly(true);
       $result = $this->ddltable->isReadonly();
       $this->assertTrue($result, 'assert failed, DDLTable : expected true - setReadonly was set with true');

       $this->ddltable->setReadonly(false);
       $result = $this->ddltable->isReadonly();
       $this->assertFalse($result, 'assert failed, DDLTable : expected false - setReadonly was set with false');
    }

    /**
     *  Visible
     *
     * @covers DDLField::isVisible
     * @covers DDLField::setVisible
     *
     * @test
     */
    public function testVisible()
    {
       // ddl field
       $this->ddlfield->setVisible(true);
       $result = $this->ddlfield->isVisible();
       $this->assertTrue($result, 'assert failed, DDLField : expected true - setVisible was set with true');

       $this->ddlfield->setVisible(false);
       $result = $this->ddlfield->isVisible();
       $this->assertFalse($result, 'assert failed, DDLField : expected false - setVisible was set with false');
    }

    /**
     * Clustered
     *
     * @covers DDLIndex::isClustered
     * @covers DDLIndex::setClustered
     *
     * @test
     */
    public function testClustered()
    {
       // DDL Index
       $this->ddlindex->setClustered(true);
       $result = $this->ddlindex->isClustered();
       $this->assertTrue($result, 'assert failed, DDLIndex : expected true - setClustered was set with true');

       $this->ddlindex->setClustered(false);
       $result = $this->ddlindex->isClustered();
       $this->assertFalse($result, 'assert failed, DDLIndex : expected false - setClustered was set with false');
    }

    /**
     * Cycle
     *
     * @covers DDLSequence::isCycle
     * @covers DDLSequence::setCycle
     *
     * @test
     */
    public function testCycle()
    {
       // DDL Sequence
       $this->ddlsequence->setCycle(true);
       $result = $this->ddlsequence->isCycle();
       $this->assertTrue($result, 'assert failed, DDLSequence : expected true - setCycle was set with true');

       $this->ddlsequence->setCycle(false);
       $result = $this->ddlsequence->isCycle();
       $this->assertFalse($result, 'assert failed, DDLSequence : expected false - setCycle was set with false');
    }

    /**
     * Deferrable
     *
     * @covers DDLForeignKey::isDeferrable
     * @covers DDLForeignKey::setDeferrable
     *
     * @test
     */
    public function testDeferrable()
    {
       // ddl field
       $this->ddlforeignkey->setDeferrable(true);
       $result = $this->ddlforeignkey->isDeferrable();
       $this->assertTrue($result, 'assert failed, DDLForeignKey : expected true - setDeferrable was set with true');

       $this->ddlforeignkey->setDeferrable(false);
       $result = $this->ddlforeignkey->isDeferrable();
       $this->assertFalse($result, 'assert failed, DDLForeignKey : expected false - setDeferrable was set with false');
    }

    /**
     * nullable
     *
     * @covers DDLColumn::isNullable
     * @covers DDLColumn::setNullable
     *
     * @test
     */
    public function testNullable()
    {
       // expected value is true
       $this->ddlcolumn->setNullable(true);
       $result = $this->ddlcolumn->isNullable();
       $this->assertTrue($result, 'assert failed, DDLColumn : expected true - setNullable was set with true');

       // expected value is false
       $this->ddlcolumn->setNullable(false);
       $result = $this->ddlcolumn->isNullable();
       $this->assertFalse($result, 'assert failed, DDLColumn : expected false - setNullable was set with false');
    }

    /**
     * unique
     *
     * @covers DDLColumn::isUnique
     * @covers DDLColumn::setUnique
     * @covers DDLIndex::isUnique
     * @covers DDLIndex::setUnique
     *
     * @test
     */
    public function testUnique()
    {
       // DDL Column
       $this->ddlcolumn->setUnique(true);
       $result = $this->ddlcolumn->isUnique();
       $this->assertTrue($result, 'assert failed, DDLColumn : expected true - setUnique was set with true');

       $this->ddlcolumn->setUnique(false);
       $result = $this->ddlcolumn->isUnique();
       $this->assertFalse($result, 'assert failed, DDLColumn : expected false - setUnique was set with false');

       // DDL Index
       $this->ddlindex->setUnique(true);
       $result = $this->ddlindex->isUnique();
       $this->assertTrue($result, 'assert failed, DDLIndex : expected true - setUnique was set with true');

       $this->ddlindex->setUnique(false);
       $result = $this->ddlindex->isUnique();
       $this->assertFalse($result, 'assert failed, DDLIndex : expected false - setUnique was set with false');
    }

    /**
     * unsigned
     *
     * @covers DDLColumn::isUnsigned
     * @covers DDLColumn::setUnsigned
     *
     * @test
     */
    public function testUnsigned()
    {
       // expected value is true
       $this->ddlcolumn->setType('integer');
       $this->ddlcolumn->setUnsigned(true);
       $result = $this->ddlcolumn->isUnsigned();
       $this->assertTrue($result, 'assert failed, DDLColumn : expected true - setUnsigned was set with true');

       // expected value is false
       $this->ddlcolumn->setUnsigned(false);
       $result = $this->ddlcolumn->isUnsigned();
       $this->assertFalse($result, 'assert failed, DDLColumn : expected false - setUnsigned was set with false');
    }

    /**
     * fixed
     *
     * @covers DDLColumn::isFixed
     * @covers DDLColumn::setFixed
     *
     * @test
     */
    public function testFixed()
    {
       // expected value is true
       $this->ddlcolumn->setFixed(true);
       $result = $this->ddlcolumn->isFixed();
       $this->assertTrue($result, 'assert failed, DDLColumn : expected true - setFixed was set with true');

       // expected value is false
       $this->ddlcolumn->setFixed(false);
       $result = $this->ddlcolumn->isFixed();
       $this->assertFalse($result, 'assert failed, DDLColumn : expected false - setFixed was set with false');
    }

    /**
     * Auto-increment
     *
     * @covers DDLColumn::isAutoIncrement
     * @covers DDLColumn::setAutoIncrement
     *
     * @test
     */
    public function testAutoIncrement()
    {
       // expected value is true
       $this->ddlcolumn->setType('integer');
       $this->ddlcolumn->setAutoIncrement(true);
       $result = $this->ddlcolumn->isAutoIncrement();
       $this->assertTrue($result, 'assert failed, DDLColumn : expected true - setAutoIncrement was set with true');

       // expected value is false
       $this->ddlcolumn->setAutoIncrement(false);
       $result = $this->ddlcolumn->isAutoIncrement();
       $this->assertFalse($result, 'assert failed, DDLColumn : expected false - setAutoIncrement was set with false');
    }

    /**
     * Auto-fill
     *
     * @covers DDLColumn::isAutoFill
     * @covers DDLColumn::setAutoFill
     *
     * @test
     */
    public function testSetAutoFill()
    {
        // expected value is true
        $this->ddlcolumn->setType('integer');
        $this->ddlcolumn->setAutoFill(true);
        $result = $this->ddlcolumn->isAutoFill();
        $this->assertTrue($result, 'assert failed, DDLColumn : expected true - setAutoFill was set with true');

        // expected value is true
        $this->ddlcolumn->setType('inet');
        $this->ddlcolumn->setDefault('REMOTE_ADDR');
        $this->ddlcolumn->setAutoFill(true);
        $result = $this->ddlcolumn->isAutoFill();
        $this->assertTrue($result, 'assert failed, DDLColumn : expected true - setAutoFill was set with true');

        // expected value is true
        $this->ddlcolumn->setType('time');
        $this->ddlcolumn->setDefault('CURRENT_TIMESTAMP');
        $this->ddlcolumn->setAutoFill(true);
        $result = $this->ddlcolumn->isAutoFill();
        $this->assertTrue($result, 'assert failed, DDLColumn : expected true - setAutoFill was set with true');

        // expected value is true
        $this->ddlcolumn->setAutoFill(true);
        $this->ddlcolumn->setDefault('Fake');
        $this->ddlcolumn->setType('image');
        $result = $this->ddlcolumn->isAutoFill();
        $this->assertFalse($result, 'assert failed, DDLColumn : expected false - setAutoFill was set with true');

        // expect an Exception, image has no autofill
        try {
            $this->ddlcolumn->setAutoFill(false);
            $this->fail("DDLColumn::setAutoFill for an image-column should raise an exception");
        } catch (\Exception $e) {
            //success
        }
    }

    /**
     * Auto-fill with invalid argument
     *
     * @covers DDLColumn::setAutoFill
     * @expectedException NotImplementedException
     *
     * @test
     */
    public function testSetAutoFillInvalidArgumentException()
    {
        // DDLColumn
        $this->ddlcolumn->setType('string');
        $this->ddlcolumn->setAutoFill(true);
    }

    /**
     * is foreign-key
     *
     * @covers DDLColumn::isForeignKey
     *
     * @test
     */
    public function testIsForeignKey()
    {
        // DDL Column
        $valid = $this->ddlcolumn->isForeignKey();
        $this->assertFalse($valid, 'assert false, "DDLColumn" : expected "false" - no foreign key found');

        // create a target-table
        $newTableA = $this->ddldatabase->addTable("someTable");
        $newTableB = $this->ddldatabase->addTable("otherTable");
        $ColumnA = $newTableA->addColumn("firstCol", "integer");
        $ColumnB = $newTableB->addColumn("someCol", "integer");
        $ColumnC = $newTableB->addColumn("someMoreCol", "integer");
        $newTableA->setPrimaryKey("firstCol");
        $foreign = $newTableB->addForeignKey("someTable");
        $foreign->setColumn("someCol");
        $valid = $ColumnB->isForeignKey();
        $this->assertTrue($valid, 'DDLColumn::isForeignKey - key expected ');
        $valid = $ColumnC->isForeignKey();
        $this->assertFalse($valid, 'DDLColumn::isForeignKey - key expected ');

    }

    /**
     * isPrimaryKey
     *
     * @covers DDLColumn::isPrimaryKey
     *
     * @test
     */
    public function testIsPrimaryKey()
    {
        // DDL Column
        $valid = $this->ddlcolumn->isPrimaryKey();
        $this->assertFalse($valid, 'assert false, "DDLColumn" : expected "false" - no primary key found');
    }

    /**
     * is number
     *
     * @covers DDLColumn::isNumber
     *
     * @test
     */
    public function testIsNumber()
    {
        // DDL Column
        $this->ddlcolumn->setType('string');
        $valid = $this->ddlcolumn->isNumber();
        $this->assertFalse($valid, 'expecting column of type string not to be a number');
        // DDL Column
        $this->ddlcolumn->setType('integer');
        $valid = $this->ddlcolumn->isNumber();
        $this->assertTrue($valid, 'expecting column of type int to be a number');
    }

    /**
     * Length
     *
     * @covers DDLIndexColumn::getLength
     * @covers DDLIndexColumn::setLength
     * @test
     */
    public function testGetLength()
    {
        // DDLIndexColumn
        $this->ddlindexcolumn->setLength(20);
        $length = $this->ddlindexcolumn->getLength();
        $this->assertEquals(20, $length, 'assert failed, DDLIndexColumn: set und get do not match');

        $this->ddlindexcolumn->setLength(0);
        $length = $this->ddlindexcolumn->getLength();
        $this->assertNull($length, 'DDLIndexColumn:setLength should return Null, if not set');
    }

    /**
     * Size and precision
     *
     * @covers DDLColumn:getLength
     * @covers DDLColumn:setLength
     * @covers DDLColumn:getSize
     * @covers DDLColumn:getPrecision
     * @covers DDLColumn:setSize
     *
     * @test
     */
    public function testSetSize()
    {
        // DDL Column
        $this->ddlcolumn->setSize(5);
        $precision = $this->ddlcolumn->getPrecision();
        $this->assertNull($precision, 'assert faield, "DDLColumn" : expected "-1"');
        $get = $this->ddlcolumn->getSize();
        $this->assertEquals(5, $get, 'assert faield, "DDLColumn" : expected "5"');

        $this->ddlcolumn->setLength(10, 2);
        $get = $this->ddlcolumn->getLength();
        $this->assertEquals(10, $get, 'assert faield, "DDLColumn" : expected "10"');
        $precision = $this->ddlcolumn->getPrecision();
        $this->assertEquals(2, $precision, 'assert faield, "DDLColumn" : expected "2"');
    }

    /**
     * Size and precision with invalid argument
     *
     * @covers DDLColumn:setLength
     * @expectedException InvalidArgumentException
     *
     * @test
     */
    public function testSetSizeInvalidArgumentException()
    {
        // DDLColumn
        $this->ddlcolumn->setLength(1, 2);
    }

    /**
     * Image-settings
     *
     * @covers DDLColumn::getImageSettings
     * @covers DDLColumn::setImageSettings
     *
     * @test
     */
    public function testImageSettings()
    {
        // DDL Column
        $width = 30;
        $height = 15;
        $ratio = true;
        $background = 'description';
        $expected = array('width' => $width, 'height' => $height, 'ratio' => $ratio, 'background' => $background);
        $this->ddlcolumn->setImageSettings($width, $height, $ratio, $background);
        $get = $this->ddlcolumn->getImageSettings();

        $expected = array('width' => $width, 'height' => $height, 'ratio' => $ratio, 'background' => $background);
        $this->assertType('array', $get, 'assert failed, DDLColumn : the value is not from type Array');
        $this->assertEquals($expected, $get, 'assert failed, DDLColumn : the image settings are not set');

        $expected = array('width' => '', 'height' => '', 'ratio' => '', 'background' => '');
        $this->ddlcolumn->setImageSettings();
        $get = $this->ddlcolumn->getImageSettings();
        $this->assertType('array', $get, 'assert failed, DDLColumn : the value is not from type Array');
        $this->assertEquals($expected, $get, 'assert failed, DDLColumn : the image settings are not set');
    }

    /**
     * Reference-settings
     *
     * @covers DDLColumn::getReferenceSettings
     * @covers DDLColumn::setReferenceSettings
     *
     * @test
     */
    public function testSetReferenceSettings()
    {
        // DDL Column
        $table = 'sometable';
        $column = 'somecolumn';
        $label = 'somelabel';

        $expected = array('table' => $table, 'column' => $column, 'label' => $label);
        $this->ddlcolumn->setReferenceSettings($table, $column, $label);
        $get = $this->ddlcolumn->getReferenceSettings();
        $this->assertType('array', $get, 'assert failed, DDLColumn : the value is not from type Array');
        $this->assertEquals($expected, $get, 'assert failed, DDLColumn : the reference settings are not set');

        $expected = array('table' => '', 'column' => '', 'label' => '');
        $this->ddlcolumn->setReferenceSettings();
        $get = $this->ddlcolumn->getReferenceSettings();
        $this->assertType('array', $get, 'assert failed, DDLColumn : the value is not from type Array');
        $this->assertEquals(3, count($get), 'assert failed, DDLColumn : the reference settings are not set');
        $this->assertEquals($expected, $get, 'assert failed, DDLColumn : the reference settings are not set');
    }

    /**
     * Default
     *
     * @covers DDLColumn::getDefaults
     * @covers DDLColumn::getDefault
     * @covers DDLColumn::setDefault
     *
     * @test
     */
    public function testSetDefault()
    {
        // DDLColumn
        $this->ddlcolumn->setDefault('a');
        $this->ddlcolumn->setDefault('b', 'mysql');
        $getAll = $this->ddlcolumn->getDefaults();
        $this->assertType('array', $getAll, 'assert failed, DDLColumn : the value is not from type array');
        $this->assertEquals(2, count($getAll), 'assert failed, DDLColumn :the values should be equal - expected number 2');
        $get = $this->ddlcolumn->getDefault('mysql');
        $this->assertType('string', $get, 'assert failed, DDLColumn : the value is not from type string');
        $this->assertEquals('b', $get, 'assert failed, DDLColumn : the variables should be equal - expected key of value "mysql"');

        $get = $this->ddlcolumn->getDefault('oracle');
        $this->assertEquals('a', $get, 'Function getDefault() must fall back to "generic" if setting is not found.');
        $this->ddlcolumn->setDefault('');
        $get = $this->ddlcolumn->getDefault('oracle');
        $this->assertEquals(0, strlen($get), 'assert failed, DDLColumn : the values should be equal - 0 expected when value does not exist in array');
    }

    /**
     * Enumeration-items
     *
     * @covers DDLColumn::getEnumerationItems
     * @covers DDLColumn::setEnumerationItem
     * @covers DDLColumn::setEnumerationItems
     * @covers DDLColumn::dropEnumerationItems
     * @covers DDLColumn::dropEnumerationItem
     * @covers DDLColumn::getEnumerationItem
     * @covers DDLColumn::getEnumerationItemNames
     *
     * @test
     */
    public function testEnumerationItems()
    {
        // DDL Column
        $array = array('aa' => '20', 'bb' => '30', 'cc' => '50');
        $this->ddlcolumn->setEnumerationItems($array);
        $get = $this->ddlcolumn->getEnumerationItems();
        $this->assertEquals($array, $get, 'assert failed, DDLColumn : the values should be equal - expected the same array which is set');

        $this->ddlcolumn->setEnumerationItem('cc', '90');
        $get = $this->ddlcolumn->getEnumerationItems();
        $this->assertNotEquals($array, $get, 'assert failed, DDLColumn : the values should not be equal, the key "cc" was manipulate with other value');

        $validate = array('aa' => '20', 'bb' => '30', 'cc' => '90');
        $this->assertEquals($validate, $get, 'assert failed, DDLColumn : the values should be equal, expected the same array which is set with the manipulated value');

        $getItemNames = $this->ddlcolumn->getEnumerationItemNames();
        $valid = array('aa', 'bb', 'cc');
        $this->assertEquals($valid, $getItemNames, 'assert failed, the values should be equal, expected the keys from array');

        $this->ddlcolumn->dropEnumerationItem('bb');
        $get = $this->ddlcolumn->getEnumerationItems();
        $this->assertNotEquals($validate, $get, 'assert failed, DDLColumn : the values should not be equal, the key "bb" is dropt');
        $get = $this->ddlcolumn->getEnumerationItem('bb');
        $this->assertNull($get, 'assert failed, DDLColumn : the enumeration item should not be exist, key "bb" was dropt before');

        $get = $this->ddlcolumn->getEnumerationItem('cc');
        $this->assertEquals(90, (int) $get, 'assert failed, DDLColumn : the enumeration item should be match the expected value');

        $this->ddlcolumn->dropEnumerationItems();
        $get = $this->ddlcolumn->getEnumerationItems();
        $this->assertType('array', $get, 'assert failed, DDLColumn : the value should be from type array');
        $this->assertEquals(0, count($get), 'assert failed, DDLColumn : the values should be equal, all entries are removed before');
    }

    /**
     * drop non-existing enumeration item
     *
     * @covers DDLColumn::dropEnumerationItem
     * @expectedException NotFoundException
     *
     * @test
     */
    public function testDropEnumerationItemNotFoundException()
    {
        // DDLColumn
        $array = array('1' => '2');
        $this->ddlcolumn->setEnumerationItems($array);
        $this->ddlcolumn->dropEnumerationItem('no_item');
    }

    /**
     * getConstraint
     *
     * @covers DDLColumn::getConstraint
     * @covers DDLColumn::getConstraints
     * @covers DDLColumn::addConstraint
     * @covers DDLTable::getConstraint
     * @covers DDLTable::getConstraints
     * @covers DDLTable::addConstraint
     * @covers DDLTable::dropConstraint
     * @covers DDLTable::getUniqueConstraints
     * @covers DDLConstraint::getConstraints
     * @covers DDLConstraint::setConstraint
     *
     * @test
     */
    public function testConstraint()
    {
        // DDLColumn::addConstraint parameter constraint
        $result = true;
        try {
            $this->ddlcolumn->addConstraint(4711, "someName", "mysql");
        } catch (\Exception $e) {
            $result = false;
        }
        $this->assertFalse($result, "DDLColumn::addConstraint should not accept an Integer as Constraint");

        // DDLColumn::addConstraint parameter name
        $result = true;
        try {
            $this->ddlcolumn->addConstraint("someConstraints", 4711, "mysql");
        } catch (\Exception $e) {
            $result = false;
        }
        $this->assertFalse($result, "DDLColumn::addConstraint should not accept an Integer as Name");

        // DDLColumn::getConstraint parameter name
        $result = true;
        try {
            $this->ddlcolumn->addConstraint(4711, "mysql");
        } catch (\Exception $e) {
            $result = false;
        }
        $this->assertFalse($result, "DDLColumn::getConstraint should not accept an Integer as Name");

        // DDLColumn::getConstraint default
        $constraint1 = new DDLConstraint();
        $constraint1->setConstraint("1");
        $constraint2 = new DDLConstraint();
        $constraint2->setConstraint("2");
        $constraint3 = new DDLConstraint();
        $constraint3->setConstraint("3");
        $testArray1 = array($constraint1, $constraint2, $constraint3);
        $this->ddlcolumn->addConstraint("1");
        $this->ddlcolumn->addConstraint("2");
        $this->ddlcolumn->addConstraint("3");
        $result1 = $this->ddlcolumn->getConstraints();
        $this->assertEquals($result1, $testArray1, 'DDLColumn::getConstraints failed');

        $this->ddlcolumn->dropConstraints();
        $result1 = $this->ddlcolumn->getConstraints();
        $this->assertEquals(count($result1), 0, 'DDLColumn::dropConstraints failed');

        $constraint1->setDBMS("mysql");
        $constraint2->setDBMS("mysql");
        $constraint3->setDBMS("mysql");
        $this->ddlcolumn->addConstraint("1", "", "mysql");
        $this->ddlcolumn->addConstraint("2", "", "mysql");
        $this->ddlcolumn->addConstraint("3", "", "mysql");
        $result1 = $this->ddlcolumn->getConstraints("mysql");
        $this->assertEquals($result1, $testArray1, 'DDLColumn::getConstraints failed');

        $result1 = $this->ddlcolumn->getConstraint("name2", "mysql");
        $this->assertNull($result1, 'DDLColumn::getConstraints failed');

        $get = $this->ddlcolumn->getConstraints('odbc');
        $this->assertType('array', $get, 'assert failed, the value should be from type array');

        // DDL Table
        $testArray1 = array("someConstraints 1", "someConstraints 2", "someConstraints 3");
        $this->ddltable->addConstraint($testArray1[0]);
        $this->ddltable->addConstraint($testArray1[1]);
        $this->ddltable->addConstraint($testArray1[2]);
        $result1 = $this->ddltable->getConstraints();

        $this->assertEquals($result1[0]->getConstraint(), $testArray1[0], 'DDLTable::getConstraints failed, both arrays should be equal');

        $testArray2 = array("someMoreConstraints 1", "someMoreConstraints 2", "someMoreConstraints 3");
        $this->ddltable->addConstraint($testArray2[0], "", "mysql");
        $this->ddltable->addConstraint($testArray2[1], "", "mysql");
        $this->ddltable->addConstraint($testArray2[2], "", "mysql");
        $result1 = $this->ddltable->getConstraints("mysql");
        $this->assertEquals($result1[1]->getConstraint(), $testArray2[1], 'DDLTable::getConstraints failed, both arrays should be equal');
        //$this->ddltable->dropConstraints();
        $this->ddltable->addConstraint("someDifferentConstraints 1", "name", "mysql");
        $this->ddltable->addConstraint("someDifferentConstraints 2", "name", "mysql");
        $result1 = $this->ddltable->getConstraint("name", "mysql");
        $this->assertEquals($result1->getConstraint(), "someDifferentConstraints 1", 'DDLTable::getConstraints failed');

        $result1 = $this->ddltable->getConstraint("name2", "mysql");
        $this->assertNull($result1, 'DDLTable::getConstraints failed');

        $get = $this->ddltable->getConstraints("oracle");
        $this->assertEquals(array(), $get, 'DDLTable::getConstraints - "oracle" doesnt exist in array');

        $this->ddltable->dropConstraints();
        $get = $this->ddltable->getConstraints();
        $this->assertEquals(array(), $get, 'DDLTable::getConstraints list should be empty after droping constraints.');

        // get Unique-Constraints
        $result1 = $this->ddltable->getUniqueConstraints();
        $uniqueCol = $this->ddltable->addColumn('unique','integer');
        $uniqueCol->setUnique();
        $result2 = $this->ddltable->getUniqueConstraints();
        $this->assertType('array',$result1, 'DDLTable::');
        $this->assertType('array',$result2, 'DDLTable::');
        $this->assertTrue(empty($result1), 'DDLTable::');
        $this->assertFalse(empty($result2), 'DDLTable::');

        // DDLConstraint
        $testArray1 = array("someConstraints 1", "someConstraints 2", "someConstraints 3");
        $this->ddlconstraint->setConstraint($testArray1[0]);
        $result1 = $this->ddlconstraint->getConstraint();
        $this->assertEquals($testArray1[0], $result1, 'DDLConstraint::getConstraint failed');

        $this->ddlconstraint->setConstraint();
        $result1 = $this->ddlconstraint->getConstraint();
        $this->assertNull($result1, 'DDLConstraint::getConstrain failed');
    }

    /**
     * Includes
     *
     * @covers DDLDatabase::getIncludes
     * @covers DDLDatabase::setIncludes
     * @covers DDLDatabase::addIncludes
     *
     * @test
     */
    public function testIncludes()
    {
        $array = array('first');
        // ddl database
        $this->ddldatabase->setIncludes($array);
        $result = $this->ddldatabase->getIncludes();
        $this->assertEquals($array, $result, 'assert failed, DDLDatabase : expected an array with one entire "first", values should be equal');
        $next = 'second';
        $add = $this->ddldatabase->addInclude($next);
        $result = $this->ddldatabase->getIncludes();
        $this->assertEquals('second', $result[1], 'assert failed, DDLDatabase : the value "second" should be match a value in array, values should be equal');
    }

    /**
     * Charset
     *
     * @covers DDLDatabase::getCharset
     * @covers DDLDatabase::setCharset
     *
     * @test
     */
    public function testCharset()
    {
        // ddl database
        $this->ddldatabase->setCharset('charset');
        $result = $this->ddldatabase->getCharset();
        $this->assertEquals('charset', $result, 'assert failed, DDLDatabase : expected "charset" as value');

        $this->ddldatabase->setCharset();
        $result = $this->ddldatabase->getCharset();
        $this->assertNull($result, 'assert failed, DDLDatabase : expected null, the charset is empty');
    }

    /**
     * DataSource
     *
     * @covers DDLDatabase::getDataSource
     * @covers DDLDatabase::setDataSource
     *
     * @test
     */
    public function testDataSource()
    {
        // ddl database
        $this->ddldatabase->setDataSource('dataSource');
        $result = $this->ddldatabase->getDataSource();
        $this->assertEquals('dataSource', $result, 'assert failed, DDLDatabase : expected "dataSource" as value');

        $this->ddldatabase->setDataSource();
        $result = $this->ddldatabase->getDataSource();
        $this->assertNull($result, 'assert failed, DDLDatabase : expected null, the DataSource is empty');
    }

    /**
     * Table
     *
     * @covers DDLForm::getTable
     * @covers DDLForm::setTable
     * @covers DDLViewField::setTable
     * @covers DDLViewField::getTable
     * @covers DDLDatabase::getTable
     * @covers DDLDatabase::addTable
     * @covers DDLDatabase::getTableNames
     * @covers DDLDatabase::getTables
     * @covers DDLDatabase::getTable
     * @covers DDLDatabase::isTable
     * @covers DDLDatabase::dropTable
     *
     * @test
     */
    public function testSetTable()
    {
        // DDL Form
        $this->ddlform->setTable('abcd');
        $result = $this->ddlform->getTable();
        $this->assertEquals('abcd', $result, 'assert failed, DDLForm : expected "abcd" as value');

        $this->ddlform->setTable('');
        $result = $this->ddlform->getTable();
        $this->assertNull($result, 'assert failed, DDLForm : expected null, non table is set');

        // DDLViewField
        $this->ddlviewfield->setTable('abcd');
        $result = $this->ddlviewfield->getTable();
        $this->assertEquals('abcd', $result, 'assert failed, DDLViewField : expected "abcd" as value');

        $this->ddlviewfield->setTable('');
        $result = $this->ddlviewfield->getTable();
        $this->assertNull($result, 'assert failed, DDLViewField : expected null, non table is set');

        // DDLDatabase
        $valid = $this->ddldatabase->isTable('newtable');
        $this->assertFalse($valid, 'assert failed, expected false, the value "newtable" is not a table');

        $add = $this->ddldatabase->addTable('newtable');
        $this->assertTrue($add instanceof DDLTable, 'assert failed, the value should be an instanceof DDLTable');
        $getAll = $this->ddldatabase->getTables();
        $this->assertArrayHasKey('newtable', $getAll, 'assert failed, the value should be match a key in array');
        $result = $this->ddldatabase->getTable('newtable');
        $this->assertType('object', $result, 'assert failed, the value should be from type object');

        $valid = $this->ddldatabase->isTable('newtable');
        $this->assertTrue($valid, 'assert failed, expected true, the value "newtable" is a Table');

        $newTable = $this->ddldatabase->addTable("someTable");
        $retTable = $this->ddldatabase->getTable("someTable");
        $this->assertNotNull($retTable, 'getTable : expected null, non table is set');
        $retTable = $this->ddldatabase->getTable("otherTable");
        $this->assertNull($retTable, 'getTable : expected null, non table is set');

        $tables = $this->ddldatabase->getTableNames();
        $this->assertContains('newtable', $tables, 'assert failed, the value should be match a key in array');
        $this->assertContains('sometable', $tables, 'assert failed, the value should be match a key in array');

        // null expected
        $drop = $this->ddldatabase->dropTable('newtable');
        $get = $this->ddldatabase->getTable('newtable');
        $this->assertNull($get, 'assert failed, expected null - table was dropt before');
    }


    /**
     * Magic Getter
     *
     * @covers DDLDatabase::__get
     * @covers DDLTable::__get
     * @covers DDLForm::__get
     * @covers DDLView::__get
     *
     * @test
     */
    public function testMagicGet()
    {
        // magic Database
        $this->ddldatabase->addTable('myTable');
        $result = $this->ddldatabase->myTable;
        $this->assertTrue($result instanceof DDLTable, 'assert failed, expected null - table was dropt before');

        // magic Table
        $this->ddldatabase->myTable->addColumn('magic', 'integer');
        $result = $this->ddldatabase->myTable->magic;
        $this->assertTrue($result instanceof DDLColumn, 'assert failed, expected null - column was dropt before');

        // magic Form
        $this->ddldatabase->addForm('magicForm');
        $result = $this->ddldatabase->magicForm;
        $this->assertTrue($result instanceof DDLForm, 'assert failed, expected null - form was dropt before');

        // magic Field
        $this->ddlform->addField('magicField');
        $result = $this->ddlform->magicField;
        $this->assertTrue($result instanceof DDLField, 'assert failed, expected null - field was dropt before');

        // magic View
        $this->ddlview->addField('magicViewfield');
        $result = $this->ddlview->magicViewfield;
        $this->assertTrue($result instanceof DDLViewField, 'assert failed, expected null - view field was dropt before');

        // magic View
        $this->ddldatabase->addView('magicView');
        $result = $this->ddldatabase->magicView;
        $this->assertTrue($result instanceof DDLView, 'assert failed, expected null - view was dropt before');

    }

    /**
     * add already existing table
     *
     * @covers DDLDatabase::addTable
     * @expectedException AlreadyExistsException
     *
     * @test
     */
    public function testAddTableAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->ddldatabase->addTable('table');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->ddldatabase->addTable('table');
    }

    /**
     * drop non-existing table
     *
     * @covers DDLDatabase::dropTable
     * @expectedException NotFoundException
     *
     * @test
     */
    public function testDropTableNotFoundException()
    {
        // DDLDatabase
        $this->ddldatabase->dropTable('no_table');
    }

    /**
     * Tables
     *
     * @covers DDLView::getTables
     * @covers DDLView::setTables
     *
     * @test
     */
    public function testTables()
    {
        // DDL View
        $array = array('one', 'two');
        $this->ddlview->setTables($array);
        $get = $this->ddlview->getTables($array);
        $this->assertEquals($array, $get, 'assert failed, "DDLView" : expected the same table as which was set with setTable, values should be equal');
    }

    /**
     * TablesInvalidArgumentException
     *
     * @covers DDLView::setTables
     * @expectedException InvalidArgumentException
     *
     * @test
     */
    public function testSetTablesInvalidArgumentException()
    {
        // DDL View
        $this->ddlview->setTables(array());
    }

    /**
     * View
     *
     * @covers DDLDatabase::getView
     * @covers DDLDatabase::addView
     * @covers DDLDatabase::getViews
     * @covers DDLDatabase::getViewNames
     * @covers DDLDatabase::isView
     * @covers DDLDatabase::dropView
     *
     * @test
     */
    public function testView()
    {
        // DDL Database
        $valid = $this->ddldatabase->isView('qwerty');
        $this->assertFalse($valid, 'assert failed, the value should be false, "qwerty" is not a view');

        $add = $this->ddldatabase->addView('qwerty');
        $this->assertType('object', $add, 'assert failed, the value should be from type object');
        $this->assertEquals('qwerty', $add->getName(), 'assert failed, the values should be equal, "qwerty" is a view');

        $add = $this->ddldatabase->addView('trewq');
        $this->assertType('object', $add, 'assert failed, the value should be from type object');
        $this->assertEquals('trewq', $add->getName(), 'assert failed, the values should be equal, "trewq" is a view');

        $get = $this->ddldatabase->getView('qwerty');
        $this->assertType('object', $get, 'assert failed, the value should be from type object');
        $this->assertEquals('qwerty', $get->getName(), 'assert failed, the values should be equal, the name of the view should be the same as expected');

        $getAll = $this->ddldatabase->getViews();
        $this->assertType('array', $getAll, 'assert failed, the value should be from type array');
        $this->assertArrayHasKey('qwerty', $getAll, 'assert failed, the value should be match a entry in array');
        $this->assertArrayHasKey('trewq', $getAll, 'assert failed, the value should be match a entry in array');

        $getNames = $this->ddldatabase->getViewNames();
        $this->assertType('array', $getNames, 'assert failed, the values should be from type array');
        $this->assertTrue(in_array('qwerty', $getNames), 'assert failed, the value should be match a entry in array');
        $this->assertTrue(in_array('trewq', $getNames), 'assert failed, the value should be match a entry in array');

        $valid = $this->ddldatabase->isView('qwerty');
        $this->assertTrue($valid, 'assert failed, the value should be true');

        $drop = $this->ddldatabase->dropView('qwerty');
        $nonexist = $this->ddldatabase->getView('qwerty');
        $this->assertNull($nonexist, 'assert failed, the value should be null, the view was dropt before');
    }

    /**
     * addViewAlreadyExistsException
     *
     * @covers DDLDatabase::addView
     * @expectedException AlreadyExistsException
     *
     * @test
     */
    public function testAddViewAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->ddldatabase->addView('view');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->ddldatabase->addView('view');
    }

    /**
     * drop non-existing view
     *
     * @covers DDLDatabase::dropView
     * @expectedException NotFoundException
     *
     * @test
     */
    public function testDropViewNotFoundException()
    {
        // DDLDatabase
        $this->ddldatabase->dropView('a');
    }

    /**
     * Function
     *
     * @covers DDLDatabase::getFunction
     * @covers DDLDatabase::addFunction
     * @covers DDLDatabase::getFunctions
     * @covers DDLDatabase::getFunctionNames
     * @covers DDLDatabase::isFunction
     * @covers DDLDatabase::dropFunction
     *
     * @test
     */
    public function testSetFunction()
    {
        // DDL Database
        $valid = $this->ddldatabase->isFunction('qwerty');
        $this->assertFalse($valid, 'assert failed, "DDLDatabase" the value should be false, "qwerty" is not a function');

        $add = $this->ddldatabase->addFunction('qwerty');
        $this->assertType('object', $add, 'assert failed, "DDLDatabase" the value should be from type object');
        $this->assertEquals('qwerty', $add->getName(), 'assert failed, "DDLDatabase" the values should be equal, "qwerty" is a function');

        $add = $this->ddldatabase->addFunction('trewq');
        $this->assertType('object', $add, 'assert failed, "DDLDatabase" the value should be from type object');
        $this->assertEquals('trewq', $add->getName(), 'assert failed, "DDLDatabase" the values should be equal, the name of the view should be the same as expected');

        $get = $this->ddldatabase->getFunction('qwerty');
        $this->assertType('object', $get, 'assert failed, "DDLDatabase" the value should be from type object');
        $this->assertEquals('qwerty', $get->getName(), 'assert failed, "DDLDatabase" the values should be equal, the name of the view should be the same as expected');

        $getAll = $this->ddldatabase->getFunctions();
        $this->assertType('array', $getAll, 'assert failed, "DDLDatabase" the value should be from type array');
        $this->assertArrayHasKey('qwerty', $getAll, 'assert failed, "DDLDatabase" the value should be match a entry in array');
        $this->assertArrayHasKey('trewq', $getAll, 'assert failed, "DDLDatabase" the value should be match a entry in array');

        $getNames = $this->ddldatabase->getFunctionNames();
        $this->assertType('array', $getNames, 'assert failed, "DDLDatabase" the values should be from type array');
        $this->assertTrue(in_array('qwerty', $getNames), 'assert failed, "DDLDatabase" the value should be match a entry in array');
        $this->assertTrue(in_array('trewq', $getNames), 'assert failed, "DDLDatabase" the value should be match a entry in array');

        $valid = $this->ddldatabase->isFunction('qwerty');
        $this->assertTrue($valid, 'assert failed, "DDLDatabase" the value should be true, "qwerty" is a function');

        $drop = $this->ddldatabase->dropFunction('qwerty');
        $nonexist = $this->ddldatabase->getFunction('qwerty');
        $this->assertNull($nonexist, 'assert failed, "DDLDatabase" the value should be null, "qwerty" was dropt before');
    }

    /**
     * addFunctionAlreadyExistsException
     *
     * @covers DDLDatabase::addFunction
     * @expectedException AlreadyExistsException
     *
     * @test
     */
    public function testAddFunctionAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->ddldatabase->addFunction('function');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->ddldatabase->addFunction('function');
    }

    /**
     * drop non-existing function
     *
     * @covers DDLDatabase::dropFunction
     * @expectedException NotFoundException
     *
     * @test
     */
    public function testDropFunctionNotFoundException()
    {
        // DDLDatabase
        $this->ddldatabase->dropFunction('gert');
    }

    /**
     * Query
     *
     * @covers DDLView::getQuery
     * @covers DDLView::setQuery
     * @covers DDLView::getQueries
     *
     * @test
     */
    public function testQuery()
    {
       // DDLView
       $set = $this->ddlview->setQuery('');
       $this->assertType('array', $set, 'assert failed, the value is not from type array');
       $this->assertEquals(0, count($set), 'assert failed, expected an array with 0 entries , no query is set');

       $get = $this->ddlview->getQueries();
       $this->assertType('array', $get, 'assert failed, the value is not from type array');
       $this->assertEquals(0, count($get), 'assert failed, expected an array with 0 entries , no query is set');

       $get = $this->ddlview->getQuery('mysql');
       $this->assertNull($get, 'assert failed, expected null , the key doesnt exist in array');

       $set = $this->ddlview->setQuery('query', 'mysql');
       $this->assertArrayHasKey('mysql', $set, 'assert failed, "DDLView" : the key "mysql" should be match the array key');
       $set = $this->ddlview->setQuery('query', 'generic');
       $get = $this->ddlview->getQuery('mysql');
       $this->assertEquals('query', $get, 'assert failed, "DDLView" : the values should be equal');

       $get = $this->ddlview->getQueries();
       $this->assertArrayHasKey('mysql', $get, 'assert failed, "DDLView" : the key "mysql" should be match the array key');
       $this->assertArrayHasKey('generic', $get, 'assert failed, "DDLView" : the key "generic" should be match the array key');
    }

    /**
     * addEntry
     *
     * @covers DDLChangeLog::addEntry
     * @test
     */
    public function testAddEntry()
    {
        for ($i = 1; $i <10; $i++)
        {
            $nr = sprintf("%04d",$i);
            $log = new DDLLogCreate('logcreate');
            $log->setName("name_" . $nr);
            $log->setVersion($nr);
            $this->ddlchangelog->addEntry($log);
        }

        $countAll = count($this->ddlchangelog->getEntries());
        $countV1 = count($this->ddlchangelog->getEntries("0004"));

        $this->assertEquals($countAll , 9, 'DDLChangeLog, adding Logs or retrieving them failed');
        $this->assertEquals($countV1, 5, 'assert failed, adding Logs with a Version number or retrieving them failed');
    }

    /**
     * dropEntries
     *
     * @covers DDLChangeLog::dropEntries
     * @test
     */
    public function testDropEntries()
    {
        for ($i = 1; $i <10; $i++)
        {
            $nr = sprintf("%04d",$i);
            $log = new DDLLogCreate('logcreate');
            $log->setName("name_" . $nr);
            $log->setVersion($nr);
            $this->ddlchangelog->addEntry($log);
        }

        // let's be bad guys, dan drop everything again
        $this->ddlchangelog->dropEntries();
        $countAll = count($this->ddlchangelog->getEntries());

        $this->assertEquals($countAll , 0, 'DDLChangeLog, dropping the entries has failed');
    }

    /**
     * getEntries
     *
     * @covers DDLChangeLog::getEntries
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
                $log = new DDLLogCreate('logcreate');
                $log->setName("name_" . $nr);
            } else {
                $log = new DDLLogSql();
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
            $this->ddlchangelog->addEntry($log);
        }

        $countAll = count($this->ddlchangelog->getEntries(null));
        $this->assertEquals($countAll , 9, 'DDLChangeLog, dropping the entries has failed');

        $countAll = count($this->ddlchangelog->getEntries(null, 'mysql'));
        $this->assertEquals($countAll , 19, 'DDLChangeLog, dropping the entries has failed');

        $countAll = count($this->ddlchangelog->getEntries(null, 'oracle'));
        $this->assertEquals($countAll , 19, 'DDLChangeLog, dropping the entries has failed');

        // truncate list of changes
        $this->ddlchangelog->dropEntries();
        $countAll = count($this->ddlchangelog->getEntries());
        $this->assertEquals($countAll , 0, 'DDLChangeLog, dropping the entries has failed');
    }

    /**
     * add field to view
     *
     * @covers DDLView
     * @test
     */
    public function testAddViewField()
    {
        // DDL View
        $get = $this->ddlview->getFields();
        $this->assertEquals(0, count($get), 'assert failed, the values should be equal, no fields found - "0" expected');

        $this->ddlview->addField('name');
        $this->ddlview->addField('abcd');
        $this->ddlview->addField('qwerty');

        $get = $this->ddlview->getFields();
        $this->assertType('array', $get, 'assert failed, "DDLView" : the value is not from type array');

        $this->assertArrayHasKey('name', $get, 'assert failed, "DDLView" : expected true - the value should be match a key in array');
        $this->assertArrayHasKey('abcd', $get, 'assert failed, "DDLView" : expected true - the value should be match a key in array');
        $this->assertArrayHasKey('qwerty', $get, 'assert failed, "DDLView" : expected true - the value should be match a key in array');

        $get = $this->ddlview->getField('abcd');
        $this->assertType('object', $get, 'assert failed, "DDLView" : the value is not from type object');
        $this->assertTrue($get instanceof DDLViewField, 'assert failed, "DDLView" : the value should be an instance of DDLViewField');

        $this->ddlview->dropField('abcd');
        try {
            $get = $this->ddlview->getField('abcd');
            $this->fail("DDLView::dropField didn't drop the Column");
        } catch (\Exception $e) {
            //success
        }
    }

    /**
     * add field to form
     *
     * @covers DDLForm
     * @test
     */
    public function testAddFormField()
    {
        $get = $this->ddlform->getFields();
        $this->assertEquals(0, count($get), 'assert failed, the values should be equal "0" expected');

        $this->ddlform->addField('name');
        $this->ddlform->addField('abcd');
        $this->ddlform->addField('qwerty');

        $get = $this->ddlform->getFields();
        $this->assertType('array', $get, 'assert failed, "DDLView" : the value is not from type array');

        $this->assertArrayHasKey('name', $get, 'assert failed, "DDLView" : expected true - the value should be match a key in array');
        $this->assertArrayHasKey('abcd', $get, 'assert failed, "DDLView" : expected true - the value should be match a key in array');
        $this->assertArrayHasKey('qwerty', $get, 'assert failed, "DDLView" : expected true - the value should be match a key in array');

        $get = $this->ddlform->getField('abcd');
        $this->assertType('object', $get, 'assert failed, "DDLView" : the value is not from type object');
        $this->assertTrue($get instanceof DDLField, 'assert failed, "DDLView" : the value should be an instance of DDLField');

        $this->ddlform->dropField('abcd');
        try {
            $this->ddlform->dropField('abcd');
            $this->fail('Field was not deleted');
        } catch (NotFoundException $e) {
            // success
        }

    }

    /**
     * addFieldInvalidArgumentException
     *
     * @covers DDLView::addField
     * @expectedException InvalidArgumentException
     *
     * @test
     */
    public function testAddFieldInvalidArgumentException()
    {
        // DDL View
        $this->ddlview->addField('');
    }

    /**
     * getFieldInvalidArgumentException4
     *
     * @covers DDLView::getField
     * @expectedException InvalidArgumentException
     *
     * @test
     */
    public function testGetFieldInvalidArgumentException()
    {
        //DDLView
        $this->ddlview->getField('nonexist');
    }

    /**
     * FieldAlreadyExistsException
     *
     * @covers DDLForm::addField
     * @expectedException AlreadyExistsException
     *
     * @test
     */
    public function testAddFieldAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->ddlform->addField('field');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->ddlform->addField('field');
    }

    /**
     * getFormInvalidArgumentException
     *
     * @covers DDLForm::getFrom
     * @expectedException InvalidArgumentException
     *
     * @test
     */
    public function testGetFormInvalidArgumentException()
    {
      $this->ddlform->getForm('non-existing-form');
    }

    /**
     * get Query
     *
     * @covers DDLView::getQuery
     * @covers DDLView::getQueries
     * @covers DDLView::dropQuery
     * @test
     */
    public function testgetQuery()
    {
        $result = $this->ddlview->getQueries();
        $this->assertTrue(empty($result), 'DDLView::getQueries queries should be void in the beginning');

        $this->ddlview->setQuery("genericQuery");
        $this->ddlview->setQuery("mysqlQuery", "mysql");
        $result = $this->ddlview->getQueries();
        $this->assertTrue(count($result) == 2, 'DDLView::getQueries should return two different Query-Types');
        $result = $this->ddlview->getQuery();
        $this->assertTrue(count($result) == 1, 'DDLView::getQueries should return the generic Query');
        $result = $this->ddlview->getQuery('oracle');
        $this->assertNull($result, 'DDLView::getQueries should return no query because for this dbms there had been no query set');

        $this->ddlview->dropQuery('mysql');
        $result = $this->ddlview->getQueries();
        $this->assertTrue(count($result) == 1, 'DDLView::dropQueries should have dropped one of the Query-Types');
    }

    /**
     * Sequence
     *
     * @covers DDLDatabase::getSequence
     * @covers DDLDatabase::addSequence
     * @covers DDLDatabase::getSequences
     * @covers DDLDatabase::getSequenceNames
     * @covers DDLDatabase::isSequence
     * @covers DDLDatabase::dropSequence
     *
     * @test
     */
    public function testSequence()
    {
        // DDL Database
        $valid = $this->ddldatabase->isSequence('qwerty');
        $this->assertFalse($valid, 'assert failed, "DDLDatabase" the value should be false, "qwerty" is not a sequence');

        $add = $this->ddldatabase->addSequence('qwerty');
        $this->assertType('object', $add, 'assert failed, "DDLDatabase" the value should be from type object');
        $this->assertEquals('qwerty', $add->getName(), 'assert failed, "DDLDatabase" the values should be equal, the name of the view should be the same as expected');

        $add = $this->ddldatabase->addSequence('trewq');
        $this->assertType('object', $add, 'assert failed, "DDLDatabase" the value should be from type object');
        $this->assertEquals('trewq', $add->getName(), 'assert failed, "DDLDatabase" the values should be equal, the name of the view should be the same as expected');

        $get = $this->ddldatabase->getSequence('qwerty');
        $this->assertType('object', $get, 'assert failed, "DDLDatabase" the value should be from type object');
        $this->assertEquals('qwerty', $get->getName(), 'assert failed, "DDLDatabase" the values should be equal, the name of the view should be the same as expected');

        $getAll = $this->ddldatabase->getSequences();
        $this->assertType('array', $getAll, 'assert failed, "DDLDatabase" the value should be from type array');
        $this->assertArrayHasKey('qwerty', $getAll, 'assert failed, "DDLDatabase" the value should be match a entry in array');
        $this->assertArrayHasKey('trewq', $getAll, 'assert failed, "DDLDatabase" the value should be match a entry in array');

        $getNames = $this->ddldatabase->getSequenceNames();
        $this->assertType('array', $getNames, 'assert failed, "DDLDatabase" the values should be from type array');
        $this->assertTrue(in_array('qwerty', $getNames), 'assert failed, "DDLDatabase" the value should be match a entry in array');
        $this->assertTrue(in_array('trewq', $getNames), 'assert failed, "DDLDatabase" the value should be match a entry in array');

        $valid = $this->ddldatabase->isSequence('qwerty');
        $this->assertTrue($valid, 'assert failed, "DDLDatabase" the value should be true, "qwerty" is a sequence');

        $drop = $this->ddldatabase->dropSequence('qwerty');
        $nonexist = $this->ddldatabase->getSequence('qwerty');
        $this->assertNull($nonexist, 'assert failed, "DDLDatabase" the value should be null, "qwerty" was removed before');
    }

    /**
     * addSequenceAlreadyExistsException
     *
     * @covers DDLDatabase::addSequence
     * @expectedException AlreadyExistsException
     *
     * @test
     */
    public function testAddSequenceAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->ddldatabase->addSequence('sequence');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->ddldatabase->addSequence('sequence');
    }

    /**
     * drop non-existing sequence
     *
     * @covers DDLDatabase::dropSequence
     * @expectedException NotFoundException
     *
     * @test
     */
    public function testDropSequenceNotFoundException()
    {
        // DDLDatabase
        $this->ddldatabase->dropSequence('no_sequence');
    }

    /**
     * Sorting
     *
     * @covers DDLIndexColumn::setSorting
     * @covers DDLIndexColumn::isDescendingOrder
     * @covers DDLIndexColumn::isAscendingOrder
     * @test
     */
    public function testSorting()
    {
        $result = true;
        try {
            $this->ddlindexcolumn->setSorting(4711);
        } catch (\Exception $e) {
            $result = false;
        }
        $this->assertFalse($result, "DDLIndexColumn::setSorting should not accept anything but Boolean");

        $this->ddlindexcolumn->setSorting();
        $descending = $this->ddlindexcolumn->isDescendingOrder();
        $ascending = $this->ddlindexcolumn->isAscendingOrder();
        $this->assertFalse($descending, 'DDLIndexColumn::isDescendingOrder setting or retrieving the sorting is misaligned');
        $this->assertTrue($ascending, 'DDLIndexColumn::isAscendingOrder setting or retrieving the sorting is misaligned');

        $this->ddlindexcolumn->setSorting(false);
        $descending = $this->ddlindexcolumn->isDescendingOrder();
        $ascending = $this->ddlindexcolumn->isAscendingOrder();
        $this->assertTrue($descending, 'DDLIndexColumn::isDescendingOrder setting or retrieving the sorting is misaligned');
        $this->assertFalse($ascending, 'DDLIndexColumn::isAscendingOrder setting or retrieving the sorting is misaligned');
    }

    /**
     * dropInit
     *
     * @covers DDLDatabase::dropInit
     *
     * @test
     */
    public function testDropInit()
    {
        // ddl database
        $this->ddldatabase->dropInit();
        $init = $this->ddldatabase->getInit();
        $this->assertTrue(empty($init), 'Initialization list should be empty after droping contents');
    }

    /**
     * addInit
     *
     * @covers DDLDatabase::addInit
     * @covers DDLDatabase::getInit
     *
     * @test
     */
    public function testAddInit()
    {
        $get = $this->ddldatabase->getInit('oracle');
        $this->assertType('array', $get, 'assert failed, the value should be from type array');
        $this->assertEquals(0, count($get), 'assert failed, the values should be equal');

        $dbms = 'mysql';
        $sql = 'select * from users';
        $this->ddldatabase->addInit($sql, $dbms);
        $get = $this->ddldatabase->getInit($dbms);
        $this->assertEquals($sql, $get[0], 'assert failed, the values should be equal');

        $get = $this->ddldatabase->getInit('oracle');
        $this->assertType('array', $get, 'assert failed, the value should be from type array');
        $this->assertEquals(0, count($get), 'assert failed, the values should be equal');
    }

    /**
     * getListOfFiles
     *
     * @covers DDLDatabase::getListOfFiles
     * @test
     */
    public function testGetListOfFiles()
    {
        $get = DDL::getListOfFiles();
        $this->assertFalse(in_array('config/db//user.db.xml', $get), 'assert failed, the value can not be exist in array');
        $this->assertTrue(in_array('user', $get), 'assert failed, the value must be exist in array');
        $this->assertType('array', $get, 'assert failed, the value should be from type array');

        $get = DDL::getListOfFiles(true);
        $this->assertTrue(in_array('config/db//user.db.xml', $get), 'assert failed, the value must be exist in array');
        $this->assertFalse(in_array('user', $get), 'assert failed, the value can not be exist in array');
        $this->assertType('array', $get, 'assert failed, the value should be from type array');
    }

    /**
     * Form
     *
     * @covers DDLDatabase::getForm
     * @covers DDLDatabase::addForm
     * @covers DDLDatabase::getForms
     * @covers DDLDatabase::getFormNames
     * @covers DDLDatabase::isForm
     * @covers DDLDatabase::dropForm
     *
     * @test
     */
    public function testSetForm()
    {
         // DDL Database
        $valid = $this->ddldatabase->isForm('qwerty');
        $this->assertFalse($valid, 'assert failed, "DDLDatabase" the value should be false, "qwerty" is not a form');

        $add = $this->ddldatabase->addForm('qwerty');
        $this->assertType('object', $add, 'assert failed, "DDLDatabase" the value should be from type object');
        $this->assertEquals('qwerty', $add->getName(), 'assert failed, "DDLDatabase" the values should be equal, the name of the view should be the same as expected');

        $add = $this->ddldatabase->addForm('trewq');
        $this->assertType('object', $add, 'assert failed, "DDLDatabase" the value should be from type object');
        $this->assertEquals('trewq', $add->getName(), 'assert failed, "DDLDatabase" the values should be equal, the name of the view should be the same as expected');

        $get = $this->ddldatabase->getForm('qwerty');
        $this->assertType('object', $get, 'assert failed, "DDLDatabase" the value should be from type object');
        $this->assertEquals('qwerty', $get->getName(), 'assert failed, "DDLDatabase" the values should be equal, the name of the view should be the same as expected');

        $getAll = $this->ddldatabase->getForms();
        $this->assertType('array', $getAll, 'assert failed, "DDLDatabase" the value should be from type array');
        $this->assertArrayHasKey('qwerty', $getAll, 'assert failed, "DDLDatabase" the value should be match a entry in array');
        $this->assertArrayHasKey('trewq', $getAll, 'assert failed, "DDLDatabase" the value should be match a entry in array');

        $getNames = $this->ddldatabase->getFormNames();
        $this->assertType('array', $getNames, 'assert failed, "DDLDatabase" the values should be from type array');
        $this->assertTrue(in_array('qwerty', $getNames), 'assert failed, "DDLDatabase" the value should be match a entry in array');
        $this->assertTrue(in_array('trewq', $getNames), 'assert failed, "DDLDatabase" the value should be match a entry in array');

        $valid = $this->ddldatabase->isForm('qwerty');
        $this->assertTrue($valid, 'assert failed, "DDLDatabase" the value should be true, "qwerty" is a form');

        $drop = $this->ddldatabase->dropForm('qwerty');
        $nonexist = $this->ddldatabase->getForm('qwerty');
        $this->assertNull($nonexist, 'assert failed, "DDLDatabase" the value should be null, "qwerty" was dropt before');
    }

    /**
     * addFormAlreadyExistsException
     *
     * @covers DDLDatabase::addForm
     * @expectedException AlreadyExistsException
     *
     * @test
     */
    public function testAddFormAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->ddldatabase->addForm('form');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->ddldatabase->addForm('form');
    }

    /**
     * dropFormInvalidArgumentException
     *
     * @covers DDLDatabase::dropForm
     * @expectedException NotFoundException
     *
     * @test
     */
    public function testDropFormInvalidArgumentException1()
    {
        // DDLDatabase
        $this->ddldatabase->dropForm('gert');
    }

    /**
     * Column
     *
     * @covers DDLForeignKey::setColumn
     * @covers DDLForeignKey::getColumns
     * @test
     */
    public function testSetColumn()
    {
        // DDL ForeignKey
        $this->ddlforeignkey->setColumn('test', 'qwertz');
        $get = $this->ddlforeignkey->getColumns();
        $this->assertArrayHasKey('test', $get, 'assert failed, the values should be equal,  the value should be match a key in array');

        // the appending test can not be done with the attributes of the class,
        // because the lack the parents
        $tableTest = $this->ddldatabase->addTable('testSetColumn');
    }

    /**
     * Columns
     *
     * @covers DDLForeignKey::getColumns
     * @covers DDLForeignKey::setColumns
     * @covers DDLIndex::getColumns
     * @covers DDLIndex::addColumn
     * @covers DDLIndex::dropColumn
     *
     * @test
     */
    public function testColumns()
    {
        $array = array('column1', 'column2', 'column3');
        // DDL ForeignKey
        $this->ddlforeignkey->setColumns($array);
        $result = $this->ddlforeignkey->getColumns();
        $this->assertEquals($array, $result, 'assert failed, DDLForeignKey : the values shoud be equal, expected the same array which was set at the begining');

        $testTable = new DDLTable('testTable');
        $testForeignKey = new DDLForeignkey('testKey', $testTable);

        // negativer Test
        try {
            $testForeignKey->setColumns($array);
            $this->fail("DDLForeignKey::setCoLumns should fail, if one of the Columns in the Targettable does not exists");
        } catch (\Exception $e) {
            //success
        }

        // DDL Index
        try {
            $this->ddlindex->addColumn("noColumn");
            $this->fail("DDLIndex::not existing column should raise an exception");
        } catch (\Exception $e) {
            // success
        }

        $someNames = array("someName_1", "someName_2", "someName_3");
        $this->ddltable->addColumn($someNames[0], 'integer');
        $this->ddltable->addColumn($someNames[1], 'integer');
        $this->ddltable->addColumn($someNames[2], 'integer');

        $result = $this->ddlindex->addColumn($someNames[0]);
        $this->assertType('DDLIndexColumn', $result, "unexpectet Returntype from addcolumn");

        $result = $this->ddlindex->addColumn($someNames[1]);
        $result = $this->ddlindex->addColumn($someNames[2]);
        try {
            $this->ddlindex->addColumn($someNames[0]);
            $this->fail("DDLIndex::redefining column should rise an exception");
        } catch (\Exception $e) {
            // success
        }

        $this->ddlindex->dropColumn($someNames[1]);
        $columns = $this->ddlindex->getColumns();
        $this->assertEquals(count($columns), 2, 'DDLIndex: either the Dropping of a column or the getting has failed');
    }

    /**
     * Column
     *
     * @covers DDLIndex::setColumn
     *
     * @expectedException NotFoundException
     * @test
     */
    function testSetColumnNotFoundException()
    {
         $this->ddlindex->addColumn('');
    }

    /**
     * Title
     *
     * @covers DDLField::getTitle
     * @covers DDLField::setTitle
     *
     * @test
     */
    public function testTitleDDLField()
    {
        // ddl field
        $this->ddlfield->setTitle('abcd');
        $result = $this->ddlfield->getTitle();
        $this->assertEquals('abcd', $result, 'assert failed, DDLFiled : expected "abcd" as value, the values should be equal');

        $this->ddlfield->setTitle('');
        $result = $this->ddlfield->getTitle();
        $this->assertNull($result, 'assert failed, DDLFiled : expected null, no label is set');
    }

    /**
     * Name
     *
     * @covers DDLLogCreate::setName
     * @covers DDLLogCreate::getName
     * @covers DDLIndex::setName
     * @covers DDLIndex::getName
     *
     * @test
     */
    public function testSetName()
    {
        // DDLLogCreate
        $this->ddllogcreate->setName('name');
        $get = $this->ddllogcreate->getName();
        $this->assertEquals('name', $get, 'assert failed, the values should be equal, "DDLLogCreate" :expected "name" as value, the values should be equal');

        // DDL Index
        $this->ddlindex->setName('name');
        $get = $this->ddlindex->getName();
        $this->assertEquals('name', $get, 'assert failed, the values should be equal, "DDLIndex" :expected "name" as value, the values should be equal');

        $this->ddlindex->setName('');
        $get = $this->ddlindex->getName();
        $this->assertNull($get, 'assert failed, the values should be equal, "DDLIndex" :expected null, the name is not set');
    }

    /**
     * Name
     *
     * @covers DDLObject::setName
     *
     * @expectedException InvalidArgumentException
     * @test
     */
    function testSetNameInvalidArgument()
    {
        // DDL Object exception
        $new = new DDLIndex(' 123df');
    }

    /**
     * Name
     *
     * @covers DDLLogCreate::setName
     *
     * @expectedException InvalidArgumentException
     * @test
     */
    function testSetNameInvalidArgument1()
    {
        $this->ddllogcreate->setName('');
    }

    /**
     * OldName
     *
     * @covers DDLLogRename::setOldName
     * @covers DDLLogRename::getOldName
     *
     * @test
     */
    public function testOldName()
    {
        // DDLLogRename
        $this->ddllogrename->setOldName('name');
        $get = $this->ddllogrename->getOldName();
        $this->assertEquals('name', $get, 'assert failed, the values should be equal, "DDLLogRename" :expected "name" as value, the values should be equal');

        $this->ddllogrename->setOldName('');
        $get = $this->ddllogrename->getOldName();
        $this->assertNull($get, 'assert failed, "DDLLogRename" : expected null, the OldName is not set');
    }

    /**
     * Order by
     *
     * @covers DDLView::setOrderBy
     * @covers DDLView::getOrderBy
     * @covers DDLView::isDescendingOrder
     *
     * @test
     */
    public function testOrderBy()
    {
        $array = array();
        // DDL View
        $this->ddlview->setOrderBy(array('qwerty'));
        $get = $this->ddlview->getOrderBy();
        $this->assertEquals(array('qwerty'), $get, 'assert failed, the values should be equal, "DDLView" :the arrays should be match each other');
        $isDesc = $this->ddlview->isDescendingOrder();
        $this->assertFalse($isDesc, 'assert failed, "DDLView" : expected false, no descendingOrder is set');

        $this->ddlview->setOrderBy($array, true);
        $get = $this->ddlview->getOrderBy();
        $this->assertEquals(0, count($get), 'assert failed, the values should be equal, "DDLView" :the array should be match each other');
        $isDesc = $this->ddlview->isDescendingOrder();
        $this->assertTrue($isDesc, 'assert failed, "DDLView" : expected true, descendingOrder is set');
    }

    /**
     * Property name
     *
     * @covers DDLLogUpdate::setPropertyName
     * @covers DDLLogUpdate::getPropertyName
     *
     * @test
     */
    public function testPropertyName()
    {
        // DDLLogUpdate
        $this->ddllogupdate->setPropertyName('property');
        $get = $this->ddllogupdate->getPropertyName();
        $this->assertEquals('property', $get, 'assert failed, the values should be equal, "DDLLogUpdate" :expected value "property" ');

        $this->ddllogupdate->setPropertyName('');
        $get = $this->ddllogupdate->getPropertyName();
        $this->assertNull($get, 'assert failed, "DDLLogUpdate" : expected null, PropertyName is not set');
    }

    /**
     * Property value
     *
     * @covers DDLLogUpdate::setPropertyValue
     * @covers DDLLogUpdate::getPropertyValue
     *
     * @test
     */
    public function testSetPropertyValue()
    {
        // DDLLogUpdate
        $this->ddllogupdate->setPropertyValue('propertyValue');
        $get = $this->ddllogupdate->getPropertyValue();
        $this->assertEquals('propertyValue', $get, 'assert failed, the values should be equal, "DDLLogUpdate" : expected "propertyValue" as value');

        $this->ddllogupdate->setPropertyValue('');
        $get = $this->ddllogupdate->getPropertyValue();
        $this->assertNull($get, 'assert failed, "DDLLogUpdate" : expected null, the PropertyValue is not set');
    }

    /**
     * Start
     *
     * @covers DDLSequence::getStart
     * @covers DDLSequence::setStart
     *
     * @test
     */
    public function testStart()
    {
        $this->ddlsequence->setStart(1);
        $get = $this->ddlsequence->getStart();
        $this->assertEquals(1, $get, 'assert failed, DDLSequence : expected "1" as number');

        $this->ddlsequence->setStart(0);
        $get = $this->ddlsequence->getStart();
        $this->assertNull($get, 'assert failed, DDLSequence : expected null, start is not set');
    }

    /**
     * Start
     *
     * @covers DDLSequence::setStart
     *
     * @expectedException OutOfBoundsException
     * @test
     */
    public function testSetStartInvalidArgument1()
    {
        $this->ddlsequence->setMin(6);
        $this->ddlsequence->setStart(5);
    }

    /**
     * Increment
     *
     * @covers DDLSequence::getIncrement
     * @covers DDLSequence::setIncrement
     *
     * @test
     */
    public function testIncrement()
    {

        $get = $this->ddlsequence->getIncrement();
        $this->assertEquals(1, $get, 'if not defined otherwise, Sequenz should iterate with 1-Steps');

        $this->ddlsequence->setIncrement(2);
        $get = $this->ddlsequence->getIncrement();
        $this->assertEquals(2, $get, 'assert failed, DDLSequence : the values should be equal');

        try {
            $this->ddlsequence->setIncrement(0);
            $this->fail("Increment value may not be set to '0'.");
        } catch (InvalidArgumentException $e) {
            // success
        }
    }

    /**
     * Implementation
     *
     * @covers DDLFunction::getImplementation
     * @covers DDLFunction::getImplementations
     * @covers DDLFunction::setImplementation
     *
     * @test
     */
    public function testImplementation()
    {
       $test3 = $this->ddlfunction->getImplementation("mysql");
       $this->assertNull($test3, "DDLFunction, no test implementations are set");

       $f1 = $this->ddlfunction->addImplementation('mysql');
       $f2 = $this->ddlfunction->addImplementation('oracle');

       $test1 = $this->ddlfunction->getImplementations();
       $this->assertEquals(count($test1), 2, "DDLFunction, a problem with reading/writing implementations has occured");

       $test2 = $this->ddlfunction->getImplementation("mysql");
       $this->assertEquals(count($test2), 1, "DDLFunction, a problem with reading specified implementations has occured");
    }

    /**
     * ImplementationAlreadyExistsException
     *
     * @covers DDLFunction::addImplementation
     * @expectedException AlreadyExistsException
     *
     * @test
     */
    public function testImplementationAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->ddlfunction->addImplementation();
        } catch (AlreadyExistsException $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->ddlfunction->addImplementation();
    }

    /**
     * Increment
     *
     * @covers DDLSequence::getIncrement
     * @covers DDLSequence::setIncrement
     *
     * @expectedException InvalidArgumentException
     * @test
     */
    function testSetIncrementInvalidArgument()
    {
         $this->ddlsequence->setIncrement(0);
    }

    /**
     * Min
     *
     * @covers DDLSequence::getMin
     * @covers DDLSequence::setMin
     *
     * @test
     */
    public function testMin()
    {
        $this->ddlsequence->setMin();
        $get = $this->ddlsequence->getMin();
        $this->assertEquals(null, $get, 'setMin() without arguments should reset the property.');

        $this->ddlsequence->setMin(1);
        $get = $this->ddlsequence->getMin();
        $this->assertEquals(1, $get, 'getMin() should return the same value as previously set by setMin().');

        $this->ddlsequence->setStart(2);
        $this->ddlsequence->setMin(2); // should succeed
        $get = $this->ddlsequence->getMin();
        $this->assertEquals(2, $get, 'setMin() to lower boundary must succeed.');
        try {
            $this->ddlsequence->setMin(3);
            $this->fail("Should not be able to set minimum higher than start value.");
        } catch (OutOfBoundsException $e) {
            // success
        }
    }

    /**
     * Max
     *
     * @covers DDLSequence::getMax
     * @covers DDLSequence::setMax
     *
     * @test
     */
    public function testMax()
    {
        $this->ddlsequence->setMax();
        $get = $this->ddlsequence->getMax();
        $this->assertEquals(null, $get, 'setMax() without arguments should reset the property.');

        $this->ddlsequence->setMax(3);
        $get = $this->ddlsequence->getMax();
        $this->assertEquals(3, $get, 'getMax() should return the same value as previously set by setMax().');

        $this->ddlsequence->setStart(2);
        $this->ddlsequence->setMax(2); // should succeed
        $get = $this->ddlsequence->getMax();
        $this->assertEquals(2, $get, 'setMax() to lower boundary must succeed.');
        try {
            $this->ddlsequence->setMax(1);
            $this->fail("Should not be able to set maximum lower than start value.");
        } catch (OutOfBoundsException $e) {
            // success
        }
    }

    /**
     * TargetTable
     *
     * @covers DDLForeignKey::getTargetTable
     * @covers DDLForeignKey::setTargetTable
     *
     * @test
     */
    public function testTargetTable()
    {
        // DDL ForeignKey
        $this->ddlforeignkey->setTargetTable('targetTable');
        $result = $this->ddlforeignkey->getTargetTable();
        $this->assertEquals('targettable', $result, 'getTargetTable() did not return expected value');

        $this->ddlforeignkey->setTargetTable('');
        $result = $this->ddlforeignkey->getTargetTable();
        $this->assertNull($result, 'reset of target table failed');
    }

    /**
     * Match
     *
     * @covers DDLForeignKey::getMatch
     * @covers DDLForeignKey::setMatch
     *
     * @test
     */
    public function testMatch()
    {
        // DDL ForeignKey
        $this->ddlforeignkey->setMatch(2);
        $result = $this->ddlforeignkey->getMatch();
        $message = 'assert failed, DDLForeignKey : expected value is the number 2';
        $this->assertEquals(DDLKeyMatchStrategyEnumeration::SIMPLE, $result, $message);

        $this->ddlforeignkey->setMatch(12);
        $result = $this->ddlforeignkey->getMatch();
        // expected default 0
        $message = 'assert failed, DDLForeignKey : expected 0 as value, the 0 number will be choosen when the number ' .
            'by setMatch does not match the numbers 0, 1, 2';
        $this->assertEquals(DDLKeyMatchStrategyEnumeration::SIMPLE, $result, $message);
    }

    /**
     * Template
     *
     * @covers DDLForm::getTemplate
     * @covers DDLForm::setTemplate
     * @test
     */
    public function testTemplate()
    {
        // DDL Form
        $this->ddlform->setTemplate('template');
        $result = $this->ddlform->getTemplate();
        $this->assertEquals('template', $result, 'assert failed, DDLForm : expected value is "template"');

        $this->ddlform->setTemplate('');
        $result = $this->ddlform->getTemplate();
        $this->assertNull($result, 'assert failed, DDLForm : expected null, non template is set');
    }

    /**
     * Language
     *
     * @covers DDLFunctionImplementation::getLanguage
     * @covers DDLFunctionImplementation::setLanguage
     *
     * @test
     */
    public function testLanguage()
    {
        // DDL FunctionImplementation
        $this->ddlfunctionimplementation->setLanguage('language');
        $validate = $this->ddlfunctionimplementation->getLanguage();
        $this->assertEquals('language', $validate, 'DDLFunctionImplementation : expected value is "language"');
    }

    /**
     * Code
     *
     * @covers DDLFunctionImplementation::getCode
     * @covers DDLFunctionImplementation::setCode
     *
     * @test
     */
    public function testCode()
    {
        // DDL FunctionImplementation
        $this->ddlfunctionimplementation->setCode('code');
        $validate = $this->ddlfunctionimplementation->getCode();
        $this->assertEquals('code', $validate, 'DDLFunctionImplementation : expected value is "code"');
    }

    /**
     * Return
     *
     * @covers DDLFunctionImplementation::getReturn
     * @covers DDLFunctionImplementation::setReturn
     *
     * @test
     */
    public function testReturn()
    {
        // DDL FunctionImplementation
        $this->ddlfunctionimplementation->setReturn('return');
        $validate = $this->ddlfunctionimplementation->getReturn();
        $this->assertEquals('return', $validate, 'DDLFunctionImplementation : expected "return" as value');

        $this->ddlfunctionimplementation->setReturn('');
        $result = $this->ddlfunctionimplementation->getReturn();
        $this->assertNull($result, 'assert failed, DDLFunctionImplementation : expected null, the return is not set');
    }

    /**
     * parameter definition
     *
     * @covers DDLFunctionImplementation::getParameter
     * @covers DDLFunctionImplementation::getParameters
     * @covers DDLFunctionImplementation::getParameterNames
     * @covers DDLFunctionImplementation::addParameter
     * @covers DDLFunctionImplementation::setParameter
     * @covers DDLFunctionImplementation::dropParameter
     *
     * @test
     */
    public function testParameter()
    {
        // DDL FunctionImplementation
        $this->ddlfunctionimplementation->addParameter('control');
        $valid = $this->ddlfunctionimplementation->getParameters();
        $this->assertArrayHasKey('control', $valid, 'assert failed, DDLFunctionImplementation : expected "control" as value');

        $valid = $this->ddlfunctionimplementation->getParameterNames();
        $this->assertEquals('control', $valid[0], 'assert failed, DDLFunctionImplementation : expected "control" as value');

        $this->ddlfunctionimplementation->getParameter('control');
        $this->ddlfunctionimplementation->addParameter('test');
        $this->ddlfunctionimplementation->addParameter('new');

        $valid = $this->ddlfunctionimplementation->getParameters();
        $this->assertArrayHasKey('control', $valid, 'assert failed, DDLFunctionImplementation : the value "control" should be match a key in array');
        $this->assertArrayHasKey('test', $valid, 'assert failed, DDLFunctionImplementation : the value "test" should be match a key in array');
        $this->assertArrayHasKey('new', $valid, 'assert failed, DDLFunctionImplementation : the value "new" should be match a key in array');

        $this->ddlfunctionimplementation->dropParameter('test');
        $valid = $this->ddlfunctionimplementation->getParameters();
        $this->assertArrayNotHasKey('test', $valid, 'assert failed, DDLFunctionImplementation : the value "test" should not be match a key in array');

        $name = 'a';
        $newParam = $this->ddlfunctionimplementation->addParameter($name);
        $valid = $this->ddlfunctionimplementation->getParameters();
        $this->assertArrayHasKey($name, $valid, 'assert failed, DDLFunctionImplementation : the value "name" should be match a key in array');

        $parameter = $this->ddlfunctionimplementation->getParameter('b');
        $this->assertNull($parameter, 'function must return NULL for undefined parameter "b"');
    }

    /**
     * addParameterAlreadyExistsException
     *
     * @test
     * @covers DDLFunctionImplementation::addParameter
     *
     * @expectedException AlreadyExistsException
     */
    public function testAddParameterAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->ddlfunctionimplementation->addParameter('parameter');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->ddlfunctionimplementation->addParameter('parameter');
    }

    /**
     * Function: DBMS
     *
     * @test
     */
    public function testFunctionDBMS()
    {
        // DDL FunctionImplementation
        $implementation = $this->ddlfunction->addImplementation('MsSqL');
        $validate = $implementation->getDBMS();
        $this->assertEquals('mssql', $validate, 'assert failed, DDLFunctionImplementation : expected "mssql", the values should be equal');

        $implementation = $this->ddlfunction->addImplementation();
        $validate = $implementation->getDBMS();
        // expected generic
        $this->assertEquals('generic', $validate, 'assert failed, DDLFunctionImplementation : expected "generic", the values should be equal');
    }

    /**
     * Data-provider for testDBMS
     */
    public function dataDBMS()
    {
        return array(
            array('ddllogsql'),
            array('ddllogchange'),
            array('ddldatabaseinit'),
            array('ddltrigger'),
            array('ddlconstraint')
        );
    }

    /**
     * DBMS
     *
     * @covers DDLLogSql::setDBMS
     * @covers DDLLogSql::getDBMS
     * @covers DDLLogChange::setDBMS
     * @covers DDLLogChange::getDBMS
     * @covers DDLDatabaseInit::setDBMS
     * @covers DDLDatabaseInit::getDBMS
     * @covers DDLTrigger::setDBMS
     * @covers DDLTrigger::getDBMS
     * @covers DDLConstraint::setDBMS
     * @covers DDLConstraint::getDBMS
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
     * @covers  DDLGrant::getRole()
     * @covers  DDLGrant::setRole()
     */
    public function testRole()
    {
        // setter and getter
        $this->ddlgrant->setRole("test");
        $role = $this->ddlgrant->getRole();
        $this->assertEquals("test", $role, 'getRole() should return the same value as set with setRole().');

        // default value
        $this->ddlgrant->setRole();
        $role = $this->ddlgrant->getRole();
        $this->assertEquals(null, $role, 'User role should default to null.');
    }

    /**
     * user group
     *
     * @test
     * @covers  DDLGrant::getUser()
     * @covers  DDLGrant::setUser()
     */
    public function testUser()
    {
        // setter and getter
        $this->ddlgrant->setUser("test");
        $user = $this->ddlgrant->getUser();
        $this->assertEquals("test", $user, 'getUser() should return the same value as set with setUser().');

        // default value
        $this->ddlgrant->setUser();
        $user = $this->ddlgrant->getUser();
        $this->assertEquals(null, $user, 'User group should default to null.');
    }

    /**
     * security level
     *
     * @test
     * @covers  DDLGrant::getLevel()
     * @covers  DDLGrant::setLevel()
     */
    public function testLevel()
    {
        // setter and getter
        $this->ddlgrant->setLevel(0);
        $level = $this->ddlgrant->getLevel();
        $this->assertEquals(0, $level, 'getLevel() should return the same value as set with setLevel().');
        $this->ddlgrant->setLevel(100);
        $level = $this->ddlgrant->getLevel();
        $this->assertEquals(100, $level, 'getLevel() should return the same value as set with setLevel().');

        // default value
        $this->ddlgrant->setLevel();
        $level = $this->ddlgrant->getLevel();
        $this->assertEquals(null, $level, 'Security level should default to null.');
    }

    /**
     * security level exceeding lower bounds
     *
     * @test
     * @covers  DDLGrant::setLevel()
     * @expectedException InvalidArgumentException
     */
    public function testLevelInvalidArgument1()
    {
        $this->ddlgrant->setLevel(-1);
    }

    /**
     * security level exceeding upper bounds
     *
     * @test
     * @covers  DDLGrant::setLevel()
     * @expectedException InvalidArgumentException
     */
    public function testLevelInvalidArgument2()
    {
        $this->ddlgrant->setLevel(101);
    }

    /**
     * select statements
     *
     * @test
     * @covers  DDLGrant::isSelectable()
     * @covers  DDLGrant::setSelect()
     */
    public function testSelect()
    {
        // set to false
        $this->ddlgrant->setSelect(false);
        $isSelectable = $this->ddlgrant->isSelectable();
        $this->assertFalse($isSelectable, 'isSelectable() should return the same value as set with setSelect().');

        // default value
        $this->ddlgrant->setSelect();
        $isSelectable = $this->ddlgrant->isSelectable();
        $this->assertTrue($isSelectable, 'Selectable should default to true.');
    }

    /**
     * insert statements
     *
     * @test
     * @covers  DDLGrant::isInsertable()
     * @covers  DDLGrant::setInsert()
     */
    public function testInsert()
    {
        // set to false
        $this->ddlgrant->setInsert(false);
        $isInsertable = $this->ddlgrant->isInsertable();
        $this->assertFalse($isInsertable, 'isInsertable() should return the same value as set with setInsert().');

        // default value
        $this->ddlgrant->setInsert();
        $isInsertable = $this->ddlgrant->isInsertable();
        $this->assertTrue($isInsertable, 'Insertable should default to true.');
    }

    /**
     * update statements
     *
     * @test
     * @covers  DDLGrant::isUpdatable()
     * @covers  DDLGrant::setUpdate()
     */
    public function testUpdate()
    {
        // set to false
        $this->ddlgrant->setUpdate(false);
        $isUpdatable = $this->ddlgrant->isUpdatable();
        $this->assertFalse($isUpdatable, 'isUpdatable() should return the same value as set with setUpdate().');

        // default value
        $this->ddlgrant->setUpdate();
        $isUpdatable = $this->ddlgrant->isUpdatable();
        $this->assertTrue($isUpdatable, 'Updatable should default to true.');
    }

    /**
     * delete statements
     *
     * @test
     * @covers  DDLGrant::isDeletable()
     * @covers  DDLGrant::setDelete()
     */
    public function testDelete()
    {
        // set to false
        $this->ddlgrant->setDelete(false);
        $isDeletable = $this->ddlgrant->isDeletable();
        $this->assertFalse($isDeletable, 'isDeletable() should return the same value as set with setDelete().');

        // default value
        $this->ddlgrant->setDelete();
        $isDeletable = $this->ddlgrant->isDeletable();
        $this->assertTrue($isDeletable, 'Deletable should default to true.');
    }

    /**
     * grant option
     *
     * @test
     * @covers  DDLGrant::isGrantable()
     * @covers  DDLGrant::setGrantOption()
     */
    public function testGrantable()
    {
        // set to false
        $this->ddlgrant->setGrantOption(false);
        $isGrantable = $this->ddlgrant->isGrantable();
        $this->assertFalse($isGrantable, 'isGrantable() should return the same value as set with setGrantOption().');

        // default value
        $this->ddlgrant->setGrantOption();
        $isGrantable = $this->ddlgrant->isGrantable();
        $this->assertTrue($isGrantable, 'Grantable should default to true.');
    }

    /**
     * event
     *
     * @test
     */
    public function testSetEvent()
    {
        $event = $this->ddlform->addEvent('test');
        $event->setAction('bla');
        $getAll = $this->ddlform->getEvents();
        $this->assertType('array', $getAll, 'assert failed, the value is not from type array');
        $this->assertArrayHasKey('test', $getAll, 'assert failed, the value "test" should be match a key in array');

        $get = $this->ddlform->getEvent('test');
        $this->assertEquals('bla', $get->getAction(), 'assert failed, expected value "bla"');

        $get = $this->ddlform->dropEvent('test');
        $this->assertTrue($get, 'assert failed, event is not droped');

        $get = $this->ddlform->dropEvent('test_foo_bar');
        $this->assertFalse($get, 'assert failed, event does not exist and can\'t be droped');

         $get = $this->ddlform->getEvent('non-existing-event');
         $this->assertNull($get, 'assert failed, expected null for non-exist event');
    }

    /**
     * EventInvalidArgumentException
     *
     * @expectedException InvalidArgumentException
     *
     * @test
     */
    public function testAddEventInvalidArgumentException()
    {
        $this->ddlform->addEvent('');
    }

    /**
     * EventInvalidArgumentException
     *
     * @expectedException InvalidArgumentException
     *
     * @test
     */
    public function testAddFieldEventInvalidArgumentException()
    {
        $this->ddlfield->addEvent('');
    }

    /**
     * Data-provider for testSetGrants
     */
    public function dataSetGrants()
    {
        return array(
            array('ddlform'),
            array('ddlfield'),
            array('ddlcolumn'),
            array('ddlview'),
            array('ddltable')
        );
    }

    /**
     * Grants
     *
     * @covers DDLForm::getGrants
     * @covers DDLForm::setGrants
     * @covers DDLForm::addGrants
     * @covers DDLField::getGrants
     * @covers DDLField::setGrants
     * @covers DDLField::addGrants
     * @covers DDLField::setGrant
     * @covers DDLColumn::getGrants
     * @covers DDLColumn::setGrants
     * @covers DDLColumn::addGrants
     * @covers DDLView::getGrants
     * @covers DDLView::setGrants
     * @covers DDLView::addGrants
     * @covers DDLTable::getGrants
     * @covers DDLTable::setGrants
     * @covers DDLTable::addGrants
     *
     * @dataProvider dataSetGrants
     * @param  string  $propertyName
     * @test
     */
    public function testGrants($propertyName)
    {
        $object = $this->$propertyName;
        $grant = new DDLGrant();
        $grant2 = new DDLGrant();
        $grant3 = new DDLGrant();
        $grant4 = new DDLGrant();

        $grants = array($grant, $grant2);

        $object->setGrant($grant);
        $object->setGrant($grant2);

        $get = $object->getGrants();
        $this->assertEquals($grants, $get, 'assert failed, the values should be equal, expected the same arrays');
        $this->assertEquals($grants[0] instanceof DDLGrant, 'assert failed, the value should be an instance of DDLGrant');

        $add = $object->addGrant('user', 'role', 10);
        $this->assertTrue($add instanceof DDLGrant, 'Function addGrant() should return instance of DDLGrant.');

        $object->dropGrants();

        $get = $object->getGrants();
        $this->assertEquals(array(), $get, 'Function getGrants() should return an empty array after calling dropGrants().');
    }

    /**
     * parent
     *
     * @covers DDLChangeLog::getParent
     * @covers DDLColumn::getParent
     * @covers DDLForeignKey::getParent
     * @covers DDLForm::getParent
     * @covers DDLForm::getDatabase
     * @covers DDLFunction::getParent
     * @covers DDLIndex::getParent
     * @covers DDLView::getParent
     * @covers DDLTable::getParent
     *
     * @test
     */
    public function testParent()
    {
        $get = $this->ddlforeignkey->getParent();
        $this->assertNull($get, 'assert failed, expected null - no parent is set');

        $database = new DDLDatabase();
        $parentTable = new DDLTable('table');
        $parentColumn = new DDLColumn('Column_Parent');
        $parentForm = new DDLForm('someform');

        // DDLChangeLog
        $childLog = new DDLChangeLog($database);
        $parentLog = $childLog->getParent();
        $this->assertEquals($database, $parentLog, 'DDLChangeLog::getParent, the values should be equal');

        // DDLColumn
        $childColumn = new DDLColumn('column', $parentTable);
        $parentColumn = $childColumn->getParent();
        $this->assertEquals($parentTable, $parentColumn, 'DDLColumn::getParent, the values should be equal');

        // DDLForeignKey
        $childForeignkey = new DDLForeignKey('column', $parentTable);
        $parentForeignkey = $childForeignkey->getParent();
        $this->assertEquals($parentTable, $parentForeignkey, 'DDLForeignKey::getParent, the values should be equal');

        // DDLForm
        $childForm = new DDLForm('form', $database);
        $parentForm = $childForm->getParent();
        $this->assertEquals($database, $parentForm, 'DDLForm::getParent, the values should be equal');
        $parentForm = $childForm->getDatabase();
        $this->assertEquals($database, $parentForm, 'DDLForm::getParent, the values should be equal');

        // DDLForm sub-form
        $subForm = $childForm->addForm('subform');
        $parentForm = $subForm->getParent();
        $this->assertEquals($parentForm, $childForm, 'DDLForm::getParent, the values should be equal');
        $parentDatabase = $subForm->getDatabase();
        $this->assertEquals($database, $parentDatabase, 'DDLForm::getDatabase, the values should be equal');

        // DDLFunction
        $childFunction = new DDLFunction('function', $database);
        $parentFunction = $childFunction->getParent();
        $this->assertEquals($database, $parentFunction, 'DDLFunction::getParent, the values should be equal');

        // DDLIndex
        $childIndex = new DDLIndex('index', $parentTable);
        $parentIndex = $childIndex->getParent();
        $this->assertEquals($parentTable, $parentIndex, 'DDLIndex::getParent, the values should be equal');

        // DDLView
        $childView = new DDLView('view', $database);
        $parentView = $childView->getParent();
        $this->assertEquals($database, $parentView, 'DDLView::getParent, the values should be equal');

        // DDLTable
        $childTable = new DDLTable('table', $database);
        $parentTable = $childTable->getParent();
        $this->assertEquals($database, $parentTable, 'DDLTable::getParent, the values should be equal');
    }

    /**
     *  on-delete action
     *
     * @covers DDLForeignKey::getOnDelete
     * @covers DDLForeignKey::setOnDelete
     *
     * @test
     */
    public function testOnDelete()
    {
        $this->ddlforeignkey->setOnDelete(DDLKeyUpdateStrategyEnumeration::NOACTION);
        $get = $this->ddlforeignkey->getOnDelete();
        $message = 'assert failed, expected value is "0" - the values should be equal';
        $this->assertEquals(DDLKeyUpdateStrategyEnumeration::NOACTION, $get, $message);

        $this->ddlforeignkey->setOnDelete(DDLKeyUpdateStrategyEnumeration::RESTRICT);
        $get = $this->ddlforeignkey->getOnDelete();
        $message ='assert failed, expected value is "1" - the values should be equal';
        $this->assertEquals(DDLKeyUpdateStrategyEnumeration::RESTRICT, $get, $message);

        $this->ddlforeignkey->setOnDelete(DDLKeyUpdateStrategyEnumeration::CASCADE);
        $get = $this->ddlforeignkey->getOnDelete();
        $message = 'assert failed, expected value is "2" - the values should be equal';
        $this->assertEquals(DDLKeyUpdateStrategyEnumeration::CASCADE, $get, $message);

        $this->ddlforeignkey->setOnDelete(DDLKeyUpdateStrategyEnumeration::SETNULL);
        $get = $this->ddlforeignkey->getOnDelete();
        $message = 'assert failed, expected value is "3" - the values should be equal';
        $this->assertEquals(DDLKeyUpdateStrategyEnumeration::SETNULL, $get, $message);

        $this->ddlforeignkey->setOnDelete(DDLKeyUpdateStrategyEnumeration::SETDEFAULT);
        $get = $this->ddlforeignkey->getOnDelete();
        $message = 'assert failed, expected value is "4" - the values should be equal';
        $this->assertEquals(DDLKeyUpdateStrategyEnumeration::SETDEFAULT, $get, $message);

        $this->ddlforeignkey->setOnDelete(14);
        $get = $this->ddlforeignkey->getOnDelete();
        $message = 'assert failed, expected value is "0" - only numbers between 0-4 can be set ' .
            'otherwise the default value "0" will be set';
        $this->assertEquals(DDLKeyUpdateStrategyEnumeration::NOACTION, $get, $message);
    }

    /**
     *  on-delete action
     *
     * @covers DDLForeignKey::getOnUpdate
     * @covers DDLForeignKey::setOnUpdate
     *
     * @test
     */
    public function testOnUpdate()
    {
        $this->ddlforeignkey->setOnUpdate(0);
        $get = $this->ddlforeignkey->getOnUpdate();
        $this->assertEquals(0, $get, 'assert failed, expected value is "0" - the values should be equal');

        $this->ddlforeignkey->setOnUpdate(1);
        $get = $this->ddlforeignkey->getOnUpdate();
        $this->assertEquals(1, $get, 'assert failed, expected value is "1" - the values should be equal');

        $this->ddlforeignkey->setOnUpdate(2);
        $get = $this->ddlforeignkey->getOnUpdate();
        $this->assertEquals(2, $get, 'assert failed, expected value is "2" - the values should be equal');

        $this->ddlforeignkey->setOnUpdate(3);
        $get = $this->ddlforeignkey->getOnUpdate();
        $this->assertEquals(3, $get, 'assert failed, expected value is "3" - the values should be equal');

        $this->ddlforeignkey->setOnUpdate(4);
        $get = $this->ddlforeignkey->getOnUpdate();
        $this->assertEquals(4, $get, 'assert failed, expected value is "4" - the values should be equal');

        $this->ddlforeignkey->setOnUpdate(14);
        $get = $this->ddlforeignkey->getOnUpdate();
        $this->assertEquals(0, $get, 'assert failed, expected value is "0" - only numbers between 0-4 can be set otherwise the default value "0" will be set');
    }

    /**
     * Old property value
     *
     * @covers DDLLogUpdate::setOldPropertyValue
     * @covers DDLLogUpdate::getOldPropertyValue
     *
     * @test
     */
    public function testOldPropertyValue()
    {
        // DDLLogUpdate
        $this->ddllogupdate->setOldPropertyValue('name');
        $get = $this->ddllogupdate->getOldPropertyValue();
        $this->assertEquals('name', $get, 'assert failed, the values should be equal, "DDLLogRename" :expected value is "name"');

        $this->ddllogupdate->setOldPropertyValue('');
        $get = $this->ddllogupdate->getOldPropertyValue();
        $this->assertNull($get, 'assert failed, "DDLLogRename" :expected null - OldPropertyValue is not set or empty');
    }

    /**
     * Handler
     *
     * @covers DDLLogUpdate::setHandler
     * @covers DDLLogUpdate::commitUpdate
     * @covers DDLLogSql::setHandler
     * @covers DDLLogSql::commitUpdate
     * @covers DDLLogRename::setHandler
     * @covers DDLLogRename::commitUpdate
     * @covers DDLLogCreate::setHandler
     * @covers DDLLogCreate::commitUpdate
     * @covers DDLLogDrop::setHandler
     * @covers DDLLogDrop::commitUpdate
     * @covers DDLLogChange::setHandler
     * @covers DDLLogChange::commitUpdate
     *
     * @test
     */
    public function testSetHandler()
    {

        $result = $this->ddllogsql->commitUpdate();
        $this->assertFalse($result, 'DDLLogSQL::commitUpdate should return False, if no handler is defined');
        $result = $this->ddllogupdate->commitUpdate();
        $this->assertFalse($result, 'DDLLogUpdate::commitUpdate should return False, if no handler is defined');


        $function = create_function('', '');

        // DDL LogUpdate
        DDLLogUpdate::setHandler($function);
        $this->ddllogupdate->commitUpdate();

        // DDL LogSql
         DDLLogSql::setHandler($function);
         $this->ddllogsql->commitUpdate();

        // DDL LogRename
        DDLLogRename::setHandler($function);
        $this->ddllogrename->commitUpdate();

        // DDLLogCreate
        DDLLogCreate::setHandler($function);
        $this->ddllogcreate->commitUpdate();

        // DDLLogDrop
        DDLLogDrop::setHandler($function);
        $this->ddllogdrop->commitUpdate();

        // DDLLogChange
        DDLLogChange::setHandler($function);
        $this->ddllogchange->commitUpdate();

        DDLLogChange::setHandler($function, 'test');
        $this->ddllogchange->setType('test');
        $this->ddllogchange->commitUpdate();
    }

    /**
     * HandlerInvalidArgumentException
     *
     * @covers DDLLogSql::setHandler
     * @expectedException InvalidArgumentException
     *
     * @test
     */
    public function testSetHandlerInvalidArgumentException()
    {
        // DDLLogSql
        DDLLogSql::setHandler('dummy');
    }

    /**
     * HandlerInvalidArgumentException1
     *
     * @covers DDLLogUpdate::setHandler
     * @expectedException InvalidArgumentException
     *
     * @test
     */
    public function testSetHandlerInvalidArgumentException1()
    {
        // DDLLogUpdate
        DDLLogUpdate::setHandler('dummy');
    }

    /**
     * HandlerInvalidArgumentException2
     *
     * @covers DDLLogRename::setHandler
     * @expectedException InvalidArgumentException
     *
     * @test
     */
    public function testSetHandlerInvalidArgumentException2()
    {
        // DDLLogRename
        DDLLogRename::setHandler('dummy');
    }

    /**
     * HandlerInvalidArgumentException3
     *
     * @covers DDLLogCreate::setHandler
     * @expectedException InvalidArgumentException
     *
     * @test
     */
    public function testSetHandlerInvalidArgumentException3()
    {
        // DDLLogCreate
        DDLLogCreate::setHandler('dummy');
    }

    /**
     * HandlerInvalidArgumentException4
     *
     * @covers DDLLogDrop::setHandler
     * @expectedException InvalidArgumentException
     *
     * @test
     */
    public function testSetHandlerInvalidArgumentException4()
    {
        // DDLLogDrop
        DDLLogDrop::setHandler('dummy');
    }

    /**
     * addColumn
     *
     * @covers DDLTable::addColumn
     * @covers DDLTable::isColumn
     * @covers DDLTable::getFileColumns
     * @covers DDLTable::getColumnNames
     * @covers DDLTable::getColumnsByType
     * @covers DDLTable::getColumns
     * @covers DDLTable::getColumn
     * @covers DDLTable::dropColumn
     * @covers DDLTable::setProfile
     * @covers DDLTable::hasProfile
     * @covers DDLTable::hasAuthorLog
     * @covers DDLTable::setAuthorLog
     *
     * @test
     */
    public function testaddColumn()
    {
        // DDLTable
        $newColumns = array('description', 'number', 'image');
        $add = $this->ddltable->addColumn($newColumns[0], 'string');
        $add2 = $this->ddltable->addColumn($newColumns[1], 'integer');
        $this->assertTrue($add instanceof DDLColumn, 'assert failed, the value should be an instance of DDLColumn');

        $result1 = $this->ddltable->getColumn('number');
        $this->assertTrue($result1 instanceof DDLColumn, 'assert failed, the value should be an instace of DDLColumn');
        $result1 = $this->ddltable->getColumn('gibbsganich');
        $this->assertNull($result1, 'DDLTable if you try to get a notexisting column, you should get null as result');

        $result1 = $this->ddltable->getColumnsByType('integer');
        $this->assertEquals(count($result1), 1, 'DDLTable::getColumnsByType does not match');

        $result1 = $this->ddltable->getColumns();
        $this->assertEquals(count($result1), 2, 'DDLTable::getColumns does not match');

        $result1 = $this->ddltable->getColumnNames();
        $this->assertTrue(in_array($newColumns[0],$result1), 'DDLTable::getColumns does not match');
        $this->assertTrue(in_array($newColumns[1],$result1), 'DDLTable::getColumns does not match');

        $add3 = $this->ddltable->addColumn($newColumns[2], 'image');
        $result1 = $this->ddltable->getFileColumns();
        $result2 = array();
        foreach ($result1 as $s)
        {
            $result2[] = $s->getName();
        }
        $this->assertFalse(in_array($newColumns[0],$result2), 'DDLTable::getFileColumns does not match');
        $this->assertTrue(in_array($newColumns[2],$result2), 'DDLTable::getFileColumns does not match');

        $checkprofile = $this->ddltable->hasProfile();
        $this->assertFalse($checkprofile, 'assert failed, the tables doesnt have a profile');

        $set = $this->ddltable->setProfile(true);
        $get = $this->ddltable->getColumns();
        $this->assertArrayHasKey('profile_id', $get, 'assert failed, the "profile_id" should be exist in array');
        $valid2 = $this->ddltable->hasProfile();
        $this->assertTrue($valid2, 'assert failed, the tables allready have a profile');

        $set = $this->ddltable->setProfile(false);
        $get = $this->ddltable->getColumns();
        $this->assertArrayNotHasKey('profile_id', $get, 'assert failed, the "profile_id" should not be exist in array');

        $authorLog = $this->ddltable->hasAuthorLog();
        $this->assertFalse($authorLog, 'assert failed, the tables doesnt have a authorLog');

        $get1 = $this->ddltable->hasAuthorLog();
        $get2 = $this->ddltable->hasAuthorLog(false);
        $this->assertFalse($get1, 'DDLTable::setVersionCheck Versioncheck should be False');
        $this->assertFalse($get2, 'DDLTable::setVersionCheck Versioncheck should be False');

        // check if column time_modified exist - expected false
        $this->ddltable->setAuthorLog(true, false);
        $get1 = $this->ddltable->hasAuthorLog();
        $get2 = $this->ddltable->hasAuthorLog(false);
        $result1 = $this->ddltable->getColumn('user_created');
        $result2 = $this->ddltable->getColumn('user_modified');
        $this->assertNotNull($result1, 'DDLTable::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, 'DDLTable::setVersionCheck time_modified should not be NULL');
        $this->assertFalse($get1, 'DDLTable::setVersionCheck Versioncheck should be False');
        $this->assertTrue($get2, 'DDLTable::setVersionCheck Versioncheck should be False');

        // check if column time_created exist - expected true
        $this->ddltable->setAuthorLog(true, true);
        $get1 = $this->ddltable->hasAuthorLog();
        $get2 = $this->ddltable->hasAuthorLog(false);
        $result1 = $this->ddltable->getColumn('user_created');
        $result2 = $this->ddltable->getColumn('user_modified');
        $this->assertNotNull($result1, 'DDLTable::setVersionCheck time_created should be NULL');
        $this->assertNotNull($result2, 'DDLTable::setVersionCheck time_modified should not be NULL');
        $this->assertTrue($get1, 'DDLTable::setVersionCheck Versioncheck should be False');
        $this->assertTrue($get2, 'DDLTable::setVersionCheck Versioncheck should be False');

        // check if column time_created exist - expected true
        $this->ddltable->setAuthorLog(false, true);
        $result1 = $this->ddltable->getColumn('user_created');
        $result2 = $this->ddltable->getColumn('user_modified');
        $this->assertNotNull($result1, 'DDLTable::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, 'DDLTable::setVersionCheck time_modified should not be NULL');

        // check if column time_created exist - expected true
        $this->ddltable->setAuthorLog(false, false);
        $result1 = $this->ddltable->getColumn('user_created');
        $result2 = $this->ddltable->getColumn('user_modified');
        $this->assertNull($result1, 'DDLTable::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, 'DDLTable::setVersionCheck time_modified should not be NULL');
    }

    /**
     * getSchemaName
     *
     * @covers DDLTable::getSchemaName
     *
     * @test
     */
    public function testGetSchemaName()
    {
        // DDLTable
        $get = $this->ddltable->getSchemaName();
        $this->assertNull($get, 'assert failed, expected null');
    }

    /**
     * VersionCheck
     *
     * @covers DDLTable::hasVersionCheck
     * @covers DDLTable::setVersionCheck
     *
     *
     * @test
     */
    public function testSetVersionCheck()
    {
        // DDLTable

        $get1 = $this->ddltable->hasVersionCheck();
        $get2 = $this->ddltable->hasVersionCheck(false);
        $this->assertFalse($get1, 'DDLTable::setVersionCheck Versioncheck should be False');
        $this->assertFalse($get2, 'DDLTable::setVersionCheck Versioncheck should be False');

        // check if column time_modified exist - expected false
        $this->ddltable->setVersionCheck(true, false);
        $get1 = $this->ddltable->hasVersionCheck();
        $get2 = $this->ddltable->hasVersionCheck(false);
        $result1 = $this->ddltable->getColumn('time_created');
        $result2 = $this->ddltable->getColumn('time_modified');
        $this->assertNotNull($result1, 'DDLTable::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, 'DDLTable::setVersionCheck time_modified should not be NULL');
        $this->assertFalse($get1, 'DDLTable::setVersionCheck Versioncheck should be False');
        $this->assertTrue($get2, 'DDLTable::setVersionCheck Versioncheck should be False');

        // check if column time_created exist - expected true
        $this->ddltable->setVersionCheck(true, true);
        $get1 = $this->ddltable->hasVersionCheck();
        $get2 = $this->ddltable->hasVersionCheck(false);
        $result1 = $this->ddltable->getColumn('time_created');
        $result2 = $this->ddltable->getColumn('time_modified');
        $this->assertNotNull($result1, 'DDLTable::setVersionCheck time_created should be NULL');
        $this->assertNotNull($result2, 'DDLTable::setVersionCheck time_modified should not be NULL');
        $this->assertTrue($get1, 'DDLTable::setVersionCheck Versioncheck should be False');
        $this->assertTrue($get2, 'DDLTable::setVersionCheck Versioncheck should be False');

        // check if column time_created exist - expected true
        $this->ddltable->setVersionCheck(false, true);
        $result1 = $this->ddltable->getColumn('time_created');
        $result2 = $this->ddltable->getColumn('time_modified');
        $this->assertNotNull($result1, 'DDLTable::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, 'DDLTable::setVersionCheck time_modified should not be NULL');

        // check if column time_created exist - expected true
        $this->ddltable->setVersionCheck(false, false);
        $result1 = $this->ddltable->getColumn('time_created');
        $result2 = $this->ddltable->getColumn('time_modified');
        $this->assertNull($result1, 'DDLTable::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, 'DDLTable::setVersionCheck time_modified should not be NULL');
    }

    /**
     * drop non-existing column
     *
     * @covers DDLTable::dropColumn
     * @expectedException NotFoundException
     *
     * @test
     */
    public function testdropColumnNotFoundException()
    {
        // DDLTable
        $this->ddltable->dropColumn('test');
    }

    /**
     * Foreign-key
     *
     * @test
     */
    public function testAddForeignKey()
    {
        // DDLTable
        $ddltable = $this->ddldatabase->addTable('table');
        $ddltable_target = $this->ddldatabase->addTable('table_target');
        $ddltable_target->addColumn('testcolumn_target','integer');
        $ddltable_target->setPrimaryKey('testcolumn_target');
        $ddltable->addColumn('testcolumn','integer');
        $fk = $ddltable->addForeignKey('table_target', 'cfkey');
        $fk->setColumn('testcolumn');
        $getAll = $ddltable->getForeignKeys();
        $this->assertType('array', $getAll, 'assert failed the values is not an array');

        foreach($getAll as $key =>$value)
        {
            $this->assertTrue($value instanceof DDLForeignKey, 'assert failed, the value should be an instance of DDLForeignKey');
        }

        $cfkey = $ddltable->getForeignKey('cfkey');

        $this->assertNotNull($cfkey, 'ForeignKey was not retrieved by constraint Name');
    }

    /**
     * Primary-key
     *
     * @test
     * @expectedException PHPUnit_Framework_Error
     */
    public function testPrimaryKey()
    {
        $get = $this->ddltable->getPrimaryKey();
    }

    /**
     * Primary-key with non-existing column
     *
     * @covers DDLTable::setPrimaryKey
     * @expectedException NotFoundException
     *
     * @test
     */
    public function testSetPrimaryKeyNotFoundException()
    {
        // DDLTable
        $this->ddltable->setPrimaryKey('no_column');
    }

    /**
     * Inheritance
     *
     * @covers DDLTable::getInheritance
     * @covers DDLTable::setInheritance
     *
     * @test
     */
    public function testInheritance()
    {
        // DDLTable
        $this->ddltable->setInheritance('inheritance');
        $get = $this->ddltable->getInheritance();
        $this->assertEquals('inheritance', $get, 'assert failed, the values should be equal');
        $this->ddltable->setInheritance('');
        $get = $this->ddltable->getInheritance();
        $this->assertNull($get, 'assert failed, expected null');
    }

    /**
     * addIndex
     *
     * @covers DDLTable::getIndexes
     * @covers DDLTable::getIndex
     * @covers DDLTable::addIndex
     * @covers DDLTable::setIndex
     *
     * @test
     */
    public function testAddIndex()
    {
        $this->ddltable->addIndex('test');
        $index = $this->ddltable->getIndex('test');
        $this->assertTrue($index instanceof DDLIndex, 'Method getIndex() should return DDLIndex objects.');
        $index = $this->ddltable->getIndex('non-existing-index');
        $this->assertNull($index, 'Search for non-existing index must return NULL.');

        $this->ddltable->addIndex('othertest');
        $index = $this->ddltable->getIndex('othertest');
        $this->assertTrue($index instanceof DDLIndex, 'Method getIndex() should return DDLIndex objects.');

        // add two more anonymous indexes
        $this->ddltable->addIndex();
        $this->ddltable->addIndex();

        $indexes = $this->ddltable->getIndexes();
        $this->assertArrayHasKey('test', $indexes, 'Expected index "test" not found.');
        $this->assertArrayHasKey('othertest', $indexes, 'Expected index "othertest" not found.');
        $this->assertArrayHasKey(0, $indexes, 'Anonymous index "0" not found.');
        $this->assertArrayHasKey(1, $indexes, 'Anonymous index "1" not found.');
        $this->assertEquals(4, count($indexes), 'Unexpected number of indexes.');
    }

    /**
     * IndexAlreadyExistsException
     * @test
     *
     * @covers  DDLTable
     * @expectedException AlreadyExistsException
     */
    public function testAddIndexAlreadyExistsException()
    {
        $this->ddltable->addColumn('column', 'string');
        try {
            // supposed to succeed
            $this->ddltable->addIndex('column', 'index');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->ddltable->addIndex('column', 'index');
    }

    /**
     * ColumnAlreadyExistsException
     * @test
     *
     * @covers  DDLTable
     * @expectedException AlreadyExistsException
     */
    public function testAddColumnAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->ddltable->addColumn('column', 'string');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->ddltable->addColumn('column', 'string');
    }

    /**
     * Trigger
     *
     * @covers DDLTable::getTriggerBeforeInsert
     * @covers DDLTable::getTriggerBeforeUpdate
     * @covers DDLTable::getTriggerBeforeDelete
     * @covers DDLTable::getTriggerAfterInsert
     * @covers DDLTable::getTriggerAfterUpdate
     * @covers DDLTable::getTriggerAfterDelete
     * @covers DDLTable::getTriggerInsteadInsert
     * @covers DDLTable::getTriggerInsteadUpdate
     * @covers DDLTable::getTriggerInsteadDelete
     * @covers DDLTable::setTriggerBeforeInsert
     * @covers DDLTable::setTriggerBeforeUpdate
     * @covers DDLTable::setTriggerBeforeDelete
     * @covers DDLTable::setTriggerAfterInsert
     * @covers DDLTable::setTriggerAfterUpdate
     * @covers DDLTable::setTriggerAfterDelete
     * @covers DDLTable::setTriggerInsteadInsert
     * @covers DDLTable::setTriggerInsteadUpdate
     * @covers DDLTable::setTriggerInsteadDelete
     * @covers DDLTrigger::getTriggerBeforeInsert
     * @covers DDLTrigger::setTriggerBeforeInsert
     * @covers DDLTrigger::getTriggerBeforeUpdate
     * @covers DDLTrigger::setTriggerBeforeUpdate
     * @covers DDLTrigger::getTriggerBeforeDelete
     * @covers DDLTrigger::setTriggerBeforeDelete
     * @covers DDLTrigger::getTriggerAfterInsert
     * @covers DDLTrigger::setTriggerAfterInsert
     * @covers DDLTrigger::getTriggerAfterUpdate
     * @covers DDLTrigger::setTriggerAfterUpdate
     * @covers DDLTrigger::getTriggerAfterDelete
     * @covers DDLTrigger::setTriggerAfterDelete
     *
     * @test
     */
    public function testTrigger()
    {

        $testArray1 = array("sometrigger 1", "sometrigger 2", "sometrigger 3");
        // DDLTable

        // DDLTable::setTriggerBeforeInsert
        $trigger = $this->ddltable->setTriggerBeforeInsert($testArray1[0]);
        $this->assertTrue($trigger->isBefore(), "DDLTrigger::isBefore returns wrong value");
        $this->assertFalse($trigger->isAfter(), "DDLTrigger::isAfter returns wrong value");
        $this->assertFalse($trigger->isInstead(), "DDLTrigger::isInstead returns wrong value");
        $this->assertTrue($trigger->isInsert(), "DDLTrigger::isInsert returns wrong value");
        $this->assertFalse($trigger->isUpdate(), "DDLTrigger::isUpdate returns wrong value");
        $this->assertFalse($trigger->isDelete(), "DDLTrigger::isDelete returns wrong value");
        $this->ddltable->setTriggerBeforeInsert($testArray1[1]);
        $this->ddltable->setTriggerBeforeInsert($testArray1[2]);
        $get = $this->ddltable->getTriggerBeforeInsert();
        $this->assertEquals($get, $testArray1[0], 'DDLTable::setTriggerBeforeInsert, the arrays should be equal');
        $get = $this->ddltable->getTriggerBeforeInsert('mysql');
        $this->assertNull($get, 'DDLTable::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->ddltable);
        $this->ddltable = new DDLTable('table');

        // DDLTable::setTriggerBeforeUpdate
        $this->ddltable->setTriggerBeforeUpdate($testArray1[0]);
        $trigger = $this->ddltable->setTriggerBeforeUpdate($testArray1[1]);
        $this->ddltable->setTriggerBeforeUpdate($testArray1[2]);
        $get = $this->ddltable->getTriggerBeforeUpdate();
        $this->assertEquals($get, $testArray1[0], 'DDLTable::setTriggerBeforeUpdate, the arrays should be equal');
        $get = $this->ddltable->getTriggerBeforeUpdate('mysql');
        $this->assertNull($get, 'DDLTable::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->ddltable);
        $this->ddltable = new DDLTable('table');

        // DDLTable::setTriggerBeforeDelete
        $this->ddltable->setTriggerBeforeDelete($testArray1[0]);
        $this->ddltable->setTriggerBeforeDelete($testArray1[1]);
        $this->ddltable->setTriggerBeforeDelete($testArray1[2]);
        $get = $this->ddltable->getTriggerBeforeDelete();
        $this->assertEquals($get, $testArray1[0], 'DDLTable::setTriggerBeforeDelete, the arrays should be equal');
        $get = $this->ddltable->getTriggerBeforeDelete('mysql');
        $this->assertNull($get, 'DDLTable::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->ddltable);
        $this->ddltable = new DDLTable('table');

        // DDLTable::setTriggerAfterInsert
        $this->ddltable->setTriggerAfterInsert($testArray1[0]);
        $this->ddltable->setTriggerAfterInsert($testArray1[1]);
        $this->ddltable->setTriggerAfterInsert($testArray1[2]);
        $get = $this->ddltable->getTriggerAfterInsert();
        $this->assertEquals($get, $testArray1[0], 'DDLTable::setTriggerAfterInsert, the arrays should be equal');
        $get = $this->ddltable->getTriggerAfterInsert('mysql');
        $this->assertNull($get, 'DDLTable::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->ddltable);
        $this->ddltable = new DDLTable('table');

        // DDLTable::setTriggerAfterUpdate
        $this->ddltable->setTriggerAfterUpdate($testArray1[0]);
        $trigger = $this->ddltable->setTriggerAfterUpdate($testArray1[1]);
        $this->assertFalse($trigger->isBefore(), "DDLTrigger::isBefore returns wrong value");
        $this->assertTrue($trigger->isAfter(), "DDLTrigger::isAfter returns wrong value");
        $this->assertFalse($trigger->isInstead(), "DDLTrigger::isInstead returns wrong value");
        $this->assertFalse($trigger->isInsert(), "DDLTrigger::isInsert returns wrong value");
        $this->assertTrue($trigger->isUpdate(), "DDLTrigger::isUpdate returns wrong value");
        $this->assertFalse($trigger->isDelete(), "DDLTrigger::isDelete returns wrong value");
        $this->ddltable->setTriggerAfterUpdate($testArray1[2]);
        $get = $this->ddltable->getTriggerAfterUpdate();
        $this->assertEquals($get, $testArray1[0], 'DDLTable::setTriggerAfterUpdate, the arrays should be equal');
        $get = $this->ddltable->getTriggerAfterUpdate('mysql');
        $this->assertNull($get, 'DDLTable::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->ddltable);
        $this->ddltable = new DDLTable('table');

        // DDLTable::setTriggerAfterDelete
        $this->ddltable->setTriggerAfterDelete($testArray1[0]);
        $this->ddltable->setTriggerAfterDelete($testArray1[1]);
        $this->ddltable->setTriggerAfterDelete($testArray1[2]);
        $get = $this->ddltable->getTriggerAfterDelete();
        $this->assertEquals($get, $testArray1[0], 'DDLTable::setTriggerAfterDelete, the arrays should be equal');
        $get = $this->ddltable->getTriggerAfterDelete('mysql');
        $this->assertNull($get, 'DDLTable::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->ddltable);
        $this->ddltable = new DDLTable('table');

        // DDLTable::setTriggerInsteadInsert
        $this->ddltable->setTriggerInsteadInsert($testArray1[0]);
        $this->ddltable->setTriggerInsteadInsert($testArray1[1]);
        $this->ddltable->setTriggerInsteadInsert($testArray1[2]);
        $get = $this->ddltable->getTriggerInsteadInsert();
        $this->assertEquals($get, $testArray1[0], 'DDLTable::setTriggerInsteadInsert, the arrays should be equal');
        $get = $this->ddltable->getTriggerInsteadInsert('mysql');
        $this->assertNull($get, 'DDLTable::setTriggerInsteadInsert, expected null - trigger "mysql" does not exist');
        unset ($this->ddltable);
        $this->ddltable = new DDLTable('table');

        // DDLTable::setTriggerInsteadUpdate
        $this->ddltable->setTriggerInsteadUpdate($testArray1[0]);
        $this->ddltable->setTriggerInsteadUpdate($testArray1[1]);
        $this->ddltable->setTriggerInsteadUpdate($testArray1[2]);
        $get = $this->ddltable->getTriggerInsteadUpdate();
        $this->assertEquals($get, $testArray1[0], 'DDLTable::setTriggerInsteadUpdate, the arrays should be equal');
        $get = $this->ddltable->getTriggerInsteadUpdate('mysql');
        $this->assertNull($get, 'DDLTable::setTriggerInsteadUpdate, expected null - trigger "mysql" does not exist');
        unset ($this->ddltable);
        $this->ddltable = new DDLTable('table');

        // DDLTable::setTriggerInsteadDelete
        $this->ddltable->setTriggerInsteadDelete($testArray1[0]);
        $this->ddltable->setTriggerInsteadDelete($testArray1[1]);
        $this->ddltable->setTriggerInsteadDelete($testArray1[2]);
        $get = $this->ddltable->getTriggerInsteadDelete();
        $this->assertEquals($get, $testArray1[0], 'DDLTable::setTriggerInsteadDelete, the arrays should be equal');
        $get = $this->ddltable->getTriggerInsteadDelete('mysql');
        $this->assertNull($get, 'DDLTable::setTriggerInsteadDelete, expected null - trigger "mysql" does not exist');
        unset ($this->ddltable);
        $this->ddltable = new DDLTable('table');


        // set the same with name

//        // DDLTable::setTriggerBeforeInsert
//        $this->ddltable->setTriggerBeforeInsert($testArray1[0], 'generic', 'test1');
//        $this->ddltable->setTriggerBeforeInsert($testArray1[1], 'generic', 'test2');
//        $this->ddltable->setTriggerBeforeInsert($testArray1[2], 'generic', 'test3');
//        $get = $this->ddltable->getTriggerBeforeInsert();
//        $this->assertEquals('sometrigger 1', $testArray1[0], 'DDLTable::setTriggerBeforeInsert, the value "sometrigger 1" must be in array');
//        $this->assertEquals('sometrigger 2', $testArray1[1], 'DDLTable::setTriggerBeforeInsert, the value "sometrigger 2" must be in array');
//        $this->assertEquals('sometrigger 3', $testArray1[2], 'DDLTable::setTriggerBeforeInsert , the value "sometrigger 3" must be in array');
//        unset ($this->ddltable);
//        $this->ddltable = new DDLTable('table');
//
//        // DDLTable::setTriggerBeforeUpdate
//        $this->ddltable->setTriggerBeforeUpdate($testArray1[0], 'generic', 'test1');
//        $this->ddltable->setTriggerBeforeUpdate($testArray1[1], 'generic', 'test2');
//        $this->ddltable->setTriggerBeforeUpdate($testArray1[2], 'generic', 'test3');
//        $get = $this->ddltable->getTriggerBeforeUpdate();
//        $this->assertEquals('sometrigger 1', $testArray1[0], 'DDLTable::setTriggerBeforeUpdate, the value "sometrigger 1" must be in array');
//        $this->assertEquals('sometrigger 2', $testArray1[1], 'DDLTable::setTriggerBeforeUpdate, the value "sometrigger 2" must be in array');
//        $this->assertEquals('sometrigger 3', $testArray1[2], 'DDLTable::setTriggerBeforeUpdate, the value "sometrigger 3" must be in array');
//        unset ($this->ddltable);
//        $this->ddltable = new DDLTable('table');
//
//        // DDLTable::setTriggerBeforeDelete
//        $this->ddltable->setTriggerBeforeDelete($testArray1[0], 'generic', 'test1');
//        $this->ddltable->setTriggerBeforeDelete($testArray1[1], 'generic', 'test2');
//        $this->ddltable->setTriggerBeforeDelete($testArray1[2], 'generic', 'test3');
//        $get = $this->ddltable->getTriggerBeforeDelete();
//        $this->assertEquals('sometrigger 1', $testArray1[0], 'DDLTable::setTriggerBeforeDelete, the value "sometrigger 1" must be in array');
//        $this->assertEquals('sometrigger 2', $testArray1[1], 'DDLTable::setTriggerBeforeDelete, the value "sometrigger 2" must be in array');
//        $this->assertEquals('sometrigger 3', $testArray1[2], 'DDLTable::setTriggerBeforeDelete, the value "sometrigger 3" must be in array');
//        unset ($this->ddltable);
//        $this->ddltable = new DDLTable('table');
//
//        // DDLTable::setTriggerAfterInsert
//        $this->ddltable->setTriggerAfterInsert($testArray1[0], 'generic', 'test1');
//        $this->ddltable->setTriggerAfterInsert($testArray1[1], 'generic', 'test2');
//        $this->ddltable->setTriggerAfterInsert($testArray1[2], 'generic', 'test3');
//        $get = $this->ddltable->getTriggerAfterInsert();
//        $this->assertEquals('sometrigger 1', $testArray1[0], 'DDLTable::setTriggerAfterInsert, the value "sometrigger 1" must be in array');
//        $this->assertEquals('sometrigger 2', $testArray1[1], 'DDLTable::setTriggerAfterInsert, the value "sometrigger 2" must be in array');
//        $this->assertEquals('sometrigger 3', $testArray1[2], 'DDLTable::setTriggerAfterInsert, the value "sometrigger 3" must be in array');
//        unset ($this->ddltable);
//        $this->ddltable = new DDLTable('table');
//
//        // DDLTable::setTriggerAfterUpdate
//        $this->ddltable->setTriggerAfterUpdate($testArray1[0], 'generic', 'test1');
//        $this->ddltable->setTriggerAfterUpdate($testArray1[1], 'generic', 'test2');
//        $this->ddltable->setTriggerAfterUpdate($testArray1[2], 'generic', 'test3');
//        $get = $this->ddltable->getTriggerAfterUpdate();
//        $this->assertEquals('sometrigger 1', $testArray1[0], 'DDLTable::setTriggerAfterUpdate, the value "sometrigger 1" must be in array');
//        $this->assertEquals('sometrigger 2', $testArray1[1], 'DDLTable::setTriggerAfterUpdate, the value "sometrigger 2" must be in array');
//        $this->assertEquals('sometrigger 3', $testArray1[2], 'DDLTable::setTriggerAfterUpdate, the value "sometrigger 3" must be in array');
//        unset ($this->ddltable);
//        $this->ddltable = new DDLTable('table');
//
//        // DDLTable::setTriggerAfterDelete
//        $this->ddltable->setTriggerAfterDelete($testArray1[0], 'generic', 'test1');
//        $this->ddltable->setTriggerAfterDelete($testArray1[1], 'generic', 'test2');
//        $this->ddltable->setTriggerAfterDelete($testArray1[2], 'generic', 'test3');
//        $get = $this->ddltable->getTriggerAfterDelete();
//        $this->assertEquals('sometrigger 1', $testArray1[0], 'DDLTable::setTriggerAfterDelete, the value "sometrigger 1" must be in array');
//        $this->assertEquals('sometrigger 2', $testArray1[1], 'DDLTable::setTriggerAfterDelete, the value "sometrigger 2" must be in array');
//        $this->assertEquals('sometrigger 3', $testArray1[2], 'DDLTable::setTriggerAfterDelete, the value "sometrigger 3" must be in array');
//
//         // DDLTrigger::setTriggerBeforeInsert
//        $this->ddltrigger->setTriggerBeforeInsert($testArray1[0]);
//        $get = $this->ddltrigger->getTriggerBeforeInsert();
//        $this->assertEquals($testArray1[0], $get, 'DDLTrigger::setTriggerBeforeInsert, the values must be equal');
//
//        $this->ddltrigger->setTriggerBeforeInsert();
//        $get = $this->ddltrigger->getTriggerBeforeInsert();
//        $this->assertNull($get, 'DDLTrigger::setTriggerBeforeInsert, expected null - trigger is not set');
//
//        // DDLTrigger::setTriggerBeforeUpdate
//        $this->ddltrigger->setTriggerBeforeUpdate($testArray1[0], 'generic', 'test1');
//        $get = $this->ddltrigger->getTriggerBeforeUpdate();
//        $this->assertEquals($testArray1[0], $get, 'DDLTrigger::setTriggerBeforeUpdate the values must be equal');
//
//        $this->ddltrigger->setTriggerBeforeUpdate();
//        $get = $this->ddltrigger->getTriggerBeforeUpdate();
//        $this->assertNull($get, 'DDLTrigger::setTriggerBeforeUpdate, expected null - trigger is not set');
//
//        // DDLTrigger::setTriggerBeforeDelete
//        $this->ddltrigger->setTriggerBeforeDelete($testArray1[0], 'generic', 'test1');
//        $get = $this->ddltrigger->getTriggerBeforeDelete();
//        $this->assertEquals($testArray1[0], $get, 'DDLTrigger::setTriggerBeforeDelete the values must be equal');
//
//        $this->ddltrigger->setTriggerBeforeDelete();
//        $get = $this->ddltrigger->getTriggerBeforeDelete();
//        $this->assertNull($get, 'DDLTrigger::setTriggerBeforeDelete, expected null - trigger is not set');
//
//        // DDLTrigger::setTriggerAfterInsert
//        $this->ddltrigger->setTriggerAfterInsert($testArray1[0], 'generic', 'test1');
//        $get = $this->ddltrigger->getTriggerAfterInsert();
//        $this->assertEquals($testArray1[0], $get, 'DDLTrigger::setTriggerAfterInsert the values must be equal');
//
//        $this->ddltrigger->setTriggerAfterInsert();
//        $get = $this->ddltrigger->getTriggerAfterInsert();
//        $this->assertNull($get, 'DDLTrigger::setTriggerAfterInsert, expected null - trigger is not set');
//
//        // DDLTrigger::setTriggerAfterUpdate
//        $this->ddltrigger->setTriggerAfterUpdate($testArray1[0], 'generic', 'test1');
//        $get = $this->ddltrigger->getTriggerAfterUpdate();
//        $this->assertEquals($testArray1[0], $get, 'DDLTrigger::setTriggerAfterUpdate the values must be equal');
//
//        $this->ddltrigger->setTriggerAfterUpdate();
//        $get = $this->ddltrigger->getTriggerAfterUpdate();
//        $this->assertNull($get, 'DDLTrigger::setTriggerAfterUpdate, expected null - trigger is not set');
//
//        // DDLTrigger::setTriggerAfterDelete
//        $this->ddltrigger->setTriggerAfterDelete($testArray1[0], 'generic', 'test1');
//        $get = $this->ddltrigger->getTriggerAfterDelete();
//        $this->assertEquals($testArray1[0], $get, 'DDLTrigger::setTriggerAfterDelete the values must be equal');
//
//        $this->ddltrigger->setTriggerAfterDelete();
//        $get = $this->ddltrigger->getTriggerAfterDelete();
//        $this->assertNull($get, 'DDLTrigger::setTriggerAfterDelete, expected null - trigger is not set');
    }

    /**
     * Alias
     *
     * @covers DDLViewField::getAlias
     * @covers DDLViewField::setAlias
     *
     * @test
     */
    public function testSetAlias()
    {
        // DDLViewField
        $this->ddlviewfield->setAlias('abcd');
        $result = $this->ddlviewfield->getAlias();
        $this->assertEquals('abcd', $result, 'assert failed, DDLViewField : alias is not set, values should be equal');

        $this->ddlviewfield->setAlias('');
        $result = $this->ddlviewfield->getAlias();
        $this->assertNull($result, 'assert failed, DDLViewField : expected null - alis is not set');
    }

    /**
     * ChangeLog
     *
     * @covers DDLDatabase::getChangeLog
     *
     * @test
     */
    public function testChangeLog()
    {
        $result = $this->ddldatabase->getChangeLog();
        $this->assertTrue($result instanceof DDLChangeLog, 'assert failed, DDLDatabase : the value should be an instance of DDLChangeLog');
    }

    /**
     * Css class
     *
     * @covers DDLField::getCssClass
     * @covers DDLField::setCssClass
     *
     * @test
     */
    public function testCssClass()
    {
        // DDLField
        $this->ddlfield->setCssClass('cssclass');
        $result = $this->ddlfield->getCssClass();
        $this->assertEquals('cssclass', $result , 'assert failed, DDLField : the value "cssclass" should be equal with the expecting value');

        $this->ddlfield->setCssClass('');
        $result = $this->ddlfield->getCssClass();
        $this->assertNull($result, 'assert failed, DDLField : expected null - cssclass is not set');
    }

    /**
     * TabIndex
     *
     * @covers DDLField::getTabIndex
     * @covers DDLField::setTabIndex
     *
     * @test
     */
    public function testTabIndex()
    {
        // DDLField
        $this->ddlfield->setTabIndex(4);
        $result = $this->ddlfield->getTabIndex();
        $this->assertEquals(4, $result , 'assert failed, DDLField : the value "4" should be the same as the expected value');

        $this->ddlfield->setTabIndex();
        $result = $this->ddlfield->getTabIndex();
        $this->assertNull($result, 'assert failed, DDLField : expected null - tabIndex is not set');
    }

    /**
     * Action
     *
     * @covers DDLEvent::getAction
     * @covers DDLEvent::setAction
     *
     * @test
     */
    public function testAction()
    {
        // DDLEvent
        $this->ddlevent->setAction('action');
        $result = $this->ddlevent->getAction();
        $this->assertEquals('action', $result , 'assert failed, DDLEvent : the value "action" should be the same as the expected value');

        $this->ddlevent->setAction();
        $result = $this->ddlevent->getAction();
        $this->assertNull($result, 'assert failed, DDLEvent : expected null - action is not set');
    }

    /**
     * Language
     *
     * @covers DDLEvent::getLanguage
     * @covers DDLEvent::setLanguage
     *
     * @test
     */
    public function testLanguageFormAction()
    {
        // DDLEvent
        $this->ddlevent->setLanguage('language');
        $result = $this->ddlevent->getLanguage();
        $this->assertEquals('language', $result , 'assert failed, DDLEvent : the value "language" should be the same as the expected value');

        $this->ddlevent->setLanguage();
        $result = $this->ddlevent->getLanguage();
        $this->assertNull($result, 'assert failed, DDLEvent : expected null - language is not set');
    }

    /**
     * Label
     *
     * @covers DDLEvent::getLabel
     * @covers DDLEvent::setLabel
     *
     * @test
     */
    public function testLabelFormAction()
    {
        // DDLEvent
        $this->ddlevent->setLabel('label');
        $result = $this->ddlevent->getLabel();
        $this->assertEquals('label', $result , 'assert failed, DDLEvent :the value "label" should be the same as the expected value');

        $this->ddlevent->setLabel();
        $result = $this->ddlevent->getLabel();
        $this->assertNull($result, 'assert failed, DDLEvent : expected null - label is not set');
    }

    /**
     * Icon
     *
     * @covers DDLEvent::getIcon
     * @covers DDLEvent::setIcon
     *
     * @test
     */
    public function testIcon()
    {
        // DDLEvent
        $icon = CWD.'resources/image/logo.png';
        $this->ddlevent->setIcon($icon);
        $get = $this->ddlevent->getIcon();
        $this->assertType('string', $get, 'assert failed, "DDLEvent:getIcon" the value should be from type string');
        $this->assertEquals($icon, $get, 'assert failed, "DDLEvent:getIcon" the values should be equal - expected the same path to a file');

        $this->ddlevent->setIcon('');
        $result = $this->ddlevent->getIcon();
        $this->assertNull($result, 'assert failed, DDLEvent : expected null - icon is not set');
    }

    /**
     * getTableByForeignKey
     *
     * @covers DDLTable::getTableByForeignKey
     *
     * @test
     */
    public function testGetTableByForeignKey()
    {
        // DDLTable

        // create a target-table
        $newTableA = $this->ddldatabase->addTable("someTable");
        $newTableB = $this->ddldatabase->addTable("otherTable");
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
     * @covers DDLTable::getTableByForeignKey
     * @expectedException NotFoundException
     *
     * @test
     */
    public function testGetTableByForeignKeyInvalidArgumentException()
    {
        // DDLTable
        $this->ddltable->getTableByForeignKey('nonexist');
    }

    /**
     * hasAllInput
     *
     * @covers DDLForm
     *
     * @test
     */
    public function testHasAllInput()
    {
        $this->assertFalse($this->ddlform->hasAllInput(), 'Setting "allinput" must default to false.');
        $this->ddlform->setAllInput(true);
        $this->assertTrue($this->ddlform->hasAllInput(), 'Setting "allinput" should allow value true.');
        $this->ddlform->setAllInput(false);
        $this->assertFalse($this->ddlform->hasAllInput(), 'Setting "allinput" should be reversible.');
    }

    /**
     * Old property value
     *
     * @covers DDLForm::dropField
     *
     * @test
     */
    public function testdropField()
    {
        $this->ddlform->addField('foo');
        $get = $this->ddlform->getField('foo');
        $get = $this->ddlform->dropField('foo');
        $this->assertNull($get, 'assert failed, field is not droped"');
    }

     /**
     * drop Field InvalidArgumentException
     *
     * @covers DDLForm::dropField
     * @expectedException NotFoundException
     *
     * @test
     */
    public function testdropFieldInvalidArgumentException()
    {
        $this->ddlform->dropField('non-existing-field');
    }

     /**
     * getColumnByForeignKey InvalidArgumentException
     *
     * @covers DDLTable::getColumnByForeignKey
     * @expectedException NotFoundException
     *
     * @test
     */
    public function testgetColumnByForeignKeyInvalidArgumentException()
    {
        // DDLTable
        $this->ddltable->getColumnByForeignKey('foo_bar');
    }

     /**
     * getColumnByForeignKey
     *
     * @covers DDLTable::getColumnByForeignKey
     *
     * @test
     */
    public function testgetColumnByForeignKey()
    {
         // create a da tabase with tables (columns)
        $db = new DDLDatabase('foobar');

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

        // DDLTable
        $result = $table->getColumnByForeignKey('foo_department_id');
        $this->assertTrue($result instanceof DDLColumn, 'assert failed, the expected value should be an instance of DDLColumn');
    }
}
?>