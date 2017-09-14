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

namespace Yana\VDrive;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class FileTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\VDrive\File
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
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
    public function testGetType()
    {
        $type = '\Yana\VDrive\FileTest';
        $this->object = new \Yana\VDrive\File(__FILE__, '\Yana\VDrive\FileTest');
        $this->assertSame($type, $this->object->getType());
    }

    /**
     * @test
     */
    public function testGetMountpoint()
    {
        $this->object = new \Yana\VDrive\File(__FILE__);
        $this->assertSame(__FILE__, $this->object->getMountpoint()->getPath());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\ClassNotFoundException
     */
    public function testConstructorClassNotFoundException()
    {
        new \Yana\VDrive\File(__FILE__, 'NoSuchClass');
    }

}
