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
namespace Yana\Plugins\Dependencies;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Dependencies\Container
     */
    protected $object;

    /**
     * @var \Yana\Security\Sessions\NullWrapper
     */
    protected $session;

    /**
     * @var array
     */
    protected $defaultEvent;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->session = new \Yana\Security\Sessions\NullWrapper();
        $this->defaultEvent = array('test');
        $this->object = new \Yana\Plugins\Dependencies\Container($this->session, $this->defaultEvent);
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
    public function testGetDefaultEvent()
    {
        $this->assertSame($this->defaultEvent, $this->object->getDefaultEvent());
    }

    /**
     * @test
     */
    public function testGetSession()
    {
        $this->assertSame($this->session, $this->object->getSession());
    }

    /**
     * @test
     */
    public function testGetPluginAdapter()
    {
        $this->assertTrue($this->object->getPluginAdapter() instanceof \Yana\Plugins\Data\IsAdapter);
    }

    /**
     * @test
     */
    public function testSetPluginAdapter()
    {
        $adapter = new \Yana\Plugins\Data\Adapter(new \Yana\Db\NullConnection());
        $this->assertSame($adapter, $this->object->setPluginAdapter($adapter)->getPluginAdapter());
    }

}
