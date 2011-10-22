<?php

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__) . '/../pluginclasscollection.php';

/**
 * Test class for PluginClassCollection.
 * Generated by PHPUnit on 2011-03-23 at 02:56:43.
 */
class PluginClassCollectionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var PluginClassCollection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new PluginClassCollection();
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
    public function testOffsetSet()
    {
        $o = new PluginConfigurationClass();
        $o->setClassName('Plugin_ClassName');
        $this->object['test'] = $o;
        $this->assertTrue($this->object['test'] instanceof PluginConfigurationClass, 'Instance was not added.');
        $this->assertEquals($this->object['test']->getClassName(), $o->getClassName());
    }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $o = new PluginConfigurationClass();
        $o->setClassName('Plugin_ClassName');
        $this->object['test'] = $o;
        $this->assertTrue($this->object['test'] instanceof PluginConfigurationClass, 'Instance was not added.');
        unset($this->object['test']);
        $this->assertTrue($this->object['test'] === null, 'Instance was not unset.');
    }

    /**
     * @test
     */
    public function testOffsetSetAutodetect()
    {
        $o = new PluginConfigurationClass();
        $o->setClassName('Plugin_ClassName');
        $this->object[] = $o;
        $this->assertTrue($this->object['classname'] instanceof PluginConfigurationClass, 'Instance was not added.');
        $this->assertEquals($this->object['classname']->getClassName(), $o->getClassName());
    }

    /**
     * @test
     * @expectedException \Yana\Core\InvalidArgumentException
     */
    public function testOffsetSetInvalidArgumentException()
    {
        $this->object[] = new PluginConfigurationMethod();
    }

}

?>