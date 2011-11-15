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

namespace Yana\Views\Helpers\OutputFilters;

/**
 * Smarty-compatible HTML-processors
 *
 * This class is registered when instantiating the Smarty Engine in the {@see SmartTemplate} class.
 *
 * @package     yana
 * @subpackage  views
 */
class RssFilter extends \Yana\Core\Object implements \Yana\Views\Helpers\IsOutputFilter
{

    /**
     * @var string
     */
    private $_feedTitle = "";

    /**
     * @return string
     */
    protected function _getFeedTitle()
    {
        return $this->_feedTitle;
    }

    /**
     * Create a new instance.
     *
     * @param  string  $feedTitle  Some text (without HTML-Tags) that serves the title of the link to the RSS feed
     */
    public function __construct($feedTitle = "")
    {
        assert('is_string($feedTitle); // Invalid argument $feedTitle: string expected');
        if (empty($feedTitle)) {
            $feedTitle = \Language::getInstance()->getVar("PROGRAM_TITLE");
        }
        $this->_feedTitle = $feedTitle;
    }

    /**
     * <<smarty outputfilter>> outputfilter
     *
     * Imports all currently used RSS-Feeds and adds a link as Meta-data to the HTML header.
     *
     * @param   string  $source  HTML code with PHP tags
     * @return  string
     */
    public function __invoke($source)
    {
        assert('is_string($source); // Wrong type for argument 1. String expected');

        if (mb_strpos($source, '</head>') > -1) {

            $htmlHead = "";

            $urlFormatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();
            $title = $this->_getFeedTitle();
            foreach (\Yana\RSS\Publisher::getFeeds() as $action)
            {
                $htmlHead .= '        <link rel="alternate" type="application/rss+xml"' .
                ' title="' . $title . '" href="' . $urlFormatter("action=$action") . "\"/>\n";
            }
            unset($action);

            $source = preg_replace('/^\s*<\/head>/m', $htmlHead . "\$0", $source, 1);
        }

        return $source;
    }

}

?>