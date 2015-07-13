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

namespace Yana\VDrive;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * Test class for VDrive
 *
 * @package  test
 */
class VDriveTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \Yana\VDrive\VDrive
     */
    private $_object;

    /**
     * @var  \Yana\VDrive\VDrive
     */
    private $_inavalidDrive;

    /**
     * @var  string
     */
    private $_path = 'resources/my.drive.xml';

    /**
     * @var  string
     */
    private $_baseDir = '/resources/';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new VDrive(CWD . $this->_path, CWD . $this->_baseDir);
        VDrive::useDefaults(false);
        // create a vdrive with a non exist path
        $this->_inavalidDrive = new VDrive(CWD . '/resources/noexist.xml', CWD . '/resources/');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->_object, $this->_inavalidDrive);
    }

    /**
     * read invalid Argument
     *
     * @expectedException \PHPUnit_Framework_Error
     * @test
     */
    public function testReadInvalidArgument()
    {
        // expected an excepion before checking content var
        $this->_inavalidDrive->read();
    }

    /**
     * get invalid Argument
     *
     * @expectedException \Yana\Core\Exceptions\NotFoundException
     * @test
     */
    public function testGetInvalidArgument()
    {
        $vDrive = new VDrive(CWD . $this->_path);
        $vDrive->getResource('noexist');
    }

    /**
     * get content
     *
     * @test
     */
    public function testGetContent()
    {
        $vDrive = new VDrive(CWD . $this->_path);
        $content = $vDrive->getContent();
        $this->assertInternalType('string', $content, 'the value should be of type string');
        unset($vDrive, $content);
    }

    /**
     * is empty
     *
     * @test
     */
    public function testIsEmpty()
    {
        $empty = $this->_inavalidDrive->isEmpty();
        // expected true for an empty source
        $this->assertTrue($empty, 'VDrive-definitions does not exist, is not redable or is empty');

        // expected false for existing path
        $this->assertFalse($this->_object->isEmpty(), 'the expected value can not be empty');
    }

    /**
     * Get Report
     *
     * @test
     */
    public function testGetReport()
    {
        $getReport = $this->_inavalidDrive->getReport();
        // expected an object
        $this->assertTrue($getReport instanceof \Yana\Report\Xml, 'expected an object of type \Yana\Report\Xml');

        // expected an object instanceof \Yana\Report\Xml without errors and warnings
        $this->_object->read();
        $getReport = $this->_object->getReport();
        $this->assertInternalType('object', $getReport, 'the value should be of type object');
        $this->assertTrue($getReport instanceof \Yana\Report\Xml, 'the value should be an instance of \Yana\Report\Xml');
        $this->assertEquals(0, count($getReport->getErrors()), 'there should be no errors');
        $this->assertEquals(0, count($getReport->getWarnings()), 'there should be no warnings');

        $string = (string) $this->_object;
        $this->assertInternalType('string', $string, 'assert faield, the value should be of type string');
        $this->assertNotEquals(0, strlen($string), 'the value cannot be empty');
    }

    /**
     * @test
     */
    public function testSerialize()
    {
        $this->_object->read();
        $serialize = $this->_object->serialize();
        $this->assertInternalType('string', $serialize, 'assert faield, the value should be of type string');

        // expected an object
        $unserialize = unserialize(serialize($this->_object));
        $this->assertInternalType('object', $unserialize, 'the value should be of type object');
        $this->assertEquals($this->_object, $unserialize, 'both ojects must be the same');
    }

    /**
     * @test
     */
    public function test()
    {
        $xml = simplexml_load_file(CWD . 'resources/my.drive.xml');

        // test file loading
        $this->assertEquals($this->_object->getContent(), $xml->asXML(), '"file loading" test failed');

        $path = $this->_object->getPath();
        $this->assertEquals(CWD . $this->_path, $path, 'the expected path should be the same as givin');

        // expected an object from element name default_config.sml
        $get = $this->_object->getResource('system:/config/profiledir/default_config.sml');
        //$get = $this->object->getResource('config/profiles/default.sml');
        $this->assertInternalType('object', $get, 'the value should be of type object');
        $this->assertEquals(CWD . $this->_baseDir . '{$CONFIGDIR}profiles/default.config', $get->getPath(), 'the given path should be match the expected');

        // expected the same result like in get() function
        $_get = $this->_object->__get('system:/config/profiledir/default_config.sml');
        $this->assertInternalType('object', $_get, 'the value should be of type object');
        $this->assertEquals($get, $_get, 'the values should be equal');

        unset($get, $_get);

        $get = $this->_object->__get('system:/config/profiledir/config.sml');
        $this->assertInternalType('object', $get, 'the value should be of type object');
        $this->assertEquals(CWD . $this->_baseDir . '{$CONFIGDIR}profiles/default.config', $get->getPath(), 'the given path should be match the expected');
        unset($get);

        // get content of the xml file - xml string expected
        $getContent = $this->_object->getContent();
        $this->assertInternalType('string', $getContent, 'the value should be of type string');
        $this->assertNotEquals(0, strlen($getContent), 'the expected value should be not empty');


        // array of an xml file
        $get = $this->_object->getMountpoints();
        $this->assertInternalType('array', $get, 'the value should be of type array');
        $this->assertArrayHasKey('system:/skin/skindir', $get, 'the expected key should be in array');
        $this->assertArrayHasKey('system:/smile', $get, 'the expected key should be in array');

        $vDrive = new VDrive(CWD . $this->_path, CWD . $this->_baseDir);
        VDrive::useDefaults(true);
        // expected the last path in source
        $get = $this->_object->__get('system:/config/profiledir/config.sml');
        $this->assertInternalType('object', $get, 'the value should be of type object');
        $this->assertEquals(CWD . $this->_baseDir . '{$CONFIGDIR}profiles/default.config', $get->getPath(), 'the given path should be match the expected');
        unset($vDrive);
    }

}

?>