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
class DependencyContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Security\Passwords\Providers\DependencyContainer
     */
    protected $object;

    /**
     * @var \Yana\Security\Passwords\Providers\Entity
     */
    protected $entity;

    /**
     * @var \Yana\Security\Passwords\NullAlgorithm
     */
    protected $algorithm;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->entity = new \Yana\Security\Passwords\Providers\Entity();
        $this->algorithm = new \Yana\Security\Passwords\NullAlgorithm();
        $this->object = new \Yana\Security\Passwords\Providers\DependencyContainer($this->entity, $this->algorithm);
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
    public function testGetAuthenticationSettings()
    {
        $this->assertSame($this->entity, $this->object->getAuthenticationSettings());
    }

    /**
     * @test
     */
    public function testGetPasswordAlgorithm()
    {
        $this->assertSame($this->algorithm, $this->object->getPasswordAlgorithm());
    }

}
