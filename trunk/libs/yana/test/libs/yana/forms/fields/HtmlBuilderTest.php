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
class HtmlBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Forms\Fields\HtmlBuilder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Forms\Fields\HtmlBuilder();
        $this->object
                ->setAttr('test="test"')
                ->setName('Name')
                ->setId('Id')
                ->setTitle('Title')
                ->setCssClass('Class');
        \Yana\Views\Helpers\Formatters\UrlFormatter::setBaseUrl('URL');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        \Yana\Views\Helpers\Formatters\UrlFormatter::setBaseUrl('');
    }

    /**
     * @test
     */
    public function testBuildSelect()
    {
        $expected = '<select class="Class" id="Id" name="Name" test="test"><option value="a">test</option></select>';
        $this->assertSame($expected, $this->object->buildSelect(array('a' => 'test'), ''));
    }

    /**
     * @test
     */
    public function testBuildSelectEmpty()
    {
        $this->assertSame('', $this->object->buildSelect(array(), ''));
    }

    /**
     * @test
     */
    public function testBuildSelectSelected()
    {
        $expected = '<select class="Class" id="Id" name="Name" test="test"><option value="a" selected="selected">test</option></select>';
        $this->assertSame($expected, $this->object->buildSelect(array('a' => 'test'), 'a'));
    }

    /**
     * @test
     */
    public function testBuildSelectWithDefault()
    {
        $expected = '<select class="Class" id="Id" name="Name" test="test"><option value="">Default</option><option value="a">test</option></select>';
        $this->assertSame($expected, $this->object->buildSelect(array('a' => 'test'), '', 'Default'));
    }

    /**
     * @test
     */
    public function testBuildSelectMore()
    {
        $expected = '<select class="Class" id="Id" name="Name" test="test"><option value="">Default</option><option value="a">1</option>' .
            '<option value="b" selected="selected">2</option></select>';
        $this->assertSame($expected, $this->object->buildSelect(array('a' => '1', 'b' => '2'), 'b', 'Default'));
    }

    /**
     * @test
     */
    public function testBuildSelectMultiple()
    {
        $expected = '<select class="Class" id="Id" name="Name[]" multiple="multiple" test="test"><option value="a">1</option></select>';
        $this->assertSame($expected, $this->object->buildSelectMultiple(array('a' => '1'), array('')));
    }

    /**
     * @test
     */
    public function testBuildSelectMultipleEmpty()
    {
        $this->assertSame('', $this->object->buildSelectMultiple(array(), array('')));
    }

    /**
     * @test
     */
    public function testBuildSelectMultipleSelected()
    {
        $expected = '<select class="Class" id="Id" name="Name[]" multiple="multiple" test="test"><option value="a" selected="selected">test</option></select>';
        $this->assertSame($expected, $this->object->buildSelectMultiple(array('a' => 'test'), array('a')));
    }

    /**
     * @test
     */
    public function testBuildSelectMultipleMore()
    {
        $expected = '<select class="Class" id="Id" name="Name[]" multiple="multiple" test="test">' .
            '<option value="a">1</option><option value="b" selected="selected">2</option><option value="c" selected="selected">3</option></select>';
        $this->assertSame($expected, $this->object->buildSelectMultiple(array('a' => '1', 'b' => '2', 'c' => '3'), array('b', 'c', 'd')));
    }

    /**
     * @test
     */
    public function testBuildSelectMultipleOptgroup()
    {
        $expected = '<select class="Class" id="Id" name="Name[]" multiple="multiple" test="test">' .
            '<optgroup label="optgroup"><option value="a">1</option></optgroup></select>';
        $this->assertSame($expected, $this->object->buildSelectMultiple(array('optgroup' => array('a' => '1')), array('')));
    }

    /**
     * @test
     */
    public function testBuildDateSelector()
    {
        $expected = '/<select class="Class" id="Id_day" name="Name\[day\]" test="test">' .
            '(?:<option value="\d+"(?: selected="selected")?>\d+<\/option>){31}<\/select>' .
            '<select class="Class" id="Id_month" name="Name\[month\]" test="test">' .
            '(?:<option value="\d+"(?: selected="selected")?>\d+<\/option>){12}<\/select>' .
            '<select class="Class" id="Id_year" name="Name\[year\]" test="test">' .
            '(<option value="\d+"(?: selected="selected")?>\d+<\/option>)+<\/select>' .
            '<script type="text\/javascript">yanaAddCalendar\("Id_year", "Id_year_year", \d+, \d+, \d{4}\);<\/script>' .
            '<script type="text\/javascript" src=\'default\/scripts\/calendar\/calendar(?:-[a-z]{2})?.js\'><\/script>/';
        $this->assertRegExp($expected, $this->object->buildDateSelector());
    }

    /**
     * @test
     */
    public function testBuildDateSelectorWithDate()
    {
        $expected = '/<option value="1" selected="selected">01<\/option>.*?<option value="2" selected="selected">02<\/option>.*?' .
            '<option value="1990" selected="selected">1990<\/option>/';
        $selector = $this->object->buildDateSelector(array('day' => 1, 'month' => 2, 'year' => 1990));
        $this->assertRegExp($expected, $selector);
    }

    /**
     * @test
     */
    public function testBuildTimeSelector()
    {
        $expected = '/<select class="Class" id="Id_hour" name="Name\[hour\]" test="test">' .
            '(?:<option value="\d+"(?: selected="selected")?>\d+<\/option>){24}<\/select>' .
            ':<select class="Class" id="Id_minute" name="Name\[minute\]" test="test">' .
            '(?:<option value="\d+"(?: selected="selected")?>\d+<\/option>){60}<\/select>/';
        $selector = $this->object->buildTimeSelector();
        $this->assertRegExp($expected, $selector);
    }

    /**
     * @test
     */
    public function testBuildTimeSelectorWithTime()
    {
        $expected = '/<option value="1" selected="selected">01<\/option>.*?<option value="2" selected="selected">02<\/option>/';
        $selector = $this->object->buildTimeSelector(array('hour' => 1, 'minute' => 2));
        $this->assertRegExp($expected, $selector);
    }

    /**
     * @test
     */
    public function testBuildRadioEmpty()
    {
        $this->assertSame('', $this->object->buildRadio(array(), ''));
    }

    /**
     * @test
     */
    public function testBuildRadio()
    {
        $expected = '<label class="Class"><input id="Id" test="test" type="radio" name="Name" value="a"/>test</label>';
        $this->assertSame($expected, $this->object->buildRadio(array('a' => 'test'), ''));
    }

    /**
     * @test
     */
    public function testBuildRadioSelected()
    {
        $expected = '<label class="Class"><input id="Id" test="test" type="radio" name="Name" value="a" checked="checked"/>test</label>';
        $this->assertSame($expected, $this->object->buildRadio(array('a' => 'test'), 'a'));
    }

    /**
     * @test
     */
    public function testBuildRadioWithDefault()
    {
        $expected = '<label class="Class"><input type="radio" test="test" name="Name" value=""/>default</label> ' .
            '<label class="Class"><input id="Id" test="test" type="radio" name="Name" value="a"/>test</label>';
        $this->assertSame($expected, $this->object->buildRadio(array('a' => 'test'), '', 'default'));
    }

    /**
     * @test
     */
    public function testBuildRadioMultiple()
    {
        $expected = '<label class="Class"><input type="radio" test="test" name="Name" value=""/>default</label> ' .
            '<label class="Class"><input id="Id" test="test" type="radio" name="Name" value="a"/>test</label> ' .
            '<label class="Class"><input test="test" type="radio" name="Name" value="b" checked="checked"/>test2</label>';
        $this->assertSame($expected, $this->object->buildRadio(array('a' => 'test', 'b' => 'test2'), 'b', 'default'));
    }

    /**
     * @test
     */
    public function testBuildCheckboxes()
    {
        $expected = '<label class="Class" title="Title"><input id="Id" test="test" type="checkbox" name="Name[]" class="Class" value="0"/>test</label>' . "\n";
        $this->assertSame($expected, $this->object->buildCheckboxes(array('test'), array()));
    }

    /**
     * @test
     */
    public function testBuildCheckboxesWithSelected()
    {
        $expected = '<label class="Class" title="Title">' .
            '<input id="Id" checked="checked" test="test" type="checkbox" name="Name[]" class="Class" value="a"/>test</label>' . "\n";
        $this->assertSame($expected, $this->object->buildCheckboxes(array('a' => 'test'), array('a')));
    }

    /**
     * @test
     */
    public function testBuildCheckboxesWithMultiple()
    {
        $expected = '<label class="Class" title="Title">' .
            '<input id="Id" test="test" type="checkbox" name="Name[]" class="Class" value="a"/>1</label>' . "\n" .
            '<label class="Class" title="Title">' .
            '<input checked="checked" test="test" type="checkbox" name="Name[]" class="Class" value="b"/>2</label>' . "\n";
        $this->assertSame($expected, $this->object->buildCheckboxes(array('a' => '1', 'b' => '2'), array('b')));
    }

    /**
     * @test
     */
    public function testBuildCheckboxesFieldset()
    {
        $expected = '<fieldset><legend>fieldset</legend><label class="Class" title="Title">' .
            '<input id="Id" test="test" type="checkbox" name="Name[]" class="Class" value="0"/>test</label>' . "\n" .
            '</fieldset>';
        $this->assertSame($expected, $this->object->buildCheckboxes(array('fieldset' => array('test')), array()));
    }

    /**
     * @test
     */
    public function testBuildCheckboxesEmpty()
    {
        $this->assertSame('', $this->object->buildCheckboxes(array(), array()));
    }

    /**
     * @test
     */
    public function testBuildBoolCheckbox()
    {
        $expected = '<input type="hidden" name="Name" value="0"/>' .
            '<input test="test" id="Id" class="Class" type="checkbox" name="Name" value="1" checked="checked" title="Title"/>';
        $this->assertSame($expected, $this->object->buildBoolCheckbox(true));
    }

    /**
     * @test
     */
    public function testBuildBoolCheckboxFalse()
    {
        $expected = '<input type="hidden" name="Name" value="0"/>' .
            '<input test="test" id="Id" class="Class" type="checkbox" name="Name" value="1" title="Title"/>';
        $this->assertSame($expected, $this->object->buildBoolCheckbox(false));
    }

    /**
     * @test
     */
    public function testBuildList()
    {
        $expected = '/' . preg_quote('<div id="Id" class="Class"><ol><li>' .
            '<input test="test" size="5" type="text" name="Name[names][]" value="a"/>&nbsp;=' .
            '&nbsp;<input size="10" type="text" name="Name[values][]" value="test"/>' .
            '<a class="buttonize" href="javascript://yanaRemoveItem(this)" onclick="yanaRemoveItem(this)" title="', '/') .
            '[^"]+' .
            preg_quote('"><span class="icon_delete">&nbsp;</span></a>' .
            '<a class="buttonize" href="javascript://yanaAddItem(this)" onclick="yanaAddItem(this)" title="', '/') .
            '[^"]+' .
            preg_quote('"><span class="icon_new">&nbsp;</span></a></li></ol></div>', '/') . '/';
        $this->assertRegExp($expected, $this->object->buildList(array('a' => 'test')));
    }

    /**
     * @test
     */
    public function testBuildListNumeric()
    {
        $expected = '/' . preg_quote('<div id="Id" class="Class"><ol><li>' .
            '<input test="test" size="21" type="text" name="Name[]" value="test"/>' .
            '<a class="buttonize" href="javascript://yanaRemoveItem(this)" onclick="yanaRemoveItem(this)" title="', '/') .
            '[^"]+' .
            preg_quote('"><span class="icon_delete">&nbsp;</span></a>' .
            '<a class="buttonize" href="javascript://yanaAddItem(this)" onclick="yanaAddItem(this)" title="', '/') .
            '[^"]+' .
            preg_quote('"><span class="icon_new">&nbsp;</span></a></li></ol></div>', '/') . '/';
        $this->assertRegExp($expected, $this->object->buildList(array('test'), true));
    }

    /**
     * @test
     */
    public function testBuildListEmpty()
    {
        $expected = '/' . preg_quote('<div id="Id" class="Class"><ol><li>' .
            '<input test="test" size="5" type="text" name="Name[names][]" value=""/>&nbsp;=' .
            '&nbsp;<input size="10" type="text" name="Name[values][]" value=""/>' .
            '<a class="buttonize" href="javascript://yanaRemoveItem(this)" onclick="yanaRemoveItem(this)" title="', '/') .
            '[^"]+' .
            preg_quote('"><span class="icon_delete">&nbsp;</span></a>' .
            '<a class="buttonize" href="javascript://yanaAddItem(this)" onclick="yanaAddItem(this)" title="', '/') .
            '[^"]+' .
            preg_quote('"><span class="icon_new">&nbsp;</span></a></li></ol></div>', '/') . '/';
        $this->assertRegExp($expected, $this->object->buildList());
    }

    /**
     * @test
     */
    public function testBuildTextfield()
    {
        $expected = '<input test="test" id="Id" name="Name" class="Class" type="text" value="test" title="Title"/>';
        $this->assertSame($expected, $this->object->buildTextfield('test'));
    }

    /**
     * @test
     */
    public function testBuildFilefield()
    {
        $expected = '<input test="test" size="1" type="file" id="Id" name="Name"/>';
        $this->assertSame($expected, $this->object->buildFilefield(false));
    }

    /**
     * @test
     */
    public function testBuildFilefieldWithDelete()
    {
        $expected = '/' . preg_quote('<input test="test" size="1" type="file" id="Id" name="Name"/>' .
            '<label class="gui_generator_file_delete"><input title="', '/') .
            '[^"]+' . preg_quote('" type="checkbox" id="Id_delete" name="Name" value="1"/>', '/') .
            '[^"]+' . preg_quote('</label>', '/') . '/';
        $this->assertRegExp($expected, $this->object->buildFilefield(true));
    }

    /**
     * @test
     */
    public function testBuildFilefieldWithMimeType()
    {
        $expected = '<input test="test" accept="text/plain" size="1" type="file" id="Id" name="Name"/>';
        $this->assertSame($expected, $this->object->buildFilefield(false, 'text/plain'));
    }

    /**
     * @test
     */
    public function testBuildFilefieldWithMaxLength()
    {
        $expected = '<input test="test" maxlength="1" size="1" type="file" id="Id" name="Name"/>';
        $this->assertSame($expected, $this->object->setMaxLength(1)->buildFilefield(false));
    }

    /**
     * @test
     */
    public function testBuildTextarea()
    {
        $expected = '<textarea test="test" id="Id" title="Title" class="Class" cols="20" rows="3" name="Name">test</textarea>';
        $this->assertSame($expected, $this->object->buildTextarea('test'));
        $this->assertSame($expected, $this->object->setMaxLength(2000)->buildTextarea('test'));
    }

    /**
     * @test
     */
    public function testBuildTextareaLong()
    {
        $expected = '<textarea test="test" id="Id" title="Title" class="Class" cols="30" rows="3" name="Name">test</textarea>';
        $this->assertSame($expected, $this->object->setMaxLength(2001)->buildTextarea('test'));
    }

    /**
     * @test
     */
    public function testBuildFileDownloadEmpty()
    {
        $this->assertSame('<span class="icon_blank">&nbsp;</span>', $this->object->buildFileDownload('', ''));
    }

    /**
     * @test
     */
    public function testBuildFileDownload()
    {
        $expected = '/' . preg_quote('<a class="buttonize" title="', '/') .
            '[^"]+' .
            preg_quote('" href="URL?&amp;action=action&amp;target=0"><span class="icon_download">&nbsp;</span></a>', '/') . '/';
        $this->assertRegExp($expected, $this->object->buildFileDownload(__FILE__, 'action'));
    }

    /**
     * @test
     */
    public function testBuildImageDownloadEmpty()
    {
        $this->assertSame('<span class="icon_blank">&nbsp;</span>', $this->object->buildImageDownload('', ''));
    }

    /**
     * @test
     */
    public function testBuildImageDownload()
    {
        $expected = '/<a href="URL\?&amp;action=action&amp;target=\d&amp;fullsize=true">' .
            '<img border="0" alt="" src="URL\?&amp;action=action&amp;target=\d"\/><\/a>/';
        $this->assertRegExp($expected, $this->object->buildImageDownload(__FILE__, 'action'));
    }

    /**
     * @test
     */
    public function testBuildColorpicker()
    {
        $expected = '<input test="test" id="Id" name="Name" class="Class" type="color" value="#AABBCC" title="Title"/>';
        $this->assertSame($expected, $this->object->buildColorpicker('#AABBCC'));
    }

    /**
     * @test
     */
    public function testBuildSpan()
    {
        $expected = '<span test="test" id="Id" title="Title" class="Class">Content</span>';
        $this->assertSame($expected, $this->object->buildSpan('Content'));
    }

    /**
     * @test
     */
    public function testBuildDiv()
    {
        $expected = '<div test="test" id="Id" title="Title" class="Class">Content</div>';
        $this->assertSame($expected, $this->object->buildDiv('Content'));
    }

    /**
     * @test
     */
    public function testBuildExternalLink()
    {
        $expected = '/' . preg_quote('<a test="test" id="Id" title="Title" class="Class" onclick="return confirm(\'', '/') .
            '[^"]+' . preg_quote('\')" href="Content">Content</a>', '/') . '/';
        $this->assertRegExp($expected, $this->object->buildExternalLink('Content'));
    }

    /**
     * @test
     */
    public function testBuildExternalLinkLong()
    {
        $content = 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabc';
        $expected = '/' . preg_quote('<a test="test" id="Id" title="Title" class="Class" onclick="return confirm(\'', '/') . '[^"]+' .
            preg_quote('\')" href="' . $content . '">abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwx ...</a>', '/') . '/';
        $this->assertRegExp($expected, $this->object->buildExternalLink($content));
    }

    /**
     * @test
     */
    public function testBuildRange()
    {
        $expected = '<input test="test" id="Id" name="Name" class="Class" type="range" value="2" min="1" max="3" step="0.5 title="Title" ' .
            'onchange="document.getElementById(\'Idoutput\').innerHTML=this.value"/><output for="Id" id="Idoutput">2</output>';
        $this->assertSame($expected, $this->object->buildRange(2.0, 1.0, 3.0, 0.5));
    }

}
