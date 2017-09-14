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

namespace Yana\Files;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../include.php';

/**
 * @package  test
 */
class DirectoryIteratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Files\DirectoryIterator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $streamFacade = new \Yana\Files\Streams\Stream();
        if (!$streamFacade->isRegistered('null')) {
            $streamFacade->registerWrapper('null');
            file_put_contents('null://dir/file1.ext', 'dummy');
            file_put_contents('null://dir/file2.ext', 'dummy');
        }
        $path = 'null://dir/';
        $dir = new \Yana\Files\Dir($path);
        $dir->read();
        $this->object = new \Yana\Files\DirectoryIterator($dir);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $streamFacade = new \Yana\Files\Streams\Stream();
        $streamFacade->unregisterWrapper('null');
    }

    /**
     * @test
     */
    public function testCount()
    {
        $this->assertEquals(2, $this->object->count());
    }

    /**
     * @test
     */
    public function testCurrent()
    {
        $this->assertEquals('file1.ext', $this->object->current());
    }

    /**
     * @test
     */
    public function testKey()
    {
        $this->assertEquals(0, $this->object->key());
    }

    /**
     * @test
     */
    public function testNext()
    {
        $this->object->next();
        $this->assertEquals('file2.ext', $this->object->current());
    }

    /**
     * @test
     */
    public function testRewind()
    {
        $this->object->next();
        $this->object->next();
        $this->object->rewind();
        $this->assertEquals('file1.ext', $this->object->current());
    }

    /**
     * @test
     */
    public function testValid()
    {
        $this->assertTrue($this->object->valid());
        $this->object->next();
        $this->object->next();
        $this->assertFalse($this->object->valid());
    }

}
