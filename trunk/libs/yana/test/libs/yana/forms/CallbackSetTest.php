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

namespace Yana\Forms;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class CallbackSetTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Forms\CallbackSet
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Forms\CallbackSet();
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
    public function testGetBeforeCreate()
    {
        $this->assertSame(array(), $this->object->getBeforeCreate()->toArray());
    }

    /**
     * @test
     */
    public function testAddBeforeCreate()
    {
        $f1 = function() {};
        $f2 = function() {};
        $this->assertSame(array($f1, $f2), $this->object->addBeforeCreate($f1)->addBeforeCreate($f2)->getBeforeCreate()->toArray());
    }

    /**
     * @test
     */
    public function testGetAfterCreate()
    {
        $this->assertSame(array(), $this->object->getAfterCreate()->toArray());
    }

    /**
     * @test
     */
    public function testAddAfterCreate()
    {
        $f1 = function() {};
        $f2 = function() {};
        $this->assertSame(array($f1, $f2), $this->object->addAfterCreate($f1)->addAfterCreate($f2)->getAfterCreate()->toArray());
    }

    /**
     * @test
     */
    public function testGetBeforeUpdate()
    {
        $this->assertSame(array(), $this->object->getBeforeUpdate()->toArray());
    }

    /**
     * @test
     */
    public function testAddBeforeUpdate()
    {
        $f1 = function() {};
        $f2 = function() {};
        $this->assertSame(array($f1, $f2), $this->object->addBeforeUpdate($f1)->addBeforeUpdate($f2)->getBeforeUpdate()->toArray());
    }

    /**
     * @test
     */
    public function testGetAfterUpdate()
    {
        $this->assertSame(array(), $this->object->getAfterUpdate()->toArray());
    }

    /**
     * @test
     */
    public function testAddAfterUpdate()
    {
        $f1 = function() {};
        $f2 = function() {};
        $this->assertSame(array($f1, $f2), $this->object->addAfterUpdate($f1)->addAfterUpdate($f2)->getAfterUpdate()->toArray());
    }

    /**
     * @test
     */
    public function testGetBeforeDelete()
    {
        $this->assertSame(array(), $this->object->getBeforeDelete()->toArray());
    }

    /**
     * @test
     */
    public function testAddBeforeDelete()
    {
        $f1 = function() {};
        $f2 = function() {};
        $this->assertSame(array($f1, $f2), $this->object->addBeforeDelete($f1)->addBeforeDelete($f2)->getBeforeDelete()->toArray());
    }

    /**
     * @test
     */
    public function testGetAfterDelete()
    {
        $this->assertSame(array(), $this->object->getAfterDelete()->toArray());
    }

    /**
     * @test
     */
    public function testAddAfterDelete()
    {
        $f1 = function() {};
        $f2 = function() {};
        $this->assertSame(array($f1, $f2), $this->object->addAfterDelete($f1)->addAfterDelete($f2)->getAfterDelete()->toArray());
    }

}
