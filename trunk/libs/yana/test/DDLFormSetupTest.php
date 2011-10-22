<?php

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__) . '/../ddlformsetup.php';

/**
 * Test class for DDLFormSetup.
 * Generated by PHPUnit on 2011-02-06 at 21:23:27.
 */
class DDLFormSetupTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var DDLFormSetup
     */
    private $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new DDLFormSetup;
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
        $this->_object->setPage(5);
        $this->assertEquals(5, $this->_object->getPage());
    }

    /**
     * @test
     * @expectedException \Yana\Core\InvalidArgumentException
     */
    public function testSetPageInvalidArgumentException()
    {
        $this->_object->setPage(-1);
    }

    /**
     * @test
     */
    public function testGetPage()
    {
        $this->assertEquals(0, $this->_object->getPage(), 'Page should default to 0.');
    }

    /**
     * @test
     */
    public function testSetEntriesPerPage()
    {
        $this->_object->setEntriesPerPage(1);
        $this->assertEquals(1, $this->_object->getEntriesPerPage());
    }

    /**
     * @test
     * @expectedException \Yana\Core\InvalidArgumentException
     */
    public function testSetEntriesPerPageInvalidArgumentException()
    {
        $this->_object->setEntriesPerPage(0);
    }

    /**
     * @test
     */
    public function testGetEntriesPerPage()
    {
        $this->assertEquals(5, $this->_object->getEntriesPerPage(), 'EntriesPerPage should default to 5.');
    }

    /**
     * @test
     */
    public function testGetValue()
    {
        $this->assertEquals(null, $this->_object->getValue('non-existing'));
    }

    /**
     * @test
     */
    public function testGetValues()
    {
        $this->assertEquals(array(), $this->_object->getValues());
    }

    /**
     * @test
     */
    public function testSetValue()
    {
        $this->_object->setValue('test', 'test');
        $this->assertEquals('test', $this->_object->getValue('test'));
        $this->assertEquals('test', $this->_object->getValue('Test'));
        $this->assertEquals('test', $this->_object->getValue('TEST'));
    }

    /**
     * @test
     */
    public function testSetValues()
    {
        $this->_object->setValues(array(1, 2, 3));
        $this->assertEquals(array(1, 2, 3), $this->_object->getValues());
    }

    /**
     * @test
     */
    public function testHasFilter()
    {
        $this->assertFalse($this->_object->hasFilter(), 'By default, form should have no filter');
    }

    /**
     * @test
     */
    public function testSetFilter()
    {
        $this->_object->setFilter('column', 'filter');
        $this->assertTrue($this->_object->hasFilter());
        $this->assertEquals('filter', $this->_object->getFilter('column'));
        $this->_object->setFilter('column');
        $this->assertFalse($this->_object->hasFilter());
    }

    /**
     * @test
     */
    public function testSetFilters()
    {
        $this->_object->setFilters(array('column' => 'filter'));
        $this->assertTrue($this->_object->hasFilter());
        $this->assertEquals(array('column' => 'filter'), $this->_object->getFilters());
    }

    /**
     * @test
     */
    public function testSetLayout()
    {
        $this->_object->setLayout(1);
        $this->assertEquals(1, $this->_object->getLayout());
    }

    /**
     * @test
     */
    public function testGetLayout()
    {
        $this->assertEquals(0, $this->_object->getLayout(), 'Layout should default to 0.');
    }

    /**
     * @test
     */
    public function testGetOrderByField()
    {
        $this->assertEquals("", $this->_object->getOrderByField(), 'Order by should be empty by default.');
    }

    /**
     * @test
     */
    public function testSetOrderByField()
    {
        $this->_object->setOrderByField("test");
        $this->assertEquals("test", $this->_object->getOrderByField());
    }

    /**
     * @test
     */
    public function testSetSortOrder()
    {
        $this->_object->setSortOrder(true);
        $this->assertTrue($this->_object->isDescending());
        $this->_object->setSortOrder(false);
        $this->assertFalse($this->_object->isDescending());
    }

    /**
     * @test
     */
    public function testIsDescending()
    {
        $this->assertFalse($this->_object->isDescending(), 'Sort order should default to ascending');
    }

    /**
     * @test
     */
    public function testSetSearchTerm()
    {
        $this->_object->setSearchTerm("test");
        $this->assertEquals("test", $this->_object->getSearchTerm());
    }

    /**
     * @test
     */
    public function testGetSearchTerm()
    {
        $this->assertEquals("", $this->_object->getSearchTerm(), 'Search term should be empty by default.');
    }

    /**
     * @test
     */
    public function testUpdateSetup()
    {
        $this->_object->setFilter("some_column", "value that should be erased");
        $request = array(
            'page' => 1,
            'entries' => 2,
            'layout' => 3,
            'search' => "search",
            'dropfilter' => true,
            'filter' => array('colum' => 'filter'),
            'orderby' => "column",
            'desc' => true
        );
        $this->_object->updateSetup($request);
        $this->assertEquals(1, $this->_object->getPage());
        $this->assertEquals(2, $this->_object->getEntriesPerPage());
        $this->assertEquals(3, $this->_object->getLayout());
        $this->assertEquals("search", $this->_object->getSearchTerm());
        $this->assertEquals(array('colum' => 'filter'), $this->_object->getFilters());
        $this->assertEquals("column", $this->_object->getOrderByField());
        $this->assertEquals(true, $this->_object->isDescending());
    }

    /**
     * @test
     */
    public function testDeleteAction()
    {
        $this->_object->setDeleteAction(__FUNCTION__);
        $this->assertEquals(__FUNCTION__, $this->_object->getDeleteAction());
    }

    /**
     * @test
     */
    public function testDownloadAction()
    {
        $this->_object->setDownloadAction(__FUNCTION__);
        $this->assertEquals(__FUNCTION__, $this->_object->getDownloadAction());
    }

    /**
     * @test
     */
    public function testInsertAction()
    {
        $this->_object->setInsertAction(__FUNCTION__);
        $this->assertEquals(__FUNCTION__, $this->_object->getInsertAction());
    }

    /**
     * @test
     */
    public function testUpdateAction()
    {
        $this->_object->setUpdateAction(__FUNCTION__);
        $this->assertEquals(__FUNCTION__, $this->_object->getUpdateAction());
    }

    /**
     * @test
     */
    public function testSearchAction()
    {
        $this->_object->setSearchAction(__FUNCTION__);
        $this->assertEquals(__FUNCTION__, $this->_object->getSearchAction());
    }

    /**
     * @test
     */
    public function testExportAction()
    {
        $this->_object->setExportAction(__FUNCTION__);
        $this->assertEquals(__FUNCTION__, $this->_object->getExportAction());
    }

}

?>