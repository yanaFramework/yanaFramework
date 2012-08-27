<?php
/**
 * PHPUnit test-case: Formmailer
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

namespace Yana\Mails;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * Test class for TextMailer.
 * Generated by PHPUnit on 2012-08-28 at 01:12:42.
 */
class TextMailerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var TextMailer
     */
    protected $object;

    /**
     * @var    \Yana\Mails\Strategies\NullStrategy
     */
    protected $strategy;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->strategy = new \Yana\Mails\Strategies\NullStrategy();
        $context = new \Yana\Mails\Strategies\Contexts\UserInputContext($this->strategy);
        $this->object = new \Yana\Mails\TextMailer($context);
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
    public function testSend()
    {
        $expectedArguments = array("mail@domain.tld", "mySubject", "someText");
        $result = $this->object->send($expectedArguments[0], $expectedArguments[1], $expectedArguments[2]);
        $this->assertTrue($result, "Mail has not been sent.");

        $mails = $this->strategy->getMails();
        $lastMail = array_pop($mails);
        $this->assertEquals($lastMail[0], $expectedArguments[0]);
        $this->assertEquals($lastMail[1], $expectedArguments[1]);
        $this->assertEquals($lastMail[2], $expectedArguments[2]);
    }

}

?>
