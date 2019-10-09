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
declare(strict_types=1);

namespace Yana\Log\Errors;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @ignore
 * @package  test
 */
class MyHandler extends \Yana\Log\Errors\Handler
{
    protected function _exit()
    {
        // do nothing
    }
}

/**
 * @package  test
 */
class HandlerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Log\NullLogger
     */
    protected $logger;

    /**
     * @var \Yana\Log\Errors\MyHandler
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->logger = new \Yana\Log\NullLogger();
        $formatter = new \Yana\Log\Formatter\NullFormatter();
        $this->object = new \Yana\Log\Errors\MyHandler($formatter, $this->logger);
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
    public function testHandleError()
    {
        $this->object->setErrorReportingLevel(2);
        $this->object->handleError(1, "description", "file", 2);
        $this->assertEmpty($this->logger->getLogs());
        $this->object->setErrorReportingLevel(1);
        $this->object->handleError(1, "description", "file", 2);
        $this->assertCount(1, $this->logger->getLogs());
        
    }

    /**
     * @test
     */
    public function testHandleAssertion()
    {
        $this->object->handleAssertion("file", 2, (string) 1);
        $this->assertCount(1, $this->logger->getLogs());
    }

    /**
     * @test
     */
    public function testHandleException()
    {
        $e = new \Exception('description', 1);
        $this->object->handleException($e);
        $this->assertCount(1, $this->logger->getLogs());
    }

}
