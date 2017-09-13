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
class AbstractManagerTest extends \PHPUnit_Framework_TestCase
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
        $this->assertSame(array('a', '', 'c'), $this->object->addScript('a')->addScript('')->addScript('a')->addScript('c')->getScripts());
    }

    /**
     * @test
     */
    public function testAddScripts()
    {
        $this->assertSame(array(), $this->object->addScripts(array())->getScripts());
        $this->assertSame(array('a', 'b'), $this->object->addScripts(array('a', 'b'))->getScripts());
        $this->assertSame(array('a', 'b', 'c'), $this->object->addScripts(array('a', 'b'))->addScripts(array('a', 'c'))->getScripts());
    }

    /**
     * @test
     */
    public function testAddStyle()
    {
        $this->assertSame(array('a', '', 'c'), $this->object->addStyle('a')->addStyle('')->addStyle('a')->addStyle('c')->getStyles());
    }

    /**
     * @test
     */
    public function testAddStyles()
    {
        $this->assertSame(array(), $this->object->addStyles(array())->getStyles());
        $this->assertSame(array('a', 'b'), $this->object->addStyles(array('a', 'b'))->getStyles());
        $this->assertSame(array('a', 'b', 'c'), $this->object->addStyles(array('a', 'b'))->addStyles(array('a', 'c'))->getStyles());
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


}
