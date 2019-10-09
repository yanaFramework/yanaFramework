<?php
/**
 * YANA library
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
 * @package  yana
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

namespace Yana\RSS;

/**
 * <<Utility>> Feed publishing container.
 *
 * This class offers functionality to publish news feeds.
 * It is used as a container for the view layer to publish links to registered RSS feeds.
 *
 * To show the RSS logo and link from inside a plugin use this example:
 * <code>\Yana\RSS\Publisher::publishFeed('rssFoobar')</code>
 * Where 'rssFoobar' is the value for the "action" parameter of the URL.
 * You are to implement the code for the action (=plugin function) yourself.
 * To find out how to do that, see the documentation to class {@see Feed}.
 *
 * @package     yana
 * @subpackage  rss
 */
class Publisher extends \Yana\Core\AbstractUtility
{

    /**
     * list of published RSS-feeds
     *
     * @var array
     * @ignore
     */
    protected static $_feeds = array();

    /**
     * Publish a RSS-feed.
     *
     * Adds the action identified by $action to the list of rss-feeds to be offered to the user.
     * You should define a function with the name of $action in your plugin,
     * that must produce the RSS content.
     *
     * Example of usage:
     *
     * Add this to your plugin code
     * <code>\Yana\RSS\Publisher::publishFeed('create_rss');</code>
     * Where 'create_rss' is the name of the plugin function
     * that displays the rss feed.
     *
     * @param   string  $action  action
     */
    public static function publishFeed($action)
    {
        assert(is_string($action), 'Wrong argument type argument 1. String expected');

        self::$_feeds[] = "$action";
        array_unique(self::$_feeds);
    }

    /**
     * Get list of all previously published RSS-feeds.
     *
     * @return  array
     */
    public static function getFeeds()
    {
        assert(is_array(self::$_feeds), 'Member "feeds" should be an array.');
        return self::$_feeds;
    }

}

?>