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

namespace Yana\Http\Uploads;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class UploadWrapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Http\Uploads\UploadWrapper
     */
    protected $inputArray = array(
        'outer' => array(
            'inner' => array(
                'column1' => array(
                    'name' => 'filename1',
                    'type' => 'type1',
                    'tmp_name' => 'temp_name1',
                    'error' => 1,
                    'size' => 1
                ),
                'column2' => array(
                    'name' => 'filename2',
                    'type' => 'type2',
                    'tmp_name' => 'temp_name2',
                    'error' => 2,
                    'size' => 2
                ),
                'column4' => array(
                    'name' => 'filename4',
                    'type' => 'type4',
                    'tmp_name' => 'temp_name4',
                    'error' => \Yana\Http\Uploads\ErrorEnumeration::NO_FILE,
                    'size' => 3
                )
            )
        )
    );

    /**
     * @var \Yana\Http\Uploads\UploadWrapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Http\Uploads\UploadWrapper($this->inputArray);
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
    public function testKeys()
    {
        $this->assertSame(array('outer'), $this->object->keys());
        $this->assertSame(array('inner'), $this->object->keys('outeR'));
        $this->assertSame(array('column1', 'column2', 'column4'), $this->object->keys('Outer.Inner'));
        $this->assertSame(array('name', 'type', 'tmp_name', 'error', 'size'), $this->object->keys('Outer.Inner.Column1'));
        $this->assertSame(array(), $this->object->keys('Outer.Inner.Column1.name'));
    }

    /**
     * @test
     */
    public function testHas()
    {
        $this->assertFalse($this->object->has('non-existing'));
        $this->assertTrue($this->object->has('outer'));
        $this->assertTrue($this->object->has('Outer'));
        $this->assertTrue($this->object->has('Outer.Inner'));
        $this->assertTrue($this->object->has('Outer.Inner.cOlumN4'));
    }

    /**
     * @test
     */
    public function testIsFile()
    {
        $this->assertFalse($this->object->isFile('non-existing'));
        $this->assertFalse($this->object->isFile('outer'));
        $this->assertFalse($this->object->isFile('Outer'));
        $this->assertFalse($this->object->isFile('Outer.Inner'));
        $this->assertTrue($this->object->isFile('Outer.Inner.cOlumN4'));
    }

    /**
     * @test
     */
    public function testIsListOfFiles()
    {
        $this->assertFalse($this->object->isListOfFiles('non-existing'));
        $this->assertFalse($this->object->isListOfFiles('outer'));
        $this->assertFalse($this->object->isListOfFiles('Outer'));
        $this->assertTrue($this->object->isListOfFiles('Outer.Inner'));
        $this->assertFalse($this->object->isListOfFiles('Outer.Inner.cOlumN4'));
    }

    /**
     * @test
     */
    public function testFile()
    {
        $this->assertTrue($this->object->file('Outer.Inner.cOlumN4') instanceof \Yana\Http\Uploads\IsFile);
    }

    /**
     * @test
     * @expectedException \Yana\Http\Uploads\NotFoundException
     */
    public function testFileNotFoundException1()
    {
        $this->object->file('non-existent');
    }

    /**
     * @test
     * @expectedException \Yana\Http\Uploads\NotFoundException
     */
    public function testFileNotFoundException2()
    {
        $this->object->file('Outer.Inner');
    }

    /**
     * @test
     */
    public function testAll()
    {
        $collection = $this->object->all('Outer.Inner');
        $this->assertTrue($collection instanceof \Yana\Http\Uploads\FileCollection);
        $this->assertEquals(3, $collection->count());
        $this->assertEquals('filename1', $collection['column1']->getName());
        $this->assertEquals('type2', $collection['column2']->getMimeType());
        $this->assertEquals('temp_name4', $collection['column4']->getTemporaryPath());
        $this->assertEquals(1, $collection['column1']->getErrorCode());
        $this->assertEquals(2, $collection['column2']->getSizeInBytes());
    }

    /**
     * @test
     * @expectedException \Yana\Http\Uploads\NotFoundException
     */
    public function testAllNotFoundException1()
    {
        $this->object->all('non-existent');
    }

    /**
     * @test
     * @expectedException \Yana\Http\Uploads\NotFoundException
     */
    public function testAllNotFoundException2()
    {
        $this->object->all('Outer');
    }

}
