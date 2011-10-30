<?php
/**
 * PHPUnit test-case: DbStructureGenerics
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
 * Test class for DbStructureGenerics
 *
 * @package  test
 */
class DbStructureGenericsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    DbStructureGenerics
     * @access protected
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {  
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {

    }

    /**
     * checkConstraint
     *
     * @test
     */
    public function testCheckConstraint()
    {
        $table = new \Yana\Db\Ddl\Table('foo');
        $table->addColumn('bar', 'string');
        $table->addColumn('foid', 'integer');
        $row = array('bar');
        $constraint = 'true';
        $table->addConstraint($constraint, 'true');
        $check = DbStructureGenerics::checkConstraint($table, $row);
        $this->assertTrue($check, 'assert failed, check constraint is valid');

        $table = new \Yana\Db\Ddl\Table('bar');
        $table->addColumn('foo', 'string');
        $table->addColumn('barid', 'integer');
        $row = array('foo', 'barid');
        $constraint = 'null';
        $table->addConstraint($constraint, 'null');
        $check = DbStructureGenerics::checkConstraint($table, $row);
        $this->assertFalse($check, 'assert failed,  check constraint is not valid');

        $table = new \Yana\Db\Ddl\Table('bar');
        $table->addColumn('foo', 'string');
        $table->addColumn('barid', 'integer');
        $row = array('foo', 'barid');
        $constraint = 'select';
        $table->addConstraint($constraint, 'select');
        $check = DbStructureGenerics::checkConstraint($table, $row);
        $this->assertFalse($check, 'assert failed,  check constraint is not valid');
    }

    /**
     * onBeforeInsert
     *
     * @test
     */
    public function testOnBeforeInsert()
    {   
        $table = new \Yana\Db\Ddl\Table('foo');
        $table->addColumn('bar', 'string');
        $table->addColumn('foid', 'integer');
        $table->setTriggerBeforeInsert('checktrigger::write');
        $value = array('bar'=>'new');
        $result = DbStructureGenerics::onBeforeInsert($table, $value);
        $this->assertEquals(1, checktrigger::read(), 'assert failed, the expected value 1 is different from the given');

    }

    /**
     * onAfterInsert
     *
     * @test
     */
    public function testOnAfterInsert()
    {   
        $table = new \Yana\Db\Ddl\Table('foo');
        $table->addColumn('bar', 'string');
        $table->addColumn('foid', 'integer');
        $table->setTriggerAfterInsert('checktrigger::write');
        $value = array();
        $result = DbStructureGenerics::onAfterInsert($table, $value);
        $this->assertEquals(1, checktrigger::read(), 'assert failed, the expected value 1 is different from the given');
    }

    /**
     * onBeforeUpdate
     *
     * @test
     */
    public function testOnBeforeUpdate()
    {
        $table = new \Yana\Db\Ddl\Table('foo');
        $table->addColumn('bar', 'string');
        $table->addColumn('foid', 'integer');
        $table->setTriggerBeforeUpdate('checktrigger::write');
        $value = array();
        $result = DbStructureGenerics::onBeforeUpdate($table, 'bar', $value);
        $this->assertEquals(1, checktrigger::read(), 'assert failed, the expected value 1 is different from the given');
    }

    /**
     * onAfterUpdate
     *
     * @test
     */
    public function testOnAfterUpdate()
    {
        $table = new \Yana\Db\Ddl\Table('foo');
        $table->addColumn('bar', 'string');
        $table->addColumn('foid', 'integer');
        $table->setTriggerAfterUpdate('checktrigger::write');
        $value = "foo// 'bar";
        $result = DbStructureGenerics::onAfterUpdate($table, 'bar', $value);
        $this->assertEquals(1, checktrigger::read(), 'assert failed, the expected value 1 is different from the given');
    }

    /**
     * onBeforeDelete
     *
     * @test
     */
    public function testOnBeforeDelete()
    {
        $table = new \Yana\Db\Ddl\Table('foo');
        $table->addColumn('bar', 'string');
        $table->addColumn('foid', 'integer');
        $table->setTriggerBeforeDelete('checktrigger::write');
        $value = array();
        $result = DbStructureGenerics::onBeforeDelete($table, $value);
        $this->assertEquals(1, checktrigger::read(), 'assert failed, the expected value 1 is different from the given');
    }

    /**
     * onAfterDelete
     *
     * @test
     */
    public function testOnAfterDelete()
    {
        $table = new \Yana\Db\Ddl\Table('foo');
        $table->addColumn('bar', 'string');
        $table->addColumn('foid', 'integer');
        $table->setTriggerAfterDelete('checktrigger::write');
        $value = array();
        $result = DbStructureGenerics::onAfterDelete($table, $value);
        $this->assertEquals(1, checktrigger::read(), 'assert failed, the expected value 1 is different from the given');
    }
}

/**
 * class checktrigger
 *
 * used for check if the trigger methods are executed
 *
 * @ignore
 */
class checktrigger
{
    protected static $value = 0;

    public static function write()
    {
        checktrigger::$value = 1;
    }

    public static function read()
    {
        $result = checktrigger::$value;
        checktrigger::$value = 0;
        return $result;
    }
}
?>