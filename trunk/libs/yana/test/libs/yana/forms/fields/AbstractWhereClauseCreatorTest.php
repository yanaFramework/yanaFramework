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
class AbstractWhereClauseCreatorTest extends \PHPUnit_Framework_TestCase
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
    public function testGetColumn()
    {
        $this->assertSame($this->column, $this->object->getColumn());
    }

    /**
     * @test
     */
    public function testGetTableName()
    {
        $this->assertSame('table', $this->object->getTableName());
    }

    /**
     * @test
     */
    public function testGetValue()
    {
        $this->assertNull($this->object->getValue());
    }

    /**
     * @test
     */
    public function testSetValue()
    {
        $this->assertSame("Test", $this->object->setValue("Test")->getValue());
    }

    /**
     * @test
     */
    public function testGetMinValue()
    {
        $this->assertEquals(array('MONTH' => 1, 'DAY' => 1, 'YEAR' => 1970), $this->object->getMinValue());
    }

    /**
     * @test
     */
    public function testGetMaxValue()
    {
        $this->assertEquals(array('MONTH' => 31, 'DAY' => 12, 'YEAR' => 2040), $this->object->getMaxValue());
    }

    /**
     * @test
     */
    public function testSetMinValue()
    {
        $this->assertEquals(2, $this->object->setMinValue(2)->getMinValue());
    }

    /**
     * @test
     */
    public function testSetMaxValue()
    {
        $this->assertEquals(10, $this->object->setMaxValue(10)->getMaxValue());
    }

}
