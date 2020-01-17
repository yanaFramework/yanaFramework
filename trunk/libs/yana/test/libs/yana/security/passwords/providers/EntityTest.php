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

namespace Yana\Security\Passwords\Providers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Passwords\Providers\Entity
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Security\Passwords\Providers\Entity();
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
    public function testGetId()
    {
        $this->assertNull($this->object->getId());
    }

    /**
     * @test
     */
    public function testSetId()
    {
        $this->assertSame(-123, $this->object->setId('-123')->getId());
        $this->assertSame(-234, $this->object->setId(-234)->getId());
    }

    /**
     * @test
     */
    public function testGetName()
    {
        $this->assertSame("", $this->object->getName());
    }

    /**
     * @test
     */
    public function testGetMethod()
    {
        $this->assertSame("", $this->object->getMethod());
    }

    /**
     * @test
     */
    public function testGetHost()
    {
        $this->assertNull($this->object->getHost());
    }

    /**
     * @test
     */
    public function testSetName()
    {
        $this->assertSame('Abß@123!', $this->object->setName('Abß@123!')->getName());
    }

    /**
     * @test
     */
    public function testSetMethod()
    {
        $this->assertSame('Abß@123!', $this->object->setMethod('Abß@123!')->getMethod());
    }

    /**
     * @test
     */
    public function testSetHost()
    {
        $this->assertSame('Abß@123!', $this->object->setHost('Abß@123!')->getHost());
    }

}
