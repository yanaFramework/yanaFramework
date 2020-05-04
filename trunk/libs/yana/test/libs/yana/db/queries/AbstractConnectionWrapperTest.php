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
declare(strict_types=1);

namespace Yana\Db\Queries;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class AbstractConnectionWrapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Queries\AbstractConnectionWrapper
     */
    protected $database;

    /**
     * @var \Yana\Db\Queries\AbstractConnectionWrapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->database = new \Yana\Db\NullConnection();
        $this->object = new \Yana\Db\Queries\Sql($this->database, "");
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
    public function test__clone()
    {
        $this->assertNull($this->object->__clone());
        $clone = clone $this->object;
        $this->assertEquals($this->object, $clone);
        $this->assertNotSame($this->object, $clone);
        $this->assertSame($this->object->getDatabase(), $clone->getDatabase());
    }

    /**
     * @test
     */
    public function testGetDatabase()
    {
        $this->assertSame($this->database, $this->object->getDatabase());
    }

    /**
     * @test
     */
    public function testSerialize()
    {
        $array = unserialize($this->object->serialize());
        $this->assertArrayNotHasKey('_table', $array);
        $this->assertArrayHasKey('_db', $array);
        $this->assertSame('null', $array['_db']);
    }

    /**
     * @test
     */
    public function testUnserialize()
    {
        $this->assertNull($this->object->unserialize(serialize(array('_db' => 'check'))));
        $this->assertSame('check', $this->object->getDatabase()->getSchema()->getName());
    }

}
