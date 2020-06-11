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
declare(strict_types=1);

namespace Yana\Db\Ddl;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ColumnTypeEnumerationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testGetSupportedTypes()
    {
        $getSupported = \Yana\Db\Ddl\ColumnTypeEnumeration::getSupportedTypes();
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
    public function testIsSingleLine()
    {
        $this->assertFalse(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine('no-such-type'));
        $this->assertFalse(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::ARR));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::BOOL));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::DATE));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::ENUM));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::FILE));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT)); 
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::INET));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::INT));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::MAIL));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::RANGE));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::STRING));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::TELEPHONE));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::TIME));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::TIMESTAMP));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::URL));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isSingleLine(\Yana\Db\Ddl\ColumnTypeEnumeration::REFERENCE));
    }

    /**
     * @test
     */
    public function testIsMultiLine()
    {
        $this->assertFalse(\Yana\Db\Ddl\ColumnTypeEnumeration::isMultiLine('no-such-type'));
        $this->assertFalse(\Yana\Db\Ddl\ColumnTypeEnumeration::isMultiLine(\Yana\Db\Ddl\ColumnTypeEnumeration::STRING));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isMultiLine(\Yana\Db\Ddl\ColumnTypeEnumeration::TEXT));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isMultiLine(\Yana\Db\Ddl\ColumnTypeEnumeration::HTML));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isMultiLine(\Yana\Db\Ddl\ColumnTypeEnumeration::IMAGE));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isMultiLine(\Yana\Db\Ddl\ColumnTypeEnumeration::SET));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isMultiLine(\Yana\Db\Ddl\ColumnTypeEnumeration::LST));
    }

    /**
     * @test
     */
    public function testIsFilterable()
    {
        $this->assertFalse(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable('no-such-type'));
        $this->assertFalse(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable(\Yana\Db\Ddl\ColumnTypeEnumeration::ARR));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable(\Yana\Db\Ddl\ColumnTypeEnumeration::BOOL));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable(\Yana\Db\Ddl\ColumnTypeEnumeration::COLOR));
        $this->assertFalse(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable(\Yana\Db\Ddl\ColumnTypeEnumeration::ENUM));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable(\Yana\Db\Ddl\ColumnTypeEnumeration::FLOAT));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable(\Yana\Db\Ddl\ColumnTypeEnumeration::INET));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable(\Yana\Db\Ddl\ColumnTypeEnumeration::INT));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable(\Yana\Db\Ddl\ColumnTypeEnumeration::MAIL));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable(\Yana\Db\Ddl\ColumnTypeEnumeration::RANGE));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable(\Yana\Db\Ddl\ColumnTypeEnumeration::STRING));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable(\Yana\Db\Ddl\ColumnTypeEnumeration::TELEPHONE));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable(\Yana\Db\Ddl\ColumnTypeEnumeration::TEXT));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable(\Yana\Db\Ddl\ColumnTypeEnumeration::HTML));
        $this->assertTrue(\Yana\Db\Ddl\ColumnTypeEnumeration::isFilterable(\Yana\Db\Ddl\ColumnTypeEnumeration::URL));
    }

}
