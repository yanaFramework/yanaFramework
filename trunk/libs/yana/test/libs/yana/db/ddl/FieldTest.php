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
class FieldTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Field
     */
    protected $object;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Field('field');
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
     * Data-provider for testDescription
     */
    public function dataDescription()
    {
        return array(
            array('field'),
            array('database'),
            array('function'),
            array('table'),
            array('sequence')
        );
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
        $this->assertEquals('Description', $this->object->setDescription('Description')->getDescription());
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
       $this->assertTrue($this->object->setReadonly(true)->isReadonly());
    }

    /**
     * @test
     */
    public function testIsSelectable()
    {
       $this->assertTrue($this->object->isSelectable());
    }

    /**
     * @test
     */
    public function testIsInsertable()
    {
       $this->assertTrue($this->object->isInsertable());
    }

    /**
     * @test
     */
    public function testIsUpdatable()
    {
       $this->assertTrue($this->object->isUpdatable());
    }

    /**
     * @test
     */
    public function testIsDeletable()
    {
       $this->assertTrue($this->object->isDeletable());
    }

    /**
     * @test
     */
    public function testIsGrantable()
    {
       $this->assertTrue($this->object->isGrantable());
    }

    /**
     * @test
     */
    public function testIsVisible()
    {
       $this->assertTrue($this->object->isVisible());
    }

    /**
     * @test
     */
    public function testSetVisible()
    {
       $this->assertTrue($this->object->setVisible(true)->isVisible());
       $this->assertFalse($this->object->setVisible(false)->isVisible());
       $this->assertTrue($this->object->setVisible(true)->isVisible());
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
        $this->assertEquals('Abcd', $this->object->setTitle('Abcd')->getTitle());
        $this->assertNull($this->object->setTitle('')->getTitle());
    }

    /**
     * @test
     */
    public function testGetColumn()
    {
        $this->assertNull($this->object->getColumn());
    }

    /**
     * @test
     */
    public function testGetEvent()
    {
        $this->assertNull($this->object->getEvent('no-such-event'));
    }

    /**
     * @test
     */
    public function testGetEvents()
    {
        $this->assertSame(array(), $this->object->getEvents());
    }

    /**
     * @test
     */
    public function testAddEvent()
    {
        $addedEvent = $this->object->addEvent('Test');
        $this->assertSame($addedEvent, $this->object->getEvent('tesT'));
        $this->assertSame(array('test' => $addedEvent), $this->object->getEvents());
    }

    /**
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     * @test
     */
    public function testAddEventInvalidArgumentException()
    {
        $this->object->addEvent('');
    }

    /**
     * @expectedException \Yana\Core\Exceptions\AlreadyExistsException
     * @test
     */
    public function testAddEventAlreadyExistsException()
    {
        $this->object->addEvent('Test');
        $this->object->addEvent('tesT');
    }

    /**
     * @test
     */
    public function testDropEvent()
    {
        $addedEvent = $this->object->addEvent('Test');
        $this->assertSame($addedEvent, $this->object->getEvent('tesT'));
        $this->assertTrue($this->object->dropEvent('tesT'));
        $this->assertFalse($this->object->dropEvent('tesT'));
        $this->assertNull($this->object->getEvent('tesT'));
        $this->assertSame(array(), $this->object->getEvents());
        $this->assertNull($this->object->getEvent('tesT'));
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
    public function testSetGrants()
    {
        $grant1 = new \Yana\Db\Ddl\Grant();
        $grant2 = new \Yana\Db\Ddl\Grant();

        $grants = array($grant1, $grant2);

        $this->assertSame($this->object, $this->object->setGrant($grant1));
        $this->assertSame($this->object, $this->object->setGrant($grant2));

        $this->assertSame($grants, $this->object->getGrants());
    }

    /**
     * @test
     */
    public function testAddGrant()
    {
        $grant = new \Yana\Db\Ddl\Grant();

        $this->object->setGrant($grant);
        $addedGrant = $this->object->addGrant('user', 'role', 10);

        $this->assertSame(array($grant, $addedGrant), $this->object->getGrants());
    }

    /**
     * @test
     */
    public function testDropGrants()
    {
        $this->object->setGrant(new \Yana\Db\Ddl\Grant());
        $this->object->setGrant(new \Yana\Db\Ddl\Grant());
        $this->assertNull($this->object->dropGrants());
        $this->assertSame(array(), $this->object->getGrants());
    }

    /**
     * Css class
     *
     * @test
     */
    public function testGetCssClass()
    {
        $this->assertNull($this->object->getCssClass());
    }

    /**
     * @test
     */
    public function testCssClass()
    {
        $this->assertEquals('CssClass', $this->object->setCssClass('CssClass')->getCssClass());
        $this->assertNull($this->object->setCssClass('')->getCssClass());
    }

    /**
     * @test
     */
    public function testGetTabIndex()
    {
        $this->assertNull($this->object->getTabIndex());
    }

    /**
     * @test
     */
    public function testTabIndex()
    {
        $this->assertEquals(4, $this->object->setTabIndex(4)->getTabIndex());
        $this->assertNull($this->object->setTabIndex()->getTabIndex());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testUnserializeFromXDDLInvalidArgumentExceptionName()
    {
        \Yana\Db\Ddl\Form::unserializeFromXDDL(new \SimpleXmlElement('<input/>'));
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDLWithoutChildren()
    {
        $field = \Yana\Db\Ddl\Field::unserializeFromXDDL(new \SimpleXmlElement('<input name="MyField"/>'));
        $this->assertSame("myfield", $field->getName());
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $xddl = '
        <input name="MyField" readonly="yes">
            <description>Test</description>
            <string name="author" readonly="yes" notnull="no" length="80" unique="no">
              <description>Test2</description>
              <grant role="2" user="1" level="5" select="no" insert="yes" update="no" delete="no" grant="no"/>
            </string>
            <grant role="2" user="1" level="5" select="no" insert="yes" update="no" delete="no" grant="no"/>
            <event name="testevent"/>
        </input>';
        $field = \Yana\Db\Ddl\Field::unserializeFromXDDL(new \SimpleXmlElement($xddl));
        $this->assertSame("myfield", $field->getName());
        $this->assertTrue($field->isReadonly());
        $this->assertSame("Test", $field->getDescription());
        $this->assertSame("Test2", $field->getColumn()->getDescription());
        $this->assertSame(5, $field->getGrants()[0]->getLevel());
        $this->assertSame("testevent", $field->getEvent('TestEvent')->getName());
    }

}

?>