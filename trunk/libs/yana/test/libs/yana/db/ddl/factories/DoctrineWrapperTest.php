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

namespace Yana\Db\Ddl\Factories;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';


/**
 * @package  test
 */
class DoctrineWrapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Factories\DoctrineWrapper
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
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        if (!$this->isAvailable()) {
            $this->markTestSkipped();
        }
        try {
            $factory = new \Yana\Db\Doctrine\ConnectionFactory();
            $this->object = new \Yana\Db\Ddl\Factories\DoctrineWrapper($factory->getConnection());

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
    public function testGetDatabaseName()
    {
        $this->assertSame(\YANA_DATABASE_NAME, $this->object->getDatabaseName());
    }

    /**
     * @test
     */
    public function testListSequences()
    {
        // MySQL test database doesn't support sequences
        $this->assertSame(array(), $this->object->listSequences());
    }

    /**
     * @test
     */
    public function testListTables()
    {
        $tables = $this->object->listTables();
        $tableNames = array();
        foreach ($tables as $table)
        {
            $tableNames[] = $table->getName();
        }
        $this->assertContains('t', $tableNames);
        $this->assertContains('u', $tableNames);
        $this->assertContains('i', $tableNames);
        $this->assertContains('ft', $tableNames);
    }

    /**
     * @test
     */
    public function testListTableColumns()
    {
        $columns = $this->object->listTableColumns('t');
        $columnNames = array();
        foreach ($columns as $table)
        {
            $columnNames[] = $table->getName();
        }
        $this->assertSame(array('tid', 'tvalue', 'ta', 'tb', 'tf', 'ti', 'ftid'), $columnNames);
    }

    /**
     * @test
     */
    public function testListTableIndexes()
    {
        $indexes = $this->object->listTableIndexes('t');
        $this->assertArrayHasKey('primary', $indexes);
        $this->assertSame('tid', $indexes['primary']->getColumns()[0]);
    }

    /**
     * @test
     */
    public function testListTableConstraints()
    {
        $constraints = $this->object->listTableConstraints('t');
        $this->assertCount(1, $constraints);
        $this->assertSame(array('ftid'), $constraints[0]->getColumns());
        $this->assertSame(array('ftid'), $constraints[0]->getForeignColumns());
        $this->assertSame('ft', $constraints[0]->getForeignTableName());
    }

    /**
     * @test
     */
    public function testListViews()
    {
        $views = $this->object->listViews();
        $this->assertCount(1, $views);
        $this->assertArrayHasKey('v', $views);
    }

}
