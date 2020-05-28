<?php
/**
 * YANA library
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
declare(strict_types=1);

namespace Yana\Core\Dependencies;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @ignore
 * @package  test
 */
class MyHasSecurity
{

    use \Yana\Core\Dependencies\HasSecurity;

    public function getDefaultUser(): array
    {
        return array();
    }
    public function getDefaultUserRequirements(): array
    {
        return array();
    }

    public function getConnectionFactory(): \Yana\Db\IsConnectionFactory
    {
        return new \Yana\Db\ConnectionFactory(new \Yana\Db\SchemaFactory());
    }
}

/**
 * Test-case
 *
 * @package  test
 */
class HasSecurityTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Dependencies\MyHasSecurity
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        restore_error_handler();
        $this->object = new \Yana\Core\Dependencies\MyHasSecurity();
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
    public function testGetRulesChecker()
    {
        $this->assertTrue($this->object->getRulesChecker() instanceof \Yana\Security\Rules\IsChecker);
        $this->assertTrue($this->object->getRulesChecker() instanceof \Yana\Security\Rules\CacheableChecker);
    }

    /**
     * @test
     */
    public function testGetRequirementsDataReader()
    {
        $this->assertTrue($this->object->getRequirementsDataReader() instanceof \Yana\Security\Rules\Requirements\IsDataReader);
        $this->assertTrue($this->object->getRequirementsDataReader() instanceof \Yana\Security\Rules\Requirements\DataReader);
        $this->assertTrue($this->object->getRequirementsDataReader() instanceof \Yana\Security\Rules\Requirements\DefaultableDataReader);
    }

    /**
     * @test
     */
    public function testSetLoginBehavior()
    {
        $object = new \Yana\Security\Logins\NullBehavior();
        $this->assertSame($object, $this->object->setLoginBehavior($object)->getLoginBehavior());
    }

    /**
     * @test
     */
    public function testSetPasswordAlgorithmBuilder()
    {
        $object = new \Yana\Security\Passwords\Builders\Builder();
        $this->assertSame($object, $this->object->setPasswordAlgorithmBuilder($object)->getPasswordAlgorithmBuilder());
    }

    /**
     * @test
     */
    public function testSetPasswordAlgorithm()
    {
        $object = new \Yana\Security\Passwords\NullAlgorithm();
        $this->assertSame($object, $this->object->setPasswordAlgorithm($object)->getPasswordAlgorithm());
    }

    /**
     * @test
     */
    public function testSetPasswordGenerator()
    {
        $object = new \Yana\Security\Passwords\Generators\NullAlgorithm();
        $this->assertSame($object, $this->object->setPasswordGenerator($object)->getPasswordGenerator());
    }

    /**
     * @test
     */
    public function testSetAuthenticationProvider()
    {
        $object = new \Yana\Security\Passwords\Providers\Ldap("");
        $this->assertSame($object, $this->object->setAuthenticationProvider($object)->getAuthenticationProvider());
    }

    /**
     * @test
     */
    public function testSetPasswordBehavior()
    {
        $object = new \Yana\Security\Passwords\Behaviors\StandardBehavior($this->object->getPasswordAlgorithm(), $this->object->getPasswordGenerator(), $this->object->getAuthenticationProvider());
        $this->assertSame($object, $this->object->setPasswordBehavior($object)->getPasswordBehavior());
    }

    /**
     * @test
     */
    public function testGetPasswordAlgorithmBuilder()
    {
        $this->assertTrue($this->object->getPasswordAlgorithmBuilder() instanceof \Yana\Security\Passwords\Builders\IsBuilder);
        $this->assertTrue($this->object->getPasswordAlgorithmBuilder() instanceof \Yana\Security\Passwords\Builders\Builder);
    }

    /**
     * @test
     */
    public function testGetLoginBehavior()
    {
        $this->assertTrue($this->object->getLoginBehavior() instanceof \Yana\Security\Logins\IsBehavior);
        $this->assertTrue($this->object->getLoginBehavior() instanceof \Yana\Security\Logins\StandardBehavior);
    }

    /**
     * @test
     */
    public function testGetPasswordAlgorithm()
    {
        $this->assertTrue($this->object->getPasswordAlgorithm() instanceof \Yana\Security\Passwords\IsAlgorithm);
        $this->assertTrue($this->object->getPasswordAlgorithm() instanceof \Yana\Security\Passwords\BasicAlgorithm);
    }

    /**
     * @test
     */
    public function testGetPasswordGenerator()
    {
        $this->assertTrue($this->object->getPasswordGenerator() instanceof \Yana\Security\Passwords\Generators\IsAlgorithm);
        $this->assertTrue($this->object->getPasswordGenerator() instanceof \Yana\Security\Passwords\Generators\StandardAlgorithm);
    }

    /**
     * @test
     */
    public function testGetPasswordBehavior()
    {
        $this->assertTrue($this->object->getPasswordBehavior() instanceof \Yana\Security\Passwords\Behaviors\IsBehavior);
        $this->assertTrue($this->object->getPasswordBehavior() instanceof \Yana\Security\Passwords\Behaviors\StandardBehavior);
    }

    /**
     * @test
     */
    public function testGetLevelsAdapter()
    {
        $this->assertTrue($this->object->getLevelsAdapter() instanceof \Yana\Security\Data\SecurityLevels\AbstractAdapter);
        $this->assertTrue($this->object->getLevelsAdapter() instanceof \Yana\Security\Data\SecurityLevels\Adapter);
    }

    /**
     * @test
     */
    public function testGetRulesAdapter()
    {
        $this->assertTrue($this->object->getRulesAdapter() instanceof \Yana\Security\Data\SecurityRules\AbstractAdapter);
        $this->assertTrue($this->object->getRulesAdapter() instanceof \Yana\Security\Data\SecurityRules\Adapter);
    }

    /**
     * @test
     */
    public function testGetAuthenticationProvider()
    {
        $this->assertTrue(($provider = $this->object->getAuthenticationProvider()) instanceof \Yana\Security\Passwords\Providers\Standard);
        $this->assertSame($provider, $this->object->getAuthenticationProvider());
    }

}
