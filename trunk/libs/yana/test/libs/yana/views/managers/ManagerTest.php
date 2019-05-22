<?php
/**
 * YANA library
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

namespace Yana\Views\Managers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package test
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Smarty
     */
    protected $smarty;

    /**
     * @var \Yana\Views\Managers\Manager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!\class_exists('\Smarty')) {
            $this->markTestSkipped();
        }
        $this->smarty = new \Smarty();
        $this->object = new \Yana\Views\Managers\Manager($this->smarty);
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
    public function testCreateLayoutTemplate()
    {
        $template = $this->object->createLayoutTemplate(__FILE__, 'test', array('var' => 'value'));
        $this->assertTrue($template instanceof \Yana\Views\Templates\IsTemplate);
        $this->assertSame(false, $template->getVar('FILE_IS_INCLUDE'));
        $this->assertSame('test', $template->getVar('SYSTEM_INSERT'));
        $this->assertSame(__FILE__, $template->getVar('SYSTEM_TEMPLATE'));
        $this->assertSame('value', $template->getVar('var'));
    }

    /**
     * All this function is supposed to do is create a template and wrap it.
     *
     * So, this is all we need to test for.
     *
     * @test
     */
    public function testCreateContentTemplate()
    {
        $template = $this->object->createContentTemplate(__FILE__);
        $this->assertTrue($template instanceof \Yana\Views\Templates\IsTemplate);
    }

    /**
     * @test
     */
    public function testSetFunction()
    {
        $name = 'test';
        $code = function() {
            return "test";
        };
        $this->assertSame($this->object, $this->object->setFunction($name, $code));
        $this->assertArrayHasKey('function', $this->smarty->registered_plugins);
        $this->assertArrayHasKey($name, $this->smarty->registered_plugins['function']);
    }

    /**
     * @test
     * @expectedException \Yana\Views\Managers\RegistrationException
     */
    public function testSetFunctionRegistrationException()
    {
        $name = 'test';
        $code = function() {
            return "test";
        };
        $this->object->setFunction($name, $code)->setFunction($name, $code);
    }

    /**
     * @test
     */
    public function testSetModifier()
    {
        $name = 'test';
        $code = function() {
            return "test";
        };
        $this->assertSame($this->object, $this->object->setModifier($name, $code));
        $this->assertArrayHasKey('modifier', $this->smarty->registered_plugins);
        $this->assertArrayHasKey($name, $this->smarty->registered_plugins['modifier']);
    }

    /**
     * @test
     * @expectedException \Yana\Views\Managers\RegistrationException
     */
    public function testSetModifierRegistrationException()
    {
        $name = 'test';
        $code = function() {
            return "test";
        };
        $this->object->setModifier($name, $code)->setModifier($name, $code);
    }

    /**
     * @test
     */
    public function testSetBlockFunction()
    {
        $name = 'test';
        $code = function() {
            return "test";
        };
        $this->assertSame($this->object, $this->object->setBlockFunction($name, $code));
        $this->assertArrayHasKey('block', $this->smarty->registered_plugins);
        $this->assertArrayHasKey($name, $this->smarty->registered_plugins['block']);
    }

    /**
     * @test
     * @expectedException \Yana\Views\Managers\RegistrationException
     */
    public function testSetBlockRegistrationException()
    {
        $name = 'test';
        $code = function() {
            return "test";
        };
        $this->object->setBlockFunction($name, $code)->setBlockFunction($name, $code);
    }

    /**
     * @test
     */
    public function testUnsetFunction()
    {
        $name = 'test';
        $code = function() {
            return "test";
        };
        $this->assertSame($this->object, $this->object->setFunction($name, $code)->unsetFunction($name));
        $this->assertArrayHasKey('function', $this->smarty->registered_plugins);
        $this->assertArrayNotHasKey($name, $this->smarty->registered_plugins['function']);
    }

    /**
     * @test
     */
    public function testUnsetModifier()
    {
        $name = 'test';
        $code = function() {
            return "test";
        };
        $this->assertSame($this->object, $this->object->setModifier($name, $code)->unsetModifier($name));
        $this->assertArrayHasKey('modifier', $this->smarty->registered_plugins);
        $this->assertArrayNotHasKey($name, $this->smarty->registered_plugins['modifier']);
    }

    /**
     * @test
     */
    public function testUnsetBlockFunction()
    {
        $name = 'test';
        $code = function() {
            return "test";
        };
        $this->assertSame($this->object, $this->object->setBlockFunction($name, $code)->unsetBlockFunction($name));
        $this->assertArrayHasKey('block', $this->smarty->registered_plugins);
        $this->assertArrayNotHasKey($name, $this->smarty->registered_plugins['block']);
    }

    /**
     * @test
     */
    public function testGetSmarty()
    {
        $this->assertSame($this->smarty, $this->object->getSmarty());
    }

}
