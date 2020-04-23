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

namespace Yana\RSS;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * @package  test
 */
class PublisherTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testGetFeeds()
    {
        $this->assertEquals(array(), Publisher::getFeeds());
    }

    /**
     * @test
     */
    public function testUnpublishFeeds()
    {
        $this->assertNull(\Yana\RSS\Publisher::unpublishFeeds());
    }

    /**
     * @test
     */
    public function testPublishFeed()
    {
        $this->assertNull(\Yana\RSS\Publisher::unpublishFeeds());
        $this->assertEquals(array(), Publisher::getFeeds());
        $feeds = array(
            'Feed Action 1',
            'Feed Action 2'
        );
        Publisher::publishFeed($feeds[0]);
        Publisher::publishFeed($feeds[1]);
        $this->assertEquals($feeds, Publisher::getFeeds());
        $this->assertNull(\Yana\RSS\Publisher::unpublishFeeds());
        $this->assertEquals(array(), Publisher::getFeeds());
    }

}
