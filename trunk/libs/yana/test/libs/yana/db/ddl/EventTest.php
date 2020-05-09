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
class EventTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Event
     */
    protected $object;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Event('action');
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
        $this->assertEquals(__FUNCTION__, $this->object->setTitle(__FUNCTION__)->getTitle());
        $this->assertNull($this->object->setTitle()->getTitle());
    }

    /**
     * @test
     */
    public function testGetAction()
    {
        $this->assertSame("", $this->object->getAction());
    }

    /**
     * @test
     */
    public function testSetAction()
    {
        $this->assertSame('Action', $this->object->setAction('Action')->getAction());
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
    public function testSetLanguage()
    {
        $this->assertSame(__FUNCTION__, $this->object->setLanguage(__FUNCTION__)->getLanguage());
        $this->assertNull($this->object->setLanguage()->getLanguage());
    }

    /**
     * @test
     */
    public function testGetLabel()
    {
        $this->assertNull($this->object->getLabel());
    }

    /**
     * @test
     */
    public function testSetLabel()
    {
        $this->assertEquals(__FUNCTION__, $this->object->setLabel(__FUNCTION__)->getLabel());
        $this->assertNull($this->object->setLabel()->getLabel());
    }

    /**
     * @test
     */
    public function testGetIcon()
    {
        $this->assertNull($this->object->getIcon());
    }

    /**
     * @test
     */
    public function testSetIcon()
    {
        $icon = CWD . 'resources/image/logo.png';
        $this->assertSame($icon, $this->object->setIcon($icon)->getIcon());
        $this->assertNull($this->object->setIcon()->getIcon());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testUnserializeFromXDDLInvalidArgumentException()
    {
        $data = "<event/>";
        \Yana\Db\Ddl\Event::unserializeFromXDDL(new \SimpleXMLElement($data));
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $data = '<event name="Test"/>';
        $this->object = \Yana\Db\Ddl\Event::unserializeFromXDDL(new \SimpleXMLElement($data));
        $this->assertSame('test', $this->object->getName());
        $this->assertSame('', $this->object->getAction());
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDLWithAttributes()
    {
        $data = '<event name="Test" language="Language" title="Title" label="Label" icon="Icon">Action</event>';
        $this->object = \Yana\Db\Ddl\Event::unserializeFromXDDL(new \SimpleXMLElement($data));
        $this->assertSame('test', $this->object->getName());
        $this->assertSame('Language', $this->object->getLanguage());
        $this->assertSame('Title', $this->object->getTitle());
        $this->assertSame('Label', $this->object->getLabel());
        $this->assertSame('Icon', $this->object->getIcon());
        $this->assertSame('Action', $this->object->getAction());
    }

}

?>