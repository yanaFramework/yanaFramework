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
 */
class SqlFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\Db\Export\SqlFactory
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        // Note: the class \Yana\Db\Export\SqlFactory is currently undergoing refactoring. Tests are incomplete.
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
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
    public function testCreateMySQL()
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
        $expected = array(
            0 => "CREATE TABLE IF NOT EXISTS `foo_department` ("
                . "\n\t`id` INT(11) NOT NULL AUTO_INCREMENT,"
                . "\n\t`name` VARCHAR(100) NOT NULL,"
                . "\n\t`description` TEXT,"
                . "\n\t`city` VARCHAR(100),"
                . "\n\t`open_time` DATETIME DEFAULT CURRENT_TIMESTAMP(),"
                . "\n\tPRIMARY KEY (`id`)"
                . "\n);",
            1 => "CREATE TABLE IF NOT EXISTS `foo_employee` ("
                . "\n\t`id` INT(11) NOT NULL AUTO_INCREMENT,"
                . "\n\t`p_number` VARCHAR(10) NOT NULL,"
                . "\n\t`name` VARCHAR(30) NOT NULL,"
                . "\n\t`surname` VARCHAR(30) NOT NULL,"
                . "\n\t`date_of_birth` DATE NOT NULL DEFAULT CURRENT_DATE(),"
                . "\n\t`phone` VARCHAR(30) NOT NULL,"
                . "\n\t`manager` ENUM ('yes', 'no'),"
                . "\n\t`foo_department_id` INT(11) NOT NULL,"
                . "\n\t`get_car` BIGINT,"
                . "\n\tPRIMARY KEY (`id`)"
                . "\n);",
            2 => "CREATE TABLE IF NOT EXISTS `foo_producer` ("
                . "\n\t`id` INT(11) NOT NULL AUTO_INCREMENT,"
                . "\n\t`name` VARCHAR(30) NOT NULL,"
                . "\n\t`website` VARCHAR(45) DEFAULT 'www.test.org',"
                . "\n\tPRIMARY KEY (`id`)"
                . "\n);",
            3 => "CREATE TABLE IF NOT EXISTS `foo_car_typ` ("
                . "\n\t`id` INT(11) NOT NULL AUTO_INCREMENT,"
                . "\n\t`description` TEXT NOT NULL,"
                . "\n\t`capacity` DECIMAL(3, 2) UNSIGNED DEFAULT 1.987,"
                . "\n\t`producer_id` INT(11) NOT NULL,"
                . "\n\tPRIMARY KEY (`id`)"
                . "\n);",
            4 => "CREATE TABLE IF NOT EXISTS `foo_car` ("
                . "\n\t`id` INT(11) NOT NULL AUTO_INCREMENT,"
                . "\n\t`name` VARCHAR(100) NOT NULL,"
                . "\n\t`license_plate` VARCHAR(30) NOT NULL,"
                . "\n\t`color` VARCHAR(30),"
                . "\n\t`car_typ_id` INT(11) NOT NULL,"
                . "\n\t`employee_id` INT(11),"
                . "\n\t`is_new` TINYINT(1) DEFAULT 1,"
                . "\n\t`car_color` CHAR(7) DEFAULT '#CCCCCC',"
                . "\n\t`preview` VARCHAR(128),"
                . "\n\t`payment` INT DEFAULT 50,"
                . "\n\tPRIMARY KEY (`id`),"
                . "\n\tUNIQUE `foo_car` (`car_typ_id`)"
                . "\n);",
            5 => "ALTER TABLE `foo_employee` ADD UNIQUE INDEX `emp_name` (`name` ASC);",
            6 => "ALTER TABLE `foo_employee` ADD CONSTRAINT `foo_employee_1_fk` FOREIGN KEY (`foo_department_id`) REFERENCES `foo_department` (`id`);",
            7 => "ALTER TABLE `foo_car_typ` ADD CONSTRAINT `foo_car_typ_2_fk` FOREIGN KEY (`producer_id`) REFERENCES `foo_producer` (`id`) ON DELETE CASCADE;",
            8 => "ALTER TABLE `foo_car` ADD CONSTRAINT `foo_car_3_fk` FOREIGN KEY (`car_typ_id`) REFERENCES `foo_car_typ` (`id`) ON DELETE RESTRICT;",
            9 => "ALTER TABLE `foo_car` ADD CONSTRAINT `test_foreign` FOREIGN KEY (`employee_id`) REFERENCES `foo_employee` (`id`);",
        );
        $this->assertEquals($expected, $result);
    }

    /**
     * test3
     *
     * @test
     */
    public function testCreateMySQL2()
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

        $expected = array(
            0 => "CREATE TABLE IF NOT EXISTS `foo_bar` ("
                . "\n\t`id` INT(11) AUTO_INCREMENT,"
                . "\n\t`phone` TEXT,"
                . "\n\tPRIMARY KEY (`id`)"
                . "\n);",
            1 => "CREATE TABLE IF NOT EXISTS `foo_test` ("
                . "\n\t`id` INT(11) AUTO_INCREMENT,"
                . "\n\t`name` TEXT,"
                . "\n\t`foo_bar_id` INT,"
                . "\n\tPRIMARY KEY (`id`)"
                . "\n);",
            2 => "CREATE TABLE IF NOT EXISTS `bar_test` ("
                . "\n\t`id` INT(11) AUTO_INCREMENT,"
                . "\n\t`foo_id` INT(11),"
                . "\n\t`foo_test_id` INT,"
                . "\n\tPRIMARY KEY (`id`)"
                . "\n);",
            3 => "ALTER TABLE `foo_test` ADD CONSTRAINT `foo_test_1_fk` FOREIGN KEY (`foo_bar_id`) REFERENCES `foo_bar` (`id`);",
            4 => "ALTER TABLE `bar_test` ADD CONSTRAINT `bar_test_2_fk` FOREIGN KEY (`foo_id`) REFERENCES `foo_test` (`id`);",
            5 => "ALTER TABLE `bar_test` ADD CONSTRAINT `bar_test_3_fk` FOREIGN KEY (`foo_test_id`) REFERENCES `foo_test` (`id`) ON DELETE SET NULL;",
        );
        $obj = new \Yana\Db\Export\SqlFactory($db);
        $result = $obj->createMySQL();
        $this->assertEquals($expected, $result);
    }

    /**
     * test4
     *
     * @test
     */
    public function testCreateMySQL3()
    {
        $sqlFactory = new \Yana\Db\Export\SqlFactory(\Yana\Files\XDDL::getDatabase(CWD . 'resources/check.db.xml'));
        $expected = array(
            0 => "CREATE TABLE IF NOT EXISTS `ft` ("
                . "\n\t`ftid` INT NOT NULL,"
                . "\n\t`ftvalue` INT DEFAULT 0,"
                . "\n\t`array` TEXT,"
                . "\n\tPRIMARY KEY (`ftid`)"
                . "\n);",
            1 => "CREATE TABLE IF NOT EXISTS `t` ("
                . "\n\t`tid` VARCHAR(32) NOT NULL,"
                . "\n\t`tvalue` INT DEFAULT 0,"
                . "\n\t`ta` TEXT,"
                . "\n\t`tb` TINYINT(1),"
                . "\n\t`tf` DOUBLE UNSIGNED,"
                . "\n\t`ti` INT(4) UNSIGNED ZEROFILL,"
                . "\n\t`ftid` INT,"
                . "\n\tPRIMARY KEY (`tid`)"
                . "\n);",
            2 => "CREATE TABLE IF NOT EXISTS `i` ("
                . "\n\t`iid` VARCHAR(32) NOT NULL,"
                . "\n\t`ta` TEXT,"
                . "\n\tPRIMARY KEY (`iid`)"
                . "\n);",
            3 => "CREATE TABLE IF NOT EXISTS `u` ("
                . "\n\t`uid` VARCHAR(32) NOT NULL,"
                . "\n\tPRIMARY KEY (`uid`)"
                . "\n);",
            4 => "ALTER TABLE `t` ADD UNIQUE INDEX `index1` (`tid` ASC);",
            5 => "ALTER TABLE `t` ADD INDEX `index2` (`tvalue` ASC);",
            6 => "ALTER TABLE `t` ADD CONSTRAINT `t_1_fk` FOREIGN KEY (`ftid`) REFERENCES `ft` (`ftid`);",
            7 => "ALTER TABLE `i` ADD CONSTRAINT `i_2_fk` FOREIGN KEY (`iid`) REFERENCES `t` (`tid`);",
            8 => "CREATE VIEW `v` (tid, tvalue) AS SELECT t.tid as id, t.tvalue as val FROM t",
        );
        $result = $sqlFactory->createMySQL();
        $this->assertInternalType('array', $result, 'assert failed the result should be of type array');
        $this->assertNotEquals(0, count($result), 'assert failed, the expected value must have some entries');
    }

    /**
     * test5
     *
     * @test
     */
    public function testCreateMySQL4()
    {
        // generate mySQL for testxml.db.xml
        $sqlFactory = new \Yana\Db\Export\SqlFactory(\Yana\Files\XDDL::getDatabase(CWD . 'resources/testxml.db.xml'));
        // invalid sql code because a foreign key refers to a non-existing table
        $expected = array(
            0 => "CREATE TABLE IF NOT EXISTS `test` ("
                . "\n\t`test_id` INT(8) NOT NULL AUTO_INCREMENT,"
                . "\n\t`test_title` VARCHAR(80) NOT NULL DEFAULT 'test',"
                . "\n\t`test_text` TEXT NOT NULL,"
                . "\n\t`test_created` BIGINT NOT NULL,"
                . "\n\t`test_author` VARCHAR(80) DEFAULT 'test',"
                . "\n\t`test_color` CHAR(7) DEFAULT '#000000',"
                . "\n\t`test_range` DOUBLE,"
                . "\n\t`test_enum` ENUM ('default', '1', 'a', 'Abc', '123'),"
                . "\n\t`profile_id` VARCHAR(128) NOT NULL,"
                . "\n\tPRIMARY KEY (`test_id`),"
                . "\n\tUNIQUE `test` (`test_id`)"
                . "\n);",
            1 => "CREATE TABLE IF NOT EXISTS `testcmt` ("
                . "\n\t`testcmt_id` INT(8) NOT NULL AUTO_INCREMENT,"
                . "\n\t`testcmt_text` TEXT NOT NULL,"
                . "\n\t`testcmt_created` BIGINT NOT NULL,"
                . "\n\t`test_author` VARCHAR(80) DEFAULT 'test',"
                . "\n\t`test_id` INT(8),"
                . "\n\t`profile_id` VARCHAR(128) NOT NULL,"
                . "\n\tPRIMARY KEY (`testcmt_id`),"
                . "\n\tUNIQUE `testcmt` (`testcmt_id`)"
                . "\n);",
            2 => "ALTER TABLE `test` ADD UNIQUE INDEX `test_index` (`test_column`(20) ASC);",
            // the following foreign key references an undefined table `table` (should we check for this and suppress the foreign-key?)
            3 => "ALTER TABLE `test` ADD CONSTRAINT `test_foreign` FOREIGN KEY (`test_id`) REFERENCES `table` (`column_id`);",
            4 => "ALTER TABLE `testcmt` ADD CONSTRAINT `testforeign` FOREIGN KEY (`testcmt_id`) REFERENCES `test` (`test_id`);",
            5 => "CREATE VIEW `test_view` (test_id, test_title) AS Select Test_title as bar, Test_id as id from Test where Test_id > 5;",
        );
        $result = $sqlFactory->createMySQL();
        $this->assertEquals($expected, $result);
    }

}
