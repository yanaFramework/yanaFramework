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
class ViewTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Views\View
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Views\View("Test");
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
    public function testGetParent()
    {
        $this->assertNull($this->object->getParent());
        $parent = new \Yana\Db\Ddl\Database("Database");
        $this->object = new \Yana\Db\Ddl\Views\View("Test", $parent);
        $this->assertSame($parent, $this->object->getParent());
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
        $this->assertEquals(__FUNCTION__, $this->object->setTitle(__FUNCTION__)->getTitle());
        $this->assertNull($this->object->setTitle('')->getTitle());
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
        $this->assertEquals(__FUNCTION__, $this->object->setDescription(__FUNCTION__)->getDescription());
        $this->assertNull($this->object->setDescription('')->getDescription());
    }

    /**
     * @test
     */
    public function testIsReadonly()
    {
       $this->assertFalse($this->object->isReadonly());
    }

    /**
     * @test
     */
    public function testSetReadonly()
    {
       $this->assertTrue($this->object->setReadonly(true)->isReadonly());
       $this->assertFalse($this->object->setReadonly(false)->isReadonly());
    }

    /**
     * @test
     */
    public function testHasCheckOption()
    {
        $this->assertFalse($this->object->hasCheckOption());
    }

    /**
     * @test
     */
    public function testGetCheckOption()
    {
        $this->assertSame(\Yana\Db\Ddl\Views\ConstraintEnumeration::NONE, $this->object->getCheckOption());
    }

    /**
     * @test
     */
    public function testSetCheckOption()
    {
        $result = $this->object->setCheckOption(\Yana\Db\Ddl\Views\ConstraintEnumeration::CASCADED)->getCheckOption();
        $this->assertSame(\Yana\Db\Ddl\Views\ConstraintEnumeration::CASCADED, $result);
        $this->assertTrue($this->object->hasCheckOption());
    }

    /**
     * @test
     */
    public function testSetCheckOptionLocal()
    {
        $result = $this->object->setCheckOption(\Yana\Db\Ddl\Views\ConstraintEnumeration::LOCAL)->getCheckOption();
        $this->assertEquals(\Yana\Db\Ddl\Views\ConstraintEnumeration::LOCAL, $result);
    }

    /**
     * @test
     */
    public function testSetCheckOptionNone()
    {
        $result = $this->object->setCheckOption(\Yana\Db\Ddl\Views\ConstraintEnumeration::NONE)->getCheckOption();
        $this->assertEquals(\Yana\Db\Ddl\Views\ConstraintEnumeration::NONE, $result);
    }

    /**
     * @test
     */
    public function testSetCheckOptionString()
    {
        $result = $this->object->setCheckOption((string) \Yana\Db\Ddl\Views\ConstraintEnumeration::LOCAL)->getCheckOption();
        $this->assertEquals(\Yana\Db\Ddl\Views\ConstraintEnumeration::LOCAL, $result);
    }


    /**
     * @test
     */
    public function testSetCheckOptionInvalid()
    {
        $result = $this->object->setCheckOption(-1)->getCheckOption(); // Must return default
        $this->assertEquals(\Yana\Db\Ddl\Views\ConstraintEnumeration::NONE, $result);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetFieldNotFoundException()
    {
        $this->object->getField("no-such-field");
    }

    /**
     * @test
     */
    public function testGetFields()
    {
        $this->assertSame(array(), $this->object->getFields());
    }

    /**
     * add field to view
     *
     * @test
     */
    public function testAddViewField()
    {
        $get = $this->object->getFields();
        $this->assertEquals(0, count($get), 'assert failed, the values should be equal, no fields found - "0" expected');

        $this->object->addField('name');
        $this->object->addField('abcd');
        $this->object->addField('qwerty');

        $get = $this->object->getFields();
        $this->assertInternalType('array', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : the value is not from type array');

        $this->assertArrayHasKey('name', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : expected true - the value should be match a key in array');
        $this->assertArrayHasKey('abcd', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : expected true - the value should be match a key in array');
        $this->assertArrayHasKey('qwerty', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : expected true - the value should be match a key in array');

        $get = $this->object->getField('abcd');
        $this->assertInternalType('object', $get, 'assert failed, "\Yana\Db\Ddl\Views\View" : the value is not from type object');
        $this->assertTrue($get instanceof \Yana\Db\Ddl\Views\Field, 'assert failed, "\Yana\Db\Ddl\Views\View" : the value should be an instance of \Yana\Db\Ddl\Views\Field');

        $this->object->dropField('abcd');
        try {
            $get = $this->object->getField('abcd');
            $this->fail("\Yana\Db\Ddl\Views\View::dropField didn't drop the Column");
        } catch (\Exception $e) {
            //success
        }
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testAddFieldInvalidArgumentException()
    {
        $this->object->addField('');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     */
    public function testAddFieldAlreadyExistsException()
    {
        $this->object->addField('test');
        $this->object->addField('test');
    }

    /**
     * @test
     */
    public function testDropField()
    {
        $this->object->addField('Test');
        $this->assertNull($this->object->dropField('Test'));
        $this->assertSame(array(), $this->object->getFields());
    }

    /**
     * @test
     */
    public function testGetQuery()
    {
        $this->assertNull($this->object->getQuery());
    }

    /**
     * @test
     */
    public function testGetQueries()
    {
        $this->assertSame(array(), $this->object->getQueries());
    }

    /**
     * @test
     */
    public function testSetQuery()
    {
       $this->assertSame(array(), $this->object->setQuery('')->getQueries());

       $this->assertNull($this->object->getQuery('mysql'));

       $this->assertSame('Query', $this->object->setQuery('Query', 'mysql')->getQuery('mysql'));
       $this->assertSame(array('mysql' => 'Query', 'generic' => 'Query2'), $this->object->setQuery('Query2', 'generic')->getQueries());
    }

    /**
     * @test
     */
    public function testDropQuery()
    {
        $this->object->setQuery("genericQuery");
        $this->object->setQuery("mysqlQuery", "mysql");
        $result = $this->object->getQueries();
        $this->assertTrue(count($result) == 2, '\Yana\Db\Ddl\Views\View::getQueries should return two different Query-Types');
        $result = $this->object->getQuery();
        $this->assertTrue(count($result) == 1, '\Yana\Db\Ddl\Views\View::getQueries should return the generic Query');
        $result = $this->object->getQuery('oracle');
        $this->assertNull($result, '\Yana\Db\Ddl\Views\View::getQueries should return no query because for this dbms there had been no query set');

        $this->object->dropQuery('mysql');
        $result = $this->object->getQueries();
        $this->assertTrue(count($result) == 1, '\Yana\Db\Ddl\Views\View::dropQueries should have dropped one of the Query-Types');
    }

    /**
     * @test
     */
    public function testGetTables()
    {
        $this->assertSame(array(), $this->object->getTables());
    }

    /**
     * @test
     */
    public function testSetTables()
    {
        $array = array('one', 'two');
        $this->assertEquals($array, $this->object->setTables($array)->getTables());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testSetTablesNotFoundException()
    {
        $parent = new \Yana\Db\Ddl\Database("Database"); // has no tables
        $this->object = new \Yana\Db\Ddl\Views\View("Test", $parent);
        $this->object->setTables(array('no-such-table'));
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetTablesInvalidArgumentException()
    {
        $this->object->setTables(array());
    }

    /**
     * @test
     */
    public function testGetWhere()
    {
        $this->assertNull($this->object->getWhere());
    }

    /**
     * @test
     */
    public function testSetWhere()
    {
        $this->assertEquals(__FUNCTION__, $this->object->setWhere(__FUNCTION__)->getWhere());
        $this->assertNull($this->object->setWhere('')->getWhere());
    }

    /**
     * @test
     */
    public function testGetOrderBy()
    {
        $this->assertSame(array(), $this->object->getOrderBy());
    }

    /**
     * @test
     */
    public function testSetOrderBy()
    {
        $array = array();

        $this->object->setOrderBy(array('qwerty'));
        $get = $this->object->getOrderBy();
        $this->assertEquals(array('qwerty'), $get, 'assert failed, the values should be equal, "\Yana\Db\Ddl\Views\View" :the arrays should be match each other');
        $isDesc = $this->object->isDescendingOrder();
        $this->assertFalse($isDesc, 'assert failed, "\Yana\Db\Ddl\Views\View" : expected false, no descendingOrder is set');

        $this->object->setOrderBy($array, true);
        $get = $this->object->getOrderBy();
        $this->assertEquals(0, count($get), 'assert failed, the values should be equal, "\Yana\Db\Ddl\Views\View" :the array should be match each other');
        $isDesc = $this->object->isDescendingOrder();
        $this->assertTrue($isDesc, 'assert failed, "\Yana\Db\Ddl\Views\View" : expected true, descendingOrder is set');
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
    public function testGetGrants()
    {
        $this->assertSame(array(), $this->object->getGrants());
    }

    /**
     * @test
     */
    public function testDropGrants()
    {
        $grant = $this->object->addGrant('User', 'Role', 10);
        $this->assertSame(array($grant), $this->object->getGrants());
        $this->assertNull($this->object->dropGrants());
        $this->assertSame(array(), $this->object->getGrants());
    }

    /**
     * @test
     */
    public function testSetGrant()
    {
        $grant = new \Yana\Db\Ddl\Grant();
        $grant->setUser('User')->setRole('Role')->setLevel(10);
        $this->assertSame(array($grant), $this->object->setGrant($grant)->getGrants());
    }

    /**
     * @test
     */
    public function testAddGrant()
    {
        $grant = $this->object->addGrant('User', 'Role', 10);
        $this->assertSame('User', $grant->getUser());
        $this->assertSame('Role', $grant->getRole());
        $this->assertSame(10, $grant->getLevel());
    }

    /**
     * @test
     */
    public function test__get()
    {
        $this->object->addField('magicViewfield');
        $result = $this->object->magicViewfield;
        $this->assertTrue($result instanceof \Yana\Db\Ddl\Views\Field, 'assert failed, expected null - view field was dropt before');
    }

    /**
     * @test
     */
    public function testSerializeToXDDL()
    {
        $node = $this->object->serializeToXDDL();
        $this->assertContains('<view name="test" sorting="ascending" checkoption="none"/>', $node->asXml());
    }

    /**
     * @test
     */
    public function testSerializeToXDDLLocal()
    {
        $this->object->setCheckOption(\Yana\Db\Ddl\Views\ConstraintEnumeration::LOCAL);
        $node = $this->object->serializeToXDDL();
        $this->assertContains('<view name="test" sorting="ascending" checkoption="local"/>', $node->asXml());
    }

    /**
     * @test
     */
    public function testSerializeToXDDLCascaded()
    {
        $this->object->setCheckOption(\Yana\Db\Ddl\Views\ConstraintEnumeration::CASCADED);
        $node = $this->object->serializeToXDDL();
        $this->assertContains('<view name="test" sorting="ascending" checkoption="cascaded"/>', $node->asXml());
    }

    /**
     * @test
     */
    public function testSerializeToXDDLDescending()
    {
        $this->object->setOrderBy(array(), true);
        $node = $this->object->serializeToXDDL();
        $this->assertContains('<view name="test" sorting="descending" checkoption="none"/>', $node->asXml());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testUnserializeFromXDDLInvalidArgumentException()
    {
        $xddl = '
  <view readonly="yes" tables="Test" where="Test_id > 5" orderby="test" sorting="descending" checkoption="none">
    <description>test</description>
    <grant role="2" user="1" level="5" select="no" insert="yes" update="no" delete="no" grant="no"/>
    <field column="test_id" table="test" alias="id"/>
    <field column="test_title" table="test" alias="bar"/>
    <select dbms="mysql">Select Test_title as bar, Test_id as id from Test where Test_id > 5</select>
  </view>';
        $node = \simplexml_load_string($xddl);
        \Yana\Db\Ddl\Views\View::unserializeFromXDDL($node);
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $xddl = '
  <view name="test_view" readonly="yes" tables="Test" where="Test_id > 5" orderby="test" sorting="descending" checkoption="none">
    <description>test</description>
    <grant role="2" user="1" level="5" select="no" insert="yes" update="no" delete="no" grant="no"/>
    <field column="test_id" table="test" alias="id"/>
    <field column="test_title" table="test" alias="bar"/>
    <select dbms="mysql">Select Test_title as bar, Test_id as id from Test where Test_id > 5</select>
  </view>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\Views\View::unserializeFromXDDL($node);
        $this->assertSame("test_view", $this->object->getName());
        $this->assertTrue($this->object->isReadonly());
        $this->assertSame("Test_id > 5", $this->object->getWhere());
        $this->assertSame(array("test"), $this->object->getOrderBy());
        $this->assertTrue($this->object->isDescendingOrder());
        $this->assertSame(\Yana\Db\Ddl\Views\ConstraintEnumeration::NONE, $this->object->getCheckOption());
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDLWithLocal()
    {
        $xddl = '
  <view name="test_view" readonly="yes" tables="Test" where="Test_id > 5" orderby="test" sorting="descending" checkoption="local">
    <description>test</description>
    <grant role="2" user="1" level="5" select="no" insert="yes" update="no" delete="no" grant="no"/>
    <field column="test_id" table="test" alias="id"/>
    <field column="test_title" table="test" alias="bar"/>
    <select dbms="mysql">Select Test_title as bar, Test_id as id from Test where Test_id > 5</select>
  </view>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\Views\View::unserializeFromXDDL($node);
        $this->assertSame(\Yana\Db\Ddl\Views\ConstraintEnumeration::LOCAL, $this->object->getCheckOption());
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDLWithCascaded()
    {
        $xddl = '
  <view name="test_view" readonly="yes" tables="Test" where="Test_id > 5" orderby="test" sorting="descending" checkoption="cascaded">
    <description>test</description>
    <grant role="2" user="1" level="5" select="no" insert="yes" update="no" delete="no" grant="no"/>
    <field column="test_id" table="test" alias="id"/>
    <field column="test_title" table="test" alias="bar"/>
    <select dbms="mysql">Select Test_title as bar, Test_id as id from Test where Test_id > 5</select>
  </view>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\Views\View::unserializeFromXDDL($node);
        $this->assertSame(\Yana\Db\Ddl\Views\ConstraintEnumeration::CASCADED, $this->object->getCheckOption());
    }

}
