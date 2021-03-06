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

namespace Yana\Mails\Strategies;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 *
 * Note that this class doesn't really test anything except that
 * the names and path of the file and the class are correct.
 */
class ImapStrategyTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Mails\Strategies\ImapStrategy
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Mails\Strategies\ImapStrategy();
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
    public function testInvoke()
    {
        $recipient = ""; // leave this empty, so no mail is sent
        $subject = "Some subject";
        $text = "Some text";
        $header = array("some" => "header");
        try {
            $this->assertFalse($this->object->__invoke($recipient, $subject, $text, $header = array()));
        } catch (\Yana\Core\Exceptions\Mails\NotSupportedException $ex) {
            $this->markTestSkipped($ex->getMessage());
        }
    }

}
