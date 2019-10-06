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
class FieldCollectionBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Forms\Fields\FieldCollectionBuilder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Forms\Fields\FieldCollectionBuilder();
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
    public function test__invokeEmpty()
    {
        $formFacade = new \Yana\Forms\Facade();
        $context = new \Yana\Forms\Setups\Context('update');
        $parentForm = new \Yana\Forms\Fields\FieldCollectionWrapper($formFacade, $context);
        $collection = $this->object->__invoke($parentForm);
        $this->assertSame(0, $collection->count());
    }

    /**
     * @test
     */
    public function test__invokeEmptyContext()
    {
        $database = new \Yana\Db\Ddl\Database('test');

        $table = $database->addTable('t');
        $table->addColumn('id', 'integer');
        $table->addColumn('value', 'string');

        $form = $database->addForm('form');
        $form->setTable('t');
        $form->setAllInput(true);

        $formFacade = new \Yana\Forms\Facade();
        $formFacade->setBaseForm($form);

        $context = new \Yana\Forms\Setups\Context('update');

        $parentForm = new \Yana\Forms\Fields\FieldCollectionWrapper($formFacade, $context);
        $collection = $this->object->__invoke($parentForm);
        $this->assertSame(0, $collection->count());
    }

    /**
     * @test
     */
    public function test__invoke()
    {
        $database = new \Yana\Db\Ddl\Database('test');

        $table = $database->addTable('t');
        $table->addColumn('id', 'integer');
        $table->addColumn('value', 'string');

        $form = $database->addForm('form');
        $form->setTable('t');
        $form->setAllInput(true);

        $formFacade = new \Yana\Forms\Facade();
        $formFacade->setBaseForm($form);

        $context = new \Yana\Forms\Setups\Context('update');
        $context->setColumnNames(array('id', 'value'));

        $parentForm = new \Yana\Forms\Fields\FieldCollectionWrapper($formFacade, $context);
        $collection = $this->object->__invoke($parentForm);
        $this->assertSame(2, $collection->count());
        $this->assertTrue($collection->offsetExists('id'));
        $this->assertTrue($collection->offsetExists('value'));
        $this->assertTrue($collection->offsetGet('id') instanceof \Yana\Forms\Fields\IsField);
        $this->assertTrue($collection->offsetGet('value') instanceof \Yana\Forms\Fields\IsField);
        $this->assertSame($context, $collection->offsetGet('id')->getContext());
    }

    /**
     * @test
     */
    public function test__invokeDuplicateColumns()
    {
        $database = new \Yana\Db\Ddl\Database('test');

        $table = $database->addTable('t');
        $table->addColumn('id', 'integer');
        $table->addColumn('value', 'string');

        $form = $database->addForm('form');
        $form->setTable('t');
        $form->setAllInput(true);

        $formFacade = new \Yana\Forms\Facade();
        $formFacade->setBaseForm($form);

        $context = new \Yana\Forms\Setups\Context('update');
        $context->setColumnNames(array('id', 'value', 'id'));

        $parentForm = new \Yana\Forms\Fields\FieldCollectionWrapper($formFacade, $context);
        $collection = $this->object->__invoke($parentForm);
        $this->assertSame(2, $collection->count());
        $this->assertTrue($collection->offsetExists('id'));
        $this->assertTrue($collection->offsetExists('value'));
    }

    /**
     * @test
     */
    public function test__invokeContextDoesNotHaveColumn()
    {
        $database = new \Yana\Db\Ddl\Database('test');

        $table = $database->addTable('t');
        $table->addColumn('id', 'integer');
        $table->addColumn('value', 'string');

        $form = $database->addForm('form');
        $form->setTable('t');
        $form->setAllInput(true);

        $formFacade = new \Yana\Forms\Facade();
        $formFacade->setBaseForm($form);

        $context = new \Yana\Forms\Setups\Context('update');
        $context->setColumnNames(array('value'));

        $parentForm = new \Yana\Forms\Fields\FieldCollectionWrapper($formFacade, $context);
        $collection = $this->object->__invoke($parentForm);
        $this->assertSame(1, $collection->count());
        $this->assertTrue($collection->offsetExists('value'));
    }

    /**
     * @test
     */
    public function test__invokeTableDoesNotHaveColumn()
    {
        $database = new \Yana\Db\Ddl\Database('test');

        $table = $database->addTable('t');
        $table->addColumn('id', 'integer');

        $form = $database->addForm('form');
        $form->setTable('t');
        $form->setAllInput(true);

        $formFacade = new \Yana\Forms\Facade();
        $formFacade->setBaseForm($form);

        $context = new \Yana\Forms\Setups\Context('update');
        $context->setColumnNames(array('id', 'value'));

        $parentForm = new \Yana\Forms\Fields\FieldCollectionWrapper($formFacade, $context);
        $collection = $this->object->__invoke($parentForm);
        $this->assertSame(1, $collection->count());
        $this->assertTrue($collection->offsetExists('id'));
    }

    /**
     * @test
     */
    public function test__invokeFormFields()
    {
        $database = new \Yana\Db\Ddl\Database('test');

        $table = $database->addTable('t');
        $table->addColumn('id', 'integer');
        $table->addColumn('value', 'string');

        $form = $database->addForm('form');
        $form->setTable('t');
        $form->addField('id');

        $formFacade = new \Yana\Forms\Facade();
        $formFacade->setBaseForm($form);

        $context = new \Yana\Forms\Setups\Context('update');
        $context->setColumnNames(array('id', 'value'));

        $parentForm = new \Yana\Forms\Fields\FieldCollectionWrapper($formFacade, $context);
        $collection = $this->object->__invoke($parentForm);
        $this->assertSame(1, $collection->count());
        $this->assertTrue($collection->offsetExists('id'));
    }

    /**
     * @test
     */
    public function test__invokeNoSuchTable()
    {
        $database = new \Yana\Db\Ddl\Database('test');

        $table = $database->addTable('t');
        $table->addColumn('id', 'integer');
        $table->addColumn('value', 'string');

        $form = $database->addForm('form');
        $form->setTable('no-such-table');
        $form->addField('id');

        $formFacade = new \Yana\Forms\Facade();
        $formFacade->setBaseForm($form);

        $context = new \Yana\Forms\Setups\Context('update');
        $context->setColumnNames(array('id', 'value'));

        $parentForm = new \Yana\Forms\Fields\FieldCollectionWrapper($formFacade, $context);
        $collection = $this->object->__invoke($parentForm);
        $this->assertSame(0, $collection->count());
    }

}
