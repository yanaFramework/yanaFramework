<?php
/**
 * PHPUnit test-case: DbInfoColumn
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

namespace Yana\Http\Uploads;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class FileCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Http\Uploads\FileCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Http\Uploads\FileCollection();
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
    public function testOffsetSet()
    {
        $file = new \Yana\Http\Uploads\File("", "", "", 0, 0);
        $this->assertEquals($file, $this->object->offsetSet(null, new \Yana\Http\Uploads\File("", "", "", 0, 0)));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $this->object->offsetSet(null, new \stdClass());
    }

    /**
     * @test
     */
    public function testSetItems()
    {
        $this->assertEquals(0, $this->object->count());
        $this->object->setItems(array(new \Yana\Http\Uploads\File("", "", "", 0, 0), new \Yana\Http\Uploads\File("", "", "", 0, 0)));
        $this->assertEquals(2, $this->object->count());
        $this->object->setItems();
        $this->assertEquals(0, $this->object->count());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetItemsInvalidArgumentException()
    {
        $this->object->setItems(array(new \stdClass()));
    }

}
