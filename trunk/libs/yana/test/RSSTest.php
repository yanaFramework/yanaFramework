<?php
/**
 * PHPUnit test-case: RSS
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
 * Test class for RSS
 *
 * @package  test
 */
class RSSTest extends PHPUnit_Framework_TestCase
{
    /**
     * @access protected
     */
    protected $_rss;

     /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        // intentionally left blank
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        chdir(CWD . '/../../..');
        Yana::getInstance();
        $this->_rss = new RSS();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        unset($this->_rss);
        chdir(CWD);
    }

    /**
     * ToSting
     *
     * @test
     */
    public function testToString()
    {
        $this->_rss->title = 'rssTitle';
        $this->_rss->link = 'http://www.rsslink.tld';
        $this->_rss->description = 'test';
        $toString = $this->_rss->toString();
        $this->assertType('string', $toString, 'assert failed, the value is not from type string');

        // check if rss is valid
        $url = str_replace(' ', '%20', CWD.'resources/dtd/rss.dtd');
        $url = str_replace('\\', '/', $url);
        $dtd = '<!DOCTYPE  rss SYSTEM "file:///' . $url . '">';
        $pattern = '/\<\?xml[^>]*?\>/';
        $replace = preg_replace($pattern, '$0' . $dtd, $toString);

        $dom = new DOMDocument();
        $dom->loadXML($replace);
        $valid = $dom->validate();

        $this->assertTrue($valid, 'assert failed, the rss Feed is not a valid document');
    }

    /**
     * AddItem
     *
     * @test
     */
    public function testAddItem()
    {
        $newItem = new RSSitem();
        $newItem->title = 'asdas';
        $newItem->link = 'http://www.domain.tld';
        $newItem->description = 'asd';
        $this->_rss->addItem($newItem);

        $this->assertObjectHasAttribute('item', $this->_rss, 'assert failed, the object should be have the attribute "item"');
        $this->assertEquals('http://www.domain.tld', $this->_rss->item[0]['link'], 'assert failed, the values should be equal');
        $this->assertTrue(count($this->_rss->item) == 1, 'assert failed, the result should be true - only 1 item expected');
        $this->_rss->link = 'http://www.domain.tld';
        $this->_rss->description = 'test';
        $toString = $this->_rss->toString();
        $this->assertType('string', $toString, 'assert failed, value is not from type string');

        // check if rss is valid
        $url = str_replace(' ', '%20', CWD.'resources/dtd/rss.dtd');
        $url = str_replace('\\', '/', $url);
        $dtd = '<!DOCTYPE  rss SYSTEM "file:///' . $url . '">';
        $pattern = '/\<\?xml[^>]*?\>/';
        $replace = preg_replace($pattern, '$0' . $dtd, $toString);

        $dom = new DOMDocument();
        $dom->loadXML($replace);
        $valid = $dom->validate();

        $this->assertTrue($valid, 'assert failed, the rss Feed is not a valid document');
    }

    /**
     * Publish feed
     *
     * @test
     */
    public function testPublishFeed()
    {
        RSS::publishFeed('test');
        $feeds = RSS::getFeeds();
        $this->assertEquals(array('test'), $feeds, 'List of feeds must contain recently published feed.');
    }

    /**
     * test 1
     *
     * @test
     */
    public function test1()
    {

        $this->_rss->title = 'test';
        $this->_rss->description = 'rss feed';

        // create item
        $item = new RSSitem();
        $item->title = 'entry';
        $item->link = 'http://www.domain.tld';
        $item->description = 'ghjkl';
        $this->_rss->addItem($item);

        // create item
        $item = new RSSitem();
        $item->title = 'new entry';
        $item->link = 'http://www.domain.tld';
        $item->description = 'ytrewq';
        $this->_rss->addItem($item);

         // create item
        $item = new RSSitem();
        $item->title = 'old entry';
        $item->link = 'http://www.domain.tld';
        $item->description = 'qwerty';
        $this->_rss->addItem($item);

        $toString = $this->_rss->toString();
        $this->assertType('string', $toString, 'assert failed, the value is not from type string');

        // check if rss is valid
        $url = str_replace(' ', '%20', CWD.'resources/dtd/rss.dtd');
        $url = str_replace('\\', '/', $url);
        $dtd = '<!DOCTYPE  rss SYSTEM "file:///' . $url . '">';
        $pattern = '/\<\?xml[^>]*?\>/';
        $replace = preg_replace($pattern, '$0' . $dtd, $toString);

        $dom = new DOMDocument();
        $dom->loadXML($replace);
        $valid = $dom->validate();

        $this->assertTrue($valid, 'assert failed, the rss Feed is not a valid document');
    }
}
?>