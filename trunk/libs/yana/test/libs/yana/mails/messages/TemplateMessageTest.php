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
class TemplateMessageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Mails\Messages\TemplateMessage
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $template = new \Yana\Views\Templates\NullTemplate();
        $this->object = new \Yana\Mails\Messages\TemplateMessage($template);
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
    public function testGetVars()
    {
        $this->assertEquals(array(), $this->object->getVars());
    }

    /**
     * @test
     */
    public function testGetVar()
    {
        $this->assertNull($this->object->getVar(""));
    }

    /**
     * @test
     */
    public function testSetVar()
    {
        $value = 'Value äß';
        $this->assertEquals($value, $this->object->setVar('Test', $value)->getVar('Test'));
    }

    /**
     * @test
     */
    public function testSetVars()
    {
        $values = array('Test' => 'Value äß');
        $this->assertEquals($values, $this->object->setVars($values)->getVars());
    }

    /**
     * @test
     */
    public function testSetVarByReference()
    {
        $value = 'Value äß';
        $this->assertEquals($value, $this->object->setVarByReference('Test', $value)->getVar('Test'));
        $value = 'test';
        $this->assertEquals($value, $this->object->getVar('Test'));
    }

    /**
     * @test
     */
    public function testSetVarsByReference()
    {
        $values = array('Test' => 'Value äß');
        $this->assertEquals($values, $this->object->setVars($values)->getVars());
    }

    /**
     * @test
     */
    public function testSetText()
    {
        $value = 'Value äß';
        $this->assertEquals($value, $this->object->setText($value)->getText());
    }

    /**
     * @test
     */
    public function testGetText()
    {
        $this->assertEquals("", $this->object->getText());
    }

}
