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
class SecurityLevelRuleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Rules\SecurityLevelRule
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Security\Rules\SecurityLevelRule("");
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
        $requiredDoesNotApply = new \Yana\Security\Rules\Requirements\Requirement("test", "test");
        $requiredPublic = new \Yana\Security\Rules\Requirements\Requirement("", "", 0);
        $required1 = new \Yana\Security\Rules\Requirements\Requirement("", "", 1);
        $required100 = new \Yana\Security\Rules\Requirements\Requirement("", "", 100);

        $entity = new \Yana\Security\Data\Users\Entity('USER');
        $entity->setActive(true)->setPassword('UNINITIALIZED');
        $container = new \Yana\Security\Dependencies\Container();
        $schema = \Yana\Files\XDDL::getDatabase('user');
        restore_error_handler();
        $connection = new \Yana\Db\FileDb\NullConnection($schema);
        $container->setDataConnection($connection);
        $user = new \Yana\Security\Data\Behaviors\Standard($container, $entity);
        $profileId = "default";

        $this->assertNull($this->object->__invoke($requiredDoesNotApply, $profileId, "", $user));
        $this->assertTrue($this->object->__invoke($requiredPublic, $profileId, "", $user));
        $this->assertFalse($this->object->__invoke($required1, $profileId, "", $user));
        \Yana\Security\Passwords\Providers\Builder::addAuthenticationProvider('standard', '\Yana\Security\Passwords\Providers\Standard');
        $user->login("");
        $this->assertTrue($this->object->__invoke($required1, $profileId, "", $user));
        $this->assertFalse($this->object->__invoke($required100, $profileId, "", $user));
    }

}
