<?php
/**
 * PHPUnit test-case
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

namespace Yana\Db\Doctrine;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Connection test-case
 *
 * @package  test
 */
class DriverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Doctrine\Driver
     */
    protected $object;

    /**
     * @return bool
     */
    protected function isAvailable()
    {
        $factory = new \Yana\Db\Doctrine\ConnectionFactory();
        return $factory->isAvailable($factory->getDsn());
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!$this->isAvailable()) {
            $this->markTestSkipped();
        }
        try {
            chdir(CWD . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
            $factory = new \Yana\Db\Doctrine\ConnectionFactory(); // may throw \Yana\Db\ConnectionException
            $this->object = new \Yana\Db\Doctrine\Driver($factory->getConnection());

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
        $this->object->rollback();
    }

    /**
     * @test
     */
    public function testBeginTransaction()
    {
        $this->assertTrue($this->object->beginTransaction());
    }

    /**
     * @test
     */
    public function testRollbackDatabase()
    {
        $this->assertTrue($this->object->beginTransaction());
        $this->assertTrue($this->object->rollback());
    }

    /**
     * @test
     */
    public function testCommit()
    {
        $this->assertTrue($this->object->beginTransaction());
        $this->assertTrue($this->object->commit());
    }

    /**
     * @test
     */
    public function testListDatabases()
    {
        $this->assertGreaterThanOrEqual(1, count($this->object->listDatabases()));
    }

    /**
     * @test
     */
    public function testListTables()
    {
        $tables = $this->object->listTables();
        $this->assertContains('t', $tables);
        $this->assertContains('ft', $tables);
        $this->assertContains('i', $tables);
    }

    /**
     * @test
     * @expectedException \Yana\Db\DatabaseException
     */
    public function testListFunctions()
    {
        $this->object->listFunctions();
    }

    /**
     * @test
     */
    public function testListTableFields()
    {
        $this->assertSame(array('iid', 'ta'), $this->object->listTableFields('i'));
    }

    /**
     * @test
     */
    public function testListTableIndexes()
    {
        $indexes = $this->object->listTableIndexes('t');
        $this->assertGreaterThanOrEqual(2, count($indexes));
    }

    /**
     * @test
     */
    public function testSendQueryObject()
    {
        $factory = new \Yana\Db\ConnectionFactory(new \Yana\Db\SchemaFactory());
        $select = new \Yana\Db\Queries\Select($factory->createConnection('check'));
        $select->setTable('t');
        $result = $this->object->sendQueryObject($select);
        $this->assertTrue($result instanceof \Yana\Db\IsResult);
        $this->assertSame(0, $result->countRows());
    }

    /**
     * @test
     */
    public function testSendQueryString()
    {
        $result = $this->object->sendQueryString("select * from t", 1);
        $this->assertTrue($result instanceof \Yana\Db\IsResult);
        $this->assertSame(0, $result->countRows());
    }

    /**
     * @test
     */
    public function testQuote()
    {
        $this->assertSame("'Test\'--test'", $this->object->quote("Test'--test"));
    }

    /**
     * @test
     */
    public function testEquals()
    {
        $factory = new \Yana\Db\Doctrine\ConnectionFactory();
        $this->assertTrue($this->object->equals(new \Yana\Db\Doctrine\Driver($factory->getConnection())));
    }

}
