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

namespace Yana\Db\FileDb;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\FileDb\Result
     */
    protected $object;

    /**
     * @var array
     */
    protected $rows = array(123 => array('A' => 'Val1', 'B' => 'Val2'), 124 => array('A' => 'Val3', 'B' => 'Val4'));

    /**
     * @var \Yana\Db\FileDb\Result
     */
    protected $error;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\FileDb\Result($this->rows);
        $this->error = new \Yana\Db\FileDb\Result(null, 'My message');
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
    public function testCountRows()
    {
        $this->assertSame(0, $this->error->countRows());
        $this->assertSame(2, $this->object->countRows());
    }

    /**
     * @test
     */
    public function testFetchRow()
    {
        $this->assertSame(array(), $this->error->fetchRow(1));
        $this->assertEquals(\Yana\Util\Hashtable::changeCase($this->rows[123]), $this->object->fetchRow(123));
    }

    /**
     * @test
     */
    public function testFetchAll()
    {
        $this->assertNull($this->error->fetchAll());
        $this->assertEquals(\Yana\Util\Hashtable::changeCase($this->rows), $this->object->fetchAll());
    }

    /**
     * @test
     */
    public function testFetchColumn()
    {
        $this->assertSame(array(), $this->error->fetchColumn(0));
        $this->assertEquals(array('Val1', 'Val3'), $this->object->fetchColumn(0));
        $this->assertEquals(array('Val2', 'Val4'), $this->object->fetchColumn(1));
        $this->assertEquals(array('Val1', 'Val3'), $this->object->fetchColumn('A'));
        $this->assertEquals(array('Val2', 'Val4'), $this->object->fetchColumn('B'));
        $this->assertEquals(array(null, null), $this->object->fetchColumn(2));
    }

    /**
     * @test
     */
    public function testFetchOne()
    {
        $this->assertNull($this->error->fetchOne(0, 0));
        $this->assertSame('Val1', $this->object->fetchOne(0, 123));
        $this->assertSame('Val2', $this->object->fetchOne(1, 123));
        $this->assertSame('Val3', $this->object->fetchOne(0, 124));
        $this->assertSame('Val4', $this->object->fetchOne(1, 124));
        $this->assertSame('Val1', $this->object->fetchOne('A', 123));
        $this->assertSame('Val2', $this->object->fetchOne('B', 123));
        $this->assertSame('Val3', $this->object->fetchOne('A', 124));
        $this->assertSame('Val4', $this->object->fetchOne('B', 124));
        $this->assertSame(null, $this->object->fetchOne(2, 0));
    }

    /**
     * @test
     */
    public function testGetMessage()
    {
        $this->assertSame('My message', $this->error->getMessage());
        $this->assertSame('', $this->object->getMessage());
    }

}
