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

namespace Yana\Db\Mdb2;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

// load PEAR-DB class
error_reporting(0);
include_once 'MDB2.php';
error_reporting(E_ALL);

/**
 * Connection test-case
 *
 * @package  test
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * database connection
     *
     * @var \Yana\Db\Mdb2\Connection
     */
    public $dbsobj = null;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        try {
            chdir(CWD . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
            $schema = \Yana\Files\XDDL::getDatabase('check');
            $this->dbsobj = new \Yana\Db\Mdb2\Connection($schema);
        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        if ($this->dbsobj instanceof \Yana\Db\Mdb2\Connection) {
            // drop all previous entries
            $this->dbsobj->remove('i', array(), 0);
            $this->dbsobj->remove('t', array(), 0);
            $this->dbsobj->remove('ft', array(), 0);
            $this->dbsobj->commit();
        }
        chdir(CWD);
    }

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        // intentionally left blank
    }

    /**
     * Update Invalid Argument Exception
     *
     * @expectedException \Yana\Db\Queries\Exceptions\NotUpdatedException
     * @test
     */
    public function testUpdateInvalidArgument()
    {
        $this->dbsobj->update('tk.foo1', array('kvalue' => 1 ));
    }

    /**
     * ImportSQL Invalid Argument Exception
     *
     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    public function testImportSQLInvalidArgument()
    {
        $this->dbsobj->importSQL(10);
    }

    /**
     * query Invalid Argument Exception
     *
     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    public function testQueryInvalidArgument()
    {
        $new = new \Yana\Db\Ddl\Table('as');
        $this->dbsobj->query($new);
    }

    /**
     * query Invalid Argument Exception 1
     *
     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    public function testQueryInvalidArgument1()
    {
        $this->dbsobj->query(3);
    }


    /**
     * get Invalid Argument Exception 1
     *
     * @test
     */
    public function testToString()
    {
        $actual = (string) $this->dbsobj;
        $expected = $this->dbsobj->schema->getName();
        $this->assertEquals($expected, $actual, "Function __toString() must return database name.");
    }

    /**
     * quote Invalid Argument Exception
     *
     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    public function testQuoteInvalidArgument()
    {
        $this->dbsobj->quote(array());
    }


    /**
     * importSQL Invalid Argument Exception2
     *
     * @expectedException \Yana\Core\Exceptions\NotReadableException
     * @test
     */
    public function testImportSQLInvalidArgument2()
    {
        $file = "non-existing-file.sql";
        $this->dbsobj->importSQL($file);
    }

    /**
     * insert and update
     *
     * @test
     */
    public function testInsertAndUpdate()
    {
        $this->dbsobj->reset();
        $dns = $this->dbsobj->getDsn();
        $this->assertArrayHasKey('DBMS', $dns, 'assert failed, the key "DBMS" is not found in giving array');
        $this->assertEquals('mysql', $dns['DBMS'], 'assert failed, the expected "mysql" value must match the value of DBMS');

        $test = $this->dbsobj->isEmpty('T');
        $this->assertTrue($test, '"isEmpty" test failed, the expected result is true for no entries inside the table');

        // init database
        $test = $this->dbsobj->insert('ft.1', array('ftvalue' => 1));
        $this->assertTrue($test, 'init ft.1 failed');

        // supposed to fail
        try {
            $this->dbsobj->insert('t.foo1', array('tvalue' => 1 ));
            $this->fail('expected insert of t.foo1 to fail');
        } catch(InvalidValueException $e) {
            // success
        }

        // supposed to fail
        $test = $this->dbsobj->insertOrUpdate('t.foo2', array('tvalue' => 1, 'ftid' => 2 ));
        $this->assertFalse($test, 'expected insert of t.foo2 to fail, due to a foreign-key constraint');

        // supposed to fail
        $test = @$this->dbsobj->insertOrUpdate('t.foo2', array('tvalue' => 1, 'ftid' => 2 ));
        $this->assertFalse($test, 'expected insert of t.foo2 to fail, due to a foreign-key constraint');

        $test = $this->dbsobj->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true ));
        $this->assertTrue($test, 'init t.foo failed');

        $test = $this->dbsobj->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false ));
        $this->assertTrue($test, 'init t.foo3 failed');

        // supposed to fail
        $test = @$this->dbsobj->insertOrUpdate('i.foo2', array('ta' => array(1 => 1 ) ));
        $this->assertFalse($test, 'init i.foo2 failed');

        $test = $this->dbsobj->insert('i.foo', array('ta' => array('1' => '1' ) ));
        $this->assertTrue($test, 'init i.foo failed');

        // supposed to succeed
        $this->dbsobj->update('i.foo.ta.1.a', 2);

        $this->dbsobj->commit();

        $test = new \Yana\Db\Queries\Select($this->dbsobj);
        $test->setTable('ft');
        $test = $test->toCSV(',', "\n", false);
        $expected = '"1","1"' . "\n";
        $this->assertEquals($expected, $test, 'assert failed, the values should be equal');

        // supposed to fail
        $test = @$this->dbsobj->insert('i.foo', array('ta' => array('1' => '1')));
        $this->dbsobj->commit();
        $this->assertFalse($test, 'duplicate key test (1) failed');

        // supposed to fail
        $test = @$this->dbsobj->insert('i', array('iid' => 'foo', 'ta' => array('1' => '1')));
        $this->dbsobj->commit();
        $this->assertFalse($test, 'duplicate key test (2) failed');

        // exists table
        $test = $this->dbsobj->exists('t');
        $this->assertTrue($test, '"exists table" test failed');

        try {
            $test = $this->dbsobj->insert('t.foo', array('tvalue' => 1));
        } catch (\Yana\Core\Exceptions\InvalidValueException $e) {
            // insert t.foo test" failed row with key already exist
        }

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
        $test = @$this->dbsobj->exists('t.fooBar.tid');
        $this->assertFalse($test, '"exists failure" test failed');

        // get table
        $test = $this->dbsobj->select('T');
        $this->assertType('array', $test, '"get table" the value should be of type array');
        $this->assertTrue(in_array('FOO', array_keys($test)), '"get table" the value should be contain the string name "FOO"');
        $this->assertType('array', $test['FOO'], '"get table" the value should be of type array');


        // get row
        $test = $this->dbsobj->select('t.fOo');
        $this->assertType('array', $test, '"get table" value should be of type array');
        $this->assertTrue(in_array('TVALUE', array_keys($test)), '"get table" test failed');

        // get cell (1)
        $test = $this->dbsobj->select('T.Foo.TValue');
        $this->assertEquals($test, 1, '"get cell 1" test failed');


        // get cell (2)
        $test = $this->dbsobj->select('t.fOo.tB');
        $this->assertTrue($test, '"get cell 2" test failed');


        // resolve foreign key
        $test = $this->dbsobj->select('t.fOo.ftid.ftvalue');
        $this->assertEquals($test, 1, '"resolving foreign key" test failed');


        // get column
        $test = $this->dbsobj->select('T.*.fTid');
        $test2 = $this->dbsobj->length('t');
        $this->assertType('array', $test, '"get column" test failed');
        $this->assertEquals(count($test), $test2, '"get column" test failed');

        // get primary key column
        $test = $this->dbsobj->select('T.*.tid');
        $test2 = $this->dbsobj->length('t');
        $this->assertType('array', $test, '"get column" test failed');
        $this->assertEquals(count($test), $test2, '"get column" test failed');


        // get last entry
        $test = $this->dbsobj->select('t.?.tValue');
        $this->assertEquals($test, 3, '"get last entry" test failed');

        // test foreign key constraint
        $test = $this->dbsobj->insertOrUpdate('t.foo.ftid', 2); // supposed to fail
        $this->assertFalse($test, '"foreign key" test failed');

        // test buffer
        $this->dbsobj->update('ft.3', array('ftvalue' => 3 ));

        $this->dbsobj->update('t.FOO3.ftid', 3); // supposed to succeed

        $test = $this->dbsobj->select('i.foo.ta.1.a'); // supposed to succeed
        $this->assertEquals($test, 2, '"get array content" failed');

        // length table
        $test = $this->dbsobj->count('T');
        $this->assertEquals($test, 2, '"get length" test failed');

        // isEmpty
        $test = $this->dbsobj->isEmpty('T');
        $this->assertFalse($test, '"isEmpty" test failed, the expected result is false - 2 entries are inside the table');

        // rollback
        $this->dbsobj->reset();

        $test = $this->dbsobj->select('i.foo');
        $temp1 = array(1 => 2, 2 => 3);
        $temp2 = 2;
        $test['ta'] = $temp1 ;
        $test['tvalue'] = $temp2;
        $this->assertTrue($this->dbsobj->update('i.foo', $test), '"update inheritance 1" test failed');
        $this->dbsobj->commit();

        $this->assertEquals($this->dbsobj->select('i.foo.ta'), $temp1, '"update inheritance 2" test failed');
        $this->assertEquals($this->dbsobj->select('t.foo.tvalue'), $temp2, '"update inheritance 2" test failed');

        unset($temp1, $temp2);

        // rollback
        $this->dbsobj->reset();

        // multiple columns
        $dbQuery = new \Yana\Db\Queries\Select($this->dbsobj);
        $dbQuery->setTable('i');
        $dbQuery->setRow('foo');
        $dbQuery->setInnerJoin('t');
        $dbQuery->setColumns(array('i.iid', 'ta', 't.tvalue'));
        $test = $this->dbsobj->select($dbQuery);
        $test2 = array(
             "IID" => "FOO",
             "TA" => array(
                   1 => "2",
                   2 => "3"
                 ),
             "TVALUE" => 2
             );

        $this->assertEquals($test, $test2, '"get multiple columns" test failed');

        $this->assertTrue($this->dbsobj->exists($dbQuery), '"exists multiple columns" test failed');

        // test for property "unsigned"
        // column t.tf has unsigned constraint
        try{
            $this->dbsobj->update('t.foo.tf', -1);
            /* "unsigned" test failed */
        } catch (\Yana\Core\Exceptions\InvalidValueException $e) {
            $this->assertTrue(true);
        }

        // test for property "zerofill"
        // column t.ti is a zerofilled integer with length 4
        $this->dbsobj->update('t.foo.ti', 1);
        $this->dbsobj->commit();

        $this->assertEquals($this->dbsobj->select('t.foo.ti'), '0001', '"get zerofill" test failed');

        $test = $this->dbsobj->quote(null);
        $this->assertEquals($test, 'NULL', 'assert failed, the value should be "null"');

        // Reset the object to default values
        $this->dbsobj->rollback();

        $test = $this->dbsobj->importSQL(CWD.'resources/empty.sql');
        $this->assertFalse($test, 'The file is empty and should no be imported.');

        $test = $this->dbsobj->importSQL(CWD.'resources/foo.sql');
        $this->assertTrue($test, 'The file is expected to be imported successfully.');

        $this->dbsobj->commit();

        $this->assertTrue($this->dbsobj->exists('t.FOO1'), 'Expect column "foo1" to exist in table "t".');
        $this->assertTrue($this->dbsobj->exists('t.FOO2'), 'Expect column "foo2" to exist in table "t".');
    }

    /**
     * test for equality
     *
     * @test
     */
    public function testEquals()
    {
        // create another object of this class
        $schema = \Yana\Files\XDDL::getDatabase('check');
        $anotherObject = new \Yana\Db\Mdb2\Connection($schema);
        $test = $this->dbsobj->equals($anotherObject);
        $this->assertFalse($test, 'assert failed, there are two different objects of dbstrem');

        $anotherObject = new Object();
        $test = $this->dbsobj->equals($anotherObject);
        $this->assertFalse($test, 'assert failed, there are two different objects');

        $sameObj = $this->dbsobj;
        $test = $this->dbsobj->equals($sameObj);
        $this->assertTrue($test, 'assert failed, there are the same objects');
    }
}

?>