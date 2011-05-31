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

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';

/**
 * Test class for Formmailer
 *
 * @package  test
 */
class FormMailerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var    FormMailer
     * @access protected
     */
    protected $object;
    /**
     * sammelt die Maileinträge
     * 
     * @var    mails
     * @access protected
     */
    protected $mails = array();
    /**
     * @var    string
     * @access protected
     */
    protected $backupMailHandler;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->object = new FormMailer();
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
     * send mail
     *
     * @test
     */
    public function testSend()
    {
        $this->object->setContent(array('Test'));

        // Test Exception Number as Recipient
        try {
            $result = $this->object->send(1);
            $this->fail("FormMailer should not accept a number as recipient.");
        } catch (\Exception $e) {
            // success
        }

        // Test exception empty as recipient
        try {
            $result = $this->object->send("");
            $this->fail("FormMailer should not accept an empty recipient.");
        } catch (\Exception $e) {
            // success
        }

        // Test empty content
        $this->object->setContent(array());
        $result = true;
        try {
            $result = $this->object->send("mail@domain.tld");
        } catch (\Exception $e) {
            $this->fail("Unexpected exception: " . $e->getMessage());
        }
        $this->assertFalse($result, "FormMailer should not accept an empty Content");

        // wrong Element Type 'Object'
        $exception = false;
        $this->object->setContent(array("test" => new Object()));
        try {
            $this->object->send("mail@domain.tld");
            $this->fail("FormMailer should only accept scalar values or arrays as content-elements.");
        } catch (\Exception $e) {
            $expectedNoticeString = $e->getMessage();
            $this->assertRegExp('/Invalid form data/i', $expectedNoticeString, "FormMailer should only accept scalar values or arrays in form fields.");
        }

        // äöüß are no illegal Character
        $this->object->setContent(array("äöüß" => "aaa"));
        try {
            $this->object->send("mail@domain.tld");
        } catch (\Exception $e) {
            $this->fail("FormMailer should accept Umlaut characters in keys");
        }

        // illegal Characters in the Key
        // should throw a Notice
        $this->object->setContent(array(iconv("", "UTF-8", "áéí") => "aaa"));
        $expectedNoticeString = "";
        try {
            $this->object->send("mail@domain.tld");
            $this->fail("FormMailer should not accept special characters in keys.");
        } catch (\Exception $e) {
            $expectedNoticeString = $e->getMessage();
            $this->assertRegExp('/Invalid form data/i', $expectedNoticeString, "FormMailer should not accept special characters in keys.");
        }

        $this->object->setContent(array("a" => "b", "c" => 1, 2 => "d"))
            ->setSubject("mySubject")
            ->setHeadline("myHeadline");
        $result = $this->object->send("mail@domain.tld");
        $this->assertTrue($result, "Mail has not been sent.");

        $lastMail = array_pop($this->mails);
        $this->assertRegExp("/a:\s*b/", $lastMail[2], "Formmailer has lost some data, or data is not retrievable");
        $this->assertRegExp("/c:\s*1/", $lastMail[2], "Formmailer has lost some data, or data is not retrievable");
        $this->assertRegExp("/2:\s*d/", $lastMail[2], "Formmailer has lost some data, or data is not retrievable");
        $this->assertRegExp("/charset\s*=\s*UTF\-8/i", $lastMail[3], "Formmailer should send UTF-8, if not stated otherwise");
    }

}

?>