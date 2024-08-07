<?php
/**
 * YANA library
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

namespace Yana\Core\Exceptions;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ExceptionTest extends \Yana\Core\Exceptions\AbstractException
{

    public function __construct($message = "", $code = E_USER_NOTICE, ?\Exception $previous = null)
    {
        self::$queue = array();
        parent::__construct($message, $code, $previous);
    }

}

/**
 * @package  test
 */
class AbstractExceptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Exceptions\ExceptionTest
     */
    protected $object;

    /**
     * @var string
     */
    protected $message = "Testmessage ä`æ";

    /**
     * @var int
     */
    protected $code = E_USER_ERROR;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Core\Exceptions\ExceptionTest($this->message, $this->code);
        $container = new \Yana\Core\Dependencies\ExceptionContainer(new \Yana\Translations\NullFacade());
        \Yana\Core\Exceptions\ExceptionTest::setDependencyContainer($container);
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
    public function testGetMessages()
    {
        $this->assertEquals(array($this->object), $this->object->getMessages());
    }

    /**
     * @test
     */
    public function testGetMessage()
    {
        $this->assertEquals($this->message, $this->object->getMessage());
    }

    /**
     * @test
     */
    public function testCountMessages()
    {
        $this->assertEquals(1, $this->object->countMessages());
    }

    /**
     * @test
     */
    public function testGetData()
    {
        $this->assertEquals(array('MESSAGE' => $this->message), $this->object->getData());
    }

    /**
     * @test
     */
    public function testSetData()
    {
        $this->assertEquals(array('ACTION' => 'test'), $this->object->setData(array('ACTION' => 'test'))->getData());
    }

    /**
     * @test
     */
    public function test__toString()
    {
        $this->assertEquals($this->object->getMessage(), (string) $this->object);
    }

    /**
     * @test
     */
    public function testGetHeader()
    {
        $this->assertEquals("", $this->object->getHeader());
    }

    /**
     * @test
     */
    public function testGetText()
    {
        $this->assertEquals($this->message, $this->object->getText());
    }

}
