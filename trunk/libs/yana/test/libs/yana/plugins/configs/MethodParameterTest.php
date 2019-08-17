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
declare(strict_types=1);

namespace Yana\Plugins\Configs;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package test
 * @ignore
 */
class MethodParameterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Configs\MethodParameter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Configs\MethodParameter('Name', 'Type');
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
    public function testGetType()
    {
        $this->assertSame('Type', $this->object->getType());
    }

    /**
     * @test
     */
    public function testGetName()
    {
        $this->assertSame('Name', $this->object->getName());
    }

    /**
     * @test
     */
    public function testIsDefaultValueAvailable()
    {
        $this->assertFalse($this->object->isDefaultValueAvailable());
        $this->assertTrue($this->object->setDefault('Default')->isDefaultValueAvailable());
    }

    /**
     * @test
     */
    public function testGetDefault()
    {
        $this->assertNull($this->object->getDefault());
    }

    /**
     * @test
     */
    public function testSetDefault()
    {
        $this->assertSame('Default', $this->object->setDefault('Default')->getDefault());
    }

}
