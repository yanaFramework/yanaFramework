<?php
/**
 * YANA library
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

namespace Yana\Core\Sessions;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class NullSaveHandlerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Sessions\NullSaveHandler
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Core\Sessions\NullSaveHandler();
    }

    /**
     * @test
     */
    public function testOpen()
    {
        $this->assertTrue($this->object->open("", ""));
    }

    /**
     * @test
     */
    public function testClose()
    {
        $this->assertTrue($this->object->close());
    }

    /**
     * @test
     */
    public function testRead()
    {
        $this->assertSame("", $this->object->read(""));
    }

    /**
     * @test
     */
    public function testWrite()
    {
        $this->assertTrue($this->object->write("test", "data"));
        $this->assertSame("data", $this->object->read("test"));
    }

    /**
     * @test
     */
    public function testDestroy()
    {
        $this->assertTrue($this->object->destroy("test"));
        $this->assertSame("", $this->object->read(""));
        $this->assertTrue($this->object->write("test", "data"));
        $this->assertSame("data", $this->object->read("test"));
        $this->assertTrue($this->object->destroy("test"));
        $this->assertSame("", $this->object->read(""));
    }

    /**
     * @test
     */
    public function testGc()
    {
        $this->assertTrue($this->object->gc(1));
    }

}
