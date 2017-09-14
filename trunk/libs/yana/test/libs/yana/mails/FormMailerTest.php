<?php
/**
 * PHPUnit test-case. *
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
 * @package  test
 */
class FormMailerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var    \Yana\Mails\FormMailer
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
        $this->object = new \Yana\Mails\FormMailer($context);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Mails\InvalidMailException
     */
    public function testException()
    {
        $this->object->send("invalid", "", array());
    }

    /**
     * send mail
     *
     * @test
     */
    public function testSend()
    {
        $formData = array("a" => "b", "c" => 1, 2 => "d");

        $result = $this->object->send("mail@domain.tld", "mySubject", $formData);
        $this->assertTrue($result, "Mail has not been sent.");

        $mails = $this->strategy->getMails();
        $lastMail = array_pop($mails);
        $this->assertRegExp("/a:\s*b/", $lastMail[2], "Formmailer has lost some data, or data is not retrievable");
        $this->assertRegExp("/c:\s*1/", $lastMail[2], "Formmailer has lost some data, or data is not retrievable");
        $this->assertRegExp("/2:\s*d/", $lastMail[2], "Formmailer has lost some data, or data is not retrievable");
    }

}
