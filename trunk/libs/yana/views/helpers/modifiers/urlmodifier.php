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

namespace Yana\Views\Helpers\Modifiers;

/**
 * Smarty-compatible modifier.
 *
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  views
 */
class UrlModifier extends \Yana\Views\Helpers\Formatters\UrlFormatter implements \Yana\Views\Helpers\IsModifier
{

    /**
     * @var \Yana\Views\Helpers\Formatters\UrlFormatter
     */
    private $_formatter = null;

    /**
     * Lazy loading for formatter class.
     *
     * @return \Yana\Views\Helpers\Formatters\UrlFormatter 
     */
    protected function _getFormatter()
    {
        if (!isset($this->_formatter)) {
            $this->_formatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();
        }
        return $this->_formatter;
    }

    /**
     * <<smarty modifier>> URL.
     *
     * Creates an URL to the script itself from a search-string fragment.
     *
     * @param   string   $string           url parameter list
     * @param   bool     $asString         decide wether entities in string should be encoded or not
     * @param   bool     $asAbsolutePath   decide wether function should return relative or absolut path
     * @return  string
     */
    public function __invoke($string, $asString = false, $asAbsolutePath = true)
    {
        $formatter = $this->_getFormatter();
        return $formatter($string, $asString, $asAbsolutePath);
    }

}

?>