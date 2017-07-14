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

namespace Yana\Http\Uris;

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
     * @var \Yana\Http\Uris\Container
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Http\Uris\Container();
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
    public function testGetRequestUri()
    {
        $this->assertEquals("", $this->object->getRequestUri());
    }

    /**
     * @test
     */
    public function testGetRequestMethod()
    {
        $this->assertEquals("", $this->object->getRequestMethod());
    }

    /**
     * @test
     */
    public function testGetHttpHost()
    {
        $this->assertEquals("", $this->object->getHttpHost());
    }

    /**
     * @test
     */
    public function testGetServerAddress()
    {
        $this->assertEquals("", $this->object->getServerAddress());
    }

    /**
     * @test
     */
    public function testGetCommandLineArguments()
    {
        $this->assertEquals(array(), $this->object->getCommandLineArguments());
    }

    /**
     * @test
     */
    public function testGetPhpSelf()
    {
        $this->assertEquals("", $this->object->getPhpSelf());
    }

    /**
     * @test
     */
    public function testIsHttps()
    {
        $this->assertFalse($this->object->isHttps());
    }

    /**
     * @test
     */
    public function testGetPostVars()
    {
        $this->assertEquals(array(), $this->object->getPostVars());
    }

    /**
     * @test
     */
    public function testSetRequestUri()
    {
        $this->assertEquals('test', $this->object->setRequestUri('test')->getRequestUri());
    }

    /**
     * @test
     */
    public function testSetRequestMethod()
    {
        $this->assertEquals('test', $this->object->setRequestMethod('test')->getRequestMethod());
    }

    /**
     * @test
     */
    public function testSetHttpHost()
    {
        $this->assertEquals('test', $this->object->setHttpHost('test')->getHttpHost());
    }

    /**
     * @test
     */
    public function testSetServerAddress()
    {
        $this->assertEquals('test', $this->object->setServerAddress('test')->getServerAddress());
    }

    /**
     * @test
     */
    public function testSetCommandLineArguments()
    {
        $this->assertEquals(array('test'), $this->object->setCommandLineArguments(array('test'))->getCommandLineArguments());
    }

    /**
     * @test
     */
    public function testSetPhpSelf()
    {
        $this->assertEquals('test', $this->object->setPhpSelf('test')->getPhpSelf());
    }

    /**
     * @test
     */
    public function testSetIsHttps()
    {
        $this->assertTrue($this->object->setIsHttps(true)->isHttps());
    }

    /**
     * @test
     */
    public function testSetPostVars()
    {
        $this->assertEquals(array('test'), $this->object->setPostVars(array('test'))->getPostVars());
    }

}
