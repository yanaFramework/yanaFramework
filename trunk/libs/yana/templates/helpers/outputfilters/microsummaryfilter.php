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

namespace Yana\Templates\Helpers\OutputFilters;

/**
 * Smarty-compatible HTML-processors
 *
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  templates
 */
class MicrosummaryFilter extends \Yana\Core\Object implements \Yana\Templates\Helpers\IsOutputFilter
{

    /**
     * <<smarty outputfilter>> outputfilter.
     *
     * Imports all currently used microsummaries and adds a link as Meta-data to the HTML header.
     *
     * @param   string  $source  HTML code with PHP tags
     * @return  string
     */
    public function __invoke($source)
    {
        assert('is_string($source); // Invalid argument $source: string expected');

        if (mb_strpos($source, '</head>') > -1) {

            $htmlHead = "";

            assert('!isset($summary); /* cannot redeclare variable $summary */');
            foreach (\Microsummary::getSummaries() as $summary)
            {
                $htmlHead .= "        " . \SmartUtility::microsummary($summary) . "\n";
            }
            unset($summary);

            $source = preg_replace('/<head(>| [^\/>]*>)/', "\$0\n" . $htmlHead, $source, 1);
        }

        return $source;
    }

}

?>