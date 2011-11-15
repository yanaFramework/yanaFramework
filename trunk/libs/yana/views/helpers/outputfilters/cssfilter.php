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
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  views
 */
class CssFilter extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsOutputFilter
{

    /**
     * <<smarty outputfilter>> outputfilter.
     *
     * Imports all currently used Stylesheets and adds a link as Meta-data to the HTML header.
     *
     * @param   string  $source  HTML code with PHP tags
     * @return  string
     */
    public function __invoke($source)
    {
        assert('is_string($source); // Wrong type for argument 1. String expected');

        if (mb_strpos($source, '</head>') > -1) {

            $htmlHead = "";

            $styleList = array_reverse((array) \SmartView::getStyles(), true);
            assert('!isset($stylesheet); /* cannot redeclare variable $stylesheet */');
            foreach ($styleList as $stylesheet)
            {
                $htmlHead = "        " . $this->_css($stylesheet) . "\n" . $htmlHead;
            }
            unset($stylesheet);

            $source = preg_replace('/^\s*<\/head>/m', $htmlHead . "\$0", $source, 1);
        }

        return $source;
    }

    /**
     * Returns HTML link to CSS file.
     *
     * @param   string  $url  path to stylesheet
     * @return  string
     */
    protected function _css($url)
    {
        $css = "";
        if (preg_match("/^[\w-_\.\/]+\.css$/si", $url)) {
            $css = '<link rel="stylesheet" type="text/css" href="' . $url . '"/>';
        }
        return $css;
    }

}

?>