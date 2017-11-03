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
class DataWriterHelperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Configs\MethodCollection
     */
    protected $collection;

    /**
     * @var \Yana\Security\Rules\Requirements\DataWriterHelper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->collection = new \Yana\Plugins\Configs\MethodCollection();
        $this->object = new \Yana\Security\Rules\Requirements\DataWriterHelper($this->collection);
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
    public function testGetActionTitlesDefault()
    {
        $this->assertSame(array(), $this->object->getActionTitles());
    }

    /**
     * @test
     */
    public function testGetRoleNamesDefault()
    {
        $this->assertSame(array(), $this->object->getRoleNames());
    }

    /**
     * @test
     */
    public function testGetGroupNamesDefault()
    {
        $this->assertSame(array(), $this->object->getGroupNames());
    }

    /**
     * @test
     */
    public function testGetRequirementsDefault()
    {
        $this->assertSame(array(), $this->object->getRequirements());
    }

    /**
     * @test
     */
    public function testGetActionTitles()
    {
        $this->collection[] = (new \Yana\Plugins\Configs\MethodConfiguration())->setMethodName('N1')->setTitle('Title1');
        $this->collection[] = (new \Yana\Plugins\Configs\MethodConfiguration())->setMethodName('N2');
        $this->assertEquals(array('N1' => 'Title1', 'N2' => 'N2'), $this->object->getActionTitles());
    }

    /**
     * @test
     */
    public function testGetRoleNames()
    {
        $this->collection[] = (new \Yana\Plugins\Configs\MethodConfiguration())->setMethodName('N1')
            ->addUserLevel((new \Yana\Plugins\Configs\UserPermissionRule())->setRole('R1'))
            ->addUserLevel((new \Yana\Plugins\Configs\UserPermissionRule())->setRole('R2'));
        $this->collection[] = (new \Yana\Plugins\Configs\MethodConfiguration())->setMethodName('N2')
            ->addUserLevel((new \Yana\Plugins\Configs\UserPermissionRule()))
            ->addUserLevel((new \Yana\Plugins\Configs\UserPermissionRule())->setRole('R3'));
        $this->assertEquals(array('r1' => 'r1', 'r2' => 'r2', 'r3' => 'r3'), $this->object->getRoleNames());
    }

    /**
     * @test
     */
    public function testGetGroupNames()
    {
        $this->collection[] = (new \Yana\Plugins\Configs\MethodConfiguration())->setMethodName('N1')
            ->addUserLevel((new \Yana\Plugins\Configs\UserPermissionRule())->setGroup('G1'))
            ->addUserLevel((new \Yana\Plugins\Configs\UserPermissionRule())->setGroup('G2'));
        $this->collection[] = (new \Yana\Plugins\Configs\MethodConfiguration())->setMethodName('N2')
            ->addUserLevel((new \Yana\Plugins\Configs\UserPermissionRule()))
            ->addUserLevel((new \Yana\Plugins\Configs\UserPermissionRule())->setGroup('G3'));
        $this->assertEquals(array('g1' => 'g1', 'g2' => 'g2', 'g3' => 'g3'), $this->object->getGroupNames());
    }

    /**
     * @test
     */
    public function testGetRequirements()
    {
        $this->collection[] = (new \Yana\Plugins\Configs\MethodConfiguration())->setMethodName('N1')
            ->addUserLevel((new \Yana\Plugins\Configs\UserPermissionRule())->setGroup('G1')->setRole('R1')->setLevel(1))
            ->addUserLevel((new \Yana\Plugins\Configs\UserPermissionRule())->setGroup('G2')->setLevel(2));
        $this->collection[] = (new \Yana\Plugins\Configs\MethodConfiguration())->setMethodName('N2')
            ->addUserLevel((new \Yana\Plugins\Configs\UserPermissionRule()))
            ->addUserLevel((new \Yana\Plugins\Configs\UserPermissionRule())->setRole('R3')->setLevel(3));
        $this->collection[] = (new \Yana\Plugins\Configs\MethodConfiguration())->setMethodName('N3')
            ->addUserLevel((new \Yana\Plugins\Configs\UserPermissionRule())->setGroup('G4')->setRole('R4'));
        $action = \Yana\Security\Data\Tables\RequirementEnumeration::ACTION;
        $predefined = \Yana\Security\Data\Tables\RequirementEnumeration::IS_PREDEFINED;
        $group = \Yana\Security\Data\Tables\RequirementEnumeration::GROUP;
        $role = \Yana\Security\Data\Tables\RequirementEnumeration::ROLE;
        $level = \Yana\Security\Data\Tables\RequirementEnumeration::LEVEL;
        $expected =
            array(
                array($predefined => true, $action => 'N1', $group => 'g1', $role => 'r1', $level => 1),
                array($predefined => true, $action => 'N1', $group => 'g2',                $level => 2),
                array($predefined => true, $action => 'N2',                 $role => 'r3', $level => 3),
                array($predefined => true, $action => 'N3', $group => 'g4', $role => 'r4')
            );
        $this->assertEquals($expected, $this->object->getRequirements());
    }

}
