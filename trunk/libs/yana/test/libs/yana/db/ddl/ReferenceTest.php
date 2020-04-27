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

namespace Yana\Db\Ddl;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';


/**
 * @package  test
 */
class ReferenceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Reference
     */
    protected $table = "TableTest";

    /**
     * @var \Yana\Db\Ddl\Reference
     */
    protected $column = "ColumnTest";

    /**
     * @var \Yana\Db\Ddl\Reference
     */
    protected $label = "LabelTest";

    /**
     * @var \Yana\Db\Ddl\Reference
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\Reference($this->table, $this->column, $this->label);
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
        $this->assertSame($this->table, $this->object->getTable());
    }

    /**
     * @test
     */
    public function testGetColumn()
    {
        $this->assertSame($this->column, $this->object->getColumn());
    }

    /**
     * @test
     */
    public function testGetLabel()
    {
        $this->assertSame($this->label, $this->object->getLabel());
    }

    /**
     * @test
     */
    public function testSetTable()
    {
        $this->assertSame(__FUNCTION__, $this->object->setTable(__FUNCTION__)->getTable());
    }

    /**
     * @test
     */
    public function testSetColumn()
    {
        $this->assertSame(__FUNCTION__, $this->object->setColumn(__FUNCTION__)->getColumn());
    }

    /**
     * @test
     */
    public function testSetLabel()
    {
        $this->assertSame(__FUNCTION__, $this->object->setLabel(__FUNCTION__)->getLabel());
    }

}
