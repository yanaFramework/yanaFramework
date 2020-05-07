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

namespace Yana\Db\Ddl\Functions;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';


/**
 * DDL test-case
 *
 * @package  test
 */
class ParameterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Functions\Parameter
     */
    protected $object;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Functions\Parameter('Parameter');
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
    public function testGetName()
    {
        $this->assertSame('parameter', $this->object->getName());
    }

    /**
     * @test
     */
    public function testGetType()
    {
        $this->assertSame("", $this->object->getType());
    }

    /**
     * @test
     */
    public function testSetType()
    {
        $this->assertSame('Integer', $this->object->setType('Integer')->getType());
        $this->assertSame('', $this->object->setType('')->getType());
    }

    /**
     * @test
     */
    public function testGetMode()
    {
        $this->assertSame(\Yana\Db\Ddl\Functions\ParameterTypeEnumeration::IN, $this->object->getMode());
    }

    /**
     * @test
     */
    public function testMode()
    {
        $this->assertSame(\Yana\Db\Ddl\Functions\ParameterTypeEnumeration::INOUT,
            $this->object->setMode(\Yana\Db\Ddl\Functions\ParameterTypeEnumeration::INOUT)->getMode());
        $this->assertSame(\Yana\Db\Ddl\Functions\ParameterTypeEnumeration::OUT,
            $this->object->setMode(\Yana\Db\Ddl\Functions\ParameterTypeEnumeration::OUT)->getMode());
        $this->assertSame(\Yana\Db\Ddl\Functions\ParameterTypeEnumeration::IN,
            $this->object->setMode(\Yana\Db\Ddl\Functions\ParameterTypeEnumeration::IN)->getMode());
        $this->assertSame(\Yana\Db\Ddl\Functions\ParameterTypeEnumeration::IN, $this->object->setMode(-1)->getMode());
    }

    /**
     * @test
     */
    public function testSerializeToXDDL()
    {
        $node = $this->object->serializeToXDDL();
        $this->assertContains('<param name="parameter" mode="in"/>', $node->asXml());
    }

    /**
     * @test
     */
    public function testSerializeToXDDLModeOut()
    {
        $node = $this->object->setMode(\Yana\Db\Ddl\Functions\ParameterTypeEnumeration::OUT)->serializeToXDDL();
        $this->assertContains('<param name="parameter" mode="out"/>', $node->asXml());
    }

    /**
     * @test
     */
    public function testSerializeToXDDLModeInOut()
    {
        $node = $this->object->setMode(\Yana\Db\Ddl\Functions\ParameterTypeEnumeration::INOUT)->serializeToXDDL();
        $this->assertContains('<param name="parameter" mode="inout"/>', $node->asXml());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testUnserializeFromXDDLInvalidArgumentExceptionName()
    {
        $xddl = '<param/>';
        $node = \simplexml_load_string($xddl);
        \Yana\Db\Ddl\Functions\Parameter::unserializeFromXDDL($node);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testUnserializeFromXDDLInvalidArgumentExceptionType()
    {
        $xddl = '<param name="P1"/>';
        $node = \simplexml_load_string($xddl);
        \Yana\Db\Ddl\Functions\Parameter::unserializeFromXDDL($node);
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $xddl = '
            <param name="P1" type="Integer" mode="in"/>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\Functions\Parameter::unserializeFromXDDL($node);
        $this->assertSame("p1", $this->object->getName());
        $this->assertSame("Integer", $this->object->getType());
        $this->assertSame(\Yana\Db\Ddl\Functions\ParameterTypeEnumeration::IN, $this->object->getMode());
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDLModeOut()
    {
        $xddl = '
            <param name="P1" type="Integer" mode="out"/>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\Functions\Parameter::unserializeFromXDDL($node);
        $this->assertSame(\Yana\Db\Ddl\Functions\ParameterTypeEnumeration::OUT, $this->object->getMode());
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDLModeInOut()
    {
        $xddl = '
            <param name="P1" type="Integer" mode="inout"/>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\Functions\Parameter::unserializeFromXDDL($node);
        $this->assertSame(\Yana\Db\Ddl\Functions\ParameterTypeEnumeration::INOUT, $this->object->getMode());
    }

}

?>