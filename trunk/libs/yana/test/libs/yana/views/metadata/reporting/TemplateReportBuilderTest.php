<?php
/**
 * YANA library
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

namespace Yana\Views\MetaData\Reporting;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 */
class TemplateReportBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\MetaData\Reporting\TemplateReportBuilder
     */
    protected $object;

    /**
     * @var \Yana\Report\Xml
     */
    protected $report;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->report = new \Yana\Report\Xml("<report/>");
        $this->object = new \Yana\Views\MetaData\Reporting\TemplateReportBuilder($this->report);
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
    public function testBuildReportEmpty()
    {
        $report = $this->object->buildReport();
        $expected = '<report><text>No problems found.</text></report>' . "\n";
        $this->assertStringEndsWith($expected, (string) $report);
    }

    /**
     * @test
     */
    public function testGetTemplateConfiguration()
    {
        $this->assertNull($this->object->getTemplateConfiguration());
    }

    /**
     * @test
     */
    public function testSetTemplateConfiguration()
    {
        $meta = new \Yana\Views\MetaData\TemplateMetaData();
        $meta->setId(__FUNCTION__);
        $this->assertSame($meta, $this->object->setTemplateConfiguration($meta)->getTemplateConfiguration());
    }

    /**
     * @test
     */
    public function testBuildReport()
    {
        $template = new \Yana\Views\MetaData\TemplateMetaData();
        $template->setId('template-id')
            ->setFile(__FILE__);
        $this->object->setTemplateConfiguration($template);
        $report = $this->object->buildReport();
        $expected = '/<report><text>File: [^<]+<\/text><text>No problems found.<\/text><\/report>/';
        $this->assertRegExp($expected, (string) $report);
    }

    /**
     * @test
     */
    public function testBuildReportWithNoFile()
    {
        $template = new \Yana\Views\MetaData\TemplateMetaData();
        $template->setId('template-id');
        $this->object->setTemplateConfiguration($template);
        $report = $this->object->buildReport();
        $expected = '<error>File \'\' does not exist. ' .
            'Please make sure this path and filename is correct and you have all files installed. ' .
            'Reinstall if necessary.</error>';
        $this->assertContains($expected, (string) $report);
    }

    /**
     * @test
     */
    public function testBuildReportWithFilePathError()
    {
        $template = new \Yana\Views\MetaData\TemplateMetaData();
        $template->setId('template-id')
            ->setFile('no-such-file');
        $this->object->setTemplateConfiguration($template);
        $report = $this->object->buildReport();
        $expected = '<report><error>File \'no-such-file\' does not exist. ' .
            'Please make sure this path and filename is correct and you have all files installed. ' .
            'Reinstall if necessary.</error></report>' . "\n";
        $this->assertStringEndsWith($expected, (string) $report);
    }

    /**
     * @test
     */
    public function testBuildReportWithInvalidStylesheet()
    {
        $template = new \Yana\Views\MetaData\TemplateMetaData();
        $template->setId('template-id')
            ->setFile(__FILE__)
            ->setStyles(array('no-such-style'));
        $this->object->setTemplateConfiguration($template);
        $report = $this->object->buildReport();
        $expected = '<error>A required stylesheet \'no-such-style\' is not available. ' .
            'This template may not be displayed correctly.</error>';
        $this->assertContains($expected, (string) $report);
    }

    /**
     * @test
     */
    public function testBuildReportWithInvalidScript()
    {
        $template = new \Yana\Views\MetaData\TemplateMetaData();
        $template->setId('template-id')
            ->setFile(__FILE__)
            ->setScripts(array('no-such-script'));
        $this->object->setTemplateConfiguration($template);
        $report = $this->object->buildReport();
        $expected = '<error>A required javascript file \'no-such-script\' is not available. ' .
            'This template may not be displayed correctly.</error>';
        $this->assertContains($expected, (string) $report);
    }

    /**
     * @test
     */
    public function testBuildReportWithInvalidLanguage()
    {
        $template = new \Yana\Views\MetaData\TemplateMetaData();
        $template->setId('template-id')
            ->setFile(__FILE__)
            ->setLanguages(array('invalid-language'));
        $this->object->setTemplateConfiguration($template);
        $report = $this->object->buildReport();
        $expected = '<warning>No language-pack found for id \'invalid-language\'. ' .
            'Please check if the chosen language file is correct and update your language pack if needed.</warning>';
        $this->assertContains($expected, (string) $report);
    }

}
