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
declare(strict_types=1);

namespace Yana\Db\Queries;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class QuerySerializerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Db\FileDb\Connection
     */
    protected $db;

    /**
     * @var QuerySerializer
     */
    protected $object;

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
        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
        $this->object = new \Yana\Db\Queries\QuerySerializer();
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
    public function testFromInsertQuery()
    {
        $query = new \Yana\Db\Queries\Insert($this->db);
        $query->setTable('ft');
        $values = array('ftid' => 2,'ftvalue' => '50');
        $query->setValues($values);
        $getValues = $query->getValues();
        $this->assertInternalType('array', $getValues, 'assert failed, the value should be of type array');
        $this->assertTrue(in_array(50, $getValues), 'assert failed, the expected value 50 should be match an enry in givin array');
        $expected = "INSERT INTO ft (ftid, ftvalue) "
            . "VALUES (2, 50)";
        $this->assertSame($expected, $this->object->fromInsertQuery($query));
    }

    /**
     * @test
     */
    public function testFromUpdateQuery()
    {
        $query = new \Yana\Db\Queries\Update($this->db);
        $query->setTable('ft');
        $values = array('ftid' => 2,'ftvalue' => '50');
        $query->setValues($values);
        $getValues = $query->getValues();
        $query->setRow(2);
        $this->assertInternalType('array', $getValues, 'assert failed, the value should be of type array');
        $this->assertTrue(in_array(50, $getValues), 'assert failed, the expected value 50 should be match an enry in givin array');
        $expected = "UPDATE ft SET ft.ftid = 2, ft.ftvalue = 50 WHERE ft.ftid = " . \YANA_DB_DELIMITER . "2" . \YANA_DB_DELIMITER;
        $this->assertSame($expected, $this->object->fromUpdateQuery($query));
    }

    /**
     * @test
     */
    public function testFromDeleteQuery()
    {
        $query = new \Yana\Db\Queries\Delete($this->db);
        $query->setTable('ft');
        $query->setRow(2);
        $query->useInheritance(true);
        $expected = "DELETE FROM ft WHERE ft.ftid = " . \YANA_DB_DELIMITER . "2" . \YANA_DB_DELIMITER;
        $this->assertSame($expected, $this->object->fromDeleteQuery($query));
    }

    /**
     * @test
     */
    public function testFromExistsQuery()
    {
        $query = new \Yana\Db\Queries\SelectExist($this->db);
        $query->setTable('t');
        $expected = 'SELECT 1 FROM t';
        $this->assertSame($expected, $this->object->fromExistsQuery($query));
    }

    /**
     * @test
     */
    public function testToStringWithRow()
    {
        $query = new \Yana\Db\Queries\SelectExist($this->db);
        $query->setTable('t')->setRow('1');
        $expected = "SELECT 1 FROM t WHERE t.tid = " . \YANA_DB_DELIMITER . '1' . \YANA_DB_DELIMITER;
        $this->assertSame($expected, $this->object->fromExistsQuery($query));
    }

    /**
     * @test
     */
    public function testToStringWithWhere()
    {
        $query = new \Yana\Db\Queries\SelectExist($this->db);
        $query->setTable('t')->setRow('1')->addWhere(array('tvalue', '>', '2'))->addWhere(array('tvalue', '<', '5'));
//        $expected = 'SELECT 1 FROM t WHERE t.tid = ? AND t.tvalue < ? AND t.tvalue > ?';
        $expected = 'SELECT 1 FROM t WHERE t.tid = ' . \YANA_DB_DELIMITER . '1' . \YANA_DB_DELIMITER .
            ' AND t.tvalue < ' . \YANA_DB_DELIMITER . '5' . \YANA_DB_DELIMITER .
            ' AND t.tvalue > ' . \YANA_DB_DELIMITER . '2' . \YANA_DB_DELIMITER;
        $this->assertSame($expected, $this->object->fromExistsQuery($query));
    }

    /**
     * @test
     */
    public function testToStringWitJoin()
    {
        $query = new \Yana\Db\Queries\SelectExist($this->db);
        $query->setTable('t')->setInnerJoin('ft', 'ftid')->setRow('1')->addWhere(array('tvalue', '>', '2'))->addWhere(array('tvalue', '<', '5'));
//        $expected = 'SELECT 1 FROM t JOIN ft ON t.ftid = ft.ftid WHERE t.tid = ? AND t.tvalue < ? AND t.tvalue > ?';
        $expected = 'SELECT 1 FROM t JOIN ft ON t.ftid = ft.ftid WHERE t.tid = ' . \YANA_DB_DELIMITER . '1' . \YANA_DB_DELIMITER .
            ' AND t.tvalue < ' . \YANA_DB_DELIMITER . '5' . \YANA_DB_DELIMITER .
            ' AND t.tvalue > ' . \YANA_DB_DELIMITER . '2' . \YANA_DB_DELIMITER;
        $this->assertSame($expected, $this->object->fromExistsQuery($query));
    }

    /**
     * @test
     */
    public function testFromCountQuery()
    {
        $query = new \Yana\Db\Queries\SelectCount($this->db);
        $query->setTable('t');
        $this->assertSame('SELECT count(*) FROM t', $this->object->fromCountQuery($query));
    }

    /**
     * @test
     */
    public function testFromCountQueryToStringColumn()
    {
        $query = new \Yana\Db\Queries\SelectCount($this->db);
        $query->setTable('t')->setColumn('tid');
        $this->assertSame('SELECT count(t.tid) FROM t', $this->object->fromCountQuery($query));
    }

    /**
     * @test
     */
    public function testFromSelectQuery()
    {
        $query = new \Yana\Db\Queries\Select($this->db);
        $query->setTable('ft');
        $query->setColumn('ftid');
        $query->setRow('1');
        $query->setOrderBy(array('ftid'));
        $expected = "SELECT ft.ftid FROM ft WHERE ft.ftid = "
            . \YANA_DB_DELIMITER . "1" . \YANA_DB_DELIMITER
            . " ORDER BY ft.ftid";
        $this->assertEquals($expected, $this->object->fromSelectQuery($query));
    }

    /**
     * @test
     */
    public function testFromSelectQuery2()
    {
        $query = new \Yana\Db\Queries\Select($this->db);

        // Select
        $query->setTable('t');
        $columns = array('tid', 'tvalue', 'ti', 'ftid');
        $query->setColumns($columns);

        $getAll = $query->getColumns();
        foreach($getAll as $key=>$entries)
        {
            $this->assertTrue(in_array($columns[$key], $entries));
        }
        $query->setWhere(array('ftid', '=', 2));
        $query->setKey('t');
        $query->setOrderBy(array('tvalue'));
        $query->setLimit(20);
        $getLimit = $query->getLimit();
        $this->assertEquals(20, $getLimit, 'assert failed, the expected value needs to be 20');
        $expected = "SELECT * FROM t WHERE t.ftid = "
            . \YANA_DB_DELIMITER . "2" . \YANA_DB_DELIMITER
            . " ORDER BY t.tvalue";
        $this->assertEquals($expected, $this->object->fromSelectQuery($query));
    }

    /**
     * @test
     */
    public function testFromSelectQueryWithHaving()
    {
        $query = new \Yana\Db\Queries\Select($this->db);
        $query->setTable('t');
        $query->setColumns(array('tid', 'tvalue'));
        $where = array('ftid', '=', '1');
        $query->setWhere($where);

        $query->setHaving(array('tvalue', '>', '20'));
        $getHaving = $query->getHaving();
        $expected = array(array('t', 'tvalue'), '>', '20');
        $this->assertEquals($expected, $getHaving, 'assert failed, the values must be equal');
        $expected = "SELECT t.tid, t.tvalue FROM t WHERE t.ftid = "
            . \YANA_DB_DELIMITER . "1" . \YANA_DB_DELIMITER
            . " HAVING t.tvalue > "
            . \YANA_DB_DELIMITER . "20" . \YANA_DB_DELIMITER;
        $this->assertEquals($expected, $this->object->fromSelectQuery($query));
    }

    /**
     * @test
     */
    public function testFromSelectQuery3()
    {
        $query = new \Yana\Db\Queries\Select($this->db);
        $query->setTable('t')->setColumns(array('tid', 'tvalue', 'ti', 'ftid'));
        $this->assertEquals("SELECT t.tid, t.tvalue, t.ti, t.ftid FROM t", $this->object->fromSelectQuery($query));
    }

}
