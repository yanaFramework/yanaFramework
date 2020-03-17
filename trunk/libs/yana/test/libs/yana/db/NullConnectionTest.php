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
 * @package  test
 */
class NullConnectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\NullConnection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\NullConnection();
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
    public function testGetSchema()
    {
        $this->assertEquals(new \Yana\Db\Ddl\Database('null'), $this->object->getSchema());
    }

    /**
     * @test
     */
    public function testGetDBMS()
    {
        $this->assertSame('generic', $this->object->getDBMS());
    }

    /**
     * @test
     */
    public function testSetDBMS()
    {
        $this->assertSame('whatever', $this->object->setDBMS('WhatEver')->getDBMS());
    }

    /**
     * @test
     */
    public function testCommit()
    {
        $this->assertSame($this->object, $this->object->commit());
    }

    /**
     * @test
     */
    public function testSelect()
    {
        $this->assertSame(array(), $this->object->select(""));
    }

    /**
     * @test
     */
    public function testUpdate()
    {
        $this->assertSame($this->object, $this->object->update(""));
    }

    /**
     * @test
     */
    public function testInsertOrUpdate()
    {
        $this->assertSame($this->object, $this->object->insertOrUpdate(""));
    }

    /**
     * @test
     */
    public function testInsert()
    {
        $this->assertSame($this->object, $this->object->insert(""));
    }

    /**
     * @test
     */
    public function testRemove()
    {
        $this->assertSame($this->object, $this->object->remove(""));
    }

    /**
     * @test
     */
    public function testLength()
    {
        $this->assertSame(0, $this->object->length(""));
    }

    /**
     * @test
     */
    public function testIsEmpty()
    {
        $this->assertTrue($this->object->isEmpty(""));
    }

    /**
     * @test
     */
    public function testExists()
    {
        $this->assertFalse($this->object->exists(""));
    }

    /**
     * @test
     */
    public function testIsWriteable()
    {
        $this->assertTrue($this->object->isWriteable());
    }

    /**
     * @test
     */
    public function testRollback()
    {
        $this->assertSame($this->object, $this->object->rollback());
    }

    /**
     * @test
     */
    public function testSendQueryString()
    {
        $this->assertEquals(new \Yana\Db\FileDb\Result(), $this->object->sendQueryString(''));
    }

    /**
     * @test
     */
    public function testSendQueryObject()
    {
        $this->assertEquals(new \Yana\Db\FileDb\Result(array()), $this->object->sendQueryObject(new \Yana\Db\Queries\Select($this->object)));
    }

    /**
     * @test
     */
    public function testImportSQL()
    {
        $this->assertTrue($this->object->importSQL(''));
    }

    /**
     * @test
     */
    public function testQuoteId()
    {
        $value = "SomeValue";
        $this->assertSame($value, $this->object->quoteId($value));
    }

    /**
     * @test
     */
    public function testQuoteDb2()
    {
        $checker = new \Yana\Db\Helpers\SqlKeywordChecker(array("USER"));
        $this->object = new \Yana\Db\NullConnection(null, \Yana\Db\DriverEnumeration::DB2, $checker);
        $value = "User";
        $this->assertSame('"' . $value. '"', $this->object->quoteId($value));
    }

    /**
     * @test
     */
    public function testQuoteMySql()
    {
        $this->object = new \Yana\Db\NullConnection(null, \Yana\Db\DriverEnumeration::MYSQL);
        $value = "my sql";
        $this->assertSame('`' . $value. '`', $this->object->quoteId($value));
    }

    /**
     * @test
     */
    public function testQuoteMsSql()
    {
        $this->object = new \Yana\Db\NullConnection(null, \Yana\Db\DriverEnumeration::MSSQL);
        $value = "ms.[sql]";
        $this->assertSame('[ms.[[sql]]]', $this->object->quoteId($value));
    }

    /**
     * @test
     */
    public function testQuoteOracle()
    {
        $this->object = new \Yana\Db\NullConnection(null, \Yana\Db\DriverEnumeration::ORACLE);
        $value = 'some "value"';
        $this->assertSame('"some ""value"""', $this->object->quoteId($value));
    }

}
