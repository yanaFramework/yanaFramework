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

namespace Yana\Security\Passwords\Behaviors;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * Test-case
 *
 * @package  test
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Passwords\Behaviors\Builder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Security\Passwords\Behaviors\Builder();
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
    public function testSetPasswordAlgorithmBuilder()
    {
        $passwordAlgorithmBuilder = new \Yana\Security\Passwords\Builders\Builder();
        $this->assertSame($passwordAlgorithmBuilder, $this->object->setPasswordAlgorithmBuilder($passwordAlgorithmBuilder)->getPasswordAlgorithmBuilder());
    }

    /**
     * @test
     */
    public function testSetPasswordAlgorithm()
    {
        $passwordAlgorithm = new \Yana\Security\Passwords\NullAlgorithm();
        $this->assertSame($passwordAlgorithm, $this->object->setPasswordAlgorithm($passwordAlgorithm)->getPasswordAlgorithm());
    }

    /**
     * @test
     */
    public function testSetPasswordGenerator()
    {
        $generatorAlgorithm = new \Yana\Security\Passwords\Generators\NullAlgorithm();
        $this->assertSame($generatorAlgorithm, $this->object->setPasswordGenerator($generatorAlgorithm)->getPasswordGenerator());
    }

    /**
     * @test
     */
    public function testSetAuthenticationProvider()
    {
        $provider = new \Yana\Security\Passwords\Providers\Ldap("");
        $this->assertSame($provider, $this->object->setAuthenticationProvider($provider)->getAuthenticationProvider());
    }

    /**
     * @test
     */
    public function testGetAuthenticationProvider()
    {
        $provider = new \Yana\Security\Passwords\Providers\Standard($this->object->getPasswordAlgorithm());
        $this->assertEquals($provider, $this->object->getAuthenticationProvider());
    }

    /**
     * @test
     */
    public function testGetPasswordAlgorithmBuilder()
    {
        $passwordAlgorithmBuilder = new \Yana\Security\Passwords\Builders\Builder();
        $this->assertEquals($passwordAlgorithmBuilder, $this->object->getPasswordAlgorithmBuilder());
    }

    /**
     * @test
     */
    public function testGetPasswordAlgorithm()
    {
        $passwordAlgorithm = $this->object->getPasswordAlgorithm();
        $this->assertTrue($passwordAlgorithm instanceof \Yana\Security\Passwords\IsAlgorithm, 'Instance of IsAlgorithm expected.');
    }

    /**
     * @test
     */
    public function testGetPasswordGenerator()
    {
        $generatorAlgorithm = $this->object->getPasswordGenerator();
        $this->assertTrue($generatorAlgorithm instanceof \Yana\Security\Passwords\Generators\IsAlgorithm, 'Instance of IsAlgorithm expected.');
    }

    /**
     * @test
     */
    public function test__invoke()
    {
        $this->assertTrue($this->object->__invoke() instanceof \Yana\Security\Passwords\Behaviors\IsBehavior, 'Instance of IsBehavior expected.');
    }

}
