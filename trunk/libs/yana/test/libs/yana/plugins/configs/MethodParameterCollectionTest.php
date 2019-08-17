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
class MethodParameterCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Configs\MethodParameterCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Configs\MethodParameterCollection();
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
    public function testOffsetUnset()
    {
        $this->assertNull($this->object->offsetUnset('No-Such-Offset'));
        $value = new \Yana\Plugins\Configs\MethodParameter('No-Such-Offset', 'Type');
        $this->object->offsetSet(123, $value);
        $this->assertTrue($this->object->offsetExists('No-Such-Offset'));
        $this->assertNull($this->object->offsetUnset('no-such-offset'));
        $this->assertFalse($this->object->offsetExists('No-Such-Offset'));
    }

    /**
     * @test
     */
    public function testOffsetExists()
    {
        $this->assertFalse($this->object->offsetExists('No-Such-Offset'));
        $this->assertFalse($this->object->offsetExists('no-such-offset'));
        $value = new \Yana\Plugins\Configs\MethodParameter('No-Such-Offset', 'Type');
        $this->object->offsetSet(123, $value);
        $this->assertTrue($this->object->offsetExists('No-Such-Offset'));
        $this->assertTrue($this->object->offsetExists('no-such-offset'));
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $this->assertNull($this->object->offsetGet('no-such-offset'));
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $this->assertNull($this->object->offsetUnset('No-Such-Offset'));
        $value = new \Yana\Plugins\Configs\MethodParameter('No-Such-Offset', 'Type');
        $this->assertSame($value, $this->object->offsetSet(123, $value));
        $this->assertSame($value, $this->object->offsetGet('No-Such-Offset'));
        $this->assertSame($value, $this->object->offsetGet('no-such-offset'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $this->object->offsetSet(123, 456);
    }

}
