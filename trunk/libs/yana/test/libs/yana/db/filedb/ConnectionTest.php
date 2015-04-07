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

namespace Yana\Db\FileDb;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * DbStream test-case
 *
 * @package  test
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * database connection
     *
     * @var \Yana\Db\FileDb\Connection
     */
    public $dbsobj = null;

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        \Yana\Db\Ddl\DDL::setDirectory(CWD. 'resources/');
    }

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        try {

            \Yana\Db\FileDb\Driver::setBaseDirectory(CWD. 'resources/db/');
            \Yana\Db\Ddl\DDL::setDirectory(CWD. 'resources/');
            $schema = \Yana\Files\XDDL::getDatabase('check');
            $this->dbsobj = new \Yana\Db\FileDb\Connection($schema);
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
        $this->dbsobj->rollback();
        $this->dbsobj->remove("i.*", array(), 0);
        $this->dbsobj->remove("t.*", array(), 0);
        $this->dbsobj->remove("ft.*", array(), 0);
        $this->dbsobj->commit();
    }

    /**
     * insert and update
     *
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\InconsistencyException
     */
    public function testInsertAndUpdateWithFailingForeignKeyCheck()
    {
        $this->dbsobj->insertOrUpdate('t.foo2', array('tvalue' => 1, 'FTid' => 2 ));
        $this->dbsobj->commit(); // expected to throw exception
    }

    /**
     * insert and update
     *
     * @test
     * @expectedException Yana\Core\Exceptions\Forms\MissingFieldException
     */
    public function testInsertWithMissingField()
    {
        $this->dbsobj->insert('t.foo1', array('tvalue' => 1));
    }

    /**
     * @test
     */
    public function testUpdateArrayAddress()
    {
        $this->dbsobj->insert('ft.1', array('array' => array('1' => '1')));
        $this->dbsobj->update('ft.1.array.1.a', 2); // must not throw exception
        $this->dbsobj->commit();
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\QueryException
     */
    public function testInsertWithDuplicateKey()
    {
        $this->dbsobj->insert('i.foo', array('ta' => array('1' => '1')));
        $this->dbsobj->insert('i.foo', array('ta' => array('1' => '1')));
        $this->dbsobj->commit();
    }

    /**
     * insert and update
     *
     * @test
     */
    public function testInsertAndUpdate()
    {
        // init database
        $this->dbsobj->insert('ft.1', array('ftvalue' => 1));

        $this->dbsobj->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true));

        $this->dbsobj->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false));
        $this->dbsobj->commit();

        // supposed to fail
        try {
            $this->dbsobj->insertOrUpdate('i.foo2', array('ta' => array(1 => 1)));
            $this->dbsobj->commit();
            $this->fail('init i.foo2 failed');
        } catch (\Yana\Db\Queries\Exceptions\InconsistencyException $e) {
            $this->dbsobj->rollback();
        }

        $this->dbsobj->insert('i.foo', array('ta' => array('1' => '1')));

        // supposed to succeed
        $this->dbsobj->update('i.foo.ta.1.a', 2);

        $this->dbsobj->commit();

        // supposed to fail
        try {

            $value = array('iid' => 'foo', 'ta' => array('1' => '1'));
            $this->dbsobj->insert('i', $value);
            $this->dbsobj->commit();
            $this->fail('duplicate key test (2) failed');
        } catch (\Yana\Db\Queries\Exceptions\QueryException $e) {
            // success
        }

        // exists table
        $test = $this->dbsobj->exists('t');
        $this->assertTrue($test, '"exists table" test failed');

        // exists row
        $test = $this->dbsobj->exists('t.fOo');
        $this->assertTrue($test, '"exists row" test failed');

        // exists cell
        $test = $this->dbsobj->exists('t.fOo.tid');
        $this->assertTrue($test, '"exists cell" test failed');

        // exists column
        $test = $this->dbsobj->exists('i.*.ta');
        $this->assertTrue($test, '"exists column" test failed');

        // exists (supposed to fail)
        $test = $this->dbsobj->exists('t.fooBar.tid');
        $this->assertFalse($test, '"exists failure" test failed');

        // get table
        $table = $this->dbsobj->select('T');
        $this->assertType('array', $table, '"get table" test failed');
        $this->assertArrayHasKey('FOO', $table, '"get table" test failed');
        $this->assertType('array', $table['FOO'], '"get table" test failed');

        // get row
        $row = $this->dbsobj->select('t.fOo');
        $this->assertType('array', $row, '"get table" test failed');
        $this->assertArrayHasKey('TVALUE', $row, '"get table" test failed');

        // get cell (1)
        $cell = $this->dbsobj->select('T.Foo.TValue');
        $this->assertEquals($cell, 1, '"get cell 1" test failed');

        // get cell (2)
        $cell = $this->dbsobj->select('t.fOo.tB');
        $this->assertTrue($cell, '"get cell 2" test failed');

        // resolve foreign key
        $test = $this->dbsobj->select('t.fOo.ftid.ftvalue');
        $this->assertEquals($test, 1, '"resolving foreign key" test failed');

        // get column
        $column = $this->dbsobj->select('T.*.fTid');
        $length = $this->dbsobj->length('t');
        $this->assertType('array', $column, '"get column" test failed');
        $this->assertEquals(count($column), $length, '"get column" test failed');

        // get last entry
        $test = $this->dbsobj->select('t.?.tValue');
        $this->assertEquals($test, 3, '"get last entry" test failed');

        // test buffer
        $this->dbsobj->update('ft.3', array('ftvalue' => 3));

        $this->dbsobj->update('t.FOO3.ftid', 3); // supposed to succeed

        $test = $this->dbsobj->select('i.foo.ta.1.a'); // supposed to succeed
        $this->assertEquals($test, 2, '"get array content" failed');

        // length table
        $length = $this->dbsobj->length('T');
        $this->assertEquals($length, 2, '"get length" test failed');

        // rollback
        $this->dbsobj->rollback();

        $test = $this->dbsobj->select('i.foo');
        $test = \array_change_key_case($test);
        $temp1 = array(1 => 2, 2 => 3);
        $temp2 = 2;
        // stored in table "i"
        $test['ta'] = $temp1;
        // not stored in table "i", but in parent table "t"
        $test['tvalue'] = $temp2;
        // must update "i" AND "t" (not just "i" or "t")
        $this->dbsobj->update('i.foo', $test);
        $this->dbsobj->commit();

        // check if tables "i" and "t" have both been updated
        $this->assertEquals($this->dbsobj->select('i.foo.ta'), $temp1, '"update inheritance 2" test failed for table "i.foo.ta".');
        $this->assertEquals($this->dbsobj->select('t.foo.tvalue'), $temp2, '"update inheritance 2" test failed for table "t.foo.tvalue"');

        unset($temp1, $temp2);

        // rollback
        $this->dbsobj->rollback();

        // multiple columns
        $dbQuery = new \Yana\Db\Queries\Select($this->dbsobj);
        $dbQuery->setTable('i');
        $dbQuery->setRow('foo');
        $dbQuery->setInnerJoin('t');
        $dbQuery->setColumns(array('i.iid', 'ta', 't.tvalue'));
        $test = $this->dbsobj->select($dbQuery);
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
        $dbQuery = new \Yana\Db\Queries\Select($this->dbsobj);
        $dbQuery->setTable('i');
        $dbQuery->setRow('foo');
        $dbQuery->setInnerJoin('t');
        $dbQuery->setColumns(array('a' => 'i.iid', 'b' => 'ta', 'c' => 't.tvalue'));
        $test = $this->dbsobj->select($dbQuery);
        $expected = array(
            "A" => "FOO",
            "B" => array(
                1 => "2",
                2 => "3"
            ),
            "C" => 2
        );

        $this->assertEquals($expected, $test, 'Using alias on joined tables failed');

        $dbQuery = new \Yana\Db\Queries\Select($this->dbsobj);
        $dbQuery->setTable('t');
        $dbQuery->setColumns(array('a' => 'tid', 'b' => 'tb', 'c' => 'tvalue'));
        $test = $this->dbsobj->select($dbQuery);
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
            $this->dbsobj->update('t.foo.tf', -1);
            $this->fail('"unsigned" test failed');
        } catch (\Yana\Core\Exceptions\Forms\InvalidValueException $e) {
            // success
        }

        // test for property "zerofill"
        // column t.ti is a zerofilled integer with length 4
        $this->dbsobj->update('t.foo.ti', 1);
        $this->dbsobj->commit();

        $this->assertEquals($this->dbsobj->select('t.foo.ti'), '0001', '"get zerofill" test failed');
    }

}

?>