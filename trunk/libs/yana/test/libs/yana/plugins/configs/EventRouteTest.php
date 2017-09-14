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
class EventRouteTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Plugins\Configs\EventRoute
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Plugins\Configs\EventRoute();
    }

    /**
     * @test
     */
    public function testType()
    {
        $this->assertEquals(\Yana\Plugins\Configs\ReturnCodeEnumeration::SUCCESS, $this->object->getCode());
    }

    /**
     * @test
     */
    public function testSetCode()
    {
        $this->object->setCode(404);
        $this->assertEquals(404, $this->object->getCode());
    }

    /**
     * @test
     */
    public function testGetTarget()
    {
        $this->assertEquals('', $this->object->getTarget());
    }

    /**
     * @todo Implement testSetTarget().
     */
    public function testSetTarget()
    {
        $this->object->setTarget('test');
        $this->assertEquals('test', $this->object->getTarget());
    }

    /**
     * @test
     */
    public function testGetMessage()
    {
        $this->assertEquals('', $this->object->getMessage());
    }

    /**
     * @test
     */
    public function testSetMessage()
    {
        $this->object->setMessage(\Yana\Plugins\Configs\ReturnClassEnumeration::SUCCESS);
        $this->assertEquals(\Yana\Plugins\Configs\ReturnClassEnumeration::SUCCESS, $this->object->getMessage());
    }

}
