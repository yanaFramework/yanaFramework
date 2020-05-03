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

namespace Yana\Db\Helpers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ValueSanitizerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Helpers\ValueSanitizer
     */
    protected $object;

    /**
     * @var \Yana\Db\Ddl\Table
     */
    protected $table;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->table = new \Yana\Db\Ddl\Table("test");
        $this->table->addColumn('id', \Yana\Db\Ddl\ColumnTypeEnumeration::INT)->setNullable(false);
        $this->table->addColumn('autoFill', \Yana\Db\Ddl\ColumnTypeEnumeration::INT)->setNullable(false)->setDefault(123);
        $this->table->addColumn('autoIncrement', \Yana\Db\Ddl\ColumnTypeEnumeration::INT)->setNullable(false)->setAutoIncrement(true);
        $this->table->addColumn('boolColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL);
        $this->table->addColumn('arrayColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::ARR);
        $this->table->addColumn('colorColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::COLOR);
        $this->table->addColumn('dateColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::DATE);
        $this->table->addColumn('enumColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::ENUM)->setEnumerationItems(array('1' => 'one', '2' => 'two'));
        $this->table->addColumn('fileColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::FILE);
        $this->table->addColumn('rangeColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::RANGE)->setRange(1.0, 2.0);
        $this->table->addColumn('floatColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT)->setLength(4, 2);
        $this->table->addColumn('htmlColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::HTML);
        $this->table->addColumn('inetColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::INET);
        $this->table->addColumn('intColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::INT);
        $this->table->addColumn('listColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::LST);
        $this->table->addColumn('mailColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::MAIL);
        $this->table->addColumn('passwordColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::PASSWORD);
        $this->table->addColumn('setColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::SET)->setEnumerationItems(array('1' => 'one', '2' => 'two'));
        $this->table->addColumn('stringColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::STRING);
        $this->table->addColumn('textColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::TEXT);
        $this->table->addColumn('timeColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::TIME);
        $this->table->addColumn('timestampColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::TIMESTAMP);
        $this->table->addColumn('urlColumn', \Yana\Db\Ddl\ColumnTypeEnumeration::URL);
        $this->object = new \Yana\Db\Helpers\ValueSanitizer();
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
    public function testSanitizeRowByTable()
    {
        $row = array('id' => 1, 'stringcolumn' => 'test');
        $this->assertSame($row, $this->object->sanitizeRowByTable($this->table, $row, false));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\FieldNotFoundException
     */
    public function testSanitizeRowByTableFieldNotFoundException()
    {
        $row = array('id' => 1, 'no-such-column' => 'test');
        $this->object->sanitizeRowByTable($this->table, $row, false);
    }

    /**
     * @test
     */
    public function testSanitizeRowByTableInsertWithDefault()
    {
        $row = array('id' => 1, 'stringcolumn' => 'test');
        $this->assertSame(array('id' => 1, 'autofill' => 123, 'stringcolumn' => 'test'), $this->object->sanitizeRowByTable($this->table, $row));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\MissingFieldException
     */
    public function testSanitizeRowByTableMissingFieldException()
    {
        $row = array('stringcolumn' => 'test');
        $this->assertSame($row, $this->object->sanitizeRowByTable($this->table, $row));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotWriteableException
     */
    public function testSanitizeRowByTableNotWriteableException()
    {
        $this->table->setReadonly(true);
        $row = array('id' => 1, 'stringcolumn' => 'test');
        $this->object->sanitizeRowByTable($this->table, $row);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotWriteableException
     */
    public function testSanitizeRowByTableNotWriteableException2()
    {
        $this->table->getColumn('stringColumn')->setReadonly(true);
        $row = array('id' => 1, 'stringcolumn' => 'test');
        $this->object->sanitizeRowByTable($this->table, $row, false);
    }

    /**
     * @test
     */
    public function testSanitizeValueByColumn()
    {
        $value = 'test';
        $this->assertSame($value, $this->object->sanitizeValueByColumn($this->table->getColumn('stringColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsArray()
    {
        $value = array(1, 2, 3);
        $this->assertSame($value, $this->object->sanitizeValueByColumn($this->table->getColumn('arrayColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsBool()
    {
        $value = "yes";
        $this->assertTrue($this->object->sanitizeValueByColumn($this->table->getColumn('boolColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsBoolTrue()
    {
        $value = true;
        $this->assertTrue($this->object->sanitizeValueByColumn($this->table->getColumn('boolColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsBool1()
    {
        $value = 1;
        $this->assertTrue($this->object->sanitizeValueByColumn($this->table->getColumn('boolColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsColor()
    {
        $value = '#012345';
        $this->assertSame($value, $this->object->sanitizeValueByColumn($this->table->getColumn('colorColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsDate()
    {
        $value = array(
            'month' => '1',
            'day' => '30',
            'year' => '2000'
        );
        $this->assertSame('2000-01-30', $this->object->sanitizeValueByColumn($this->table->getColumn('dateColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsEnumeration()
    {
        $value = '1>';
        $this->assertSame($value, $this->object->sanitizeValueByColumn($this->table->getColumn('enumColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsFileId()
    {
        $value = array();
        $fileId = $this->object->sanitizeValueByColumn($this->table->getColumn('fileColumn'), $value);
        $this->assertInternalType('string', $fileId);
        $this->assertNotEmpty($fileId);
    }

    /**
     * @test
     */
    public function testAsFileIdString()
    {
        $value = "File Name.txt";
        $this->assertSame("Name", $this->object->sanitizeValueByColumn($this->table->getColumn('fileColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsFileIdDeletedException()
    {
        $value = "1";
        $files = array();
        $this->assertSame("", $this->object->sanitizeValueByColumn($this->table->getColumn('fileColumn'), $value, $files));
        $file = $files[0];
        /* @var $file \Yana\Http\Uploads\File */
        $this->assertSame($this->table->getColumn('fileColumn'), $file->getTargetColumn());
    }

    /**
     * @test
     */
    public function testAsFileIdNotFoundException()
    {
        $value = array("error" => \UPLOAD_ERR_NO_FILE);
        $this->assertNull($this->object->sanitizeValueByColumn($this->table->getColumn('fileColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsRangeValue()
    {
        $value = "1";
        $this->assertSame(1.0, $this->object->sanitizeValueByColumn($this->table->getColumn('rangeColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsFloat()
    {
        $value = "-9.25";
        $this->assertSame(-9.25, $this->object->sanitizeValueByColumn($this->table->getColumn('floatColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsHtmlString()
    {
        $value = "Test<b>Test</b>";
        $this->assertSame("Test&lt;b&gt;Test&lt;/b&gt;", $this->object->sanitizeValueByColumn($this->table->getColumn('htmlColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsInet()
    {
        $value = "192.168.0.1";
        $this->assertSame("192.168.0.1", $this->object->sanitizeValueByColumn($this->table->getColumn('inetColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsInteger()
    {
        $value = "-123";
        $this->assertSame(-123, $this->object->sanitizeValueByColumn($this->table->getColumn('intColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsListOfValues()
    {
        $value = array("a" => "1", "b" => "2");
        $this->assertSame(array_values($value), $this->object->sanitizeValueByColumn($this->table->getColumn('listColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsMailAddress()
    {
        $value = 'a@b.c';
        $this->assertSame($value, $this->object->sanitizeValueByColumn($this->table->getColumn('mailColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsSetOfEnumerationItems()
    {
        $value = array('1', '2');
        $this->assertSame($value, $this->object->sanitizeValueByColumn($this->table->getColumn('setColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsPassword()
    {
        $value = "Password1";
        $this->assertSame(md5($value), $this->object->sanitizeValueByColumn($this->table->getColumn('passwordColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsString()
    {
        $value = "Test\nTest";
        $this->assertSame("Test Test", $this->object->sanitizeValueByColumn($this->table->getColumn('stringColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsText()
    {
        $value = "Test\nTest";
        $this->assertSame("Test[br]Test", $this->object->sanitizeValueByColumn($this->table->getColumn('textColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsTimeString()
    {
        $value = array(
            'month' => '1',
            'day' => '30',
            'year' => '2000',
            'hour' => '23',
            'minute' => '45'
        );
        $this->assertStringStartsWith('2000-01-30 23:45:00', $this->object->sanitizeValueByColumn($this->table->getColumn('timeColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsTimestamp()
    {
        $value = mktime(0, 0, 0, 1, 30, 2000);
        $this->assertSame($value, $this->object->sanitizeValueByColumn($this->table->getColumn('timestampColumn'), $value));
    }

    /**
     * @test
     */
    public function testAsUrl()
    {
        $value = 'https://yanaframework.net/';
        $this->assertSame($value, $this->object->sanitizeValueByColumn($this->table->getColumn('urlColumn'), $value));
    }

}
