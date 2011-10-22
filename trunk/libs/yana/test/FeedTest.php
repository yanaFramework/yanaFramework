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

namespace Yana\RSS;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';


/**
 * Test class for RSS
 *
 * @package  test
 */
class FeedTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @access protected
     * @var    \Yana\RSS\Feed
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
        \Yana::getInstance();
        $this->_rss = new Feed('description');
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
     * @test
     */
    public function testTitle()
    {
        $this->assertEquals('Title text', $this->_rss->setTitle('Title text')->getTitle());
    }

    /**
     * @test
     */
    public function testLink()
    {
        $this->assertEquals('Feed Link', $this->_rss->setLink('Feed Link')->getLink());
    }

    /**
     * @test
     */
    public function testGetDescription()
    {
        $this->assertEquals('description', $this->_rss->getDescription());
    }

    /**
     * @test
     */
    public function testSetDescription()
    {
        $this->assertEquals('Feed Description', $this->_rss->setDescription('Feed Description')->getDescription());
    }

    /**
     * @test
     */
    public function testLanguage()
    {
        $this->assertEquals('en-US', $this->_rss->setLanguage('en-US')->getLanguage());
    }

    /**
     * @test
     */
    public function testGetCopyright()
    {
        $this->assertEquals('', $this->_rss->getCopyright());
    }

    /**
     * @test
     */
    public function testSetCopyright()
    {
        $this->assertEquals('Feed Copyright', $this->_rss->setCopyright('Feed Copyright')->getCopyright());
    }

    /**
     * @test
     */
    public function testGetManagingEditor()
    {
        $this->assertEquals('', $this->_rss->getManagingEditor());
    }

    /**
     * @test
     */
    public function testSetManagingEditor()
    {
        $this->assertEquals('Feed@ManagingEditor', $this->_rss->setManagingEditor('Feed@Managing Editor')->getManagingEditor());
    }

    /**
     * @test
     */
    public function testGetWebMaster()
    {
        $this->assertEquals('', $this->_rss->getWebMaster());
    }

    /**
     * @test
     */
    public function testSetWebMaster()
    {
        $this->assertEquals('Feed@WebMaster', $this->_rss->setWebMaster('Feed@Web Master')->getWebMaster());
    }

    /**
     * @test
     */
    public function testGetTimeToLive()
    {
        $this->assertEquals(0, $this->_rss->getTimeToLive());
    }

    /**
     * @test
     */
    public function testSetTimeToLive()
    {
        $this->assertEquals(1000, $this->_rss->setTimeToLive(1000)->getTimeToLive());
        $this->assertEquals(0, $this->_rss->setTimeToLive(0)->getTimeToLive());
    }

    /**
     * @test
     */
    public function testGetImage()
    {
        $this->assertEquals('', $this->_rss->getImage());
    }

    /**
     * @test
     */
    public function testSetImage()
    {
        $this->assertEquals('Feed Image', $this->_rss->setImage('Feed Image')->getImage());
    }

    /**
     * @test
     */
    public function testGetCategory()
    {
        $this->assertEquals(array(), $this->_rss->getCategory());
    }

    /**
     * @test
     */
    public function testSetCategory()
    {
        $this->assertEquals(array('Feed Category'), $this->_rss->setCategory(array('Feed Category'))->getCategory());
    }

    /**
     * @test
     */
    public function testGetCss()
    {
        $this->assertEquals('', $this->_rss->getCss());
    }

    /**
     * @test
     */
    public function testSetCss()
    {
        $this->assertEquals('Feed Css', $this->_rss->setCss('Feed Css')->getCss());
    }

    /**
     * @test
     */
    public function testGetXslt()
    {
        $this->assertEquals('', $this->_rss->getXslt());
    }

    /**
     * @test
     */
    public function testSetXslt()
    {
        $this->assertEquals('Feed Xslt', $this->_rss->setXslt('Feed Xslt')->getXslt());
    }

    /**
     * @test
     */
    public function testGetItems()
    {
        $this->assertEquals(array(), $this->_rss->getItems());
    }

    /**
     * @test
     */
    public function testAddItem()
    {
        $newItem = new Item('title');
        $this->_rss->addItem($newItem);
        $this->assertEquals(array($newItem), $this->_rss->getItems());
    }

    /**
     * @test
     */
    public function testToSimpleXml()
    {
        $this->_rss->setTitle('title')
            ->setDescription('description')
            ->setCategory(array('category'))
            ->setCopyright('copyright')
            ->setImage('image')
            ->setLanguage('la-NG')
            ->setLink('link')
            ->setManagingEditor('managingEditor')
            ->setTimeToLive(10)
            ->setWebMaster('webMaster');

        $xml = $this->_rss->toSimpleXml();
        $channel = $xml->channel;
        $this->assertTrue(isset($channel->docs));
        $this->assertTrue(isset($channel->pubDate));
        $this->assertTrue(isset($channel->generator));
        $this->assertEquals('title', (string) $channel->title);
        $this->assertEquals('link', (string) $channel->link);
        $this->assertEquals('description', (string) $channel->description);
        $this->assertEquals('la-NG', (string) $channel->language);
        $this->assertEquals('managingEditor', (string) $channel->managingEditor);
        $this->assertEquals('webMaster', (string) $channel->webMaster);
        $this->assertEquals(10, (int) $channel->ttl);
        $this->assertEquals('category', (string) $channel->category);
        $this->assertEquals('title', (string) $channel->image->title);
        $this->assertEquals('link', (string) $channel->image->link);
        $this->assertEquals('image', (string) $channel->image->url);
    }

    /**
     * @test
     */
    public function testToString()
    {
        $this->_rss->setTitle('title');
        $this->_rss->setLink('http://www.rsslink.tld');
        $this->_rss->setCss('test.css');
        $this->_rss->setXslt('test.xsl');
        $toString = (string) $this->_rss;
        $this->assertContains('test.css', $toString);
        $this->assertContains('test.xsl', $toString);
        $this->assertType('string', $toString, 'assert failed, the value is not from type string');

        // check if rss is valid
        $url = str_replace(' ', '%20', CWD.'resources/dtd/rss.dtd');
        $url = str_replace('\\', '/', $url);
        $dtd = '<!DOCTYPE  rss SYSTEM "file:///' . $url . '">';
        $pattern = '/\<\?xml.*\?\>/s';
        $replace = preg_replace($pattern, '$0' . $dtd, $toString);

        $dom = new \DOMDocument();
        $dom->loadXML($replace);
        $valid = $dom->validate();

        $this->assertTrue($valid, 'assert failed, the rss Feed is not a valid document');
    }

}

?>