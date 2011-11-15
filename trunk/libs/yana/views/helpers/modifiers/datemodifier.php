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
class DateModifier extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsModifier
{

    /**
     * @var \Yana\Views\Helpers\Formatters\DateFormatter
     */
    private $_formatter = null;

    /**
     * Lazy loading for formatter class.
     *
     * @return \Yana\Views\Helpers\Formatters\DateFormatter 
     */
    protected function _getFormatter()
    {
        if (!isset($this->_formatter)) {
            $this->_formatter = new \Yana\Views\Helpers\Formatters\DateFormatter();
        }
        return $this->_formatter;
    }

    /**
     * <<smarty modifier>> Date.
     *
     * Create HTML from a unix timestamp.
     *
     * @param   numeric  $time  valid timestamp, falls back to the current timestamp, if empty
     * @return  string
     */
    public function __invoke($time)
    {
        $time = (empty($time)) ? time() : (int) $time;
        $formatter = $this->_getFormatter();
        return $formatter($time);
    }

}

?>