<?php

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__) . '/../pluginuserlevel.php';

/**
 * Test class for PluginGrant.
 * Generated by PHPUnit on 2011-03-06 at 21:12:05.
 */
class PluginUserLevelTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var PluginGrant
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new PluginUserLevel();
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
    public function testGetRole()
    {
        $this->assertEquals('', $this->object->getRole());
    }

    /**
     * @test
     */
    public function testSetRole()
    {
        $this->object->setRole('test');
        $this->assertEquals('test', $this->object->getRole());
    }

    /**
     * @test
     * @expectedException \Yana\Core\InvalidArgumentException
     */
    public function testSetRoleInvalidArgumentException()
    {
        $this->object->setRole(' ');
    }

    /**
     * @test
     */
    public function testGetGroup()
    {
        $this->assertEquals('', $this->object->getGroup());
    }

    /**
     * @test
     */
    public function testSetGroup()
    {
        $this->object->setGroup('test');
        $this->assertEquals('test', $this->object->getGroup());
    }

    /**
     * @test
     * @expectedException \Yana\Core\InvalidArgumentException
     */
    public function testSetGroupInvalidArgumentException()
    {
        $this->object->setGroup(' ');
    }

    /**
     * @test
     */
    public function testGetLevel()
    {
        $this->assertEquals(0, $this->object->getLevel());
    }

    /**
     * @test
     */
    public function testSetLevel()
    {
        $this->object->setLevel(0);
        $this->assertEquals(0, $this->object->getLevel());
        $this->object->setLevel(100);
        $this->assertEquals(100, $this->object->getLevel());
    }

    /**
     * @test
     * @expectedException \Yana\Core\InvalidArgumentException
     */
    public function testSetLevelLowerBoundary()
    {
        $this->object->setLevel(-1);
    }

    /**
     * @test
     * @expectedException \Yana\Core\InvalidArgumentException
     */
    public function testSetLevelUpperBoundary()
    {
        $this->object->setLevel(101);
    }

}

?>
