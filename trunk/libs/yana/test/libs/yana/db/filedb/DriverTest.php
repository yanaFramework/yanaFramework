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

namespace Yana\Db\FileDb;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyQuery extends \Yana\Db\Queries\AbstractQuery
{
    protected function toString(): string
    {
        return "";
    }

}

/**
 * @package  test
 */
class DriverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\FileDb\NullDriver
     */
    protected $object;

    /**
     * @var \Yana\Db\Ddl\Database
     */
    protected $schema;

    /**
     * @var string
     */
    protected $baseDirectory;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->baseDirectory = \Yana\Db\FileDb\Helpers\FilenameMapper::getBaseDirectory();
        \Yana\Db\FileDb\Helpers\FilenameMapper::setBaseDirectory(\Yana\Db\Ddl\DDL::getDirectory());
        $this->schema = \Yana\Files\XDDL::getDatabase('check');
        $parser = new \Yana\Db\Queries\Parser(new \Yana\Db\FileDb\Connection($this->schema));
        $this->object = new \Yana\Db\FileDb\NullDriver($parser);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        \Yana\Db\FileDb\Helpers\FilenameMapper::setBaseDirectory($this->baseDirectory);
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
    public function testRollback()
    {
        $this->assertTrue($this->object->rollback());
    }

    /**
     * @test
     */
    public function testCommit()
    {
        $this->assertTrue($this->object->commit());
    }

    /**
     * @test
     */
    public function testListDatabases()
    {
        $this->assertSame(\Yana\Db\Ddl\DDL::getListOfFiles(), $this->object->listDatabases());
    }

    /**
     * @test
     */
    public function testListTables()
    {
        $this->assertSame($this->schema->getTableNames(), $this->object->listTables());
    }

    /**
     * @test
     */
    public function testListFunctions()
    {
        $this->assertSame($this->schema->getFunctionNames(), $this->object->listFunctions());
    }

    /**
     * @test
     */
    public function testListSequences()
    {
        $this->assertSame($this->schema->getSequenceNames(), $this->object->listSequences());
    }

    /**
     * @test
     */
    public function testListTableFields()
    {
        $this->assertSame($this->schema->getTable('t')->getColumnNames(), $this->object->listTableFields('t'));
    }

    /**
     * @test
     */
    public function testListTableIndexes()
    {
        $indexes = array();
        foreach ($this->schema->getTable('t')->getIndexes() as $index)
        {
            if (is_string($index->getName())) {
                $indexes[] = $index->getName();
            }
        }
        $this->assertSame($indexes, $this->object->listTableIndexes('t'));
    }

    /**
     * @test
     */
    public function testSendQueryObject()
    {
        $connection = new \Yana\Db\FileDb\Connection($this->schema);
        $selectQuery = new \Yana\Db\Queries\Select($connection);
        $selectQuery->setTable('t');
        $resultObject = new \Yana\Db\FileDb\Result(array(array('tvalue' => 1, 'tb' => true, 'ftid' => 1, 'tid' => 1)));
        $this->assertEquals($resultObject, $this->object->sendQueryObject($selectQuery));
    }

    /**
     * @test
     */
    public function testSendQueryObjectWithCache()
    {
        $connection = new \Yana\Db\FileDb\Connection($this->schema);
        $query = new \Yana\Db\Queries\Select($connection);
        $query->setTable('t');
        $resultObject = $this->object->sendQueryObject($query);
        $this->assertSame($resultObject, $this->object->sendQueryObject($query));
    }

    /**
     * @test
     */
    public function testSendQueryObjectCount()
    {
        $connection = new \Yana\Db\FileDb\Connection($this->schema);
        $selectQuery = new \Yana\Db\Queries\SelectCount($connection);
        $selectQuery->setTable('t');
        $expectedResultObject = new \Yana\Db\FileDb\Result(array(array(1)));
        $actualResultObject = $this->object->sendQueryObject($selectQuery);
        $this->assertEquals($expectedResultObject, $actualResultObject);
        $this->assertEquals(1, $actualResultObject->fetchOne());
    }

    /**
     * @test
     */
    public function testSendQueryObjectCountWithCache()
    {
        $connection = new \Yana\Db\FileDb\Connection($this->schema);
        $query = new \Yana\Db\Queries\SelectCount($connection);
        $query->setTable('t');
        $resultObject = $this->object->sendQueryObject($query);
        $this->assertSame($resultObject, $this->object->sendQueryObject($query));
    }

    /**
     * @test
     */
    public function testSendQueryObjectExists()
    {
        $connection = new \Yana\Db\FileDb\Connection($this->schema);
        $query = new \Yana\Db\Queries\SelectExist($connection);
        $query->setTable('t');
        $resultObject = new \Yana\Db\FileDb\Result(array(1));
        $this->assertEquals($resultObject, $this->object->sendQueryObject($query));
    }

    /**
     * @test
     */
    public function testSendQueryObjectExistsFalse()
    {
        $connection = new \Yana\Db\FileDb\Connection($this->schema);
        $query = new \Yana\Db\Queries\SelectExist($connection);
        $query->setTable('t')->setRow('no-such-row');
        $resultObject = new \Yana\Db\FileDb\Result(array());
        $this->assertEquals($resultObject, $this->object->sendQueryObject($query));
    }

    /**
     * @test
     */
    public function testSendQueryObjectExistsWithCache()
    {
        $connection = new \Yana\Db\FileDb\Connection($this->schema);
        $query = new \Yana\Db\Queries\SelectExist($connection);
        $query->setTable('t');
        $resultObject = $this->object->sendQueryObject($query);
        $this->assertSame($resultObject, $this->object->sendQueryObject($query));
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\InvalidSyntaxException
     */
    public function testSendQueryObjectInsertInvalidSyntaxException()
    {
        $connection = new \Yana\Db\FileDb\Connection($this->schema);
        $query = new \Yana\Db\Queries\Insert($connection);
        $query->setTable('t');
        $this->object->sendQueryObject($query);
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\InvalidPrimaryKeyException
     */
    public function testSendQueryObjectUpdateInvalidPrimaryKeyException()
    {
        $connection = new \Yana\Db\FileDb\Connection($this->schema);
        $query = new \Yana\Db\Queries\Update($connection);
        $query->setTable('t');
        $this->object->sendQueryObject($query);
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\NotSupportedException
     */
    public function testSendQueryObjectNotSupportedException()
    {
        $connection = new \Yana\Db\FileDb\Connection($this->schema);
        $query = new \Yana\Db\FileDb\MyQuery($connection);
        $this->object->sendQueryObject($query);
    }

    /**
     * @test
     */
    public function testSendQueryString()
    {
        try {
            $resultObject = new \Yana\Db\FileDb\Result(array(array('tvalue' => 1, 'tb' => true, 'ftid' => 1, 'tid' => 1)));
            $this->assertEquals($resultObject, $this->object->sendQueryString('select * from t'));
        } catch (\Yana\Db\Queries\Exceptions\NotSupportedException $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    /**
     * @test
     */
    public function testQuote()
    {
        $this->assertSame(YANA_DB_DELIMITER . 'string' . YANA_DB_DELIMITER, $this->object->quote('string'));
    }

    /**
     * @test
     */
    public function testId()
    {
        $connection = new \Yana\Db\FileDb\Connection($this->schema);
        $parser = new \Yana\Db\Queries\Parser($connection);
        $object = new \Yana\Db\FileDb\Driver($parser);
        $insert = new \Yana\Db\Queries\Insert($connection);
        $insert->setTable('u')->setValues(array('uid' => 'test'));
        $object->sendQueryObject($insert);
        $select = new \Yana\Db\Queries\Select($connection);
        $select->setTable('u')->setRow('test');
        $this->assertSame(array('uid' => 'TEST'), $this->object->sendQueryObject($select)->fetchRow(0));
    }

    /**
     * @test
     */
    public function testId2()
    {
        $connection = new \Yana\Db\FileDb\Connection($this->schema);
        $parser = new \Yana\Db\Queries\Parser($connection);
        $object = new \Yana\Db\FileDb\Driver($parser);
        $delete = new \Yana\Db\Queries\Delete($connection);
        $delete->setTable('ft')->setLimit(0);
        $object->sendQueryObject($delete);
        $insert = new \Yana\Db\Queries\Insert($connection);
        $insert->setTable('ft')->setValues(array('ftid' => 123, 'ftvalue' => 2));
        $object->sendQueryObject($insert);
        $select = new \Yana\Db\Queries\Select($connection);
        $select->setTable('ft')->setRow(123);
        $this->assertEquals(array('ftvalue' => '2', 'ftid' => 123), $this->object->sendQueryObject($select)->fetchRow(0));
    }

    /**
     * @test
     */
    public function testQuoteNull()
    {
        $this->assertSame(YANA_DB_DELIMITER . YANA_DB_DELIMITER, $this->object->quote(null));
    }

    /**
     * @test
     */
    public function testQuoteArray()
    {
        $this->assertSame(YANA_DB_DELIMITER . \json_encode(array(1)) . YANA_DB_DELIMITER, $this->object->quote(array(1)));
    }

    /**
     * @test
     */
    public function testQuoteIdentifier()
    {
        $this->assertSame('t', $this->object->quoteIdentifier('t'));
    }

}
