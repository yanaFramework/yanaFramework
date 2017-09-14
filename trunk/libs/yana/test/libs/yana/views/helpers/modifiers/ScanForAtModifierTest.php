<?php
/**
 * PHPUnit test-case
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

namespace Yana\Views\Helpers\Modifiers;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';

/**
 * @package test
 */
class ScanForAtModifierTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\Modifiers\ScanForAtModifier
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Views\Helpers\Modifiers\ScanForAtModifier(new \Yana\Views\Managers\NullManager());
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
    public function test__invoke()
    {
        $this->assertSame("", $this->object->__invoke(""));
        $this->assertSame("Test", $this->object->__invoke("Test"));
        $this->assertSame(1234, $this->object->__invoke(1234));
        $this->assertSame("&#97;&#64;&#98;&#46;&#99;", $this->object->__invoke("a@b.c"));
        $this->assertSame("a &#97;&#64;&#98;&#46;&#99; c", $this->object->__invoke("a a@b.c c"));
        $this->assertSame("a &#97;&#49;&#64;&#98;&#46;&#99; c", $this->object->__invoke("a a1@b.c c"));
        $this->assertSame("a?&#97;&#49;&#64;&#98;&#46;&#99;?c", $this->object->__invoke("a?a1@b.c?c"));
        $this->assertSame('<input value="a@b.c">', $this->object->__invoke('<input value="a@b.c">'));
    }

}
