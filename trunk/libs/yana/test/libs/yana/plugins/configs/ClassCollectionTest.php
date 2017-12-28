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

namespace Yana\Plugins\Configs;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ClassCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Configs\ClassCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Configs\ClassCollection();
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
    public function testOffsetSet()
    {
        $o = new \Yana\Plugins\Configs\ClassConfiguration();
        $o->setClassName('Plugin_ClassName');
        $this->object['test'] = $o;
        $this->assertTrue($this->object['test'] instanceof \Yana\Plugins\Configs\IsClassConfiguration, 'Instance was not added.');
        $this->assertEquals($this->object['test']->getClassName(), $o->getClassName());
    }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $o = new \Yana\Plugins\Configs\ClassConfiguration();
        $o->setClassName('Plugin_ClassName');
        $this->object['test'] = $o;
        $this->assertTrue($this->object['test'] instanceof \Yana\Plugins\Configs\IsClassConfiguration, 'Instance was not added.');
        unset($this->object['test']);
        $this->assertTrue($this->object['test'] === null, 'Instance was not unset.');
    }

    /**
     * @test
     */
    public function testOffsetSetAutodetect()
    {
        $o = new \Yana\Plugins\Configs\ClassConfiguration();
        $o->setClassName('Plugin_ClassName');
        $this->object[] = $o;
        $this->assertTrue($this->object['Plugin_ClassName'] instanceof \Yana\Plugins\Configs\IsClassConfiguration, 'Instance was not added.');
        $this->assertEquals($this->object['Plugin_ClassName']->getClassName(), $o->getClassName());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $this->object[] = new \Yana\Plugins\Configs\MethodConfiguration();
    }

    /**
     * @test
     */
    public function testIsActive()
    {
        $o = new \Yana\Plugins\Configs\ClassConfiguration();
        $o->setClassName('Plugin_ClassName');
        $this->object['test'] = $o;
        $this->assertFalse($this->object->isActive('noSuchClass'));
        $this->assertFalse($this->object->isActive('test'));
        $o->setActive(\Yana\Plugins\ActivityEnumeration::INACTIVE);
        $this->assertFalse($this->object->isActive('test'));
        $o->setActive(-1);
        $this->assertFalse($this->object->isActive('test'));
        $o->setActive(\Yana\Plugins\ActivityEnumeration::ACTIVE);
        $this->assertTrue($this->object->isActive('test'));
        $o->setActive(\Yana\Plugins\ActivityEnumeration::DEFAULT_ACTIVE);
        $this->assertTrue($this->object->isActive('test'));
    }

    /**
     * @test
     */
    public function testIsActiveByDefault()
    {
        $o = new \Yana\Plugins\Configs\ClassConfiguration();
        $o->setClassName('Plugin_ClassName');
        $this->object['test'] = $o;
        $this->assertFalse($this->object->isActiveByDefault('noSuchClass'));
        $this->assertFalse($this->object->isActiveByDefault('test'));
        $o->setActive(\Yana\Plugins\ActivityEnumeration::INACTIVE);
        $this->assertFalse($this->object->isActiveByDefault('test'));
        $o->setActive(-1);
        $this->assertFalse($this->object->isActiveByDefault('test'));
        $o->setActive(\Yana\Plugins\ActivityEnumeration::ACTIVE);
        $this->assertFalse($this->object->isActiveByDefault('test'));
        $o->setActive(\Yana\Plugins\ActivityEnumeration::DEFAULT_ACTIVE);
        $this->assertTrue($this->object->isActiveByDefault('test'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testActivateNotFoundException()
    {
        $this->assertFalse($this->object->activate('noSuchClass'));
    }

    /**
     * @test
     */
    public function testActivate()
    {
        $o = new \Yana\Plugins\Configs\ClassConfiguration();
        $o->setClassName('Plugin_ClassName');
        $this->object['test'] = $o;
        $this->assertFalse($this->object->isActive('test'));
        $this->assertTrue($this->object->activate('test')->isActive('test'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testDeactivateNotFoundException()
    {
        $this->assertFalse($this->object->deactivate('noSuchClass'));
    }

    /**
     * @test
     */
    public function testDeactivate()
    {
        $o = new \Yana\Plugins\Configs\ClassConfiguration();
        $o->setClassName('Plugin_ClassName')->activate();
        $this->object['test'] = $o;
        $this->assertTrue($this->object->isActive('test'));
        $this->assertFalse($this->object->deactivate('test')->isActive('test'));
    }

}

?>