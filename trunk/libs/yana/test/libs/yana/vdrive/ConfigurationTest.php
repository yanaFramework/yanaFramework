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

namespace Yana\VDrive;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  \Yana\VDrive\Configuration
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = \Yana\VDrive\Configuration::createInstanceFromFile(CWD . '/resources/test.drive.xml');
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
     * @expectedException \Yana\Core\Exceptions\Files\NotFoundException
     */
    public function testCreateInstanceFromFileNotFoundException()
    {
        \Yana\VDrive\Configuration::createInstanceFromFile('no-such-file');
    }

    /**
     * @test
     */
    public function testCreateInstanceFromString()
    {
        $actual = \Yana\VDrive\Configuration::createInstanceFromString(\file_get_contents(CWD . '/resources/test.drive.xml'));
        $this->assertEquals($this->object, $actual);
    }

    /**
     * @test
     */
    public function testIsInclude()
    {
        $this->assertFalse(\Yana\VDrive\Configuration::createInstanceFromString('<drive/>')->isInclude());
        $this->assertTrue(\Yana\VDrive\Configuration::createInstanceFromString('<include/>')->isInclude());
    }

    /**
     * @test
     */
    public function testSetNodeRequirements()
    {
        $file = $this->object->addNodeFile("test");
        $requirements1 = $file->setNodeRequirements(true, true, true);
        $this->assertTrue($requirements1 instanceof \Yana\VDrive\Configuration);
        $this->assertEquals('requirements', $requirements1->getName());

        $attributes = $requirements1->toArray();
        $this->assertArrayHasKey('@readable', $attributes);
        $this->assertArrayHasKey('@writeable', $attributes);
        $this->assertArrayHasKey('@executable', $attributes);
        $this->assertEquals('yes', $attributes['@readable']);
        $this->assertEquals('yes', $attributes['@writeable']);
        $this->assertEquals('yes', $attributes['@executable']);
        $this->assertTrue($file->nodeRequiresReadable());
        $this->assertTrue($file->nodeRequiresWriteable());
        $this->assertTrue($file->nodeRequiresExecutable());

        $requirements2 = $file->setNodeRequirements(false, false, false);
        $this->assertSame('no', (string) $requirements1->attributes()->readable);
        $this->assertSame('no', (string) $requirements1->attributes()->writeable);
        $this->assertSame('no', (string) $requirements1->attributes()->executable);
        $this->assertSame('no', (string) $requirements2->attributes()->readable);
        $this->assertSame('no', (string) $requirements2->attributes()->writeable);
        $this->assertSame('no', (string) $requirements2->attributes()->executable);
        $this->assertEquals($requirements1, $requirements2);
        $this->assertFalse($file->nodeRequiresReadable());
        $this->assertFalse($file->nodeRequiresWriteable());
        $this->assertFalse($file->nodeRequiresExecutable());
    }

    /**
     * @test
     */
    public function testGetNodeFiles()
    {
        $file1 = $this->object->addNodeFile("test1");
        $file2 = $this->object->addNodeFile("test2");
        $files = $this->object->getNodeFiles();
        $this->assertCount(2, $files);
        $this->assertEquals($file1, $files[0]);
        $this->assertEquals($file2, $files[1]);
    }

    /**
     * Test vars.
     *
     * @test
     */
    public function testVars()
    {
        $this->object = \Yana\VDrive\Configuration::createInstance();
        $var = $this->object->addNodeVar('foo', 'yes');
        $this->assertEquals('yes', $var->getNodeValue());
        $this->assertTrue($var->isVar());
        $this->assertFalse($this->object->isVar());
        $this->object->addNodeVar('bar', 'no');
        $vars = $this->object->getNodeVars();
        $this->assertEquals($vars[0]->asXML(), '<var name="foo" value="yes"/>', '"set/get vars" test failed');
        $this->assertEquals($vars[1]->asXML(), '<var name="bar" value="no"/>', '"set/get vars" test failed');
    }

    /**
     * @test
     */
    public function testNamespace()
    {
        $file = $this->object->addNodeFile("test");
        $this->assertNull($this->object->getNodeNamespace());
        $this->assertNull($file->getNodeNamespace());
        $file->setNodeNamespace('test');
        $this->assertEquals('test', $file->getNodeNamespace(), 'Must add attribute');
        $file->setNodeNamespace('test1');
        $this->assertEquals('test1', $file->getNodeNamespace(), 'Must replace attribute');
        $this->object->setNodeNamespace('test');
        $this->assertNull($this->object->getNodeNamespace(), 'Must only add attribute to file-nodes');
    }

    /**
     * @test
     */
    public function testFunctions()
    {
        // test includes
        $this->object->addNodeInclude('foo.php');
        $this->object->addNodeInclude('bar.php');
        $this->assertEquals($this->object->getNodeIncludes()[0]->asXML(), '<include path="foo.php"/>', '"set/get includes" test failed');
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
        $this->assertEquals('<source>foo.txt</source>', $file->getNodeSources()[0]->asXML(), '"set/get source" test failed');
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
        $this->assertEquals($dir->getNodeSources()[0]->asXML(), '<source>foo</source>', '"set/get source" test failed');
        $file = $dir->addNodeFile("bar");
        $this->assertEquals($file->getNodeName(), "bar", '"add file" test failed');
    }

}
