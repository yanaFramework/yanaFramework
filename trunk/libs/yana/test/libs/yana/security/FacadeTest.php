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
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        \Yana\Db\Ddl\DDL::setDirectory(CWD. 'resources/');
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        \Yana\Db\FileDb\Driver::setBaseDirectory(CWD. 'resources/db/');
        \Yana\Db\Ddl\DDL::setDirectory(CWD. 'resources/');
        $schema = \Yana\Files\XDDL::getDatabase('user');
        restore_error_handler();
        $this->container = new \Yana\Security\Dependencies\Container();
        $this->container->setDataConnection(new \Yana\Db\FileDb\NullConnection($schema))
                ->setEventConfigurationsForPlugins(new \Yana\Plugins\Configs\MethodCollection());
        $this->object = new \Yana\Security\Facade($this->container);
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
    public function testRefreshPluginSecurityRules()
    {
        $this->object->refreshPluginSecurityRules();
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
    public function testCheckRules()
    {
        $this->object->checkRules();
    }

    /**
     * @test
     */
    public function testCheckByRequirement()
    {
        $requirement = new \Yana\Security\Rules\Requirements\Requirement('group', 'role', 0);
        $this->object->checkByRequirement($requirement, 'default', 'sitemap', 'administrator');
    }

    /**
     * @test
     */
    public function testLoadListOfGroups()
    {
        $this->object->loadListOfGroups();
    }

    /**
     * @test
     */
    public function testLoadListOfRoles()
    {
        $this->object->loadListOfRoles();
    }

    /**
     * @test
     */
    public function testLoadUser()
    {
        $this->object->loadUser('Administrator');
    }

    /**
     * @test
     */
    public function testCreateUser()
    {
        $this->object->createUser('Test', 'mail@domain.tld');
    }

    /**
     * @test
     */
    public function testRemoveUser()
    {
        $this->object->removeUser('TestUser');
    }

    /**
     * @test
     */
    public function testIsExistingUserName()
    {
        $this->object->isExistingUserName('Administrator');
    }

}
