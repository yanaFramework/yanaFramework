<?php
/**
 * YANA library
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
 * @package  yana
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

namespace Yana\Core\MetaData;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * Test class for MetaData.
 */
class PackageMetaDataTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var MetaData
     */
    protected $_object = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new PackageMetaData();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        // add your code here
    }

    /**
     * @test
     */
    public function testGetPreviewImage()
    {
        $this->assertEquals("", $this->_object->getPreviewImage());
    }

    /**
     * @test
     */
    public function testSetPreviewImage()
    {
        $value = 'ÄößAbc01;';
        $this->assertEquals($value, $this->_object->setPreviewImage($value)->getPreviewImage());
    }

    /**
     * @test
     */
    public function testGetLastModified()
    {
        $this->assertNull($this->_object->getLastModified());
    }

    /**
     * @test
     */
    public function testSetLastModified()
    {
        $value = 1;
        $this->assertEquals($value, $this->_object->setLastModified($value)->getLastModified());
    }

    /**
     * @test
     */
    public function testGetTitle()
    {
        $this->assertEquals("", $this->_object->getTitle());
    }

    /**
     * @test
     */
    public function testSetTitle()
    {
        $value = 'ÄößAbc01;';
        $this->assertEquals($value, $this->_object->setTitle($value)->getTitle());
    }

    /**
     * @test
     */
    public function testGetText()
    {
        $this->assertEquals("", $this->_object->getText());
    }

    /**
     * @test
     */
    public function testSetTexts()
    {
        $value = 'ÄößAbc01;';
        $this->assertEquals($value, $this->_object->setTexts(array("" => $value))->getText());
    }

    /**
     * @test
     */
    public function testGetAuthor()
    {
        $this->assertEquals("", $this->_object->getAuthor());
    }

    /**
     * @test
     */
    public function testSetAuthor()
    {
        $value = 'ÄößAbc01;';
        $this->assertEquals($value, $this->_object->setAuthor($value)->getAuthor());
    }

    /**
     * @test
     */
    public function testGetUrl()
    {
        $this->assertEquals("", $this->_object->getUrl());
    }

    /**
     * @test
     */
    public function testSetUrl()
    {
        $value = 'http://www.Abc01.tld?foo=1#test';
        $this->assertEquals($value, $this->_object->setUrl($value)->getUrl());
    }

}

?>