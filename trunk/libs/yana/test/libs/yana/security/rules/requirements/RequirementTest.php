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

namespace Yana\Security\Rules\Requirements;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class RequirementTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Default security user group for testing
     */
    CONST GROUP = "group";
    /**
     * Default security user role for testing
     */
    CONST ROLE = "role";
    /**
     * Default security user level for testing
     */
    CONST LEVEL = 0;

    /**
     * @var \Yana\Security\Rules\Requirements\Requirement
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Security\Rules\Requirements\Requirement(self::GROUP, self::ROLE, self::LEVEL);
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
    public function testGetGroup()
    {
        $this->assertEquals(self::GROUP, $this->object->getGroup());
    }

    /**
     * @test
     */
    public function testGetRole()
    {
        $this->assertEquals(self::ROLE, $this->object->getRole());
    }

    /**
     * @test
     */
    public function testGetLevel()
    {
        $this->assertEquals(self::LEVEL, $this->object->getLevel());
    }

}
