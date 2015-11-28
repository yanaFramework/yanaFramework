<?php
/**
 * PHPUnit test-case: DbStructureGenerics
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

namespace Yana\Db\Helpers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Test class for DbStructureGenerics
 *
 * @package  test
 */
class ConstraintCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Db\Helpers\ConstraintCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Db\Helpers\ConstraintCollection(array(), $row = array('ID' => 'foo', 'VALUE' => 'bar'));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * checkConstraint
     *
     * @test
     */
    public function testCheckConstraint()
    {
        $object = new \Yana\Db\Ddl\Constraint(__FUNCTION__);
        $object->setConstraint('true');
        $this->object[] = $object;

        $this->assertTrue($this->object->__invoke(), 'assert failed, check constraint is valid');
    }

    /**
     * checkConstraint
     *
     * @test
     */
    public function testCheckConstraint1()
    {
        $object = new \Yana\Db\Ddl\Constraint(__FUNCTION__);
        $object->setConstraint('null');
        $this->object[] = $object;

        $this->assertFalse($this->object->__invoke(), 'assert failed,  check constraint is not valid');
    }

    /**
     * checkConstraint
     *
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testCheckConstraint2()
    {
        $object = new \Yana\Db\Ddl\Constraint(__FUNCTION__);
        $object->setConstraint('select');
        $this->object[] = $object;

        $this->assertFalse($this->object->__invoke(), 'assert failed,  check constraint is not valid');
    }

    /**
     * checkConstraint
     *
     * @test
     */
    public function testCheckConstraint3()
    {
        $object = new \Yana\Db\Ddl\Constraint(__FUNCTION__);
        $object->setConstraint('"bar" === "bar"');
        $this->object[] = $object;

        $this->assertTrue($this->object->__invoke(), 'assert failed,  check constraint is not valid');
    }
}

/**
 * class checktrigger
 *
 * used for check if the trigger methods are executed
 *
 * @ignore
 */
class checktrigger
{
    protected static $value = 0;

    public static function write()
    {
        checktrigger::$value = 1;
    }

    public static function read()
    {
        $result = checktrigger::$value;
        checktrigger::$value = 0;
        return $result;
    }
}
?>