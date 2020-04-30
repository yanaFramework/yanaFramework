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
class MyRegistryLoader extends \Yana\Plugins\Loaders\AbstractRegistryLoader
{
    public function loadRegistry(string $name): \Yana\VDrive\IsRegistry
    {
        return new \Yana\VDrive\Registry($this->_getPluginDirectory()->getPath() . $name . '/' . $name . '.drive.xml');
    }

}

/**
 * @package  test
 * @ignore
 */
class AbstractRegistryLoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Loaders\MyRegistryLoader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Loaders\MyRegistryLoader(new \Yana\Files\Dir(CWD . '/resources/plugins'));
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
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function test__getNotFoundException()
    {
        $this->object->__get('test');
    }

    /**
     * @test
     */
    public function testLoadRegistriesEmpty()
    {
        $this->assertCount(0, $this->object->loadRegistries(array('no-such-file')));
    }

    /**
     * @test
     */
    public function testLoadRegistries()
    {
        $expected = new \Yana\VDrive\RegistryCollection();
        $expected['test'] = $this->object->loadRegistry('test');
        $expected['test']->read(); // or else cached values will be different
        $this->assertEquals($expected, $this->object->loadRegistries(array('test', 'test')));
        $this->assertEquals($expected, $this->object->loadRegistries(array('')));
        $this->assertEquals($expected, $this->object->loadRegistries(array('no-such-file')));
    }

    /**
     * @test
     */
    public function testUnserialize()
    {
        $object = \unserialize(\serialize($this->object));
        $this->assertEquals($object, $this->object);
    }

    /**
     * @test
     */
    public function testSerialize()
    {
        $this->assertInternalType('string', $this->object->serialize());
    }

}
