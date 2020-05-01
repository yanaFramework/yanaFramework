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

namespace Yana\Plugins\Loaders;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class RegistryLoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Loaders\RegistryLoader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Loaders\RegistryLoader(new \Yana\Files\Dir('resources/plugins'));
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
    public function testLoadRegistry()
    {
        $expected = new \Yana\VDrive\Registry('resources/plugins//test/test.drive.xml', 'resources/plugins/test/');
        $expected->read();
        $this->assertEquals($expected, $this->object->loadRegistry('test'));
    }

    /**
     * @test
     */
    public function testLoadRegistryTwice()
    {
        $expected = $this->object->loadRegistry('test');
        $this->assertSame($expected, $this->object->loadRegistry('test'), 'calling the function twice must return a cached object');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testLoadRegistryNotFoundException()
    {
        $this->object->loadRegistry('no-such-registry');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function test__getNotFoundException()
    {
        $this->object->loadRegistries(array('test'));
        $this->object->__get('test:/no-such.file');
    }

    /**
     * @test
     */
    public function test__get()
    {
        $this->object->loadRegistries(array('test'));
        $actual = $this->object->__get('test:/my.file');
        $expected = $this->object->loadRegistry('test')->getResource('test:/my.file');
        $this->assertEquals($expected, $actual);
        $this->assertEquals('nothing', $actual->read()->getContent());
    }

}
