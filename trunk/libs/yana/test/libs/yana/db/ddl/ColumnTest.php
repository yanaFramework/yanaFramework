<?php
/**
 * PHPUnit test-case
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

namespace Yana\Db\Ddl;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';


/**
 * @package  test
 */
class ColumnTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Column
     */
    protected $column;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->column = new \Yana\Db\Ddl\Column('column');
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
    public function getSupportedTypes()
    {
        $getSupported = $this->column->getSupportedTypes();
        $this->assertContains("bool", $getSupported, "supported types should at least contain bool, integer and text");
        $this->assertContains("integer", $getSupported, "supported types should at least contain bool, integer and text");
        $this->assertContains("text", $getSupported, "supported types should at least contain bool, integer and text");
        foreach ($getSupported as $type)
        {
            $this->assertNotEmpty($type);
            $this->assertInternalType('string', $type);
        }
    }

    /**
     * @test
     */
    public function testGetParent()
    {
        $this->assertNull($this->column->getParent());
    }

    /**
     * @test
     */
    public function testGetType()
    {
        $this->assertNull($this->column->getType());
    }

    /**
     * @test
     */
    public function testIsFile()
    {
        $this->assertFalse($this->column->isFile());
        $this->assertFalse($this->column->setType('string')->isFile());
        $this->assertTrue($this->column->setType('file')->isFile());
        $this->assertTrue($this->column->setType('image')->isFile());
    }

    /**
     * @test
     */
    public function testSetType()
    {
        $this->assertEquals('string', $this->column->setType('string')->getType());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetTypeInvalidArgumentException()
    {
        $this->column->setType('0');
    }

    /**
     * @test
     */
    public function testGetTitle()
    {
        $this->assertNull($this->column->getTitle());
    }

    /**
     * @test
     */
    public function testSetTitle()
    {
        $this->assertEquals('My Title', $this->column->setTitle('My Title')->getTitle());
        $this->assertNull($this->column->setTitle('')->getTitle());
    }

    /**
     * @test
     */
    public function testGetPattern()
    {
        $this->assertNull($this->column->getPattern());
    }

    /**
     * @test
     */
    public function testSetPattern()
    {
        $this->assertEquals('My Pattern', $this->column->setPattern('My Pattern')->getPattern());
        $this->assertNull($this->column->setPattern('')->getPattern());
    }

    /**
     * @test
     */
    public function testGetDescription()
    {
        $this->assertNull($this->column->getDescription());
    }

    /**
     * @test
     */
    public function testSetDescription()
    {
        $this->assertEquals('My Description', $this->column->setDescription('My Description')->getDescription());
        $this->assertNull($this->column->setDescription('')->getDescription());
    }

    /**
     * @test
     */
    public function testGetGrants()
    {
        $this->assertSame(array(), $this->column->getGrants());
    }

    /**
     * @test
     */
    public function testSetGrant()
    {
        $grant1 = new \Yana\Db\Ddl\Grant();
        $grant2 = new \Yana\Db\Ddl\Grant();

        $this->column->setGrant($grant1);
        $this->column->setGrant($grant2);

        $this->assertSame(array($grant1, $grant2), $this->column->getGrants());
    }

    /**
     * @test
     */
    public function testAddGrant()
    {
        $grant1 = $this->column->addGrant();
        $grant2 = $this->column->addGrant();
        $grant3 = $this->column->addGrant('user', 'role', 10);
        $this->assertTrue($grant3 instanceof \Yana\Db\Ddl\Grant);
        $this->assertSame(array($grant1, $grant2, $grant3), $this->column->getGrants());
    }

    /**
     * @test
     */
    public function testDropGrants()
    {
        $this->column->addGrant();
        $this->column->addGrant();
        $this->column->addGrant('user', 'role', 10);
        $this->assertSame(array(), $this->column->dropGrants()->getGrants());
    }

    /**
     * @test
     */
    public function testIsUpdatable()
    {
       $this->assertTrue($this->column->isUpdatable());
    }

    /**
     * @test
     */
    public function testIsReadonly()
    {
       $this->assertFalse($this->column->isReadonly());
    }

    /**
     * @test
     */
    public function testSetReadonly()
    {
       $this->assertTrue($this->column->setReadonly(true)->isReadonly());
       $this->assertFalse($this->column->setReadonly(false)->isReadonly());
    }

    /**
     * @test
     */
    public function testIsNullable()
    {
       $this->assertTrue($this->column->isNullable());
    }

    /**
     * @test
     */
    public function testSetNullable()
    {
       $this->assertFalse($this->column->setNullable(false)->isNullable());
       $this->assertTrue($this->column->setNullable(true)->isNullable());
    }

    /**
     * @test
     */
    public function testIsUnsigned()
    {
       $this->assertFalse($this->column->isUnsigned());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testSetUnsignedNotImplementedException()
    {
        $this->column->setUnsigned(true);
    }

    /**
     * @test
     */
    public function testSetUnsigned()
    {
       $this->column->setType('integer');
       $this->assertTrue($this->column->setUnsigned(true)->isUnsigned());
       $this->assertFalse($this->column->setUnsigned(false)->isUnsigned());
    }

    /**
     * @test
     */
    public function testSetUnsignedFixed()
    {
       $this->column->setType('integer');
       $this->assertTrue($this->column->setFixed(true)->isUnsigned());
    }

    /**
     * @test
     */
    public function testIsUnique()
    {
       $this->assertFalse($this->column->isUnique());
    }

    /**
     * @test
     */
    public function testSetUnique()
    {
       $this->assertTrue($this->column->setUnique(true)->isUnique());
       $this->assertFalse($this->column->setUnique(false)->isUnique());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testSetAutoIncrementNotImplementedException()
    {
        $this->column->setAutoIncrement(true);
    }

    /**
     * @test
     */
    public function testSetAutoIncrement()
    {
       $this->column->setType('integer');
       $this->assertTrue($this->column->setAutoIncrement(true)->isAutoIncrement());
    }

    /**
     * @test
     */
    public function testSetAutoIncrement2()
    {
       $this->column->setType('integer');
       $this->column->setAutoIncrement(false);
       $this->assertFalse($this->column->setAutoIncrement(false)->isAutoIncrement());
    }

    /**
     * @test
     */
    public function testSetAutoFill()
    {
        // expected value is true
        $this->column->setType('integer');
        $this->assertTrue($this->column->setAutoFill(true)->isAutoFill());
    }

    /**
     * @test
     */
    public function testSetAutoFillInet()
    {
        $this->column->setType('inet');
        $this->assertTrue($this->column->setAutoFill(true)->isAutoFill());
        $this->assertSame('REMOTE_ADDR', $this->column->getDefault());
    }

    /**
     * @test
     */
    public function testSetAutoFillInetReset()
    {
        $this->column->setType('inet');
        $this->column->setDefault('REMOTE_ADDR');
        $this->assertFalse($this->column->setAutoFill(false)->isAutoFill());
        $this->assertNull($this->column->getDefault());
    }

    /**
     * @test
     */
    public function testSetAutoFillTime()
    {
        $this->column->setType('time');
        $this->assertTrue($this->column->setAutoFill(true)->isAutoFill());
        $this->assertSame('CURRENT_TIMESTAMP', $this->column->getDefault());
    }

    /**
     * @test
     */
    public function testSetAutoFillTimeReset()
    {
        $this->column->setType('time');
        $this->column->setDefault('CURRENT_TIMESTAMP');
        $this->assertFalse($this->column->setAutoFill(false)->isAutoFill());
        $this->assertNull($this->column->getDefault());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testSetAutoFillNotImplementedException()
    {
        $this->column->setType('image');
        $this->column->setAutoFill(true);
    }

    /**
     * @test
     */
    public function testIsFixed()
    {
        $this->assertFalse($this->column->isFixed());
    }

    /**
     * @test
     */
    public function testSetFixed()
    {
        $this->assertTrue($this->column->setFixed(true)->isFixed());
        $this->assertFalse($this->column->setFixed(false)->isFixed());
    }

    /**
     * @test
     */
    public function testIsAutoIncrement()
    {
        $this->assertFalse($this->column->isAutoIncrement());
    }

    /**
     * @test
     */
    public function testIsAutoFill()
    {
        $this->assertFalse($this->column->isAutoFill());
    }

    /**
     * @test
     */
    public function testIsForeignKey()
    {
        $this->assertFalse($this->column->isForeignKey());
    }

    /**
     * @test
     */
    public function testIsForeignKeyReference()
    {
        $this->assertTrue($this->column->setType('reference')->isForeignKey());
    }

    /**
     * Foreign-key
     *
     * @test
     */
    public function testIsForeignKeyParent()
    {
        $database = new \Yana\Db\Ddl\Database();
        $sourceTable = $database->addTable('table');
        $targetTable = $database->addTable('table_target');
        $targetTable->addColumn('testcolumn_target', 'integer');
        $targetTable->setPrimaryKey('testcolumn_target');
        $sourceTable->addColumn('testcolumn', 'integer');
        $fk = $sourceTable->addForeignKey('table_target', 'cfkey');
        $fk->setColumn('testcolumn');
        $this->assertTrue($sourceTable->getColumn('testcolumn')->isForeignKey());
    }

    /**
     * @test
     */
    public function testIsPrimaryKey()
    {
        $this->assertFalse($this->column->isPrimaryKey());
    }

    /**
     * @test
     */
    public function testIsNumber()
    {
        $this->assertFalse($this->column->isNumber());
        $this->assertFalse($this->column->setType('string')->isNumber());
        $this->assertTrue($this->column->setType('integer')->isNumber());
    }

    /**
     * @test
     */
    public function testGetLength()
    {
        $this->assertNull($this->column->getLength());
    }

    /**
     * @test
     */
    public function testSetLength()
    {
        $this->column->setLength(10, 2);
        $this->assertEquals(10, $this->column->getLength());
        $this->assertEquals(2, $this->column->getPrecision());
    }

    /**
     * @test
     */
    public function testGetSize()
    {
        $this->assertNull($this->column->getSize());
    }

    /**
     * @test
     */
    public function testGetPrecision()
    {
        $this->assertNull($this->column->getPrecision());
    }

    /**
     * @test
     */
    public function testGetImageSettings()
    {
        $this->assertSame(array('width' => null, 'height' => null, 'ratio' => null, 'background' => null), $this->column->getImageSettings());
    }

    /**
     * @test
     */
    public function testSetImageSettings()
    {
        $expected = array('width' => $w = 1, 'height' => $h = 2, 'ratio' => $r = true, 'background' => $b = 'White');
        $this->assertSame($expected, $this->column->setImageSettings($w, $h, $r, $b)->getImageSettings());
    }

    /**
     * @test
     */
    public function testGetReferenceSettings()
    {
        $this->assertEquals(new \Yana\Db\Ddl\Reference('', '', ''), $this->column->getReferenceSettings());
    }

    /**
     * @test
     */
    public function testGetEnumerationItems()
    {
        $this->assertSame(array(), $this->column->getEnumerationItems());
    }

    /**
     * @test
     */
    public function testGetRangeMax()
    {
        $this->assertNull($this->column->getRangeMax());
    }

    /**
     * @test
     */
    public function testGetRangeMin()
    {
        $this->assertNull($this->column->getRangeMin());
    }

    /**
     * @test
     */
    public function testGetRangeStep()
    {
        $this->assertNull($this->column->getRangeStep());
    }

    /**
     * @test
     */
    public function testSetRange()
    {
        $this->column->setRange(0.0, 100.0, 0.5);
        $this->assertEquals(0.0, $this->column->getRangeMin(), "Unable to set min attribute.");
        $this->assertEquals(100.0, $this->column->getRangeMax(), "Unable to set max attribute.");
        $this->assertEquals(0.5, $this->column->getRangeStep(), "Unable to set step attribute.");
    }

    /**
     * @test
     */
    public function testHasIndex()
    {
        $this->assertFalse($this->column->hasIndex());
    }

    /**
     * @test
     */
    public function testHasIndex2()
    {
        $table = new \Yana\Db\Ddl\Table('t');
        $column = $table->addColumn('test', 'integer');
        $table->addIndex()->addColumn('test');
        $this->assertTrue($column->hasIndex());
    }

    /**
     * @test
     */
    public function testGetAutoValue()
    {
        $this->assertNull($this->column->getAutoValue());
    }

    /**
     * @test
     */
    public function testGetAutoValueInet()
    {
        $this->assertSame('0.0.0.0', $this->column->setType('inet')->setAutoFill(true)->getAutoValue());
    }

    /**
     * @test
     */
    public function testGetAutoValueInet2()
    {
        $this->assertSame('1.1.1.1', $this->column->setType('inet')->setDefault('1.1.1.1')->getAutoValue());
    }

    /**
     * @test
     */
    public function testGetAutoValueTime()
    {
        $format = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[\+\-]\d{2}:\d{2}$/s';
        $this->assertRegExp($format, $this->column->setType('time')->setAutoFill(true)->getAutoValue());
    }

    /**
     * @test
     */
    public function testGetAutoValueTime2()
    {
        $this->assertSame('1234', $this->column->setType('time')->setDefault('1234')->getAutoValue());
    }

    /**
     * @test
     */
    public function testGetAutoValueDate()
    {
        $format = '/^\d{4}-\d{2}-\d{2}$/s';
        $this->assertRegExp($format, $this->column->setType('date')->setAutoFill(true)->getAutoValue());
    }

    /**
     * @test
     */
    public function testGetAutoValueDate2()
    {
        $this->assertSame('1234', $this->column->setType('date')->setDefault('1234')->getAutoValue());
    }

    /**
     * @test
     */
    public function testGetAutoValueTimestamp()
    {
        $this->assertRegExp('/^\d{10}$/', (string) $this->column->setType('timestamp')->setAutoFill(true)->getAutoValue());
    }

    /**
     * @test
     */
    public function testGetAutoValueTimestamp2()
    {
        $this->assertSame('1234', $this->column->setType('timestamp')->setDefault('1234')->getAutoValue());
    }

    /**
     * @test
     */
    public function testGetAutoValueString()
    {
        // must be empty since no user is currently logged in
        $this->assertSame('', (string) $this->column->setType('string')->setName('user_created')->getAutoValue());
        $this->assertSame('', (string) $this->column->setType('string')->setName('user_modified')->getAutoValue());
    }

    /**
     * @test
     */
    public function testGetAutoValueString2()
    {
        $this->assertSame('default', (string) $this->column->setType('string')->setName('profile_id')->getAutoValue());
    }

    /**
     * @test
     */
    public function testGetAutoValueString3()
    {
        $this->assertSame('1234', $this->column->setType('string')->setDefault('1234')->getAutoValue());
    }

    /**
     * @test
     */
    public function testGetReferenceColumn()
    {
        $this->assertSame($this->column, $this->column->getReferenceColumn());
    }

    /**
     * @test
     */
    public function testGetReferenceColumnParent()
    {
        $database = new \Yana\Db\Ddl\Database();
        $sourceTable = $database->addTable('table');
        $targetTable = $database->addTable('table_target');
        $targetTable->addColumn('testcolumn_target', 'integer');
        $targetTable->setPrimaryKey('testcolumn_target');
        $sourceTable->addColumn('testcolumn', 'reference')->setReferenceSettings('table_target', 'testcolumn_target');
        $fk = $sourceTable->addForeignKey('table_target', 'cfkey');
        $fk->setColumn('testcolumn');
        $this->assertSame($targetTable->getColumn('testcolumn_target'), $sourceTable->getColumn('testcolumn')->getReferenceColumn());
    }

    /**
     * @test
     */
    public function testGetReferenceColumnParent2()
    {
        $database = new \Yana\Db\Ddl\Database();
        $sourceTable = $database->addTable('table');
        $targetTable = $database->addTable('table_target');
        $targetTable->addColumn('testcolumn_target', 'integer');
        $targetTable->setPrimaryKey('testcolumn_target');
        $sourceTable->addColumn('testcolumn', 'reference');
        $fk = $sourceTable->addForeignKey('table_target', 'cfkey');
        $fk->setColumn('testcolumn');
        $this->assertSame($targetTable->getColumn('testcolumn_target'), $sourceTable->getColumn('testcolumn')->getReferenceColumn());
    }

    /**
     * @test
     */
    public function testInterpretValue()
    {
        $this->assertSame('test', $this->column->setType('string')->interpretValue('test'));
    }

    /**
     * @test
     */
    public function testInterpretValueArray()
    {
        $this->assertSame(array('test'), $this->column->setType('array')->interpretValue('["test"]'));
    }

    /**
     * @test
     */
    public function testInterpretValueArray2()
    {
        $this->assertSame(2, $this->column->setType('array')->interpretValue('{"a":1,"b":2}', 'b'));
    }

    /**
     * @test
     */
    public function testInterpretValueArray3()
    {
        $this->assertNull($this->column->setType('array')->interpretValue('{"a":1,"b":2}', 'c'));
    }

    /**
     * @test
     */
    public function testInterpretValueBool()
    {
        $this->assertTrue($this->column->setType('bool')->interpretValue('test'));
    }

    /**
     * @test
     */
    public function testInterpretValueDate()
    {
        $this->assertNull($this->column->setType('date')->interpretValue(1));
    }

    /**
     * @test
     */
    public function testInterpretValueDate2()
    {
        $this->assertSame(\strtotime('2000-01-01'), $this->column->setType('date')->interpretValue('2000-01-01'));
    }

    /**
     * @test
     */
    public function testInterpretValueHtml()
    {
        $this->assertSame('', $this->column->setType('html')->interpretValue(array()));
    }

    /**
     * @test
     */
    public function testInterpretValueHtml2()
    {
        $this->assertSame('<p>test</p>', $this->column->setType('html')->interpretValue('&lt;p&gt;test&lt;/p&gt;'));
    }

    /**
     * @test
     */
    public function testInterpretValueColor()
    {
        $this->assertSame('', $this->column->setType('color')->interpretValue(array()));
    }

    /**
     * @test
     */
    public function testInterpretValueColor2()
    {
        $this->assertSame('red', $this->column->setType('color')->interpretValue('red'));
    }

    /**
     * @test
     */
    public function testInterpretValueTimestamp()
    {
        $this->assertNull($this->column->setType('timestamp')->interpretValue(array()));
    }

    /**
     * @test
     */
    public function testInterpretValueTimestamp2()
    {
        $this->assertSame(1234, $this->column->setType('timestamp')->interpretValue('1234'));
    }

    /**
     * @test
     */
    public function testInterpretValueFile()
    {
        $this->assertNull($this->column->setType('file')->interpretValue('no-such-file'));
    }

    /**
     * @test
     */
    public function testInterpretValueFile2()
    {
        $this->assertNull($this->column->setType('file')->interpretValue(''));
    }

    /**
     * @test
     */
    public function testInterpretInteger()
    {
        $this->assertNull($this->column->setType('integer')->interpretValue('no-such-number'));
    }

    /**
     * @test
     */
    public function testInterpretInteger2()
    {
        $this->assertSame(1234, $this->column->setType('integer')->interpretValue('1234'));
    }

    /**
     * @test
     */
    public function testInterpretInteger3()
    {
        $this->assertSame(1, $this->column->setType('integer')->interpretValue('1.234'));
    }

    /**
     * @test
     */
    public function testInterpretIntegerUnsigned()
    {
        $this->assertSame(1234, $this->column->setType('integer')->setUnsigned(true)->interpretValue('-1234'));
    }

    /**
     * @test
     */
    public function testInterpretIntegerFixed()
    {
        $this->assertSame('0012', $this->column->setType('integer')->setLength(4)->setFixed(true)->interpretValue('-12'));
    }

    /**
     * @test
     */
    public function testInterpretFloat()
    {
        $this->assertNull($this->column->setType('float')->interpretValue('no-such-number'));
    }

    /**
     * @test
     */
    public function testInterpretFloat2()
    {
        $this->assertSame(1.2, $this->column->setType('float')->interpretValue('1.2'));
    }

    /**
     * @test
     */
    public function testInterpretFloatUnsigned()
    {
        $this->assertSame(1.2, $this->column->setType('float')->setUnsigned(true)->interpretValue('-1.2'));
    }

    /**
     * @test
     */
    public function testInterpretFloatPrecision()
    {
        $this->assertSame(-1.56, $this->column->setType('float')->setLength(3, 2)->interpretValue('-1.5555'));
    }

    /**
     * @test
     */
    public function testInterpretFloatFixed()
    {
        $this->assertSame('0001.20', $this->column->setType('float')->setLength(6, 2)->setFixed(true)->interpretValue('-1.2'));
    }

    /**
     * @test
     */
    public function testSetSize()
    {
        $this->column->setSize(5);
        $this->assertNull($this->column->getPrecision());
        $this->assertEquals(5, $this->column->getSize());
    }

    /**
     * Size and precision with invalid argument
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     *
     * @test
     */
    public function testSetSizeInvalidArgumentException()
    {
        // \Yana\Db\Ddl\Column
        $this->column->setLength(1, 2);
    }

    /**
     * Image-settings
     *
     * @test
     */
    public function testImageSettings()
    {
        // DDL Column
        $width = 30;
        $height = 15;
        $ratio = true;
        $background = 'description';
        $expected = array('width' => $width, 'height' => $height, 'ratio' => $ratio, 'background' => $background);
        $this->column->setImageSettings($width, $height, $ratio, $background);
        $get = $this->column->getImageSettings();

        $expected = array('width' => $width, 'height' => $height, 'ratio' => $ratio, 'background' => $background);
        $this->assertInternalType('array', $get, 'assert failed, \Yana\Db\Ddl\Column : the value is not from type Array');
        $this->assertEquals($expected, $get, 'assert failed, \Yana\Db\Ddl\Column : the image settings are not set');

        $expected = array('width' => '', 'height' => '', 'ratio' => '', 'background' => '');
        $this->column->setImageSettings();
        $get = $this->column->getImageSettings();
        $this->assertInternalType('array', $get, 'assert failed, \Yana\Db\Ddl\Column : the value is not from type Array');
        $this->assertEquals($expected, $get, 'assert failed, \Yana\Db\Ddl\Column : the image settings are not set');
    }

    /**
     * Reference-settings
     *
     * @test
     */
    public function testSetReferenceSettings()
    {
        // DDL Column
        $table = 'sometable';
        $column = 'somecolumn';
        $label = 'somelabel';

        $expected = new \Yana\Db\Ddl\Reference($table, $column, $label);
        $this->column->setReferenceSettings($table, $column, $label);
        $get = $this->column->getReferenceSettings();
        $this->assertTrue($get instanceof \Yana\Db\Ddl\Reference, 'Instance of \Yana\Db\Ddl\Reference expected');
        $this->assertEquals($expected, $get, 'Expected values not found in returned \Yana\Db\Ddl\Reference');
    }

    /**
     * @test
     */
    public function testGetDefaults()
    {
        $this->assertSame(array(), $this->column->getDefaults());
    }

    /**
     * @test
     */
    public function testGetDefaults2()
    {
        $this->column->setDefault('a');
        $this->column->setDefault('b', 'mysql');
        $getAll = $this->column->getDefaults();
        $this->assertInternalType('array', $getAll, 'assert failed, \Yana\Db\Ddl\Column : the value is not from type array');
        $this->assertEquals(2, count($getAll), 'assert failed, \Yana\Db\Ddl\Column :the values should be equal - expected number 2');
    }

    /**
     * @test
     */
    public function testGetDefault()
    {
        $this->assertNull($this->column->getDefault());
    }

    /**
     * @test
     */
    public function testSetDefault()
    {
        $this->column->setDefault('a');
        $this->column->setDefault('b', 'mysql');
        $get = $this->column->getDefault('mysql');
        $this->assertInternalType('string', $get, 'assert failed, \Yana\Db\Ddl\Column : the value is not from type string');
        $this->assertEquals('b', $get, 'assert failed, \Yana\Db\Ddl\Column : the variables should be equal - expected key of value "mysql"');

        $get = $this->column->getDefault('oracle');
        $this->assertEquals('a', $get, 'Function getDefault() must fall back to "generic" if setting is not found.');
        $this->column->setDefault('');
        $get = $this->column->getDefault('oracle');
        $this->assertEquals(0, strlen($get), 'the values should be equal - 0 expected when value does not exist in array');
    }

    /**
     * @test
     */
    public function testSetEnumerationItems()
    {
        $array = array('aa' => '20', 'bb' => '30', 'cc' => '50');
        $this->assertEquals($array, $this->column->setEnumerationItems($array)->getEnumerationItems());
    }

    /**
     * @test
     */
    public function testSetEnumerationItem()
    {
        $array = array('aa' => '20', 'bb' => '30', 'cc' => '50');
        $this->assertEquals($array, $this->column->setEnumerationItems($array)->getEnumerationItems());
        $this->column->setEnumerationItem('cc', '90');
        $get = $this->column->getEnumerationItems();
        $this->assertNotEquals($array, $get, 'the values should not be equal, the key "cc" was manipulate with other value');

        $validate = array('aa' => '20', 'bb' => '30', 'cc' => '90');
        $this->assertEquals($validate, $get);
    }

    /**
     * @test
     */
    public function testGetEnumerationItem()
    {
        $this->assertNull($this->column->getEnumerationItem('no such item'));
    }

    /**
     * @test
     */
    public function testGetEnumerationItem2()
    {
        $this->assertSame('50', $this->column->setEnumerationItems(array('aa' => '20', 'cc' => '50'))->getEnumerationItem('cc'));
    }

    /**
     * @test
     */
    public function testGetEnumerationItemNames()
    {
        $array = array('aa' => '20', 'bb' => '30', 'cc' => '50');
        $this->assertEquals(array_keys($array), $this->column->setEnumerationItems($array)->getEnumerationItemNames());
        $array['dd'] = '120';
        $this->assertEquals(array_keys($array), $this->column->setEnumerationItems($array)->getEnumerationItemNames());
    }

    /**
     * @test
     */
    public function testGetEnumerationItemNames2()
    {
        $array = array('a' => '1', 'b' => '2', 'group1' => array('c' => '3', 'd' => '4'), 'group2' => array('e' => '5'));
        $getItemNames = $this->column->setEnumerationItems($array)->getEnumerationItemNames();
        $this->assertEquals(array('a', 'b', 'c', 'd', 'e'), $getItemNames);
    }

    /**
     * @test
     */
    public function testDropEnumerationItem()
    {
        $array = array('aa' => '20', 'bb' => '30', 'cc' => '50');
        $this->assertNull($this->column->setEnumerationItems($array)->dropEnumerationItem('bb'));
        $this->assertSame(array('aa' => '20', 'cc' => '50'), $this->column->getEnumerationItems());
    }

    /**
     * @test
     */
    public function testDropEnumerationItems()
    {
        $array = array('aa' => '20', 'cc' => '50');
        $this->column->setEnumerationItems($array);

        $get = $this->column->dropEnumerationItems()->getEnumerationItems();
        $this->assertInternalType('array', $get);
        $this->assertEquals(0, count($get));
    }

    /**
     * Drop non-existing enumeration item.
     *
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     * @test
     */
    public function testDropEnumerationItemNotFoundException()
    {
        $array = array('1' => '2');
        $this->column->setEnumerationItems($array);
        $this->column->dropEnumerationItem('no_item');
    }

    /**
     * @test
     */
    public function testAddConstraint()
    {
        $constraint = $this->column->addConstraint("1", "Name", "MySQL");
        $this->assertSame("1", $constraint->getConstraint());
        $this->assertSame("name", $constraint->getName());
        $this->assertSame("mysql", $constraint->getDBMS());
    }

    /**
     * @test
     */
    public function testGetConstraint()
    {
        $this->column->addConstraint("1", "Name", "MySQL");
        $constraint = $this->column->getConstraint("naMe", "Mysql");
        $this->assertSame("1", $constraint->getConstraint());
        $this->assertSame("name", $constraint->getName());
        $this->assertSame("mysql", $constraint->getDBMS());
    }

    /**
     * @test
     */
    public function testGetConstraints()
    {
        $constraint1 = new \Yana\Db\Ddl\Constraint();
        $constraint1->setConstraint("1");
        $constraint2 = new \Yana\Db\Ddl\Constraint();
        $constraint2->setConstraint("2");
        $constraint3 = new \Yana\Db\Ddl\Constraint();
        $constraint3->setConstraint("3");
        $this->column->addConstraint("1");
        $this->column->addConstraint("2");
        $this->column->addConstraint("3");
        $this->assertEquals(array($constraint1, $constraint2, $constraint3), $this->column->getConstraints());
    }

    /**
     * @test
     */
    public function testGetConstraintsMysql()
    {
        $constraint1 = new \Yana\Db\Ddl\Constraint();
        $constraint1->setConstraint("1")->setName('name1')->setDBMS('mysql');
        $constraint2 = new \Yana\Db\Ddl\Constraint();
        $constraint2->setConstraint("2")->setName('name2')->setDBMS('mysql');
        $constraint3 = new \Yana\Db\Ddl\Constraint();
        $constraint3->setConstraint("3");
        $this->column->addConstraint("1", "Name1", "Mysql");
        $this->column->addConstraint("2", "Name2", "mysql");
        $this->column->addConstraint("3");
        $this->assertEquals(array($constraint1, $constraint2), $this->column->getConstraints('MySql'));
        $this->assertEquals(array(), $this->column->getConstraints('no-such-dbms'));
    }

    /**
     * @test
     */
    public function testGetConstraintsNull()
    {
        $this->assertSame(array(), $this->column->getConstraints());
    }

    /**
     * @test
     */
    public function testGetConstraintNull()
    {
        $this->assertNull($this->column->getConstraint("no-such-constraint", "mysql"));
    }

    /**
     * @test
     */
    public function testDropConstraints()
    {
        $this->column->addConstraint("1");
        $this->column->addConstraint("2");
        $this->column->addConstraint("3");
        $this->assertSame(array(), $this->column->dropConstraints()->getConstraints());
    }

    /**
     * A type must be chosen to set the XDDL tag name.
     *
     * @test
     * @expectedException \Yana\Db\Ddl\NoTagNameException
     */
    public function testSerializeToXDDLNoTagNameException()
    {
        $this->column->serializeToXDDL();
    }

    /**
     * @test
     */
    public function testSerializeToXDDL()
    {
        $parent = new \SimpleXMLElement('<table name="test"/>');
        $simpleXmlElement = $this->column->setType('string')->serializeToXDDL($parent);
        $this->assertTrue($simpleXmlElement instanceof \SimpleXMLElement);
        $this->assertContains('<table name="test"><declaration><string name="column"/></declaration></table>', $parent->asXML());
    }

    /**
     * @test
     */
    public function testSerializeToXDDLWithDeclaration()
    {
        $parent = new \SimpleXMLElement('<table name="test"><declaration/></table>');
        $simpleXmlElement = $this->column->setType('string')->serializeToXDDL($parent);
        $this->assertTrue($simpleXmlElement instanceof \SimpleXMLElement);
        $this->assertContains('<table name="test"><declaration><string name="column"/></declaration></table>', $parent->asXML());
    }

    /**
     * @test
     */
    public function testSerializeToXDDLEnumeration()
    {
        $enumerationItems = array(
            'optgroup' => array('a' => 'b', 'c' => 'd'),
            'e' => 'f'
        );
        $simpleXmlElement = $this->column->setType('enum')->setEnumerationItems($enumerationItems)->serializeToXDDL();
        $optgroup = '<optgroup label="optgroup"><option value="a">b</option><option value="c">d</option></optgroup>';
        $expected = '<enum name="column">' . $optgroup . '<option value="e">f</option></enum>';
        $this->assertContains($expected, $simpleXmlElement->asXML());
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $parent = new \SimpleXMLElement('<table name="test"/>');
        $this->column->setType('float')->setDefault(1.0)->setRange(0.0, 10.0);
        $this->assertEquals($this->column, \Yana\Db\Ddl\Column::unserializeFromXDDL($this->column->serializeToXDDL($parent)));
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDLLength()
    {
        $parent = new \SimpleXMLElement('<table name="test"/>');
        $this->column->setType('integer')->setLength(10);
        $this->assertEquals($this->column, \Yana\Db\Ddl\Column::unserializeFromXDDL($this->column->serializeToXDDL($parent)));
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDLFile()
    {
        $parent = new \SimpleXMLElement('<table name="test"/>');
        $this->column->setType('file')->setSize(10000);
        $this->assertEquals($this->column, \Yana\Db\Ddl\Column::unserializeFromXDDL($this->column->serializeToXDDL($parent)));
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDLEnumeration()
    {
        $parent = new \SimpleXMLElement('<table name="test"/>');
        $enumerationItems = array(
            'optgroup' => array('a' => 'b', 'c' => 'd'),
            'e' => 'f'
        );
        $this->column->setType('enum')->setEnumerationItems($enumerationItems);
        $this->assertEquals($this->column, \Yana\Db\Ddl\Column::unserializeFromXDDL($this->column->serializeToXDDL($parent)));
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDLDefaults()
    {
        $string = '<integer name="' . $this->column->getName() . '"><default>1</default><default>2</default></integer>';
        $this->column->setType('integer')->setDefault(2);
        $this->assertEquals($this->column, \Yana\Db\Ddl\Column::unserializeFromXDDL(new \SimpleXMLElement($string)));
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDLTableWithEnumeration()
    {
        $enumerationItems = array(
            'optgroup' => array('a' => 'b', 'c' => 'c'),
            'e' => 'f'
        );
        $table = new \Yana\Db\Ddl\Table('test');
        $column = $table->addColumn('column', 'enum');
        $column->setType('enum')->setEnumerationItems($enumerationItems);
        $simpleXmlElement = $table->serializeToXDDL();
        $enumerationString = '<optgroup label="optgroup"><option value="a">b</option><option>c</option></optgroup><option value="e">f</option>';
        $expected = '<table name="test"><declaration><enum name="column">' . $enumerationString . '</enum></declaration></table>';
        $this->assertContains($expected, $simpleXmlElement->asXML());
        $this->assertEquals($column, \Yana\Db\Ddl\Table::unserializeFromXDDL($simpleXmlElement)->getColumn('column'));
    }

    /**
     * @test
     * @expectedException \Yana\Db\Ddl\NoNameException
     */
    public function testUnserializeFromXDDLNoNameException()
    {
        \Yana\Db\Ddl\Column::unserializeFromXDDL(new \SimpleXMLElement("<integer/>"));
    }

}
