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
class DeleteTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Db\Queries\Delete
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
            $this->query = new \Yana\Db\Queries\Delete($this->db);
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
    public function testSetOrderBy()
    {
        $this->assertEquals(array(array('t', 'tid')), $this->query->setTable('t')->setOrderBy(array('tid'))->getOrderBy());
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
    public function testSetWhere()
    {
        $this->assertEquals(array(array('t', 'tid'), '=', '1'), $this->query->setTable('t')->setWhere(array('tid', '=', '1'))->getWhere());
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
    public function testSetLimit()
    {
        $this->assertSame(2, $this->query->setTable('t')->setLimit(2)->getLimit());
    }

    /**
     * @test
     */
    public function testGetLimit()
    {
        $this->assertSame(0, $this->query->setTable('t')->getLimit());
    }

    /**
     * @test
     */
    public function testGetOldValues()
    {
        $this->assertSame(array(), $this->query->setTable('t')->getOldValues());
    }

    /**
     * @test
     */
    public function testToString()
    {
        $this->query->setTable('ft');
        $this->query->setRow(2);
        $this->query->useInheritance(true);
        $sql = (string) $this->query;
        $valid = "DELETE FROM ft WHERE ft.ftid = " . \YANA_DB_DELIMITER . "2" . \YANA_DB_DELIMITER;
        $this->assertEquals($valid, $sql);
    }

}
