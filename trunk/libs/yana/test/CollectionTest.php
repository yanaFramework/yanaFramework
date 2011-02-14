<?php

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__) . '/../collection.class.php';

/**
 * Test class for Collection.
 * Generated by PHPUnit on 2011-02-14 at 18:31:44.
 */
class CollectionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Collection
     */
    private $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new Collection(array(0, 1, 2, 3));
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
    public function testIterator()
    {
        foreach ($this->_object as $key => $value)
        {
            $this->assertEquals($key, $value);
            $this->assertTrue($this->_object->valid());
        }
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     */
    public function testIteratorOutOfBoundsException()
    {
        foreach ($this->_object as $key => $value)
        {
            // intenionally left blank
        }
        $this->assertFalse($this->_object->valid());
        $this->_object->current();
    }

    /**
     * @test
     */
    public function testCurrent()
    {
        $i = 0;
        while ($this->_object->valid())
        {
            $this->assertEquals($i, $this->_object->current());
            $this->assertEquals($i, $this->_object->key());
            $this->_object->next();
            $this->assertTrue($i < 4);
            $i++;
        }
    }

    /**
     * @test
     */
    public function testCountable()
    {
        $this->assertEquals(4, count($this->_object));
        $this->assertEquals(4, $this->_object->count());
    }

    /**
     * @test
     */
    public function testToArray()
    {
        $this->assertEquals(array(0, 1, 2, 3), $this->_object->toArray());
    }

    /**
     * @test
     */
    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->_object[1]));
        $this->assertFalse(isset($this->_object[-1]));
    }

    /**
     * @test
     */
    public function testOffsetExistsAPI()
    {
        $this->assertTrue($this->_object->offsetExists(1));
        $this->assertFalse($this->_object->offsetExists(-1));
    }

    /**
     * @test
     */
    public function testOffsetGet()
    {
        $this->assertEquals(1, $this->_object[1]);
        $this->assertEquals(null, $this->_object[-1]);
    }

    /**
     * @test
     */
    public function testOffsetGetViaAPI()
    {
        $this->assertEquals(1, $this->_object->offsetGet(1));
        $this->assertEquals(null, $this->_object->offsetGet(-1));
    }

    /**
     * @test
     */
    public function testOffsetSet()
    {
        $this->assertEquals(null, $this->_object[-1]);
        $this->_object[-1] = 'a';
        $this->assertEquals('a', $this->_object[-1]);
    }

    /**
     * @test
     */
    public function testOffsetSetViaAPI()
    {
        $this->assertEquals(null, $this->_object[-1]);
        $this->_object->offsetSet(-1, 'a');
        $this->assertEquals('a', $this->_object[-1]);
    }

    /**
     * @test
     */
    public function testOffsetUnset()
    {
        $this->assertTrue(isset($this->_object[0]));
        unset($this->_object[0]);
        $this->assertFalse(isset($this->_object[0]));
    }

    /**
     * @test
     */
    public function testOffsetUnsetViaAPI()
    {
        $this->assertTrue(isset($this->_object[0]));
        $this->_object->offsetUnset(0);
        $this->assertFalse(isset($this->_object[0]));
    }

}

?>