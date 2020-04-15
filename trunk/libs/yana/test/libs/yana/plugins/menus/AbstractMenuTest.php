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

namespace Yana\Plugins\Menus;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyMenu extends \Yana\Plugins\Menus\AbstractMenu
{
    public function getTextMenu(): array
    {
        return array();
    }

}

/**
 * @package  test
 */
class AbstractMenuTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Menus\MyMenu
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Menus\MyMenu();
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
    public function testSetMenuEntry()
    {
        $entry = new \Yana\Plugins\Menus\Entry();
        $entry
                ->setTitle('test')
                ->setGroup('Group');
        $this->assertSame(array('Action' => $entry), $this->object->setMenuEntry('Action', $entry)->getMenuEntries('Group'));
        $this->assertSame(array('Group' => array('Action' => $entry)), $this->object->getMenuEntries());
    }

    /**
     * @test
     */
    public function testUnsetMenuEntry()
    {
        $entry = new \Yana\Plugins\Menus\Entry();
        $entry
                ->setTitle('test');
        $this->assertTrue($this->object->setMenuEntry('Action', $entry)->unsetMenuEntry('Action'));
        $this->assertFalse($this->object->unsetMenuEntry('Action'));
        $this->assertSame(array(), $this->object->getMenuEntries());
    }

    /**
     * @test
     */
    public function testSetMenuName()
    {
        $this->assertSame('Name', $this->object->setMenuName('Id', 'Name')->getMenuName('Id'));
    }

    /**
     * @test
     */
    public function testGetMenuEntries()
    {
        $this->assertSame(array(), $this->object->getMenuEntries());
        $this->assertSame(array(), $this->object->getMenuEntries('no such entry'));
    }

    /**
     * @test
     */
    public function testGetMenuName()
    {
        $this->assertSame('Id', $this->object->getMenuName('Id'));
    }

}
