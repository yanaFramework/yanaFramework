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
class RuleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Data\SecurityRules\Rule
     */
    protected $object1;

    /**
     * @var \Yana\Security\Data\SecurityRules\Rule
     */
    protected $object2;

    /**
     * @var \Yana\Security\Data\SecurityRules\Rule
     */
    protected $object3;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object1 = new \Yana\Security\Data\SecurityRules\Rule('Group1', 'Role1', true, 'Profile1');
        $this->object2 = new \Yana\Security\Data\SecurityRules\Rule('Group2', 'Role2', false, 'Profile2');
        $this->object3 = new \Yana\Security\Data\SecurityRules\Rule('', '', false);
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
        $this->assertSame('Group1', $this->object1->getGroup());
        $this->assertSame('Group2', $this->object2->getGroup());
        $this->assertSame('', $this->object3->getGroup());
    }

    /**
     * @test
     */
    public function testGetRole()
    {
        $this->assertSame('Role1', $this->object1->getRole());
        $this->assertSame('Role2', $this->object2->getRole());
        $this->assertSame('', $this->object3->getRole());
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

    /**
     * @test
     */
    public function testGetProfile()
    {
        $this->assertSame('Profile1', $this->object1->getProfile());
        $this->assertSame('Profile2', $this->object2->getProfile());
        $this->assertSame('', $this->object3->getProfile());
    }

    /**
     * @test
     */
    public function testGetId()
    {
        $this->assertSame(0, $this->object1->getId());
    }

    /**
     * @test
     */
    public function testSetId()
    {
        $this->assertSame(1, $this->object1->setId(1)->getId());
        $this->assertSame(2, $this->object2->setId(2)->getId());
        $this->assertSame(3, $this->object3->setId(3)->getId());
    }

}
