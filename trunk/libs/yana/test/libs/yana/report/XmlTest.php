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

namespace Yana\Report;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class XmlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Report\Xml
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = \Yana\Report\Xml::createReport();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * Creates a new XML document, including a header and final new-line.
     *
     * @test
     */
    public function testCreateReport()
    {
        $this->assertTrue(\Yana\Report\Xml::createReport() instanceof \Yana\Report\IsReport);
        $xml = '<?xml version="1.0"?>' . "\n"
            . '<report><title>Test</title></report>' . "\n";
        $this->assertSame($xml, \Yana\Report\Xml::createReport("Test")->asXML());
    }

    /**
     * This function adds a child to the report node.
     *
     * @test
     */
    public function testAddReport()
    {
        $report = $this->object->addReport("Test");
        $xml = '<report><title>Test</title></report>';
        $this->assertSame($xml, $report->asXML());
        
    }

    /**
     * This function adds a child to the report node.
     *
     * @test
     */
    public function testAddMultipleReports()
    {
        $this->object->addReport("1");
        $this->object->addReport("2");
        $xml = '<?xml version="1.0"?>' . "\n"
            . '<report><report><title>1</title></report><report><title>2</title></report></report>' . "\n";
        $this->assertSame($xml, $this->object->asXML());
    }

    /**
     * This function adds a child to the report node.
     *
     * @test
     */
    public function testAddNestedReports()
    {
        $this->object->addReport("1")->addReport("2");
        $xml = '<?xml version="1.0"?>' . "\n"
            . '<report><report><title>1</title><report><title>2</title></report></report></report>' . "\n";
        $this->assertSame($xml, $this->object->asXML());
    }

    /**
     * Returns a list of reports.
     *
     * @test
     */
    public function testGetReports()
    {
        $reports = array(
            $this->object->addReport("1"),
            $this->object->addReport("2")
        );
        $this->assertEquals($reports, $this->object->getReports());
    }

    /**
     * @test
     */
    public function testGetTitle()
    {
        $this->assertSame("", $this->object->getTitle());
        $this->assertSame("Title", $this->object->addReport("Title")->getTitle());
    }

    /**
     * @test
     */
    public function testAddText()
    {
        $nodes = array(
            new \Yana\Report\Xml('<text>1</text>'),
            new \Yana\Report\Xml('<text>2</text>')
        );
        $this->assertEquals($nodes, $this->object->addText("1")->addText("2")->getTexts());
    }

    /**
     * @test
     */
    public function testGetTexts()
    {
        $this->assertEquals(array(), $this->object->getTexts());
    }

    /**
     * @test
     */
    public function testAddNotice()
    {
        $nodes = array(
            new \Yana\Report\Xml('<notice>1</notice>'),
            new \Yana\Report\Xml('<notice>2</notice>')
        );
        $this->assertEquals($nodes, $this->object->addNotice("1")->addNotice("2")->getNotices());
    }

    /**
     * @test
     */
    public function testGetNotices()
    {
        $this->assertEquals(array(), $this->object->getNotices());
    }

    /**
     * @test
     */
    public function testAddWarning()
    {
        $nodes = array(
            new \Yana\Report\Xml('<warning>1</warning>'),
            new \Yana\Report\Xml('<warning>2</warning>')
        );
        $this->assertEquals($nodes, $this->object->addWarning("1")->addWarning("2")->getWarnings());
    }

    /**
     * @test
     */
    public function testGetWarnings()
    {
        $this->assertEquals(array(), $this->object->getWarnings());
    }

    /**
     * @test
     */
    public function testAddError()
    {
        $nodes = array(
            new \Yana\Report\Xml('<error>1</error>'),
            new \Yana\Report\Xml('<error>2</error>')
        );
        $this->assertEquals($nodes, $this->object->addError("1")->addError("2")->getErrors());
    }

    /**
     * @test
     */
    public function testGetErrors()
    {
        $this->assertEquals(array(), $this->object->getErrors());
    }

    /**
     * @test
     */
    public function test__toString()
    {
        $xml = '<?xml version="1.0"?>' . "\n"
            . '<report><title>Test</title></report>' . "\n";
        $this->assertSame($xml, \Yana\Report\Xml::createReport("Test")->__toString());
    }

    /**
     * @test
     */
    public function testIsReport()
    {
        $this->assertTrue($this->object->isReport());
        $this->assertTrue($this->object->addReport('test')->isReport());
        $node1 = new \Yana\Report\Xml('<report/>');
        $this->assertTrue($node1->isReport());
        $node2 = new \Yana\Report\Xml('<other/>');
        $this->assertFalse($node2->isReport());
    }

    /**
     * @test
     */
    public function testIsTitle()
    {
        $this->assertFalse($this->object->isTitle());
        $this->assertTrue($this->object->addReport('test')->title->isTitle());
        $node1 = new \Yana\Report\Xml('<title/>');
        $this->assertTrue($node1->isTitle());
        $node2 = new \Yana\Report\Xml('<other/>');
        $this->assertFalse($node2->isTitle());
    }

    /**
     * @test
     */
    public function testIsText()
    {
        $this->assertFalse($this->object->isText());
        $this->assertTrue($this->object->addText('test')->text->isText());
        $node1 = new \Yana\Report\Xml('<text/>');
        $this->assertTrue($node1->isText());
        $node2 = new \Yana\Report\Xml('<other/>');
        $this->assertFalse($node2->isText());
    }

    /**
     * @test
     */
    public function testIsNotice()
    {
        $this->assertFalse($this->object->isNotice());
        $this->assertTrue($this->object->addNotice('test')->notice->isNotice());
        $node1 = new \Yana\Report\Xml('<notice/>');
        $this->assertTrue($node1->isNotice());
        $node2 = new \Yana\Report\Xml('<other/>');
        $this->assertFalse($node2->isNotice());
    }

    /**
     * @test
     */
    public function testIsWarning()
    {
        $this->assertFalse($this->object->isWarning());
        $this->assertTrue($this->object->addWarning('test')->warning->isWarning());
        $node1 = new \Yana\Report\Xml('<warning/>');
        $this->assertTrue($node1->isWarning());
        $node2 = new \Yana\Report\Xml('<other/>');
        $this->assertFalse($node2->isWarning());
    }

    /**
     * @test
     */
    public function testIsError()
    {
        $this->assertFalse($this->object->isError());
        $this->assertTrue($this->object->addError('test')->error->isError());
        $node1 = new \Yana\Report\Xml('<error/>');
        $this->assertTrue($node1->isError());
        $node2 = new \Yana\Report\Xml('<other/>');
        $this->assertFalse($node2->isError());
    }

}
