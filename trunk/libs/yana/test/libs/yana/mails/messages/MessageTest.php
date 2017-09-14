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

namespace Yana\Mails\Messages;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Mails\Messages\Message
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Mails\Messages\Message();
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
    public function testGetHeaders()
    {
        $headers = new \Yana\Mails\Headers\MailHeaderCollection();
        $this->assertEquals($headers, $this->object->getHeaders());
    }

    /**
     * @test
     */
    public function testSetHeaders()
    {
        $headers = new \Yana\Mails\Headers\MailHeaderCollection();
        $headers['reply-to'] = 'Address';
        $this->assertEquals($headers, $this->object->setHeaders($headers)->getHeaders());
    }

    /**
     * @test
     */
    public function testGetSubject()
    {
        $this->assertEquals("", $this->object->getSubject());
    }

    /**
     * @test
     */
    public function testSetSubject()
    {
        $this->assertEquals("some Subject", $this->object->setSubject("some Subject")->getSubject());
    }

    /**
     * @test
     */
    public function testGetRecipient()
    {
        $this->assertEquals("", $this->object->getRecipient());
    }

    /**
     * @test
     */
    public function testSetRecipient()
    {
        $this->assertEquals("Address", $this->object->setRecipient("Address")->getRecipient());
    }

    /**
     * @test
     */
    public function testGetText()
    {
        $this->assertEquals("", $this->object->getText());
    }

    /**
     * @test
     */
    public function testSetText()
    {
        $this->assertEquals("Some text", $this->object->setText("Some text")->getText());
    }

}
