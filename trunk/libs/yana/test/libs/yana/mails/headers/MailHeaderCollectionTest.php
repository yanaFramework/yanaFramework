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

namespace Yana\Mails\Headers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class MailHeaderCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Mails\Headers\MailHeaderCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Mails\Headers\MailHeaderCollection();
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
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $this->object->offsetSet("", "test");
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $value = "Value ß";
        $this->assertEquals($value, $this->object->offsetSet("Key", $value));
        $this->assertEquals($value, $this->object->offsetGet("key"));
    }

    /**
     * @test
     */
    public function testSetHighPriority()
    {
        $this->assertTrue($this->object->setHighPriority()->isHighPriority());
        $this->assertFalse($this->object->setNormalPriority()->isHighPriority());
        $this->object['importance'] = 'high';
        $this->assertTrue($this->object->isHighPriority());
    }

    /**
     * @test
     */
    public function testSetNormalPriority()
    {
        $this->assertFalse($this->object->setHighPriority()->isNormalPriority());
        $this->assertTrue($this->object->setNormalPriority()->isNormalPriority());
        $this->object['importance'] = 'normal';
        $this->assertTrue($this->object->isNormalPriority());
        $this->object['importance'] = 'normal';
        $this->assertFalse($this->object->setHighPriority()->isNormalPriority());
    }

    /**
     * @test
     */
    public function testSetLowPriority()
    {
        $this->assertTrue($this->object->setLowPriority()->isLowPriority());
        $this->assertFalse($this->object->setNormalPriority()->isLowPriority());
        $this->object['importance'] = 'low';
        $this->assertTrue($this->object->isLowPriority());
    }

    /**
     * @test
     */
    public function testIsHighPriority()
    {
        $this->assertFalse($this->object->isHighPriority());
        $this->assertTrue($this->object->setHighPriority()->isHighPriority());
    }

    /**
     * @test
     */
    public function testIsNormalPriority()
    {
        $this->assertTrue($this->object->isNormalPriority());
    }

    /**
     * @test
     */
    public function testIsLowPriority()
    {
        $this->assertFalse($this->object->isLowPriority());
        $this->assertTrue($this->object->setLowPriority()->isLowPriority());
    }

    /**
     * @test
     */
    public function testSetReplyAddresses()
    {
        $addresses = array('Address1', 'Address2');
        $this->assertEquals($addresses, $this->object->setReplyAddresses($addresses)->getReplyAddresses());
        $this->assertEquals($addresses, $this->object['reply-to']);
    }

    /**
     * @test
     */
    public function testGetReplyAddresses()
    {
        $this->assertEquals(array(), $this->object->getReplyAddresses());
    }

    /**
     * @test
     */
    public function testSetFromAddress()
    {
        $this->assertEquals('Address', $this->object->setFromAddress('Address')->getFromAddress());
    }

    /**
     * @test
     */
    public function testGetFromAddress()
    {
        $this->assertEquals("", $this->object->getFromAddress());
    }

    /**
     * @test
     */
    public function testSetCcAddresses()
    {
        $addresses = array('Address1', 'Address2');
        $this->assertEquals($addresses, $this->object->setCcAddresses($addresses)->getCcAddresses());
        $this->assertEquals($addresses, $this->object['cc']);
    }

    /**
     * @test
     */
    public function testGetCcAddresses()
    {
        $this->assertEquals(array(), $this->object->getCcAddresses());
    }

    /**
     * @test
     */
    public function testSetBccAddresses()
    {
        $addresses = array('Address1', 'Address2');
        $this->assertEquals($addresses, $this->object->setBccAddresses($addresses)->getBccAddresses());
        $this->assertEquals($addresses, $this->object['bcc']);
    }

    /**
     * @test
     */
    public function testGetBccAddresses()
    {
        $this->assertEquals(array(), $this->object->getBccAddresses());
    }

    /**
     * @test
     */
    public function testSetAsHtml()
    {
        $this->assertTrue($this->object->setAsHtml()->isHtml());
        $this->assertEquals('text/html; charset=UTF-8', $this->object['content-type']);
        $this->assertFalse($this->object->setAsPlainText()->isHtml());
        $this->assertNotEquals('text/html; charset=UTF-8', $this->object['content-type']);
    }

    /**
     * @test
     */
    public function testIsHtml()
    {
        $this->assertFalse($this->object->isHtml());
    }

    /**
     * @test
     */
    public function testSetAsPlainText()
    {
        $this->assertTrue($this->object->setAsPlainText()->isPlainText());
        $this->assertEquals('text/plain; charset=UTF-8', $this->object['content-type']);
        $this->assertFalse($this->object->setAsHtml()->isPlainText());
        $this->assertNotEquals('text/plain; charset=UTF-8', $this->object['content-type']);
    }

    /**
     * @test
     */
    public function testIsPlainText()
    {
        $this->assertTrue($this->object->isPlainText());
    }

}
