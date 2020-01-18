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
class BuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Passwords\Providers\Builder
     */
    protected $object;

    /**
     * @var \Yana\Security\Passwords\IsAlgorithm
     */
    protected $algorithm;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->algorithm = new \Yana\Security\Passwords\NullAlgorithm();
        \Yana\Security\Passwords\Providers\Builder::addAuthenticationProvider("test", '\Yana\Security\Passwords\Providers\Standard');
        $this->object = new \Yana\Security\Passwords\Providers\Builder($this->algorithm);
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
    public function testBuildDefaultAuthenticationProvider()
    {
        $this->assertTrue($this->object->buildDefaultAuthenticationProvider() instanceof \Yana\Security\Passwords\Providers\IsAuthenticationProvider);
        $this->assertTrue($this->object->buildDefaultAuthenticationProvider() instanceof \Yana\Security\Passwords\Providers\Standard);
    }

    /**
     * @test
     */
    public function testBuildFromUserName()
    {
        $provider = $this->object->buildFromUserName("administrator");
        $this->assertTrue($provider instanceof \Yana\Security\Passwords\Providers\Standard);
    }

    /**
     * @test
     */
    public function testBuildFromAuthenticationSettings()
    {
        $entity = new \Yana\Security\Passwords\Providers\Entity();
        $entity->setMethod("test");
        $provider = $this->object->buildFromAuthenticationSettings($entity);
        $this->assertTrue($provider instanceof \Yana\Security\Passwords\Providers\Standard);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testBuildFromAuthenticationSettingsNotFoundException()
    {
        $entity = new \Yana\Security\Passwords\Providers\Entity();
        $entity->setMethod("no-such-provider");
        $this->object->buildFromAuthenticationSettings($entity);
    }

    /**
     * @test
     */
    public function testBuildFromAuthenticationSettingsDefault()
    {
        $entity = new \Yana\Security\Passwords\Providers\Entity();
        $provider = $this->object->buildFromAuthenticationSettings($entity);
        $this->assertTrue($provider instanceof \Yana\Security\Passwords\Providers\Standard);
    }

}
