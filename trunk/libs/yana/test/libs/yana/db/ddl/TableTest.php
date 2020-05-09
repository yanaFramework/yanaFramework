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
class TableTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Table
     */
    protected $object;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Table('table');
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
     * @test
     */
    public function testReadonly()
    {
       $this->object->setReadonly(true);
       $result = $this->object->isReadonly();
       $this->assertTrue($result, 'assert failed, \Yana\Db\Ddl\Table : expected true - setReadonly was set with true');

       $this->object->setReadonly(false);
       $result = $this->object->isReadonly();
       $this->assertFalse($result, 'assert failed, \Yana\Db\Ddl\Table : expected false - setReadonly was set with false');
    }

    /**
     * @test
     */
    public function testConstraint()
    {
        // DDL Table
        $testArray1 = array("someConstraints 1", "someConstraints 2", "someConstraints 3");
        $this->object->addConstraint($testArray1[0]);
        $this->object->addConstraint($testArray1[1]);
        $this->object->addConstraint($testArray1[2]);
        $result1 = $this->object->getConstraints();

        $this->assertEquals($result1[0]->getConstraint(), $testArray1[0], '\Yana\Db\Ddl\Table::getConstraints failed, both arrays should be equal');

        $testArray2 = array("someMoreConstraints 1", "someMoreConstraints 2", "someMoreConstraints 3");
        $this->object->addConstraint($testArray2[0], "", "mysql");
        $this->object->addConstraint($testArray2[1], "", "mysql");
        $this->object->addConstraint($testArray2[2], "", "mysql");
        $result1 = $this->object->getConstraints("mysql");
        $this->assertEquals($result1[1]->getConstraint(), $testArray2[1], '\Yana\Db\Ddl\Table::getConstraints failed, both arrays should be equal');
        //$this->table->dropConstraints();
        $this->object->addConstraint("someDifferentConstraints 1", "name", "mysql");
        $this->object->addConstraint("someDifferentConstraints 2", "name", "mysql");
        $result1 = $this->object->getConstraint("name", "mysql");
        $this->assertEquals($result1->getConstraint(), "someDifferentConstraints 1", '\Yana\Db\Ddl\Table::getConstraints failed');

        $result1 = $this->object->getConstraint("name2", "mysql");
        $this->assertNull($result1, '\Yana\Db\Ddl\Table::getConstraints failed');

        $get = $this->object->getConstraints("oracle");
        $this->assertEquals(array(), $get, '\Yana\Db\Ddl\Table::getConstraints - "oracle" doesnt exist in array');

        $this->object->dropConstraints();
        $get = $this->object->getConstraints();
        $this->assertEquals(array(), $get, '\Yana\Db\Ddl\Table::getConstraints list should be empty after droping constraints.');

        // get Unique-Constraints
        $result1 = $this->object->getUniqueConstraints();
        $uniqueCol = $this->object->addColumn('unique','integer');
        $uniqueCol->setUnique();
        $result2 = $this->object->getUniqueConstraints();
        $this->assertInternalType('array',$result1, '\Yana\Db\Ddl\Table::');
        $this->assertInternalType('array',$result2, '\Yana\Db\Ddl\Table::');
        $this->assertTrue(empty($result1), '\Yana\Db\Ddl\Table::');
        $this->assertFalse(empty($result2), '\Yana\Db\Ddl\Table::');
    }

    /**
     * @test
     */
    public function testGrants()
    {
        $grant = new \Yana\Db\Ddl\Grant();
        $grant2 = new \Yana\Db\Ddl\Grant();

        $grants = array($grant, $grant2);

        $this->object->setGrant($grant);
        $this->object->setGrant($grant2);

        $this->assertEquals($grants, $this->object->getGrants(), 'assert failed, the values should be equal, expected the same arrays');

        $add = $this->object->addGrant('user', 'role', 10);
        $this->assertTrue($add instanceof \Yana\Db\Ddl\Grant, 'Function addGrant() should return instance of \Yana\Db\Ddl\Grant.');

        $this->object->dropGrants();

        $get = $this->object->getGrants();
        $this->assertEquals(array(), $get, 'Function getGrants() should return an empty array after calling dropGrants().');
    }

    /**
     * @test
     */
    public function testGetParent()
    {
        $database = new \Yana\Db\Ddl\Database();
        $childTable = new \Yana\Db\Ddl\Table('table', $database);
        $parentTable = $childTable->getParent();
        $this->assertEquals($database, $parentTable, '\Yana\Db\Ddl\Table::getParent, the values should be equal');
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
        $add = $this->object->addColumn($newColumns[0], 'string');
        $add2 = $this->object->addColumn($newColumns[1], 'integer');
        $this->assertTrue($add instanceof \Yana\Db\Ddl\Column, 'assert failed, the value should be an instance of \Yana\Db\Ddl\Column');

        $result1 = $this->object->getColumn('number');
        $this->assertTrue($result1 instanceof \Yana\Db\Ddl\Column, 'assert failed, the value should be an instace of \Yana\Db\Ddl\Column');
        $result1 = $this->object->getColumn('gibbsganich');
        $this->assertNull($result1, '\Yana\Db\Ddl\Table if you try to get a notexisting column, you should get null as result');

        $result1 = $this->object->getColumnsByType('integer');
        $this->assertEquals(count($result1), 1, '\Yana\Db\Ddl\Table::getColumnsByType does not match');

        $result1 = $this->object->getColumns();
        $this->assertEquals(count($result1), 2, '\Yana\Db\Ddl\Table::getColumns does not match');

        $result1 = $this->object->getColumnNames();
        $this->assertTrue(in_array($newColumns[0],$result1), '\Yana\Db\Ddl\Table::getColumns does not match');
        $this->assertTrue(in_array($newColumns[1],$result1), '\Yana\Db\Ddl\Table::getColumns does not match');

        $add3 = $this->object->addColumn($newColumns[2], 'image');
        $result1 = $this->object->getFileColumns();
        $result2 = array();
        foreach ($result1 as $s)
        {
            $result2[] = $s->getName();
        }
        $this->assertFalse(in_array($newColumns[0],$result2), '\Yana\Db\Ddl\Table::getFileColumns does not match');
        $this->assertTrue(in_array($newColumns[2],$result2), '\Yana\Db\Ddl\Table::getFileColumns does not match');

        $checkprofile = $this->object->hasProfile();
        $this->assertFalse($checkprofile, 'assert failed, the tables doesnt have a profile');

        $set = $this->object->setProfile(true);
        $get = $this->object->getColumns();
        $this->assertArrayHasKey('profile_id', $get, 'assert failed, the "profile_id" should be exist in array');
        $valid2 = $this->object->hasProfile();
        $this->assertTrue($valid2, 'assert failed, the tables allready have a profile');

        $set = $this->object->setProfile(false);
        $get = $this->object->getColumns();
        $this->assertArrayNotHasKey('profile_id', $get, 'assert failed, the "profile_id" should not be exist in array');

        $authorLog = $this->object->hasAuthorLog();
        $this->assertFalse($authorLog, 'assert failed, the tables doesnt have a authorLog');

        $get1 = $this->object->hasAuthorLog();
        $get2 = $this->object->hasAuthorLog(false);
        $this->assertFalse($get1, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');
        $this->assertFalse($get2, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');

        // check if column time_modified exist - expected false
        $this->object->setAuthorLog(true, false);
        $get1 = $this->object->hasAuthorLog();
        $get2 = $this->object->hasAuthorLog(false);
        $result1 = $this->object->getColumn('user_created');
        $result2 = $this->object->getColumn('user_modified');
        $this->assertNotNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');
        $this->assertFalse($get1, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');
        $this->assertTrue($get2, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');

        // check if column time_created exist - expected true
        $this->object->setAuthorLog(true, true);
        $get1 = $this->object->hasAuthorLog();
        $get2 = $this->object->hasAuthorLog(false);
        $result1 = $this->object->getColumn('user_created');
        $result2 = $this->object->getColumn('user_modified');
        $this->assertNotNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNotNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');
        $this->assertTrue($get1, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');
        $this->assertTrue($get2, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');

        // check if column time_created exist - expected true
        $this->object->setAuthorLog(false, true);
        $result1 = $this->object->getColumn('user_created');
        $result2 = $this->object->getColumn('user_modified');
        $this->assertNotNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');

        // check if column time_created exist - expected true
        $this->object->setAuthorLog(false, false);
        $result1 = $this->object->getColumn('user_created');
        $result2 = $this->object->getColumn('user_modified');
        $this->assertNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');
    }

    /**
     * @test
     */
    public function testGetSchemaName()
    {
        $this->assertNull($this->object->getSchemaName());
    }

    /**
     * @test
     */
    public function testSetVersionCheck()
    {
        $get1 = $this->object->hasVersionCheck();
        $get2 = $this->object->hasVersionCheck(false);
        $this->assertFalse($get1, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');
        $this->assertFalse($get2, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');

        // check if column time_modified exist - expected false
        $this->object->setVersionCheck(true, false);
        $get1 = $this->object->hasVersionCheck();
        $get2 = $this->object->hasVersionCheck(false);
        $result1 = $this->object->getColumn('time_created');
        $result2 = $this->object->getColumn('time_modified');
        $this->assertNotNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');
        $this->assertFalse($get1, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');
        $this->assertTrue($get2, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');

        // check if column time_created exist - expected true
        $this->object->setVersionCheck(true, true);
        $get1 = $this->object->hasVersionCheck();
        $get2 = $this->object->hasVersionCheck(false);
        $result1 = $this->object->getColumn('time_created');
        $result2 = $this->object->getColumn('time_modified');
        $this->assertNotNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNotNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');
        $this->assertTrue($get1, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');
        $this->assertTrue($get2, '\Yana\Db\Ddl\Table::setVersionCheck Versioncheck should be False');

        // check if column time_created exist - expected true
        $this->object->setVersionCheck(false, true);
        $result1 = $this->object->getColumn('time_created');
        $result2 = $this->object->getColumn('time_modified');
        $this->assertNotNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');

        // check if column time_created exist - expected true
        $this->object->setVersionCheck(false, false);
        $result1 = $this->object->getColumn('time_created');
        $result2 = $this->object->getColumn('time_modified');
        $this->assertNull($result1, '\Yana\Db\Ddl\Table::setVersionCheck time_created should be NULL');
        $this->assertNull($result2, '\Yana\Db\Ddl\Table::setVersionCheck time_modified should not be NULL');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testdropColumnNotFoundException()
    {
        // \Yana\Db\Ddl\Table
        $this->object->dropColumn('test');
    }

    /**
     * @test
     */
    public function testGetPrimaryKey()
    {
        $this->assertNull($this->object->getPrimaryKey());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testSetPrimaryKeyNotFoundException()
    {
        $this->object->setPrimaryKey('no-such-column');
    }

    /**
     * @test
     */
    public function testInheritance()
    {
        $this->object->setInheritance('inheritance');
        $get = $this->object->getInheritance();
        $this->assertEquals('inheritance', $get, 'assert failed, the values should be equal');
        $this->object->setInheritance('');
        $get = $this->object->getInheritance();
        $this->assertNull($get, 'assert failed, expected null');
    }

    /**
     * @test
     */
    public function testAddIndex()
    {
        $this->object->addIndex('test');
        $index = $this->object->getIndex('test');
        $this->assertTrue($index instanceof \Yana\Db\Ddl\Index, 'Method getIndex() should return \Yana\Db\Ddl\Index objects.');
        $index = $this->object->getIndex('non-existing-index');
        $this->assertNull($index, 'Search for non-existing index must return NULL.');

        $this->object->addIndex('othertest');
        $index = $this->object->getIndex('othertest');
        $this->assertTrue($index instanceof \Yana\Db\Ddl\Index, 'Method getIndex() should return \Yana\Db\Ddl\Index objects.');

        // add two more anonymous indexes
        $this->object->addIndex();
        $this->object->addIndex();

        $indexes = $this->object->getIndexes();
        $this->assertArrayHasKey('test', $indexes, 'Expected index "test" not found.');
        $this->assertArrayHasKey('othertest', $indexes, 'Expected index "othertest" not found.');
        $this->assertArrayHasKey(0, $indexes, 'Anonymous index "0" not found.');
        $this->assertArrayHasKey(1, $indexes, 'Anonymous index "1" not found.');
        $this->assertEquals(4, count($indexes), 'Unexpected number of indexes.');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     */
    public function testAddIndexAlreadyExistsException()
    {
        $this->object->addColumn('column', 'string');
        try {
            // supposed to succeed
            $this->object->addIndex('column', 'index');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->object->addIndex('column', 'index');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     */
    public function testAddColumnAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->object->addColumn('column', 'string');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->object->addColumn('column', 'string');
    }

    /**
     * @test
     */
    public function testTrigger()
    {

        $testArray1 = array("sometrigger 1", "sometrigger 2", "sometrigger 3");

        // \Yana\Db\Ddl\Table::setTriggerBeforeInsert
        $trigger = $this->object->setTriggerBeforeInsert($testArray1[0]);
        $this->assertTrue($trigger->isBefore(), "\Yana\Db\Ddl\Trigger::isBefore returns wrong value");
        $this->assertFalse($trigger->isAfter(), "\Yana\Db\Ddl\Trigger::isAfter returns wrong value");
        $this->assertFalse($trigger->isInstead(), "\Yana\Db\Ddl\Trigger::isInstead returns wrong value");
        $this->assertTrue($trigger->isInsert(), "\Yana\Db\Ddl\Trigger::isInsert returns wrong value");
        $this->assertFalse($trigger->isUpdate(), "\Yana\Db\Ddl\Trigger::isUpdate returns wrong value");
        $this->assertFalse($trigger->isDelete(), "\Yana\Db\Ddl\Trigger::isDelete returns wrong value");
        $this->object->setTriggerBeforeInsert($testArray1[1]);
        $this->object->setTriggerBeforeInsert($testArray1[2]);
        $get = $this->object->getTriggerBeforeInsert();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, the arrays should be equal');
        $get = $this->object->getTriggerBeforeInsert('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->object);
        $this->object = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerBeforeUpdate
        $this->object->setTriggerBeforeUpdate($testArray1[0]);
        $trigger = $this->object->setTriggerBeforeUpdate($testArray1[1]);
        $this->object->setTriggerBeforeUpdate($testArray1[2]);
        $get = $this->object->getTriggerBeforeUpdate();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerBeforeUpdate, the arrays should be equal');
        $get = $this->object->getTriggerBeforeUpdate('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->object);
        $this->object = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerBeforeDelete
        $this->object->setTriggerBeforeDelete($testArray1[0]);
        $this->object->setTriggerBeforeDelete($testArray1[1]);
        $this->object->setTriggerBeforeDelete($testArray1[2]);
        $get = $this->object->getTriggerBeforeDelete();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerBeforeDelete, the arrays should be equal');
        $get = $this->object->getTriggerBeforeDelete('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->object);
        $this->object = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerAfterInsert
        $this->object->setTriggerAfterInsert($testArray1[0]);
        $this->object->setTriggerAfterInsert($testArray1[1]);
        $this->object->setTriggerAfterInsert($testArray1[2]);
        $get = $this->object->getTriggerAfterInsert();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerAfterInsert, the arrays should be equal');
        $get = $this->object->getTriggerAfterInsert('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->object);
        $this->object = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerAfterUpdate
        $this->object->setTriggerAfterUpdate($testArray1[0]);
        $trigger = $this->object->setTriggerAfterUpdate($testArray1[1]);
        $this->assertFalse($trigger->isBefore(), "\Yana\Db\Ddl\Trigger::isBefore returns wrong value");
        $this->assertTrue($trigger->isAfter(), "\Yana\Db\Ddl\Trigger::isAfter returns wrong value");
        $this->assertFalse($trigger->isInstead(), "\Yana\Db\Ddl\Trigger::isInstead returns wrong value");
        $this->assertFalse($trigger->isInsert(), "\Yana\Db\Ddl\Trigger::isInsert returns wrong value");
        $this->assertTrue($trigger->isUpdate(), "\Yana\Db\Ddl\Trigger::isUpdate returns wrong value");
        $this->assertFalse($trigger->isDelete(), "\Yana\Db\Ddl\Trigger::isDelete returns wrong value");
        $this->object->setTriggerAfterUpdate($testArray1[2]);
        $get = $this->object->getTriggerAfterUpdate();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerAfterUpdate, the arrays should be equal');
        $get = $this->object->getTriggerAfterUpdate('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->object);
        $this->object = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerAfterDelete
        $this->object->setTriggerAfterDelete($testArray1[0]);
        $this->object->setTriggerAfterDelete($testArray1[1]);
        $this->object->setTriggerAfterDelete($testArray1[2]);
        $get = $this->object->getTriggerAfterDelete();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerAfterDelete, the arrays should be equal');
        $get = $this->object->getTriggerAfterDelete('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerBeforeInsert, expected null - trigger "mysql" does not exist');
        unset ($this->object);
        $this->object = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerInsteadInsert
        $this->object->setTriggerInsteadInsert($testArray1[0]);
        $this->object->setTriggerInsteadInsert($testArray1[1]);
        $this->object->setTriggerInsteadInsert($testArray1[2]);
        $get = $this->object->getTriggerInsteadInsert();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerInsteadInsert, the arrays should be equal');
        $get = $this->object->getTriggerInsteadInsert('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerInsteadInsert, expected null - trigger "mysql" does not exist');
        unset ($this->object);
        $this->object = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerInsteadUpdate
        $this->object->setTriggerInsteadUpdate($testArray1[0]);
        $this->object->setTriggerInsteadUpdate($testArray1[1]);
        $this->object->setTriggerInsteadUpdate($testArray1[2]);
        $get = $this->object->getTriggerInsteadUpdate();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerInsteadUpdate, the arrays should be equal');
        $get = $this->object->getTriggerInsteadUpdate('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerInsteadUpdate, expected null - trigger "mysql" does not exist');
        unset ($this->object);
        $this->object = new \Yana\Db\Ddl\Table('table');

        // \Yana\Db\Ddl\Table::setTriggerInsteadDelete
        $this->object->setTriggerInsteadDelete($testArray1[0]);
        $this->object->setTriggerInsteadDelete($testArray1[1]);
        $this->object->setTriggerInsteadDelete($testArray1[2]);
        $get = $this->object->getTriggerInsteadDelete();
        $this->assertEquals($get, $testArray1[0], '\Yana\Db\Ddl\Table::setTriggerInsteadDelete, the arrays should be equal');
        $get = $this->object->getTriggerInsteadDelete('mysql');
        $this->assertNull($get, '\Yana\Db\Ddl\Table::setTriggerInsteadDelete, expected null - trigger "mysql" does not exist');
        unset ($this->object);
        $this->object = new \Yana\Db\Ddl\Table('table');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetTableByForeignKeyInvalidArgumentException()
    {
        $this->object->getTableByForeignKey('nonexist');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testgetColumnByForeignKeyInvalidArgumentException()
    {
        $this->object->getColumnByForeignKey('foo_bar');
    }

}

?>