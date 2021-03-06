<?php
/**
 * PHPUnit test-case.
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
declare(strict_types=1);

namespace Yana\Translations\TextData;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class LanguageInterchangeFileTest extends \PHPUnit_Framework_TestCase
{

    private $_xml = '<?xml version="1.0"?>
        <!DOCTYPE xliff PUBLIC "-//XLIFF//DTD XLIFF//EN" "http://www.oasis-open.org/committees/xliff/documents/xliff.dtd">
        <xliff version="1.0">
            <file source-language="source" datatype="html" original="" target-language="target">
                <header/>
                <body>
                    <group id="group1"/>
                    <group id="group2">
                        <trans-unit id="group2.unit1">
                            <source>source1</source>
                            <target>target1</target>
                        </trans-unit>
                    </group>
                    <trans-unit id="unit2">
                        <source>source2<it id="br1" pos="open">&lt;br /&gt;</it></source>
                        <target>target2<it id="br1" pos="open">&lt;br /&gt;</it></target>
                    </trans-unit>
                </body>
            </file>
        </xliff>';

    /**
     * @var \Yana\Translations\TextData\LanguageInterchangeFile
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Translations\TextData\LanguageInterchangeFile($this->_xml);
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
    public function testGetSourceLanguage()
    {
        $this->assertEquals('source', $this->object->getSourceLanguage());
    }

    /**
     * @test
     */
    public function testGetTargetLanguage()
    {
        $this->assertEquals('target', $this->object->getTargetLanguage());
    }

    /**
     * @test
     */
    public function testSetSourceLanguage()
    {
        $source = 'newSource';
        $this->assertEquals($source, $this->object->setSourceLanguage($source)->getSourceLanguage());
        $attribute = (string) $this->object->file[0]['source-language'];
        $this->assertEquals($source, $attribute);
    }

    /**
     * @test
     */
    public function testSetTargetLanguage()
    {
        $target = 'newTarget';
        $this->assertEquals($target, $this->object->setTargetLanguage($target)->getTargetLanguage());
    }

    /**
     * @test
     */
    public function testGetGroups()
    {
        $input = array(
            "group1" => array("test"),
            "group2" => array("test"),
            "group3" => array("test")
        );
        $groups = array(
            "group1" => array(),
            "group2" => array(
                "group2.unit1" => "unit1"
            ),
            "group3" => array("test")
        );
        $this->assertEquals($groups, $this->object->getGroups($input));
    }

    /**
     * @test
     */
    public function testToArray()
    {
        $input = array(
            "test" => "<b>test</b>"
        );
        $translations = array(
            "test" => "<b>test</b>",
            "group2.unit1" => "target1",
            "unit2" => "target2<br />"
        );
        // Remove the following lines when you implement this test.
        $this->assertEquals($translations, $this->object->toArray($input));
    }

}
