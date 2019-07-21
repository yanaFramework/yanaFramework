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
class DbLoggerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Log\DbLogger
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Log\DbLogger(new \Yana\Db\NullConnection());
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
    public function testAddLog()
    {
        $this->object->addLog("Test", -1, array(1, 2, 3));
        $messages = $this->object->getMessages();
        $this->assertArrayHasKey("log_action", $messages[0]);
        $this->assertArrayHasKey("log_message", $messages[0]);
        $this->assertArrayHasKey("log_data", $messages[0]);
        $this->assertSame("Test", $messages[0]["log_message"]);
        $this->assertSame(array(1, 2, 3), $messages[0]["log_data"]);
    }

    /**
     * @test
     */
    public function testSetMaxNumerOfEntries()
    {
        $this->assertSame(123, $this->object->setMaxNumerOfEntries(123)->getMaxNumerOfEntries());
    }

    /**
     * @test
     */
    public function testGetMaxNumerOfEntries()
    {
        $this->assertSame(0, $this->object->getMaxNumerOfEntries());
    }

    /**
     * @test
     */
    public function testSetMailRecipient()
    {
        $this->assertSame("Test@test.tld", $this->object->setMailRecipient("Test@test.tld")->getMailRecipient());
    }

    /**
     * @test
     */
    public function testGetMailRecipient()
    {
        $this->assertSame('', $this->object->getMailRecipient());
    }

}
