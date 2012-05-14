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
require_once __DIR__ . '/../../../include.php';

/**
 * Test class for RSSitem
 *
 * @package  test
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Item
     */
    protected $item;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->item = new \Yana\RSS\Item('Abcöäß');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        unset($this->item);
    }

    /**
     * @test
     */
    public function testGetTitle()
    {
        $this->assertEquals('Abcöäß', $this->item->getTitle());
    }

    /**
     * @test
     */
    public function testGetLink()
    {
        $this->assertEquals('', $this->item->getLink());
    }

    /**
     * @test
     */
    public function testSetLink()
    {
        $this->assertEquals('Abcöäß', $this->item->setLink('Abcöäß')->getLink());
    }

    /**
     * @test
     */
    public function testGetDescription()
    {
        $this->assertEquals('', $this->item->getDescription());
    }

    /**
     * @test
     */
    public function testSetDescription()
    {
        $this->assertEquals('Abcöäß', $this->item->setDescription('Abcöäß')->getDescription());
    }

    /**
     * @test
     */
    public function testGetAuthor()
    {
        $this->assertEquals('', $this->item->getAuthor());
    }

    /**
     * @test
     */
    public function testSetAuthor()
    {
        $this->assertEquals('Abc@domain.tld', $this->item->setAuthor('Abc@öäßdomain.tld')->getAuthor());
    }

    /**
     * @test
     */
    public function testGetCategory()
    {
        $this->assertEquals(array(), $this->item->getCategory());
    }

    /**
     * @test
     */
    public function testSetCategory()
    {
        $category = array('Abcöäß', 'dEf');
        $this->assertEquals($category, $this->item->setCategory($category)->getCategory());
    }

    /**
     * @test
     */
    public function testGetComments()
    {
        $this->assertEquals('', $this->item->getComments());
    }

    /**
     * @test
     */
    public function testSetComments()
    {
        $url = 'http://sub.domain.tld/path';
        $this->assertEquals($url, $this->item->setComments($url)->getComments());
    }

    /**
     * @test
     */
    public function testPubDate()
    {
        $time = date('r');
        $this->assertEquals($time, $this->item->setPubDate($time)->getPubDate());
    }

    /**
     * @test
     */
    public function testToString()
    {
        $this->item->setAuthor('author');
        $this->item->setCategory(array('category'));
        $this->item->setComments('comments');
        $this->item->setDescription('description');
        $this->item->setLink('link');
        $this->item->setPubDate('date');
        $xml = $this->item->toSimpleXml();
        $this->assertTrue(isset($xml->title));
        $this->assertTrue(isset($xml->guid));
        $this->assertEquals('date', (string) $xml->pubDate);
        $this->assertEquals('description', (string) $xml->description);
        $this->assertEquals('category', (string) $xml->category);
        $this->assertEquals('author', (string) $xml->author);
    }

}

?>