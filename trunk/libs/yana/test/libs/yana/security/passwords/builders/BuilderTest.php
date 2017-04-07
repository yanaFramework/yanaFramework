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

namespace Yana\Security\Passwords\Builders;

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
     * @var \Yana\Security\Passwords\Builders\Builder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Security\Passwords\Builders\Builder();
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
    public function testAdd()
    {
        $algorithm = \Yana\Security\Passwords\Builders\Enumeration::BASIC;
        $this->assertTrue($this->object->add($algorithm) instanceof \Yana\Security\Passwords\Builders\IsBuilder);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function testAddNotImplementedException()
    {
        $this->object->add('invalid');
    }

    /**
     * @test
     */
    public function test__invoke()
    {
        $this->object->add(\Yana\Security\Passwords\Builders\Enumeration::BASIC);
        $this->object->add(\Yana\Security\Passwords\Builders\Enumeration::SHA256);
        $this->object->add(\Yana\Security\Passwords\Builders\Enumeration::SHA512);
        $algorithm = $this->object->__invoke();
        $this->assertTrue($algorithm instanceof \Yana\Security\Passwords\IsAlgorithm);
        $this->assertTrue($algorithm instanceof \Yana\Security\Passwords\Sha512Algorithm);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotImplementedException
     */
    public function test__invokeNotImplementedException()
    {
        $this->object->__invoke();
    }

}
