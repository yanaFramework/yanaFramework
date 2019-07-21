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
 * @ignore
 */
class MyQuery extends \Yana\Db\Queries\AbstractQuery
{

    /**
     * @param   string  $stmt  sql statement template
     * @return  string
     */
    public function toString($stmt = "")
    {
        return parent::toString($stmt);
    }
}

/**
 * @package  test
 */
class AbstractQueryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Queries\MyQuery
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
            $this->query = new \Yana\Db\Queries\MyQuery($this->db);
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
    public function test__get()
    {
        $this->assertSame($this->db->getSchema()->getTable('t'), $this->query->__get('t'));
    }

    /**
     * @test
     */
    public function test__call()
    {
        $this->assertSame('check', $this->query->__call('getName', array()));
    }

    /**
     * @test
     */
    public function test__isset()
    {
        $this->assertFalse($this->query->__isset('test'));
        $this->assertTrue($this->query->__isset('t'));
    }

    /**
     * @test
     */
    public function testResetQuery()
    {
        $this->assertSame('*', $this->query->setTable('t')->setRow('tid')->resetQuery()->getRow());
    }

    /**
     * @test
     */
    public function testGetType()
    {
        $this->assertSame(\Yana\Db\Queries\TypeEnumeration::UNKNOWN, $this->query->getType());
    }

    /**
     * @test
     */
    public function testGetExpectedResult()
    {
        $this->assertSame(\Yana\Db\Queries\TypeEnumeration::UNKNOWN, $this->query->getExpectedResult());
    }

    /**
     * @test
     */
    public function testUseInheritance()
    {
        $this->assertSame($this->query, $this->query->useInheritance(true));
        $this->assertSame($this->query, $this->query->useInheritance(false));
    }

    /**
     * @test
     */
    public function testGetTableByColumn()
    {
        $this->query->setTable('t');
        $this->assertSame($this->db->getSchema()->getTable('t'), $this->query->getTableByColumn('tid'));
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\ColumnNotFoundException
     */
    public function testGetTableByColumnNotFoundException()
    {
        $this->query->setTable('t')->getTableByColumn('no-such-column');
    }

    /**
     * @test
     */
    public function testGetTableByColumnWithJoin()
    {
        $query = new \Yana\Db\Queries\Select($this->db);
        $query->setTable('t')->setInnerJoin('ft');
        $this->assertSame($this->db->getSchema()->getTable('t'), $query->getTableByColumn('tid'));
        $this->assertSame($this->db->getSchema()->getTable('ft'), $query->getTableByColumn('ftvalue'));
    }

    /**
     * @test
     */
    public function testGetTableByColumnWithInheritance()
    {
        $query = new \Yana\Db\Queries\Select($this->db);
        $query->useInheritance(true)->setTable('i'); // Because we use inheritance the table "t" is auto-joined
        $this->assertSame($this->db->getSchema()->getTable('t'), $query->getTableByColumn('tid'));
        $this->assertSame($this->db->getSchema()->getTable('i'), $query->getTableByColumn('iid'));
        $this->assertSame($this->db->getSchema()->getTable('i'), $query->getTableByColumn('ta'));
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\ConstraintException
     */
    public function testSetJoinConstraintException()
    {
        $query = new \Yana\Db\Queries\Select($this->db);
        $query->useInheritance(true)->setTable('t');
        $query->setInnerJoin('i'); // Wrong join order... let's see what breaks first!
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\ColumnNotFoundException
     */
    public function testGetTableByColumnWithJoinColumnNotFoundException()
    {
        $query = new \Yana\Db\Queries\Select($this->db);
        $query->setTable('t')->setInnerJoin('ft');
        $query->getTableByColumn('no-such-column');
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\TableNotSetException
     */
    public function testGetTableByColumnTableNotSetException()
    {
        $this->query->getTableByColumn('tid');
    }

    /**
     * @test
     */
    public function testGetParentFalse()
    {
        $this->assertFalse($this->query->getParent());
    }

    /**
     * @test
     */
    public function testGetParent()
    {
        $query = new \Yana\Db\Queries\Select($this->db);
        $query->useInheritance(true)->setTable('i');
        $expected = $this->db->getSchema()->getTable('t');
        $this->assertSame($expected, $query->getParent('i'));
    }

    /**
     * @test
     */
    public function testSetTable()
    {
        $this->assertSame('t', $this->query->setTable('t')->getTable());
    }

    /**
     * @test
     */
    public function testGetTable()
    {
        $this->assertFalse($this->query->getTable());
    }

    /**
     * @test
     */
    public function testSetRow()
    {
        $this->assertSame('123', $this->query->setTable('t')->setRow(123)->getRow());
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\TableNotSetException
     */
    public function testSetRowTableNotSetException()
    {
        $this->query->setRow(123);
    }

    /**
     * @test
     */
    public function testGetRow()
    {
        $this->assertSame('*', $this->query->getRow());
    }

    /**
     * @test
     */
    public function testSetKey()
    {
        $this->assertSame('t', $this->query->setKey('t.1.tvalue')->getTable());
        $this->assertSame('1', $this->query->getRow());
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
    public function testGetOffset()
    {
        $this->assertSame(0, $this->query->getOffset());
    }

    /**
     * @test
     */
    public function testToId()
    {
        $this->query->setTable('ft');
        $this->query->setRow('abc');
        $serialize = $this->query->toId();
        $result = unserialize($serialize);
        $this->assertTrue(in_array('abc', $result));
        $this->assertTrue(in_array('ft', $result));
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\NotSupportedException
     */
    public function testSendQueryNotSupportedException()
    {
        $this->query->sendQuery();
    }

    /**
     * @test
     */
    public function testToStringEmpty()
    {
        $this->query->setTable('t')->setRow('tid');
        $this->assertEquals("", (string) $this->query);
    }

    /**
     * @test
     */
    public function testToString()
    {
        $this->query->setTable('t')->setRow('123');
        $this->assertEquals('t WHERE t.tid = "123"', $this->query->toString('%TABLE% %WHERE% %ORDERBY%'));
    }

    /**
     * @test
     */
    public function testToStringNoTable()
    {
        $this->assertFalse($this->query->toString('%TABLE% %WHERE% %ORDERBY%'));
    }

}
