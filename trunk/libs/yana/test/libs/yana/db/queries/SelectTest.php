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

namespace Yana\Db\Queries;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class SelectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Db\Queries\Select
     */
    protected $query;

    /**
     * @var  \Yana\Db\FileDb\Connection
     */
    protected $db;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        try {
            chdir(CWD . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
            if (!isset($this->db)) {
                $schema = \Yana\Files\XDDL::getDatabase('check');
                $this->db = new \Yana\Db\FileDb\Connection($schema);
            }
            // reset database
            $this->db->remove('i', array(), 0);
            $this->db->remove('t', array(), 0);
            $this->db->remove('ft', array(), 0);
            $this->db->commit();
            $this->query = new \Yana\Db\Queries\Select($this->db);
        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        chdir(CWD);
    }

    /**
     * @covers Yana\Db\Queries\Select::resetQuery
     * @todo   Implement testResetQuery().
     */
    public function testResetQuery()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @test
     */
    public function testSetColumns()
    {
        $this->query->setTable('t');
        $columns = array('tid', 'tvalue', 'ti', 'ftid');
        $this->query->setColumns($columns);
        $this->assertEquals($columns[0], $this->query->getColumn(0));
        $this->assertEquals($columns[1], $this->query->getColumn(1));
        $this->assertEquals($columns[2], $this->query->getColumn(2));
        $this->assertEquals($columns[3], $this->query->getColumn(3));
    }

    /**
     * @covers Yana\Db\Queries\Select::addColumn
     * @todo   Implement testAddColumn().
     */
    public function testAddColumn()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @test
     */
    public function testGetArrayAddress()
    {
        $this->assertEquals('', $this->query->getArrayAddress());
    }

    /**
     * build select statement using query builder
     *
     * @test
     */
    public function testSetArrayAddress()
    {
        $this->query->setTable('t');
        $arrayAddress = 'foo.bar';
        $this->query->setColumn('ta');
        $this->query->setArrayAddress($arrayAddress);
        $fooBar = $this->query->getArrayAddress();
        $this->assertEquals($fooBar, $arrayAddress, 'Returned array address does not match.');

        $this->query->resetQuery();
        $empty = $this->query->getArrayAddress();
        $this->assertTrue(empty($empty), 'Array address was not reset');
    }

    /**
     * @covers Yana\Db\Queries\Select::setOrderBy
     * @todo   Implement testSetOrderBy().
     */
    public function testSetOrderBy()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\Queries\Select::getOrderBy
     * @todo   Implement testGetOrderBy().
     */
    public function testGetOrderBy()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\Queries\Select::getDescending
     * @todo   Implement testGetDescending().
     */
    public function testGetDescending()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\Queries\Select::setHaving
     * @todo   Implement testSetHaving().
     */
    public function testSetHaving()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\Queries\Select::addHaving
     * @todo   Implement testAddHaving().
     */
    public function testAddHaving()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\Queries\Select::getHaving
     * @todo   Implement testGetHaving().
     */
    public function testGetHaving()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\Queries\Select::setLimit
     * @todo   Implement testSetLimit().
     */
    public function testSetLimit()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @test
     */
    public function testGetOffset()
    {
        $this->assertEquals(0, $this->query->getOffset());
    }

    /**
     * @test
     */
    public function testSetOffset()
    {
        $this->assertEquals(20, $this->query->setOffset(20)->getOffset());
    }

    /**
     * @test
     */
    public function testToCSV()
    {
        // add expected value
        $row = array('ftid' => 1, 'ftvalue' => 1);
        $this->db->insert('ft', $row);
        $row = array('tid' => '1', 'tvalue' => 1, 'tb' => true, 'ftid' => 1);
        $this->db->insert('t', $row);
        $this->db->commit();
        // retrieve rows from table
        $this->query = new \Yana\Db\Queries\Select($this->db);
        $this->query->setTable('t');
        $this->query->setColumns(array('tid', 'tvalue', 'tb'));
        $actual = $this->query->toCSV();
        $expected = '"tid";"tvalue";"tb"' . "\n" . '"1";"1";"1"' . "\n";
        $this->assertEquals($expected, $actual, "CSV export invalid when querying table.");
        // retriev single row from table
        $this->query->setRow('1');
        $actual = $this->query->toCSV();
        $this->assertEquals($expected, $actual, "CSV export invalid when querying row.");
        // retriev single cell from table
        $this->query->setColumn("tvalue");
        $actual = $this->query->toCSV();
        $expected = "\"tvalue\"\n\"1\"\n";
        $this->assertEquals($expected, $actual, "CSV export invalid when querying cell.");
    }

    /**
     * @covers Yana\Db\Queries\Select::getColumnTitles
     * @todo   Implement testGetColumnTitles().
     */
    public function testGetColumnTitles()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @test
     */
    public function testSetInnerJoin()
    {
        $this->query->setTable('t');
        $columns = array('tid', 'tvalue', 'ti', 'ftid');
        $this->query->setColumns($columns);
        $this->query->setInnerJoin('ft', 'ftid', 't', 'ftid');
        $sql = (string) $this->query;
        $expectedSQL = "SELECT t.tid, t.tvalue, t.ti, t.ftid FROM t JOIN ft ON t.ftid = ft.ftid";
        $this->assertEquals($expectedSQL, $sql, 'assert failed the sql select statements must be equal');
    }

    /**
     * @test
     */
    public function testSetInnerJoin2()
    {
        $this->query->setTable('t');
        $columns = array('tid', 'tvalue', 'ti', 'ftid');
        $this->query->setColumns($columns);
        $this->query->setInnerJoin('ft');
        $join = $this->query->getJoin('ft');
        $this->assertEquals('ftid', $join->getForeignKey());
        $this->assertEquals('ftid', $join->getTargetKey());
        $this->assertEquals('ft', $join->getJoinedTableName());
        $this->assertEquals('t', $join->getSourceTableName());
        $this->assertTrue($join->isInnerJoin());
    }

    /**
     * @test
     */
    public function testSetLeftJoin()
    {
        $this->query->setTable('t');
        $columns = array('tid', 'tvalue', 'ti', 'ftid');
        $this->query->setColumns($columns);
        $this->query->setInnerJoin('ft');
        $join = $this->query->getJoin('ft');


        $this->assertEquals('ftid', $join->getForeignKey());
        $this->assertEquals('ftid', $join->getTargetKey());
        $this->assertEquals('ft', $join->getJoinedTableName());
        $this->assertEquals('t', $join->getSourceTableName());
        $this->assertTrue($join->isInnerJoin());

        $this->query->unsetJoin('ft');
        $getJoin = $this->query->getJoin('ft');
        $this->assertFalse($getJoin, 'assert failed, the join ft does not exist');
        $s4 = (string) $this->query;
        $valid = "SELECT t.tid, t.tvalue, t.ti, t.ftid FROM t";
        $this->assertEquals($valid, $s4, 'assert failed, the expected sql select statement must be equal');
        $this->query->setLeftJoin('ft', 'ftid', 't', 'ftid');
        $getJoin = $this->query->getJoin('ft');
        $this->assertEquals(array('ftid', 'ftid', true), $getJoin, 'Join clause must match ftid=ftid, leftJoin=true');
        $s4 = (string) $this->query;
        $valid = "SELECT t.tid, t.tvalue, t.ti, t.ftid FROM t LEFT JOIN ft ON t.ftid = ft.ftid";
        $this->assertEquals($valid, $s4, 'assert failed, the expected sql select statement must be equal');
    }

    /**
     * @covers Yana\Db\Queries\Select::setNaturalJoin
     * @todo   Implement testSetNaturalJoin().
     */
    public function testSetNaturalJoin()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\Queries\Select::countResults
     * @todo   Implement testCountResults().
     */
    public function testCountResults()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\Queries\Select::getResults
     * @todo   Implement testGetResults().
     */
    public function testGetResults()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * build select statement using query builder
     *
     * @test
     */
    public function testSelect1()
    {
        $this->query->setTable('ft');
        $this->query->setColumn('ftid');
        $this->query->setRow('1');
        $this->query->setOrderBy(array('ftid'));
        $sql =  (string) $this->query;
        $valid = "SELECT ft.ftid FROM ft WHERE ft.ftid = " . \YANA_DB_DELIMITER . "1" . \YANA_DB_DELIMITER . " ORDER BY ft.ftid";
        $this->assertEquals($valid, $sql);

        $this->query->resetQuery();

        // Select
        $this->query->setTable('t');
        $columns = array('tid', 'tvalue', 'ti', 'ftid');
        $this->query->setColumns($columns);

        $getAll = $this->query->getColumns();
        foreach($getAll as $key=>$entries)
        {
            $this->assertTrue(in_array($columns[$key], $entries));
        }
        $this->query->setWhere(array('ftid', '=', 2));
        $this->query->setKey('t');
        $this->query->setOrderBy(array('tvalue'));
        $this->query->setLimit(20);
        $getLimit = $this->query->getLimit();
        $this->assertEquals(20, $getLimit, 'assert failed, the expected value needs to be 20');
        $s2 = (string) $this->query;
        $valid = "SELECT * FROM t WHERE t.ftid = " . \YANA_DB_DELIMITER . "2" . \YANA_DB_DELIMITER . " ORDER BY t.tvalue";
        $this->assertEquals($valid, $s2, 'assert failed, the sql select statements must be equal');
    }

    /**
     * build select statement with having clause using query builder
     *
     * @test
     */
    public function testWithHaving()
    {
        $this->query->setTable('t');
        $this->query->setColumns(array('tid', 'tvalue'));
        $where = array('ftid', '=', '1');
        $this->query->setWhere($where);

        $this->query->setHaving(array('tvalue', '>', '20'));
        $getHaving = $this->query->getHaving();
        $expected = array(array('t', 'tvalue'), '>', '20');
        $this->assertEquals($expected, $getHaving, 'assert failed, the values must be equal');
        $string = (string) $this->query;
        $valid = "SELECT t.tid, t.tvalue FROM t WHERE t.ftid = '1' HAVING t.tvalue > '20'";
        $this->assertEquals($valid, $string, 'assert failed, the sql select statements must be equal');
    }

}
