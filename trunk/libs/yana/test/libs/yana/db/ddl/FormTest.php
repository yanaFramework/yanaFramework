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
declare(strict_types=1);

namespace Yana\Db\Ddl;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class FormTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Form
     */
    protected $form;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->form = new \Yana\Db\Ddl\Form('Form');
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
    public function testGetParent()
    {
        $this->assertNull($this->form->getParent());
        $form = new \Yana\Db\Ddl\Form("Test1", $database = new \Yana\Db\Ddl\Database("Database"));
        $this->assertSame($database, $form->getParent());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function test__constructInvalidArgumentException()
    {
        new \Yana\Db\Ddl\Form("Test", new \Yana\Db\Ddl\Field("Field"));
    }

    /**
     * @test
     */
    public function testGetDatabase()
    {
        $this->assertNull($this->form->getDatabase());
        $form1 = new \Yana\Db\Ddl\Form("Test1", $database = new \Yana\Db\Ddl\Database("Database"));
        $this->assertSame($database, $form1->getDatabase());
        $form2 = new \Yana\Db\Ddl\Form("Test2", $form1);
        $this->assertSame($database, $form2->getDatabase());
    }

    /**
     * @test
     */
    public function testGetSchemaName()
    {
        $this->assertNull($this->form->getSchemaName());
        $form1 = new \Yana\Db\Ddl\Form("Test1", $database = new \Yana\Db\Ddl\Database("Database"));
        $this->assertSame($database->getName(), $form1->getSchemaName());
    }

    /**
     * @test
     */
    public function testGetTable()
    {
        $this->assertNull($this->form->getTable());
    }

    /**
     * @test
     */
    public function testSetTable()
    {
        $this->assertSame('Table', $this->form->setTable('Table')->getTable());
        $this->assertNull($this->form->setTable('')->getTable());
    }

    /**
     * @test
     */
    public function testGetTitle()
    {
        $this->assertNull($this->form->getTitle());
    }

    /**
     * @test
     */
    public function testSetTitle()
    {
        $this->assertSame('Abc 1 Áß!', $this->form->setTitle('Abc 1 Áß!')->getTitle());
        $this->assertNull($this->form->setTitle('')->getTitle());
    }

    /**
     * @test
     */
    public function testGetDescription()
    {
        $this->assertNull($this->form->getDescription());
    }

    /**
     * @test
     */
    public function testSetDescription()
    {
        $this->assertSame('Abc 1 Áß!', $this->form->setDescription('Abc 1 Áß!')->getDescription());
        $this->assertNull($this->form->setDescription('')->getDescription());
    }

    /**
     * @test
     */
    public function testGetTemplate()
    {
        $this->assertNull($this->form->getTemplate());
    }

    /**
     * @test
     */
    public function testSetTemplate()
    {
        $this->assertEquals('template', $this->form->setTemplate('template')->getTemplate());
        $this->assertNull($this->form->setTemplate('')->getTemplate());
    }

    /**
     * @test
     */
    public function testGetKey()
    {
        $this->assertNull($this->form->getKey());
    }

    /**
     * @test
     */
    public function testSetKey()
    {
        $this->assertSame('Key', $this->form->setKey('Key')->getKey());
        $this->assertNull($this->form->setKey('')->getKey());
    }

    /**
     * @test
     */
    public function testGetGrants()
    {
        $this->assertSame(array(), $this->form->getGrants());
    }

    /**
     * @test
     */
    public function testDropGrants()
    {
        $this->assertNull($this->form->dropGrants());
        $this->assertSame(array(), $this->form->getGrants());
        $grant = $this->form->addGrant();
        $this->assertSame(array($grant), $this->form->getGrants());
        $this->assertNull($this->form->dropGrants());
        $this->assertSame(array(), $this->form->getGrants());
    }

    /**
     * @test
     */
    public function testAddGrantObject()
    {
        $grant = new \Yana\Db\Ddl\Grant();
        $grant->setUser("UserGroup")->setRole("UserRole")->setLevel(34);
        $this->assertSame(array($grant), $this->form->addGrantObject($grant)->getGrants());
    }

    /**
     * @test
     */
    public function testAddGrant()
    {
        $this->assertSame(array(), $this->form->getGrants());
        $grant = $this->form->addGrant("User", "Role", 12);
        $this->assertSame(array($grant), $this->form->getGrants());
        $this->assertSame("User", $grant->getUser());
        $this->assertSame("Role", $grant->getRole());
        $this->assertSame(12, $grant->getLevel());
    }

    /**
     * @test
     */
    public function testIsForm()
    {
        $this->assertFalse($this->form->isForm('non-existing-form'));
    }

    /**
     * @test
     */
    public function testGetForm()
    {
        $form = $this->form->addForm('Test');
        $this->assertSame($form, $this->form->getForm('tesT'));
    }

    /**
     * getFormInvalidArgumentException
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     *
     * @test
     */
    public function testGetFormInvalidArgumentException()
    {
        $this->form->getForm('non-existing-form');
    }

    /**
     * @test
     */
    public function testAddForm()
    {
        $this->assertSame('test', $this->form->addForm('Test')->getName());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     */
    public function testAddFormAlreadyExistsException()
    {
        $this->form->addForm('Test');
        $this->form->addForm('tesT');
    }

    /**
     * @test
     */
    public function testGetForms()
    {
        $this->assertSame(array(), $this->form->getForms());
    }

    /**
     * @test
     */
    public function testGetFormNames()
    {
        $this->assertSame(array(), $this->form->getFormNames());
        $this->form->addForm("Test");
        $this->assertSame(array("test"), $this->form->getFormNames());
    }

    /**
     * @test
     */
    public function testDropForm()
    {
        $this->assertNull($this->form->dropForm('test'));
        $this->form->addForm("Test");
        $this->assertTrue($this->form->isForm('tesT'));
        $this->assertTrue($this->form->isForm('test'));
        $this->assertNull($this->form->dropForm('teSt'));
        $this->assertFalse($this->form->isForm('test'));
    }

    /**
     * @test
     */
    public function testIsField()
    {
        $this->assertFalse($this->form->isField('abcD'));
        $this->form->addField('Abcd');
        $this->assertTrue($this->form->isField('abcD'));
    }

    /**
     * @test
     */
    public function testGetField()
    {
        $field = $this->form->addField('Foo');
        $this->assertSame($field, $this->form->getField('foO'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetFieldNotFoundException()
    {
        $this->form->getField('no-such-field');
    }

    /**
     * @test
     */
    public function testGetFields()
    {
        $this->assertSame(array(), $this->form->getFields());
        $this->assertEquals(0, count($this->form->getFields()));
    }

    /**
     * @test
     */
    public function testGetFieldNames()
    {
        $this->assertSame(array(), $this->form->getFieldNames());
        $this->form->addField('Foo');
        $this->assertSame(array('foo'), $this->form->getFieldNames());
    }

    /**
     * @test
     */
    public function testAddField()
    {
        $field = $this->form->addField('abcd');
        $this->assertTrue($field instanceof \Yana\Db\Ddl\Field);
        $this->assertSame('abcd', $field->getName());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     */
    public function testAddFieldAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->form->addField('field');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->form->addField('field');
    }

    /**
     * add field to form
     *
     * @test
     */
    public function testAddFormField()
    {
        $field = $this->form->addField('abcd');
        $this->form->addField('qwerty');

        $fields = $this->form->getFields();
        $this->assertInternalType('array', $fields);

        $this->assertArrayHasKey('abcd', $fields);
        $this->assertArrayHasKey('qwerty', $fields);

        $this->assertTrue($field instanceof \Yana\Db\Ddl\Field);
        $this->assertSame($field, $this->form->getField('abcd'));
        $this->assertSame('abcd', $field->getName());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testDropFieldNotFoundException()
    {
        $this->form->dropField('no-such-field');
    }

    /**
     * @test
     */
    public function testDropField()
    {
        $this->form->addField('abcd');
        $this->assertTrue($this->form->isField('abcd'));
        $this->assertNull($this->form->dropField('abcd'));
        $this->assertFalse($this->form->isField('abcd'));
    }

    /**
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     *
     * @test
     */
    public function testDropFieldInvalidArgumentException()
    {
        $this->form->dropField('non-existing-field');
    }

    /**
     * @test
     */
    public function testGetEvents()
    {
        $this->assertSame(array(), $this->form->getEvents());
        $event = $this->form->addEvent('Test');
        $this->assertSame(array('test' => $event), $this->form->getEvents());
    }

    /**
     * @test
     */
    public function testGetEvent()
    {
        $this->assertNull($this->form->getEvent('test'));
        $this->form->addEvent('test')->setAction('bla');
        $this->assertTrue($this->form->getEvent('test') instanceof \Yana\Db\Ddl\Event);
        $this->assertEquals('bla', $this->form->getEvent('test')->getAction());
    }

    /**
     * @test
     */
    public function testAddEvent()
    {
        $event = $this->form->addEvent('Test');
        $event->setAction('bla');
        $this->assertTrue($event instanceof \Yana\Db\Ddl\Event);
        $this->assertSame($event, $this->form->getEvent('TesT'));
    }
    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     */
    public function testAddEventAlreadyExistsException()
    {
        $this->form->addEvent('Test');
        $this->form->addEvent('tesT');
    }

    /**
     * EventInvalidArgumentException
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     *
     * @test
     */
    public function testAddEventInvalidArgumentException()
    {
        $this->form->addEvent('');
    }

    /**
     * @test
     */
    public function testDropEvent()
    {
        $this->assertFalse($this->form->dropEvent('Test'), 'assert failed, event does not exist and can\'t be droped');
        $this->form->addEvent('Test');
        $this->assertTrue($this->form->dropEvent('tesT'));
        $this->assertFalse($this->form->dropEvent('tesT'));
    }

    /**
     * @test
     */
    public function testIsSelectable()
    {
        $this->assertTrue($this->form->isSelectable());
        $this->form->addGrant()->setSelect(false);
        $this->assertFalse($this->form->isSelectable());
    }

    /**
     * @test
     */
    public function testIsInsertable()
    {
        $this->assertTrue($this->form->isInsertable());
        $this->form->addGrant()->setInsert(false);
        $this->assertFalse($this->form->isInsertable());
    }

    /**
     * @test
     */
    public function testIsUpdatable()
    {
        $this->assertTrue($this->form->isUpdatable());
        $this->form->addGrant()->setUpdate(false);
        $this->assertFalse($this->form->isUpdatable());
    }

    /**
     * @test
     */
    public function testHasAllInput()
    {
        $this->assertFalse($this->form->hasAllInput(), 'Setting "allinput" must default to false.');
    }

    /**
     * @test
     */
    public function testSetAllInput()
    {
        $this->assertTrue($this->form->setAllInput(true)->hasAllInput(), 'Setting "allinput" should allow value true.');
        $this->assertFalse($this->form->setAllInput(false)->hasAllInput(), 'Setting "allinput" should be reversible.');
    }

    /**
     * @test
     */
    public function testIsDeletable()
    {
        $this->assertTrue($this->form->isDeletable());
        $this->form->addGrant()->setDelete(false);
        $this->assertFalse($this->form->isDeletable());
    }

    /**
     * @test
     */
    public function testIsGrantable()
    {
        $this->assertTrue($this->form->isGrantable());
        $this->form->addGrant()->setGrantOption(false);
        $this->assertFalse($this->form->isGrantable());
    }

    /**
     * @test
     */
    public function test__get()
    {
        $this->form->addField('MagicField');
        $this->assertTrue($this->form->magicField instanceof \Yana\Db\Ddl\Field);
        $this->form->addForm('MagicForm');
        $this->assertTrue($this->form->magicForm instanceof \Yana\Db\Ddl\Form);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\UndefinedPropertyException
     */
    public function test__getUndefinedPropertyException()
    {
        $this->form->oops;
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testUnserializeFromXDDLInvalidArgumentExceptionName()
    {
        \Yana\Db\Ddl\Form::unserializeFromXDDL(new \SimpleXmlElement('<form/>'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testUnserializeFromXDDLInvalidArgumentExceptionTable()
    {
        \Yana\Db\Ddl\Form::unserializeFromXDDL(new \SimpleXmlElement('<form name="myForm"/>'));
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $form = \Yana\Db\Ddl\Form::unserializeFromXDDL(new \SimpleXmlElement('<form name="myForm" table="myTable" template="myTemplate" allinput="yes"/>'));
        $this->assertSame("myform", $form->getName());
        $this->assertSame("myTable", $form->getTable()); // case-sensitive
        $this->assertSame("mytemplate", $form->getTemplate());
        $this->assertTrue($form->hasAllInput());
    }

}
