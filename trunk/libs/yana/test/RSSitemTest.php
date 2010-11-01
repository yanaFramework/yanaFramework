<?php
/**
 * PHPUnit test-case: RSSitem
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
 * Test class for RSSitem
 *
 * @package  test
 */
class RSSitemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @access protected
     */
    protected $item;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->item = new RSSitem();
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
     * Get
     *
     * @test
     */
    public function testGet()
    {
        // set title and description for rssItem
        $this->item->title = 'abcde';
        $this->item->description = 'qwerty';

        $get = $this->item->get();
        $this->assertType('array', $get, 'assert failed, the value is not from type array');
        $this->assertEquals('abcde', $get['title'], 'assert failed, the values should be equal');
        $this->assertEquals('qwerty', $get['description'], 'assert failed, the values should be equal');
        $this->assertEquals(8, count($get), 'assert failed, the value should be have 8 entries');

        // set author, comments, link
        $this->item->author = 'mail@domain.tld';
        $this->item->comments = 'comments';
        $this->item->link = 'https://www.domain.tld';

        $newGet = $this->item->get();
        // expected true "abcde"
        $this->assertEquals($newGet['title'], $get['title'], 'assert failed, the values should be equal');
        // expected false - different values
        $this->assertNotEquals($newGet['author'], $get['author'], 'assert failed, the values cant be equal');
        // expected true "author"
        $this->assertEquals('mail@domain.tld', $newGet['author'], 'assert failed, the values should be equal');
        // expected true "comments"
        $this->assertEquals('comments', $newGet['comments'], 'assert failed, the values should be equal');
        // expected true "link"
        $this->assertEquals('https://www.domain.tld', $newGet['link'], 'assert failed, the values should be equal');
    }
}
?>