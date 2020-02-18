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
class ValueConverterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Helpers\ValueConverter
     */
    protected $withPostgresql;

    /**
     * @var \Yana\Db\Helpers\ValueConverter
     */
    protected $withInterbase;

    /**
     * @var \Yana\Db\Helpers\ValueConverter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->withPostgresql = new \Yana\Db\Helpers\ValueConverter(\Yana\Db\DriverEnumeration::POSTGRESQL);
        $this->withInterbase = new \Yana\Db\Helpers\ValueConverter(\Yana\Db\DriverEnumeration::INTERBASE);
        $this->object = new \Yana\Db\Helpers\ValueConverter();
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
    public function testGetQuotingAlgorithm()
    {
        $this->assertEquals(new \Yana\Db\Sql\Quoting\GenericAlgorithm(), $this->object->getQuotingAlgorithm());
    }

    /**
     * @test
     */
    public function testSetQuotingAlgorithm()
    {
        $algorithm = new \Yana\Db\Sql\Quoting\GenericAlgorithm();
        $this->assertSame($algorithm, $this->object->setQuotingAlgorithm($algorithm)->getQuotingAlgorithm());
    }

    /**
     * @test
     */
    public function testGetFileMapper()
    {
        $this->assertEquals(new \Yana\Db\Binaries\FileMapper(), $this->object->getFileMapper());
    }

    /**
     * @test
     */
    public function testSetFileMapper()
    {
        $mapper = new \Yana\Db\Binaries\FileMapper();
        $this->assertSame($mapper, $this->object->setFileMapper($mapper)->getFileMapper());
    }

    /**
     * @test
     */
    public function testConvertToInternalValueSet()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::SET);
        $value = array(1 => array(0 => 'a', 2 => 'b'), 2 => 'c');
        $this->assertSame($expected = 'b', $this->object->convertToInternalValue($value, $column, '1.2'));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueSetNoKey()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::SET);
        $value = array(1 => array(0 => 'a', 2 => 'b'), 2 => 'c');
        $this->assertSame($value, $this->object->convertToInternalValue($value, $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueSetJson()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::SET);
        $value = array(1 => array(0 => 'a', 2 => 'b'), 2 => 'c');
        $this->assertSame($expected = 'b', $this->object->convertToInternalValue(\json_encode($value), $column, '1.2'));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueSetNull()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::SET);
        $value = array(1 => array(0 => 'a', 2 => 'b'), 2 => 'c');
        $this->assertNull($this->object->convertToInternalValue($value, $column, '3'));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueSetInvalidValue()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::SET);
        $value = 123;
        $this->assertNull($this->object->convertToInternalValue($value, $column, '1.2'));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueBool()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::BOOL);
        $this->assertTrue($this->object->convertToInternalValue("1", $column));
        $this->assertTrue($this->object->convertToInternalValue("T", $column));
        $this->assertTrue($this->object->convertToInternalValue("yes", $column));
        $this->assertTrue($this->object->convertToInternalValue("true", $column));
        $this->assertTrue($this->object->convertToInternalValue("TRUE", $column));
        $this->assertTrue($this->object->convertToInternalValue(1, $column));
        $this->assertTrue($this->object->convertToInternalValue(true, $column));
        $this->assertFalse($this->object->convertToInternalValue("F", $column));
        $this->assertFalse($this->object->convertToInternalValue("0", $column));
        $this->assertFalse($this->object->convertToInternalValue("", $column));
        $this->assertFalse($this->object->convertToInternalValue(0, $column));
        $this->assertFalse($this->object->convertToInternalValue(2, $column));
        $this->assertFalse($this->object->convertToInternalValue(false, $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueDate()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::DATE);
        $this->assertSame(1577833200, $this->object->convertToInternalValue("2020-01-01", $column));
        $this->assertNull($this->object->convertToInternalValue("abc", $column));
        $this->assertNull($this->object->convertToInternalValue(null, $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueHtml()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::HTML);
        $this->assertSame("&lt;body/&gt;", $this->object->convertToInternalValue("<body/>", $column));
        $this->assertSame("123", $this->object->convertToInternalValue(123, $column));
        $this->assertNull($this->object->convertToInternalValue(array(1, 2, 3), $column));
        $this->assertNull($this->object->convertToInternalValue(null, $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueFileNull()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::FILE);
        $this->assertNull($this->object->convertToInternalValue("0", $column));
        $this->assertNull($this->object->convertToInternalValue("no-such-file", $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueFile()
    {
        $configuration = new \Yana\Db\Binaries\Configuration();
        $configuration->setDirectory($directory = CWD . 'resources' . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR);
        $fileMapper = new \Yana\Db\Binaries\FileMapper($configuration);
        $this->object->setFileMapper($fileMapper);
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::IMAGE);
        $this->assertSame($directory . 'test1.png', $this->object->convertToInternalValue("test1", $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueRange()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::RANGE);
        $this->assertSame(123.0, $this->object->convertToInternalValue("123", $column));
        $this->assertSame(123.0, $this->object->convertToInternalValue(123, $column));
        $this->assertSame(123.456, $this->object->convertToInternalValue(123.456, $column));
        $this->assertSame(-123.456, $this->object->convertToInternalValue(-123.456, $column));
        $this->assertNull($this->object->convertToInternalValue("abc", $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueFloat()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT)->setLength(6, 2);
        $this->assertSame(123.0, $this->object->convertToInternalValue("123", $column));
        $this->assertSame(123.0, $this->object->convertToInternalValue(123, $column));
        $this->assertSame(123.46, $this->object->convertToInternalValue(123.456, $column));
        $this->assertSame(-123.46, $this->object->convertToInternalValue(-123.456, $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueFloatUnsigned()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT)->setUnsigned(true);
        $this->assertSame(123.456, $this->object->convertToInternalValue("123.456", $column));
        $this->assertSame(123.456, $this->object->convertToInternalValue("-123.456", $column));
        $this->assertSame(123.456, $this->object->convertToInternalValue(123.456, $column));
        $this->assertSame(123.456, $this->object->convertToInternalValue(-123.456, $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueFloatZerofill()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT)->setLength(6, 2)->setFixed(true);
        $this->assertSame("0012.10", $this->object->convertToInternalValue("-12.1", $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueFloatFixed()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT)->setLength(8)->setFixed(true);
        $this->assertSame("00123.456", $this->object->convertToInternalValue("123.456", $column));
        $this->assertSame("00123.456", $this->object->convertToInternalValue("-123.456", $column));
        $this->assertSame("000012.34", $this->object->convertToInternalValue(12.34, $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueInteger()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::INT);
        $this->assertSame(123, $this->object->convertToInternalValue("123.456", $column));
        $this->assertSame(-123, $this->object->convertToInternalValue("-123.456", $column));
        $this->assertSame(12, $this->object->convertToInternalValue(12.34, $column));
        $this->assertNull($this->object->convertToInternalValue("abc", $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueIntegerUnsigned()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::INT)->setUnsigned(true);
        $this->assertSame(123, $this->object->convertToInternalValue("123.456", $column));
        $this->assertSame(123, $this->object->convertToInternalValue("-123.456", $column));
        $this->assertSame(123, $this->object->convertToInternalValue(123.456, $column));
        $this->assertSame(123, $this->object->convertToInternalValue(-123.456, $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueIntegerZerofill()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::INT)->setLength(6)->setFixed(true);
        $this->assertSame("000012", $this->object->convertToInternalValue("-12.1", $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueTimestamp()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::TIMESTAMP);
        $this->assertSame(12345, $this->object->convertToInternalValue("12345.67", $column));
        $this->assertSame(-12345, $this->object->convertToInternalValue("-12345.67", $column));
        $this->assertNull($this->object->convertToInternalValue("abc", $column));
    }

    /**
     * @test
     */
    public function testConvertToInternalValueString()
    {
        $column = new \Yana\Db\Ddl\Column(__FUNCTION__);
        $column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::STRING);
        $this->assertSame("Abc", $this->object->convertToInternalValue("Abc", $column));
        $this->assertSame("12345", $this->object->convertToInternalValue(12345, $column));
        $this->assertNull($this->object->convertToInternalValue(array(123), $column));
    }

    /**
     * @test
     */
    public function testConvertRowToStringEmpty()
    {
        $this->assertSame(array(), $this->object->convertRowToString(new \Yana\Db\Ddl\Table('Table'), array()));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\FieldNotFoundException
     */
    public function testConvertRowToStringFieldNotFoundException()
    {
        $this->object->convertRowToString(new \Yana\Db\Ddl\Table('Table'), array(1));
    }

    /**
     * @test
     */
    public function testConvertRowToString()
    {
        $table = new \Yana\Db\Ddl\Table('Table');
        $table->addColumn('a', \Yana\Db\Ddl\ColumnTypeEnumeration::INT);
        $table->addColumn('b', \Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT);
        $this->assertSame(array('a' => '12', 'b' => '45.6'), $this->object->convertRowToString($table, array('a' => 12.3, 'b' => 45.6)));
    }

    /**
     * @test
     */
    public function testConvertValueToString()
    {
        $this->assertSame('NULL', $this->object->convertValueToString(null, 'data type doesnt matter'));
    }

    /**
     * @test
     */
    public function testConvertValueToStringFloat()
    {
        $this->assertSame('-123.45', $this->object->convertValueToString(-123.45, \Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT));
        $this->assertSame('-123.45', $this->object->convertValueToString('-123.45', \Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT));
        $this->assertSame('-123', $this->object->convertValueToString('-123.00', \Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT));
    }

    /**
     * @test
     */
    public function testConvertValueToStringInteger()
    {
        $this->assertSame('123', $this->object->convertValueToString(123, \Yana\Db\Ddl\ColumnTypeEnumeration::INT));
        $this->assertSame('-123', $this->object->convertValueToString(-123, \Yana\Db\Ddl\ColumnTypeEnumeration::INT));
        $this->assertSame('-123', $this->object->convertValueToString(-123.45, \Yana\Db\Ddl\ColumnTypeEnumeration::INT));
        $this->assertSame('-123', $this->object->convertValueToString('-123.45', \Yana\Db\Ddl\ColumnTypeEnumeration::INT));
        $this->assertSame('-123', $this->object->convertValueToString('-123.00', \Yana\Db\Ddl\ColumnTypeEnumeration::INT));
    }

    /**
     * @test
     */
    public function testConvertValueToStringBool()
    {
        $this->assertSame('1', $this->object->convertValueToString(true, \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL));
        $this->assertSame('0', $this->object->convertValueToString('true', \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL));
        $this->assertSame('0', $this->object->convertValueToString(1, \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL));
        $this->assertSame('TRUE', $this->withPostgresql->convertValueToString(true, \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL));
        $this->assertSame('T', $this->withInterbase->convertValueToString(true, \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL));
        $this->assertSame('0', $this->object->convertValueToString(false, \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL));
        $this->assertSame('0', $this->object->convertValueToString('0', \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL));
        $this->assertSame('FALSE', $this->withPostgresql->convertValueToString(false, \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL));
        $this->assertSame('F', $this->withInterbase->convertValueToString(false, \Yana\Db\Ddl\ColumnTypeEnumeration::BOOL));
    }

    /**
     * @test
     */
    public function testConvertValueToStringDate()
    {
        $quotingAlgorithm = new \Yana\Db\Sql\Quoting\NullAlgorithm();
        $this->object->setQuotingAlgorithm($quotingAlgorithm);
        $this->assertSame(date('c', 0), $this->object->convertValueToString('abc', \Yana\Db\Ddl\ColumnTypeEnumeration::DATE));
        $this->assertSame(date('c', 946681200), $this->object->convertValueToString(strtotime('2000-01-01'), \Yana\Db\Ddl\ColumnTypeEnumeration::DATE));
    }

    /**
     * @test
     */
    public function testConvertValueToStringSet()
    {
        $quotingAlgorithm = new \Yana\Db\Sql\Quoting\NullAlgorithm();
        $this->object->setQuotingAlgorithm($quotingAlgorithm);
        $this->assertSame('"abc"', $this->object->convertValueToString('abc', \Yana\Db\Ddl\ColumnTypeEnumeration::SET));
        $this->assertSame("[1,2,3]", $this->object->convertValueToString(array(1, 2, 3), \Yana\Db\Ddl\ColumnTypeEnumeration::SET));
    }

}
