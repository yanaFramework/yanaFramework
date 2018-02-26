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
class AbstractFacadeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Forms\Fields\Facade
     */
    protected $object;

    /**
     * @var \Yana\Forms\ContextSensitiveWrapper
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
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->form = new \Yana\Forms\ContextSensitiveWrapper(new \Yana\Forms\Facade(), new \Yana\Forms\Setups\Context('Test Context'));
        $this->column = new \Yana\Db\Ddl\Column('TestColumn');
        $this->field = new \Yana\Db\Ddl\Field('TestField');
        $this->object = new \Yana\Forms\Fields\Facade($this->form, $this->column, $this->field);
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
    public function test__construct()
    {
        $this->object = new \Yana\Forms\Fields\Facade($this->form, $this->column); // auto-generates a dummy field based on given column
        $this->assertSame($this->column->getName(), $this->object->getField()->getName());
    }

    /**
     * @test
     */
    public function test__call()
    {
        /* @var $columnWrapper \Yana\Db\Ddl\Column */
        $columnWrapper = $this->object;
        $this->assertSame(null, $columnWrapper->getAutoValue());
        /* @var $fieldWrapper \Yana\Db\Ddl\Field */
        $fieldWrapper = $this->object;
        $this->assertSame('testfield', $fieldWrapper->getName()); // field name is lower-case
        /* @var $formWrapper \Yana\Forms\ContextSensitiveWrapper */
        $formWrapper = $this->object;
        $this->assertSame(false, $formWrapper->hasRows());
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
    public function testGetField()
    {
        $this->assertSame($this->field, $this->object->getField());
    }

    /**
     * @test
     */
    public function testGetForm()
    {
        $this->assertSame($this->form, $this->object->getForm());
    }

    /**
     * @test
     */
    public function test__toString()
    {
        $this->assertSame("", $this->object->__toString());
    }

}
