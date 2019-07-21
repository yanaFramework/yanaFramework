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
     * @test
     */
    public function testResetQuery()
    {
        $this->query->setTable('t');
        $columns = array('tid', 'tvalue', 'ti', 'ftid');
        $this->query->setColumns($columns);
        $this->assertSame(array(), $this->query->resetQuery()->getColumns());
    }

    /**
     * @test
     */
    public function testSetColumn()
    {
        $this->query->setTable('t');
        $this->query->setColumn('tid');
        $this->assertEquals('tid', $this->query->getColumn(0));
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
        $this->assertEquals(\Yana\Db\ResultEnumeration::TABLE, $this->query->getExpectedResult());
    }

    /**
     * @test
     */
    public function testSetColumnsWithAlias()
    {
        $this->query->setTable('t');
        $columns = array('a' => 'tid', 'b' => 'tvalue', 'c' => 'ti', 'd' => 'ftid');
        $this->query->setColumns($columns);
        $this->assertEquals($columns['a'], $this->query->getColumn('A'));
        $this->assertEquals($columns['b'], $this->query->getColumn('B'));
        $this->assertEquals($columns['c'], $this->query->getColumn('C'));
        $this->assertEquals($columns['d'], $this->query->getColumn('D'));
        $this->assertEquals('*', $this->query->getColumn('a'));
        $this->assertEquals(\Yana\Db\ResultEnumeration::TABLE, $this->query->getExpectedResult());
    }

    /**
     * @test
     */
    public function testSetColumnsWithRow()
    {
        $this->query->setTable('t');
        $columns = array('tid', 'tvalue', 'ti', 'ftid');
        $this->query->setRow('1')->setColumns($columns);
        $this->assertEquals(\Yana\Db\ResultEnumeration::ROW, $this->query->getExpectedResult());
    }

    /**
     * @test
     */
    public function testSetColumnsEmpty()
    {
        $this->query->setTable('t');
        $this->assertSame(array(), $this->query->setColumns(array('tid', 'tvalue', 'ti', 'ftid'))->setColumns()->getColumns());
    }

    /**
     * @test
     */
    public function testSetColumnsOne()
    {
        $this->query->setTable('t');
        $this->assertSame(array(array('t', 'tid')), $this->query->setColumns(array('tid'))->getColumns());
    }

    /**
     * @expectedException \Yana\Db\Queries\Exceptions\InvalidSyntaxException
     * @test
     */
    public function testSetColumnsInvalidSyntaxException()
    {
        $columns = array('tid', 'tvalue', 'ti', 'ftid');
        $this->query->setColumns($columns);
    }

    /**
     * @test
     */
    public function testAddColumn()
    {
        $this->query->setTable('t');
        $columns = array('tid', 'tvalue', 'ti', 'ftid');
        $this->query->addColumn($columns[0])->addColumn($columns[1])->addColumn($columns[2])->addColumn($columns[3]);
        $this->assertEquals($columns[0], $this->query->getColumn(0));
        $this->assertEquals($columns[1], $this->query->getColumn(1));
        $this->assertEquals($columns[2], $this->query->getColumn(2));
        $this->assertEquals($columns[3], $this->query->getColumn(3));
    }

    /**
     * @test
     */
    public function testAddColumnWithAlias()
    {
        $this->query->setTable('t');
        $this->query->addColumn('tid', 'alias');
        $this->assertEquals('tid', $this->query->getColumn('alias'));
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
     * @test
     */
    public function testSetOrderBy()
    {
        $this->query->setTable('ft');
        $this->assertSame(array(array('ft', 'ftid')), $this->query->setOrderBy(array('ftid'))->getOrderBy());
    }

    /**
     * @test
     */
    public function testGetOrderBy()
    {
        $this->assertSame(array(), $this->query->getOrderBy());
    }

    /**
     * @test
     */
    public function testGetDescending()
    {
        $this->assertSame(array(), $this->query->getDescending());
    }

    /**
     * @test
     */
    public function testSetHaving()
    {
        $this->query->setTable('t');
        $having = array('tvalue', '=', 1);
        $this->assertSame(array(array('t', 'tvalue'), '=', '1'), $this->query->setHaving($having)->getHaving());
    }

    /**
     * @test
     */
    public function testSetHavingPrimaryKey()
    {
        $this->query->setTable('t');
        $having = array('tid', '=', 1);
        $this->assertSame('1', $this->query->setHaving($having)->getRow());
        $this->assertSame(array(), $this->query->getHaving());
    }

    /**
     * @test
     */
    public function testSetHavingComplexQuery()
    {
        $this->query->setTable('t');
        $having = array(array('tid', '=', 1), 'or', array('tvalue', '=', 2));
        $expected = array(array(array('t', 'tid'), '=', '1'), 'or', array(array('t', 'tvalue'), '=', '2'));
        $this->assertSame($expected, $this->query->setHaving($having)->getHaving());
    }

    /**
     * @test
     */
    public function testAddHaving()
    {
        $this->query->setTable('t');
        $having = $this->query->setHaving(array('tvalue', '>', 1))->addHaving(array('tvalue', '=', 2))->getHaving();
        $expected = array(array(array('t', 'tvalue'), '>', '1'), 'and', array(array('t', 'tvalue'), '=', '2'));
        $this->assertSame($expected, $having);
    }

    /**
     * @test
     */
    public function testAddHavingWithOr()
    {
        $this->query->setTable('t');
        $having = $this->query->addHaving(array('tvalue', '>', 1), false)->addHaving(array('tvalue', '=', 2), false)->getHaving();
        $expected = array(array(array('t', 'tvalue'), '>', '1'), 'or', array(array('t', 'tvalue'), '=', '2'));
        $this->assertSame($expected, $having);
    }

    /**
     * @test
     */
    public function testGetHaving()
    {
        $this->assertSame(array(), $this->query->getHaving());
    }

    /**
     * @test
     */
    public function testGetHavingEmpty()
    {
        $this->query->setTable('t');
        $this->query->setHaving(array('tvalue', '>', 1));
        $this->assertSame(array(), $this->query->setHaving()->getHaving());
    }

    /**
     * @test
     */
    public function testGetLimit()
    {
        $this->assertSame(0, $this->query->getLimit());
    }

    /**
     * @test
     */
    public function testSetLimit()
    {
        $this->assertSame(2, $this->query->setLimit(2)->getLimit());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetLimitInvalidArgumentException()
    {
        $this->query->setLimit(-1);
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
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetOffsetInvalidArgumentException()
    {
        $this->query->setOffset(-1);
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
        // retrieve single cell from table
        $this->query->setColumn("tvalue");
        $actual = $this->query->toCSV();
        $expected = "\"tvalue\"\n\"1\"\n";
        $this->assertEquals($expected, $actual, "CSV export invalid when querying cell.");
    }

    /**
     * @test
     */
    public function testGetColumnTitles()
    {
        $this->assertEquals(array(), $this->query->getColumnTitles());
    }

    /**
     * @test
     */
    public function testGetColumnTitlesEmpty()
    {
        $this->query->setTable('t')->setColumns(array('tid', 'tvalue'));
        $this->assertEquals(array('tid', 'tvalue'), $this->query->getColumnTitles());
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
     * @expectedException \Yana\Db\Queries\Exceptions\ConstraintException
     */
    public function testSetInnerJoinWrongJoinOrder()
    {
        $query = new \Yana\Db\Queries\Select($this->db);
        $query->useInheritance(true)->setTable('t');
        $query->setInnerJoin('i'); // Wrong join order... let's see what breaks first!
    }

    /**
     * @test
     */
    public function testSetLeftJoin()
    {
        $this->query->setTable('t');
        $columns = array('tid', 'tvalue', 'ti', 'ftid');
        $this->query->setColumns($columns);
        $this->query->setLeftJoin('ft', 'ftid', 't', 'ftid');
        $this->assertTrue($this->query->getJoin('ft')->isLeftJoin());
        $valid = "SELECT t.tid, t.tvalue, t.ti, t.ftid FROM t LEFT JOIN ft ON t.ftid = ft.ftid";
        $this->assertEquals($valid, (string) $this->query);
    }

    /**
     * @test
     */
    public function testSetNaturalJoin()
    {
        $this->query->setTable('t');
        $join = $this->query->setNaturalJoin('ft')->getJoin('ft');
        $this->assertFalse($join->isNaturalJoin());
        $this->assertTrue($join->isInnerJoin());
        $this->assertEquals([['t', 'ftid'], '=', ['ft', 'ftid']], $this->query->getWhere());
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\TableNotFoundException
     */
    public function testSetNaturalJoinTableNotFoundException()
    {
        $this->query->setTable('t')->setNaturalJoin('no-such-table');
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\ConstraintException
     */
    public function testSetNaturalJoinConstraintException()
    {
        $this->query->setTable('t')->setNaturalJoin('u');
    }

    /**
     * @test
     */
    public function testCountResults()
    {
        $this->query->setTable('t');
        $this->assertSame(0, $this->query->countResults());
    }

    /**
     * @test
     */
    public function testGetResults()
    {
        $this->query->setTable('t');
        $this->assertSame(array(), $this->query->getResults());
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
        $valid = "SELECT t.tid, t.tvalue FROM t WHERE t.ftid = " . \YANA_DB_DELIMITER . "1" . \YANA_DB_DELIMITER .
            " HAVING t.tvalue > " . \YANA_DB_DELIMITER . "20" . \YANA_DB_DELIMITER;
        $this->assertEquals($valid, (string) $this->query, 'assert failed, the sql select statements must be equal');
    }

    /**
     * @test
     */
    public function testToString()
    {
        $this->query->setTable('t')->setColumns(array('tid', 'tvalue', 'ti', 'ftid'));
        $this->assertEquals("SELECT t.tid, t.tvalue, t.ti, t.ftid FROM t", (string) $this->query);
    }

}
