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

namespace Yana\Data\Adapters\MemCache;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 */
class ServerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Data\Adapters\MemCache\Server
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Data\Adapters\MemCache\Server('localhost', 1234, 10);
    }

    /**
     * @test
     */
    public function testGetHostName()
    {
        $this->assertEquals('localhost', $this->object->getHostName());
    }

    /**
     * @test
     */
    public function testGetPort()
    {
        $this->assertEquals(1234, $this->object->getPort());
    }

    /**
     * @test
     */
    public function testGetWeight()
    {
        $this->assertEquals(10, $this->object->getWeight());
    }

}
