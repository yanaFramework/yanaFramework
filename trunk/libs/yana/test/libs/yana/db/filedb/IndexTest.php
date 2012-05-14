<?php
/**
 * PHPUnit test-case: FileDbIndex
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

namespace Yana\Db\FileDb;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Test class for FileDbIndex
 *
 * @package  test
 */
class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileDbIndex
     */
    private $_object;

    /**
     * @var string
     */
    private $_indexFilePath;

    /**
     * @var string
     */
    private $_indexFileConents = 'a:1:{s:6:"FVALUE";a:3:{i:12;i:3;i:28;i:1;i:44;i:2;}}';

    /**
     * @var string
     */
    private $_smlFilePath;

    /**
     * @var string
     */
    private $_smlFileContents = '
        <FOOID>
            <1>
                    <FOOID>1</FOOID>
                    <FVALUE>28</FVALUE>
                    <FNUMBER>1</FNUMBER>
            </1>
            <2>
                    <FOOID>2</FOOID>
                    <FVALUE>44</FVALUE>
                    <FNUMBER>3</FNUMBER>
            </2>
            <3>
                    <FOOID>3</FOOID>
                    <FVALUE>12</FVALUE>
                    <FNUMBER>6</FNUMBER>
            </3>
        </FOOID>';


    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        // intentionally left blank
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->_smlFilePath = tempnam(sys_get_temp_dir(), __CLASS__);
        file_put_contents($this->_smlFilePath, $this->_smlFileContents);
        $this->_indexFilePath = tempnam(sys_get_temp_dir(), __CLASS__);
        file_put_contents($this->_indexFilePath, $this->_indexFileConents);

        $data = new \Yana\Files\SML($this->_smlFilePath, CASE_UPPER);
        $table = new \Yana\Db\Ddl\Table('FOO');
        $table->addColumn('FOOID', 'integer');
        $table->addColumn('FVALUE', 'string');
        $table->addColumn('FNUMBER', 'integer');
        $table->setPrimaryKey('FOOID');
        $table->addIndex('FVALUE');
        $this->_object = new \Yana\Db\FileDb\Index($table, $data, $this->_indexFilePath);
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
        unlink($this->_smlFilePath);
        unlink($this->_indexFilePath);
    }

    /**
     * Create
     *
     * @test
     */
    public function testCreate()
    {
        $create = $this->_object->create('FVALUE', array('FOO', 'FVALUE'));
        $this->assertTrue($create, 'file db index is not created');
        $this->assertTrue($this->_object->commit(), 'no changes are written in the target file');
        $this->_object->rollback();

        $get = $this->_object->getVar('FVALUE');
        $this->assertType('array', $get, 'the value is not of type array');
        $this->assertArrayHasKey('FVALUE', $get, 'index is missing key after create');
        $this->assertEquals('FOO', $get['FVALUE'], 'index is missing value after create');
    }

    /**
     * Create
     *
     * @test
     */
    public function testCreateForColumnWithoutIndex()
    {
        $this->assertTrue($this->_object->create('FNUMBER'));
        $this->assertEquals(array(1 => 1, 3 => 2, 6 => 3), $this->_object->getVar('FNUMBER'));
        $this->assertEquals(2, $this->_object->getVar('FNUMBER', 3));
    }

    /**
     * Create
     *
     * @test
     */
    public function testCreateForPrimaryKey()
    {
        $this->assertFalse($this->_object->create('FooId'));
    }

    /**
     * get
     *
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetNotFoundException()
    {
        $this->_object->getVar('non-existing-column');
    }

    /**
     * get
     *
     * @test
     */
    public function testGet()
    {
        $get = $this->_object->getVar('FVALUE', 44);
        $this->assertEquals(2, (int) $get, 'expected primary key 2 where the expected FVALUE has the entry 44');
    }

    /**
     * get
     *
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetForNonExistingValue()
    {
        $this->assertEquals(array(12 => 3, 28 => 1, 44 => 2), $this->_object->getVar('FVALUE'));
        $this->_object->getVar('FVALUE', -1);
    }

    /**
     * get
     *
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetForNonExistingIndex()
    {
        $this->_object->getVar('FNUMBER');
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetValueNotFoundException()
    {
        $get = $this->_object->getVar('FVALUE', 325);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function testGetIndexNotFoundException()
    {
        $get = $this->_object->getVar('FVALUES', 325);
    }

}
?>