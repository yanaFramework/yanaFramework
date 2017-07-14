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

namespace Yana\Http\Requests;

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
     * @var \Yana\Http\Requests\Container
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new \Yana\Http\Requests\Container();
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
    public function testGetRequest()
    {
        $this->assertEquals(new \Yana\Http\Requests\ValueWrapper(), $this->_object->getRequest());
    }

    /**
     * @test
     */
    public function testGetGet()
    {
        $this->assertEquals(new \Yana\Http\Requests\ValueWrapper(), $this->_object->getGet());
    }

    /**
     * @test
     */
    public function testGetPost()
    {
        $this->assertEquals(new \Yana\Http\Requests\ValueWrapper(), $this->_object->getPost());
    }

    /**
     * @test
     */
    public function testGetCookie()
    {
        $this->assertEquals(new \Yana\Http\Requests\ValueWrapper(), $this->_object->getCookie());
    }

    /**
     * @test
     */
    public function testGetArguments()
    {
        $this->assertEquals(new \Yana\Http\Requests\ValueWrapper(), $this->_object->getArguments());
    }

    /**
     * @test
     */
    public function testGetFiles()
    {
        $this->assertEquals(array(), $this->_object->getFiles());
    }

    /**
     * @test
     */
    public function testGetMethod()
    {
        $this->assertTrue($this->_object->getMethod() instanceof \Yana\Http\Requests\IsMethod);
        $this->assertEquals(new \Yana\Http\Requests\Method(""), $this->_object->getMethod());
    }

    /**
     * @test
     */
    public function testSetRequest()
    {
        $request = new \Yana\Http\Requests\ValueWrapper(array('1', '2', '3', array('a')));
        $this->assertEquals($request, $this->_object->setRequest($request)->getRequest());
    }

    /**
     * @test
     */
    public function testSetGet()
    {
        $request = new \Yana\Http\Requests\ValueWrapper(array('1', '2', '3', array('a')));
        $this->assertEquals($request, $this->_object->setGet($request)->getGet());
    }

    /**
     * @test
     */
    public function testSetPost()
    {
        $request = new \Yana\Http\Requests\ValueWrapper(array('1', '2', '3', array('a')));
        $this->assertEquals($request, $this->_object->setPost($request)->getPost());
    }

    /**
     * @test
     */
    public function testSetCookie()
    {
        $request = new \Yana\Http\Requests\ValueWrapper(array('1', '2', '3', array('a')));
        $this->assertEquals($request, $this->_object->setCookie($request)->getCookie());
    }

    /**
     * @test
     */
    public function testSetArguments()
    {
        $request = new \Yana\Http\Requests\ValueWrapper(array('1', '2', '3', array('a')));
        $this->assertEquals($request, $this->_object->setArguments($request)->getArguments());
    }

    /**
     * @test
     */
    public function testSetFiles()
    {
        $request = array('1', '2', '3', array('a'));
        $this->assertEquals($request, $this->_object->setFiles($request)->getFiles());
    }

    /**
     * @test
     */
    public function testSetMethod()
    {
        $method = new \Yana\Http\Requests\Method(\Yana\Http\Requests\MethodEnumeration::GET);
        $this->assertEquals($method, $this->_object->setMethod($method)->getMethod());
    }

}
