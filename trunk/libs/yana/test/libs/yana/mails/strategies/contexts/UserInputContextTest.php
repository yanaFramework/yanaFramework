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

namespace Yana\Mails\Strategies\Contexts;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 */
class UserInputContextTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Mails\Strategies\Contexts\UserInputContext
     */
    protected $object;

    /**
     * @var \Yana\Mails\Strategies\NullStrategy
     */
    protected $strategy;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->strategy = new \Yana\Mails\Strategies\NullStrategy();
        $this->object = new \Yana\Mails\Strategies\Contexts\UserInputContext($this->strategy);
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
        $message = new \Yana\Mails\Messages\Message();
        $message->setSubject('Subject')
            ->setRecipient('Address@domain.tld')
            ->setText('Some Text')
            ->getHeaders()->setHighPriority();
        $this->object->__invoke($message);

        $expectedArguments = array($message->getRecipient(), $message->getSubject(), $message->getText());
        $mails = $this->strategy->getMails();
        $actualArguments = array_pop($mails);
        $actualHeaders = array_pop($actualArguments); // drop headers
        $this->assertEquals($expectedArguments, $actualArguments);
        $this->assertEquals($message->getHeaders()->offsetGet('importance'), $actualHeaders['importance']);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Mails\InvalidMailException
     */
    public function testInvokeInvalidMailException()
    {
        $message = new \Yana\Mails\Messages\Message();
        $message->setSubject('Valid subject')
            ->setRecipient('invalid mail')
            ->setText('Valid text');
        $this->object->__invoke($message);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Mails\MissingSubjectException
     */
    public function testInvokeMissingSubjectException()
    {
        $message = new \Yana\Mails\Messages\Message();
        $message->setSubject("\n\r\f")
            ->setRecipient('valid@mail.tld')
            ->setText('Valid text');
        $this->object->__invoke($message);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\Mails\MissingTextException
     */
    public function testInvokeMissingTextException()
    {
        $message = new \Yana\Mails\Messages\Message();
        $message->setSubject('Valid subject')
            ->setRecipient('valid@mail.tld')
            ->setText('');
        $this->object->__invoke($message);
    }

    /**
     * @test
     */
    public function testInvokeHtml()
    {
        $message = new \Yana\Mails\Messages\Message();
        $message->setSubject('Valid subject')
            ->setRecipient('valid@mail.tld')
            ->setText('<b onbad="attribute">Good Tag</b><script>Bad Tag</script>@')
            ->getHeaders()->setAsHtml();
        $this->object->__invoke($message);

        $expectedArgument = '<b>Good Tag</b>Bad Tag[at]';
        $mails = $this->strategy->getMails();
        $actualArguments = array_pop($mails);
        $this->assertEquals($expectedArgument, $actualArguments[2]);
    }

    /**
     * @test
     */
    public function testInvokeInvalidHeader()
    {
        $message = new \Yana\Mails\Messages\Message();
        $message->setSubject('Valid subject')
            ->setRecipient('valid@mail.tld')
            ->setText('Valid text')
            ->getHeaders()->offsetSet('invalid header', "foo\nbar");
        $this->object->__invoke($message);

        $mails = $this->strategy->getMails();
        $actualArguments = array_pop($mails);
        $headers = $actualArguments[3];
        $this->assertArrayHasKey('x-yana-php-header-protection', $headers);
        $this->assertArrayNotHasKey('invalid header', $headers);
        $this->assertRegExp('/^1 \(.+\)/', $headers['x-yana-php-header-protection']);
    }

    /**
     * @test
     */
    public function testInvokeRemoveHeader()
    {
        $message = new \Yana\Mails\Messages\Message();
        $message->setSubject('Valid subject')
            ->setRecipient('valid@mail.tld')
            ->setText('Valid text')
            ->getHeaders()->offsetSet('x-valid-header', 'to remove');
        $this->object->__invoke($message);

        $mails = $this->strategy->getMails();
        $actualArguments = array_pop($mails);
        $headers = $actualArguments[3];
        $this->assertArrayHasKey('x-yana-php-header-protection', $headers);
        $this->assertArrayNotHasKey('x-valid-header', $headers);
        $this->assertRegExp('/^1 \(.+\)/', $headers['x-yana-php-header-protection']);
    }

    /**
     * @test
     */
    public function testInvokeHeaders()
    {
        $message = new \Yana\Mails\Messages\Message();
        $message->setSubject('Valid subject')
            ->setRecipient('valid@mail.tld')
            ->setText('Valid text');
        $expectedHeaders = $message->getHeaders();
        $expectedHeaders->setAsPlainText()
            ->setFromAddress('from@mail.tld')
            ->setCcAddresses(array('cc1@mail.tld', 'cc2@mail.tld'))
            ->setBccAddresses(array('bcc@mail.tld'))
            ->setReplyAddresses(array('reply@mail.tld'))
            ->setHighPriority();
        $expectedHeaders['mime-version'] = '1.2';
        $expectedHeaders['content-transfer-encoding'] = '8bit';
        $this->object->__invoke($message);

        $mails = $this->strategy->getMails();
        $actualArguments = array_pop($mails);
        $actualHeaders = $actualArguments[3];
        $this->assertArrayHasKey('x-yana-php-header-protection', $actualHeaders);
        $this->assertRegExp('/^0 \(.+\)/', $actualHeaders['x-yana-php-header-protection']);
        $this->assertArrayHasKey('from', $actualHeaders);
        $this->assertArrayHasKey('cc', $actualHeaders);
        $this->assertArrayHasKey('bcc', $actualHeaders);
        $this->assertArrayHasKey('reply-to', $actualHeaders);
        $this->assertArrayHasKey('importance', $actualHeaders);
        $this->assertArrayHasKey('x-priority', $actualHeaders);
        $this->assertArrayHasKey('mime-version', $actualHeaders);
        $this->assertArrayHasKey('content-transfer-encoding', $actualHeaders);
        $this->assertEquals($expectedHeaders['from'], $actualHeaders['from']);
        $this->assertEquals(\implode('; ', $expectedHeaders['cc']), $actualHeaders['cc']);
        $this->assertEquals(\implode('; ', $expectedHeaders['bcc']), $actualHeaders['bcc']);
        $this->assertEquals(\implode('; ', $expectedHeaders['reply-to']), $actualHeaders['reply-to']);
        $this->assertEquals($expectedHeaders['importance'], $actualHeaders['importance']);
        $this->assertEquals($expectedHeaders['x-priority'], $actualHeaders['x-priority']);
        $this->assertEquals($expectedHeaders['mime-version'], $actualHeaders['mime-version']);
        $this->assertEquals($expectedHeaders['content-transfer-encoding'], $actualHeaders['content-transfer-encoding']);
    }

}
