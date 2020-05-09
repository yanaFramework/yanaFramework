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
class ConstraintTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Constraint
     */
    protected $constraint;

    /**
     * sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->constraint = new \Yana\Db\Ddl\Constraint();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->constraint);
    }

    /**
     * @test
     */
    public function testGetConstraint()
    {
        $this->assertNull($this->constraint->getConstraint());
    }

    /**
     * @test
     */
    public function testSetConstraint()
    {
        $this->assertSame(__FUNCTION__, $this->constraint->setConstraint(__FUNCTION__)->getConstraint());
        $this->assertNull($this->constraint->setConstraint('')->getConstraint());
    }

    /**
     * @test
     */
    public function testGetDBMS()
    {
        $this->assertEquals(\Yana\Db\DriverEnumeration::GENERIC, $this->constraint->getDBMS());
    }

    /**
     * @test
     */
    public function testDBMS()
    {
        $this->assertSame('mssql', $this->constraint->setDBMS('MsSql')->getDBMS());
        $this->assertNull($this->constraint->setDBMS('')->getDBMS());
    }

    /**
     * @test
     */
    public function testUnserializeFromXDDL()
    {
        $xddl = '<constraint name="test" dbms="mysql">Constraint text</constraint>';
        $node = \simplexml_load_string($xddl);
        $this->object = \Yana\Db\Ddl\Constraint::unserializeFromXDDL($node);
        $this->assertSame('test', $this->object->getName());
        $this->assertSame('mysql', $this->object->getDbms());
        $this->assertSame('Constraint text', $this->object->getConstraint());
    }

}

?>