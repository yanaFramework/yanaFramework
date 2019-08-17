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

namespace Yana\Core;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * Test implementation
 *
 * @package  test
 * @ignore
 */
class MyDecorator extends \Yana\Core\AbstractDecorator
{
    public function __construct($decoratedObject)
    {
        $this->_setDecoratedObject($decoratedObject);
    }
}

/**
 * Test implementation
 *
 * @package  test
 * @ignore
 */
class MyObject extends \Yana\Core\Object
{

    public $a = "b";
    public function c($d)
    {
        return "e";
    }
}

/**
 * @package  test
 */
class AbstractDecoratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\MyDecorator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Core\MyDecorator(new \Yana\Core\MyObject());
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
    public function test__call()
    {
        $this->assertSame("e", $this->object->c("d"));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\UndefinedMethodException
     */
    public function test__callUndefinedMethodException()
    {
        $this->object->other();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\UndefinedPropertyException
     */
    public function test__getUndefinedPropertyException()
    {
        $this->object->other;
    }

    /**
     * @test
     */
    public function test__get()
    {
        $this->assertSame("b", $this->object->a);
    }

    /**
     * @test
     */
    public function test__set()
    {
        $this->object->a = "f";
        $this->assertSame("f", $this->object->a);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\UndefinedPropertyException
     */
    public function test__setUndefinedPropertyException()
    {
        $this->object->other;
    }

}
