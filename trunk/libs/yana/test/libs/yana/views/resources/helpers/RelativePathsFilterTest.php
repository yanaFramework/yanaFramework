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

namespace Yana\Views\Resources\Helpers;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';

/**
 * @package  test
 */
class RelativePathsFilterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Dependencies\IsViewContainer
     */
    protected $container;

    /**
     * @var \Yana\Views\Resources\Helpers\RelativePathsFilter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!\class_exists('\Smarty') || !\class_exists('\Smarty_Internal_Template')) {
            $this->markTestSkipped();
        }
        $configurationFactory = new \Yana\ConfigurationFactory();
        $configuration = $configurationFactory->loadConfiguration(CWD . 'resources/system.config.xml');
        $this->container = new \Yana\Core\Dependencies\Container($configuration);
        $this->object = new \Yana\Views\Resources\Helpers\RelativePathsFilter($this->container);
        $this->object->setLeftDelimiter('{%')->setRightDelimiter('%}');
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
    public function test__invokeEmptyBaseDir()
    {
        $basedir = "";
        $source = "Some Text";
        $this->assertSame($source, $this->object->__invoke($source, $basedir));
    }

    /**
     * @test
     */
    public function test__invokeEmptyString()
    {
        $basedir = "./Base/Path";
        $source = "";
        $this->assertSame($source, $this->object->__invoke($source, $basedir));
    }

    /**
     * @test
     */
    public function test__invokeImport()
    {
        $basedir = "./Base/Path";
        $source = 'Text {%import file="Some/File.ext" %}';
        $this->assertSame('Text {%import file="Base/Path/Some/File.ext" %}', $this->object->__invoke($source, $basedir));
    }

    /**
     * @test
     */
    public function test__invokeImportLiteralBefore()
    {
        $basedir = "./Base/Path";
        $source = 'Text {%import literal file="Some/File.ext" %}';
        $this->assertSame('Text {%import file="Some/File.ext" %}', $this->object->__invoke($source, $basedir));
    }

    /**
     * @test
     */
    public function test__invokeImportLiteralAfter()
    {
        $basedir = "./Base/Path";
        $source = 'Text {%import file="Some/File.ext" literal %}';
        $this->assertSame('Text {%import file="Some/File.ext" %}', $this->object->__invoke($source, $basedir));
    }

    /**
     * @test
     */
    public function test__invokeImportPreParser()
    {
        $basedir = "./Base/Path";
        $source = 'Text {%import preparser="true" file="Some/File.ext" %} texT';
        $this->assertSame('Text  texT', $this->object->__invoke($source, $basedir));
    }

    /**
     * @test
     */
    public function test__invokeInsert()
    {
        $basedir = "./Base/Path";
        $source = 'Text {%insert file="Some/File.ext" %}';
        $this->assertSame('Text {%insert file="Base/Path/Some/File.ext" %}', $this->object->__invoke($source, $basedir));
    }

    /**
     * @test
     */
    public function test__invokeBackground()
    {
        $basedir = "./Base/Path";
        $source = 'Text background  =  "Some/File.ext" texT';
        $this->assertSame('Text background="Base/Path/Some/File.ext" texT', $this->object->__invoke($source, $basedir));
    }

}
