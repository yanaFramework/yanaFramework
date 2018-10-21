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
    protected $object = null;

    /**
     * @var \Yana\Db\Ddl\Form
     */
    private $_form = null;

    /**
     * @var string
     */
    private $_contextName = \Yana\Forms\Setups\ContextNameEnumeration::EDITABLE;

    /**
     * @var \Yana\Forms\Facade
     */
    private $_facade = null;

    /**
     * @var \Yana\Forms\Setups\Context
     */
    private $_context = null;

    /**
     * @var \Yana\Forms\ContextSensitiveWrapper
     */
    private $_wrapper = null;

    /**
     * @var \Yana\Forms\Setup
     */
    private $_setup = null;

    /**
     * @var \Yana\Db\Ddl\Column
     */
    private $_column = null;

    /**
     * @var \Yana\Forms\Fields\Facade
     */
    private $_field = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $db = new \Yana\Db\Ddl\Database('db');
        $table = $db->addTable('table');
        $this->_form = $db->addForm('form');
        $this->_form->setTable($table->getName());
        $this->_facade = new \Yana\Forms\Facade();
        $this->_facade->setBaseForm($this->_form);
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

    /**
     * @test
     */
    public function testBuildByTypeUpdatableReference()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('reference');
        $this->_setup->setReferenceValues(array('column' => array('a' => '1', 'b' => '2')));

        $expected = '/<select class="gui_generator_reference" id="form-update-column" name="form\[update\]\[column\]">' .
            '<option value="">.*?<\/option><option value="a">1<\/option><option value="b">2<\/option><\/select>/';
        $this->assertRegExp($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableSet()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('set')->setEnumerationItems(array('a' => '1'));

        $expected = '<label class="gui_generator_set" title="">' .
            '<input id="form-update-column" type="checkbox" name="form[update][column][]" class="gui_generator_set" value="a"/>1</label>' . "\n";
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableText()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('text');

        $expected = '<textarea id="form-update-column" title="" class="" cols="20" rows="3" name="form[update][column]"></textarea>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableDate()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('date');

        $expected = '/<span id="form-update-column" title="" class="gui_generator_date"><select class="gui_generator_date".*<\/span>/';
        $this->assertRegExp($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeUpdatableTime()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $this->setUp();
        $this->_column->setType('time');

        $expected = '/<span id="form-update-column" title="" class="gui_generator_time"><select class="gui_generator_time".*<\/span>/';
        $this->assertRegExp($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableEmpty()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('string');

        $expected = '&ndash;';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableString()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('string');
        $this->_context->setValue('column', 'value');

        $expected = '<span id="" title="" class="form-read-column">value</span>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableArray()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('array');
        $this->_context->setValue('column', array('key' => 'value'));

        $expected = '<div id="" title="" class="gui_generator_array"><ul class="gui_array_list">' .
            '<li class="gui_array_list"><span class="gui_array_key">key:</span><span class="gui_array_value">value</span></li></ul></div>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableBool()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('bool');
        $this->_context->setValue('column', '1');

        $expected = '<span id="" title="" class="gui_generator_bool icon_true">&nbsp;</span>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableColor()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('color');
        $this->_context->setValue('column', '#f0f0f0');

        $expected = '<span style="background-color: #f0f0f0" id="" title="" class="gui_generator_color">#f0f0f0</span>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableFile()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('file');
        $this->_context->setValue('column', 1);

        $expected = '<span id="" title="" class="gui_generator_file_download"><span class="icon_blank">&nbsp;</span></span>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableText()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('text');
        $this->_context->setValue('column', '<p>myText</p>');

        $expected = '<div id="" title="" class="form-read-column">&lt;p&gt;myText&lt;/p&gt;</div>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableHtml()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('html');
        $this->_context->setValue('column', '<p>myText</p>');

        $expected = '<div id="" title="" class="form-read-column"><p>myText</p></div>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableHtmlLong()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('html');
        $this->_context->setValue('column', '<p>abcdefghijklmnopqrstuvwxyz</p>');

        $expected = '<div id="" title="" class="gui_generator_readonly_textarea"><p>abcdefghijklmnopqrstuvwxyz</p></div>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableImage()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('image');
        $this->_context->setValue('column', 0);

        $expected = '<div id="" title="" class="gui_generator_image"><span class="icon_blank">&nbsp;</span></div>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableListString()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('list')->setEnumerationItem('test', 'value');
        $this->_context->setValue('column', 'test');

        $expected = '<div id="" title="" class="gui_generator_array">test</div>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableList()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('list');
        $this->_context->setValue('column', array('a' => '1', 'b' => '2'));

        $expected = '<div id="" title="" class="gui_generator_array"><ul class="gui_array_list">' .
            '<li class="gui_array_list"><span class="gui_array_value">1</span></li>' .
            '<li class="gui_array_list"><span class="gui_array_value">2</span></li></ul></div>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatablePassword()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('password');
        $this->_context->setValue('column', 'this_should_never_be_shown');

        $expected = '&ndash;';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableReferenceWrongDataType()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('reference');
        $this->_context->setValue('column', array('test'));

        $expected = '<span id="" title="" class="form-read-column"></span>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableReference()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('reference');
        $this->_context->setValue('column', 'test');
        $this->_field->getColumn()->setReferenceSettings('table', 'column', 'label');
        $this->_field->getContext()->setRows(array(array('COLUMN' => '1', 'LABEL' => '2')));

        $expected = '<span id="" title="" class="form-read-column">2</span>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableDate()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('date');
        $this->_context->setValue('column', 1);
        \Yana\Views\Helpers\Formatters\DateFormatter::setFormat('d/m/Y', 'date.toLocaleString()');

        $expected = '<span id="" title="" class="form-read-column">' .
            '<script type="text/javascript" language="JavaScript">date=new Date(1000);document.write(date.toLocaleString());</script>' .
            '<span class="yana_noscript">01/01/1970</span></span>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableUrlWrongDataType()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('url');
        $this->_context->setValue('column', array('test'));

        $expected = '&ndash;';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeNonUpdatableUrl()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('url');
        $this->_context->setValue('column', 'url?a=1&b=2');

        $expected = '/<a id="" title=".*?" class="form-read-column" ' .
            'onclick="return confirm\(\'.*?\'\)" href="url\?a=1&amp;b=2">url\?a=1&b=2<\/a>/';
        $this->assertRegExp($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeStringWrongDataType()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('string');
        $this->_context->setValue('column', array('test'));

        $expected = '<span id="" title="" class="form-read-column">&ndash;</span>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeString()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('string');
        $this->_context->setValue('column', '<p>test</p>');

        $expected = '<span id="" title="" class="form-read-column">&lt;p&gt;test&lt;/p&gt;</span>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeStringLong()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('string');
        $this->_context->setValue('column', 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabc');

        $expected = '<span id="" title="" class="form-read-column">' .
            'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwx&nbsp;...</span>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeSearchfieldStringWrongDataType()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::SEARCH;
        $this->setUp();
        $this->_column->setType('string');
        $this->_context->setValue('column', array('test'));

        $expected = '<input id="form-search-column" name="form[search][column]" class="" type="text" value="" title=""/>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeSearchfieldString()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::SEARCH;
        $this->setUp();
        $this->_column->setType('string');
        $this->_context->setValue('column', '<p>test</p>');

        $expected = '<input id="form-search-column" name="form[search][column]" class="" type="text" value="&lt;p&gt;test&lt;/p&gt;" title=""/>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeSearchfieldBool()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::SEARCH;
        $this->setUp();
        $this->_column->setType('bool');
        $this->_context->setValue('column', 'true');

        $expected = '/<label class="gui_generator_bool"><input id="form-search-column" type="radio" name="form\[search\]\[column\]" value="\*"\/>.*?<\/label>' .
            ' <label class="gui_generator_bool"><input type="radio" name="form\[search\]\[column\]" value="true" checked="checked"\/>.*?<\/label>' .
            ' <label class="gui_generator_bool"><input type="radio" name="form\[search\]\[column\]" value="false"\/>.*?<\/label>/';
        $this->assertRegExp($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeSearchfieldEnum()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::SEARCH;
        $this->setUp();
        $this->_column->setType('enum');
        $this->_context->setValue('column', 'test');
        $this->_column->setEnumerationItem('test', 'value');

        $expected = '<label class="gui_generator_set" title="">' .
            '<input id="form-search-column" checked="checked" type="checkbox" name="form[search][column][]" class="gui_generator_set" value="test"/>' .
            'value</label>' . "\n";
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeSearchfieldDate()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::SEARCH;
        $this->setUp();
        $this->_column->setType('date');
        $this->_context->setValue('column', array('day' => '1', 'month' => '1', 'year' => '1970'));

        $expected = '/<span id="form-search-column" title="" class="gui_generator_date">.*<\/span>/';
        $this->assertRegExp($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testBuildByTypeSearchfieldInteger()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::SEARCH;
        $this->setUp();
        $this->_column->setType('integer');
        $this->_context->setValue('column', 123);

        $expected = '<input id="form-search-column_start" name="form[search][column][start]" class="" type="text" value="123" title=""/>' .
            '&nbsp;&le;&nbsp;<input id="form-search-column_end" name="form[search][column][end]" class="" type="text" value="123" title=""/>';
        $this->assertSame($expected, $this->object->__invoke($this->_field));
    }

    /**
     * @test
     */
    public function testCreateLink()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('string');
        $this->_context->setValue('column', 123);
        $this->_field->getField()->addEvent('test')->setAction('action')->setLabel('label')->setIcon(__FILE__);
        \Yana\Views\Helpers\Formatters\UrlFormatter::setBaseUrl('https://URL');

        $link = $this->object->__invoke($this->_field);
        $this->assertStringStartsWith('<span id="" title="" class="form-read-column">123</span>', $link);
        $this->assertRegExp('/<img src="[^"]+" alt="[^"]*"\/>/', $link);
        $expected = '/<a id="form---column" class="gui_generator_int_link" title=".*?" ' .
            'href=".*?\?&amp;action=test&amp;target\[\]=&amp;target%5Bcolumn%5D=123">/';
        $this->assertRegExp($expected, $link);
        \Yana\Views\Helpers\Formatters\UrlFormatter::setBaseUrl('');
    }

    /**
     * @test
     */
    public function testCreateJavascriptEvents()
    {
        $this->_contextName = \Yana\Forms\Setups\ContextNameEnumeration::READ;
        $this->setUp();
        $this->_column->setType('string');
        $this->_context->setValue('column', 'test');
        $this->_field->getField()->addEvent('ontest')->setAction('action')->setLanguage('javascript');

        $javascript = $this->object->__invoke($this->_field);
        $this->assertSame('<span ontest="action" id="" title="" class="form-read-column">test</span>', $javascript);
    }

}
