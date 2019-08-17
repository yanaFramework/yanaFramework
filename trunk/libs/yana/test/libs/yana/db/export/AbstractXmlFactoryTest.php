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

namespace Yana\Db\Export;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyXmlFactory extends \Yana\Db\Export\AbstractXmlFactory
{
    
}

/**
 * @package  test
 */
class AbstractXmlFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Export\MyXmlFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Export\MyXmlFactory();
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
    public function testIsUsingForeignKeys()
    {
        $this->assertFalse($this->object->isUsingForeignKeys());
    }

    /**
     * @test
     */
    public function testGetDatabaseNames()
    {
        $this->assertSame(array(), $this->object->getDatabaseNames());
    }

    /**
     * @test
     */
    public function testSetUsingForeignKeys()
    {
        $this->assertTrue($this->object->setUsingForeignKeys(true)->isUsingForeignKeys());
        $this->assertFalse($this->object->setUsingForeignKeys(false)->isUsingForeignKeys());
    }

    /**
     * @test
     */
    public function testAddDatabaseName()
    {
        $this->assertSame(array(__FUNCTION__), $this->object->addDatabaseName(__FUNCTION__)->getDatabaseNames());
    }

    /**
     * @test
     */
    public function testSetDatabaseNames()
    {
        $this->assertSame(array('1', '2'), $this->object->setDatabaseNames(array('1', '2'))->getDatabaseNames());
    }

}
