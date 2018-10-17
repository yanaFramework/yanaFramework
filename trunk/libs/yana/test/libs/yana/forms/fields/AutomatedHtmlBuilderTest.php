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
class AutomatedHtmlBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Forms\Fields\AutomatedHtmlBuilder
     */
    protected $object;

    /**
     * @var \Yana\Forms\Fields\AutomatedHtmlBuilder
     */
    private $_contextName = \Yana\Forms\Setups\ContextNameEnumeration::EDITABLE;

    /**
     * @var \Yana\Forms\Facade
     */
    private $_facade;

    /**
     * @var \Yana\Forms\Setups\Context
     */
    private $_context;

    /**
     * @var \Yana\Forms\ContextSensitiveWrapper
     */
    private $_wrapper;

    /**
     * @var \Yana\Forms\Setup
     */
    private $_setup;

    /**
     * @var \Yana\Db\Ddl\Column
     */
    private $_column;

    /**
     * @var \Yana\Forms\Fields\Facade
     */
    private $_field;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_facade = new \Yana\Forms\Facade();
        $this->_context = new \Yana\Forms\Setups\Context($this->_contextName);
        $this->_context->setAction('action');
        $this->_wrapper = new \Yana\Forms\ContextSensitiveWrapper($this->_facade, $this->_context);
        $this->_setup = new \Yana\Forms\Setup();
        $this->_setup->setContext($this->_context);
        $this->_facade->setSetup($this->_setup);
        $this->_column = new \Yana\Db\Ddl\Column('column');
        $this->_field = new \Yana\Forms\Fields\Facade($this->_wrapper, $this->_column);
        $this->object = new \Yana\Forms\Fields\AutomatedHtmlBuilder();
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
        $facade = new \Yana\Forms\Facade();
        $context = new \Yana\Forms\Setups\Context('context');
        $wrapper = new \Yana\Forms\ContextSensitiveWrapper($facade, $context);
        $column = new \Yana\Db\Ddl\Column('column');
        $field = new \Yana\Forms\Fields\Facade($wrapper, $column);
        $this->assertEmpty($this->object->__invoke($field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableString()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('string');

        $expected = '<input id="form-update-column" name="form[update][column]" class="" type="text" value="" title=""/>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableUrl()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('url');

        $expected = '<input id="form-update-column" name="form[update][column]" class="" type="text" value="" title=""/>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableArray()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('array');

        $this->assertRegExp('/<div id="form-update-column" class="gui_generator_array">.*<\/div>/', $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableList()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('list');

        $this->assertRegExp('/<div id="form-update-column" class="gui_generator_array">.*<\/div>/', $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableBool()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('bool');

        $expected = '/<input id="form-update-column" class="gui_generator_check" type="checkbox" name="form\[update\]\[column\]" value="1".*\/>/';
        $this->assertRegExp($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableColor()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('color');

        $expected = '/<input id="form-update-column" name="form\[update\]\[column\]" class="" type="color" value="".*\/>/';
        $this->assertRegExp($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableEnum()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('enum')->setEnumerationItem('test', 'value');

        $expected = '/<select class="gui_generator_set" id="form-update-column" name="form\[update\]\[column\]">.*' .
            '<option value="test">value<\/option><\/select>/';
        $this->assertRegExp($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableFile()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('file');

        $expected = '/<div class="gui_generator_file_download">.*<\/div>/';
        $this->assertRegExp($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableImage()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('image');

        $expected = '/<div class="gui_generator_image">.*<\/div>/';
        $this->assertRegExp($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableFloat()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('float');

        $expected = '<input id="form-update-column" name="form[update][column]" class="" type="text" value="" maxlength="1" size="1" title="column: ."/>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableHtml()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('html');

        $expected = '<textarea id="form-update-column" title="" class="editable" cols="20" rows="3" name="form[update][column]"></textarea>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatablePassword()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('password');

        $expected = '<input id="form-update-column" name="form[update][column]" class="" type="password" value="" title=""/>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableRange()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('range');

        $expected = '<input id="form-update-column" name="form[update][column]" class="" type="range" value="0" min="0" max="0" step="1 title="" ' .
            'onchange="document.getElementById(\'form-update-columnoutput\').innerHTML=this.value"/>' .
            '<output for="form-update-column" id="form-update-columnoutput">0</output>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

}
