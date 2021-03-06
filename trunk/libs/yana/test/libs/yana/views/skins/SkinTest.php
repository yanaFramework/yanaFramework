<?php
/**
 * PHPUnit test-case.
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

namespace Yana\Views\Skins;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package test
 * @ignore
 */
class MySkin extends \Yana\Views\Skins\Skin
{
    public function getMetaDataProvider(): \Yana\Core\MetaData\IsDataProvider
    {
        return parent::_getMetaDataProvider();
    }
}

/**
 * @package  test
 */
class SkinTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Views\Skins\MySkin
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
     */
    protected function setUp()
    {
        $this->_defaultDir = new \Yana\Files\Dir(CWD . '/resources/skin/default');
        $this->_defaultDir->setFilter('*.skin.xml');
        $this->_testDir = new \Yana\Files\Dir(CWD . '/resources/skin/test');
        $this->_testDir->setFilter('*.skin.xml');
        $this->_baseDir = CWD . '/resources/skin';
        \Yana\Views\Skins\Skin::setBaseDirectory($this->_baseDir);
        $this->_object = new \Yana\Views\Skins\MySkin('test');
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
    public function testGetStyles()
    {
        $fooData = $this->_object->getTemplateData('foo');

        $file =  $this->_testDir->getPath() . 'test.txt';
        $file2 = $this->_defaultDir->getPath() .  'default_1.txt';
        $this->assertEquals(array('test' => $file, $file2), $fooData->getStyles(), "read stylesheet failed");
    }

    /**
     * @test
     */
    public function testSetStyles()
    {
        $fooData = $this->_object->getTemplateData('foo');
        $fooData->setStyles(array('foo.css'));
        $this->assertEquals(array('foo.css'), $fooData->getStyles());
    }

    /**
     * @test
     */
    public function testScript()
    {
        $fooData = $this->_object->getTemplateData('foo');

        $file = $this->_defaultDir->getPath() . 'default.txt';
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

    /**
     * @test
     */
    public function testDirectory()
    {
        $this->assertStringEndsWith('/resources/skintest/', $this->_object->getDirectory());
    }

    /**
     * @test
     */
    public function testSkinDirectory()
    {
        $this->assertStringEndsWith('/resources/skintest/', \Yana\Views\Skins\Skin::getSkinDirectory($this->_object->getName()));
    }

    /**
     * @test
     */
    public function testGetName()
    {
        $this->assertSame('test', $this->_object->getName());
    }

    /**
     * @test
     */
    public function testGetSkins()
    {
        $this->assertSame(array('default' => 'Default', 'test' => 'Test'), $this->_object->getSkins());
    }

    /**
     * @test
     */
    public function testGetMetaDataProvider()
    {
        $this->assertTrue($this->_object->getMetaDataProvider() instanceof \Yana\Views\MetaData\XmlDataProvider);
    }

    /**
     * @test
     */
    public function testSetMetaDataProvider()
    {
        $provider = new \Yana\Views\MetaData\XmlDataProvider(new \Yana\Files\Dir(__DIR__));
        $this->assertSame($provider, $this->_object->setMetaDataProvider($provider)->getMetaDataProvider());
    }

    /**
     * @test
     */
    public function testGetMetaData()
    {
        $metaData = $this->_object->getMetaData();
        $this->assertTrue($metaData instanceof \Yana\Views\MetaData\SkinMetaData);
        $this->assertSame("Test", $metaData->getTitle());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetTemplateDataNotFoundException()
    {
        $this->_object->getTemplateData('no-such-template');
    }

    /**
     * @test
     */
    public function testGetReport()
    {
        $report = $this->_object->getReport();
        $this->assertTrue($report instanceof \Yana\Report\IsReport);
        $expected = '/^<\?xml version="1\.0"\?>\s+' .
            '<report><title>Yana\\\\Views\\\\Skins\\\\Skin<\/title>' .
            '<text>Skin directory: test<\/text><report>' .
            '<title>FOO<\/title><text>.*?\/resources\/skin\/test\/test\.txt<\/text>' .
            '<text>No problems found\.<\/text><\/report><\/report>$/s';
        $this->assertRegExp($expected, (string) $report);
        $this->assertSame('<text>Skin directory: test</text>', (string) $report->getTexts()[0]);
    }

}
