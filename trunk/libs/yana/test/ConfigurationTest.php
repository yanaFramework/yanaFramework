<?php
/**
 * PHPUnit test-case: Configuration
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

namespace Yana\VDrive;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';

/**
 * Test class for Configuration
 *
 * @package  test
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var    Configuration
     * @access protected
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->object = Configuration::loadFile(CWD . '/resources/test.drive.xml');
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
     * @test
     */
    public function test()
    {
        $xml = simplexml_load_file(CWD . '/resources/test.drive.xml');

        // test file loading
        $this->assertEquals((string) $this->object, $xml->asXML(), '"file loading" test failed');

        $this->object = Configuration::createDrive();
        // test vars
        $this->object->addNodeVar('foo', 'yes');
        $this->object->addNodeVar('bar', 'no');
        $this->assertEquals($this->object->getNodeVars()->asXML(), '<var name="foo" value="yes"/>', '"set/get vars" test failed');
        // test includes
        $this->object->addNodeInclude('foo.class.php');
        $this->object->addNodeInclude('bar.class.php');
        $this->assertEquals($this->object->getNodeIncludes()->asXML(), '<include path="foo.class.php"/>', '"set/get includes" test failed');
        // test files
        $file = $this->object->addNodeFile("test", true);
        $this->assertTrue(isset($file), '"create file" test failed');
        // test requirements
        $file->setNodeRequirements(true, true, true);
        $this->assertTrue($file->nodeRequiresReadable(), '"get/set readable" test failed');
        $this->assertTrue($file->nodeRequiresWriteable(), '"get/set writeable" test failed');
        $this->assertTrue($file->nodeRequiresExecutable(), '"get/set executable" test failed');
        // test name
        $file->setNodeName("bar");
        $this->assertEquals($file->getNodeName(), "bar", '"get/set name" test failed');
        // test auto-mount
        $file->setNodeAutomount(false);
        $this->assertFalse($file->getNodeAutomount(), '"get/set automount to false" test failed');
        $file->setNodeAutomount(true);
        $this->assertTrue($file->getNodeAutomount(), '"get/set automount to true" test failed');
        // test source
        $file->addNodeSource('foo.txt');
        $file->addNodeSource('bar.txt');
        $this->assertEquals($file->getNodeSources()->asXML(), '<source>foo.txt</source>', '"set/get source" test failed');
        // test directories
        $dir = $this->object->addNodeDir("test", true);
        $this->assertTrue(isset($dir), '"create dir" test failed');
        // test requirements
        $dir->setNodeRequirements(true, true, true);
        $this->assertTrue($dir->nodeRequiresReadable(), '"get/set readable" test failed');
        $this->assertTrue($dir->nodeRequiresWriteable(), '"get/set writeable" test failed');
        $this->assertTrue($dir->nodeRequiresExecutable(), '"get/set executable" test failed');
        // test name
        $dir->setNodeName("bar");
        $this->assertEquals($dir->getNodeName(), "bar", '"get/set name" test failed');
        // test filter
        $dir->setNodeFilter("*.foo");
        $this->assertEquals($dir->getNodeFilter(), "*.foo", '"get/set filter" test failed');
        // test auto-mount
        $dir->setNodeAutomount(false);
        $this->assertFalse($dir->getNodeAutomount(), '"get/set automount to false" test failed');
        $dir->setNodeAutomount(true);
        $this->assertTrue($dir->getNodeAutomount(), '"get/set automount to true" test failed');
        // test source
        $dir->addNodeSource('foo');
        $dir->addNodeSource('bar');
        $this->assertEquals($dir->getNodeSources()->asXML(), '<source>foo</source>', '"set/get source" test failed');
        $file = $dir->addNodeFile("bar");
        $this->assertEquals($file->getNodeName(), "bar", '"add file" test failed');
    }
}
?>