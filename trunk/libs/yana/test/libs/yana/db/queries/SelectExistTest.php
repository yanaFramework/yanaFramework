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
class SelectExistTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Db\Queries\SelectExist
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
            $this->query = new \Yana\Db\Queries\SelectExist($this->db);
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
    public function testSetWhere()
    {
        $this->query->setTable('t');
        $having = array('tvalue', '=', 1);
        $this->assertSame(array(array('t', 'tvalue'), '=', '1'), $this->query->setWhere($having)->getWhere());
    }

    /**
     * @test
     */
    public function testAddWhere()
    {
        $this->query->setTable('t');
        $having = $this->query->addWhere(array('tvalue', '>', 1))->addWhere(array('tvalue', '=', 2))->getWhere();
        $expected = array(array(array('t', 'tvalue'), '=', '2'), 'and', array(array('t', 'tvalue'), '>', '1'));
        $this->assertEquals($expected, $having);
    }

    /**
     * @test
     */
    public function testGetWhere()
    {
        $this->assertSame(array(), $this->query->getWhere());
    }

    /**
     * @test
     */
    public function testSetInnerJoin()
    {
        $this->query->setTable('t');
        $this->query->setInnerJoin('ft', 'ftid', 't', 'ftid');
        $join = $this->query->getJoin('ft');
        $this->assertTrue($this->query->getJoin('ft')->isInnerJoin());
    }

    /**
     * @test
     */
    public function testUnsetJoin()
    {
        $this->query->setTable('t');
        $this->query->setInnerJoin('ft');
        $this->assertTrue($this->query->getJoin('ft')->isInnerJoin());
        $this->query->unsetJoin('ft');
        $this->assertEquals(array(), $this->query->getJoins());
    }

    /**
     * @test
     */
    public function testGetJoin()
    {
        $this->query->setTable('t');
        $this->query->setInnerJoin('ft', 'ftid', 't', 'ftid');
        $join = $this->query->getJoin('ft');
        $this->assertEquals('ftid', $join->getForeignKey());
        $this->assertEquals('ftid', $join->getTargetKey());
        $this->assertEquals('ft', $join->getJoinedTableName());
        $this->assertEquals('t', $join->getSourceTableName());
        $this->assertTrue($join->isInnerJoin());
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\NotFoundException
     */
    public function testGetJoinNotFoundException()
    {
        $this->query->setTable('t')->setInnerJoin('ft')->unsetJoin('ft');
        $this->query->getJoin('ft');
    }

    /**
     * @test
     */
    public function testGetJoins()
    {
        $this->assertSame(array(), $this->query->getJoins());
    }

    /**
     * @test
     */
    public function testDoesExist()
    {
        $this->query->setTable('t');
        $this->assertFalse($this->query->doesExist());
    }

    /**
     * @test
     */
    public function testToString()
    {
        $this->query->setTable('t');
        $this->assertSame('SELECT 1 FROM t', (string) $this->query);
    }

    /**
     * @test
     */
    public function testToStringWithRow()
    {
        $this->query->setTable('t')->setRow('1');
        $this->assertSame('SELECT 1 FROM t WHERE t.tid = ' . \YANA_DB_DELIMITER . '1' . \YANA_DB_DELIMITER, (string) $this->query);
    }

    /**
     * @test
     */
    public function testToStringWithWhere()
    {
        $this->query->setTable('t')->setRow('1')->addWhere(array('tvalue', '>', '2'))->addWhere(array('tvalue', '<', '5'));
        $expected = 'SELECT 1 FROM t WHERE t.tid = ' . \YANA_DB_DELIMITER . '1' . \YANA_DB_DELIMITER .
            ' AND t.tvalue < ' . \YANA_DB_DELIMITER . '5' . \YANA_DB_DELIMITER .
            ' AND t.tvalue > ' . \YANA_DB_DELIMITER . '2' . \YANA_DB_DELIMITER;
        $this->assertSame($expected, (string) $this->query);
    }

    /**
     * @test
     */
    public function testToStringWitJoin()
    {
        $this->query->setTable('t')->setInnerJoin('ft', 'ftid')->setRow('1')->addWhere(array('tvalue', '>', '2'))->addWhere(array('tvalue', '<', '5'));
        $expected = 'SELECT 1 FROM t JOIN ft ON t.ftid = ft.ftid WHERE t.tid = ' . \YANA_DB_DELIMITER . '1' . \YANA_DB_DELIMITER .
            ' AND t.tvalue < ' . \YANA_DB_DELIMITER . '5' . \YANA_DB_DELIMITER .
            ' AND t.tvalue > ' . \YANA_DB_DELIMITER . '2' . \YANA_DB_DELIMITER;
        $this->assertSame($expected, (string) $this->query);
    }

    /**
     * @test
     */
    public function testIsSubSelect()
    {
        $this->assertFalse($this->query->isSubSelect());
    }

}
