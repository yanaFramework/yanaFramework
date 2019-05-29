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

namespace Yana\Forms;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class WorkerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * form facade
     *
     * @var \Yana\Forms\Facade
     */
    public $form = null;

    /**
     * database connection
     *
     * @var \Yana\Db\FileDb\Connection
     */
    public $db = null;

    /**
     * @var \Yana\Forms\Worker
     */
    protected $object = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $schema = \Yana\Files\XDDL::getDatabase('check');
        $this->db = new \Yana\Db\FileDb\NullConnection($schema);
        $this->form = new \Yana\Forms\Facade();
        $this->object = new \Yana\Forms\Worker($this->db, $this->form);
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
    public function testBeforeCreateEmpty()
    {
        $array = $this->object->beforeCreate();
        $this->assertSame(array(), $array);
    }

    /**
     * @test
     */
    public function testBeforeCreate()
    {
        $f1 = function() {};
        $f2 = function() {};
        $this->object->beforeCreate($f1);
        $array = $this->object->beforeCreate($f2);
        $this->assertSame(array($f1, $f2), $array);
    }

    /**
     * @test
     */
    public function testAfterCreateEmpty()
    {
        $this->assertSame(array(), $this->object->afterCreate());
    }

    /**
     * @test
     */
    public function testAfterCreate()
    {
        $f1 = function() {};
        $f2 = function() {};
        $this->object->afterCreate($f1);
        $array = $this->object->afterCreate($f2);
        $this->assertSame(array($f1, $f2), $array);
    }

    /**
     * @test
     */
    public function testBeforeUpdateEmpty()
    {
        $this->assertSame(array(), $this->object->beforeUpdate());
    }

    /**
     * @test
     */
    public function testBeforeUpdate()
    {
        $f1 = function() {};
        $f2 = function() {};
        $this->object->beforeUpdate($f1);
        $array = $this->object->beforeUpdate($f2);
        $this->assertSame(array($f1, $f2), $array);
        $this->assertSame(array($f1, $f2), $this->object->beforeUpdate());
    }

    /**
     * @test
     */
    public function testAfterUpdateEmpty()
    {
        $this->assertSame(array(), $this->object->afterUpdate());
    }

    /**
     * @test
     */
    public function testAfterUpdate()
    {
        $f1 = function() {};
        $f2 = function() {};
        $this->object->afterUpdate($f1);
        $array = $this->object->afterUpdate($f2);
        $this->assertSame(array($f1, $f2), $array);
        $this->assertSame(array($f1, $f2), $this->object->afterUpdate());
    }

    /**
     * @test
     */
    public function testBeforeDeleteEmpty()
    {
        $this->assertSame(array(), $this->object->beforeDelete());
    }

    /**
     * @test
     */
    public function testBeforeDelete()
    {
        $f1 = function() {};
        $f2 = function() {};
        $this->object->beforeDelete($f1);
        $array = $this->object->beforeDelete($f2);
        $this->assertSame(array($f1, $f2), $array);
        $this->assertSame(array($f1, $f2), $this->object->beforeDelete());
    }

    /**
     * @test
     */
    public function testAfterDeleteEmpty()
    {
        $this->assertSame(array(), $this->object->afterDelete());
    }

    /**
     * @test
     */
    public function testAfterDelete()
    {
        $f1 = function() {};
        $f2 = function() {};
        $this->object->afterDelete($f1);
        $array = $this->object->afterDelete($f2);
        $this->assertSame(array($f1, $f2), $array);
        $this->assertSame(array($f1, $f2), $this->object->afterDelete());
    }

    /**
     * @test
     */
    public function testCreate()
    {
        $this->form->getBaseForm()->setTable('ft');
        $values = array('ftId' => 123, 'ftValue' => 234);
        $this->form->getSetup()->getContext(\Yana\Forms\Setups\ContextNameEnumeration::INSERT)->setValues($values);
        $this->assertTrue($this->object->create());
    }

    /**
     * @test
     */
    public function testCreateWithBefore()
    {
        $this->form->getBaseForm()->setTable('ft');
        $values = array('ftId' => 123, 'ftValue' => 234);
        $this->form->getSetup()->getContext(\Yana\Forms\Setups\ContextNameEnumeration::INSERT)->setValues($values);
        $f1 = function() {static $a = 0; $a++; return $a;};
        $f2 = function() {static $b = 0; $b--; return $b;};
        $this->object->beforeCreate($f1);
        $this->object->beforeCreate($f2);
        $this->assertTrue($this->object->create());
        $this->assertSame(2, $f1());
        $this->assertSame(-2, $f2());
    }

    /**
     * @test
     */
    public function testCreateWithAfter()
    {
        $this->form->getBaseForm()->setTable('ft');
        $values = array('ftId' => 123, 'ftValue' => 234);
        $this->form->getSetup()->getContext(\Yana\Forms\Setups\ContextNameEnumeration::INSERT)->setValues($values);
        $f1 = function() {static $a = 0; $a++; return $a;};
        $f2 = function() {static $b = 0; $b--; return $b;};
        $this->object->afterCreate($f1);
        $this->object->afterCreate($f2);
        $this->assertTrue($this->object->create());
        $this->assertSame(2, $f1());
        $this->assertSame(-2, $f2());
    }

    /**
     * @test
     */
    public function testCreateWithBeforeAndAfter()
    {
        $this->form->getBaseForm()->setTable('ft');
        $values = array('ftId' => 123, 'ftValue' => 234);
        $this->form->getSetup()->getContext(\Yana\Forms\Setups\ContextNameEnumeration::INSERT)->setValues($values);
        $f1 = function() {static $a = 0; $a++; return $a;};
        $f2 = function() {static $b = 0; $b--; return $b;};
        $this->object->afterCreate($f1);
        $this->object->beforeCreate($f2);
        $this->assertTrue($this->object->create());
        $this->assertSame(2, $f1());
        $this->assertSame(-2, $f2());
    }

    /**
     * @test
     */
    public function testCreateFalse()
    {
        $this->db->getSchema()->setReadonly(true);
        $this->form->getBaseForm()->setTable('ft');
        $values = array('ftId' => 123, 'ftValue' => 234);
        $this->form->getSetup()->getContext(\Yana\Forms\Setups\ContextNameEnumeration::INSERT)->setValues($values);
        $this->assertFalse($this->object->create());
    }

    /**
     * @test
     */
    public function testCreateFalse2()
    {
        $this->form->getBaseForm()->setTable('ft');
        $values = array('ftId' => 123, 'ftValue' => 234, 'Test' => 'nope');
        $this->form->getSetup()->getContext(\Yana\Forms\Setups\ContextNameEnumeration::INSERT)->setValues($values);
        $this->assertFalse($this->object->create());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\MissingInputException
     */
    public function testCreateMissingInputException()
    {
        $this->object->create();
    }

    /**
     * @test
     */
    public function testAutocomplete()
    {
        $this->assertSame(array(), $this->object->autocomplete('tvalue'));
    }

    /**
     * @test
     * @todo implement this
     */
    public function testExport()
    {
        $this->assertSame("", $this->object->export());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\MissingInputException
     */
    public function testUpdateMissingInputException()
    {
        $this->object->update();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Forms\MissingInputException
     */
    public function testDeleteMissingInputException()
    {
        $this->object->delete(array());
    }

}
