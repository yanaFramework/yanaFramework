<?php
/**
 * PHPUnit test-case.
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

namespace Yana\Log;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class ExceptionLoggerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Log\ExceptionLogger
     */
    protected $object;

    /**
     * @var \Yana\Log\ExceptionLogger
     */
    protected $resultObject;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $inputContainer = new \Yana\Core\VarContainer();
        $inputContainer->setVars(array(
            'Yana\Core\Exceptions\RuntimeException' => array('h' => 'RuntimeException'),
            'Yana\Core\Exceptions\Messages\SuccessMessage' => array('h' => 'SuccessMessage'),
            'Exception' => array('p' => 'Exception')
        ));
        $this->object = new \Yana\Log\ExceptionLogger($inputContainer);
        $this->resultObject = $this->object->getMessages();
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
    public function testAddException()
    {
        $this->object->addException(new \Yana\Core\Exceptions\RuntimeException());
        $this->assertEquals('RuntimeException', $this->resultObject[0]->getHeader());
    }

    /**
     * @test
     */
    public function testAddExceptionNoEntry()
    {
        $this->object = new \Yana\Log\ExceptionLogger(new \Yana\Core\VarContainer());
        $message = __FUNCTION__;
        $data = array(1, 2);
        $text = \Yana\Util\Strings::htmlEntities($message . "; " . print_r($data, true), true);
        $exception = new \Yana\Core\Exceptions\Files\NotFoundException($message, 0);
        $exception->setData($data);
        $this->object->addException($exception);
        $resultObject = $this->object->getMessages();
        $this->assertEquals("", $resultObject[0]->getHeader());
        $this->assertEquals($text, $resultObject[0]->getText());
    }

    /**
     * @test
     */
    public function testAddExceptionUsingParents()
    {
        $this->object->addException(new \Yana\Core\Exceptions\LogicException());
        $this->assertEquals('Exception', $this->resultObject[0]->getText());
    }

    /**
     * @test
     */
    public function testAddLog()
    {
        $this->object->addLog('My Message');
        $this->assertEquals('Exception', $this->resultObject[0]->getText());
    }

    /**
     * @test
     */
    public function testAddLogSuccessMessage()
    {
        $this->object->addLog('My Message', \Yana\Log\TypeEnumeration::SUCCESS);
        $this->assertEquals('SuccessMessage', $this->resultObject[0]->getHeader());
    }

}
