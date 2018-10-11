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
class InsertTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Db\Queries\Insert
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
            $this->query = new \Yana\Db\Queries\Insert($this->db);
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
    public function testSetSanitizer()
    {
        $this->assertSame($this->query, $this->query->setSanitizer(new \Yana\Db\Helpers\NullSanitizer()));
    }

    /**
     * @covers Yana\Db\Queries\Insert::__clone
     * @todo   Implement test__clone().
     */
    public function test__clone()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\Queries\Insert::resetQuery
     * @todo   Implement testResetQuery().
     */
    public function testResetQuery()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\InvalidResultTypeException
     */
    public function testSetValuesInvalidResultTypeException()
    {
        $this->query->setValues(1);
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException
     */
    public function testSetValuesInvalidPrimaryKeyException()
    {
        $this->query->setTable('t')->setValues(array(1));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\MissingFieldException
     */
    public function testSetValuesMissingFieldException()
    {
        $this->query->setTable('t')->setValues(array('tid' => 1));
    }

    /**
     * @test
     */
    public function testSetValues()
    {
        $this->query->setSanitizer(new \Yana\Db\Helpers\NullSanitizer());
        $this->assertSame(array('tid' => '1'), $this->query->setTable('t')->setValues(array('tid' => 1))->getValues());
    }

    /**
     * @test
     */
    public function testGetValues()
    {
        $this->assertNull($this->query->getValues());
    }

    /**
     * @covers Yana\Db\Queries\Insert::sendQuery
     * @todo   Implement testSendQuery().
     */
    public function testSendQuery()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @test
     */
    public function testToString()
    {
        $this->query->setTable('ft');
        $values = array('ftid' => 2,'ftvalue' => '50');
        $this->query->setValues($values);
        $getValues = $this->query->getValues();
        $this->assertInternalType('array', $getValues, 'assert failed, the value should be of type array');
        $this->assertTrue(in_array(50, $getValues), 'assert failed, the expected value 50 should be match an enry in givin array');
        $sql = (string) $this->query;
        $valid = "INSERT INTO ft (ftid, ftvalue) "
            . "VALUES (" . \YANA_DB_DELIMITER . "2" . \YANA_DB_DELIMITER . ", " . \YANA_DB_DELIMITER . "50" . \YANA_DB_DELIMITER . ")";
        $this->assertEquals($valid, $sql, 'assert failed, the expected sql insert statement must be equal');
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

}
