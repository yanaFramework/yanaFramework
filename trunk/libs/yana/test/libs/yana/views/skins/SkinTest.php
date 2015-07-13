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

namespace Yana\Views\Skins;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Test class for Skin
 *
 * @package  test
 */
class SkinTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Views\Skins\Skin
     */
    protected $_object;

    /**
     * @var  \Yana\Files\Dir
     */
    protected $_defaultDir;

    /**
     * @var  \Yana\Files\Dir
     */
    protected $_testDir;

    /**
     * @var  string
     */
    protected $_baseDir;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->_defaultDir = new \Yana\Files\Dir(CWD . '/resources/skin/default');
        $this->_defaultDir->setFilter('*.skin.xml');
        $this->_testDir = new \Yana\Files\Dir(CWD . '/resources/skin/test');
        $this->_testDir->setFilter('*.skin.xml');
        $this->_baseDir = CWD . '/resources/skin';
        \Yana\Views\Skins\Skin::setBaseDirectory($this->_baseDir);
        $this->_object = new \Yana\Views\Skins\Skin('test');
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
        $fooData = $this->_object->getTemplateData('foo');

        $file =  $this->_testDir->getPath() . 'test.txt';
        $this->assertEquals(array('test' => $file), $fooData->getStyles(), "read stylesheet failed");

        // add stylesheet
        $fooData->setStyles(array('foo.css'));
        $this->assertEquals(array('foo.css'), $fooData->getStyles(), "add stylesheet failed");
    }

    /**
     * script
     *
     * @test
     */
    public function testScript()
    {
        $fooData = $this->_object->getTemplateData('foo');

        $file = str_replace($this->_baseDir . '/', '', $this->_defaultDir->getPath() . 'default.txt');
        $this->assertEquals(array($file, $file), $fooData->getScripts(), "read script failed");
    }

    /**
     * language
     *
     * @test
     */
    public function testLanguage()
    {
        $fooData = $this->_object->getTemplateData('foo');

        $this->assertEquals(array('default'), $fooData->getLanguages(), "read language failed");
    }

}

?>