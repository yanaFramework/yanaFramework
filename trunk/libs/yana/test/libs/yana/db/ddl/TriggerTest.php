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
 * @package  test
 */
class TriggerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Trigger
     */
    protected $object;

    /**
     * @var array
     */
    protected $testData = array("sometrigger 1", "sometrigger 2", "sometrigger 3");

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Trigger();
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
    public function testGetDBMS()
    {
        $this->assertSame("generic", $this->object->getDBMS());
    }

    /**
     * @test
     */
    public function testSetDBMS()
    {
        $this->assertSame("mysql", $this->object->setDBMS("MySql")->getDBMS());
        $this->assertNull($this->object->setDBMS("")->getDBMS());
    }

    /**
     * @test
     */
    public function testGetTrigger()
    {
        $this->assertNull($this->object->getTrigger());
    }

    /**
     * @test
     */
    public function testSetTrigger()
    {
        $this->assertSame("Test", $this->object->setTrigger("Test")->getTrigger());
    }

    /**
     * @test
     */
    public function testIsBefore()
    {
        $this->assertTrue($this->object->isBefore(), 'before is default');
    }

    /**
     * @test
     */
    public function testIsAfter()
    {
        $this->assertFalse($this->object->isAfter());
    }

    /**
     * @test
     */
    public function testIsInstead()
    {
        $this->assertFalse($this->object->isInstead());
    }

    /**
     * @test
     */
    public function testSetBefore()
    {
        $this->assertTrue($this->object->setBefore()->isBefore());
        $this->assertFalse($this->object->setAfter()->isBefore());
        $this->assertFalse($this->object->setInstead()->isBefore());
    }

    /**
     * @test
     */
    public function testSetAfter()
    {
        $this->assertFalse($this->object->setBefore()->isAfter());
        $this->assertTrue($this->object->setAfter()->isAfter());
        $this->assertFalse($this->object->setInstead()->isAfter());
    }

    /**
     * @test
     */
    public function testSetInstead()
    {
        $this->assertFalse($this->object->setBefore()->isInstead());
        $this->assertFalse($this->object->setAfter()->isInstead());
        $this->assertTrue($this->object->setInstead()->isInstead());
    }

    /**
     * @test
     */
    public function testIsInsert()
    {
        $this->assertFalse($this->object->isInsert());
    }

    /**
     * @test
     */
    public function testIsUpdate()
    {
        $this->assertFalse($this->object->isUpdate());
    }

    /**
     * @test
     */
    public function testIsDelete()
    {
        $this->assertFalse($this->object->isDelete());
    }

    /**
     * @test
     */
    public function testSetInsert()
    {
        $this->assertTrue($this->object->setInsert()->isInsert());
        $this->assertTrue($this->object->setInsert(true)->isInsert());
        $this->assertFalse($this->object->setInsert(false)->isInsert());
    }

    /**
     * @test
     */
    public function testSetUpdate()
    {
        $this->assertTrue($this->object->setUpdate()->isUpdate());
        $this->assertTrue($this->object->setUpdate(true)->isUpdate());
        $this->assertFalse($this->object->setUpdate(false)->isUpdate());
    }

    /**
     * @test
     */
    public function testSetDelete()
    {
        $this->assertTrue($this->object->setDelete()->isDelete());
        $this->assertTrue($this->object->setDelete(true)->isDelete());
        $this->assertFalse($this->object->setDelete(false)->isDelete());
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $data = "<trigger name='Test' dbms='MySql' insert='yes' update='yes' delete='yes' on='after'>content</trigger>";
        $unserialized = $this->object->unserializeFromXDDL(new \SimpleXMLElement($data));
        $this->assertSame('test', $unserialized->getName());
        $this->assertSame('MySql', $unserialized->getDBMS());
        $this->assertSame('content', $unserialized->getTrigger());
        $this->assertTrue($unserialized->isAfter());
        $this->assertFalse($unserialized->isBefore());
        $this->assertFalse($unserialized->isInstead());
        $this->assertTrue($unserialized->isAfter());
        $this->assertTrue($unserialized->isInsert());
        $this->assertTrue($unserialized->isUpdate());
        $this->assertTrue($unserialized->isDelete());
    }

}
