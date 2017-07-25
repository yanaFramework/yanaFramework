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

namespace Yana\Security\Data\Behaviors;


/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Data\Behaviors\Standard
     */
    protected $object;

    /**
     * @var \Yana\Security\Data\Users\Entity
     */
    protected $entity;

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        \Yana\Db\Ddl\DDL::setDirectory(CWD. 'resources/');
        \Yana\Db\FileDb\Driver::setBaseDirectory(CWD. 'resources/db/');
        \Yana\Db\Ddl\DDL::setDirectory(CWD. 'resources/');
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->entity = new \Yana\Security\Data\Users\Entity('Test');
        $container = new \Yana\Security\Dependencies\Container();
        $container->setSession(new \Yana\Security\Sessions\NullWrapper());

        $schema = \Yana\Files\XDDL::getDatabase('user');
        restore_error_handler();
        $connection = new \Yana\Db\FileDb\NullConnection($schema);
        $container->setDataConnection($connection);

        $this->object = new \Yana\Security\Data\Behaviors\Standard($container, $this->entity);
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
    public function testSaveChanges()
    {
        $adapter = new \Yana\Data\Adapters\ArrayAdapter();
        $this->entity->setDataAdapter($adapter);
        $this->object->setMail('test@test.test')->saveChanges();
        $this->assertSame($adapter[0], $this->entity);
    }

    /**
     * @test
     */
    public function testGetId()
    {
        $this->assertSame('Test', $this->object->getId());
    }

    /**
     * @test
     */
    public function testGetSessionCheckSum()
    {
        $this->assertSame('', $this->object->getSessionCheckSum());
    }

    /**
     * @test
     */
    public function testSetLanguage()
    {
        $this->assertSame('En', $this->object->setLanguage('En')->getLanguage());
    }

    /**
     * @test
     */
    public function testGetLanguage()
    {
        $this->assertSame('', $this->object->getLanguage());
    }

    /**
     * @test
     */
    public function testGetFailureCount()
    {
        $this->assertSame(0, $this->object->getFailureCount());
    }

    /**
     * @test
     */
    public function testGetFailureTime()
    {
        $this->assertSame(0, $this->object->getFailureTime());
    }

    /**
     * @test
     */
    public function testGetLoginCount()
    {
        $this->assertSame(0, $this->object->getLoginCount());
    }

    /**
     * @test
     */
    public function testGetLoginTime()
    {
        $this->assertSame(0, $this->object->getLoginTime());
    }

    /**
     * @test
     */
    public function testGetMail()
    {
        $this->assertSame('', $this->object->getMail());
    }

    /**
     * @test
     */
    public function testSetExpert()
    {
        $this->assertSame(false, $this->object->setExpert(false)->isExpert());
        $this->assertSame(true, $this->object->setExpert(true)->isExpert());
    }

    /**
     * @test
     */
    public function testIsExpert()
    {
        $this->assertSame(false, $this->object->isExpert());
    }

    /**
     * @test
     */
    public function testSetActive()
    {
        $this->assertSame(false, $this->object->setActive(false)->isActive());
        $this->assertSame(true, $this->object->setActive(true)->isActive());
    }

    /**
     * @test
     */
    public function testIsActive()
    {
        $this->assertSame(false, $this->object->isActive());
    }

    /**
     * @test
     */
    public function testGetTimeCreated()
    {
        $this->assertSame(0, $this->object->getTimeCreated());
    }

    /**
     * @test
     */
    public function testGetPasswordChangedTime()
    {
        $this->assertSame(0, $this->object->getPasswordChangedTime());
    }

    /**
     * @test
     */
    public function testGetRecentPasswords()
    {
        $this->assertEquals(array(), $this->object->getRecentPasswords());
    }

    /**
     * @test
     */
    public function testGetPasswordRecoveryTime()
    {
        $this->assertSame(0, $this->object->getPasswordRecoveryTime());
    }

    /**
     * @test
     */
    public function testGeneratePasswordRecoveryId()
    {
        $recoveryId = $this->object->generatePasswordRecoveryId();
        $this->assertTrue($this->object->checkRecoveryId($recoveryId));
        $this->assertGreaterThan(0, $this->object->getPasswordRecoveryTime());
        $this->assertLessThanOrEqual(\time(), $this->object->getPasswordRecoveryTime());
    }

    /**
     * @test
     */
    public function testChangePassword()
    {
        $this->assertFalse($this->object->changePassword('old Password')->checkPassword('new Password'));
        $this->assertTrue($this->object->changePassword('new Password')->checkPassword('new Password'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Mails\InvalidMailException
     */
    public function testSetMailInvalidMailException()
    {
        $this->object->setMail('invalid');
    }

    /**
     * @test
     */
    public function testSetMail()
    {
        $this->assertSame('Mail@domain.tld', $this->object->setMail('Mail@domain.tld')->getMail());
    }

    /**
     * @test
     */
    public function testCheckPassword()
    {
        $this->assertFalse($this->object->checkPassword('')); // no password set yet
        $this->assertFalse($this->object->checkPassword('password')); // no password set yet
        $this->assertFalse($this->object->changePassword('new Password')->checkPassword('password'));
        $this->assertTrue($this->object->checkPassword('new Password'));
    }

    /**
     * @test
     */
    public function testCheckPasswordUninitialized()
    {
        $this->assertFalse($this->object->checkPassword('')); // no password set yet
        $this->entity->setPassword('UNINITIALIZED');
        $this->assertTrue($this->object->checkPassword('')); // no password set yet
        $this->assertTrue($this->object->checkPassword('password')); // no password set yet
        $this->assertFalse($this->object->changePassword('new Password')->checkPassword('password'));
    }

    /**
     * @test
     */
    public function testCheckRecoveryId()
    {
        $this->assertFalse($this->object->checkRecoveryId('invalid'));
        $recoveryId = $this->object->generatePasswordRecoveryId();
        $this->assertFalse($this->object->checkRecoveryId('invalid'));
        $this->assertTrue($this->object->checkRecoveryId($recoveryId));
    }

    /**
     * @test
     */
    public function testGenerateRandomPassword()
    {
        $password = $this->object->generateRandomPassword();
        $this->assertInternalType('string', $password);
        $this->assertNotEmpty($password);
        $this->assertTrue($this->object->checkPassword($password));
    }

    /**
     * @test
     */
    public function testGetSecurityGroupsAndRoles()
    {
        $this->assertEquals(new \Yana\Security\Data\SecurityRules\Collection(), $this->object->getSecurityGroupsAndRoles(''));
    }

    /**
     * @test
     */
    public function testGetSecurityLevel()
    {
        $this->assertSame(0, $this->object->getSecurityLevel(''));
    }

    /**
     * @test
     */
    public function testGetAllSecurityLevels()
    {
        $expected = new \Yana\Security\Data\SecurityLevels\Collection();
        $expected[] = new \Yana\Security\Data\SecurityLevels\Level(-1, 0, true);
        $this->assertEquals($expected, $this->object->getAllSecurityLevels());
    }

    /**
     * @test
     */
    public function testLogin()
    {
        $this->entity->setActive(true);
        $this->assertFalse($this->object->isLoggedIn());
        $this->assertTrue($this->object->changePassword('test')->login('test')->isLoggedIn());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Security\InvalidLoginException
     */
    public function testLoginInvalidLoginException()
    {
        $this->entity->setActive(false);
        $this->object->changePassword('test')->login('test');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Security\PermissionDeniedException
     */
    public function testLoginPermissionDeniedException()
    {
        try {
            $this->object->login('test');
        } catch (\Yana\Core\Exceptions\Security\InvalidLoginException $e) {
            // ignore
        }
        try {
            $this->object->login('test');
        } catch (\Yana\Core\Exceptions\Security\InvalidLoginException $e) {
            // ignore
        }
        try {
            $this->object->login('test');
        } catch (\Yana\Core\Exceptions\Security\InvalidLoginException $e) {
            // ignore
        }
        try {
            $this->object->login('test');
        } catch (\Yana\Core\Exceptions\Security\InvalidLoginException $e) {
            // ignore
        }
        try {
            $this->object->login('test');
        } catch (\Yana\Core\Exceptions\Security\InvalidLoginException $e) {
            // ignore
        }
        $this->object->login('test');
    }

    /**
     * @test
     */
    public function testLogout()
    {
        $this->entity->setActive(true);
        $this->assertFalse($this->object->isLoggedIn());
        $this->assertTrue($this->object->changePassword('test')->login('test')->isLoggedIn());
        $this->assertFalse($this->object->logout()->isLoggedIn());
    }

    /**
     * @test
     */
    public function testIsLoggedIn()
    {
        $this->assertFalse($this->object->isLoggedIn());
    }

}
