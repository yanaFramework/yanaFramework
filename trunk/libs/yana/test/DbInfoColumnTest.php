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
class DbInfoColumnTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var  \Plugins\DbTools\InfoColumn
     */
    protected $dbinfocolumn;

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        /**
         * @ignore
         */
        include_once dirname(__FILE__) . '/../../../plugins/dbtools/infocolumn.php';
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->dbinfocolumn = new \Plugins\DbTools\InfoColumn('columnname');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->dbinfocolumn);
    }

    /**
     * setType
     *
     * @covers \Plugins\DbTools\InfoColumn::setType
     * @covers \Plugins\DbTools\InfoColumn::getType
     *
     * @test
     */
    public function testSetType()
    {
        $this->dbinfocolumn->setType('foo');
        $getType = $this->dbinfocolumn->getType();
        $this->assertEquals('foo', $getType, 'assert failed, the giving value does not match the expected value "foo"');
    }

    /**
     * getType
     *
     * @test
     */
    public function testGetType()
    {
        // intentionally left blank
    }

    /**
     * setNullable
     *
     * @covers \Plugins\DbTools\InfoColumn::setNullable
     * @covers \Plugins\DbTools\InfoColumn::isNullable
     *
     * @test
     */
    public function testSetNullable()
    {
        $this->dbinfocolumn->setNullable(true);
        $isNullable = $this->dbinfocolumn->isNullable();
        $this->assertTrue($isNullable, 'assert failed, nullable must be true');

        $this->dbinfocolumn->setNullable(false);
        $isNullable = $this->dbinfocolumn->isNullable();
        $this->assertFalse($isNullable, 'assert failed, nullable must be false');
    }

    /**
     * isNullable
     *
     * @test
     */
    public function testIsNullable()
    {
        // intentionally left blank
    }

    /**
     * setPrimaryKey
     *
     * @covers \Plugins\DbTools\InfoColumn::setPrimaryKey
     * @covers \Plugins\DbTools\InfoColumn::isPrimaryKey
     *
     * @test
     */
    public function testSetPrimaryKey()
    {
        $this->dbinfocolumn->setPrimaryKey(true);
        $isPrimaryKey = $this->dbinfocolumn->isPrimaryKey();
        $this->assertTrue($isPrimaryKey, 'assert failed, PrimaryKey must be true');

        $this->dbinfocolumn->setPrimaryKey(false);
        $isPrimaryKey = $this->dbinfocolumn->isPrimaryKey();
        $this->assertFalse($isPrimaryKey, 'assert failed, PrimaryKey must be false');

    }

    /**
     * isPrimaryKey
     *
     * @test
     */
    public function testIsPrimaryKey()
    {
        // intentionally left blank
    }

    /**
     * setForeignKey
     *
     * @covers \Plugins\DbTools\InfoColumn::setForeignKey
     * @covers \Plugins\DbTools\InfoColumn::isForeignKey
     *
     * @test
     */
    public function testSetForeignKey()
    {
        $this->dbinfocolumn->setForeignKey(true);
        $isForeignKey = $this->dbinfocolumn->isForeignKey();
        $this->assertTrue($isForeignKey, 'assert failed, ForeignKey must be true');

        $this->dbinfocolumn->setForeignKey(false);
        $isForeignKey = $this->dbinfocolumn->isForeignKey();
        $this->assertFalse($isForeignKey, 'assert failed, ForeignKey must be false');
    }

    /**
     * isForeignKey
     *
     * @test
     */
    public function testIsForeignKey()
    {
        // intentionally left blank
    }

    /**
     * setUnique
     *
     * @covers \Plugins\DbTools\InfoColumn::setUnique
     * @covers \Plugins\DbTools\InfoColumn::isUnique
     *
     * @test
     */
    public function testSetUnique()
    {
        $this->dbinfocolumn->setUnique(true);
        $isUnique = $this->dbinfocolumn->isUnique();
        $this->assertTrue($isUnique, 'assert failed, Unique must be true');

        $this->dbinfocolumn->setUnique(false);
        $isUnique = $this->dbinfocolumn->isUnique();
        $this->assertFalse($isUnique, 'assert failed, Unique must be false');
    }

    /**
     * isUnique
     *
     * @test
     */
    public function testIsUnique()
    {
        // intentionally left blank
    }

    /**
     * setIndex
     *
     * @covers \Plugins\DbTools\InfoColumn::setIndex
     * @covers \Plugins\DbTools\InfoColumn::hasIndex
     *
     * @test
     */
    public function testSetIndex()
    {
        $this->dbinfocolumn->setIndex(true);
        $hasIndex = $this->dbinfocolumn->hasIndex();
        $this->assertTrue($hasIndex, 'assert failed, Index must be true');

        $this->dbinfocolumn->setIndex(false);
        $hasIndex = $this->dbinfocolumn->hasIndex();
        $this->assertFalse($hasIndex, 'assert failed, Index must be false');
    }

    /**
     * hasIndex
     *
     * @test
     */
    public function testHasIndex()
    {
        // intentionally left blank
    }

    /**
     * setAuto
     *
     * @covers \Plugins\DbTools\InfoColumn::setAuto
     * @covers \Plugins\DbTools\InfoColumn::isAuto
     *
     * @test
     */
    public function testSetAuto()
    {
        $setAuto = $this->dbinfocolumn->setAuto(true);
        $isAuto = $this->dbinfocolumn->isAuto();
        $this->assertTrue($setAuto, 'assert failed, Auto is not set');
        $this->assertTrue($isAuto, 'assert failed, Auto must be true');

        $setAuto = $this->dbinfocolumn->setAuto(false);
        $isAuto = $this->dbinfocolumn->isAuto();
        $this->assertTrue($setAuto, 'assert failed, Auto is not set');
        $this->assertFalse($isAuto, 'assert failed, Auto must be false');
    }

    /**
     * isAuto
     *
     * @test
     */
    public function testIsAuto()
    {
        // intentionally left blank
    }

    /**
     * setUpdate
     *
     * @covers \Plugins\DbTools\InfoColumn::setUpdate
     * @covers \Plugins\DbTools\InfoColumn::isUpdatable
     *
     * @test
     */
    public function testSetUpdate()
    {
        $this->dbinfocolumn->setUpdate(true);
        $isUpdatable = $this->dbinfocolumn->isUpdatable();
        $this->assertTrue($isUpdatable, 'assert failed, Update must be true');

        $this->dbinfocolumn->setUpdate(false);
        $isUpdatable = $this->dbinfocolumn->isUpdatable();
        $this->assertFalse($isUpdatable, 'assert failed, Update must be false');
    }

    /**
     * isUpdatable
     *
     * @test
     */
    public function testIsUpdatable()
    {
        // intentionally left blank
    }

    /**
     * setSelect
     *
     * @covers \Plugins\DbTools\InfoColumn::setSelect
     * @covers \Plugins\DbTools\InfoColumn::isSelectable
     *
     * @test
     */
    public function testSetSelect()
    {
        $this->dbinfocolumn->setSelect(true);
        $isSelectable = $this->dbinfocolumn->isSelectable();
        $this->assertTrue($isSelectable, 'assert failed, Select must be true');

        $this->dbinfocolumn->setSelect(false);
        $isSelectable = $this->dbinfocolumn->isSelectable();
        $this->assertFalse($isSelectable, 'assert failed, Select must be false');
    }

    /**
     * isSelectable
     *
     * @test
     */
    public function testIsSelectable()
    {
        // intentionally left blank
    }

    /**
     * setInsert
     *
     * @covers \Plugins\DbTools\InfoColumn::setInsert
     * @covers \Plugins\DbTools\InfoColumn::isInsertable
     *
     * @test
     */
    public function testSetInsert()
    {
        $this->dbinfocolumn->setInsert(true);
        $isInsertable = $this->dbinfocolumn->isInsertable();
        $this->assertTrue($isInsertable, 'assert failed, Insert must be true');

        $this->dbinfocolumn->setInsert(false);
        $isInsertable = $this->dbinfocolumn->isInsertable();
        $this->assertFalse($isInsertable, 'assert failed, Insert must be false');
    }

    /**
     * isInsertable
     *
     * @test
     */
    public function testIsInsertable()
    {
        // intentionally left blank
    }

    /**
     * setUnsigned
     *
     * @covers \Plugins\DbTools\InfoColumn::setUnsigned
     * @covers \Plugins\DbTools\InfoColumn::isUnsigned
     *
     * @test
     */
    public function testSetUnsigned()
    {
        $this->dbinfocolumn->setUnsigned(true);
        $isUnsigned = $this->dbinfocolumn->isUnsigned();
        $this->assertTrue($isUnsigned, 'assert failed, Unsigned must be true');

        $this->dbinfocolumn->setUnsigned(false);
        $isUnsigned = $this->dbinfocolumn->isUnsigned();
        $this->assertFalse($isUnsigned, 'assert failed, Unsigned must be false');
    }

    /**
     * isUnsigned
     *
     * @test
     */
    public function testIsUnsigned()
    {
        // intentionally left blank
    }

    /**
     * setZerofill
     *
     * @covers \Plugins\DbTools\InfoColumn::setZerofill
     * @covers \Plugins\DbTools\InfoColumn::isZerofill
     *
     * @test
     */
    public function testSetZerofill()
    {
        $this->dbinfocolumn->setZerofill(true);
        $isZerofill = $this->dbinfocolumn->isZerofill();
        $this->assertTrue($isZerofill, 'assert failed, Zerofill must be true');

        $this->dbinfocolumn->setZerofill(false);
        $isZerofill = $this->dbinfocolumn->isZerofill();
        $this->assertFalse($isZerofill, 'assert failed, Zerofill must be false');
    }

    /**
     * isZerofill
     *
     * @test
     */
    public function testIsZerofill()
    {
        // intentionally left blank
    }

    /**
     * setTable
     *
     * @covers \Plugins\DbTools\InfoColumn::setTable
     * @covers \Plugins\DbTools\InfoColumn::getTable
     *
     * @test
     */
    public function testSetTable()
    {
        $this->dbinfocolumn->setTable('bar');
        $getTable = $this->dbinfocolumn->getTable();
        $this->assertEquals('bar', $getTable, 'assert failed, the giving value does not match the expected value "bar"');
    }

    /**
     * getTable
     *
     * @test
     */
    public function testGetTable()
    {
        // intentionally left blank
    }

    /**
     * setName
     *
     * @covers \Plugins\DbTools\InfoColumn::setName
     * @covers \Plugins\DbTools\InfoColumn::getName
     *
     * @test
     */
    public function testSetName()
    {
        $this->dbinfocolumn->setName('foobar');
        $getName = $this->dbinfocolumn->getName();
        $this->assertEquals('foobar', $getName, 'assert failed, the giving value does not match the expected value "foobar"');
    }

    /**
     * getName
     *
     * @test
     */
    public function testGetName()
    {
        // intentionally left blank
    }

    /**
     * getDefault
     *
     * @covers \Plugins\DbTools\InfoColumn::getDefault
     * @covers \Plugins\DbTools\InfoColumn::setDefault
     *
     * @test
     */
    public function testGetDefault()
    {
        $getDefault = $this->dbinfocolumn->getDefault();
        $this->assertNull($getDefault, 'assert failed, Default is not set');

        $this->dbinfocolumn->setDefault('default foo bar');
        $getDefault = $this->dbinfocolumn->getDefault();
        $this->assertEquals('default foo bar', $getDefault, 'assert failed, the giving value does not match the expected value "default foo bar"');
    }

    /**
     * setDefault
     *
     * @test
     */
    public function testSetDefault()
    {
        // intentionally left blank
    }

    /**
     * getComment
     *
     * @covers \Plugins\DbTools\InfoColumn::getComment
     * @covers \Plugins\DbTools\InfoColumn::setComment
     *
     * @test
     */
    public function testGetComment()
    {
        $this->dbinfocolumn->setComment('comments of foo');
        $getComment = $this->dbinfocolumn->getComment();
        $this->assertEquals('comments of foo', $getComment, 'assert failed, the giving value does not match the expected value "comments of foo"');
    }

    /**
     * setComment
     *
     * @test
     */
    public function testSetComment()
    {
        // intentionally left blank
    }

    /**
     * getLength
     *
     * @covers \Plugins\DbTools\InfoColumn::getLength
     * @covers \Plugins\DbTools\InfoColumn::setLength
     *
     * @test
     */
    public function testGetLength()
    {
        $this->dbinfocolumn->setLength(20);
        $getLength = $this->dbinfocolumn->getLength();
        $this->assertEquals(20, $getLength, 'assert failed, the giving value does not match the expected value "20"');

        $this->dbinfocolumn->setLength(-10);
        $getLength = $this->dbinfocolumn->getLength();
        $this->assertFalse($getLength, 'assert failed, the length attribute is null false is giving');
    }

    /**
     * setLength
     *
     * @test
     */
    public function testSetLength()
    {
        // intentionally left blank
    }

    /**
     * getReference
     *
     * @covers \Plugins\DbTools\InfoColumn::getReference
     * @covers \Plugins\DbTools\InfoColumn::setReference
     *
     * @test
     */
    public function teastGetReference()
    {
        $this->dbinfocolumn->setReference('ftable', 'fcolumn');
        $getReference = $this->dbinfocolumn->getReference();
        $valid = array(0=>'ftable', 1=>'fcolumn');
        $this->assertEquals($valid, $getReference, 'assert failed, the giving value does not match the expected value');
    }

    /**
     * setReference
     *
     * @test
     */
    public function testSetReference()
    {
        // intentionally left blank
    }

    /**
     * toArray
     *
     * @covers \Plugins\DbTools\InfoColumn::toArray
     *
     * @test
     */
    public function testToArray()
    {
        $toArray = $this->dbinfocolumn->toArray();
        $this->assertInternalType('array', $toArray, 'assert failed, the value is not of type array');
        $this->assertArrayHasKey('table', $toArray, 'assert failed, the array value should be have the "table" as a key');
    }

}
