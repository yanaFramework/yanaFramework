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

namespace Yana\Plugins;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class EntryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Menus\Entry
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Menus\Entry();
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
    public function testGetGroup()
    {
        $this->assertEquals('', $this->object->getGroup());
    }

    /**
     * @test
     */
    public function testSetGroup()
    {
        $this->object->setGroup('foo.bar');
        $this->assertEquals('foo.bar', $this->object->getGroup());
    }

    /**
     * @test
     */
    public function testGetTitle()
    {
        $this->assertEquals('', $this->object->getTitle());
    }

    /**
     * @test
     */
    public function testSetTitle()
    {
        $this->object->setTitle('{lang id="foo"}');
        $this->assertEquals('{lang id="foo"}', $this->object->getTitle());
    }

    /**
     * @test
     */
    public function testGetIcon()
    {
        $this->assertEquals('', $this->object->getIcon());
    }

    /**
     * @test
     */
    public function testSetIcon()
    {
        $this->object->setIcon('foo');
        $this->assertEquals('foo', $this->object->getIcon());
    }

    /**
     * @test
     */
    public function testGetSafeMode()
    {
        $this->assertNull($this->object->getSafeMode());
    }

    /**
     * @test
     */
    public function testSetSafeMode()
    {
        $this->object->setSafeMode(true);
        $this->assertTrue($this->object->getSafeMode());
        $this->object->setSafeMode(false);
        $this->assertFalse($this->object->getSafeMode());
        $this->object->setSafeMode(null);
        $this->assertNull($this->object->getSafeMode());
    }

}
