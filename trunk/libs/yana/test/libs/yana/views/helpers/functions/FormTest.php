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

namespace Yana\Views\Helpers\Functions;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyFormHelper extends \Yana\Views\Helpers\Functions\Form
{
    public function _mapParameters(\Yana\Forms\IsBuilder $smartForm, array $params)
    {
        return parent::_mapParameters($smartForm, $params);
    }

}

/**
 * @package  test
 */
class FormTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Core\Dependencies\IsViewContainer
     */
    protected $container;

    /**
     * @var \Yana\Views\Helpers\Functions\MyFormHelper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!\class_exists('\Smarty') || !\class_exists('\Smarty_Internal_Template')) {
            $this->markTestSkipped();
        }
        $configurationFactory = new \Yana\ConfigurationFactory();
        $configuration = $configurationFactory->loadConfiguration(CWD . 'resources/system.config.xml');
        $configuration->configdrive = YANA_INSTALL_DIR . 'config/system.drive.xml';
        $this->container = new \Yana\Core\Dependencies\Container($configuration);
        $this->object = new \Yana\Views\Helpers\Functions\MyFormHelper($this->container);
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
        // Intentionally left blank
    }

    /**
     * @test
     */
    public function test_mapParametersEmpty()
    {
        $formBuilder = new \Yana\Forms\Builder('', $this->container);
        $this->assertSame($this->object, $this->object->_mapParameters($formBuilder, array()));
    }

    /**
     * @test
     */
    public function test_mapParameters()
    {
        $params = array(
            'id' => 'MyId',
            'table' => 'MyTable',
            'show' => 'Column1,Column2',
            'hide' => 'Column3,Column4',
            'where' =>  array('Column5', '=', 'foo'),
            'on_insert' => 'InsertAction',
            'on_update' => 'UpdateAction',
            'on_delete' => 'DeleteAction',
            'on_export' => 'ExportAction',
            'on_search' => 'SearchAction',
            'on_download' => 'DownloadAction',
            'sort' => 'SortColumn',
            'desc' => '1',
            'page' => '123',
            'entries' => '456',
            'layout' => '-789'
        );
        $formBuilder = new \Yana\Forms\Builder('', $this->container);
        $this->object->_mapParameters($formBuilder, $params);
        $this->assertSame($params['id'], $formBuilder->getId());
        $this->assertSame(explode(',', $params['show']), $formBuilder->getShow());
        $this->assertSame(explode(',', $params['hide']), $formBuilder->getHide());
        $this->assertSame($params['where'], $formBuilder->getWhere());
        $this->assertSame($params['on_insert'], $formBuilder->getOninsert());
        $this->assertSame($params['on_update'], $formBuilder->getOnupdate());
        $this->assertSame($params['on_delete'], $formBuilder->getOndelete());
        $this->assertSame($params['on_export'], $formBuilder->getOnexport());
        $this->assertSame($params['on_search'], $formBuilder->getOnsearch());
        $this->assertSame($params['on_download'], $formBuilder->getOndownload());
        $this->assertSame((bool) $params['desc'], $formBuilder->isDescending());
        $this->assertSame((int) $params['page'], $formBuilder->getPage());
        $this->assertSame((int) $params['entries'], $formBuilder->getEntries());
        $this->assertSame((int) $params['layout'], $formBuilder->getLayout());
    }

}
