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

namespace Yana\Forms\Setups;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Forms\Setups\Context
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Forms\Setups\Context('test');
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
    public function testGetContextName()
    {
        $this->assertEquals('test', $this->object->getContextName());
    }

    /**
     * @test
     */
    public function testGetRow()
    {
        $row = array('a' => 1, 'b' => 2);
        $this->object->setRows(array($row));
        $this->assertEquals(array('A' => 1, 'B' => 2), $this->object->getRow());
    }

    /**
     * @test
     */
    public function testGetRows()
    {
        $this->assertTrue($this->object->getRows() instanceof \Yana\Forms\RowIterator,
            'Instance of \Yana\Forms\RowIterator expected.');
    }

    /**
     * @test
     */
    public function testUpdateRow()
    {
        $newValues = array('b' => array('A' => 2), 'c' => array('D' => array(1, 2, 3)));
        $oldValues = array('a' => 1, 'c' => array('D' => ''));
        $this->object->updateRow(1, $oldValues);
        $this->object->updateRow(1, $newValues);
        $row = $this->object->getRows()->offsetGet(1);
        $this->assertEquals($oldValues['a'], $row['A']);
        $this->assertEquals($newValues['b'], $row['B']);
        $this->assertEquals($newValues['c'], $row['C']);
    }

    /**
     * @test
     */
    public function testGetValue()
    {
        $this->assertEquals(null, $this->object->getValue('test'));
    }

    /**
     * @test
     */
    public function testGetValues()
    {
        $this->assertEquals(array(), $this->object->getValues());
    }

    /**
     * @test
     */
    public function testSetValue()
    {
        $this->assertSame(123, $this->object->setValue('a', 123)->getValue('a'));
    }

    /**
     * @test
     */
    public function testSetValues()
    {
        $values = array('a' => 1, 'b' => array('a' => 2), 'c' => array('d' => array(1, 2, 3)));
        $this->object->setValues($values);
        $this->assertEquals($values['a'], $this->object->getValue('a'));
        $this->assertEquals($values['b'], $this->object->getValue('b'));
        $this->assertEquals($values['b']['a'], $this->object->getValue('b.a'));
        $this->assertEquals($values['c'], $this->object->getValue('c'));
        $this->assertEquals($values['c']['d'], $this->object->getValue('c.d'));
        $this->assertEquals(1, $this->object->getValue('c.d.0'));
    }

    /**
     * @test
     */
    public function testSetValues2()
    {
        $values = array(1, 2, 3);
        $this->assertEquals($values, $this->object->setValues($values)->getValues());
    }

    /**
     * @test
     */
    public function testAddValues()
    {
        $newValues = array('b' => array('a' => 2), 'c' => array('d' => array(1, 2, 3)));
        $oldValues = array('a' => 1, 'c' => array('d' => ''));
        $this->object->setValues($oldValues);
        $this->object->addValues($newValues);
        $this->assertEquals($oldValues['a'], $this->object->getValue('a'));
        $this->assertEquals($newValues['b'], $this->object->getValue('b'));
        $this->assertEquals($newValues['b']['a'], $this->object->getValue('b.a'));
        $this->assertEquals($newValues['c'], $this->object->getValue('c'));
        $this->assertEquals($newValues['c']['d'], $this->object->getValue('c.d'));
        $this->assertEquals(1, $this->object->getValue('c.d.0'));
    }

    /**
     * @test
     */
    public function testSetAction()
    {
        $this->assertEquals('functionName', $this->object->setAction('functionName')->getAction());
    }

    /**
     * @test
     */
    public function testGetAction()
    {
        $this->assertEquals('', $this->object->getAction());
    }

    /**
     * @test
     */
    public function testGetColumnNames()
    {
        $this->assertEquals(array(), $this->object->getColumnNames());
    }

    /**
     * @test
     */
    public function testSetColumnNames()
    {
        $columnNames = array('a', 'B', 'ä');
        $this->assertEquals(array('A', 'B', 'Ä'), $this->object->setColumnNames($columnNames)->getColumnNames());
    }

    /**
     * @test
     */
    public function testGetFooter()
    {
        $this->assertEquals(null, $this->object->getFooter());
    }

    /**
     * @test
     */
    public function testSetFooter()
    {
        $test = "ä\n'<\"Äß#.";
        $this->assertEquals($test, $this->object->setFooter($test)->getFooter());
    }

    /**
     * @test
     */
    public function testGetHeader()
    {
        $this->assertEquals(null, $this->object->getHeader());
    }

    /**
     * @test
     */
    public function testSetHeader()
    {
        $test = "ä\n'<\"Äß#.";
        $this->assertEquals($test, $this->object->setHeader($test)->getHeader());
    }

    /**
     * @test
     */
    public function testAddColumnName()
    {
        $this->assertSame(array('Ä', 'B'), $this->object->addColumnName('ä')->addColumnName('b')->getColumnNames());
    }

    /**
     * @test
     */
    public function testHasColumnNames()
    {
        $this->assertFalse($this->object->hasColumnNames());
        $this->assertTrue($this->object->addColumnName('test')->hasColumnNames());
    }

    /**
     * @test
     */
    public function testHasColumnName()
    {
        $this->assertFalse($this->object->addColumnName('Ä')->hasColumnName('b'));
        $this->assertTrue($this->object->addColumnName('b')->hasColumnName('B'));
        $this->assertTrue($this->object->hasColumnName('b'));
        $this->assertTrue($this->object->hasColumnName('ä'));
    }

}
