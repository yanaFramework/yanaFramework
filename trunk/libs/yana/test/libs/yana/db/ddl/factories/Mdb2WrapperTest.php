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
class Mdb2WrapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Factories\Mdb2Wrapper
     */
    protected $object;

    /**
     * @return bool
     */
    protected function isAvailable()
    {
        $factory = new \Yana\Db\Mdb2\ConnectionFactory();
        return $factory->isAvailable($factory->getDsn());
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (\version_compare(\phpversion(), '7.0.0') >= 0 && \version_compare(\MDB2::apiVersion(), '2.5.0b5') < 0) {
            $this->markTestSkipped('MDB2 version not compatible with PHP7.');
        }
        if (!isset($GLOBALS['_MDB2_dsninfo_default'])) {
            $GLOBALS['_MDB2_dsninfo_default'] = array();
        }
        if (!$this->isAvailable()) {
            $this->markTestSkipped();
        }
        try {
            $server = new \Yana\Db\Mdb2\ConnectionFactory();
            $this->object = new \Yana\Db\Ddl\Factories\Mdb2Wrapper($server->getConnection());

        } catch (\Yana\Db\Mdb2\PearDbException $e) {
            $this->markTestSkipped("MDB2 extension not available");

        } catch (\Yana\Db\ConnectionException $e) {
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
        $tableNames = $this->object->listTables();
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
        $columnNames = $this->object->listTableColumns('t');
        $this->assertSame(array('tid', 'tvalue', 'ta', 'tb', 'tf', 'ti', 'ftid'), array_keys($columnNames));
    }

    /**
     * @test
     */
    public function testListTableIndexes()
    {
        $indexes = $this->object->listTableIndexes('t');
        $this->assertCount(1, $indexes);
        $index = current($indexes);
        $this->assertSame(array('ftid' => array('position' => 1, 'sorting' => 'ascending')), $index['fields']);
    }

    /**
     * @test
     */
    public function testListTableConstraints()
    {
        $constraints = $this->object->listTableConstraints('t');
        $this->assertCount(1, $constraints);
        $fields = $constraints['primary']['fields'];
        $this->assertCount(1, $fields);
        $this->assertArrayHasKey('tid', $fields);
    }

    /**
     * @test
     */
    public function testListViews()
    {
        $views = $this->object->listViews();
        $this->assertSame(array('v'), $views);
    }

}
