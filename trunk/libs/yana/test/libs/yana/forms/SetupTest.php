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

namespace Yana\Forms;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class SetupTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var FormSetup
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Forms\Setup();
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
    public function testSetPage()
    {
        $this->assertEquals(5, $this->object->setPage(5)->getPage());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetPageInvalidArgumentException()
    {
        $this->object->setPage(-1);
    }

    /**
     * @test
     */
    public function testGetPage()
    {
        $this->assertEquals(0, $this->object->getPage());
    }

    /**
     * @test
     */
    public function testSetEntriesPerPage()
    {
        $this->assertEquals(1, $this->object->setEntriesPerPage(1)->getEntriesPerPage());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetEntriesPerPageInvalidArgumentException()
    {
        $this->object->setEntriesPerPage(0);
    }

    /**
     * @test
     */
    public function testGetEntriesPerPage()
    {
        $this->assertEquals(10, $this->object->getEntriesPerPage());
    }

    /**
     * @test
     */
    public function testHasFilter()
    {
        $this->assertFalse($this->object->hasFilter());
        $this->object->setFilter('test', 'test');
        $this->assertTrue($this->object->hasFilter());
        $this->object->setFilter('test');
        $this->assertFalse($this->object->hasFilter());
    }

    /**
     * @test
     */
    public function testGetFilter()
    {
        $this->assertEquals('', $this->object->getFilter('test'));
    }

    /**
     * @test
     */
    public function testGetFilters()
    {
        $this->assertEquals(array(), $this->object->getFilters());
    }

    /**
     * @test
     */
    public function testSetFilter()
    {
        $this->assertEquals('Test_/%&lt;&gt;', $this->object->setFilter('column', 'Test?/*<>')->getFilter('column'));
    }

    /**
     * @test
     */
    public function testSetFilters()
    {
        $filters = array(
            'column1' => 'Test?/*',
            'column2' => 'Ä<>'
        );
        $expected = array(
            'column1' => 'Test_/%',
            'column2' => 'Ä&lt;&gt;'
        );
        $this->assertEquals($expected, $this->object->setFilters($filters)->getFilters());
    }

    /**
     * @test
     */
    public function testSetLayout()
    {
        $this->assertEquals(1, $this->object->setLayout(1)->getLayout());
    }

    /**
     * @test
     */
    public function testGetLayout()
    {
        $this->assertEquals(0, $this->object->getLayout());
    }

    /**
     * @test
     */
    public function testGetOrderByField()
    {
        $this->assertEquals('', $this->object->getOrderByField());
    }

    /**
     * @test
     */
    public function testSetOrderByField()
    {
        $this->assertEquals('Test', $this->object->setOrderByField('Test')->getOrderByField());
    }

    /**
     * @test
     */
    public function testSetSortOrder()
    {
        $this->assertFalse($this->object->setSortOrder(false)->isDescending());
        $this->assertTrue($this->object->setSortOrder(true)->isDescending());
    }

    /**
     * @test
     */
    public function testIsDescending()
    {
        $this->assertFalse($this->object->isDescending());
    }

    /**
     * @test
     */
    public function testSetSearchTerm()
    {
        $this->assertEquals('Test?/*<>', $this->object->setSearchTerm('Test?/*<>')->getSearchTerm());
    }

    /**
     * @test
     */
    public function testGetSearchTerm()
    {
        $this->assertEquals('', $this->object->getSearchTerm());
    }

    /**
     * @test
     */
    public function testSetDownloadAction()
    {
        $this->assertEquals('testFunction', $this->object->setDownloadAction('testFunction')->getDownloadAction());
    }

    /**
     * @test
     */
    public function testGetDownloadAction()
    {
        $this->assertEquals('', $this->object->getDownloadAction());
    }

    /**
     * @test
     */
    public function testSetSearchAction()
    {
        $this->assertEquals('testFunction', $this->object->setSearchAction('testFunction')->getSearchAction());
    }

    /**
     * @test
     */
    public function testGetSearchAction()
    {
        $this->assertEquals('', $this->object->getSearchAction());
    }

    /**
     * @test
     */
    public function testSetInsertAction()
    {
        $this->assertEquals('testFunction', $this->object->setInsertAction('testFunction')->getInsertAction());
    }

    /**
     * @test
     */
    public function testGetInsertAction()
    {
        $this->assertEquals('', $this->object->getInsertAction());
    }

    /**
     * @test
     */
    public function testSetUpdateAction()
    {
        $this->assertEquals('testFunction', $this->object->setUpdateAction('testFunction')->getUpdateAction());
    }

    /**
     * @test
     */
    public function testGetUpdateAction()
    {
        $this->assertEquals('', $this->object->getUpdateAction());
    }

    /**
     * @test
     */
    public function testSetDeleteAction()
    {
        $this->assertEquals('testFunction', $this->object->setDeleteAction('testFunction')->getDeleteAction());
    }

    /**
     * @test Implement testGetDeleteAction().
     */
    public function testGetDeleteAction()
    {
        $this->assertEquals('', $this->object->getDeleteAction());
    }

    /**
     * @test
     */
    public function testSetExportAction()
    {
        $this->assertEquals('testFunction', $this->object->setExportAction('testFunction')->getExportAction());
    }

    /**
     * @test
     */
    public function testGetExportAction()
    {
        $this->assertEquals('', $this->object->getExportAction());
    }

    /**
     * @test
     */
    public function testGetForeignKeys()
    {
        $this->assertEquals(array(), $this->object->getForeignKeys());
    }

    /**
     * @test
     */
    public function testAddForeignKeyReference()
    {
        $reference = new \Yana\Db\Ddl\Reference('table', 'column', 'label');
        $this->assertEquals(array('column' => $reference), $this->object->addForeignKeyReference('column', $reference)->getForeignKeys());
    }

    /**
     * @test
     */
    public function testGetEntryCount()
    {
        $this->assertSame(0, $this->object->getEntryCount());
    }

    /**
     * @test
     */
    public function testSetEntryCount()
    {
        $this->assertSame(5, $this->object->setEntryCount(5)->getEntryCount());
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testSetEntryCountInvalidArgumentException()
    {
        $this->object->setEntryCount(-1);
    }

    /**
     * Test with 5 entries showing 5 entries per page: trying to show entries 6-10 (that are not there) should reset the current page to the first page.
     *
     * @test
     */
    public function testSetEntryCountLastPage()
    {
        $this->assertSame(0, $this->object->setEntriesPerPage(5)->setPage(1)->setEntryCount(5)->getPage());
    }

    /**
     * @test
     */
    public function testGetPageCount()
    {
        $this->assertSame(0, $this->object->getPageCount());
    }

    /**
     * @test
     */
    public function testGetContexts()
    {
        $this->assertEquals(array(), $this->object->getContexts());
    }

    /**
     * @test
     */
    public function testGetContext()
    {
        $expected = new \Yana\Forms\Setups\Context(\Yana\Forms\Setups\ContextNameEnumeration::UPDATE);
        $this->assertEquals($expected, $this->object->getContext(\Yana\Forms\Setups\ContextNameEnumeration::UPDATE));
    }

    /**
     * @test
     */
    public function testSetContext()
    {
        $name = \Yana\Forms\Setups\ContextNameEnumeration::UPDATE;
        $expected = new \Yana\Forms\Setups\Context($name);
        $expected->setFooter('footer')->setHeader('header');
        $this->assertSame($expected, $this->object->setContext($expected)->getContext($name));
    }

    /**
     * @test
     */
    public function testGetReferenceValues()
    {
        $this->assertSame(array(), $this->object->getReferenceValues('column'));
    }

    /**
     * @test
     */
    public function testSetReferenceValues()
    {
        $references = array(
            'Column' => array('test')
        );
        $this->assertEquals(array('test'), $this->object->setReferenceValues($references)->getReferenceValues('column'));
    }

}
