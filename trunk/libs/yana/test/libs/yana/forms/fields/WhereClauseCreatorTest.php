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

namespace Yana\Forms\Fields;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class WhereClauseCreatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    protected $tableName = "Table";

    /**
     * @var \Yana\Db\Ddl\Column
     */
    protected $column;

    /**
     * @var \Yana\Forms\Fields\WhereClauseCreator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->column = new \Yana\Db\Ddl\Column("Column");
        $this->object = new \Yana\Forms\Fields\WhereClauseCreator($this->column, $this->tableName);
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
    public function test__invoke()
    {
        $this->assertNull($this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildStringClause()
    {
        $this->object->setValue('test');
        $this->assertEquals(array(array('table', 'column'), 'LIKE', 'test'), $this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildBoolClause()
    {
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::BOOL);
        $this->assertNull($this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildBoolClauseTrue()
    {
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::BOOL);
        $this->object->setValue('true');
        $this->assertEquals(array(array('table', 'column'), '=', true), $this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildBoolClauseFalse()
    {
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::BOOL);
        $this->object->setValue('false');
        $this->assertEquals(array(array('table', 'column'), '=', false), $this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildListClause()
    {
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::ENUM);
        $this->assertNull($this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildListClauseNonArray()
    {
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::ENUM);
        $this->object->setValue('false');
        $this->assertNull($this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildListClauseEmpty()
    {
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::ENUM)
                ->setEnumerationItems(array('a' => 'A', 'b' => 'B'));
        $this->object->setValue(array('c', 'd'));
        $this->assertNull($this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildListClauseIntersect()
    {
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::ENUM)
                ->setEnumerationItems(array('a' => 'A-label', 'b' => 'B-label', 'c' => 'C-label'));
        $this->object->setValue(array('b', 'c', 'd'));
        $this->assertEquals(array(array('table', 'column'), 'IN', array('b', 'c')), $this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildTimeRangeClause()
    {
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::DATE);
        $this->assertNull($this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildTimeRangeClauseActive()
    {
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::DATE);
        $this->object->setValue(array('ACTIVE' => 'true'))
                ->setMinValue(array('MONTH' => 20, 'DAY' => 1, 'YEAR' => 2000))
                ->setMaxValue(array('MONTH' => 21, 'DAY' => 2, 'YEAR' => 2001));
        $left = array('table', 'column');
        $min = mktime(0, 0, 0, 20, 1, 2000);
        $max = mktime(23, 59, 59, 21, 2, 2001);
        $this->assertEquals(array(array($left, '>=', $min), 'AND', array($left, '<=', $max)), $this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildNumberRangeClause()
    {
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::INT);
        $this->assertNull($this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildNumberRangeClauseMin()
    {
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::INT);
        $this->object->setMinValue(-1);
        $this->assertEquals(array(array('table', 'column'), '>=', -1), $this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildNumberRangeClauseMax()
    {
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::INT);
        $this->object->setMaxValue(10);
        $this->assertEquals(array(array('table', 'column'), '<=', 10), $this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildNumberRangeClauseEqual()
    {
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::INT);
        $this->object->setMinValue(2)->setMaxValue(2);
        $this->assertEquals(array(array('table', 'column'), '=', 2), $this->object->__invoke());
    }

    /**
     * @test
     */
    public function testBuildNumberRangeClausBoth()
    {
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::INT);
        $this->object->setMinValue(1)->setMaxValue(2);
        $left = array('table', 'column');
        $this->assertEquals(array(array($left, '>=', 1), 'AND', array($left, '<=', 2)), $this->object->__invoke());
    }

}
