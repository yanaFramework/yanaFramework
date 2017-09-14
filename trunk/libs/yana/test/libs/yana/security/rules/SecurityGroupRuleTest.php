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

namespace Yana\Security\Rules;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class SecurityGroupRuleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Rules\SecurityGroupRule
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Security\Rules\SecurityGroupRule("default");
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
     * @runInSeparateProcess
     */
    public function test__invoke()
    {
        $requiredPublic = new \Yana\Security\Rules\Requirements\Requirement("", "", 0);
        $requiredGroup = new \Yana\Security\Rules\Requirements\Requirement("DEFAULT", "", 0);
        $requiredRole = new \Yana\Security\Rules\Requirements\Requirement("", "DEFAULT", 0);
        $requiredBoth = new \Yana\Security\Rules\Requirements\Requirement("DEFAULT", "DEFAULT", 0);

        $entity = new \Yana\Security\Data\Users\Entity('USER');
        $entity->setActive(true)->setPassword('UNINITIALIZED');
        $container = new \Yana\Security\Dependencies\Container(\Yana\Plugins\Manager::getInstance());
        $schema = \Yana\Files\XDDL::getDatabase('user');
        restore_error_handler();
        $connection = new \Yana\Db\FileDb\NullConnection($schema);
        $container->setDataConnection($connection);
        $user = new \Yana\Security\Data\Behaviors\Standard($container, $entity);

        $this->assertNull($this->object->__invoke($requiredPublic, "default", "", $user));
        $this->assertFalse($this->object->__invoke($requiredGroup, "default", "", $user));
        $this->assertFalse($this->object->__invoke($requiredGroup, "PRIVATE", "", $user));
        $user->login("");
        $this->assertTrue($this->object->__invoke($requiredGroup, "PRIVATE", "", $user));
        $this->assertFalse($this->object->__invoke($requiredRole, "default", "", $user));
        $this->assertTrue($this->object->__invoke($requiredRole, "PRIVATE", "", $user));
        $this->assertFalse($this->object->__invoke($requiredBoth, "default", "", $user));
        $this->assertTrue($this->object->__invoke($requiredBoth, "PRIVATE", "", $user));
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function test__invokeAdmin()
    {
        $requiredAdmin = new \Yana\Security\Rules\Requirements\Requirement("ADMIN", "ADMIN", 0);


        $entity = new \Yana\Security\Data\Users\Entity('ADMINISTRATOR');
        $entity->setActive(true)->setPassword('UNINITIALIZED');
        $container = new \Yana\Security\Dependencies\Container(\Yana\Plugins\Manager::getInstance());
        $schema = \Yana\Files\XDDL::getDatabase('user');
        restore_error_handler();
        $connection = new \Yana\Db\FileDb\NullConnection($schema);
        $container->setDataConnection($connection);
        $user = new \Yana\Security\Data\Behaviors\Standard($container, $entity);
        $user->login("");

        $this->assertTrue($this->object->__invoke($requiredAdmin, "default", "", $user));
        $this->assertTrue($this->object->__invoke($requiredAdmin, "PRIVATE", "", $user));
    }

}
