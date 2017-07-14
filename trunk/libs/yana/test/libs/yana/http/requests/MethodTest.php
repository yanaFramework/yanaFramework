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
class MethodTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Http\Requests\Method
     */
    protected $get;

    /**
     * @var \Yana\Http\Requests\Method
     */
    protected $post;

    /**
     * @var \Yana\Http\Requests\Method
     */
    protected $put;

    /**
     * @var \Yana\Http\Requests\Method
     */
    protected $delete;

    /**
     * @var \Yana\Http\Requests\Method
     */
    protected $cli;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->get = new \Yana\Http\Requests\Method(\Yana\Http\Requests\MethodEnumeration::GET);
        $this->post = new \Yana\Http\Requests\Method(\Yana\Http\Requests\MethodEnumeration::POST);
        $this->put = new \Yana\Http\Requests\Method(\Yana\Http\Requests\MethodEnumeration::PUT);
        $this->delete = new \Yana\Http\Requests\Method(\Yana\Http\Requests\MethodEnumeration::DELETE);
        $this->cli = new \Yana\Http\Requests\Method(\Yana\Http\Requests\MethodEnumeration::CLI);
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
    public function testIsPost()
    {
        $this->assertFalse($this->get->isPost());
        $this->assertTrue($this->post->isPost());
        $this->assertFalse($this->put->isPost());
        $this->assertFalse($this->delete->isPost());
        $this->assertFalse($this->cli->isPost());
    }

    /**
     * @test
     */
    public function testIsGet()
    {
        $this->assertTrue($this->get->isGet());
        $this->assertFalse($this->post->isGet());
        $this->assertFalse($this->put->isGet());
        $this->assertFalse($this->delete->isGet());
        $this->assertFalse($this->cli->isGet());
    }

    /**
     * @test
     */
    public function testIsPut()
    {
        $this->assertFalse($this->get->isPut());
        $this->assertFalse($this->post->isPut());
        $this->assertTrue($this->put->isPut());
        $this->assertFalse($this->delete->isPut());
        $this->assertFalse($this->cli->isPut());
    }

    /**
     * @test
     */
    public function testIsDelete()
    {
        $this->assertFalse($this->get->isDelete());
        $this->assertFalse($this->post->isDelete());
        $this->assertFalse($this->put->isDelete());
        $this->assertTrue($this->delete->isDelete());
        $this->assertFalse($this->cli->isDelete());
    }

    /**
     * @test
     */
    public function testIsCommandLine()
    {
        $this->assertFalse($this->get->isCommandLine());
        $this->assertFalse($this->post->isCommandLine());
        $this->assertFalse($this->put->isCommandLine());
        $this->assertFalse($this->delete->isCommandLine());
        $this->assertTrue($this->cli->isCommandLine());
    }

}
