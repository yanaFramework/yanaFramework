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
 * DDL test-case
 *
 * @package  test
 */
class DatabaseInitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\DatabaseInit
     */
    protected $object;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Ddl\DatabaseInit();
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
    public function testGetSQL()
    {
        $this->assertNull($this->object->getSQL());
    }

    /**
     * @test
     */
    public function testSQL()
    {
        $this->assertEquals('Sql', $this->object->setSQL('Sql')->getSQL());
        $this->assertNull($this->object->setSQL('')->getSQL());
    }

    /**
     * @test
     */
    public function testGetDBMS()
    {
        $this->assertSame('generic', $this->object->getDBMS());
    }

    /**
     * @test
     */
    public function testDBMS()
    {
        $this->assertSame('mssql', $this->object->setDBMS('MsSql')->getDBMS());
        $this->assertEquals('generic', $this->object->setDBMS()->getDBMS());
        $this->assertNull($this->object->setDBMS('')->getDBMS());
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $xddl = '<initialization dbms="mysql">Sql</initialization>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\DatabaseInit::unserializeFromXDDL($node);
        $this->assertSame('mysql', $this->object->getDbms());
        $this->assertSame('Sql', $this->object->getSQL());
    }

}

?>