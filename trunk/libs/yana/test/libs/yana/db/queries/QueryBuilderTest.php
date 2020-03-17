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
class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Queries\QueryBuilder
     */
    protected $object;

    /**
     * @var  \Yana\Db\FileDb\NullConnection
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
                $this->db = new \Yana\Db\FileDb\NullConnection($schema);
            }
            $this->object = new \Yana\Db\Queries\QueryBuilder($this->db);
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
        
    }

    /**
     * @test
     */
    public function testSelect()
    {
        $select = $this->object->select(
                't.1.ta.a.b',
                array('tvalue', \Yana\Db\Queries\OperatorEnumeration::EQUAL, 123),
                array('Tid', 'TValue'),
                1,
                2,
                array(true, false)
        );
        $this->assertSame('t', $select->getTable());
        $this->assertSame('1', $select->getRow());
        $this->assertSame('ta', $select->getColumn());
        $this->assertSame('a.b', $select->getArrayAddress());
        $where = array(
            array(array('t', 'tid'), \Yana\Db\Queries\OperatorEnumeration::EQUAL, '1'),
            \Yana\Db\Queries\OperatorEnumeration::AND,
            array(array('t', 'tvalue'), \Yana\Db\Queries\OperatorEnumeration::EQUAL, '123')
        );
        $this->assertSame($where, $select->getWhere());
        $this->assertSame(array(array('t', 'tid'), array('t', 'tvalue')), $select->getOrderBy());
        $this->assertSame(1, $select->getOffset());
        $this->assertSame(2, $select->getLimit());
        $this->assertSame(array(true, false), $select->getDescending());
    }

    /**
     * @test
     */
    public function testUpdate()
    {
        $update = $this->object->update('t.1.ta.a.b', array(1 => 'a', 2 => 'b'));
        $this->assertSame('t', $update->getTable());
        $this->assertSame('1', $update->getRow());
        $this->assertSame('ta', $update->getColumn());
        $this->assertSame('a.b', $update->getArrayAddress());
        $this->assertSame(array(1 => 'a', 2 => 'b'), $update->getValues());
    }

    /**
     * @test
     */
    public function testInsert()
    {
        $insert = $this->object->insert('t.1', array('ta' => array('a'), 'tvalue' => 123, 'ftid' => 1));
        $this->assertSame('t', $insert->getTable());
        $this->assertSame('1', $insert->getRow());
        $this->assertSame(array('tid' => '1', 'tvalue' => 123, 'ta' => array('a'), 'ftid' => 1), $insert->getValues());
    }

    /**
     * @test
     */
    public function testRemove()
    {
        $delete = $this->object->remove('T.1', array('tvalue', \Yana\Db\Queries\OperatorEnumeration::EQUAL, 123), 2);
        $this->assertSame('t', $delete->getTable());
        $this->assertSame('1', $delete->getRow());
        $this->assertSame(2, $delete->getLimit());
        $where = array(
            array(array('t', 'tid'), \Yana\Db\Queries\OperatorEnumeration::EQUAL, '1'),
            \Yana\Db\Queries\OperatorEnumeration::AND,
            array(array('t', 'tvalue'), \Yana\Db\Queries\OperatorEnumeration::EQUAL, '123')
        );
        $this->assertSame($where, $delete->getWhere());
    }

    /**
     * @test
     */
    public function testSelectLast()
    {
        $select = $this->object->select('t.?.tValue');
        $this->assertSame(array(array('t', 'tid')), $select->getOrderBy());
        $this->assertSame(array(true), $select->getDescending());
        $this->assertSame(1, $select->getLimit());
    }

    /**
     * @test
     */
    public function testSelectLastWithOrderBy()
    {
        $select = $this->object->select('t.?.tValue', array(), array('Tvalue', 'fTid'), 1, 2, array(true));
        $this->assertSame(array(array('t', 'tid'), array('t', 'tvalue'), array('t', 'ftid')), $select->getOrderBy());
        $this->assertSame(array(true, true, false), $select->getDescending());
        $this->assertSame(1, $select->getOffset());
        $this->assertSame(2, $select->getLimit());
    }

    /**
     * @test
     */
    public function testLength()
    {
        $length = $this->object->length('T', array('tvalue', \Yana\Db\Queries\OperatorEnumeration::EQUAL, 123));
        $this->assertSame('t', $length->getTable());
        $this->assertSame(array(array('t', 'tvalue'), \Yana\Db\Queries\OperatorEnumeration::EQUAL, '123'), $length->getWhere());
    }

    /**
     * @test
     */
    public function testExists()
    {
        $exists = $this->object->exists('T.1', array('tvalue', \Yana\Db\Queries\OperatorEnumeration::EQUAL, 123));
        $this->assertSame('t', $exists->getTable());
        $this->assertSame('1', $exists->getRow());
        $where = array(
            array(array('t', 'tid'), \Yana\Db\Queries\OperatorEnumeration::EQUAL, '1'),
            \Yana\Db\Queries\OperatorEnumeration::AND,
            array(array('t', 'tvalue'), \Yana\Db\Queries\OperatorEnumeration::EQUAL, '123')
        );
        $this->assertSame($where, $exists->getWhere());
    }

}
