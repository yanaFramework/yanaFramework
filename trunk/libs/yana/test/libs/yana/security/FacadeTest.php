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

namespace Yana\Security;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class FacadeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\MyTestFacade
     */
    protected $object;

    /**
     * @var \Yana\Security\Dependencies\Container
     */
    protected $container;

    /**
     * @var \Yana\Db\Ddl\Database
     */
    protected $schema;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->schema = \Yana\Files\XDDL::getDatabase('user');
        restore_error_handler();
        $this->container = new \Yana\Security\Dependencies\Container();
        $this->container->setDataConnection(new \Yana\Db\FileDb\NullConnection($this->schema))
                ->setEventConfigurationsForPlugins(new \Yana\Plugins\Configs\MethodCollection());
        $this->object = new \Yana\Security\Facade($this->container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->schema->setReadonly(false); // all schema instances are cached. So this needs to be reset
    }

    /**
     * @test
     */
    public function testRefreshPluginSecurityRules()
    {
        // data write has its own unit tests, so we won't test this again here and instead just check the return value
        $this->assertTrue($this->object->refreshPluginSecurityRules() instanceof \Yana\Security\Facade);
    }

    /**
     * @test
     */
    public function testAddSecurityRule()
    {
        // rules checker has its own unit tests, so we won't test this again here and instead just check the return value
        $this->assertTrue($this->object->addSecurityRule(new \Yana\Security\Rules\NullRule()) instanceof \Yana\Security\Facade);
    }

    /**
     * @test
     */
    public function testCheckRulesNoAction()
    {
        $this->assertFalse($this->object->checkRules(null, ""), 'Must return false if provided action is empty');
    }

    /**
     * @test
     */
    public function testCheckRules()
    {
        $this->assertTrue($this->object->checkRules("default", "SITEMAP"), 'action sitemap has no requirements');
        $this->assertFalse($this->object->checkRules("default", "INDEX"), 'action index requires logged in user');
    }

    /**
     * @test
     */
    public function testCheckByRequirement()
    {
        // rules checker has its own unit tests, so we won't test this again here and instead just check the return value
        $requirement = new \Yana\Security\Rules\Requirements\Requirement('group', 'role', 0);
        $this->assertFalse($this->object->checkByRequirement($requirement, 'default', 'sitemap', 'administrator'), 'Must return false if there are no rules');
        $this->object->addSecurityRule(new \Yana\Security\Rules\NullRule());
        $this->assertTrue($this->object->checkByRequirement($requirement, 'default', 'sitemap', 'administrator'), 'Must return true if rule returns true');
    }

    /**
     * @test
     */
    public function testLoadListOfGroups()
    {
        // data reader has its own unit tests, so we won't test this again here and instead just check the return value
        $this->assertInternalType('array', $this->object->loadListOfGroups());
    }

    /**
     * @test
     */
    public function testLoadListOfRoles()
    {
        // data reader has its own unit tests, so we won't test this again here and instead just check the return value
        $this->assertInternalType('array', $this->object->loadListOfRoles());
    }

    /**
     * @test
     */
    public function testLoadUser()
    {
        $this->assertTrue($this->object->loadUser('Administrator') instanceof \Yana\Security\Data\Behaviors\IsBehavior);
    }

    /**
     * @test
     */
    public function testFindUserByMail()
    {
        $this->assertTrue($this->object->findUserByMail('anymail@domain.tld') instanceof \Yana\Security\Data\Behaviors\IsBehavior);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\MissingNameException
     */
    public function testCreateUserMissingNameException()
    {
        $this->object->createUser('', 'mail@domain.tld');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\MissingMailException
     */
    public function testCreateUserMissingMailException()
    {
        $this->object->createUser('Test', '');
    }

    /**
     * @test
     * @expectedException \Yana\Db\CommitFailedException
     */
    public function testCreateUserCommitFailedException()
    {
        $this->schema->setReadonly(true);
        $this->object->createUser('Test', 'mail@domain.tld');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\AlreadyExistsException
     */
    public function testCreateUserAlreadyExistsException()
    {
        $this->object->createUser('Administrator', 'mail@domain.tld');
    }

    /**
     * @test
     */
    public function testCreateUser()
    {
        $user = $this->object->createUser('Test', 'mail@domain.tld');
        $this->assertTrue($user instanceof \Yana\Security\Data\Behaviors\IsBehavior);
        $actualUser = $this->object->loadUser('Test');
        $this->assertEquals('TEST', $user->getId());
        $this->assertEquals('mail@domain.tld', $user->getMail());
        $this->assertEquals('TEST', $actualUser->getId());
        $this->assertEquals('mail@domain.tld', $actualUser->getMail());
    }

    /**
     * @test
     */
    public function testRemoveUser()
    {
        $this->assertTrue($this->object->isExistingUserName('TestUser'));
        $this->assertFalse($this->object->removeUser('TestUser')->isExistingUserName('TestUser'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\DeleteSelfException
     */
    public function testRemoveUserDeleteSelfException()
    {
        $user = $this->object->loadUser('Manager');
        @$user->login(''); // Mute operator used because this will throw an E_NOTICE that cookies cannot be set
        $this->assertTrue($user->isLoggedIn());
        $this->object->removeUser('Manager');
    }

    /**
     * @test
     */
    public function testRemoveUserDeleteSelfAllowed()
    {
        $user = $this->object->loadUser('Manager');
        @$user->login(''); // Mute operator used because this will throw an E_NOTICE that cookies cannot be set
        $this->assertTrue($user->isLoggedIn());
        $this->assertFalse($this->object->removeUser('Manager', true)->isExistingUserName('Manager'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\User\DeleteAdminException
     */
    public function testRemoveUserDeleteAdminException()
    {
        $user = $this->object->loadUser('Manager');
        @$user->login(''); // Mute operator used because this will throw an E_NOTICE that cookies cannot be set
        $this->assertTrue($user->isLoggedIn());
        $this->object->removeUser('Administrator');
    }

    /**
     * @test
     */
    public function testIsExistingUserName()
    {
        $this->assertFalse($this->object->isExistingUserName('non-existing user'));
        $this->assertTrue($this->object->isExistingUserName('Administrator'));
    }

}
