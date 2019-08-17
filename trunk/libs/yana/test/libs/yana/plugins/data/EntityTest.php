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
declare(strict_types=1);

namespace Yana\Plugins\Data;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Data\Entity
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Data\Entity();
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
        $this->assertSame('', $this->object->getId());
    }

    /**
     * @test
     */
    public function testSetId()
    {
        $this->assertSame('Test', $this->object->setId('Test')->getId());
    }

    /**
     * @test
     */
    public function testSetActive()
    {
        $this->assertTrue($this->object->setActive(true)->isActive());
        $this->assertFalse($this->object->setActive(false)->isActive());
    }

    /**
     * @test
     */
    public function testIsActive()
    {
        $this->assertFalse($this->object->isActive());
    }

}
