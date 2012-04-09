<?php
/**
 * PHPUnit test-case: DbQueryParser
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
 * Test class for DbQueryParser
 *
 * @package  test
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var    \Yana\Db\Queries\Parser
     * @access protected
     */
    protected $parser;
    /**
     * @var    \Yana\Db\Queries\AbstractQuery
     * @access protected
     */
    protected $query;
    /**
     * @var    \Yana\Db\FileDb\Connection
     * @access protected
     */
    protected $db;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        try {
            \Yana\Db\FileDb\Driver::setBaseDirectory(CWD. 'resources/');
            chdir(CWD . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
            if (!isset($this->db)) {
                \Yana\Db\DDl\DDL::setDirectory(CWD. 'resources/');
                $schema = \XDDL::getDatabase('check');
                $this->db = new \Yana\Db\FileDb\Connection($schema);
            }
            $this->parser = new \Yana\Db\Queries\Parser($this->db);
        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        chdir(CWD);
    }

    /**
     * exists
     *
     * @test
     */
    public function testExists()
    {
        $sqlStmt = 'SELECT 1 FROM ft WHERE ft.ftid = "2"';
        $this->query = $this->parser->parseSQL($sqlStmt, $this->db);
        $this->assertTrue($this->query instanceof \Yana\Db\Queries\SelectExist, "Parser error: $sqlStmt");
        $sqlResult = (string) $this->query;
        $this->assertEquals($sqlStmt, $sqlResult, "Statement not resolved: $sqlResult");
    }

    /**
     * alternative writing
     *
     * @test
     */
    public function testAlternativeWriting1()
    {
        $sql1 = 'SELECT 1 FROM ft WHERE ft.ftid = "2"';
        $sql3 = 'SELECT 1 FROM ft WHERE ftid = "2"';
        $this->query = $this->parser->parseSQL($sql3, $this->db);
        $this->assertTrue($this->query instanceof \Yana\Db\Queries\SelectExist, "Parser error: $sql3");
        $sql2 = (string) $this->query;
        $this->assertEquals($sql1, $sql2, "Statement not resolved: $sql2");
    }

    /**
     * alternative writing
     *
     * @test
     */
    public function testAlternativeWriting2()
    {
        $sql1 = 'SELECT ft.ftid FROM ft WHERE ft.ftid = "1" ORDER BY ft.ftid';
        $sql3 = 'SELECT ft.ftid FROM ft WHERE ft.ftid = "1" ORDER BY ftid';
        $this->query = $this->parser->parseSQL($sql3, $this->db);
        $this->assertTrue($this->query instanceof \Yana\Db\Queries\SelectExist, "Parser error: $sql3");
        $sql2 = (string) $this->query;
        $this->assertEquals($sql1, $sql2, "Statement not resolved: $sql2");
    }

    /**
     * alternative writing
     *
     * @test
     */
    public function testAlternativeWriting3()
    {
        $sql1 = 'SELECT * FROM ft WHERE ft.ftid = "1" ORDER BY ft.ftid DESC';
        $sql3 = 'SELECT * FROM ft WHERE ft.ftid = "1" ORDER BY ftid DESC';
        $this->query = $this->parser->parseSQL($sql3, $this->db);
        $this->assertTrue($this->query instanceof \Yana\Db\Queries\SelectExist, "Parser error: $sql3");
        $sql2 = (string) $this->query;
        $this->assertEquals($sql1, $sql2, "Statement not resolved: $sql2");
    }

    /**
     * parse delete statement
     *
     * @test
     */
    public function testDelete()
    {
        $sql1 = 'DELETE FROM ft WHERE ft.ftvalue = "0"';
        $this->query = $this->parser->parseSQL($sql1, $this->db);
        $this->assertTrue($this->query instanceof \Yana\Db\Queries\Delete, "Parser error: $sql1");
        $sql2 = (string) $this->query;
        $this->assertEquals($sql1, $sql2, "Statement not resolved: $sql2");

        $sql1 = 'DELETE FROM ft WHERE ft.ftvalue = "1"';
        $this->query = $this->parser->parseSQL($sql1, $this->db);
        $this->assertTrue($this->query instanceof \Yana\Db\Queries\Delete, "Parser error: $sql1");
        $sql2 = (string) $this->query;
        $this->assertEquals($sql1, $sql2, "Statement not resolved: $sql2");
    }

    /**
     * test Delete Invalid Argument Exception
     *
     * This is supposed to fail (due to missing foreign key).
     *
     * @expectedException ParserError
     * @test
     */
    public function testDeleteInvalidArgumentException()
    {
        $sql1 = 'DELETE t&t FROM ft WHERE ft.ftvalue = "1"';
        $result = $this->query = $this->parser->parseSQL($sql1, $this->db);
    }

    /**
     * length
     *
     * @test
     */
    public function testLength()
    {
        $sql1 = 'SELECT count(*) FROM ft WHERE ft.ftid = "2"';
        $this->query = $this->parser->parseSQL($sql1, $this->db);
        $this->assertTrue($this->query instanceof \Yana\Db\Queries\SelectCount, "Parser error: $sql1");
        $sql2 = (string) $this->query;
        $this->assertEquals($sql1, $sql2, "Statement not resolved: $sql2");
    }

    /**
     * select table
     *
     * @test
     */
    public function testSelectTable()
    {
        $sql1 = 'SELECT * FROM ft';
        $this->query = $this->parser->parseSQL($sql1, $this->db);
        $this->assertTrue($this->query instanceof \Yana\Db\Queries\Select, "Parser error: $sql1");
        $sql2 = (string) $this->query;
        $this->assertEquals($sql1, $sql2, "Statement not resolved: $sql2");
    }

    /**
     * select row
     *
     * @test
     */
    public function testSelectRow()
    {
        $sql1 = 'SELECT * FROM ft WHERE ft.ftid = "2"';
        $this->query = $this->parser->parseSQL($sql1, $this->db);
        $this->assertTrue($this->query instanceof \Yana\Db\Queries\Select, "Parser error: $sql1");
        $sql2 = (string) $this->query;
        $this->assertEquals($sql1, $sql2, "Statement not resolved: $sql2");
    }

    /**
     * select column
     *
     * @test
     */
    public function testSelectColumn()
    {
        $sql1 = 'SELECT ftid FROM ft WHERE ftid = "2" ;';
        $this->query = $this->parser->parseSQL($sql1, $this->db);
        $this->assertTrue($this->query instanceof \Yana\Db\Queries\Select, "Parser error: $sql1");
        $sql2 = (string) $this->query;
        $expected = 'SELECT ft.ftid FROM ft WHERE ft.ftid = "2"';
        $this->assertEquals($expected, $sql2, "Statement not resolved: $sql2");
    }

    /**
     * add column
     *
     * @test
     */
    public function testAddColumn()
    {
        $sql1 = 'SELECT ftid FROM ft WHERE ftid = "2" ;';
        $this->query = $this->parser->parseSQL($sql1, $this->db);
        $this->query->addColumn('ftvalue');
        $columns = $this->query->getColumns();
        $expected = array(array('ft', 'ftid'), array('ft', 'ftvalue'));
        $this->assertEquals($expected, $columns, "Unable to add column to select query.");
        $resultType = $this->query->getExpectedResult();
        $this->assertEquals(\Yana\Db\ResultEnumeration::ROW, $resultType, "Auto-revalidation of result type failed.");
    }

    /**
     * select cross join
     *
     * This is supposed to fail (due to cross join).
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testSelectCrossJoin()
    {
        $sql1 = 'SELECT ft.ftid FROM ft,t WHERE ft.ftid = "2"';
        $this->parser->parseSQL($sql1, $this->db);
    }

    /**
     * select missing foreign key InvalidException
     *
     * This is supposed to fail (due to missing foreign key).
     *
     * @expectedException MissingFieldWarning
     * @test
     */
    public function testSelectMissingForeignKeyInvalidException()
    {
        $sql1 = 'INSERT INTO t (tid, tvalue) VALUES ("1", "2")';
        $this->parser->parseSQL($sql1, $this->db);
    }

    /**
     * insert into
     *
     * @test
     */
    public function testInsertInto()
    {
        $sql1 = 'INSERT INTO t (tid, tvalue, ftid) VALUES ("1", "2", "1");';
        $this->query = $this->parser->parseSQL($sql1, $this->db);
        $this->assertTrue($this->query instanceof \Yana\Db\Queries\Insert, "Parser error: $sql1");
        $sql2 = (string) $this->query;
        $sql3 = 'INSERT INTO t (tid, tvalue, ftid) VALUES ("1", "2", "1")';
        $this->assertEquals($sql3, $sql2, "Statement not resolved: $sql2");
    }

    /**
     * insert into (missing primary key)
     *
     * @expectedException DbWarningLog
     * @test
     */
    public function testInsertIntoMissingPrimaryKey()
    {
        $sql1 = 'INSERT INTO ft(ftvalue) VALUES("2")';
        // supposed to fail (due to missing primary key)
        $this->parser->parseSQL($sql1, $this->db);
    }

    /**
     * update
     *
     * @test
     */
    public function testUpdate()
    {
        $sql1 = 'UPDATE t SET tvalue="2" WHERE tid = "2"';
        $this->query = $this->parser->parseSQL($sql1, $this->db);
        $this->assertTrue($this->query instanceof \Yana\Db\Queries\Update, "Parser error: $sql1");
        $sql2 = (string) $this->query;
        $sql3 = 'UPDATE t SET tvalue = "2" WHERE t.tid = "2"';
        $this->assertEquals($sql3, $sql2, "Statement not resolved: $sql2");
    }

    /**
     * delete table
     *
     * @test
     */
    public function testDeleteTable()
    {
        $sql1 = 'DELETE FROM ft';
        $this->query = $this->parser->parseSQL($sql1, $this->db);
        $this->assertTrue($this->query instanceof \Yana\Db\Queries\Delete, "Parser error: $sql1");
        $sql2 = (string) $this->query;
        $this->assertEquals($sql1, $sql2, "Statement not resolved: $sql2");
    }

    /**
     * build select statement using query builder
     *
     * @test
     */
    public function testBuildSelectWithArrayAddress()
    {
        // Select
        $this->query = new \Yana\Db\Queries\Select($this->db);
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
     * build select statement using query builder
     *
     * @test
     */
    public function testBuildSelect1()
    {
        // Select
        $this->query = new \Yana\Db\Queries\Select($this->db);
        $this->query->setTable('ft');
        $this->query->setColumn('ftid');
        $this->query->setRow('1');
        $this->query->setOrderBy(array('ftid'));
        $s1 =  (string) $this->query;
        $valid = 'SELECT ft.ftid FROM ft WHERE ft.ftid = "1" ORDER BY ft.ftid';
        $this->assertEquals($valid, $s1, 'assert failed, the sql select statements must be equal');

        $this->query->resetQuery();

        // Select
        $this->query->setTable('t');
        $columns = array('tid', 'tvalue', 'ti', 'ftid');
        $this->query->setColumns($columns);
        $this->assertEquals('tid', $this->query->getColumn(0), 'assert failed, the expected value "tid" should be match the givin entry');
        $this->assertEquals('tvalue', $this->query->getColumn(1), 'assert failed, the expected value "tid" should be match the givin entry');
        $this->assertEquals('ti', $this->query->getColumn(2), 'assert failed, the expected value "tid" should be match the givin entry');
        $this->assertEquals('ftid', $this->query->getColumn(3), 'assert failed, the expected value "tid" should be match the givin entry');

        $getAll = $this->query->getColumns();
        foreach($getAll as $key=>$entries)
        {
            $this->assertTrue(in_array($columns[$key], $entries), "assert failed, the expected value $columns[$key] should be match a value in giving array");
        }
        $this->query->setWhere(array('ftid', '=', 2));
        $this->query->setKey('t');
        $this->query->setOrderBy(array('tvalue'));
        $this->query->setLimit(20);
        $getLimit = $this->query->getLimit();
        $this->assertEquals(20, $getLimit, 'assert failed, the expected value needs to be 20');
        $s2 = (string) $this->query;
        $valid = 'SELECT * FROM t WHERE t.ftid = "2" ORDER BY t.tvalue';
        $this->assertEquals($valid, $s2, 'assert failed, the sql select statements must be equal');
    }

    /**
     * build select statement using query builder
     *
     * @test
     */
    public function testBuildSelectInnerJoin()
    {
        // Select
        $this->query = new \Yana\Db\Queries\Select($this->db);
        $this->query->setTable('t');
        $columns = array('tid', 'tvalue', 'ti', 'ftid');
        $this->query->setColumns($columns);
        $this->query->setInnerJoin('ft', 'ftid', 'ftid');
        $getJoin = $this->query->getJoin('ft');
        $this->assertEquals(array('ftid', 'ftid', false), $getJoin, 'Join clause must match ftid=ftid, leftJoin=false');
        $s3 = (string) $this->query;
        $valid = 'SELECT t.tid, t.tvalue, t.ti, t.ftid FROM t, ft  WHERE t.ftid = ft.ftid';
        $this->assertEquals($valid, $s3, 'assert failed the sql select statements must be equal');
    }

    /**
     * build select statement using query builder
     *
     * @test
     */
    public function testBuildSelectLeftJoin()
    {
        // Select
        $this->query = new \Yana\Db\Queries\Select($this->db);
        $this->query->setTable('t');
        $columns = array('tid', 'tvalue', 'ti', 'ftid');
        $this->query->setColumns($columns);
        $setOffset = $this->query->setOffset(20);
        $this->assertTrue($setOffset, 'assert failed, the offset are not set');
        $this->query->setInnerJoin('ft');
        $getJoins = $this->query->getJoins();
        $this->assertType('array', $getJoins, 'assert failed, the expected value should be of type array');
        $this->assertArrayHasKey('ft', $getJoins, 'assert failed, the array shuld have the expected key "ft"');
        $this->assertTrue(in_array('ftid', $getJoins['ft']), 'assert failed, the value ftid must be match an entrie in givin array');
        $unsetJoin = $this->query->unsetJoin('ft');
        $this->assertTrue($unsetJoin, 'assert failed, the join is still avalible');
        $getJoin = $this->query->getJoin('ft');
        $this->assertFalse($getJoin, 'assert failed, the join ft does not exist');
        $s4 = (string) $this->query;
        $valid = 'SELECT t.tid, t.tvalue, t.ti, t.ftid FROM t';
        $this->assertEquals($valid, $s4, 'assert failed, the expected sql select statement must be equal');
        $this->query->setLeftJoin('ft', 'ftid', 'ftid');
        $getJoin = $this->query->getJoin('ft');
        $this->assertEquals(array('ftid', 'ftid', true), $getJoin, 'Join clause must match ftid=ftid, leftJoin=true');
        $s4 = (string) $this->query;
        $valid = 'SELECT t.tid, t.tvalue, t.ti, t.ftid FROM t LEFT JOIN ft ON t.ftid = ft.ftid';
        $this->assertEquals($valid, $s4, 'assert failed, the expected sql select statement must be equal');
    }

    /**
     * build delete statement using query builder
     *
     * @test
     */
    public function testBuildDelete()
    {
        // Delete
        $this->query = new \Yana\Db\Queries\Delete($this->db);
        $this->query->setTable('ft');
        $this->query->setRow(2);
        $this->query->useInheritance(true);
        $s5 = (string) $this->query;
        $valid = 'DELETE FROM ft WHERE ft.ftid = "2"';
        $this->assertEquals($valid, $s5, 'assert failed, the expected sql delete statement must be equal');
    }

    /**
     * build select statement with having clause using query builder
     *
     * @test
     */
    public function testBuildInsert()
    {
        // Insert
        $this->query = new \Yana\Db\Queries\Insert($this->db);
        $this->query->setTable('ft');
        $values = array('ftid' => 2,'ftvalue' => '50');
        $this->query->setValues($values);
        $getValues = $this->query->getValues();
        $this->assertType('array', $getValues, 'assert failed, the value should be of type array');
        $this->assertTrue(in_array(50, $getValues), 'assert failed, the expected value 50 should be match an enry in givin array');
        $s6 = (string) $this->query;
        $valid = 'INSERT INTO ft (ftid, ftvalue) VALUES ("2", "50")';
        $this->assertEquals($valid, $s6, 'assert failed, the expected sql insert statement must be equal');

        $this->query->resetQuery();
        $this->query->setRow('abc');
        $serialize = $this->query->toId();
        $result = unserialize($serialize);
        $this->assertTrue(in_array('abc', $result), 'assert failed, the expected string "abc" must match a value in the array');
    }

    /**
     * build select statement with having clause using query builder
     *
     * @test
     */
    public function testBuildSelectWithHaving()
    {
        // Select
        $this->query = new \Yana\Db\Queries\Select($this->db);
        $this->query->setTable('t');
        $this->query->setColumns(array('tid', 'tvalue'));
        $where = array('ftid', '=', '1');
        $this->query->setWhere($where);

        $this->query->setHaving(array('tvalue', '>', '20'));
        $getHaving = $this->query->getHaving();
        $expected = array(array('t', 'tvalue'), '>', '20');
        $this->assertEquals($expected, $getHaving, 'assert failed, the values must be equal');
        $string = (string) $this->query;
        $valid = 'SELECT t.tid, t.tvalue FROM t WHERE t.ftid = "1" HAVING t.tvalue > "20"';
        $this->assertEquals($valid, $string, 'assert failed, the sql select statements must be equal');
    }

    /**
     * test csv export
     *
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
        $this->query->setRow("1");
        $actual = $this->query->toCSV();
        $this->assertEquals($expected, $actual, "CSV export invalid when querying row.");
        // retriev single cell from table
        $this->query->setColumn("tvalue");
        $actual = $this->query->toCSV();
        $expected = "\"tvalue\"\n\"1\"\n";
        $this->assertEquals($expected, $actual, "CSV export invalid when querying cell.");
    }

}

?>