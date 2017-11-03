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
 */
class LogManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Log\LoggerCollection 
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Log\LoggerCollection();
        \Yana\Log\LogManager::setLoggers($this->object);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        \Yana\Log\LogManager::setLoggers(new \Yana\Log\LoggerCollection());
    }

    /**
     * @test
     */
    public function testGetLogger()
    {
        $this->assertSame($this->object, \Yana\Log\LogManager::getLogger());
    }

    /**
     * @test
     */
    public function testAttachLogger()
    {
        $this->assertCount(0, $this->object);
        $myLogger = new \Yana\Log\NullLogger();
        \Yana\Log\LogManager::attachLogger($myLogger);
        $this->assertCount(1, $this->object);
        $this->assertSame($myLogger, $this->object[0]);
    }

    /**
     * @test
     */
    public function testSetLoggers()
    {
        \Yana\Log\LogManager::setLoggers($this->object);
        $this->assertSame($this->object, \Yana\Log\LogManager::getLogger());
        $collection = new \Yana\Log\LoggerCollection();
        \Yana\Log\LogManager::setLoggers($collection);
        $this->assertNotSame($this->object, \Yana\Log\LogManager::getLogger());
        $this->assertSame($collection, \Yana\Log\LogManager::getLogger());
    }

}
