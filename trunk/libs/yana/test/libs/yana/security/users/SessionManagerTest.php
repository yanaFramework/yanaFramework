<?php
/**
 * PHPUnit test-case: SessionManager
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

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * SessionManager test class
 *
 * @package  test
 * @ignore
 */
class MySessionManager extends \Yana\Security\Users\SessionManager
{

    /**
     * drop security
     */
    public static function dropSecurityRules()
    {
        \Yana\Security\Users\SessionManager::$rules = array();
        \Yana\Security\Users\SessionManager::getInstance()->cache = array();
    }

}

/**
 * SessionManager test class
 *
 * @package  test
 * @ignore
 */
class MyYanaUser extends \Yana\Security\Users\User
{

    /**
     * drop security
     */
    public static function dropChanges()
    {
        foreach (\Yana\User::$instances as $user)
        {
            $user->updates = null;
        }
        \Yana\User::$instances = array();
        \Yana\User::$selectedUser = null;
    }

}

/**
 * SessionManager test-case
 *
 * @package  test
 */
class SessionManagerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Security\Users\SessionManager
     */
    protected $_sessionManager;

    /**
     * @var  \Yana\Db\FileDb
     */
    protected $_database;

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        \Yana\Db\Ddl\DDL::setDirectory(CWD . '/../../../config/db/');
        \Yana\Db\FileDb\Driver::setBaseDirectory(CWD . '/resources/db/');
        // path to plugins configuration file
        \Yana\Plugins\Manager::setPath(CWD . '/resources/plugins.cfg', CWD . '/../../../plugins/');
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        chdir(CWD . '/../../../');
        \Yana\Db\Ddl\DDL::setDirectory('config/db/');
        $schema = \Yana\Files\XDDL::getDatabase('user');
        $this->_database = new \Yana\Db\FileDb\Connection($schema);
        \Yana\Security\Users\SessionManager::setDatasource($this->_database);
        \Yana\User::setDatasource($this->_database);
        $this->_sessionManager = \Yana\Security\Users\SessionManager::getInstance();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        MyYanaUser::dropChanges();
        chdir(CWD);
    }

    /**
     * Check Login
     *
     * @test
     */
    public function testCheckLogin()
    {
        $user = \Yana\User::getInstance('administrator');
        $canLogin = $user->checkPassword('UNINITIALIZED');
        $this->assertTrue($canLogin, 'must provide login for unitialized password');

        // try to LogIn with a illegal (non exist) username and password
        // expecting false
        $user = \Yana\User::getInstance('testuser');
        $user->setPassword();
        $canLogin = $user->checkPassword('invalid_password');
        $this->assertFalse($canLogin, 'must not allow login with invalid password');

        $user->setPassword('foo');
        $canLogin = $user->checkPassword('foo');
        $this->assertTrue($canLogin, 'must allow login with correct password');
    }

    /**
     * Is logged-in
     *
     * @test
     */
    public function testIsLoggedIn()
    {
        $user = \Yana\User::getInstance('testuser');
        $this->assertFalse($user->isLoggedIn(), 'user should not be logged in');
    }

    /**
     * Login
     *
     * @test
     */
    public function testLogin()
    {
        $language = \Yana\Translations\Facade::getInstance();
        $language->addDirectory('languages');
        $language->setLocale('de');

        $user = \Yana\User::getInstance('administrator');
        $canLogin = $user->checkPassword('UNINITIALIZED');
        $this->assertTrue($canLogin, 'login as admin has failed, possible reason is, that the admin has set a password');
        $loginCount = $user->getLoginCount();
        $loginTime = $user->getLoginTime();
        $user->login();
        $this->assertTrue(\Yana\User::isLoggedIn(), 'login as admin has failed, possible reason is, that the admin has set a password');
        $this->assertEquals($user->getName(), \Yana\User::getUserName(), 'getUserName() must return name of logged-in user.');
        $this->assertTrue($user->getLoginCount() == $loginCount + 1, 'failed to increment login-count');
        $this->assertTrue($user->getLoginTime() > $loginTime, 'failed to set login-time');

        $user->logout();
        $this->assertFalse(\Yana\User::isLoggedIn(), 'Logout failed.');
    }

    /**
     * Test access functions
     *
     * @test
     */
    function testUserAccessFunctions()
    {
        $user = \Yana\User::getInstance('testuser');

        $active = $user->isActive();
        $user->setActive(!$active);
        $this->assertEquals($user->isActive(), !$active, 'unable to get isActive');

        $isExpert = $user->isExpert();
        $user->setExpert(!$isExpert);
        $this->assertEquals($user->isExpert(), !$isExpert, 'unable to get isExpert');

        $user->setLanguage('en');
        $this->assertEquals('en', $user->getLanguage(), 'unable to get language');

        $user->setMail('test@domain.tld');
        $this->assertEquals('test@domain.tld', $user->getMail(), 'unable to get mail');

        $count = $user->getFailureCount();
        $time = $user->getFailureTime();
        $user->checkPassword('invalid_password');
        $this->assertEquals($count + 1, $user->getFailureCount(), 'unable to set failure count');
        $this->assertTrue($user->getFailureTime() > $time, 'unable to set failure time');

        $user->setPassword('foo');
        $user->checkPassword('foo');
        $this->assertEquals(0, $user->getFailureCount(), 'unable to reset failure count');
        $this->assertEquals(0, $user->getFailureTime(), 'unable to reset failure time');
    }

    /**
     * Logout
     *
     * @test
     */
    public function testLogout()
    {
        $language = \Yana\Translations\Facade::getInstance();
        $language->addDirectory('languages/');
        $language->setLocale('de');

        $user = \Yana\User::getInstance('administrator');
        $user->login();
        $this->assertTrue(\Yana\User::isLoggedIn(), 'login failed');
        $this->assertTrue(isset($_SESSION['user_name']), 'for some reason there has been a login and no user_name in the global _SESSION');
        $user->logout();
        $this->assertFalse(isset($_SESSION['user_name']), 'after logout the name of the User has not been destroyed');
        $this->assertFalse(\Yana\User::isLoggedIn(), 'logout failed');
    }

    /**
     * Change Pwd
     *
     * @test
     */
    public function testChangePwd()
    {
        $user = \Yana\User::getInstance('administrator');
        $canLogin = $user->checkPassword('UNINITIALIZED');
        $this->assertTrue($canLogin, 'Login has failed, this is essntial for the following tests');
        $user = \Yana\User::getInstance('testuser');
        $user->login();

        $user->createPasswordRecoveryId();
        $recoveryId = $user->getPasswordRecoveryId();
        $recoveryTime = $user->getPasswordRecoveryTime();
        $time = $user->getPasswordChangedTime();

        $intermediatePassword = 'iKnowButYouDont';
        $user->setPassword($intermediatePassword);
        $this->assertTrue($user->checkPassword($intermediatePassword), 'changing the password has failed');
        $this->assertEquals('', $user->getPasswordRecoveryId(), 'failed to reset recovery id');
        $this->assertEquals(0, $user->getPasswordRecoveryTime(), 'failed to reset recovery time');
        $this->assertTrue($user->getPasswordChangedTime() > $time, 'failed to set password change time');
    }

    /**
     * Create user
     *
     * @test
     */
    public function testCreateUser()
    {
        $this->assertFalse(\Yana\User::isUser('nonExistingUser'), 'test user should not yet exist');
        \Yana\User::createUser('nonExistingUser', 'mail@domain.tld');
        $this->assertTrue(\Yana\User::isUser('nonExistingUser'), 'failed to create user');

        $result = \Yana\User::getInstance('nonExistingUser');
        $this->assertEquals('mail@domain.tld', $result->getMail(), 'creating a new user has failed, the expected email address is incorrect');

        $user = \Yana\User::getUserNames();
        $user = array_keys($user);
        $this->assertInternalType('array', $user, 'the value should be an array');
        $this->assertNotEquals(0, count($user), 'the values cant be equal - expected an user array');
        $this->assertTrue(in_array('ADMINISTRATOR', $user), 'the value should be match a key in array');
        $this->assertTrue(in_array('NONEXISTINGUSER', $user), 'the value should be match a key in array');
        $this->assertTrue(in_array('MANAGER', $user), 'the value should be match a key in array');
        $this->assertTrue(in_array('USER', $user), 'the value should be match a key in array');

        \Yana\User::removeUser('nonExistingUser');
        $this->assertFalse(\Yana\User::isUser('nonExistingUser'), 'failed to remove user');
    }

    /**
     * set password
     *
     * @test
     */
    public function testSetPwd()
    {
        $user = \Yana\User::getInstance('testuser');
        // try to set a password before u sign in
        $set = $user->setPassword();
        $this->assertInternalType('string', $set, 'the value must be from type string');
        $this->assertEquals(10, strlen($set), 'the expected value should have 10 digits.');
    }

    /**
     * Get security level
     *
     * @test
     */
    public function testGetSecurityLevel()
    {
        $this->_sessionManager->setSecurityLevel(70, 'TESTUsEr', 'FOO');
        // expected an integer value "70"
        $getSecurityLevel = $this->_sessionManager->getSecurityLevel('testuser', 'foo');
        $this->assertEquals(70 , (int) $getSecurityLevel, 'the security level for the user "testuser" should be "70"');

        $this->_sessionManager->setSecurityLevel(80, 'TESTuSER');
        // expected an integer value "80"
        $getSecurityLevel = $this->_sessionManager->getSecurityLevel('testUser');
        $this->assertEquals(80 , (int) $getSecurityLevel, 'the security level for the user "testuser" should be "80"');

        $this->_sessionManager->setSecurityLevel(100, 'administrator', 'TT');
        // expected an integer value "100"
        $getSecurityLevel = $this->_sessionManager->getSecurityLevel('administrator');

        $this->assertEquals(100 , (int) $getSecurityLevel, 'the security level for the user "administrator" should be "100"');

        // expected an integer value "0"
        $getSecurityLevel = $this->_sessionManager->getSecurityLevel();
        $this->assertEquals(0 , (int) $getSecurityLevel, 'the security level should be "0"');
    }

    /**
     * User exist
     *
     * @test
     */
    public function testIsUser()
    {
        // expected false for nonexist User
        $nonExist = \Yana\User::isUser('bla');
        $this->assertFalse($nonExist, 'the user is not exist');

        // expected true for an existing User
        $exist = \Yana\User::isUser('testuser');
        $this->assertTrue($exist, 'the user exists');
    }

    /**
     * CheckPremission
     *
     * check premission of function which is using only a group and / or role rights to use it.
     *
     * @test
     */
    public function testCheckPremission()
    {
        require_once(CWD.'../../../plugins/user_group/user_group.plugin.php');
        MySessionManager::dropSecurityRules();
        \Yana\Security\Users\SessionManager::addSecurityRule(array('plugin_user_group', 'checkGroupsAndRoles'));

        /**
         * check_addfoobar
         * @user        role: print, level: 50
         * @user        group: foobar, level: 60
         */

         // expected true - has rights for the expected role
         $checkAccess = $this->_sessionManager->checkPermission('default', 'check_addfoobar', 'administrator');
         $this->assertTrue($checkAccess, ' the user "administrator" has all needed rights');

         // expected true - has rights for the expected group
         $checkAccess = $this->_sessionManager->checkPermission('default', 'check_addfoobar', 'testuser1');
         $this->assertTrue($checkAccess, ' the user "testuser1" has all needed rights');

         // expected true - has rights for expected role and group
         $checkAccess = $this->_sessionManager->checkPermission('default', 'check_addfoobar', 'manager');
         $this->assertTrue($checkAccess, ' the user "manager" has all needed rights');

         // expected fales - user has no access to use this function
         $checkAccess = $this->_sessionManager->checkPermission('default', 'check_addfoobar', 'user');
         $this->assertFalse($checkAccess, 'the user "user" does not match the expected rights');

         /**
          * check_oldfoo
          * group: default, level: 60
          *
          */

         $checkAccess = $this->_sessionManager->checkPermission('ng', 'check_oldfoo', 'user');
         $this->assertTrue($checkAccess, ' the user "user" has all needed rights');

         $checkAccess = $this->_sessionManager->checkPermission('ng', 'check_oldfoo', 'administrator');
         $this->assertFalse($checkAccess, ' the user "administrator" does not match the expected rights');

         $checkAccess = $this->_sessionManager->checkPermission('default', 'check_oldfoo', 'dealer');
         $this->assertTrue($checkAccess, ' the user "dealer" has all needed rights');


        /**
         * check_presentfoo
         * group: default, role: manager, level: 40
         *
         */

        $checkAccess = $this->_sessionManager->checkPermission('default', 'check_presentfoo', 'manager');
        // expected true for user "manager" - he match the expected group and role
        $this->assertTrue($checkAccess, 'the user "manager" has all needed rights');

        $checkAccess = $this->_sessionManager->checkPermission('default', 'check_presentfoo', 'user');
        // expected false for the user "user" - does not match the expected role
        $this->assertFalse($checkAccess, 'the user "user" does not match the expected rights');

        $checkAccess = $this->_sessionManager->checkPermission('default', 'check_presentfoo', 'administrator');
        // expected false for the user "user" - does not match the expected role
        $this->assertFalse($checkAccess, 'the user "administrator" does not match the expected rights');
     }

    /**
     * CheckPremission1
     *
     * check premission of function which is using only a security level.
     *
     * @test
     */
    public function testCheckPremission1()
    {
        require_once(CWD.'../../../plugins/user/user.plugin.php');
        MySessionManager::dropSecurityRules();
        \Yana\Security\Users\SessionManager::addSecurityRule(array('plugin_user', 'checkSecurityLevel'));
        /**
         * check_baricons
         *
         * @type        primary
         * @user        level: 60
         */

        $checkAccess = $this->_sessionManager->checkPermission('bar', 'check_baricons', 'user');
        // expecting true for the user "user"
        $this->assertTrue($checkAccess, 'the user "user" has all needed rights');

        $checkAccess = $this->_sessionManager->checkPermission('bar', 'check_baricons', 'dealer');
        // expecting false for the user "dealer" - security level is too low
        $this->assertFalse($checkAccess, 'the user "dealer" does not match the expected rights');

        $checkAccess = $this->_sessionManager->checkPermission('bar', 'check_baricons', 'administrator');
        // expecting true for the user "administrator"
        $this->assertTrue($checkAccess, 'the user "administrator" does not match the expected rights');

        /**
         * check_foo
         * group: default, level: 100
         *
         */

        $checkAccess = $this->_sessionManager->checkPermission('default', 'check_foo', 'user');
        $this->assertFalse($checkAccess, 'the user "user" does not match the expected rights');

        $checkAccess = $this->_sessionManager->checkPermission('default', 'check_foo', 'manager');
        $this->assertTrue($checkAccess, 'the user "manager" does not match the expecteg group');

        /**
          * check_oldfoo
          * group: default, level: 60
          *
          */

        $checkAccess = $this->_sessionManager->checkPermission('default', 'check_oldfoo', 'user');
        $this->assertTrue($checkAccess, ' the user "user" has all needed rights');

        $checkAccess = $this->_sessionManager->checkPermission('default', 'check_oldfoo', 'dealer');
        $this->assertFalse($checkAccess, ' the user "user" has all needed rights');
    }


    /*
     * CheckPremission 2
     *
     * check premission with combine groups, roles and security level for access to some functions.
     *
     * @test
     */
    public function testCheckPremission2()
    {
        require_once(CWD.'../../../plugins/user_group/user_group.plugin.php');
        require_once(CWD.'../../../plugins/user/user.plugin.php');
        MySessionManager::dropSecurityRules();
        \Yana\Security\Users\SessionManager::addSecurityRule(array('plugin_user_group', 'checkGroupsAndRoles'));
        \Yana\Security\Users\SessionManager::addSecurityRule(array('plugin_user', 'checkSecurityLevel'));

        /**
         * check_foo
         * group: default, level: 100
         *
         */

        $checkAccess = $this->_sessionManager->checkPermission('default', 'check_foo', 'user');
        $this->assertFalse($checkAccess, 'the user "user" does not match the expected rights');

        $checkAccess = $this->_sessionManager->checkPermission('default', 'check_foo', 'employer');
        $this->assertTrue($checkAccess, 'the user "employer" has all needed rights to use this function');

        $checkAccess = $this->_sessionManager->checkPermission('default', 'check_foo', 'dealer');
        $this->assertFalse($checkAccess, 'the user "dealer" does not match the expected rights');

        $checkAccess = $this->_sessionManager->checkPermission('default', 'check_foo', 'manager');
        $this->assertTrue($checkAccess, 'the user "manager" does not match the expecteg group');


        /**
         * check_insertfoo
         * group: admin, role: default, level: 75
         * group: default, role: manager, level: 75
         *
         */

        $check = $this->_sessionManager->checkPermission('default', 'check_insertfoo', 'manager');
        // expecting true for the user "manager" - has all needed rights
        $this->assertTrue($check, 'the user "manager" has all needed rights');

        $check = $this->_sessionManager->checkPermission('default', 'check_insertfoo', 'administrator');
        // expecting true for the user "administrator" - has all needed rights
        $this->assertTrue($check, 'the user "administrator" has all needed rights');

        $check = $this->_sessionManager->checkPermission('default', 'check_insertfoo', 'user');
        // expected false for user "user" - the user does not match the expected rights
        $this->assertFalse($check, 'the user "user" does not match the expected rights');


        /**
         * check_barfoo
         * @user        group: bar, role: sales, level: 80
         *
         */
        $check = $this->_sessionManager->checkPermission('default', 'check_barfoo', 'testuser1');
        // expecting true for the user "testuser" - the testuser match the expected group , role and sec_level
        $this->assertTrue($check, 'the user "testuser" has all needed rights');

        $check = $this->_sessionManager->checkPermission('default', 'check_barfoo', 'user3');
        // expecting true for the user "testuser" - the testuser match the expected group , role and sec_level
        $this->assertTrue($check, 'the user "user2" has all needed rights');

        $check = $this->_sessionManager->checkPermission('foo', 'check_barfoo', 'user2');
        // expecting true for the user "testuser" - the testuser match the expected group , role and sec_level
        $this->assertFalse($check, 'the user "user3" does not match the expected rights');

        /**
         * check_redirectfoo
         *
         * @user        group: bar, role: sales, level: 70
         * @user        group: foobar, level: 50
         *
         */

        $checkAccess = $this->_sessionManager->checkPermission('default', 'check_redirectfoo', 'testuser1');
        // expected true for the user "testuser"
        $this->assertTrue($checkAccess, 'the user "testuser" has all needed rights');

        $checkAccess = $this->_sessionManager->checkPermission('default', 'check_redirectfoo', 'administrator');
        // expected true for the user "administrator"
        $this->assertFalse($checkAccess, 'the user "testuser" does not match the expected rights');

        $checkAccess = $this->_sessionManager->checkPermission('foo', 'check_redirectfoo', 'user2');
        // expected false - the user "user2" does not match the expected rights (security_level is too low)
        $this->assertFalse($checkAccess, 'the user "user2" does not match the expected rights');

        $checkAccess = $this->_sessionManager->checkPermission('default', 'check_redirectfoo', 'user3');
        // expected true - the user "user3" has match all needed rights
        $this->assertTrue($checkAccess, 'the user "user3" has match all needed rights');
    }

    /**
     * remove user
     *
     * @test
     */
    public function testRemoveUser()
    {
        // create a user
        \Yana\User::createUser('usertodelete','mail@domain.tld');

        // check if the user is really created
        $getUser = \Yana\User::getInstance('usertodelete');
        // valid user|sec_lvl|role
        $this->assertEquals('usertodelete', strtolower($getUser->getName()), 'the values should be equal - the expected username should be in that array');
        $this->assertEquals('mail@domain.tld', $getUser->getMail(), 'the values should be equal - the expected USER_MAIL should be match the user mail');

        // remove this user
        $this->assertTrue(\Yana\User::isUser('usertodelete'), 'expected user does not exist');
        \Yana\User::removeUser('usertodelete');
        $this->assertFalse(\Yana\User::isUser('usertodelete'), 'user was not deleted');
    }

    /**
     * removeUser with non-existing user
     *
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     * @test
     */
    function testRemoveUserInvalidArgument()
    {
        // remove an non-exist user
        \Yana\User::removeUser('nonexist');
    }

    /**
     * removeUser with empty user name
     *
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    function testRemoveUserInvalidArgument1()
    {
        \Yana\User::removeUser('');
    }

    /**
     * Serialze
     *
     * @covers SessionManager::serialize
     * @covers SessionManager::unserialize
     * @test
     */
    public function testSerialize()
    {
        $serialize = serialize($this->_sessionManager);
        $this->assertInternalType('string', $serialize, 'the value should be of type string');

        $unserialize = unserialize($serialize);
        $this->assertTrue($unserialize instanceof \Yana\Security\Users\SessionManager, 'the value should be an instance of SessionManager');
        $this->assertEquals($unserialize, $this->_sessionManager, 'assert failed , there are the same objects');
    }

}

?>