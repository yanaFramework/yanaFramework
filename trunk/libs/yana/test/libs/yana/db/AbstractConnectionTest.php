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

namespace Yana\Db;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package test
 * @ignore
 */
class MyAbstractConnection extends \Yana\Db\AbstractConnection
{
    public function getQueryBuilder()
    {
        return $this->_getQueryBuilder();
    }

    protected function _getDriver(): \Yana\Db\IsDriver
    {
        $parser = new \Yana\Db\Queries\Parser($this);
        return new \Yana\Db\FileDb\NullDriver($parser);
    }

    public function getDBMS(): string
    {
        return "test";
    }

    public function importSQL($sqlFile): bool
    {
        return true;
    }

    public function quoteId($value): string
    {
        return $value;
    }

    public function sendQueryObject(Queries\AbstractQuery $sqlStmt): \Yana\Db\IsResult
    {
        return new \Yana\Db\FileDb\Result();
    }

    public function sendQueryString($sqlStmt, $offset = 0, $limit = 0): \Yana\Db\IsResult
    {
        return new \Yana\Db\FileDb\Result();
    }

    public function commit()
    {
        return $this;
    }

    public function getTransactionHandler(): \Yana\Db\IsTransaction
    {
        return $this->_getTransaction();
    }

    public function getName()
    {
        return $this->_getName();
    }
}

/**
 * @package test
 * @ignore
 */
class MyTransaction extends \Yana\Db\Transaction
{
    public function getQueue(): array
    {
        return $this->_getQueue();
    }
}

/**
 * @package  test
 */
class AbstractConnectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\MyAbstractConnection
     */
    protected $object;

    /**
     * @var \Yana\Db\Ddl\Database
     */
    protected $schema;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->schema = new \Yana\Db\Ddl\Database('MyDatabase');
        $table = $this->schema->addTable('test');
        $table->addColumn("id", \Yana\Db\Ddl\ColumnTypeEnumeration::INT);
        $table->addColumn("a", \Yana\Db\Ddl\ColumnTypeEnumeration::ARR);
        $table->setPrimaryKey("id");
        $this->object = new \Yana\Db\MyAbstractConnection($this->schema);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        if (isset($_SESSION['transaction_isolation_created'])) {
            unset($_SESSION['transaction_isolation_created']);
        }
    }

    /**
     * @test
     */
    public function testGetQueryBuilder()
    {
        $queryBuilder = new \Yana\Db\Queries\QueryBuilder($this->object);
        $this->assertEquals($queryBuilder, $this->object->getQueryBuilder());
    }

    /**
     * @test
     */
    public function testSetQueryBuilder()
    {
        $queryBuilder = new \Yana\Db\Queries\QueryBuilder($this->object);
        $this->assertSame($queryBuilder, $this->object->setQueryBuilder($queryBuilder)->getQueryBuilder());
    }

    /**
     * @test
     */
    public function testSetTempDir()
    {
        $original = preg_replace('/' . \preg_quote(\DIRECTORY_SEPARATOR, '/') . '$/', '', \Yana\Db\MyAbstractConnection::getTempDir());
        $this->assertNull(\Yana\Db\MyAbstractConnection::SetTempDir("test"));
        $this->assertSame("test" . \DIRECTORY_SEPARATOR, \Yana\Db\MyAbstractConnection::getTempDir());
        \Yana\Db\MyAbstractConnection::SetTempDir($original);
    }

    /**
     * @test
     */
    public function testGetTempDir()
    {
        $this->assertStringMatchesFormat("%Acache%A%e", \Yana\Db\MyAbstractConnection::getTempDir());
    }

    /**
     * @test
     */
    public function testGetSchema()
    {
        $this->assertSame($this->schema, $this->object->getSchema());
    }

    /**
     * @test
     */
    public function test__get()
    {
        $this->assertSame($this->schema->getTable('test'), $this->object->__get('test'));
        $this->assertSame($this->schema->getTable('test'), $this->object->test);
        $this->assertNull($this->object->invalid);
    }

    /**
     * @test
     */
    public function test__call()
    {
        $this->assertSame($this->schema->getTable('test'), $this->object->__call('getTable', array('test')));
        $this->assertSame($this->schema->getTable('test'), $this->object->getTable('test'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\UndefinedMethodException
     */
    public function test__callUndefinedMethodException()
    {
        $this->assertNull($this->object->__call('invalid', array()));
    }

    /**
     * @test
     */
    public function test__isset()
    {
        $this->assertTrue($this->object->__isset('test'));
        $this->assertTrue(isset($this->object->test));
        $this->assertFalse($this->object->__isset('invalid'));
        $this->assertFalse(isset($this->object->invalid));
    }

    /**
     * @test
     */
    public function testGetTransactionHandler()
    {
        $transaction = new \Yana\Db\Transaction($this->schema);
        $this->assertEquals($transaction, $this->object->getTransactionHandler());
    }

    /**
     * @test
     */
    public function testSetTransactionHandler()
    {
        $transaction = new \Yana\Db\Transaction($this->schema);
        $this->assertSame($transaction, $this->object->setTransactionHandler($transaction)->getTransactionHandler());
    }

    /**
     * @test
     */
    public function testGetName()
    {
        $this->assertSame("MyDatabase", $this->object->getName());
        $connection = new \Yana\Db\MyAbstractConnection(new \Yana\Db\Ddl\Database());
        $this->assertSame("", $connection->getName());
    }

    /**
     * @test
     */
    public function testSelect()
    {
        $this->assertEquals(array(), $this->object->select('test'));
    }

    /**
     * @test
     */
    public function testUpdate()
    {
        $transaction = new \Yana\Db\MyTransaction($this->object->getSchema());
        $this->object->setTransactionHandler($transaction);
        $this->assertSame($this->object, $this->object->update('test.1.id', 2));
        $queue = $transaction->getQueue();
        $this->assertCount(1, $queue);
        $this->assertSame('test', $queue[0][0]->getTable());
        $this->assertSame('1', $queue[0][0]->getRow());
        $this->assertSame('id', $queue[0][0]->getColumn());
        $this->assertSame(2, $queue[0][0]->getValues());
    }

    /**
     * Update Invalid Argument Exception
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testUpdateInvalidArgumentException()
    {
        $this->object->update('');
    }

    /**
     * Update Invalid Argument Exception
     *
     * @expectedException \Yana\Core\Exceptions\Forms\TimeoutException
     * @test
     */
    public function testUpdateTimeoutException()
    {
        $_SESSION['transaction_isolation_created'] = -1;
        $_SESSION['REMOTE_ADDR'] = '::1';
        $this->object->update('test');
    }

    /**
     * Update Invalid Argument Exception
     *
     * @expectedException \Yana\Core\Exceptions\NotWriteableException
     * @test
     */
    public function testUpdateNotWriteableException()
    {
        $this->schema->setReadonly(true);
        $this->object->update('test');
    }

    /**
     * Update Invalid Argument Exception
     *
     * @expectedException \Yana\Db\Queries\Exceptions\TableNotFoundException
     * @test
     */
    public function testUpdateTableNotFoundException()
    {
        $this->object->update('tf.foo1', array('kvalue' => 1 ));
    }

    /**
     * @test
     */
    public function testInsertOrUpdate()
    {
        $transaction = new \Yana\Db\MyTransaction($this->object->getSchema());
        $this->object->setTransactionHandler($transaction);
        $this->assertSame($this->object, $this->object->insertOrUpdate('test.1', array('id' => 1)));
        $queue = $transaction->getQueue();
        $this->assertCount(1, $queue);
        $this->assertSame('test', $queue[0][0]->getTable());
        $this->assertSame('1', $queue[0][0]->getRow());
        $this->assertSame(array('id' => 1), $queue[0][0]->getValues());
    }

    /**
     * @test
     */
    public function testInsertOrUpdate2()
    {
        $transaction = new \Yana\Db\MyTransaction($this->object->getSchema());
        $this->object->setTransactionHandler($transaction);
        $this->assertSame($this->object, $this->object->insertOrUpdate('test.1.a.1', "a")->insertOrUpdate('test.1.a.2', "b"));
        $queue = $transaction->getQueue();
        $this->assertCount(2, $queue);
        $this->assertTrue($queue[0] instanceof \Yana\Db\Queries\Insert);
        $this->assertFalse($queue[0] instanceof \Yana\Db\Queries\Update);
        $this->assertTrue($queue[1] instanceof \Yana\Db\Queries\Update);
        $this->assertSame('test', $queue[0][0]->getTable());
        $this->assertSame('1', $queue[0][0]->getRow());
        $this->assertSame(array(1 => "a", 2 => "b"), $queue[1][0]->getValues());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testInsertOrUpdateInvalidArgumentException()
    {
        $this->object->insertOrUpdate('');
    }

    /**
     * @test
     */
    public function testInsert()
    {
        $transaction = new \Yana\Db\MyTransaction($this->object->getSchema());
        $this->object->setTransactionHandler($transaction);
        $this->object->insert('test', array('id' => 1))->insert('test.1', array('id' => 1));
        $queue = $transaction->getQueue();
        $this->assertCount(2, $queue);
        $this->assertSame('test', $queue[0][0]->getTable());
        $this->assertSame('test', $queue[1][0]->getTable());
        $this->assertSame('1', $queue[1][0]->getRow());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testInsertInvalidArgumentException()
    {
        $this->object->insert('');
    }

    /**
     * @test
     */
    public function testRemove()
    {
        $transaction = new \Yana\Db\MyTransaction($this->object->getSchema());
        $this->object->setTransactionHandler($transaction);
        $this->object->remove('test', array('id', \Yana\Db\Queries\OperatorEnumeration::EQUAL, 1), 2);
        $queue = $transaction->getQueue();
        $this->assertCount(1, $queue);
        $this->assertSame('test', $queue[0][0]->getTable());
        $this->assertSame('1', $queue[0][0]->getRow());
        $this->assertSame(2, $queue[0][0]->getLimit());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testRemoveInvalidArgumentException()
    {
        $this->object->remove('');
    }

    /**
     * @test
     */
    public function testLength()
    {
        $this->assertSame(0, $this->object->length("test"));
        $this->assertSame(0, $this->object->length("no-such-table"));
        $this->assertSame(0, $this->object->length(""));
    }

    /**
     * @test
     */
    public function testIsEmpty()
    {
        $this->assertTrue($this->object->isEmpty("test"));
        $this->assertTrue($this->object->isEmpty("no-such-table"));
        $this->assertTrue($this->object->isEmpty(""));
    }

    /**
     * @test
     */
    public function testExists()
    {
        $this->assertTrue($this->object->exists("test"));
        $this->assertFalse($this->object->exists("test.1"));
        $this->assertFalse($this->object->exists("no-such-table"));
    }

    /**
     * @test
     */
    public function testIsWriteable()
    {
        $this->assertTrue($this->object->isWriteable());
        $this->schema->setReadonly(true);
        $this->assertFalse($this->object->isWriteable());
    }

    /**
     * @test
     */
    public function test__toString()
    {
        $this->assertSame($this->object->getName(), $this->object->__toString());
    }

    /**
     * @test
     */
    public function testQuote()
    {
        $this->assertSame('NULL', $this->object->quote(null));
        $this->assertSame(\YANA_DB_DELIMITER . '[1,2,3]' . \YANA_DB_DELIMITER, $this->object->quote(array(1, 2, 3)));
        $this->assertSame(\YANA_DB_DELIMITER . 'test' . \YANA_DB_DELIMITER, $this->object->quote('test'));
    }

    /**
     * @test
     */
    public function testReset()
    {
        $transaction = new \Yana\Db\Transaction($this->schema);
        $this->object->setTransactionHandler($transaction);
        $this->assertSame($this->object, $this->object->reset());
        $this->assertNotSame($transaction, $this->object->getTransactionHandler());
    }

    /**
     * @test
     */
    public function testRollback()
    {
        $transaction = new \Yana\Db\Transaction($this->schema);
        $this->object->setTransactionHandler($transaction);
        $this->assertSame($this->object, $this->object->rollback());
        $this->assertNotSame($transaction, $this->object->getTransactionHandler());
    }

    /**
     * @test
     */
    public function testEquals()
    {
        $this->assertFalse($this->object->equals(new \Yana\Core\StdObject()));
        $this->assertFalse($this->object->equals(new \Yana\Db\MyAbstractConnection(new \Yana\Db\Ddl\Database())));
        $this->assertTrue($this->object->equals(new \Yana\Db\MyAbstractConnection($this->schema)));
        $this->assertTrue($this->object->equals($this->object));
    }

    /**
     * @test
     */
    public function testSerialize()
    {
        $string = $this->object->serialize();
        $array = \unserialize($string);

        $this->assertArrayHasKey('_name', $array);
        $this->assertArrayHasKey('_schema', $array);
        $this->assertArrayNotHasKey('_database', $array);
        $this->assertEquals($this->schema, $array['_schema']);
    }

    /**
     * @test
     */
    public function testUnserialize()
    {
        $expected =  array('_name' => __FUNCTION__);
        $string = \serialize($expected);
        $this->assertNull($this->object->unserialize($string));
        $this->assertSame(__FUNCTION__, $this->object->getName());
    }

}
