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
class NullReaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Dummy requirement.
     *
     * @var \Yana\Security\Rules\Requirements\Requirement
     */
    protected $requirement;

    /**
     * @var \Yana\Security\Rules\Requirements\NullReader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->requirement = new \Yana\Security\Rules\Requirements\Requirement("group", "role", 0);
        $this->object = new \Yana\Security\Rules\Requirements\NullReader($this->requirement);
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
    public function testLoadRequirementsByAssociatedAction()
    {
        $collection = $this->object->loadRequirementsByAssociatedAction("");
        $this->assertTrue($collection instanceof \Yana\Security\Rules\Requirements\Collection, 'Instance of Collection expected');
    }

    /**
     * @test
     */
    public function testLoadRequirementById()
    {
        $collection = $this->object->loadRequirementById(1);
        $this->assertTrue($collection instanceof \Yana\Security\Rules\Requirements\Requirement, 'Instance of Requirement expected');
    }

    /**
     * @test
     */
    public function testLoadRequirementById1()
    {
        $collection = $this->object->loadRequirementById(0);
        $this->assertTrue($collection instanceof \Yana\Security\Rules\Requirements\Requirement, 'Instance of Requirement expected');
    }

    /**
     * @test
     */
    public function testLoadListOfGroups()
    {
        $this->assertInternalType('array', $this->object->loadListOfGroups());
        $this->assertEquals(array("group"), $this->object->loadListOfGroups());
    }

    /**
     * @test
     */
    public function testLoadListOfRoles()
    {
        $this->assertInternalType('array', $this->object->loadListOfRoles());
        $this->assertEquals(array("role"), $this->object->loadListOfRoles());
    }

}

?>