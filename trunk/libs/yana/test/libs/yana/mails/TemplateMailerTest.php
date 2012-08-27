<?php
/**
 * PHPUnit test-case: Mailer
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
 * Test class for Mailer
 *
 * @package  test
 */
class TemplateMailerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Mails\Strategies\NullStrategy
     */
    protected $strategy;

    /**
     * @var  \Yana\Views\NullTemplate
     */
    protected $template;

    /**
     * @var  \Yana\Mails\TemplateMailer
     */
    protected $object;

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        // intentionally left blank
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->strategy = new \Yana\Mails\Strategies\NullStrategy();
        $context = new \Yana\Mails\Strategies\Contexts\UserInputContext($this->strategy);
        $this->template = new \Yana\Views\NullTemplate();
        $this->template->setPath(CWD . 'resources/mail.tpl');
        $this->object = new \Yana\Mails\TemplateMailer($this->template, $context);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    /**
     * @test
     */
    public function testSend()
    {
        $vars = array('Test');
        $expectedArguments = array("mail@domain.tld", "mySubject", $this->template->fetch());
        $result = $this->object->send($expectedArguments[0], $expectedArguments[1], $vars);
        $this->assertTrue($result, "Mail has not been sent.");

        $mails = $this->strategy->getMails();
        $lastMail = array_pop($mails);
        $this->assertEquals($lastMail[0], $expectedArguments[0]);
        $this->assertEquals($lastMail[1], $expectedArguments[1]);
        $this->assertEquals($lastMail[2], $expectedArguments[2]);
    }

}

?>