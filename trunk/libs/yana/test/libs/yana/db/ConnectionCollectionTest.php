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

namespace Yana\Db;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class ConnectionCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\ConnectionCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\ConnectionCollection();
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $o = new \Yana\Db\FileDb\Connection(new \Yana\Db\Ddl\Database());
        $this->object->offsetSet('test', $o);
        $this->assertTrue($this->object['test'] instanceof \Yana\Db\FileDb\Connection, 'Instance was not added.');
        $this->assertEquals($this->object['test']->getName(), $o->getName());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $this->object[] = new \Yana\Core\Object();
    }

}
