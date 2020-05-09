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
class ForeignKeyTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\ForeignKey
     */
    protected $object;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\ForeignKey('foreignkey');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->object);
    }

    /**
     * @test
     */
    public function testGetSourceTable()
    {
        $this->assertNull($this->object->getSourceTable());
    }

    /**
     * @test
     */
    public function testDeferrable()
    {
       $this->object->setDeferrable(true);
       $result = $this->object->isDeferrable();
       $this->assertTrue($result, 'assert failed, \Yana\Db\Ddl\ForeignKey : expected true - setDeferrable was set with true');

       $this->object->setDeferrable(false);
       $result = $this->object->isDeferrable();
       $this->assertFalse($result, 'assert failed, \Yana\Db\Ddl\ForeignKey : expected false - setDeferrable was set with false');
    }

    /**
     * @test
     */
    public function testGetColumns()
    {
        $this->assertSame(array(), $this->object->getColumns());
    }

    /**
     * @test
     */
    public function testSetColumn()
    {
        $get = $this->object->setColumn('test', 'qwertz')->getColumns();
        $this->assertArrayHasKey('test', $get, 'assert failed, the values should be equal,  the value should be match a key in array');
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
        $this->object->setColumns($array);
        $result = $this->object->getColumns();
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
     * TargetTable
     *
     * @test
     */
    public function testTargetTable()
    {
        $this->object->setTargetTable('targetTable');
        $result = $this->object->getTargetTable();
        $this->assertEquals('targettable', $result, 'getTargetTable() did not return expected value');

        $this->object->setTargetTable('');
        $result = $this->object->getTargetTable();
        $this->assertNull($result, 'reset of target table failed');
    }

    /**
     * Match
     *
     * @test
     */
    public function testMatch()
    {
        $this->object->setMatch(2);
        $result = $this->object->getMatch();
        $message = 'assert failed, \Yana\Db\Ddl\ForeignKey : expected value is the number 2';
        $this->assertEquals(\Yana\Db\Ddl\KeyMatchStrategyEnumeration::SIMPLE, $result, $message);

        $this->object->setMatch(12);
        $result = $this->object->getMatch();
        // expected default 0
        $message = 'assert failed, \Yana\Db\Ddl\ForeignKey : expected 0 as value, the 0 number will be choosen when the number ' .
            'by setMatch does not match the numbers 0, 1, 2';
        $this->assertEquals(\Yana\Db\Ddl\KeyMatchStrategyEnumeration::SIMPLE, $result, $message);
    }

    /**
     * @test
     */
    public function testGetParent()
    {
        $this->assertNull($this->object->getParent());

        $parentTable = new \Yana\Db\Ddl\Table('table');

        $childForeignkey = new \Yana\Db\Ddl\ForeignKey('column', $parentTable);
        $this->assertEquals($parentTable, $childForeignkey->getParent());
    }

    /**
     * @test
     */
    public function testOnDelete()
    {
        $this->object->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION);
        $get = $this->object->getOnDelete();
        $message = 'assert failed, expected value is "0" - the values should be equal';
        $this->assertEquals(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION, $get, $message);

        $this->object->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT);
        $get = $this->object->getOnDelete();
        $message ='assert failed, expected value is "1" - the values should be equal';
        $this->assertEquals(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT, $get, $message);

        $this->object->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE);
        $get = $this->object->getOnDelete();
        $message = 'assert failed, expected value is "2" - the values should be equal';
        $this->assertEquals(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE, $get, $message);

        $this->object->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL);
        $get = $this->object->getOnDelete();
        $message = 'assert failed, expected value is "3" - the values should be equal';
        $this->assertEquals(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL, $get, $message);

        $this->object->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT);
        $get = $this->object->getOnDelete();
        $message = 'assert failed, expected value is "4" - the values should be equal';
        $this->assertEquals(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETDEFAULT, $get, $message);

        $this->object->setOnDelete(14);
        $get = $this->object->getOnDelete();
        $message = 'assert failed, expected value is "0" - only numbers between 0-4 can be set ' .
            'otherwise the default value "0" will be set';
        $this->assertEquals(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::NOACTION, $get, $message);
    }

    /**
     * @test
     */
    public function testOnUpdate()
    {
        $this->object->setOnUpdate(0);
        $get = $this->object->getOnUpdate();
        $this->assertEquals(0, $get, 'assert failed, expected value is "0" - the values should be equal');

        $this->object->setOnUpdate(1);
        $get = $this->object->getOnUpdate();
        $this->assertEquals(1, $get, 'assert failed, expected value is "1" - the values should be equal');

        $this->object->setOnUpdate(2);
        $get = $this->object->getOnUpdate();
        $this->assertEquals(2, $get, 'assert failed, expected value is "2" - the values should be equal');

        $this->object->setOnUpdate(3);
        $get = $this->object->getOnUpdate();
        $this->assertEquals(3, $get, 'assert failed, expected value is "3" - the values should be equal');

        $this->object->setOnUpdate(4);
        $get = $this->object->getOnUpdate();
        $this->assertEquals(4, $get, 'assert failed, expected value is "4" - the values should be equal');

        $this->object->setOnUpdate(14);
        $get = $this->object->getOnUpdate();
        $this->assertEquals(0, $get, 'assert failed, expected value is "0" - only numbers between 0-4 can be set otherwise the default value "0" will be set');
    }

}

?>