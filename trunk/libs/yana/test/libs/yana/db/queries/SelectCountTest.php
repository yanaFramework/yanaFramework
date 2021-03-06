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
class SelectCountTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Db\Queries\SelectCount
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
            $this->query = new \Yana\Db\Queries\SelectCount($this->db);
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
        $this->query->setTable('t');
        $this->assertEquals('tid', $this->query->setColumn('tid')->getColumn());
    }

    /**
     * @test
     */
    public function testSetColumnWithAlias()
    {
        $this->query->setTable('t');
        $this->assertEquals('tid', $this->query->setColumn('tid', 'alias')->getColumn('alias'));
    }

    /**
     * @test
     */
    public function testGetColumn()
    {
        $this->query->setTable('t');
        $this->assertEquals('*', $this->query->getColumn());
    }

    /**
     * @test
     */
    public function testGetColumns()
    {
        $this->query->setTable('t');
        $this->assertEquals(array(array('t', 'tid')), $this->query->setColumn('tid')->getColumns());
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
    public function testToStringTable()
    {
        $this->query->setTable('t');
        $this->assertSame('SELECT count(*) FROM t', (string) $this->query);
    }

    /**
     * @test
     */
    public function testToStringColumn()
    {
        $this->query->setTable('t')->setColumn('tid');
        $this->assertSame('SELECT count(t.tid) FROM t', (string) $this->query);
    }

}
