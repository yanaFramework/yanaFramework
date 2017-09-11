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

namespace Yana\Views\Helpers\Formatters;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';

/**
 * @package  test
 */
class TextFormatterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\Formatters\TextFormatter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Views\Helpers\Formatters\TextFormatter();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * Generated from @assert ('a[br]b') == 'a<br />b'.
     *
     * @test
     */
    public function test__invoke()
    {
        $this->assertEquals(
            'a<br />b'
            , $this->object->__invoke('a[br]b')
        );
    }

    /**
     * Generated from @assert ('a[wbr]b') == 'a&shy;b'.
     *
     * @test
     */
    public function test__invoke2()
    {
        $this->assertEquals(
            'a&shy;b'
            , $this->object->__invoke('a[wbr]b')
        );
    }

    /**
     * Generated from @assert ('a[i]bc') == 'a<span class="embtag_tag_i">bc</span>'.
     *
     * @test
     */
    public function test__invoke3()
    {
        $this->assertEquals(
            'a<span class="embtag_tag_i">bc</span>'
            , $this->object->__invoke('a[i]bc')
        );
    }

    /**
     * Generated from @assert ('a[i]b[/i]c') == 'a<span class="embtag_tag_i">b</span>c'.
     *
     * @test
     */
    public function test__invoke4()
    {
        $this->assertEquals(
            'a<span class="embtag_tag_i">b</span>c'
            , $this->object->__invoke('a[i]b[/i]c')
        );
    }

    /**
     * Generated from @assert ('a[u]b[/u]c') == 'a<span class="embtag_tag_u">b</span>c'.
     *
     * @test
     */
    public function test__invoke5()
    {
        $this->assertEquals(
            'a<span class="embtag_tag_u">b</span>c'
            , $this->object->__invoke('a[u]b[/u]c')
        );
    }

    /**
     * Generated from @assert ('a[emp]b[/emp]c') == 'a<span class="embtag_tag_emp">b</span>c'.
     *
     * @test
     */
    public function test__invoke6()
    {
        $this->assertEquals(
            'a<span class="embtag_tag_emp">b</span>c'
            , $this->object->__invoke('a[emp]b[/emp]c')
        );
    }

    /**
     * Generated from @assert ('a[h]b[/h]c') == 'a<span class="embtag_tag_h">b</span>c'.
     *
     * @test
     */
    public function test__invoke7()
    {
        $this->assertEquals(
            'a<span class="embtag_tag_h">b</span>c'
            , $this->object->__invoke('a[h]b[/h]c')
        );
    }

    /**
     * Generated from @assert ('a[c]b[/c]c') == 'a<span class="embtag_tag_c">b</span>c'.
     *
     * @test
     */
    public function test__invoke8()
    {
        $this->assertEquals(
            'a<span class="embtag_tag_c">b</span>c'
            , $this->object->__invoke('a[c]b[/c]c')
        );
    }

    /**
     * Generated from @assert ('a[small]b[/small]c') == 'a<span class="embtag_tag_small">b</span>c'.
     *
     * @test
     */
    public function test__invoke9()
    {
        $this->assertEquals(
            'a<span class="embtag_tag_small">b</span>c'
            , $this->object->__invoke('a[small]b[/small]c')
        );
    }

    /**
     * Generated from @assert ('a[big]b[/big]c') == 'a<span class="embtag_tag_big">b</span>c'.
     *
     * @test
     */
    public function test__invoke10()
    {
        $this->assertEquals(
            'a<span class="embtag_tag_big">b</span>c'
            , $this->object->__invoke('a[big]b[/big]c')
        );
    }

    /**
     * Generated from @assert ('a[code]b[/code]c') == 'a<span class="embtag_tag_code">b</span>c'.
     *
     * @test
     */
    public function test__invoke11()
    {
        $this->assertEquals(
            'a<span class="embtag_tag_code">b</span>c'
            , $this->object->__invoke('a[code]b[/code]c')
        );
    }

    /**
     * Generated from @assert ('a[hide]b[/hide]c') == 'a<span class="embtag_tag_hide">b</span>c'.
     *
     * @test
     */
    public function test__invoke12()
    {
        $this->assertEquals(
            'a<span class="embtag_tag_hide">b</span>c'
            , $this->object->__invoke('a[hide]b[/hide]c')
        );
    }

    /**
     * Generated from @assert ('a[mark=a]b[/mark]c') == 'a<span class="embtag_tag_mark" style="background-color:a">b</span>c'.
     *
     * @test
     */
    public function test__invoke13()
    {
        $this->assertEquals(
            'a<span class="embtag_tag_mark" style="background-color:a">b</span>c'
            , $this->object->__invoke('a[mark=a]b[/mark]c')
        );
    }

    /**
     * Generated from @assert ('a[color=a]b[/color]c') == 'a<span class="embtag_tag_color" style="color:a">b</span>c'.
     *
     * @test
     */
    public function test__invoke14()
    {
        $this->assertEquals(
            'a<span class="embtag_tag_color" style="color:a">b</span>c'
            , $this->object->__invoke('a[color=a]b[/color]c')
        );
    }

    /**
     * Generated from @assert ('a[mail=mailto:a[wbr]@b.c]b[/color]c') == 'a<a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;a@b.c" target="_blank">b</a>c'.
     *
     * @test
     */
    public function test__invoke15()
    {
        $this->assertEquals(
            'a<a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;a@b.c" target="_blank">b</a>c'
            , $this->object->__invoke('a[mail=mailto:a[wbr]@b.c]b[/mail]c')
        );
    }

    /**
     * Generated from @assert ('a[url]b[/url]c') == 'a<a href="http://b" target="_blank">b</a>c'.
     *
     * @test
     */
    public function test__invoke16()
    {
        $this->assertEquals(
            'a<a href="http://b" target="_blank">b</a>c'
            , $this->object->__invoke('a[url]b[/url]c')
        );
    }

    /**
     * @test
     */
    public function test__invokePHP()
    {
        $php = $this->object->__invoke('a[php]print "Hallo Welt!";[/php]c');
        $this->assertRegExp('/^a<span class="embtag_tag_code"><code><.*?&lt;\?php.*?>print&nbsp;<.*?>"Hallo&nbsp;Welt!"<.*?>;.*?<\/span>c$/si', $php);
    }

    /**
     * @test
     */
    public function test__invokeImg()
    {
        $img = $this->object->__invoke('a[img]b.png[/img]c');
        $expected = 'a<img alt="" border="0" src="b.png" style="max-width: 320px; max-height: 240px" onload="javascript:if' .
            '(this.width>320) { this.width=320; }; if(this.height>240) { this.height=240; };' .
                '"/>c';
        $this->assertSame($expected, $img);
    }

}
