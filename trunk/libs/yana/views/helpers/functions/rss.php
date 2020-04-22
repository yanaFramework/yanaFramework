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
 *
 * @ignore
 */
declare(strict_types=1);

namespace Yana\Views\Helpers\Functions;

/**
 * Smarty-compatible function.
 *
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  views
 */
class Rss extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsFunction
{

    /**
     * List of RSS feeds.
     *
     * @var array
     */
    private $_rssFeeds = array();

    /**
     * Get list of all previously published RSS-feeds.
     *
     * A RSS feed (in this context) is the name of action that creates it,
     * meaning: A string.
     *
     * @return  array
     * @ignore
     */
    public function getRssFeeds()
    {
        if (empty($this->_rssFeeds)) {
            $this->_rssFeeds = \Yana\RSS\Publisher::getFeeds();
        }
        return $this->_rssFeeds;
    }

    /**
     * Publish a RSS-feed.
     *
     * Adds the action identified by $action to the list of rss-feeds to be offered to the user.
     * You should define a function with the name of $action in your plugin,
     * that must produce the RSS content.
     *
     * Note: This should be reserved for unit tests only.
     *
     * @param   string  $action  action
     * @return  $this
     * @ignore
     */
    public function addRssFeed(string $action)
    {
        $this->_rssFeeds[] = $action;
        return $this;
    }

    /**
     * <<smarty function>> Create HTML RSS link.
     *
     * @param   array                      $params  any list of arguments
     * @param   \Smarty_Internal_Template  $smarty  reference to currently rendered template
     * @return  scalar
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        if (isset($params['image'])) {
            $image = (string) $params['image'];
        } else {
            $image = $this->_getRegistry()->getVar('DATADIR') .'rss.gif';
        }
        $title = $this->_getLanguage()->getVar('RSS_TITLE');
        $name = $this->_getLanguage()->getVar('PROGRAM_TITLE');
        $result = "";
        $formatter = $this->_getDependencyContainer()->getUrlFormatter();
        foreach ($this->getRssFeeds() as $action)
        {
            $result .= '<a title="' . $name . ': ' . $title . '" href="' . $formatter("action={$action}", false, false) . '">' .
            '<img alt="RSS" src="' . $image . '"/></a>';
        }
        return $result;
    }

}

?>