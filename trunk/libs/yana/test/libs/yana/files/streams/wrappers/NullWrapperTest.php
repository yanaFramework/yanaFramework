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

namespace Yana\Files\Streams\Wrappers;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../../../include.php';

/**
 * Test class for NullWrapper.
 */
class NullWrapperTest extends \PHPUnit_Framework_TestCase
{

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
    public function testDirectoryFunctions()
    {
        $this->assertFalse(is_dir('null://somethingelse'));
        $this->assertTrue(is_dir('null://dir'));
        $this->assertTrue(is_readable('null://dir'));
        $this->assertTrue(is_writable('null://dir'));
        $dir = dir('null://dir');
        $this->assertEquals('file1.ext', $dir->read());
        $this->assertEquals('file2.ext', $dir->read());
        $this->assertEquals(false, $dir->read());
        $this->assertTrue(rmdir('null://dir'));
        $this->assertFalse(is_dir('null://dir'));
        $this->assertTrue(mkdir('null://dir'));
        $this->assertTrue(is_dir('null://dir'));
    }

}

?>