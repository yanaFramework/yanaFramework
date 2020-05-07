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
class ImplementationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Functions\Implementation
     */
    protected $object;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Functions\Implementation();
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
    public function testGetLanguage()
    {
        $this->assertNull($this->object->getLanguage());
    }

    /**
     * @test
     */
    public function testLanguage()
    {
        $this->assertSame('Language', $this->object->setLanguage('Language')->getLanguage());
        $this->assertNull($this->object->setLanguage('')->getLanguage());
    }

    /**
     * @test
     */
    public function testGetCode()
    {
        $this->assertNull($this->object->getCode());
    }

    /**
     * @test
     */
    public function testSetCode()
    {
        $this->assertEquals('Code', $this->object->setCode('Code')->getCode());
    }

    /**
     * @test
     */
    public function testGetReturn()
    {
        $this->assertNull($this->object->getReturn());
    }

    /**
     * @test
     */
    public function testSetReturn()
    {
        $this->assertSame('Return', $this->object->setReturn('Return')->getReturn());
        $this->assertNull($this->object->setReturn('')->getReturn());
    }

    /**
     * @test
     */
    public function testGetParameters()
    {
        $this->assertSame(array(), $this->object->getParameters());
    }

    /**
     * @test
     */
    public function testGetParametersWithValues()
    {
        $p1 = $this->object->addParameter('Test1');
        $p2 = $this->object->addParameter('test2');
        $p3 = $this->object->addParameter('tesT3');

        $this->assertSame(array('test1' => $p1, 'test2' => $p2, 'test3' => $p3), $this->object->getParameters());
    }

    /**
     * @test
     */
    public function testAddParameter()
    {
        $p = $this->object->addParameter('Test');
        $this->assertSame($p, $this->object->getParameter('tesT'));
    }

    /**
     * @test
     */
    public function testGetParameter()
    {
        $this->assertNull($this->object->getParameter('Test'));
        $p = $this->object->addParameter('Test');
        $this->assertSame($p, $this->object->getParameter('tesT'));
        $this->assertSame('test', $this->object->getParameter('tesT')->getName());
    }

    /**
     * @test
     */
    public function testGetParameterNames()
    {
        $this->object->addParameter('Test1');
        $this->object->addParameter('test2');
        $this->object->addParameter('tesT3');

        $this->assertEquals(array('test1', 'test2', 'test3'), $this->object->getParameterNames());
    }

    /**
     * @test
     */
    public function testDropParameter()
    {
        $this->object->addParameter('Test1');
        $this->object->addParameter('Test2');
        $this->object->addParameter('Test3');

        $valid = $this->object->getParameters();
        $this->assertArrayHasKey('test1', $valid);
        $this->assertArrayHasKey('test2', $valid);
        $this->assertArrayHasKey('test3', $valid);

        $this->object->dropParameter('tesT1');
        $this->assertArrayNotHasKey('test1', $this->object->getParameters());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     */
    public function testAddParameterAlreadyExistsException()
    {
        try {
            // supposed to succeed
            $this->object->addParameter('parameter');
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        // supposed to fail
        $this->object->addParameter('parameter');
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $xddl = '
            <implementation dbms="test1" language="Sql">
                <param name="p1" type="string"/>
                <param name="p2" type="int"/>
                <return>Integer</return>
                <code>Implementation</code>
            </implementation>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\Functions\Implementation::unserializeFromXDDL($node);
        $this->assertSame("test1", $this->object->getDBMS());
        $this->assertSame("Sql", $this->object->getLanguage());
        $this->assertSame("string", $this->object->getParameter('P1')->getType());
        $this->assertSame("int", $this->object->getParameter('p2')->getType());
        $this->assertSame("Integer", $this->object->getReturn());
        $this->assertSame("Implementation", $this->object->getCode());
    }

}

?>