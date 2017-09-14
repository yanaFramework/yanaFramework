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

namespace Yana\Views\Helpers\Html;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class Helper extends \Yana\Views\Helpers\Html\AbstractHelper
{

}

/**
 * @package  test
 */
class AbstractHelperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\Html\AbstractHelper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Views\Helpers\Html\Helper();
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
    public function testGetId()
    {
        $this->assertEquals('', $this->object->getId());
    }

    /**
     * @test
     */
    public function testSetId()
    {
        $value = '<Äß;0';
        $this->assertEquals(\htmlspecialchars($value), $this->object->setId($value)->getId());
    }

    /**
     * @test
     */
    public function testGetName()
    {
        $this->assertEquals('', $this->object->getName());
    }

    /**
     * @test
     */
    public function testSetName()
    {
        $value = '<Äß;0';
        $this->assertEquals(\htmlspecialchars($value), $this->object->setName($value)->getName());
    }

    /**
     * @test
     */
    public function testGetCssClass()
    {
        $this->assertEquals('', $this->object->getCssClass());
    }

    /**
     * @test
     */
    public function testSetCssClass()
    {
        $value = '<Äß;0';
        $this->assertEquals(\htmlspecialchars($value), $this->object->setCssClass($value)->getCssClass());
    }

    /**
     * @test
     */
    public function testGetTitle()
    {
        $this->assertEquals('', $this->object->getTitle());
    }

    /**
     * @test
     */
    public function testSetTitle()
    {
        $value = '<Äß;0';
        $this->assertEquals(\htmlspecialchars($value), $this->object->setTitle($value)->getTitle());
    }

    /**
     * @test
     */
    public function testGetMaxLength()
    {
        $this->assertEquals(0, $this->object->getMaxLength());
    }

    /**
     * @test
     */
    public function testSetMaxLength()
    {
        $value = 1;
        $this->assertEquals($value, $this->object->setMaxLength($value)->getMaxLength());
    }

    /**
     * @test
     */
    public function testGetAttr()
    {
        $this->assertEquals('', $this->object->getAttr());
    }

    /**
     * @test
     */
    public function testSetAttr()
    {
        $value = '<Äß;0';
        $this->assertEquals(\htmlspecialchars($value), $this->object->setAttr($value)->getAttr());
    }

}
