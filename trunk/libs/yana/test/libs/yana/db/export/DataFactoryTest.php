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
class DataFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Database
     */
    protected $schema;

    /**
     * @var \Yana\Db\FileDb\Connection
     */
    protected $connection;

    /**
     * @var \Yana\Db\Export\DataFactory
     */
    protected $object;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $schemaFactory = new \Yana\Db\SchemaFactory();
        $this->schema = $schemaFactory->createSchema('check');
        $this->connection = new \Yana\Db\FileDb\Connection($this->schema);
        $this->tearDown();
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Export\DataFactory($this->connection, new \Yana\Db\Helpers\SqlKeywordChecker(array('RESERVED')));
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
    public function testCreateMySQL()
    {
        $this->connection->insert('ft.1', array('ftvalue' => 1));
        $this->connection->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true, 'ta' => array(1 => '2', 2 => '3')));
        $this->connection->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false));
        $this->connection->insert('i.foo', array('ta' => array('1' => '1')));
        $this->connection->commit();

        $expectedSQL = array(
            0 => 'INSERT INTO `ft` (`ftvalue`, `ftid`) VALUES (1, 1);',
            1 => 'INSERT INTO `t` (`tvalue`, `ta`, `tb`, `ftid`, `tid`) VALUES (1, \'{\"1\":\"2\",\"2\":\"3\"}\', 1, 1, \'FOO\');',
            2 => 'INSERT INTO `t` (`tvalue`, `tb`, `ftid`, `tid`) VALUES (3, 0, 1, \'FOO3\');',
            3 => 'INSERT INTO `i` (`ta`, `iid`, `tvalue`, `tb`, `ftid`, `tid`) VALUES (\'{\"1\":\"2\",\"2\":\"3\"}\', \'FOO\', 1, 1, 1, \'FOO\');'
        );
        $createdSql = $this->object->createMySQL(false, true);
        $this->assertInternalType('array', $createdSql);
        $this->assertSame($expectedSQL, $createdSql);
    }

    /**
     * @test
     */
    public function testCreatePostgreSQL()
    {
        $this->connection->insert('ft.1', array('ftvalue' => 1));
        $this->connection->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true, 'ta' => array(1 => '2', 2 => '3')));
        $this->connection->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false));
        $this->connection->insert('i.foo', array('ta' => array('1' => '1')));
        $this->connection->commit();

        $expectedSQL = array(
            0 => 'INSERT INTO "ft" ("ftvalue", "ftid") VALUES (1, 1);',
            1 => 'INSERT INTO "t" ("tvalue", "ta", "tb", "ftid", "tid") VALUES (1, \'{\"1\":\"2\",\"2\":\"3\"}\', TRUE, 1, \'FOO\');',
            2 => 'INSERT INTO "t" ("tvalue", "tb", "ftid", "tid") VALUES (3, FALSE, 1, \'FOO3\');',
            3 => 'INSERT INTO "i" ("ta", "iid", "tvalue", "tb", "ftid", "tid") VALUES (\'{\"1\":\"2\",\"2\":\"3\"}\', \'FOO\', 1, TRUE, 1, \'FOO\');'
        );
        $createdSql = $this->object->createPostgreSQL(false, true);
        $this->assertInternalType('array', $createdSql);
        $this->assertSame($expectedSQL, $createdSql);
    }

    /**
     * @test
     */
    public function testCreateMSSQL()
    {
        $this->connection->insert('ft.1', array('ftvalue' => 1));
        $this->connection->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true, 'ta' => array(1 => '2', 2 => '3')));
        $this->connection->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false));
        $this->connection->insert('i.foo', array('ta' => array('1' => '1')));
        $this->connection->commit();

        $expectedSQL = array(
            0 => 'INSERT INTO [ft] ([ftvalue], [ftid]) VALUES (1, 1);',
            1 => 'INSERT INTO [t] ([tvalue], [ta], [tb], [ftid], [tid]) VALUES (1, \'{\"1\":\"2\",\"2\":\"3\"}\', 1, 1, \'FOO\');',
            2 => 'INSERT INTO [t] ([tvalue], [tb], [ftid], [tid]) VALUES (3, 0, 1, \'FOO3\');',
            3 => 'INSERT INTO [i] ([ta], [iid], [tvalue], [tb], [ftid], [tid]) VALUES (\'{\"1\":\"2\",\"2\":\"3\"}\', \'FOO\', 1, 1, 1, \'FOO\');'
        );
        $createdSql = $this->object->createMSSQL(false, true);
        $this->assertInternalType('array', $createdSql);
        $this->assertSame($expectedSQL, $createdSql);
    }

    /**
     * @test
     */
    public function testCreateMSAccess()
    {
        $this->connection->insert('ft.1', array('ftvalue' => 1));
        $this->connection->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true, 'ta' => array(1 => '2', 2 => '3')));
        $this->connection->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false));
        $this->connection->insert('i.foo', array('ta' => array('1' => '1')));
        $this->connection->commit();

        $expectedSQL = array(
            0 => 'INSERT INTO [ft] ([ftvalue], [ftid]) VALUES (1, 1);',
            1 => 'INSERT INTO [t] ([tvalue], [ta], [tb], [ftid], [tid]) VALUES (1, \'{\"1\":\"2\",\"2\":\"3\"}\', 1, 1, \'FOO\');',
            2 => 'INSERT INTO [t] ([tvalue], [tb], [ftid], [tid]) VALUES (3, 0, 1, \'FOO3\');',
            3 => 'INSERT INTO [i] ([ta], [iid], [tvalue], [tb], [ftid], [tid]) VALUES (\'{\"1\":\"2\",\"2\":\"3\"}\', \'FOO\', 1, 1, 1, \'FOO\');'
        );
        $createdSql = $this->object->createMSAccess(false, true);
        $this->assertInternalType('array', $createdSql);
        $this->assertSame($expectedSQL, $createdSql);
    }

    /**
     * @test
     */
    public function testCreateDB2()
    {
        $this->connection->insert('ft.1', array('ftvalue' => 1));
        $this->connection->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true, 'ta' => array(1 => '2', 2 => '3')));
        $this->connection->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false));
        $this->connection->insert('i.foo', array('ta' => array('1' => '1')));
        $this->connection->commit();

        $expectedSQL = array(
            0 => 'INSERT INTO ft (ftvalue, ftid) VALUES (1, 1);',
            1 => 'INSERT INTO t (tvalue, ta, tb, ftid, tid) VALUES (1, \'{\"1\":\"2\",\"2\":\"3\"}\', 1, 1, \'FOO\');',
            2 => 'INSERT INTO t (tvalue, tb, ftid, tid) VALUES (3, 0, 1, \'FOO3\');',
            3 => 'INSERT INTO i (ta, iid, tvalue, tb, ftid, tid) VALUES (\'{\"1\":\"2\",\"2\":\"3\"}\', \'FOO\', 1, 1, 1, \'FOO\');'
        );
        $createdSql = $this->object->createDB2(false, true);
        $this->assertInternalType('array', $createdSql);
        $this->assertSame($expectedSQL, $createdSql);
    }

    /**
     * @test
     */
    public function testCreateOracleDB()
    {
        $this->connection->insert('ft.1', array('ftvalue' => 1));
        $this->connection->insert('t.foo', array('tvalue' => 1, 'ftid' => 1, 'tb' => true, 'ta' => array(1 => '2', 2 => '3')));
        $this->connection->insert('t.foo3', array('tvalue' => 3, 'ftid' => 1, 'tb' => false));
        $this->connection->insert('i.foo', array('ta' => array('1' => '1')));
        $this->connection->commit();

        $expectedSQL = array(
            0 => 'INSERT INTO "ft" ("FTVALUE", "FTID") VALUES (1, 1);',
            1 => 'INSERT INTO "t" ("TVALUE", "TA", "TB", "FTID", "TID") VALUES (1, \'{\"1\":\"2\",\"2\":\"3\"}\', 1, 1, \'FOO\');',
            2 => 'INSERT INTO "t" ("TVALUE", "TB", "FTID", "TID") VALUES (3, 0, 1, \'FOO3\');',
            3 => 'INSERT INTO "i" ("TA", "IID", "TVALUE", "TB", "FTID", "TID") VALUES (\'{\"1\":\"2\",\"2\":\"3\"}\', \'FOO\', 1, 1, 1, \'FOO\');'
        );
//        $createdSql = $this->object->createOracleDB(false, true);
//        $this->assertInternalType('array', $createdSql);
//        $this->assertSame($expectedSQL, $createdSql);
    }

    /**
     * @covers Yana\Db\Export\DataFactory::quoteValue
     * @todo   Implement testQuoteValue().
     */
    public function testQuoteValue()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
