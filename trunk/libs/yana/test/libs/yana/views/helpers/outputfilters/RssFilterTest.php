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

namespace Yana\Views\Helpers\OutputFilters;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/../../../../../include.php';

/**
 * @package test
 * @ignore
 */
class RssFilterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Views\Helpers\OutputFilters\RssFilter
     */
    protected $object;

    /**
     * @var \Yana\Core\Dependencies\IsApplicationContainer
     */
    protected $container;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!\class_exists('\Smarty') || !\class_exists('\Smarty_Internal_Template')) {
            $this->markTestSkipped();
        }
        $configurationFactory = new \Yana\ConfigurationFactory();
        $configuration = $configurationFactory->loadConfiguration(CWD . 'resources/system.config.xml');
        $configuration->configdrive = YANA_INSTALL_DIR . 'config/system.drive.xml';
        $this->container = new \Yana\Core\Dependencies\Container($configuration);
        $this->object = new \Yana\Views\Helpers\OutputFilters\RssFilter($this->container);
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
    public function testGetRssFeeds()
    {
        $this->assertNull(\Yana\RSS\Publisher::unpublishFeeds());
        $this->assertSame(array(), $this->object->getRssFeeds());
    }
    /**
     * @test
     */
    public function testAddRssFeeds()
    {
        $this->assertSame(array("test"), $this->object->addRssFeed("test")->getRssFeeds());
    }

    /**
     * @test
     */
    public function test__invokeEmpty()
    {
        $this->assertSame("", $this->object->__invoke(""));
    }

    /**
     * @test
     */
    public function test__invoke()
    {
        $this->object->addRssFeed("Test!");
        $expected = '/^<head><title>Test<\/title>\s+<link rel="alternate" type="application\/rss\+xml" title="{lang id=\'PROGRAM_TITLE\'}" href="http:\/\/[^"]*\?id=default&amp;action=Test%21"\/>\s+<\/head>$/s';
        $this->assertRegExp($expected, $this->object->__invoke("<head><title>Test</title></head>"));
    }

    /**
     * @test
     */
    public function test__invokeWithLineBreak()
    {
        $this->object->addRssFeed("Test!");
        $expected = '/^\s+<head>\s+<title>Test<\/title>\s+<link rel="alternate" type="application\/rss\+xml" title="{lang id=\'PROGRAM_TITLE\'}" href="http:\/\/[^"]*\?id=default&amp;action=Test%21"\/>\s+<\/head>\s+body$/s';
        $this->assertRegExp($expected, $this->object->__invoke("\t<head>\n\t\t<title>Test</title>\n\t</head>\nbody"));
    }

}
