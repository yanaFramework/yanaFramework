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

    public function getTransactionHandler()
    {
        return $this->_getTransaction();
    }

    public function getName()
    {
        return $this->_getName();
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
        $this->schema = new \Yana\Db\Ddl\Database();
        $this->schema->addTable('test');
        $this->object = new \Yana\Db\MyAbstractConnection($this->schema);
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
        $original = preg_replace('/\/$/', '', \Yana\Db\MyAbstractConnection::getTempDir());
        $this->assertNull(\Yana\Db\MyAbstractConnection::SetTempDir("test"));
        $this->assertSame("test/", \Yana\Db\MyAbstractConnection::getTempDir());
        \Yana\Db\MyAbstractConnection::SetTempDir($original);
    }

    /**
     * @test
     */
    public function testGetTempDir()
    {
        $this->assertSame("cache/", \Yana\Db\MyAbstractConnection::getTempDir());
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
        $this->assertSame("", $this->object->getName());
        $connection = new \Yana\Db\MyAbstractConnection(new \Yana\Db\Ddl\Database("Test"));
        $this->assertSame("Test", $connection->getName());
    }

    /**
     * @covers Yana\Db\AbstractConnection::select
     * @todo   Implement testSelect().
     */
    public function testSelect()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\AbstractConnection::update
     * @todo   Implement testUpdate().
     */
    public function testUpdate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\AbstractConnection::insertOrUpdate
     * @todo   Implement testInsertOrUpdate().
     */
    public function testInsertOrUpdate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\AbstractConnection::insert
     * @todo   Implement testInsert().
     */
    public function testInsert()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\AbstractConnection::remove
     * @todo   Implement testRemove().
     */
    public function testRemove()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\AbstractConnection::length
     * @todo   Implement testLength().
     */
    public function testLength()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\AbstractConnection::isEmpty
     * @todo   Implement testIsEmpty().
     */
    public function testIsEmpty()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\AbstractConnection::exists
     * @todo   Implement testExists().
     */
    public function testExists()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\AbstractConnection::isWriteable
     * @todo   Implement testIsWriteable().
     */
    public function testIsWriteable()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\AbstractConnection::__toString
     * @todo   Implement test__toString().
     */
    public function test__toString()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\AbstractConnection::quote
     * @todo   Implement testQuote().
     */
    public function testQuote()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @test
     */
    public function testReset()
    {
        $this->assertSame($this->object, $this->object->reset());
    }

    /**
     * @test
     */
    public function testRollback()
    {
        $this->assertSame($this->object, $this->object->reset());
    }

    /**
     * @covers Yana\Db\AbstractConnection::equals
     * @todo   Implement testEquals().
     */
    public function testEquals()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\AbstractConnection::serialize
     * @todo   Implement testSerialize().
     */
    public function testSerialize()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Yana\Db\AbstractConnection::unserialize
     * @todo   Implement testUnserialize().
     */
    public function testUnserialize()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
