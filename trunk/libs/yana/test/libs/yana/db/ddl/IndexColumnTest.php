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
declare(strict_types=1);

namespace Yana\Db\Ddl;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';


/**
 * DDL test-case
 *
 * @package  test
 */
class IndexColumnTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\IndexColumn
     */
    protected $object;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\IndexColumn('indexColumn');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->object);
    }

    /**
     * @test
     */
    public function testGetLength()
    {
        $this->assertNull($this->object->getLength());
    }

    /**
     * @test
     */
    public function testSetLength()
    {
        $this->assertEquals(20, $this->object->setLength(20)->getLength());
        $this->assertNull($this->object->setLength(0)->getLength());
    }

    /**
     * @test
     */
    public function testIsDescendingOrder()
    {
        $this->assertFalse($this->object->isDescendingOrder());
    }

    /**
     * @test
     */
    public function testIsAscendingOrder()
    {
        $this->assertTrue($this->object->isAscendingOrder());
    }

    /**
     * @test
     */
    public function testSetSorting()
    {
        $this->assertTrue($this->object->setSorting(false)->isDescendingOrder());
        $this->assertFalse($this->object->setSorting(false)->isAscendingOrder());
        $this->assertFalse($this->object->setSorting(true)->isDescendingOrder());
        $this->assertTrue($this->object->setSorting(true)->isAscendingOrder());
    }

    /**
     * @test
     */
    public function testSerializeToXDDL()
    {
        $simpleXmlElement = $this->object->setLength(123)->serializeToXDDL();
        $this->assertContains('<column name="indexcolumn" sorting="ascending" length="123"/>', $simpleXmlElement->asXML());
    }

    /**
     * @test
     */
    public function testSerializeToXDDLDescending()
    {
        $simpleXmlElement = $this->object->setSorting(false)->setLength(123)->serializeToXDDL();
        $this->assertContains('<column name="indexcolumn" sorting="descending" length="123"/>', $simpleXmlElement->asXML());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testUnserializeFromXDDLInvalidArgumentException()
    {
        $data = "<column/>";
        \Yana\Db\Ddl\IndexColumn::unserializeFromXDDL(new \SimpleXMLElement($data));
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $data = "<column name='Test' sorting='ascending' length='123'/>";
        $this->object = \Yana\Db\Ddl\IndexColumn::unserializeFromXDDL(new \SimpleXMLElement($data));
        $this->assertSame('test', $this->object->getName());
        $this->assertTrue($this->object->isAscendingOrder());
        $this->assertSame(123, $this->object->getLength());
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDLDescending()
    {
        $data = "<column name='Test' sorting='descending' length='123'/>";
        $this->object = \Yana\Db\Ddl\IndexColumn::unserializeFromXDDL(new \SimpleXMLElement($data));
        $this->assertSame('test', $this->object->getName());
        $this->assertTrue($this->object->isDescendingOrder());
        $this->assertSame(123, $this->object->getLength());
    }

}

?>