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

namespace Yana\Db\Helpers\Triggers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test class for database triggers
 *
 * @package  test
 */
class AbstractTriggerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Db\Helpers\Triggers\Container
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp()
    {
        $query = new \Yana\Db\Queries\Insert(new \Yana\Db\NullConnection());
        $table = $query->getDatabase()->getSchema()->addTable('foo');
        $table->addColumn('bar', 'string');
        $table->addColumn('fooid', 'integer');
        $table->setPrimaryKey('fooid');
        $query->setTable($table->getName());
        $query->setRow(1);
        $this->object = new \Yana\Db\Helpers\Triggers\Container($table, $query);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     */
    protected function tearDown()
    {
        
    }

    /**
     * onBeforeInsert
     *
     * @test
     */
    public function testOnBeforeInsert()
    {
        $triggerFunction = '\Yana\Db\Helpers\Triggers\Checktrigger::test';
        $this->object->table->setTriggerBeforeInsert($triggerFunction);
        $trigger = new \Yana\Db\Helpers\Triggers\BeforeInsert($this->object);
        $trigger();
        $row = $this->object->query->getValues();
        $this->assertEquals("bar", $row['bar']);
    }

    /**
     * onAfterInsert
     *
     * @test
     */
    public function testOnAfterInsert()
    {
        $triggerFunction = '\Yana\Db\Helpers\Triggers\Checktrigger::test';
        $this->object->table->setTriggerAfterInsert($triggerFunction);
        $trigger = new \Yana\Db\Helpers\Triggers\AfterInsert($this->object);
        $trigger();
        $row = $this->object->query->getValues();
        $this->assertEquals("bar", $row['bar']);
    }

    /**
     * onBeforeUpdate
     *
     * @test
     */
    public function testOnBeforeUpdate()
    {
        $triggerFunction = '\Yana\Db\Helpers\Triggers\Checktrigger::test';
        $this->object->table->setTriggerBeforeUpdate($triggerFunction);
        $trigger = new \Yana\Db\Helpers\Triggers\BeforeUpdate($this->object);
        $trigger();
        $row = $this->object->query->getValues();
        $this->assertEquals("bar", $row['bar']);
    }

    /**
     * onAfterUpdate
     *
     * @test
     */
    public function testOnAfterUpdate()
    {
        $triggerFunction = '\Yana\Db\Helpers\Triggers\Checktrigger::test';
        $this->object->table->setTriggerAfterUpdate($triggerFunction);
        $trigger = new \Yana\Db\Helpers\Triggers\AfterUpdate($this->object);
        $trigger();
        $row = $this->object->query->getValues();
        $this->assertEquals("bar", $row['bar']);
    }

    /**
     * onBeforeDelete
     *
     * @test
     */
    public function testOnBeforeDelete()
    {
        $triggerFunction = '\Yana\Db\Helpers\Triggers\Checktrigger::test';
        $this->object->table->setTriggerBeforeDelete($triggerFunction);
        $trigger = new \Yana\Db\Helpers\Triggers\BeforeDelete($this->object);
        $trigger();
        $row = $this->object->query->getValues();
        $this->assertEquals("bar", $row['bar']);
    }

    /**
     * onAfterDelete
     *
     * @test
     */
    public function testOnAfterDelete()
    {
        $triggerFunction = '\Yana\Db\Helpers\Triggers\Checktrigger::test';
        $this->object->table->setTriggerAfterDelete($triggerFunction);
        $trigger = new \Yana\Db\Helpers\Triggers\AfterDelete($this->object);
        $trigger();
        $row = $this->object->query->getValues();
        $this->assertEquals("bar", $row['bar']);
    }

}

/**
 * class checktrigger
 *
 * used for check if the trigger methods are executed
 *
 * @ignore
 */
class Checktrigger
{

    public static function test(\Yana\Db\Helpers\Triggers\Container $container)
    {
        $container->query->setValues(array('bar' => "bar"));
    }

}

?>