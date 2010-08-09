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

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';

/**
 * Test class for Mailer
 *
 * @package  test
 */
class MailerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Mailer
     * @access protected
     */
    protected $mailer;

    /**
     * @var    string
     * @access protected
     */
    protected $backupMailHandler;

    /**
     * @var    array
     * @access protected
     */
    protected $mails = array();

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
        chdir(CWD . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
        global $YANA;
        if (!isset($YANA)) {
            $YANA = Yana::getInstance();
        }
        $this->mailer = new Mailer(CWD.'resources/mail.tpl');
        $this->mailer->sender = 'qwerty@domain.tld';
        $this->mailer->subject = 'unit test';
        $this->backupMailHandler = Mailer::getGlobalMailHandler();
        Mailer::setGlobalMailHandler(array($this, 'sendMail'));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        unset($this->mailer);
        chdir(CWD);
        Mailer::setGlobalMailHandler($this->backupMailHandler);
    }

    /**
     * Dummy send mail function
     *
     * Protocols any function call to check whether mail input vars are correct.
     *
     * @access  public
     * @param   string  $to
     * @param   string  $subject
     * @param   string  $message
     * @param   string  $additionalHeaders
     * @param   string  $additionalParameters
     * @return  bool
     */
    public function sendMail($to, $subject, $message, $additionalHeaders = "", $additionalParameters = "")
    {
        $this->mails[] = func_get_args();
        return true;
    }

    /**
     * Send
     *
     * @test
     */
    public function testSend()
    {
        // set text for valid template text
        $validText = file_get_contents($this->mailer->getPath());
        //set recipient
        $recipient = 'mail@domain.tld';

        $result = $this->mailer->send($recipient);
        $this->assertTrue($result, 'assert failed, the mail is not sended');
        
        $mail = array_pop($this->mails);
        // check if message match the template text
        $this->assertEquals($mail[2], $validText, 'Message text should match given message argument');
        // check recipient
        $this->assertEquals($mail[0], $recipient, 'Recipient should match given recipient argument');
        $expectedSubject = '[MAILFORM] unit test';
        // check subject
        $this->assertEquals($mail[1], $expectedSubject, 'Subject should match given subject argument');
    }

    /**
     * setGlobalMailHandler Exception
     *
     * @expectedException InvalidArgumentException
     * @test
     */
    public function testsetGlobalMailHandlerException()
    {
       Mailer::setGlobalMailHandler('sendMail');
    }

    /**
     * set Mail Handler Exception
     *
     * @expectedException InvalidArgumentException
     * @test
     */
    public function testsetMailHandlerException()
    {
       $this->mailer->setMailHandler('sendMail');
    }

    /**
     * Mail
     *
     * @test
     */
    public function testMail()
    {
        $subject = $this->mailer->subject;
        $recipient = 'mail@domain.tld';
        $text = 'qwerty qwerty qwerty qwerty qwerty'."\n".
                'ytrewq ytrewq ytrewq ytrewq ytrewq';

        $send = Mailer::mail($recipient, $subject, $text);
        $this->assertTrue($send, 'assert failed, message is not sended');
        $mail = array_pop($this->mails);
        
        // check if text match the argunent text
        $this->assertEquals($mail[2], $text, 'Message text should match given message argument');
        // check recipient
        $this->assertEquals($mail[0], $recipient, 'Recipient should match given recipient argument');
        // check subject
        $this->assertEquals($mail[1], $subject, 'Subject should match given subject argument');

        $header = array('cc' => 'new@mail.tld', 'content-type' => 'text/plain; charset=UTF-8', 'mime-version' => '1.0');
        $send = Mailer::mail($recipient, $subject, $text, $header);
        $this->assertTrue($send, 'assert failed, message is not sended');
        $mail = array_pop($this->mails);
        // check if text match the argunent text
        $this->assertEquals($mail[2], $text, 'Message text should match given message argument');

        $result = $this->mailer->getMailHandler();
        $this->assertTrue($result[0] instanceof MailerTest, 'assert failed , value should be an instance of MailerTest');
        $this->assertTrue($result[1] == 'sendMail', 'assert failed, the variables should be equal');
    }
}
?>