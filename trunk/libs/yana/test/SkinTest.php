<?php
/**
 * PHPUnit test-case: Skin
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

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';

/**
 * Test class for Skin
 *
 * @package  test
 */
class SkinTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Skin
     * @access protected
     */
    protected $object;
    /**
     * @var    Dir
     * @access protected
     */
    protected $defaultDir;
    /**
     * @var    Dir
     * @access protected
     */
    protected $testDir;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->defaultDir = new Dir(CWD . '/resources/skin/default');
        $this->defaultDir->setFilter('*.skin.xml');
        $this->testDir = new Dir(CWD . '/resources/skin/test');
        $this->testDir->setFilter('*.skin.xml');
        Skin::setBaseDirectory(new Dir(CWD . '/resources/skin/'));
        $this->object = Skin::getInstance('test');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    /**
     * stylesheet
     *
     * @test
     */
    public function testStyleSheet()
    {
        $temp = array();

        $file =  $this->testDir->getPath() . 'test.txt';
        $this->assertEquals(array('test' => $file), $this->object->getStyle('foo'), "read stylesheet failed");
        $this->object->setStyle('foo');

        // add stylesheet
        $test = $this->object->setStyle('foo', 'foo.css');
        $temp[] = 'foo.css';
        $this->assertEquals($temp, $this->object->getStyle('foo'), "add stylesheet failed");

        // replace stylesheet 'bar'
        $test = $this->object->setStyle('foo', 'bar.css', 'bar');
        $temp['bar'] = 'bar.css';
        $this->assertEquals($temp, $this->object->getStyle('foo'), "replace stylesheet 'bar' failed");

        // remove stylesheet 'bar'
        $test = $this->object->setStyle('foo', '', 'bar');
        unset($temp['bar']);
        $this->assertEquals($temp, $this->object->getStyle('foo'), "remove stylesheet 'bar' failed");

        // remove all stylesheets
        $test = $this->object->setStyle('foo');
        $temp = array();
        $this->assertEquals($temp, $this->object->getStyle('foo'), "remove all stylesheets");
    }

    /**
     * script
     *
     * @test
     */
    public function testScript()
    {
        $temp = array();

        $file =  $this->defaultDir->getPath() . 'default.txt';
        $this->assertEquals(array($file, $file), $this->object->getScript('foo'), "read script failed");
        $this->object->setScript('foo');

        // add script
        $test = $this->object->setScript('foo', 'foo.js');
        $temp[] = 'foo.js';
        $this->assertEquals($this->object->getScript('foo'), $temp, "add script failed");

        // replace script 'bar'
        $test = $this->object->setScript('foo', 'bar.js', 'bar');
        $temp['bar'] = 'bar.js';
        $this->assertEquals($this->object->getScript('foo'), $temp, "replace script 'bar' failed");

        // remove script 'bar'
        $test = $this->object->setScript('foo', '', 'bar');
        unset($temp['bar']);
        $this->assertEquals($this->object->getScript('foo'), $temp, "remove script 'bar' failed");

        // remove all scripts
        $test = $this->object->setScript('foo');
        $temp = array();
        $this->assertEquals($this->object->getScript('foo'), $temp, "remove all scripts");
    }

    /**
     * language
     *
     * @test
     */
    public function testLanguage()
    {
        $temp = array();

        $this->assertEquals($this->object->getLanguage('foo'), array('default'), "read language failed");
        $this->object->setLanguage('foo');

        // add language
        $test = $this->object->setLanguage('foo', 'foo.config');
        $temp[] = 'foo.config';
        $this->assertEquals($this->object->getLanguage('foo'), $temp, "add language");

        // replace language 'bar'
        $test = $this->object->setLanguage('foo', 'bar.config', 'bar');
        $temp['bar'] = 'bar.config';
        $this->assertEquals($this->object->getLanguage('foo'), $temp, "replace language 'bar' failed");

        // remove language 'bar'
        $test = $this->object->setLanguage('foo', '', 'bar');
        unset($temp['bar']);
        $this->assertEquals($this->object->getLanguage('foo'), $temp, "remove language 'bar' failed");

        // remove all language-files
        $test = $this->object->setLanguage('foo');
        $temp = array();
        $this->assertEquals($this->object->getLanguage('foo'), $temp, "remove all language-files");
    }
}
?>