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

namespace Yana\Security\Data\SecurityRules;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Data\SecurityRules\IsCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Security\Data\SecurityRules\Collection();
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
    public function testOffsetSet()
    {
        $rule = new \Yana\Security\Data\SecurityRules\Rule('group', 'role', true);
        $this->object->offsetSet(null, $rule);
        $this->assertSame($rule, $this->object[0]);
        $this->assertEquals(1, count($this->object));
        $this->object->offsetSet(null, $rule);
        $this->assertEquals(2, count($this->object));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $this->object->offsetSet(null, new \Yana\Core\Object());
    }

    /**
     * @test
     */
    public function testHasGroupAndRole()
    {
        $this->assertFalse($this->object->hasGroupAndRole('group', 'role'));
        $this->assertFalse($this->object->hasGroupAndRole('no-such-group', 'no-such-role'));

        $this->object[] = new \Yana\Security\Data\SecurityRules\Rule('group', 'other-role', true);
        $this->object[] = new \Yana\Security\Data\SecurityRules\Rule('other-group', 'role', true);
        $this->assertFalse($this->object->hasGroupAndRole('GrOuP', 'RoLe'));
        $this->assertFalse($this->object->hasGroupAndRole('no-such-group', 'no-such-role'));

        $this->object[] = new \Yana\Security\Data\SecurityRules\Rule('group', '', true);
        $this->object[] = new \Yana\Security\Data\SecurityRules\Rule('', 'role', true);
        $this->assertFalse($this->object->hasGroupAndRole('group', 'role'));
        $this->assertFalse($this->object->hasGroupAndRole('no-such-group', 'no-such-role'));

        $this->object[] = new \Yana\Security\Data\SecurityRules\Rule('Group', 'Role', true);

        $this->assertTrue($this->object->hasGroupAndRole('GrOuP', 'RoLe'));
        $this->assertFalse($this->object->hasGroupAndRole('no-such-group', 'no-such-role'));

        $this->object->setItems();
        $this->assertFalse($this->object->hasGroupAndRole('GrOuP', 'RoLe'));
    }

    /**
     * @test
     */
    public function testHasGroup()
    {
        $this->assertFalse($this->object->hasGroup('group'));
        $this->assertFalse($this->object->hasGroup('no-such-group'));

        $rule = new \Yana\Security\Data\SecurityRules\Rule('group', 'role', true);
        $this->object->offsetSet(null, $rule);

        $this->assertTrue($this->object->hasGroup('GrOuP'));
        $this->assertFalse($this->object->hasGroup('no-such-group'));
        $this->object->offsetUnset(0);
        $this->assertFalse($this->object->hasGroup('GrOuP'));
    }

    /**
     * @test
     */
    public function testHasRole()
    {
        $this->assertFalse($this->object->hasRole('role'));
        $this->assertFalse($this->object->hasRole('no-such-role'));

        $rule = new \Yana\Security\Data\SecurityRules\Rule('group', 'role', true);
        $this->object->offsetSet(null, $rule);

        $this->assertTrue($this->object->hasRole('RoLe'));
        $this->assertFalse($this->object->hasRole('no-such-role'));
        $this->object->offsetUnset(0);
        $this->assertFalse($this->object->hasRole('RoLe'));
    }

}
