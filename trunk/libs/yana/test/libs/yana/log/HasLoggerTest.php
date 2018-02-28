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
 * @package test
 * @ignore
 */
class MyLogger
{
    use \Yana\Log\HasLogger;
}

/**
 * @package test
 */
class HasLoggerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Log\MyLogLevel 
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Log\MyLogger();
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
    public function testAttachLogger()
    {
        $logger = new \Yana\Log\NullLogger();
        $loggerCollection = new \Yana\Log\LoggerCollection();
        $loggerCollection[] = $logger;
        $this->assertEquals($loggerCollection, $this->object->attachLogger($logger)->getLogger());
    }

    /**
     * @test
     */
    public function testGetLogger()
    {
        $this->assertEquals(new \Yana\Log\LoggerCollection(), $this->object->getLogger());
    }

}
