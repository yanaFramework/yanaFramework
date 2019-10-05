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

namespace Yana\Forms\Setups;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Dependencies\Container
     */
    protected $container;

    /**
     * @var \Yana\Forms\Setups\Builder
     */
    protected $object;

    /**
     * @var \Yana\Db\Ddl\Form
     */
    protected $form;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $configurationFactory = new \Yana\ConfigurationFactory();
        $configuration = $configurationFactory->loadConfiguration(CWD . 'resources/system.config.xml');
        $configuration->configdrive = YANA_INSTALL_DIR . 'config/system.drive.xml';
        $this->container = new \Yana\Core\Dependencies\Container($configuration);
        $schemaFactory = new \Yana\Db\SchemaFactory();
        $schema = $schemaFactory->createSchema('check');
        $this->form = $this->_buildForm($schema);
        $this->object = new \Yana\Forms\Setups\Builder($this->form, $this->container);
    }

    /**
     * @param   \Yana\Db\Ddl\Database  $schema  database
     * @return  \Yana\Db\Ddl\Form
     */
    private function _buildForm(\Yana\Db\Ddl\Database $schema)
    {
        if (!$schema->isForm('form')) {
            $form = $schema->addForm('form');
            $form->setTable('t');
            $form->setAllInput(true);
            $form->setDescription('description');
            $form->setTitle('title');
        } else {
            $form = $schema->getForm('form');
        }
        return $form;
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
    public function testSetSetup()
    {
        $setup = new \Yana\Forms\Setup();
        $this->assertSame($setup, $this->object->setSetup($setup)->__invoke());
    }

    /**
     * @test
     */
    public function test__invoke()
    {
        $setup = new \Yana\Forms\Setup();
        $setup
                ->setDeleteAction('delete')
                ->setDownloadAction('download')
                ->setInsertAction('insert')
                ->setUpdateAction('update')
                ->setSearchAction('search')
                ->setExportAction('export');
        $this->object->setSetup($setup)->__invoke();
        // Actions are checked against security module: must not be allowed
        $this->assertSame("", $setup->getSearchAction());
        $this->assertSame("", $setup->getDeleteAction());
        $this->assertSame("", $setup->getDownloadAction());
        $this->assertSame("", $setup->getExportAction());
        $this->assertSame("", $setup->getInsertAction());
        $this->assertSame("", $setup->getUpdateAction());
    }

    /**
     * @test
     */
    public function test__invokeActions()
    {
        $setup = new \Yana\Forms\Setup();
        $setup
                ->setDeleteAction('delete')
                ->setDownloadAction('download')
                ->setInsertAction('insert')
                ->setUpdateAction('update')
                ->setSearchAction('search')
                ->setExportAction('export');
        $this->container->getSecurity()->addSecurityRule(new \Yana\Security\Rules\NullRule());
        $this->object->setSetup($setup)->__invoke();
        // With the NULL security rule in place, all security checks should now come back positive
        $this->assertSame("search", $setup->getSearchAction());
        $this->assertSame("delete", $setup->getDeleteAction());
        $this->assertSame("download", $setup->getDownloadAction());
        $this->assertSame("export", $setup->getExportAction());
        $this->assertSame("insert", $setup->getInsertAction());
        $this->assertSame("update", $setup->getUpdateAction());
    }

    /**
     * @test
     */
    public function test__invokeActionsEmptyDownloadAction()
    {
        $setup = new \Yana\Forms\Setup();
        $this->container->getSecurity()->addSecurityRule(new \Yana\Security\Rules\NullRule());
        $this->object->setSetup($setup)->__invoke();
        // There is a default action for downloading files
        $this->assertSame("download_file", $setup->getDownloadAction());
    }

    /**
     * @test
     */
    public function test__invokeEventsEmpty()
    {
        $setup = new \Yana\Forms\Setup();
        $this->form->addEvent('delete');
        $this->form->addEvent('download');
        $this->form->addEvent('insert');
        $this->form->addEvent('update');
        $this->form->addEvent('search');
        $this->form->addEvent('export');
        $this->container->getSecurity()->addSecurityRule(new \Yana\Security\Rules\NullRule());
        $this->object->setSetup($setup)->__invoke();
        // With the NULL security rule in place, all security checks should now come back positive
        $this->assertSame("", $setup->getSearchAction());
        $this->assertSame("", $setup->getDeleteAction());
        $this->assertSame("download_file", $setup->getDownloadAction()); // default action
        $this->assertSame("", $setup->getExportAction());
        $this->assertSame("", $setup->getInsertAction());
        $this->assertSame("", $setup->getUpdateAction());
    }

    /**
     * @test
     */
    public function test__invokeEvents()
    {
        $setup = new \Yana\Forms\Setup();
        $this->form->getEvent('delete')->setAction('deleteAction');
        $this->form->getEvent('download')->setAction('downloadAction');
        $this->form->getEvent('insert')->setAction('insertAction');
        $this->form->getEvent('update')->setAction('updateAction');
        $this->form->getEvent('search')->setAction('searchAction');
        $this->form->getEvent('export')->setAction('exportAction');
        $this->container->getSecurity()->addSecurityRule(new \Yana\Security\Rules\NullRule());
        $this->object->setSetup($setup)->__invoke();
        // With the NULL security rule in place, all security checks should now come back positive
        $this->assertSame("searchAction", $setup->getSearchAction());
        $this->assertSame("deleteAction", $setup->getDeleteAction());
        $this->assertSame("downloadAction", $setup->getDownloadAction());
        $this->assertSame("exportAction", $setup->getExportAction());
        $this->assertSame("insertAction", $setup->getInsertAction());
        $this->assertSame("updateAction", $setup->getUpdateAction());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function test__invokeNotFoundException()
    {
        $this->object = new \Yana\Forms\Setups\Builder(new \Yana\Db\Ddl\Form("form"), $this->container);
        $this->object->__invoke();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function test__invokeNotFoundExceptionTableName()
    {
        $this->form = new \Yana\Db\Ddl\Form("form", new \Yana\Db\Ddl\Database("db"));
        $this->object = new \Yana\Forms\Setups\Builder($this->form, $this->container);
        $this->object->__invoke();
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     */
    public function test__invokeNotFoundExceptionTable()
    {
        $this->form = new \Yana\Db\Ddl\Form("form", new \Yana\Db\Ddl\Database("db"));
        $this->form->setTable('t');
        $this->object = new \Yana\Forms\Setups\Builder($this->form, $this->container);
        $this->object->__invoke();
    }

    /**
     * @test
     */
    public function testGetForm()
    {
        $this->assertSame($this->form, $this->object->getForm());
    }

    /**
     * @test
     */
    public function testSetForm()
    {
        $form = new \Yana\Db\Ddl\Form(__FUNCTION__);
        $this->assertSame($form, $this->object->setForm($form)->getForm());
    }

    /**
     * @test
     */
    public function testUpdateSetup()
    {
        $array = array(
            'page' => 1,
            'entries' => 2,
            'layout' => 3,
            'searchterm' => 'Test',
            'dropfilter' => true,
            'filter' => array(
                'tid' => '4'
            ),
            'sort' => 'tid',
            'orderby' => 'tvalue',
            'desc' => true
        );
        $setup = $this->object->__invoke();
        $setup->setFilters(array('thisMustBeGone' => 'test'));
        $this->object->updateSetup($array);
        $this->assertSame($array['page'], $setup->getPage());
        $this->assertSame($array['entries'], $setup->getEntriesPerPage());
        $this->assertSame($array['layout'], $setup->getLayout());
        $this->assertSame($array['searchterm'], $setup->getSearchTerm());
        $this->assertSame($array['filter'], $setup->getFilters());
        $this->assertSame($array['orderby'], $setup->getOrderByField());
        $this->assertTrue($setup->isDescending());
    }

    /**
     * @test
     */
    public function testUpdateValues()
    {
        $array = array(
            \Yana\Forms\Setups\ContextNameEnumeration::INSERT => array(
                'tid' => '4',
                'tvalue' => 'Test',
                'other' => 'this must not be used'
            )
        );
        $setup = $this->object->__invoke();
        $insertContextName = \Yana\Forms\Setups\ContextNameEnumeration::INSERT;
        $setup->setInsertAction('insertAction')->getContext($insertContextName)
                ->setColumnNames(array('tid', 'tvalue'))->setValues(array('tid' => '5', 'foo' => 'something'));
        $this->object->updateValues($array);
        $this->assertSame(array('TID' => '4', 'TVALUE' => 'Test'), $setup->getContext($insertContextName)->getValues());
    }

    /**
     * @test
     */
    public function testUpdateValuesUpdates()
    {
        $array = array(
            \Yana\Forms\Setups\ContextNameEnumeration::UPDATE => array(
                1 => array(
                    'tid' => '4',
                    'tvalue' => 'Test',
                    'other' => 'this must not be used'
                )
            )
        );
        $setup = $this->object->__invoke();
        $updateContextName = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $setup->setInsertAction('insertAction')->setUpdateAction('updateAction')->getContext($updateContextName)
                ->setColumnNames(array('tid', 'tvalue'))->setRows(array(1 => array('TID' => '5', 'FOO' => 'something')));
        $this->object->updateValues($array);
        $expected = array(1 => array('TID' => '4', 'TVALUE' => 'Test', 'FOO' => 'something'));
        $this->assertSame($expected, $setup->getContext($updateContextName)->getRows()->toArray());
    }

    /**
     * @test
     */
    public function testUpdateValuesSearch()
    {
        $array = array(
            \Yana\Forms\Setups\ContextNameEnumeration::INSERT => array(
                'tid' => '4',
                'tvalue' => 'Test',
                'other' => 'this must not be used'
            ),
            \Yana\Forms\Setups\ContextNameEnumeration::UPDATE => array(
                1 => array(
                    'tid' => '4',
                    'tvalue' => 'Test',
                    'other' => 'this must not be used'
                )
            ),
            \Yana\Forms\Setups\ContextNameEnumeration::SEARCH => array(
                'tid' => '5',
                'tvalue' => 'Test2',
                'other' => 'this must not be used'
            )
        );
        $setup = $this->object->__invoke();
        $searchContextName = \Yana\Forms\Setups\ContextNameEnumeration::SEARCH;
        $setup->setInsertAction('insertAction')->setUpdateAction('updateAction')->setSearchAction('searchAction')->getContext($searchContextName)
                ->setColumnNames(array('tid', 'tvalue'))->setValues(array('tid' => '5', 'foo' => 'something'));
        $this->object->updateValues($array);
        $expected = array('TID' => '5', 'TVALUE' => 'Test2');
        $this->assertSame($expected, $setup->getContext($searchContextName)->getValues());
    }

    /**
     * @test
     */
    public function testGetRows()
    {
        $this->assertSame(array(), $this->object->getRows());
    }

    /**
     * @test
     */
    public function testSetRows()
    {
        $rows = array(
            1 => array(
                'tid' => '4',
                'tvalue' => 'Test',
                'other' => 'this must not be used'
            )
        );
        $this->assertSame($rows, $this->object->setRows($rows)->getRows());
    }

    /**
     * @test
     */
    public function testGetColumnsWhitelist()
    {
        $this->assertSame(array(), $this->object->getColumnsWhitelist());
    }

    /**
     * @test
     */
    public function testSetColumnsWhitelist()
    {
        $columnNames = array('Tvalue', 'tId');
        $this->assertSame($columnNames, $this->object->setColumnsWhitelist($columnNames)->getColumnsWhitelist());
    }

    /**
     * @test
     */
    public function testGetColumnsBlacklist()
    {
        $this->assertSame(array(), $this->object->getColumnsBlacklist());
    }

    /**
     * @test
     */
    public function testSetColumnsBlacklist()
    {
        $columnNames = array('tValue', 'Tid');
        $this->assertSame($columnNames, $this->object->setColumnsBlacklist($columnNames)->getColumnsBlacklist());
    }

}
