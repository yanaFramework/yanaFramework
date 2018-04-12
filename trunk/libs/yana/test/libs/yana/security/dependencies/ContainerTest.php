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

namespace Yana\Security\Dependencies;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Dependencies\Container
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        restore_error_handler();
        $this->object = new \Yana\Security\Dependencies\Container();
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
    public function testGetPlugins()
    {
        $this->assertTrue($this->object->getPlugins() instanceof \Yana\Plugins\Facade);
    }

    /**
     * @test
     */
    public function testSetPlugins()
    {
        $object = new \Yana\Plugins\Facade(new \Yana\Plugins\Dependencies\Container(new \Yana\Security\Sessions\Wrapper(), array()));
        $this->assertSame($object, $this->object->setPlugins($object)->getPlugins());
    }

    /**
     * @test
     */
    public function testGetProfileId()
    {
        $this->assertSame("", $this->object->getProfileId());
    }

    /**
     * @test
     */
    public function testSetProfileId()
    {
        $this->assertSame("Test", $this->object->setProfileId("Test")->getProfileId());
    }

    /**
     * @test
     */
    public function testGetCache()
    {
        $this->assertTrue($this->object->getCache() instanceof \Yana\Data\Adapters\IsDataAdapter);
        $this->assertTrue($this->object->getCache() instanceof \Yana\Data\Adapters\ArrayAdapter);
    }

    /**
     * @test
     */
    public function testSetCache()
    {
        $object = new \Yana\Data\Adapters\SessionAdapter(__CLASS__);
        $this->assertSame($object, $this->object->setCache($object)->getCache());
    }

    /**
     * @test
     */
    public function testGetDefaultUser()
    {
        $this->assertInternalType('array', $this->object->getDefaultUser());
    }

    /**
     * @test
     */
    public function testSetDefaultUser()
    {
        $array = array('test');
        $this->assertSame($array, $this->object->setDefaultUser($array)->getDefaultUser());
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
    public function testGetSession()
    {
        $this->assertTrue($this->object->getSession() instanceof \Yana\Security\Sessions\IsWrapper);
        $this->assertTrue($this->object->getSession() instanceof \Yana\Security\Sessions\Wrapper);
    }

    /**
     * @test
     */
    public function testSetSession()
    {
        $object = new \Yana\Security\Sessions\NullWrapper();
        $this->assertSame($object, $this->object->setSession($object)->getSession());
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
    public function testSetPasswordBehavior()
    {
        $object = new \Yana\Security\Passwords\Behaviors\StandardBehavior($this->object->getPasswordAlgorithm(), $this->object->getPasswordGenerator());
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
    public function testGetEventConfigurationsForPlugins()
    {
        $this->assertTrue($this->object->getEventConfigurationsForPlugins() instanceof \Yana\Plugins\Configs\MethodCollection);
    }

    /**
     * @test
     */
    public function testSetEventConfigurationsForPlugins()
    {
        $object = new \Yana\Plugins\Configs\MethodCollection();
        $this->assertSame($object, $this->object->setEventConfigurationsForPlugins($object)->getEventConfigurationsForPlugins());
    }

    /**
     * @test
     */
    public function testGetLogger()
    {
        $this->assertSame(\Yana\Log\LogManager::getLogger(), $this->object->getLogger());
    }

}
