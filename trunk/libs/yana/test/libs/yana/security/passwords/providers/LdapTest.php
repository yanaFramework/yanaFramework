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
class LdapTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    protected $server = "";

    /**
     * @var string
     */
    protected $userName = "";

    /**
     * @var string
     */
    protected $userPassword = "";

    /**
     * @var \Yana\Security\Passwords\Providers\Ldap
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if ($this->userName === "" || $this->userName === "") {
            $this->markTestSkipped("Activate this if you have a Active Directory server for testing purposes");
        } else {
            putenv('LDAPTLS_REQCERT=never');
            $user = new \Yana\Security\Data\Users\Entity($this->userName);
            $this->object = new \Yana\Security\Passwords\Providers\Ldap($user, $this->server);
        }
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
    public function testIsAbleToChangePassword()
    {
        $this->assertFalse($this->object->isAbleToChangePassword());
    }

    /**
     * @test
     */
    public function testChangePassword()
    {
        $this->object->changePassword("", "");
    }

    /**
     * @test
     */
    public function testCheckPassword()
    {
        $this->assertTrue($this->object->checkPassword($this->userPassword));
    }

}
