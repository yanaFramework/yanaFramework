<?php

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__) . '/../formsetupcachemanager.class.php';

/**
 * Test class for FormSetupCacheManager.
 * Generated by PHPUnit on 2011-04-11 at 20:41:55.
 */
class FormSetupCacheManagerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var FormSetupCacheManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new FormSetupCacheManager();
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
    public function testSet()
    {
        $formSetup = new FormSetup();
        $this->assertFalse(isset($this->object->test));
        $this->object->test = $formSetup;
        $this->assertTrue(isset($this->object->test));
        $result = $this->object->test;
        $this->assertEquals($formSetup, $result);
        unset($this->object->test);
        $this->assertFalse(isset($this->object->test));
    }

}

?>