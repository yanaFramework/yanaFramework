<?php
/**
 * PHPUnit test-case: Toolbox
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
 * Test class for Toolbox
 *
 * @package  test
 */
class ToolboxTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var  string
     */
    protected $dir = 'resources/';

    /**
     * Create a new instance.
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->dir = CWD . $this->dir;
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        // intentionally left blank
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        // intentionally left blank
    }

    /**
     * dirlist
     *
     * @test
     */
    public function testDirList()
    {
        // read all txt entries
        $dirList = dirlist($this->dir, '*.txt');
        $expected = array();
        foreach (glob($this->dir . '/*.txt') as $path)
        {
            $expected[] = basename($path);
        }
        $this->assertType('array', $dirList);
        $this->assertGreaterThanOrEqual(1, count($dirList));
        $this->assertEquals($expected, $dirList, 'directory listing with filter *.txt should match directory contents');

        // read without set a filter
        $dirList = dirlist($this->dir);
        $expected = array();
        foreach (scandir($this->dir) as $path)
        {
            if (!is_dir($path)) {
                $expected[] = $path;
            }
        }
        $this->assertType('array', $dirList);
        $this->assertGreaterThanOrEqual(1, count($dirList));
        $this->assertEquals($expected, $dirList, 'directory listing should match directory contents');

        // choose more file types
        $dirList = dirlist($this->dir, '*.txt');
        $expected = array();
        foreach (glob($this->dir . '/*.txt') as $path)
        {
            $expected[] = basename($path);
        }
        foreach (glob($this->dir . '/*.xml') as $path)
        {
            $expected[] = basename($path);
        }
        foreach (glob($this->dir . '/*.dat') as $path)
        {
            $expected[] = basename($path);
        }
        sort($expected);
        $dirList = dirlist($this->dir, '*.txt|*.xml|*.dat');
        $this->assertType('array', $dirList);
        $this->assertGreaterThanOrEqual(1, count($dirList));
        $this->assertEquals($expected, $dirList, 'directory listing with filter *.txt, *.xml, *.dat should match directory contents');

        // set switch to yana_get_all
        $dirList = dirlist($this->dir, '', YANA_GET_ALL);
        $expected = array();
        foreach (scandir($this->dir) as $path)
        {
            if ($path[0] !== '.') {
                $expected[] = basename($path);
            }
        }
        $this->assertType('array', $dirList);
        $this->assertGreaterThanOrEqual(1, count($dirList));
        $this->assertGreaterThanOrEqual(13, count($dirList), 'the value must be 13 or higher');
        $this->assertEquals($expected, $dirList, 'directory listing without filter should match directory contents');

        // set switch to yana_get_files
        $dirList = dirlist($this->dir, '', YANA_GET_FILES);
        $expected = array();
        foreach (scandir($this->dir) as $path)
        {
            if (is_file($this->dir . $path)) {
                $expected[] = basename($path);
            }
        }
        $this->assertType('array', $dirList);
        $this->assertGreaterThanOrEqual(1, count($dirList));
        $this->assertGreaterThanOrEqual(10, count($dirList), 'the value must be 10 or higher');
        $this->assertEquals($expected, $dirList, 'directory listing for files should match directory contents');

        // set switch to yana_get_dirs
        $dirList = dirlist($this->dir, '', YANA_GET_DIRS);
        $expected = array();
        foreach (scandir($this->dir) as $path)
        {
            if (is_dir($this->dir . $path) && $path[0] !== '.') {
                $expected[] = $path;
            }
        }
        $this->assertType('array', $dirList);
        $this->assertGreaterThanOrEqual(1, count($dirList), 'the value needs to be 1 or higher');
        $this->assertGreaterThanOrEqual(3, count($dirList), 'the value needs to be 3 or higher');
        $this->assertEquals($expected, $dirList, 'directory listing for direcories should match directory contents');
    }

    /**
     * DirListInvalidArgument
     *
     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    public function testDirListInvalidArgument()
    {
        $dirList = dirlist('newresource');
    }

}

?>