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

namespace Yana\Security\Data\SecurityLevels;


/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class LevelTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Data\SecurityLevels\Level
     */
    protected $object1;

    /**
     * @var \Yana\Security\Data\SecurityLevels\Level
     */
    protected $object2;

    /**
     * @var \Yana\Security\Data\SecurityLevels\Level
     */
    protected $object3;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object1 = new \Yana\Security\Data\SecurityLevels\Level(0, true);
        $this->object2 = new \Yana\Security\Data\SecurityLevels\Level(10, false);
        $this->object3 = new \Yana\Security\Data\SecurityLevels\Level(100, false);
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
    public function testGetSecurityLevel()
    {
        $this->assertSame(0, $this->object1->getSecurityLevel());
        $this->assertSame(10, $this->object2->getSecurityLevel());
        $this->assertSame(100, $this->object3->getSecurityLevel());
    }

    /**
     * @test
     */
    public function testIsUserProxyActive()
    {
        $this->assertSame(true, $this->object1->isUserProxyActive());
        $this->assertSame(false, $this->object2->isUserProxyActive());
        $this->assertSame(false, $this->object3->isUserProxyActive());
    }

}
