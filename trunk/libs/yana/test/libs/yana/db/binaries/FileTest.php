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

namespace Yana\Db\Binaries;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class FileTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Binaries\File
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Binaries\File(__FILE__);
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
    public function testGetPath()
    {
        $this->assertSame(__FILE__, $this->object->getPath());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testReadNotFoundException()
    {
        $this->object = new \Yana\Db\Binaries\File('no_such_file');
        $this->object->read();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotReadableException
     */
    public function testReadNotReadableException()
    {
        $this->object = new \Yana\Db\Binaries\File(__FILE__);
        $this->object->read();
    }

    /**
     * @test
     */
    public function testGetMimeType()
    {
        $this->assertSame('application/unknown', $this->object->getMimeType());
    }

    /**
     * @test
     */
    public function testGetFilesize()
    {
        $this->assertSame(0, $this->object->getFilesize());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Files\NotFoundException
     */
    public function testRemoveFileNotFoundException()
    {
        \Yana\Db\Binaries\File::removeFile("no-such-file");
    }

}
