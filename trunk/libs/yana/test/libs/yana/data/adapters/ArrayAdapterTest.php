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

namespace Yana\Data\Adapters;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ArrayAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Data\Adapters\ArrayAdapter
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new \Yana\Data\Adapters\ArrayAdapter();
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
    public function testSaveEntity()
    {
        $this->assertEquals(0, count($this->_object));
        $mockObject = new \Yana\Data\Adapters\MockSplSubject();
        $this->_object->saveEntity($mockObject);
        $this->assertEquals(1, count($this->_object));
        $this->assertEquals($mockObject, $this->_object[0]);
    }

    /**
     * @test
     */
    public function testGetIds()
    {
        $this->_object[1] = 0;
        $this->_object[2] = 0;
        $this->_object[3] = 0;
        $this->assertEquals(array(1, 2, 3), $this->_object->getIds());
    }

}

/**
 * Dummy class for mocking.
 *
 * @package test
 * @ignore
 */
class MockSplSubject extends \Yana\Core\StdObject implements \Yana\Data\Adapters\IsEntity
{

    private $_id;

    public function setDataAdapter(\Yana\Data\Adapters\IsDataAdapter $adapter)
    {
        // intentionally left blank
    }

    public function saveEntity()
    {
        // intentionally left blank
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

}
