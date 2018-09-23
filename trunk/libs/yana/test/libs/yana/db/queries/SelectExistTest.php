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
     * @covers Yana\Db\Queries\SelectExist::setWhere
     * @todo   Implement testSetWhere().
     */
    public function testSetWhere()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\Queries\SelectExist::addWhere
     * @todo   Implement testAddWhere().
     */
    public function testAddWhere()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\Queries\SelectExist::getWhere
     * @todo   Implement testGetWhere().
     */
    public function testGetWhere()
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
        $this->query->setInnerJoin('ft', 'ftid', 't', 'ftid');
        $join = $this->query->getJoin('ft');
        $this->assertEquals('ftid', $join->getForeignKey());
        $this->assertEquals('ftid', $join->getTargetKey());
        $this->assertEquals('ft', $join->getJoinedTableName());
        $this->assertEquals('t', $join->getSourceTableName());
        $this->assertTrue($join->isInnerJoin());
    }

    /**
     * @covers Yana\Db\Queries\SelectExist::unsetJoin
     * @todo   Implement testUnsetJoin().
     */
    public function testUnsetJoin()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\Queries\SelectExist::getJoin
     * @todo   Implement testGetJoin().
     */
    public function testGetJoin()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\Queries\SelectExist::getJoins
     * @todo   Implement testGetJoins().
     */
    public function testGetJoins()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\Queries\SelectExist::doesExist
     * @todo   Implement testDoesExist().
     */
    public function testDoesExist()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
