<?php
/**
 * PHPUnit test-case: \Yana\Db\Export\SqlFactory
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
require_once dirname(__FILE__) . '/include.php';

/**
 * \Yana\Db\Export\SqlFactory test-case
 *
 * @package  test
 */
class SqlFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var    \Yana\Db\Export\SqlFactory
     * @access protected
     */
    protected $_object;

    /**
     * constructor
     *
     * @access public
     * @ignore
     */
    public function __construct()
    {
        FileDbConnection::setBaseDirectory(CWD. 'resources/db/');
        DDL::setDirectory(CWD. 'resources/db/');
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        // Note: the class \Yana\Db\Export\SqlFactory is currently undergoing refactoring. Tests are incomplete.
        $this->markTestIncomplete();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        unset($this->_object);
    }

    /**
     * test
     *
     * this part create a database structure and returns a mySQL code
     * make sure that the generated code works on mysql
     * (copy the result and try it on mySQL client)
     *
     * @test
     */
    public function test()
    {

        // create a da tabase with tables (columns)
        $db = new \Yana\Db\Ddl\Database('foobar');

        /* create table "foo_department" and columns */
        $table = $db->addTable('foo_department');
        $id = $table->addColumn('id', 'integer');
        $id->setType('integer');
        $id->setSize(11);
        $id->setAutoIncrement(true);
        $id->setNullable(false);
        $table->setPrimaryKey('id');

        $name = $table->addColumn('name', 'string');
        $name->setType('string');
        $name->setSize(100);
        $name->setNullable(false);
        $description = $table->addColumn('description', 'text');
        $description->setNullable(true);
        $city = $table->addColumn('city', 'string');
        $city->setType('string');
        $city->setSize(100);
        $city->setNullable(true);
        $openTime = $table->addColumn('open_time', 'time');
        $openTime->setNullable(true);
        $openTime->setAutoFill(true);


        /* create table and columns*/

        $table = $db->addTable('foo_employee');
        $id = $table->addColumn('id', 'integer');
        $id->setType('integer');
        $id->setSize(11);
        $id->setAutoIncrement(true);
        $id->setNullable(false);
        $table->setPrimaryKey('id');

        $p_number = $table->addColumn('p_number', 'string');
        $p_number->setType('string');
        $p_number->setSize(10);
        $p_number->setNullable(false);
        $name = $table->addColumn('name', 'string');
        $name->setType('string');
        $name->setSize(30);
        $name->setNullable(false);
        $surname = $table->addColumn('surname', 'string');
        $surname->setType('string');
        $surname->setSize(30);
        $surname->setNullable(false);
        $birthday = $table->addColumn('date_of_birth', 'date');
        $birthday->setAutoFill(true);
        $birthday->setNullable(false);
        $phone = $table->addColumn('phone', 'string');
        $phone->setType('string');
        $phone->setSize(30);
        $phone->setNullable(false);
        $pm = $table->addColumn('manager', 'enum');
        $pm->setType('enum');
        $pm->setEnumerationItem('yes', '1');
        $pm->setEnumerationItem('no', '0');
        $pm->setSize(1);
        $fk = $table->addColumn('foo_department_id', 'integer');
        $fk->setType('integer');
        $fk->setSize(11);
        $fk->setNullable(false);
        $getCar = $table->addColumn('get_car', 'timestamp');
        $getCar->setAutoFill(true);
        $fk->setNullable(false);
        $foreign = $table->addForeignKey('foo_department');
        $foreign->setColumn('foo_department_id');
        $index = $table->addIndex('emp_name');
        $index->addColumn('name');
        $index->setUnique(true);

        /* create table "foo_producer" and columns */
        $table = $db->addTable('foo_producer');
        $id = $table->addColumn('id', 'integer');
        $id->setSize(11);
        $id->setAutoIncrement(true);
        $id->setNullable(false);
        $table->setPrimaryKey('id');

        $name = $table->addColumn('name', 'string');
        $name->setType('string');
        $name->setSize(30);
        $name->setNullable(false);
        $web = $table->addColumn('website', 'inet');
        $web->setSize(50);
        $web->setNullable(true);
        $web->setDefault('www.test.org');

        /* create table "foo_car_typ" and columns */
        $table = $db->addTable('foo_car_typ');
        $id = $table->addColumn('id', 'integer');
        $id->setSize(11);
        $id->setAutoIncrement(true);
        $id->setNullable(false);
        $table->setPrimaryKey('id');

        $description = $table->addColumn('description', 'text');
        $description->setNullable(false);
        $capacity = $table->addColumn('capacity', 'float');
        $capacity->setLength(3, 2);
        $capacity->setDefault('1.987');
        $capacity->setFixed(true);
        $producer_id = $table->addColumn('producer_id', 'integer');
        $producer_id->setSize(11);
        $producer_id->setNullable(false);
        $fk = $table->addForeignKey('foo_producer');
        $fk->setColumn('producer_id');
        $fk->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE);

        /* create table "foo_car" and columns */
        $table = $db->addTable('foo_car');
        $id = $table->addColumn('id', 'integer');
        $id->setType('integer');
        $id->setSize(11);
        $id->setAutoIncrement(true);
        $id->setNullable(false);
        $table->setPrimaryKey('id');

        $name = $table->addColumn('name', 'string');
        $name->setType('string');
        $name->setSize(100);
        $name->setNullable(false);
        $license_plate = $table->addColumn('license_plate', 'string');
        $license_plate->setType('string');
        $license_plate->setSize(30);
        $license_plate->setNullable(false);
        $color = $table->addColumn('color', 'string');
        $color->setType('string');
        $color->setSize(30);
        $color->setNullable(true);
        $car_typ_id = $table->addColumn('car_typ_id', 'integer');
        $car_typ_id->setType('integer');
        $car_typ_id->setSize(11);
        $car_typ_id->setNullable(false);
        $car_typ_id->setUnique(true);
        $employee_id = $table->addColumn('employee_id', 'integer');
        $employee_id->setType('integer');
        $employee_id->setSize(11);
        $employee_id->setNullable(true);
        $isNew = $table->addColumn('is_new', 'bool');
        $isNew->setDefault(1);
        $color = $table->addColumn('car_color', 'color');
        $color->setDefault('#CCCCCC');
        $image = $table->addColumn('preview', 'image');
        $image->setNullable(true);
        $payment = $table->addColumn('payment', 'integer');
        $payment->setDefault(50);
        $fk = $table->addForeignKey('foo_car_typ');
        $fk->setColumn('car_typ_id');
        $fk->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT);
        $newfk = $table->addForeignKey('foo_employee', 'test_foreign');
        $newfk->setColumn('employee_id');
        $newfk->setOnUpdate(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::CASCADE);

        $this->_object = new \Yana\Db\Export\SqlFactory($db);
        $result = $this->_object->createMySQL();
        //foreach($result as $t) print "$t\n";
        $this->assertType('array', $result, 'assert failed, the value should be of type array');
        $this->assertNotEquals(0, count($result), 'assert failed, the expected value must have some entries');
    }

    /**
     * test3
     *
     * @test
     */
    public function test3()
    {
        $db = new \Yana\Db\Ddl\Database('test');

        $table = $db->addTable('foo_bar');
        $id = $table->addColumn('id', 'integer');
        $id->setSize(11);
        $id->setAutoIncrement(true);
        $name = $table->addColumn('phone', 'string');
        $table->setPrimaryKey('id');

        $table = $db->addTable('foo_test');
        $id = $table->addColumn('id', 'integer');
        $id->setSize(11);
        $id->setAutoIncrement(true);
        $name = $table->addColumn('name', 'string');
        $table->setPrimaryKey('id');
        $foo_bar_id = $table->addColumn('foo_bar_id', 'integer');
        $fk = $table->addForeignKey('foo_bar');
        $fk->setColumn('foo_bar_id');
        $fk->setOnUpdate(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::RESTRICT);

        $table = $db->addTable('bar_test');
        $id = $table->addColumn('id', 'integer');
        $id->setSize(11);
        $id->setAutoIncrement(true);
        $fk = $table->addColumn('foo_id', 'integer');
        $fk->setSize(11);
        $add = $table->addForeignKey('foo_test');
        $add->setColumn('foo_id');
        $add->setOnUpdate(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL);
        $table->setPrimaryKey('id');
        $foo_bar_id = $table->addColumn('foo_test_id', 'integer');
        $fk = $table->addForeignKey('foo_test');
        $fk->setColumn('foo_test_id');
        $fk->setOnDelete(\Yana\Db\Ddl\KeyUpdateStrategyEnumeration::SETNULL);

        $obj = new \Yana\Db\Export\SqlFactory($db);
        $result = $obj->createMySQL();
        $this->assertType('array', $result, 'assert failed the result should be of type array');
        $this->assertNotEquals(0, count($result), 'assert failed, the expected value must have some entries');
    }

    /**
     * test4
     *
     * @test
     */
    public function test4()
    {
        $sqlFactory = new \Yana\Db\Export\SqlFactory(XDDL::getDatabase(CWD.'resources/check.db.xml'));
        $result = $sqlFactory->createMySQL();
        $this->assertType('array', $result, 'assert failed the result should be of type array');
        $this->assertNotEquals(0, count($result), 'assert failed, the expected value must have some entries');
    }

    /**
     * test5
     *
     * @test
     */
    public function test5()
    {
        // generate mySQL for testxml.db.xml
        $sqlFactory = new \Yana\Db\Export\SqlFactory(XDDL::getDatabase(CWD.'resources/testxml.db.xml'));
        // invalid sql code because some tabels missing in the current file
        // it does not work on because the preset foreignKeys has an non existing tabels
        $result = $sqlFactory->createMySQL();
        $this->assertType('array', $result, 'assert failed the result should be of type array');
        $this->assertNotEquals(0, count($result), 'assert failed, the expected value must have some entries');
    }

}

?>