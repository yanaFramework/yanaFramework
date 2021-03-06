<?php
/**
 * PHPUnit test-case.
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

namespace Yana\Db\FileDb;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * database connection
     *
     * @var \Yana\Db\FileDb\Connection
     */
    public $object = null;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        try {
            $schema = \Yana\Files\XDDL::getDatabase('check');
            $this->object = new \Yana\Db\FileDb\Connection($schema);
            restore_error_handler();

        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // drop all previous entries
        $this->object->rollback();
        $this->object->remove("i.*", array(), 0);
        $this->object->remove("t.*", array(), 0);
        $this->object->remove("ft.*", array(), 0);
        $this->object->commit();
    }

    /**
     * @test
     */
    public function testImportSQL()
    {
        $this->assertTrue($this->object->importSQL(__FILE__));
    }

    /**
     * @test
     */
    public function testSendQueryString()
    {
        $this->assertSame(array(), $this->object->sendQueryString("select * from t")->fetchAll());
    }

    /**
     * insert and update
     *
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\InconsistencyException
     */
    public function testInsertAndUpdateWithFailingForeignKeyCheck()
    {
        $this->object->insertOrUpdate('t.foo2', array('tvalue' => 1, 'FTid' => 2 ));
        $this->object->commit(); // expected to throw exception
    }

    /**
     * insert and update
     *
     * @test
     * @expectedException Yana\Core\Exceptions\Forms\MissingFieldException
     */
    public function testInsertWithMissingField()
    {
        $this->object->insert('t.foo1', array('tvalue' => 1));
    }

    /**
     * @test
     */
    public function testUpdateArrayAddress()
    {
        $this->object->insert('ft.1', array('array' => array('1' => '1')));
        $this->object->update('ft.1.array.1.a', 2); // must not throw exception
        $this->object->commit();
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\QueryException
     */
    public function testInsertWithDuplicateKey()
    {
        $this->object->insert('i.foo', array('ta' => array('1' => '1')));
        $this->object->insert('i.foo', array('ta' => array('1' => '1')));
        $this->object->commit();
    }

    /**
     * insert and update
     *
     * @test
     */
    public function testInsertAndUpdate()
    {
        // init database
        $this->object->insert('ft.1', array('ftvalue' => 1));

        $this->object->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true));

        $this->object->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false));
        $this->object->commit();

        // supposed to fail
        try {
            $this->object->insertOrUpdate('i.foo2', array('ta' => array(1 => 1)));
            $this->object->commit();
            $this->fail('init i.foo2 failed');
        } catch (\Yana\Db\Queries\Exceptions\InconsistencyException $e) {
            $this->object->rollback();
        }

        $this->object->insert('i.foo', array('ta' => array('1' => '1')));

        // supposed to succeed
        $this->object->update('i.foo.ta.1.a', 2);

        $this->object->commit();

        // supposed to fail
        try {

            $value = array('iid' => 'foo', 'ta' => array('1' => '1'));
            $this->object->insert('i', $value);
            $this->object->commit();
            $this->fail('duplicate key test (2) failed');
        } catch (\Yana\Db\Queries\Exceptions\QueryException $e) {
            // success
        }

        // exists table
        $test = $this->object->exists('t');
        $this->assertTrue($test, '"exists table" test failed');

        // exists row
        $test = $this->object->exists('t.fOo');
        $this->assertTrue($test, '"exists row" test failed');

        // exists cell
        $test = $this->object->exists('t.fOo.tid');
        $this->assertTrue($test, '"exists cell" test failed');

        // exists column
        $test = $this->object->exists('i.*.ta');
        $this->assertTrue($test, '"exists column" test failed');

        // exists (supposed to fail)
        $test = $this->object->exists('t.fooBar.tid');
        $this->assertFalse($test, '"exists failure" test failed');

        // get table
        $table = $this->object->select('T');
        $this->assertInternalType('array', $table, '"get table" test failed');
        $this->assertArrayHasKey('FOO', $table, '"get table" test failed');
        $this->assertInternalType('array', $table['FOO'], '"get table" test failed');

        // get row
        $row = $this->object->select('t.fOo');
        $this->assertInternalType('array', $row, '"get table" test failed');
        $this->assertArrayHasKey('TVALUE', $row, '"get table" test failed');

        // get cell (1)
        $cell = $this->object->select('T.Foo.TValue');
        $this->assertEquals($cell, 1, '"get cell 1" test failed');

        // get cell (2)
        $cell = $this->object->select('t.fOo.tB');
        $this->assertTrue($cell, '"get cell 2" test failed');

        // resolve foreign key
        $test = $this->object->select('t.fOo.ftid.ftvalue');
        $this->assertEquals($test, 1, '"resolving foreign key" test failed');

        // get column
        $column = $this->object->select('T.*.fTid');
        $length = $this->object->length('t');
        $this->assertInternalType('array', $column, '"get column" test failed');
        $this->assertEquals(count($column), $length, '"get column" test failed');

        // get last entry
        $test = $this->object->select('t.?.tValue');
        $this->assertEquals($test, 3, '"get last entry" test failed');

        // test buffer
        $this->object->update('ft.3', array('ftvalue' => 3));

        $this->object->update('t.FOO3.ftid', 3); // supposed to succeed

        $test = $this->object->select('i.foo.ta.1.a'); // supposed to succeed
        $this->assertEquals($test, 2, '"get array content" failed');

        // length table
        $length = $this->object->length('T');
        $this->assertEquals($length, 2, '"get length" test failed');

        // rollback
        $this->object->rollback();

        $test = $this->object->select('i.foo');
        $test = \array_change_key_case($test);
        $temp1 = array(1 => 2, 2 => 3);
        $temp2 = 2;
        // stored in table "i"
        $test['ta'] = $temp1;
        // not stored in table "i", but in parent table "t"
        $test['tvalue'] = $temp2;
        // must update "i" AND "t" (not just "i" or "t")
        $this->object->update('i.foo', $test);
        $this->object->commit();

        // check if tables "i" and "t" have both been updated
        $this->assertEquals($this->object->select('i.foo.ta'), $temp1, '"update inheritance 2" test failed for table "i.foo.ta".');
        $this->assertEquals($this->object->select('t.foo.tvalue'), $temp2, '"update inheritance 2" test failed for table "t.foo.tvalue"');

        unset($temp1, $temp2);

        // rollback
        $this->object->rollback();

        // multiple columns
        $dbQuery = new \Yana\Db\Queries\Select($this->object);
        $dbQuery->setTable('i');
        $dbQuery->setRow('foo');
        $dbQuery->setInnerJoin('t');
        $dbQuery->setColumns(array('i.iid', 'ta', 't.tvalue'));
        $test = $this->object->select($dbQuery);
        $expected = array(
            "IID" => "FOO",
            "TA" => array(
                1 => "2",
                2 => "3"
            ),
            "TVALUE" => 2
        );

        $this->assertEquals($expected, $test, '"get multiple columns" test failed');

        // Alias test
        $dbQuery = new \Yana\Db\Queries\Select($this->object);
        $dbQuery->setTable('i');
        $dbQuery->setRow('foo');
        $dbQuery->setInnerJoin('t');
        $dbQuery->setColumns(array('a' => 'i.iid', 'b' => 'ta', 'c' => 't.tvalue'));
        $test = $this->object->select($dbQuery);
        $expected = array(
            "A" => "FOO",
            "B" => array(
                1 => "2",
                2 => "3"
            ),
            "C" => 2
        );

        $this->assertEquals($expected, $test, 'Using alias on joined tables failed');

        $dbQuery = new \Yana\Db\Queries\Select($this->object);
        $dbQuery->setTable('t');
        $dbQuery->setColumns(array('a' => 'tid', 'b' => 'tb', 'c' => 'tvalue'));
        $test = $this->object->select($dbQuery);
        $expected = array(
            array(
                "A" => "FOO",
                "B" => true,
                "C" => 2
            ),
            array(
                "A" => "FOO3",
                "B" => false,
                "C" => 3
            )
        );

        $this->assertEquals($expected, $test, 'Using alias on single table failed');

        // test for property "unsigned"
        // column t.tf has unsigned constraint

        try {
            $this->object->update('t.foo.tf', -1);
            $this->fail('"unsigned" test failed');
        } catch (\Yana\Core\Exceptions\Forms\InvalidValueException $e) {
            // success
        }

        // test for property "zerofill"
        // column t.ti is a zerofilled integer with length 4
        $this->object->update('t.foo.ti', 1);
        $this->object->commit();

        $this->assertEquals($this->object->select('t.foo.ti'), '0001', '"get zerofill" test failed');
    }

}
