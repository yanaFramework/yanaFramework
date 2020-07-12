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

namespace Yana\Db\Export;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class QueryFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\FileDb\Connection
     */
    protected $connection;

    /**
     * @var \Yana\Db\Export\QueryFactory
     */
    protected $object;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $schemaFactory = new \Yana\Db\SchemaFactory();
        $schema = $schemaFactory->createSchema('check');
        $this->connection = new \Yana\Db\FileDb\Connection($schema);
        $this->tearDown();
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $target = new \Yana\Db\NullConnection($this->connection->getSchema(), \Yana\Db\DriverEnumeration::MYSQL);
        $this->object = new \Yana\Db\Export\QueryFactory($this->connection, $target);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->connection->remove('i', array(), 0);
        $this->connection->remove('t', array(), 0);
        $this->connection->remove('ft', array(), 0);
        $this->connection->commit();
    }

    /**
     * @test
     */
    public function testCreateInsertStatements()
    {
        $this->connection->insert('ft.1', array('ftvalue' => 1));
        $this->connection->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true, 'ta' => array(1 => '2', 2 => '3')));
        $this->connection->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false));
        $this->connection->insert('i.foo', array('ta' => array('1' => '1')));
        $this->connection->commit();

        $expectedSQL = array(
            0 => 'INSERT INTO ft (ftid, ftvalue) VALUES (1, 1)',
            1 => 'INSERT INTO t (tid, tvalue, ta, tb, ftid) VALUES '
                . '(' . YANA_DB_DELIMITER . 'FOO' . YANA_DB_DELIMITER . ', 1, ' . YANA_DB_DELIMITER . '{"1":"2","2":"3"}' . YANA_DB_DELIMITER . ', 1, 1)',
            2 => 'INSERT INTO t (tid, tvalue, tb, ftid) VALUES '
                . '(' . YANA_DB_DELIMITER . 'FOO3' . YANA_DB_DELIMITER . ', 3, 0, 1)',
            3 => 'INSERT INTO i (iid, ta) VALUES '
                . '(' . YANA_DB_DELIMITER . 'FOO' . YANA_DB_DELIMITER . ', ' . YANA_DB_DELIMITER . '{"1":"1"}' . YANA_DB_DELIMITER . ')'
        );
        $insert1 = $this->object->createInsertStatements(1);
        $insert2 = $this->object->createInsertStatements(1);
        $insert3 = $this->object->createInsertStatements(1);
        $insert4 = $this->object->createInsertStatements(1);
        $insert5 = $this->object->createInsertStatements(1);
        $this->assertSame($expectedSQL[0], (string) $insert1[0]);
        $this->assertSame($expectedSQL[1], (string) $insert2[0]);
        $this->assertSame($expectedSQL[2], (string) $insert3[0]);
        $this->assertSame($expectedSQL[3], (string) $insert4[0]);
        $this->assertSame(array(), $insert5);
    }

    /**
     * @test
     */
    public function testCreateInsertStatementsEmpty()
    {
        $this->connection->insert('ft.1', array('ftvalue' => 1));
        $this->connection->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true, 'ta' => array(1 => '2', 2 => '3')));
        $this->connection->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false));
        $this->connection->insert('i.foo', array('ta' => array('1' => '1')));
        $this->connection->commit();
        $insert = $this->object->createInsertStatements(0);
        $this->assertSame(array(), $insert);
    }

}
