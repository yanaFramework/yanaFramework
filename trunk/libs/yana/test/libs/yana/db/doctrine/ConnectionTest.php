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

namespace Yana\Db\Doctrine;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

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
     * @var \Yana\Db\Doctrine\Connection
     */
    protected $object = null;

    /**
     * @return bool
     */
    protected function isAvailable()
    {
        $factory = new \Yana\Db\Doctrine\ConnectionFactory();
        return $factory->isAvailable($factory->getDsn());
    }

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        if (!$this->isAvailable()) {
            $this->markTestSkipped();
        }
        try {
            chdir(CWD . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
            $schema = \Yana\Files\XDDL::getDatabase('check');
            $this->object = new \Yana\Db\Doctrine\Connection($schema);

        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        if ($this->object instanceof \Yana\Db\Doctrine\Connection) {
            // drop all previous entries
            $this->object->reset();
            $this->object->remove('i', array(), 0);
            $this->object->remove('t', array(), 0);
            $this->object->remove('ft', array(), 0);
            $this->object->commit();
        }
        chdir(CWD);
    }

    /**
     * Update Invalid Argument Exception
     *
     * @expectedException \Yana\Db\Queries\Exceptions\TableNotFoundException
     * @test
     */
    public function testUpdateTableNotFoundException()
    {
        $this->object->update('tf.foo1', array('kvalue' => 1 ));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotReadableException
     */
    public function testImportSQLNotReadableException()
    {
        $this->object->importSQL('no-such-file');
    }

    /**
     * quote Invalid Argument Exception
     *
     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    public function testQuoteInvalidArgument()
    {
        $this->object->quote(array());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\MissingFieldException
     */
    public function testInsertMissingFieldException()
    {
        $this->object->insert('t.foo1', array('tvalue' => 1 ));
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\ConstraintException
     */
    public function testInsertOrUpdateConstraintException()
    {
        $this->object->insertOrUpdate('t.foo2', array('tvalue' => 1, 'ftid' => 2))->commit();
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\ConstraintException
     */
    public function testInsertOrUpdateConstraintException2()
    {
        $this->object->insert('ft.1', array('ftvalue' => 1));
        $this->object->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true ));
        $this->object->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false ));
        $this->object->insertOrUpdate('i.foo2', array('ta' => array(1 => 1)));
        $this->object->commit();
    }

    /**
     * insert and update
     *
     * @test
     */
    public function testUpdate()
    {
        // init database
        $this->object->insert('ft.1', array('ftvalue' => 1));

        $this->object->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true ));

        $this->object->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false ));

        $this->object->insert('i.foo', array('ta' => array('1' => '1' ) ));

        // supposed to succeed
        $this->object->update('i.foo.ta.1.a', 2)->commit();
        $this->assertEquals(2, $this->object->select('i.foo.ta.1.a'));
    }

    /**
     * @test
     */
    public function testSelectToCsv()
    {
        $this->object->insert('ft.1', array('ftvalue' => 2))->commit();
        $select = new \Yana\Db\Queries\Select($this->object);
        $select->setTable('ft');
        $expected = '"1","2","";';
        $this->assertEquals($expected, $select->toCSV(',', ";", false));
        
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\ConstraintException
     */
    public function testInsertDuplicateKey()
    {
        $this->object->insert('ft.1', array('ftvalue' => 1))->commit();
        $this->object->insert('ft.1', array('ftvalue' => 1))->commit();
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\ConstraintException
     */
    public function testInsertDuplicateKey2()
    {
        $this->object->insert('ft.1', array('ftvalue' => 1))->insert('ft.1', array('ftvalue' => 1))->commit();
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
        $test = $this->object->equals($anotherObject);
        $this->assertFalse($test, 'assert failed, there are two different objects of dbstrem');

        $anotherObject = new \Yana\Core\Object();
        $test = $this->object->equals($anotherObject);
        $this->assertFalse($test, 'assert failed, there are two different objects');

        $sameObj = $this->object;
        $test = $this->object->equals($sameObj);
        $this->assertTrue($test, 'assert failed, there are the same objects');
    }

    /**
     * @test
     */
    public function testImportSqlEmpty()
    {
        $this->assertFalse($this->object->importSQL(CWD . 'resources/empty.sql'));
    }

    /**
     * @test
     */
    public function testImportSqlConstraintFailed()
    {
        $this->assertFalse($this->object->importSQL(CWD . 'resources/foo.sql'));
    }

    /**
     * @test
     */
    public function testImportSql()
    {
        $this->assertFalse($this->object->exists('t.FOO1'));
        $this->assertFalse($this->object->exists('t.FOO2'));

        $this->object->insert('ft.1', array())->commit();
        $this->assertTrue($this->object->importSQL(CWD . 'resources/foo.sql'));

        $this->object->commit();

        $this->assertTrue($this->object->exists('t.FOO1'));
        $this->assertTrue($this->object->exists('t.FOO2'));
    }

    /**
     * @test
     */
    public function testImportSqlArray()
    {
        $this->assertFalse($this->object->exists('t.FOO1'));
        $this->assertFalse($this->object->exists('t.FOO2'));

        $this->object->insert('ft.1', array())->commit();
        $this->assertTrue($this->object->importSQL(file(CWD . 'resources/foo.sql')));

        $this->object->commit();

        $this->assertTrue($this->object->exists('t.FOO1'));
        $this->assertTrue($this->object->exists('t.FOO2'));
    }

    /**
     * @test
     * @expectedException Yana\Db\Queries\Exceptions\ConstraintException
     */
    public function testInsertAndUpdateConstraintException()
    {
        // init database
        $this->object->insert('ft.1', array('ftvalue' => 1));
        $this->object->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true ));
        $this->object->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false ));
        $this->object->insert('i.foo', array('ta' => array('1' => '1' ) ));
        $this->object->update('i.foo.ta.1.a', 2);
        $this->object->commit();
        $this->object->insert('i.foo', array('ta' => array('1' => '1')));
        $this->object->commit();
    }

    /**
     * @test
     * @expectedException Yana\Db\Queries\Exceptions\ConstraintException
     */
    public function testInsertAndUpdateConstraintException2()
    {
        // init database
        $this->object->insert('ft.1', array('ftvalue' => 1));
        $this->object->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true ));
        $this->object->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false ));
        $this->object->insert('i.foo', array('ta' => array('1' => '1' ) ));
        $this->object->update('i.foo.ta.1.a', 2);
        $this->object->commit();
        $this->object->insert('i', array('iid' => 'foo', 'ta' => array('1' => '1')));
        $this->object->commit();
    }

    /**
     * @test
     * @expectedException Yana\Db\Queries\Exceptions\ConstraintException
     */
    public function testInsertAndUpdateConstraintException3()
    {
        // init database
        $this->object->insert('ft.1', array('ftvalue' => 1));
        $this->object->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true ));
        $this->object->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false ));
        $this->object->insert('i.foo', array('ta' => array('1' => '1' ) ));
        $this->object->update('i.foo.ta.1.a', 2);
        $this->object->commit();

        $this->object->insertOrUpdate('t.foo.ftid', 2);
        $this->object->commit();
    }

    /**
     * @test
     */
    public function testExists()
    {
        // init database
        $this->object->insert('ft.1', array('ftvalue' => 1));
        $this->object->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true ));
        $this->object->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false ));
        $this->object->insert('i.foo', array('ta' => array('1' => '1' ) ));
        $this->object->update('i.foo.ta.1.a', 2);
        $this->object->commit();

        // exists table
        $this->assertTrue($this->object->exists('t'), '"exists table" test failed');

        // exists row
        $this->assertTrue($this->object->exists('t.fOo'), '"exists row" test failed');

        // exists cell
        $this->assertTrue($this->object->exists('t.fOo.tid'));

        // exists column
        $this->assertTrue($this->object->exists('i.*.ta'));

        $this->assertFalse($this->object->exists('t.fooBar.tid'));
    }

    /**
     * @test
     */
    public function testLength()
    {
        // init database
        $this->object->insert('ft.1', array('ftvalue' => 1));
        $this->object->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true ));
        $this->object->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false ));
        $this->object->commit();

        $this->assertEquals($this->object->length('T'), 2, '"get column" test failed');
        $this->assertEquals($this->object->length('t'), 2, '"get column" test failed');
    }

    /**
     * @test
     */
    public function testSelect2()
    {
        // init database
        $this->object->insert('ft.1', array('ftvalue' => 1));
        $this->object->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true ));
        $this->object->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false ));
        $this->object->insert('i.foo', array('ta' => array('1' => '1' ) ));
        $this->object->update('i.foo.ta.1.a', 2);
        $this->object->commit();

        // get table
        $test = $this->object->select('T');
        $this->assertInternalType('array', $test, '"get table" the value should be of type array');
        $this->assertTrue(in_array('FOO', array_keys($test)), '"get table" the value should be contain the string name "FOO"');
        $this->assertInternalType('array', $test['FOO'], '"get table" the value should be of type array');

        // get row
        $test = $this->object->select('t.fOo');
        $this->assertInternalType('array', $test, '"get table" value should be of type array');
        $this->assertTrue(in_array('TVALUE', array_keys($test)), '"get table" test failed');

        // get cell (1)
        $test = $this->object->select('T.Foo.TValue');
        $this->assertEquals($test, 1, '"get cell 1" test failed');

        // get cell (2)
        $test = $this->object->select('t.fOo.tB');
        $this->assertTrue($test, '"get cell 2" test failed');

        // resolve foreign key
        $test = $this->object->select('t.fOo.ftid.ftvalue');
        $this->assertEquals($test, 1, '"resolving foreign key" test failed');

        // get column
        $test = $this->object->select('T.*.fTid');
        $this->assertInternalType('array', $test, '"get column" test failed');
        $this->assertEquals(2, count($test));
        $this->assertEquals(array('FOO' => '1', 'FOO3' => '1'), $test);

        // get primary key column
        $test = $this->object->select('T.*.tid');
        $this->assertInternalType('array', $test, '"get column" test failed');
        $this->assertEquals(count($test), 2, '"get column" test failed');
        $this->assertEquals(array('FOO' => 'FOO', 'FOO3' => 'FOO3'), $test);
    }

    /**
     * @test
     */
    public function testSelectLast()
    {
        // init database
        $this->object->insert('ft.1', array('ftvalue' => 1));
        $this->object->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true ));
        $this->object->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false ));
        $this->object->insert('i.foo', array('ta' => array('1' => '1' ) ));
        $this->object->update('i.foo.ta.1.a', 2);
        $this->object->commit();

        // get last entry
        $test = $this->object->select('t.?.tValue');
        $this->assertEquals($test, 3, '"get last entry" test failed');
    }

    /**
     * @test
     */
    public function testIsEmpty()
    {
        $this->assertTrue($this->object->isEmpty('t'));
        $this->assertTrue($this->object->isEmpty('T'));

        // init database
        $this->object->insert('ft.1', array('ftvalue' => 1));
        $this->object->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true ));
        $this->object->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false ));
        $this->object->commit();

        $this->assertFalse($this->object->isEmpty('t'));
        $this->assertFalse($this->object->isEmpty('T'));
    }

    /**
     * @test
     */
    public function testSelect()
    {
        // init database
        $this->object->insert('ft.1', array('ftvalue' => 1));
        $this->object->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true ));
        $this->object->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false ));
        $this->object->insert('i.foo', array('ta' => array('1' => '1' ) ));
        $this->object->update('i.foo.ta.1.a', 2);
        $this->object->commit();

        // test buffer
        $this->object->update('ft.3', array('ftvalue' => 3 ));
        $this->object->update('t.FOO3.ftid', 3); // supposed to succeed
        $test = $this->object->select('i.foo.ta.1.a'); // supposed to succeed
        $this->assertEquals($test, 2, '"get array content" failed');

        // rollback
        $this->object->reset();

        $test = $this->object->select('i.foo');
        $temp1 = array(1 => 2, 2 => 3);
        $temp2 = 2;
        $test['ta'] = $temp1 ;
        $test['tvalue'] = $temp2;
        $this->object->update('i.foo', $test);
        $this->object->commit();

        $this->assertEquals($this->object->select('i.foo.ta'), $temp1, '"update inheritance 2" test failed');
        $this->assertEquals($this->object->select('t.foo.tvalue'), $temp2, '"update inheritance 2" test failed');

        unset($temp1, $temp2);

        // rollback
        $this->object->reset();

        // multiple columns
        $dbQuery = new \Yana\Db\Queries\Select($this->object);
        $dbQuery->setTable('i');
        $dbQuery->setRow('foo');
        $dbQuery->setInnerJoin('t');
        $dbQuery->setColumns(array('i.iid', 'ta', 't.tvalue'));
        $test = $this->object->select($dbQuery);
        $test2 = array(
             "IID" => "FOO",
             "TA" => array(
                   1 => "2",
                   2 => "3"
                 ),
             "TVALUE" => 2
             );

        $this->assertEquals($test, $test2, '"get multiple columns" test failed');

        $this->assertTrue($this->object->exists($dbQuery), '"exists multiple columns" test failed');

        // test for property "unsigned"
        // column t.tf has unsigned constraint
        try{
            $this->object->update('t.foo.tf', -1);
            $this->fail('"unsigned" test failed');
        } catch (\Yana\Core\Exceptions\Forms\InvalidValueException $e) {
            $this->assertTrue(true);
        }

        // test for property "zerofill"
        // column t.ti is a zerofilled integer with length 4
        $this->object->update('t.foo.ti', 1);
        $this->object->commit();

        $this->assertEquals($this->object->select('t.foo.ti'), '0001', '"get zerofill" test failed');

        $test = $this->object->quote(null);
        $this->assertEquals($test, 'NULL', 'assert failed, the value should be "null"');

        // Reset the object to default values
        $this->object->rollback();
    }

}

?>