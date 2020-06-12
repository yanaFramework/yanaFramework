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

namespace Yana\Util;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../include.php';

/**
 * @package  test
 */
class CsvTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Util\Csv
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Util\Csv();
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
    public function testGetColumnDelimiter()
    {
        $this->assertSame(";", $this->object->getColumnDelimiter());
    }

    /**
     * @test
     */
    public function testGetRowDelimiter()
    {
        $this->assertSame("\n", $this->object->getRowDelimiter());
    }

    /**
     * @test
     */
    public function testHasHeader()
    {
        $this->assertTrue($this->object->hasHeader());
    }

    /**
     * @test
     */
    public function testGetStringDelimiter()
    {
        $this->assertSame('"', $this->object->getStringDelimiter());
    }

    /**
     * @test
     */
    public function testSetColumnDelimiter()
    {
        $this->assertSame(__FUNCTION__, $this->object->setColumnDelimiter(__FUNCTION__)->getColumnDelimiter());
    }

    /**
     * @test
     */
    public function testSetRowDelimiter()
    {
        $this->assertSame(__FUNCTION__, $this->object->setRowDelimiter(__FUNCTION__)->getRowDelimiter());
    }

    /**
     * @test
     */
    public function testSetHeader()
    {
        $this->assertFalse($this->object->setHeader(false)->hasHeader());
        $this->assertTrue($this->object->setHeader(true)->hasHeader());
        $this->assertFalse($this->object->setHeader(false)->hasHeader());
    }

    /**
     * @test
     */
    public function testSetStringDelimiter()
    {
        $this->assertSame(__FUNCTION__, $this->object->setStringDelimiter(__FUNCTION__)->getStringDelimiter());
    }

    /**
     * @test
     */
    public function testConvertCellToCSV()
    {
        $this->object->setStringDelimiter("'");
        $this->assertSame("'Title'\n'Content'\n", $this->object->convertCellToCSV("Content", "Title"));
        $this->assertSame("'a''b'\n'c''d'\n", $this->object->convertCellToCSV("c'd", "a'b"));
        $this->assertSame("'Abc'\n", $this->object->convertCellToCSV("Abc"));
    }

    /**
     * @test
     */
    public function testConvertRowToCSV()
    {
        $this->object->setStringDelimiter("'");
        $this->assertSame("'Title'\n'Content'\n", $this->object->convertRowToCSV(["Content"], ["Title"]));
        $this->assertSame("'1';'2'\n'a';'b'\n", $this->object->convertRowToCSV(["a", "b"], ["1", "2"]));
        $this->assertSame("'a''b'\n'c''d'\n", $this->object->convertRowToCSV(["c'd"], ["a'b"]));
        $this->assertSame("'a'\n'b'\n", $this->object->convertRowToCSV(["a" => "b"]));
    }

    /**
     * @test
     */
    public function testConvertTableToCSV()
    {
        $this->object->setStringDelimiter("'");
        $this->assertSame("'Title'\n'Content'\n", $this->object->convertTableToCSV([["Content"]], ["Title"]));
        $this->assertSame("'1';'2'\n'a';'b'\n", $this->object->convertTableToCSV([["a", "b"]], ["1", "2"]));
        $this->assertSame("'a''b'\n'c''d'\n", $this->object->convertTableToCSV([["c'd"]], ["a'b"]));
        $this->assertSame("'a'\n'b'\n", $this->object->convertTableToCSV([["a" => "b"]]));
        $this->assertSame("'0';'1'\n'a';'b'\n", $this->object->convertTableToCSV([["a", "b"]]));
    }

}
