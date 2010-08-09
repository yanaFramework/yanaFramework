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

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';

/**
 * Test class for FileDbIndex
 *
 * @package  test
 */
class FileDbIndexTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    FileDbIndex
     * @access protected
     */
    protected $object;

    /**
     * @var    savePath
     * @access protected
     */
    protected $savePath = 'resources/filedb.idx';

    /**
     * @var    dataset
     * @access protected
     */
    protected $dataset = 'resources/filedb.sml';


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
        // intentionally left blank
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        unset($this->object);
    }

    /**
     * Create
     *
     * @test
     * @ignore
     */
    public function testCreate()
    {
        // intentionally left blank
    }

    /**
     * getByReference
     *
     * @test
     * @ignore
     */
    public function testGetByReference()
    {
        // intentionally left blank
    }

    /**
     * get
     *
     * @test
     * @ignore
     */
    public function testGet()
    {
        // intentionally left blank
    }

    /**
     * commit
     *
     * @test
     * @ignore
     */
    public function testCommit()
    {
        // intentionally left blank
    }

    /**
     * rollback
     *
     * @test
     * @ignore
     */
    public function testRollback()
    {
        // intentionally left blank
    }

    /**
     * test1
     *
     * @test
     */
    public function test1()
    {
        $data = new SML(CWD . $this->dataset, CASE_UPPER);
        $table = new DDLTable('FOO');
        $table->addColumn('FOOID', 'integer');
        $table->addColumn('FVALUE', 'string');
        $table->addColumn('FNUMBER', 'integer');
        $table->setPrimaryKey('FOOID');
        $table->addIndex('FVALUE', 'IDX_VALUE');
        $file = $this->object = new FileDbIndex($table, $data, CWD . $this->savePath);

        $create = $this->object->create();
        $this->assertTrue($create, 'assert failed, file db index is not created');
        $commit = $this->object->commit();
        $this->assertTrue($commit, 'assert failed, no changes are written in the target file');

        
        $create = $this->object->create('FVALUE', array('FOO', 'FVALUE'));
        $this->assertTrue($create, 'assert failed, file db index is not created');
        $commit = $this->object->commit();
        $this->assertTrue($commit, 'assert failed, no changes are written in the target file');

        $get = $this->object->get('FVALUE');
        $this->assertType('array', $get, 'assert failed, the value is not of type array');
        $this->assertArrayHasKey('FVALUE', $get, 'assert failed, the value "FVALUE" should be match the key in givin array');

        $getbyref = $this->object->getByReference('FVALUE');
        $this->assertType('array', $getbyref, 'assert failed, the value is not of type array');
        $this->assertArrayHasKey('FVALUE', $getbyref, 'assert failed, the value "FVALUE" should be match the key in givin array');

        $get = $this->object->get('FVALUE', 44);
        // expected value 2 for primary key where the expected FVALUE has the entry 44
        $this->assertEquals(2, (int)$get, 'assert failed, the values should be equal - expected primary key 2 for the searching row');

        try {
            $get = $this->object->get('FNUMBER');
        } catch(NotFoundException $e) {
            $expected = "SQL syntax error. No such index 'FNUMBER' in table 'foo'.";
            $this->assertEquals($expected, $e->getMessage(), 'assert failed, there is no index fnumber');
        }

        try {
            $get = $this->object->get('FVALUE', 325);
        } catch (NotFoundException $e) {
            $expected = "SQL syntax error. No such index 'FVALUE' in table 'foo'.";
            $this->assertEquals($expected, $e->getMessage(), 'assert failed, there is no index fvalue with value 325');
        }

        try {
            $get = $this->object->get('FVALUES', 325);
        } catch (NotFoundException $e) {
            $expected = "SQL syntax error. No such column 'FVALUES' in table 'foo'.";
            $this->assertEquals($expected, $e->getMessage(), 'assert failed, the such column does not exist');
        }
    }
}
?>