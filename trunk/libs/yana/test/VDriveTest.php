<?php
/**
 * PHPUnit test-case: VDrive
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

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';

/**
 * Test class for VDrive
 *
 * @package  test
 */
class VDriveTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var    VDrive
     * @access protected
     */
    protected $_object;

    /**
     * @var    path
     *
     * @access protected
     */
    protected $_path = 'resources/my.drive.xml';

    /**
     * @var    basDir
     *
     * @access protected
     */
    protected $_baseDir = '/resources/';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->_object = new VDrive(CWD.$this->_path, CWD.$this->_baseDir);
        VDrive::useDefaults(false);
        // create a vdrive with a non exist path
        $this->no_vdrive = new VDrive(CWD.'/resources/noexist.xml', CWD.'/resources/');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        unset($this->_object, $this->no_vdrive);
    }

    /**
     * read invalid Argument
     *
     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    function testReadInvalidArgument()
    {
        // expected an excepion before checking content var
        $this->no_vdrive->read();
    }

    /**
     * get invalid Argument
     *
     * @expectedException NotFoundException
     * @test
     */
    function testGetInvalidArgument()
    {
        $vDrive = new VDrive(CWD.$this->_path);
        $vDrive->getResource('noexist');
    }

    /**
     * get content
     *
     * @test
     */
    function testGetContent()
    {
        $vDrive = new VDrive(CWD.$this->_path);
        $content = $vDrive->getContent();
        $this->assertType('string', $content, 'assert failed, the value should be of type string');
        unset($vDrive, $content);
    }

    /**
     * is empty
     *
     * @test
     */
    function testIsEmpty()
    {
        $empty = $this->no_vdrive->isEmpty();
        // expected true for an empty source
        $this->assertTrue($empty, 'assert failed, VDrive-definitions does not exist, is not redable or is empty');
    }

    /**
     * get Report
     *
     * @test
     */
    function testGetReport()
    {
        $getReport = $this->no_vdrive->getReport();
        // expected an object
        $this->assertType('object', $getReport, 'assert failed, the value should be of type object');
        $this->assertTrue($getReport instanceof ReportXML, 'assert failed, expected an object of type ReportXML');
    }

    /**
     * test
     *
     * @test
     */
    public function test()
    {
        $xml = simplexml_load_file(CWD . 'resources/my.drive.xml');

        // test file loading
        $this->assertEquals($this->_object->getContent(), $xml->asXML(), '"file loading" test failed');


        $path = $this->_object->getPath();
        $this->assertEquals(CWD.$this->_path, $path, 'assert failed, the expected path should be the same as givin');

        // expected an object from element name default_config.sml
        $get = $this->_object->getResource('system:/config/profiledir/default_config.sml');
        //$get = $this->object->getResource('config/profiles/default.sml');
        $this->assertType('object', $get, 'assert failed, the value should be of type object');
        $this->assertEquals(CWD.$this->_baseDir.'{$CONFIGDIR}profiles/default.config', $get->getPath(), 'assert failed, the given path should be match the expected');

        // expected the same result like in get() function
        $_get = $this->_object->__get('system:/config/profiledir/default_config.sml');
        $this->assertType('object', $_get, 'assert failed, the value should be of type object');
        $this->assertEquals($get, $_get, 'assert failed, the values should be equal');

        unset($get, $_get);

        $get = $this->_object->__get('system:/config/profiledir/config.sml');
        $this->assertType('object', $get, 'assert failed, the value should be of type object');
        $this->assertEquals(CWD.$this->_baseDir.'{$CONFIGDIR}profiles/default.config', $get->getPath(), 'assert failed, the given path should be match the expected');
        unset($get);

        // get content of the xml file - xml string expected
        $getContent = $this->_object->getContent();
        $this->assertType('string', $getContent, 'assert failed, the value should be of type string');
        $this->assertNotEquals(0, strlen($getContent), 'assert failed, the expected value should be not empty');


        // array of an xml file
        $get = $this->_object->getMountpoints();
        $this->assertType('array', $get, 'assert failed, the value should be of type array');
        $this->assertArrayHasKey('system:/skin/skindir', $get, 'assert failed, the expected key should be in array');
        $this->assertArrayHasKey('system:/smile', $get, 'assert failed, the expected key should be in array');

        // expected false for existing path
        $empty = $this->_object->isEmpty();
        $this->assertFalse($empty, 'assert failed, the expected value can not be empty');

        // expected an object instanceof reportxml without errors and warnings
        $getReport = $this->_object->getReport();
        $this->assertType('object', $getReport, 'assert failed, the value should be of type object');
        $this->assertTrue($getReport instanceof ReportXML, 'assert failed, the value should be an instance of ReportXML');
        $this->assertEquals(0, count($getReport->getErrors()), 'assert failed, there should be no errors');
        $this->assertEquals(0, count($getReport->getWarnings()), 'assert failed, there should be no warnings');

        $string = $this->_object->toString();
        $this->assertType('string', $string, 'assert faield, the value should be of type string');
        $this->assertNotEquals(0, strlen($string), 'assert failed, the value can not be empty');

        $serialize = serialize($this->_object);
        $this->assertType('string', $serialize, 'assert faield, the value should be of type string');

        // expected an object
        $unserialize = unserialize($serialize);
        $this->assertType('object', $unserialize, 'assert failed, the value should be of type object');
        $this->assertEquals($this->_object, $unserialize, 'assert failed, both ojects must be the same');

        $vDrive = new VDrive(CWD.$this->_path, CWD.$this->_baseDir);
        VDrive::useDefaults(true);
        // expected the last path in source
        $get = $this->_object->__get('system:/config/profiledir/config.sml');
        $this->assertType('object', $get, 'assert failed, the value should be of type object');
        $this->assertEquals(CWD.$this->_baseDir.'{$CONFIGDIR}profiles/default.config', $get->getPath(), 'assert failed, the given path should be match the expected');
        unset($vDrive);
    }

}

?>