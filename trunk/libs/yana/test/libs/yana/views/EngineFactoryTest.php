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

namespace Yana\Views;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../include.php';

/**
 * @package  test
 * @ignore
 */
class MyTestPlugin
{
    public function __invoke()
    {
        return "test";
    }
}

/**
 * @package  test
 */
class EngineFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\EngineFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $config = simplexml_load_string('<system><templates>
        <leftdelimiter>{</leftdelimiter>
        <rightdelimiter>}</rightdelimiter>
        <templatedir>skins/</templatedir>
        <templatedir>plugins/</templatedir>
        <cachedir>cache/</cachedir>
        <caching>false</caching>
        <cachelifetime></cachelifetime>
        <cachingtype>file</cachingtype>
        <compilecheck>true</compilecheck>
        <compiledir>cache/</compiledir>
        <configdir>skins/.config/</configdir>
        <debugging>false</debugging>
        <defaultmodifier>replaceToken</defaultmodifier>
        <resourcetype name="id">\Yana\Views\Resources\IdResource</resourcetype>
        <resourcetype name="template">\Yana\Views\Resources\FileResource</resourcetype>
        <resourcetype name="string">\Yana\Views\Resources\StringResource</resourcetype>
        <modifier name="test1" cacheable="true">\Yana\Views\MyTestPlugin</modifier>
        <function name="test2" cacheable="true">\Yana\Views\MyTestPlugin</function>
        <function name="test3" cacheable="false">\Yana\Views\MyTestPlugin</function>
        <blockfunction name="test4" cacheable="true">\Yana\Views\MyTestPlugin</blockfunction>
        <prefilter>\Yana\Views\Helpers\PreFilters\LanguageTokenFilter</prefilter>
        <postfilter>\Yana\Views\Helpers\PostFilters\SpamFilter</postfilter>
        <outputfilter>\Yana\Views\Helpers\OutputFilters\RssFilter</outputfilter>
        <defaultresourcetype>template</defaultresourcetype>
        <usesubdirs>true</usesubdirs>
        <security>
            <phphandling>remove</phphandling>
            <securedir></securedir>
            <trusteddir></trusteddir>
            <staticclass>false</staticclass>
            <phpfunction>isset</phpfunction>
            <phpfunction>empty</phpfunction>
            <phpfunction>count</phpfunction>
            <phpfunction>sizeof</phpfunction>
            <phpfunction>in_array</phpfunction>
            <phpfunction>is_array</phpfunction>
            <phpfunction>time</phpfunction>
            <phpfunction>nl2br</phpfunction>
            <phpmodifier>escape</phpmodifier>
            <phpmodifier>count</phpmodifier>
            <streams>false</streams>
            <allowedmodifier></allowedmodifier>
            <disabledmodifier></disabledmodifier>
            <allowedtag></allowedtag>
            <disabledtag></disabledtag>
            <allowconstants>false</allowconstants>
            <allowsuperglobals>false</allowsuperglobals>
            <allowphptag>false</allowphptag>
        </security>
    </templates></system>');
        $container = new \Yana\Core\Dependencies\Container(\Yana\Util\Xml\Converter::convertXmlToObject($config));
        $this->object = new \Yana\Views\EngineFactory($container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        \chdir(CWD);
    }

    /**
     * Must not raise an error or throw an exception.
     *
     * @test
     */
    public function testCreateInstance()
    {
        \chdir(CWD . '/../../../');
        $this->object->createInstance();
    }

}

?>