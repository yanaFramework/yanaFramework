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

namespace Yana\Http;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class FacadeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Http\Facade
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Http\Facade();
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
    public function testFiles()
    {
        $this->assertTrue($this->object->files() instanceof \Yana\Http\Uploads\IsUploadWrapper);
    }

    /**
     * @test
     */
    public function testAll()
    {
        $this->assertTrue($this->object->all() instanceof \Yana\Http\Requests\ValueWrapper);
    }

    /**
     * @test
     */
    public function testArgs()
    {
        $this->assertTrue($this->object->args() instanceof \Yana\Http\Requests\ValueWrapper);
    }

    /**
     * @test
     */
    public function testGet()
    {
        $this->assertTrue($this->object->get() instanceof \Yana\Http\Requests\ValueWrapper);
    }

    /**
     * @test
     */
    public function testPost()
    {
        $this->assertTrue($this->object->post() instanceof \Yana\Http\Requests\ValueWrapper);
    }

    /**
     * @test
     */
    public function testCookie()
    {
        $this->assertTrue($this->object->cookie() instanceof \Yana\Http\Requests\ValueWrapper);
    }

    /**
     * @test
     */
    public function testRequest()
    {
        $this->assertTrue($this->object->request() instanceof \Yana\Http\Requests\ValueWrapper);
    }

    /**
     * @test
     */
    public function testMethod()
    {
        $this->assertTrue($this->object->method() instanceof \Yana\Http\Requests\Method);
    }

    /**
     * @test
     */
    public function testIsAjaxRequest()
    {
        $this->assertFalse($this->object->isAjaxRequest());
    }

    /**
     * @test
     */
    public function testUri()
    {
        $this->assertTrue(is_string($this->object->uri()));
    }

}
