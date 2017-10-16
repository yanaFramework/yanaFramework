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

namespace Yana\Log\Formatter;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Log\Formatter\Message
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Log\Formatter\Message();
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
    public function testGetLevel()
    {
        $this->assertSame(0, $this->object->getLevel());
    }

    /**
     * @test
     */
    public function testSetLevel()
    {
        $this->assertSame(10, $this->object->setLevel(10)->getLevel());
    }

    /**
     * @test
     */
    public function testGetDescription()
    {
        $this->assertSame("", $this->object->getDescription());
    }

    /**
     * @test
     */
    public function testSetDescription()
    {
        $this->assertSame("Test", $this->object->setDescription("Test")->getDescription());
    }

    /**
     * @test
     */
    public function testGetFilename()
    {
        $this->assertSame("", $this->object->getFilename());
    }

    /**
     * @test
     */
    public function testSetFilename()
    {
        $this->assertSame("Test", $this->object->setFilename("Test")->getFilename());
    }

    /**
     * @test
     */
    public function testGetLineNumber()
    {
        $this->assertSame(0, $this->object->getLineNumber());
    }

    /**
     * @test
     */
    public function testSetLineNumber()
    {
        $this->assertSame(10, $this->object->setLineNumber(10)->getLineNumber());
    }

    /**
     * @test
     */
    public function testHasMore()
    {
        $this->assertFalse($this->object->hasMore());
    }

    /**
     * @test
     */
    public function testSetHasMore()
    {
        $this->assertTrue($this->object->setHasMore(true)->hasMore());
    }

}
