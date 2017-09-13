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
class NullManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Managers\NullManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Views\Managers\NullManager();
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
    public function testAddScript()
    {
        $this->assertSame($this->object, $this->object->addScript(""));
    }

    /**
     * @test
     */
    public function testAddScripts()
    {
        $this->assertSame($this->object, $this->object->addScripts(array()));
    }

    /**
     * @test
     */
    public function testAddStyle()
    {
        $this->assertSame($this->object, $this->object->addStyle(""));
    }

    /**
     * @test
     */
    public function testAddStyles()
    {
        $this->assertSame($this->object, $this->object->addStyles(array()));
    }

    /**
     * @test
     */
    public function testGetScripts()
    {
        $this->assertSame(array(), $this->object->getScripts());
    }

    /**
     * @test
     */
    public function testGetStyles()
    {
        $this->assertSame(array(), $this->object->getStyles());
    }

    /**
     * @test
     */
    public function testCreateLayoutTemplate()
    {
        $this->assertEquals(new \Yana\Views\Templates\NullTemplate(), $this->object->createLayoutTemplate("", "", array()));
    }

    /**
     * @test
     */
    public function testCreateContentTemplate()
    {
        $this->assertEquals(new \Yana\Views\Templates\NullTemplate(), $this->object->createContentTemplate(""));
    }

    /**
     * @test
     */
    public function testClearCache()
    {
        $this->assertNull($this->object->clearCache());
    }

    /**
     * @test
     */
    public function testSetFunction()
    {
        $this->assertSame($this->object, $this->object->setFunction("", ""));
    }

    /**
     * @test
     */
    public function testSetModifier()
    {
        $this->assertSame($this->object, $this->object->setModifier("", ""));
    }

    /**
     * @test
     */
    public function testSetBlockFunction()
    {
        $this->assertSame($this->object, $this->object->setBlockFunction("", ""));
    }

    /**
     * @test
     */
    public function testUnsetFunction()
    {
        $this->assertSame($this->object, $this->object->unsetFunction(""));
    }

    /**
     * @test
     */
    public function testUnsetModifier()
    {
        $this->assertSame($this->object, $this->object->unsetModifier(""));
    }

    /**
     * @test
     */
    public function testUnsetBlockFunction()
    {
        $this->assertSame($this->object, $this->object->unsetBlockFunction(""));
    }

    /**
     * @test
     */
    public function testGetSmarty()
    {
        if (!\class_exists('\Smarty')) {
            $this->markTestSkipped('Smarty class required for this test');
        }
        $this->assertTrue($this->object->getSmarty() instanceof \Smarty);
    }

}
