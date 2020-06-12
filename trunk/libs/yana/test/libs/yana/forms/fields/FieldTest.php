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
class FieldTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Forms\Fields\IsField
     */
    protected $object;

    /**
     * @var \Yana\Forms\Fields\FieldCollectionWrapper
     */
    protected $form;

    /**
     * @var \Yana\Db\Ddl\Column
     */
    protected $column;

    /**
     * @var \Yana\Db\Ddl\Field
     */
    protected $field;

    /**
     * @var \Yana\Forms\Setups\IsContext
     */
    protected $context;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->context = new \Yana\Forms\Setups\Context('Test Context');
        $this->form = new \Yana\Forms\Fields\FieldCollectionWrapper(new \Yana\Forms\Facade(), $this->context);
        $this->column = new \Yana\Db\Ddl\Column('TestColumn');
        $this->field = new \Yana\Db\Ddl\Field('TestField');
        $this->object = new \Yana\Forms\Fields\Field($this->form, $this->column, $this->field);
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
    public function testGetContext()
    {
        $this->assertSame($this->context, $this->object->getContext());
    }

    /**
     * @test
     */
    public function testGetTitle()
    {
        $this->assertSame($this->field->getName(), $this->object->getTitle());
    }

    /**
     * @test
     */
    public function testGetFilter()
    {
        $this->assertSame('', $this->object->getFilter());
    }

    /**
     * @test
     */
    public function testHasFilter()
    {
        $this->assertFalse($this->object->hasFilter());
        $this->assertTrue($this->object->setFilter(__FUNCTION__)->hasFilter());
        $this->assertFalse($this->object->setFilter("")->hasFilter());
    }

    /**
     * @test
     */
    public function testSetFilter()
    {
        $this->assertSame(__FUNCTION__, $this->object->setFilter(__FUNCTION__)->getFilter());
    }

    /**
     * @test
     */
    public function testIsFilterable()
    {
        $this->assertFalse($this->object->isFilterable());
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::BOOL);
        $this->assertTrue($this->object->isFilterable());
    }

    /**
     * @test
     */
    public function testRefersToTable()
    {
        $this->assertTrue($this->object->refersToTable());
        $this->field = \Yana\Db\Ddl\Field::unserializeFromXDDL(new \SimpleXMLElement('<input name="TestField"><bool name="TestColumn"/></input>'));
        $this->object = new \Yana\Forms\Fields\Field($this->form, $this->column, $this->field);
        $this->assertFalse($this->object->refersToTable());
    }

    /**
     * @test
     */
    public function testIsSingleLine()
    {
        $this->assertFalse($this->object->isSingleLine());
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::STRING);
        $this->assertTrue($this->object->isSingleLine());
    }

    /**
     * @test
     */
    public function testIsMultiLine()
    {
        $this->assertFalse($this->object->isMultiLine());
        $this->column->setType(\Yana\Db\Ddl\ColumnTypeEnumeration::TEXT);
        $this->assertTrue($this->object->isMultiLine());
    }

    /**
     * @test
     */
    public function testGetFilterValue()
    {
        $this->assertSame('', $this->object->getFilterValue());
    }

    /**
     * @test
     */
    public function testGetCssClass()
    {
        $this->assertSame("gui_generator_col_testcolumn", $this->object->getCssClass());
        $this->field->setCssClass('Test');
        $this->assertSame("Test", $this->object->getCssClass());
    }

    /**
     * @test
     */
    public function testGetValue()
    {
        $this->assertNull($this->object->getValue());
        $this->context->setRows(array(array('TestField' => 'Test')));
        $this->assertSame('Test', $this->object->getValue());
    }

    /**
     * @test
     */
    public function testGetMinValue()
    {
        $this->assertNull($this->object->getMinValue());
        $this->context->setRows(array(array('TestField' => array('start' => -123))));
        $this->assertSame(-123, $this->object->getMinValue());
    }

    /**
     * @test
     */
    public function testGetMaxValue()
    {
        $this->assertNull($this->object->getMaxValue());
        $this->context->setRows(array(array('TestField' => array('end' => 123))));
        $this->assertSame(123, $this->object->getMaxValue());
    }

    /**
     * @test
     */
    public function testGetValueAsWhereClause()
    {
        $this->assertNull($this->object->getValueAsWhereClause());
        $this->form->getBaseForm()->setTable('Test');
        $this->context->setRows(array(array('TestField' => 'Test')));
        $this->assertSame(array(array('test', 'testcolumn'), 'LIKE', 'Test'), $this->object->getValueAsWhereClause());
    }

}
