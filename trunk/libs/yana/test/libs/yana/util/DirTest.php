<?php
/**
 * PHPUnit test-case: Hashtable
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

namespace Yana\Util;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../include.php';

/**
 * Test class for Hashtable
 *
 * @package  test
 */
class DirTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testGetSize()
    {
        $path = CWD . '/resources';

        $size = \Yana\Util\Dir::getSize(CWD . '/resources', false);
        $scanDirSize = 0;
        // reduce by 2 for bogus-entries '.' and '..'
        foreach (scandir($path) as $file)
        {
            $file = $path . '/' . $file;
            if (is_file($file)) {
                $scanDirSize += filesize($file);
            }
        }
        $this->assertInternalType('int', $size, 'expecting getSize() to return result of type integer');
        $this->assertEquals($size, $scanDirSize, "Size does not match the size of the files in the directory");
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotFoundException
     */
    public function testGetSizeNotFoundException()
    {
        $size = \Yana\Util\Dir::getSize('no-such-directory', false);
    }

    /**
     * dirlist
     *
     * @test
     */
    public function testListFiles()
    {
        $path = CWD . '/resources';

        // read all txt entries
        $dirList = \Yana\Util\Dir::listFiles($path);
        $expected = array();
        foreach (scandir($path) as $entry)
        {
            if (!is_dir($path . '/' . $entry)) {
                $expected[] = $entry;
            }
        }
        $this->assertInternalType('array', $dirList);
        $this->assertNotEmpty($dirList);
        $this->assertEquals($expected, $dirList, 'directory listing should match directory contents');

    }

    /**
     * dirlist
     *
     * @test
     */
    public function testListFilesWithFilter()
    {
        $path = CWD . '/resources';

        // read all txt entries
        $dirList = \Yana\Util\Dir::listFiles($path, '*.txt');
        $expected = array();
        foreach (glob($path . '/*.txt') as $entry)
        {
            $expected[] = basename($entry);
        }
        $this->assertInternalType('array', $dirList);
        $this->assertGreaterThanOrEqual(1, count($dirList));
        $this->assertEquals($expected, $dirList, 'directory listing with filter *.txt should match directory contents');

    }

    /**
     * dirlist
     *
     * @test
     */
    public function testListFilesWithMultipleFilters()
    {
        $path = CWD . '/resources';

        // choose more file types
        $dirList = \Yana\Util\Dir::listFiles($path, '*.txt|*.xml|*.dat');
        $expected = array();
        foreach (glob($path . '/*.{txt,xml,dat}', GLOB_BRACE) as $entry)
        {
            $expected[] = basename($entry);
        }
        sort($expected);
        $this->assertInternalType('array', $dirList);
        $this->assertGreaterThanOrEqual(1, count($dirList));
        $this->assertEquals($expected, $dirList, 'directory listing with filter *.txt, *.xml, *.dat should match directory contents');
    }

    /**
     * dirlist
     *
     * @test
     */
    public function testListDirectories()
    {
        $path = CWD . '/resources';

        // read all txt entries
        $dirList = \Yana\Util\Dir::listDirectories($path);
        $expected = array();
        foreach (scandir($path) as $entry)
        {
            if ($entry !== '.' && $entry !== '..' && is_dir($path . '/' . $entry)) {
                $expected[] = $entry;
            }
        }
        $this->assertInternalType('array', $dirList);
        $this->assertNotEmpty($dirList);
        $this->assertEquals($expected, $dirList, 'directory listing should match directory contents');

    }

    /**
     * dirlist
     *
     * @test
     */
    public function testListFilesAndDirectories()
    {
        $path = CWD . '/resources';

        // read all txt entries
        $dirList = \Yana\Util\Dir::listFilesAndDirectories($path);
        $expected = array();
        foreach (scandir($path) as $entry)
        {
            if ($entry !== '.' && $entry !== '..') {
                $expected[] = $entry;
            }
        }
        $this->assertInternalType('array', $dirList);
        $this->assertNotEmpty($dirList);
        $this->assertEquals($expected, $dirList, 'directory listing should match directory contents');

    }

}
