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

namespace Yana\Db\Ddl\Views;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 */
class FieldTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Views\Field
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Views\Field("Test");
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
    public function testGetTable()
    {
        $this->assertNull($this->object->getTable());
    }

    /**
     * @test
     */
    public function testSetTable()
    {
        $this->assertEquals('Abcd', $this->object->setTable('Abcd')->getTable());
        $this->assertNull($this->object->setTable('')->getTable());
    }

    /**
     * @test
     */
    public function testGetAlias()
    {
        $this->assertNull($this->object->getAlias());
    }

    /**
     * @test
     */
    public function testSetAlias()
    {
        $this->assertEquals('Abcd', $this->object->setAlias('Abcd')->getAlias());
        $this->assertNull($this->object->setAlias('')->getAlias());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testUnserializeFromXDDLInvalidArgumentException()
    {
        $xddl = '<field table="Test" alias="Id"/>';
        $node = \simplexml_load_string($xddl);
        \Yana\Db\Ddl\Views\Field::unserializeFromXDDL($node);
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $xddl = '<field column="Test_id" table="Test" alias="Id"/>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\Views\Field::unserializeFromXDDL($node);
        $this->assertSame("test_id", $this->object->getName());
        $this->assertSame("Test", $this->object->getTable());
        $this->assertSame("Id", $this->object->getAlias());
    }

}
