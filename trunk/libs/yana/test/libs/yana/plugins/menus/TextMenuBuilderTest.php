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

namespace Yana\Plugins;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class TextMenuBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Dependencies\Container
     */
    protected $container;

    /**
     * @var \Yana\Plugins\Menus\TextMenuBuilder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $configurationFactory = new \Yana\ConfigurationFactory();
        $configuration = $configurationFactory->loadConfiguration(CWD . 'resources/system.config.xml');
        $configuration->configdrive = YANA_INSTALL_DIR . 'config/system.drive.xml';
        $this->container = new \Yana\Core\Dependencies\Container($configuration);
        $container = new \Yana\Plugins\Dependencies\MenuContainer($this->container);
        $this->object = new \Yana\Plugins\Menus\TextMenuBuilder($container);
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
    public function testTranslateMenuName()
    {
        $this->assertSame('start', $this->object->translateMenuName('start'));
    }

    /**
     * @test
     */
    public function testGetTextMenuEmpty()
    {
        $menuConfiguration = new \Yana\Plugins\Menus\Menu($this->object);
        $this->assertSame(array(), $this->object->getTextMenu($menuConfiguration));
    }

    /**
     * @test
     */
    public function testGetTextMenu()
    {
        $menuConfiguration = new \Yana\Plugins\Menus\Menu($this->object);
        $menuEntry = new \Yana\Plugins\Menus\Entry();
        $menuEntry->setGroup('start');
        $menuEntry->setTitle('Title 1');
        $menuConfiguration->setMenuEntry('action1', $menuEntry);
        $menuEntry2 = new \Yana\Plugins\Menus\Entry();
        $menuEntry2->setGroup('start');
        $menuEntry2->setTitle('Title 2');
        $menuConfiguration->setMenuEntry('action2', $menuEntry2);
        $menuEntry3 = new \Yana\Plugins\Menus\Entry();
        $menuEntry3->setGroup('start');
        $menuEntry3->setTitle('Invisible');
        $menuEntry3->setSafeMode(false);
        $menuConfiguration->setMenuEntry('hidden', $menuEntry3);
        $menuConfiguration->setMenuName('L2.test', 'My Menu');
        $menuEntry4 = new \Yana\Plugins\Menus\Entry();
        $menuEntry4->setGroup('L2.test');
        $menuEntry4->setTitle('Title 3');
        $menuConfiguration->setMenuEntry('action3', $menuEntry4);
        $_SERVER['PHP_SELF'] = "";
        $expected =  array(
            'start' => array(
                'http://?id=default&action=action1' => 'Title 1',
                'http://?id=default&action=action2' => 'Title 2'
            ),
            'L2' => array(
                'My Menu' => array(
                    'http://?id=default&action=action3' => 'Title 3'
                )
            )
        );
        $this->assertSame($expected, $this->object->getTextMenu($menuConfiguration));
    }

}
