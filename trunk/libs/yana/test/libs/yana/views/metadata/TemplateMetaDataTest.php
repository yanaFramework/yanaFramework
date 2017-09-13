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
 * @package  test
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

namespace Yana\Views\MetaData;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class TemplateMetaDataTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var TemplateMetaData
     */
    protected $_object = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new TemplateMetaData();
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
    public function testGetId()
    {
        $this->assertEquals("", $this->_object->getId());
    }

    /**
     * @test
     */
    public function testSetId()
    {
        $value = 'ÄößAbc01;';
        $this->assertEquals($value, $this->_object->setId($value)->getId());
    }

    /**
     * @test
     */
    public function testGetFile()
    {
        $this->assertEquals("", $this->_object->getFile());
    }

    /**
     * @test
     */
    public function testSetFile()
    {
        $value = 'ÄößAbc01;';
        $this->assertEquals($value, $this->_object->setFile($value)->getFile());
    }

    /**
     * @test
     */
    public function testGetLanguages()
    {
        $this->assertEquals(array(), $this->_object->getLanguages());
    }

    /**
     * @test
     */
    public function testSetLanguages()
    {
        $value = array('test');
        $this->assertEquals($value, $this->_object->setLanguages($value)->getLanguages());
    }

    /**
     * @test
     */
    public function testGetScripts()
    {
        $this->assertEquals(array(), $this->_object->getScripts());
    }

    /**
     * @test
     */
    public function testSetScripts()
    {
        $value = array('test');
        $this->assertEquals($value, $this->_object->setScripts($value)->getScripts());
    }

    /**
     * @test
     */
    public function testGetStyles()
    {
        $this->assertEquals(array(), $this->_object->getStyles());
    }

    /**
     * @test
     */
    public function testSetStyles()
    {
        $value = array('test');
        $this->assertEquals($value, $this->_object->setStyles($value)->getStyles());
    }

}

?>