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
class DefinitionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Functions\Definition
     */
    protected $object;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Functions\Definition('function');
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
    public function testGetTitle()
    {
        $this->assertNull($this->object->getTitle());
    }

    /**
     * @test
     */
    public function testSetTitle()
    {
        $this->assertEquals("some Title", $this->object->setTitle("some Title")->getTitle());
        $this->assertNull($this->object->setTitle()->getTitle());
    }

    /**
     * @test
     */
    public function testGetDescription()
    {
        $this->assertNull($this->object->getDescription());
    }

    /**
     * @test
     */
    public function testSetDescription()
    {
        $this->assertEquals("some Description", $this->object->setDescription("some Description")->getDescription());
        $this->assertNull($this->object->setDescription("")->getDescription());
    }

    /**
     * @test
     */
    public function testGetImplementation()
    {
       $this->assertNull($this->object->getImplementation());
       $this->assertNull($this->object->getImplementation("mysql"));
       $this->assertNull($this->object->getImplementation("no-such-dbms"));
    }

    /**
     * @test
     */
    public function testGetImplementations()
    {
       $this->assertSame(array(), $this->object->getImplementations());
    }

    /**
     * @test
     */
    public function testSetImplementation()
    {
       $this->assertNull($this->object->getImplementation("mysql"));

       $f1 = $this->object->addImplementation('mysql');
       $f2 = $this->object->addImplementation('oracle');

       $expected = array(
           'mysql' => $f1,
           'oracle' => $f2
        );
       $this->assertCount(2, $this->object->getImplementations());
       $this->assertSame($expected, $this->object->getImplementations());
       $this->assertSame($f1, $this->object->getImplementation("mysql"));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     */
    public function testImplementationAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->object->addImplementation();
        } catch (\Yana\Core\Exceptions\AlreadyExistsException $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->object->addImplementation();
    }

    /**
     * @test
     */
    public function testAddImplementation()
    {
        $implementation1 = $this->object->addImplementation('MsSqL');
        $this->assertEquals('mssql', $implementation1->getDBMS());

        $implementation2 = $this->object->addImplementation();
        $this->assertEquals('generic', $implementation2->getDBMS());
        $this->assertSame(array('mssql' => $implementation1, 'generic' => $implementation2), $this->object->getImplementations());
    }

    /**
     * parent
     *
     * @test
     */
    public function testParent()
    {
        $database = new \Yana\Db\Ddl\Database();

        $childFunction = new \Yana\Db\Ddl\Functions\Definition('function', $database);
        $parentFunction = $childFunction->getParent();
        $this->assertEquals($database, $parentFunction, '\Yana\Db\Ddl\Functions\Definition::getParent, the values should be equal');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testUnserializeFromXDDLInvalidArgumentException()
    {
        \Yana\Db\Ddl\Functions\Definition::unserializeFromXDDL(new \SimpleXmlElement('<function/>'));
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $xddl = '
            <function name="myFunction" title="myTitle">
                <description>Description</description>
                <implementation dbms="test1"><code>Implementation1</code></implementation>
                <implementation dbms="test2"><code>Implementation2</code></implementation>
                <implementation><code>Implementation3</code></implementation>
                <implementation><code>Implementation4</code></implementation>
            </function>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\Functions\Definition::unserializeFromXDDL($node);
        $this->assertSame("myfunction", $this->object->getName());
        $this->assertSame("myTitle", $this->object->getTitle());
        $this->assertSame("Description", $this->object->getDescription());
        $this->assertSame("Implementation1", $this->object->getImplementation("Test1")->getCode());
        $this->assertSame("Implementation2", $this->object->getImplementation("Test2")->getCode());
        $this->assertSame("Implementation4", $this->object->getImplementation()->getCode());
    }

}

?>