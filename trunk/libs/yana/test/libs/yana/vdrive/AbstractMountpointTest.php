<?php
/**
 * PHPUnit test-case: Mountpoint
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
 * Test implementation for abstract class Mountpoint
 *
 * @package test
 * @ignore
 */
class MountpointImplementationTest extends AbstractMountpoint
{
    /**
     * constructor
     *
     * @access  public
     * @param   string  $path  path to the source file
     * @return  bool
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->mountpoint = new \Yana\Files\File($path);
        $this->type = "file";
    }
}

/**
 * Test class for AbstractResource
 *
 * @package  test
 */
class AbstractMountpointTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var    Mountpoint
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
        $this->object = new MountpointImplementationTest(CWD . 'resources/file.txt');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        unset($this->object);
    }

    /**
     * mount
     *
     * @test
     */
    public function testMount()
    {
        $mount = $this->object->mount();
        $this->assertTrue($mount, 'mount has failed');
        $mount = $this->object->mount();
        $this->assertTrue($mount, 'already mounted');
    }

    /**
     * get mountpoint
     *
     * @test
     */
    public function testGetMountpoint()
    {
        $getMount = $this->object->getMountpoint();
    }

    /**
     * get path
     *
     * @test
     */
    public function testGetPath()
    {
        $getPath = $this->object->getPath();
        $this->assertType('string', $getPath, 'the value should be of type string');
        $this->assertEquals(CWD.'resources/file.txt', $getPath, 'the given path should be the same as the expected');
    }

    /**
     * get Type
     *
     * @test
     */
    public function testGetType()
    {
        $type = $this->object->getType();
        $this->assertType('string', $type, 'the value should be of type string');
        $this->assertEquals('file', $type, 'the value of the given variable should be "file"');
    }

    /**
     * get report
     *
     * @test
     */
    public function testGetReport()
    {
        $getReport = $this->object->getReport();
        $this->assertTrue($getReport instanceof \Yana\Report\Xml, 'value must be an instance of \Yana\Report\Xml');
    }

    /**
     * is mounted
     *
     * @test
     */
    public function testIsMounted()
    {
        $isMounted = $this->object->isMounted();
        $this->assertFalse($isMounted, 'the vDrive is not mounted');
        $this->object->mount();
        $isMounted = $this->object->isMounted();
        $this->assertTrue($isMounted, 'the vDrive already mounted');
    }

    /**
     * requires readable
     *
     * @test
     */
    public function testRequiresReadable()
    {
       $requiresReadable = $this->object->requiresReadable();
       $this->assertFalse($requiresReadable, 'readable is not set');
       $this->object->setRequirements(true);
       $requiresReadable = $this->object->requiresReadable();
       $this->assertTrue($requiresReadable, 'readable is set');
    }

    /**
     * requires writeble
     *
     * @test
     */
    public function testRequiresWriteable()
    {
       $requiresWriteable = $this->object->requiresWriteable();
       $this->assertFalse($requiresWriteable, 'writeable is not set');
       $this->object->setRequirements(true, true);
       $requiresWriteable = $this->object->requiresWriteable();
       $this->assertTrue($requiresWriteable, 'writeable is set');
    }

    /**
     * requires executable
     *
     * @test
     */
    public function testRequiresExecutable()
    {
       $requiresExecutable = $this->object->requiresExecutable();
       $this->assertFalse($requiresExecutable, 'executable is not set');
       $this->object->setRequirements(true, true, true);
       $requiresExecutable = $this->object->requiresExecutable();
       $this->assertTrue($requiresExecutable, 'executable is set');
    }

    /**
     * @test
     */
    public function test()
    {
        $vDrive = new MountpointImplementationTest('');
        $result = $vDrive->getPath();
        $this->assertFalse($result, 'asset failed the path is empty.');
        $vDrive->setRequirements(true, true, true);
        $report = $vDrive->getReport();
        unset($vDrive);
    }

}

?>