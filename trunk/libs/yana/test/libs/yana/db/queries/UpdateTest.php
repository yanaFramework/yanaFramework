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
class UpdateTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Db\Queries\Update
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
            $this->query = new \Yana\Db\Queries\Update($this->db);
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
    public function testSetColumn()
    {
        $this->assertSame('tvalue', $this->query->setTable('t')->setColumn('tvalue')->getColumn());
    }

    /**
     * @test
     */
    public function testGetColumn()
    {
        $this->assertSame('*', $this->query->getColumn());
    }

    /**
     * @test
     */
    public function testGetArrayAddress()
    {
        $this->assertSame('', $this->query->getArrayAddress());
    }

    /**
     * @test
     */
    public function testSetOrderBy()
    {
        $this->assertSame(array(array('t', 'tvalue')), $this->query->setTable('t')->setOrderBy(array('tvalue'))->getOrderBy());
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
        $this->query->setTable('t');
        $having = array('tvalue', '=', 1);
        $this->assertSame(array(array('t', 'tvalue'), '=', '1'), $this->query->setWhere($having)->getWhere());
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
    public function testGetOldValues()
    {
        $this->query->setTable('t');
        $this->assertSame(array(), $this->query->getOldValues());
    }

}
