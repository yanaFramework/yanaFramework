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

/**
 * @ignore
 */
require_once __DIR__ . '/include.php';

/**
 * @package  test
 */
class DbInfoTableTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var  \Plugins\DbTools\InfoTable
     */
    protected $dbinfotable;

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        /** @ignore */
        include_once CWD . '/../../../plugins/dbtools/infotable.php';
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!class_exists('\Plugins\DbTools\InfoTable')) {
            $this->markTestSkipped();
        } else {
            $this->dbinfotable = new \Plugins\DbTools\InfoTable('foo');
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->dbinfotable = null;
    }

    /**
     * get name
     *
     * @test
     */
    public function testGetName()
    {
        $name = $this->dbinfotable->getName();
        // expected foo
        $this->assertInternalType('string', $name, 'assert failed, the expected value is not of type String');
        $this->assertEquals('foo', $name, 'assert failed, the expected value is "foo"');
    }

    /**
     * get init
     *
     * @test
     */
    public function testGetInit()
    {
        // intentionally left blank
    }

    /**
     * set initialization record
     *
     * @covers \Plugins\DbTools\InfoTable::setInit
     * @covers \Plugins\DbTools\InfoTable::getInit
     *
     * @test
     */
    public function testSetInit()
    {
        // intentionally left blank
    }

    /**
     * get comment
     *
     * @test
     */
    public function testGetComment()
    {
        // intentionally left blank
    }

    /**
     * set comment
     *
     * @covers \Plugins\DbTools\InfoTable::setComment
     * @covers \Plugins\DbTools\InfoTable::getComment
     *
     * @test
     */
    public function testSetComment()
    {
        $getComment = $this->dbinfotable->getComment();
        $this->assertFalse($getComment, 'assert failed, no comments are set');
        unset($getComment);
        $text = 'this is a comment blog';
        $getComment = $this->dbinfotable->setComment($text)->getComment();
        $this->assertEquals($text, $getComment, 'assert failed, the "$getComment" value should match the expected text');
        unset($getComment);
        // try second one
        $getComment = $this->dbinfotable->setComment('only one')->getComment();
        $this->assertEquals('only one', $getComment, 'assert failed, the "$getComment" value should match the expected text');
    }

    /**
     * get the name of the primary key
     *
     * @test
     */
    public function testGetPrimaryKey()
    {
        // intentionally left blank
    }

    /**
     * set primary key
     *
     * @covers \Plugins\DbTools\InfoTable::setPrimaryKey
     * @covers \Plugins\DbTools\InfoTable::getPrimaryKey
     *
     * @test
     */
    public function testSetPrimaryKey()
    {
        $getFK = $this->dbinfotable->getPrimaryKey();
        $this->assertFalse($getFK, 'assert failed, primaryKey is not set');
    }

    /**
     * set primaryKey Invalid Argument
     *
     * @covers \Plugins\DbTools\InfoTable::setPrimaryKey
     *
     * @test
     */
    function testSetPrimaryKeyInvalidArgument()
    {
        $this->assertFalse($this->dbinfotable->setPrimaryKey('foobar'));
    }

    /**
     * get array of foreign keys
     *
     * @test
     */
    public function testGetForeignKeys()
    {
        // intentionally left blank
    }

    /**
     * set a foreign key constraint
     *
     * @covers \Plugins\DbTools\InfoTable::setForeignKey
     * @covers \Plugins\DbTools\InfoTable::getForeignKeys
     *
     * @test
     */
    public function testSetForeignKey()
    {
        // intentionally left blank
    }

    /**
     * set ForeignKey Invalid Argument
     *
     * @covers \Plugins\DbTools\InfoTable::setForeignKey
     *
     * @test
     */
    function testSetForeignKeyInvalidArgument()
    {
        $this->assertFalse($this->dbinfotable->setForeignKey('barfoo', ''));
    }

    /**
     * set ForeignKey Invalid Argument1
     *
     * @covers \Plugins\DbTools\InfoTable::setForeignKey
     *
     * @test
     */
    function testSetForeignKeyInvalidArgument1()
    {
        $this->assertFalse($this->dbinfotable->setForeignKey('barfoo', 'foobar', 'qwerty'));
    }


    /**
     * add column object
     *
     * @covers \Plugins\DbTools\InfoTable::addColumn
     *
     * @test
     */
    public function testAddColumn()
    {
        // intentionally left blank
    }

    /**
     * export object as associative array
     *
     * @covers \Plugins\DbTools\InfoTable::toArray
     *
     * @test
     */
    public function testToArray()
    {
        // intentionally left blank
    }

    /**
     * test 1
     *
     * @covers \Plugins\DbTools\InfoTable::setInit
     * @covers \Plugins\DbTools\InfoTable::getInit
     * @covers \Plugins\DbTools\InfoTable::setPrimaryKey
     * @covers \Plugins\DbTools\InfoTable::getPrimaryKey
     * @covers \Plugins\DbTools\InfoTable::setForeignKey
     * @covers \Plugins\DbTools\InfoTable::getForeignKeys
     * @covers \Plugins\DbTools\InfoTable::addColumn
     * @covers \Plugins\DbTools\InfoTable::toArray
     *
     * @test
     */
     public function test1()
     {
        // create column1
        $column1 = new Plugins\DbTools\InfoColumn('id');
        $column1->setTable('bar');
        $column1->setLength(10);
        // add column
        $add = $this->dbinfotable->addColumn($column1);
        $this->assertTrue($add, 'assert failed, the column1 is not added');
        // create column2
        $column2 = new Plugins\DbTools\InfoColumn('name');
        $column2->setTable('bar');
        $column2->setLength(20);
        // add column
        $add = $this->dbinfotable->addColumn($column2);
        $this->assertTrue($add, 'assert failed, the column2 is not added');

        // set foreignKey
        $setFK = $this->dbinfotable->setForeignKey('id', 'qwertz');
        $this->assertTrue($setFK, 'assert failed, foreignKey is not set');
        $getFK = $this->dbinfotable->getForeignKeys();
        foreach($getFK as $key)
        {
          $this->assertEquals('id', $key['column'], 'assert failed, the expected value "id" is missing');
          $this->assertEquals('qwertz', $key['foreigntable'], 'assert failed, the expected value "qwertz" is missing');
          $this->assertEquals('id', $key['foreigncolumn'], 'assert failed, the expected value "id" is missing');
        }
        unset($key, $getFK);
        
        $setFK = $this->dbinfotable->setForeignKey('id', 'qwerty', 'bid');
        $this->assertTrue($setFK, 'assert failed, foreignKey is not set');
        $getFK = $this->dbinfotable->getForeignKeys();
        // unset the first row
        unset($getFK[0]);

        foreach($getFK as $key)
        {
          $this->assertEquals('id', $key['column'], 'assert failed, the expected value "id" is missing');
          $this->assertEquals('qwerty', $key['foreigntable'], 'assert failed, the expected value "qwerty" is missing');
          $this->assertEquals('bid', $key['foreigncolumn'], 'assert failed, the expected value "bid" is missing');
        }
        // set primaryKey
        $setPK = $this->dbinfotable->setPrimaryKey('id');
        $this->assertTrue($setPK, 'assert failed, primaryKey is not set');
        $getFK = $this->dbinfotable->getPrimaryKey();
        $this->assertEquals('id', $getFK, 'assert failed, the primaryKey is different as the expected');
        // set init
        $getInit = $this->dbinfotable->getInit();
        // expected false - no initialization record is set
        $this->assertInternalType('array', $getInit, 'assert failed, the value is not of type array');
        $this->assertEquals(0, count($getInit), 'assert failed, no initialization record is set');
        unset($getInit);
        $array = array(1 =>'select * from foo', 2=>'select * from bar');
        $getInit = $this->dbinfotable->setInit($array)->getInit();
        $this->assertTrue(in_array($array[1], $getInit), 'assert failed, the expected value is not in array');
        $this->assertTrue(in_array($array[2], $getInit), 'assert failed, the expected value is not in array');
        // create column3
        $column3 = new Plugins\DbTools\InfoColumn('pid');
        $column3->setTable('fooo');
        $column3->setLength(10);
        $column3->setPrimaryKey(true);
        // add column
        $add = $this->dbinfotable->addColumn($column3);
        $this->assertTrue($add, 'assert failed, the column3 is not added');
        // create column4
        $column4 = new Plugins\DbTools\InfoColumn('tt');
        $column4->setTable('barr');
        $column4->setLength(10);
        $column4->setForeignKey(true);
        $column4->setReference('foo', 'name');
        // add column
        $add = $this->dbinfotable->addColumn($column4);
        $this->assertTrue($add, 'assert failed, the column4 is not added');
        // toArray
        $array = $this->dbinfotable->toArray();
        $this->assertInternalType('array', $array, 'assert failed, the expected value is not of type array');
        $this->assertEquals('foo', $array['name'], 'assert failed, the expected value "name" is different from the tabel name');
    }

}
