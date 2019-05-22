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

namespace Yana\Forms;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * database connection
     *
     * @var \Yana\Db\FileDb\Connection
     */
    public $db = null;

    /**
     * @var \Yana\Forms\QueryBuilder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        try {
            $schema = \Yana\Files\XDDL::getDatabase('check');
            $this->db = new \Yana\Db\FileDb\Connection($schema);
            $this->object = new \Yana\Forms\QueryBuilder($this->db);

        } catch (\Exception $e) {
            $this->markTestSkipped("Unable to connect to database");
        }
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
    public function testSetForm()
    {
        $form = new \Yana\Forms\Facade();
        $this->assertSame($form, $this->object->setForm($form)->getForm());
    }

    /**
     * @test
     */
    public function testGetForm()
    {
        $this->assertNull($this->object->getForm());
    }

    /**
     * @test
     */
    public function testGetDatabase()
    {
        $this->assertSame($this->db, $this->object->getDatabase());
    }

    /**
     * @test
     */
    public function testBuildSelectQueryEmpty()
    {
        $query = $this->object->setForm(new \Yana\Forms\Facade())->buildSelectQuery();
        $this->assertTrue($query instanceof \Yana\Db\Queries\Select);
        $this->assertSame($query, $this->object->buildSelectQuery());
        $this->assertSame($this->db, $query->getDatabase());
    }

    /**
     * @test
     */
    public function testBuildSelectQuery()
    {
        $form = new \Yana\Db\Ddl\Form('test', $this->db->getSchema());
        $form->setTable('t');
        $facade = new \Yana\Forms\Facade();
        $facade->setBaseForm($form);
        $setup = $facade->getSetup();
        $setup->setPage(1)->setEntriesPerPage(10)->setOrderByField('tid');
        $query = $this->object->setForm($facade)->buildSelectQuery();
        $this->assertTrue($query instanceof \Yana\Db\Queries\Select);
        $this->assertSame($query, $this->object->buildSelectQuery());
        $this->assertSame($this->db, $query->getDatabase());
        $this->assertSame($form->getTable(), $query->getTable());
        $this->assertSame($setup->getEntriesPerPage(), $query->getLimit());
        $this->assertSame($setup->getPage() * $setup->getEntriesPerPage(), $query->getOffset());
        $this->assertSame(array(array($form->getTable(), $setup->getOrderByField())), $query->getOrderBy());
    }

    /**
     * @test
     */
    public function testBuildSelectQuerySearchTerm()
    {
        $table = 't';
        $form = new \Yana\Db\Ddl\Form('test', $this->db->getSchema());
        $form->setTable($table);
        $form->setAllInput(false);
        $form->addField('tid');
        $formFacade = new \Yana\Forms\Facade();
        $formFacade->setBaseForm($form);
        $setup = $formFacade->getSetup();
        $setup->setSearchTerm("test");
        $this->object->setForm($formFacade);
        $formFacade->getUpdateForm()->getContext()->addColumnName('tid');
        $query = $this->object->buildSelectQuery();
        $this->assertSame($table, $query->getTable());
        $this->assertSame(array(array($table, 'tid')), $query->getColumns());
        $this->assertSame(array(array($table, 'tid'), 'like', '%test%'), $query->getWhere());
    }

    /**
     * @test
     */
    public function testBuildAutocompleteQuery()
    {
        $table = 't';
        $column = 'tid';
        $label = 'tvalue';
        $searchTerm = 'Search Term';
        $limit = 123;
        $targetReference = new \Yana\Db\Ddl\Reference($table, $column, $label);
        $query = $this->object->buildAutocompleteQuery($targetReference, $searchTerm, $limit);
        $this->assertSame($table, $query->getTable());
        $this->assertSame(array('VALUE' => array($table, $column), 'LABEL' => array($table, $label)), $query->getColumns());
        $this->assertSame(array(array($table, $label), 'like', $searchTerm . '%'), $query->getWhere());
        $this->assertSame($limit, $query->getLimit());
        $this->assertSame(array(array($table, $label)), $query->getOrderBy());
    }

    /**
     * @test
     */
    public function testBuildCountQuery()
    {
        $form = new \Yana\Db\Ddl\Form('test', $this->db->getSchema());
        $form->setTable('t');
        $facade = new \Yana\Forms\Facade();
        $facade->setBaseForm($form);
        $setup = $facade->getSetup();
        $setup->setPage(1)->setEntriesPerPage(10)->setOrderByField('tid');
        $query = $this->object->setForm($facade)->buildCountQuery();
        $this->assertTrue($query instanceof \Yana\Db\Queries\Select);
        $this->assertSame($query, $this->object->buildCountQuery());
        $this->assertSame($this->db, $query->getDatabase());
        $this->assertSame($form->getTable(), $query->getTable());
        $this->assertSame(array(array($form->getTable(), $setup->getOrderByField())), $query->getOrderBy());
        $this->assertSame(0, $query->getLimit());
        $this->assertSame(0, $query->getOffset());
    }

}
