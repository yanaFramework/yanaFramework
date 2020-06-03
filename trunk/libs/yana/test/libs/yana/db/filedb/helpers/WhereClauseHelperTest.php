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

namespace Yana\Db\FileDb\Helpers;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../../include.php';

/**
 * @package  test
 */
class WhereClauseHelperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Db\Ddl\Database
     */
    protected $schema;

    /**
     * @var \Yana\Db\Ddl\Table
     */
    protected $baseTable;

    /**
     * @var \Yana\Db\FileDb\Helpers\WhereClauseHelper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->schema = new \Yana\Db\Ddl\Database('check');
        $this->baseTable = $this->schema->addTable('t');
        $this->baseTable->addColumn('a', 'integer');
        $this->object = new \Yana\Db\FileDb\Helpers\WhereClauseHelper($this->schema, $this->baseTable);
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
    public function test__invoke()
    {
        $this->assertTrue($this->object->__invoke(array(), array()));
    }

    /**
     * @test
     * @expectedException \Yana\Db\Queries\Exceptions\TableNotFoundException
     */
    public function test__invokeTableNotFoundException()
    {
        $rows = array('A' => 1);
        $where = array(array('x', 'a'), \Yana\Db\Queries\OperatorEnumeration::EQUAL, 1);
        $this->object->__invoke($rows, $where);
    }

    /**
     * @test
     */
    public function test__invokeUnknownOperator()
    {
        $rows = array('A' => 1);
        $where = array('a', ':-P', 1);
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeNotScalar()
    {
        $rows = array('A' => array('b' => 123));
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::EQUAL, '<b>123</b>' . "\n");
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeEquals()
    {
        $rows = array('A' => 1);
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::EQUAL, 1);
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeEqualsWithTableName()
    {
        $rows = array('A' => 1);
        $where = array(array('t', 'a'), \Yana\Db\Queries\OperatorEnumeration::EQUAL, 1);
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeEqualsWithPrimaryKey()
    {
        $this->baseTable->setPrimaryKey('a');
        $rows = array('A' => 'Abc');
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::EQUAL, 'abC');
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeIsNull()
    {
        $rows = array('A' => 1);
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::EQUAL, null);
        $this->assertFalse($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeIgnoreTable()
    {
        $rows = array('A' => 1);
        $where = array('b', \Yana\Db\Queries\OperatorEnumeration::EQUAL, 123);
        $this->assertTrue($this->object->__invoke($rows, $where, $this->baseTable));
    }

    /**
     * @test
     */
    public function test__invokeIsNull2()
    {
        $rows = array();
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::EQUAL, null);
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeExists()
    {
        $rows = array('A' => 1);
        $connection = new \Yana\Db\FileDb\NullConnection($this->schema);
        $query = new \Yana\Db\Queries\SelectExist($connection);
        $query->setTable('t');
        $where = array('', \Yana\Db\Queries\OperatorEnumeration::EXISTS, $query);
        $this->assertFalse($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeNotExists()
    {
        $rows = array('A' => 1);
        $connection = new \Yana\Db\FileDb\NullConnection($this->schema);
        $query = new \Yana\Db\Queries\SelectExist($connection);
        $query->setTable('t');
        $where = array('', \Yana\Db\Queries\OperatorEnumeration::NOT_EXISTS, $query);
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeGreaterOrEqual()
    {
        $rows = array('A' => 1);
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::GREATER_OR_EQUAL, 1);
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeGreater()
    {
        $rows = array('A' => 1);
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::GREATER, 1);
        $this->assertFalse($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeLess()
    {
        $rows = array('A' => 1);
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::LESS, 1);
        $this->assertFalse($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeLessOrEqual()
    {
        $rows = array('A' => 1);
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::LESS_OR_EQUAL, 1);
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeAnd()
    {
        $rows = array('A' => 1);
        $where = array(
            array('a', \Yana\Db\Queries\OperatorEnumeration::GREATER_OR_EQUAL, 1),
            \Yana\Db\Queries\OperatorEnumeration::AND,
            array('a', \Yana\Db\Queries\OperatorEnumeration::LESS_OR_EQUAL, 1)
        );
        $this->assertTrue($this->object->__invoke($rows, $where));
        $where = array(
            array('a', \Yana\Db\Queries\OperatorEnumeration::GREATER, 0),
            \Yana\Db\Queries\OperatorEnumeration::AND,
            array('a', \Yana\Db\Queries\OperatorEnumeration::LESS, 0)
        );
        $this->assertFalse($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeOr()
    {
        $rows = array('A' => 1);
        $where = array(
            array('a', \Yana\Db\Queries\OperatorEnumeration::GREATER, 0),
            \Yana\Db\Queries\OperatorEnumeration::OR,
            array('a', \Yana\Db\Queries\OperatorEnumeration::LESS, 0)
        );
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeNotEqual()
    {
        $rows = array('A' => 1);
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::NOT_EQUAL, 1);
        $this->assertFalse($this->object->__invoke($rows, $where));
    }
    /**
     * @test
     */
    public function test__invokeNotEqual2()
    {
        $rows = array('A' => 1);
        $where = array('a', '<>', 1);
        $this->assertFalse($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeLike()
    {
        $rows = array('A' => 'Abcdef');
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::LIKE, 'Abc%');
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeNotLike()
    {
        $rows = array('A' => 'Abcdef');
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::NOT_LIKE, 'Abc%');
        $this->assertFalse($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeIn()
    {
        $rows = array('A' => 1);
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::IN, array(1, 2, 3));
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeInWithQuery()
    {
        $rows = array('A' => 1);
        $connection = new \Yana\Db\FileDb\NullConnection($this->schema);
        $query = new \Yana\Db\Queries\Select($connection);
        $query->setTable('t');
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::IN, $query);
        $this->assertFalse($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeNotIn()
    {
        $rows = array('A' => 1);
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::NOT_IN, array(1, 2, 3));
        $this->assertFalse($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeNotInWithQuery()
    {
        $rows = array('A' => 1);
        $connection = new \Yana\Db\FileDb\NullConnection($this->schema);
        $query = new \Yana\Db\Queries\Select($connection);
        $query->setTable('t');
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::NOT_IN, $query);
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeRegex()
    {
        $rows = array('A' => 'Abcdef');
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::REGEX, 'Abc.*');
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeRegexWithInt()
    {
        $rows = array('A' => '123');
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::REGEX, 123);
        $this->assertTrue($this->object->__invoke($rows, $where));
    }

    /**
     * @test
     */
    public function test__invokeRegexWithInvalidValue()
    {
        $rows = array('A' => '123');
        $where = array('a', \Yana\Db\Queries\OperatorEnumeration::REGEX, array(1));
        $this->assertFalse($this->object->__invoke($rows, $where));
    }

}
