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
     * @var \Yana\Security\Data\Users\IsEntity
     */
    protected $user;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->user = new \Yana\Security\Data\Users\Entity($this->userName);
        $this->object = new \Yana\Security\Passwords\Providers\Ldap($this->server);
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
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testChangePassword()
    {
        $this->object->changePassword($this->user, "");
    }

    /**
     * @test
     */
    public function testCheckPassword()
    {
        if ($this->server === "") {
            $this->markTestSkipped("Activate this if you have an Active Directory server for testing purposes");
        }

        // The following line means that we don't verify the certificate presented by the server.
        // This is for testing purposes only, do NOT do that on a productive system.
        putenv('LDAPTLS_REQCERT=never');
        $this->assertTrue($this->object->checkPassword($this->user, $this->userPassword));
    }

}
