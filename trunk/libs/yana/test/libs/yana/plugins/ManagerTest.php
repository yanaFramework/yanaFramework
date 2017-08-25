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
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Manager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $pluginConfigurationFile = new \Yana\Files\Text(CWD . '/resources/plugins.cfg');
        $pluginsDirectory = new \Yana\Files\Dir(CWD . '/../../../plugins/');
        \Yana\Plugins\Manager::setPath($pluginConfigurationFile, $pluginsDirectory);
        $this->object = \Yana\Plugins\Manager::getInstance();
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
    public function testIsActive()
    {
        $this->assertFalse($this->object->isActive('noplugin'));
    }

    /**
     * @test
     */
    public function testSetActive()
    {
        $this->assertFalse($this->object->isActive('helloworld'));
        $this->assertTrue($this->object->setActive('helloworld')->isActive('helloworld'));
        $this->assertTrue($this->object->setActive('helloworld', ActivityEnumeration::ACTIVE)->isActive('helloworld'));
        $this->assertFalse($this->object->setActive('helloworld', ActivityEnumeration::INACTIVE)->isActive('helloworld'));
    }

}

?>
