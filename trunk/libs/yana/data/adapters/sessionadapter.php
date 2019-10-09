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

namespace Yana\Data\Adapters;

/**
 * <<adapter>> data adapter
 *
 * Session adapter, that stores and restores the given object from the session settings.
 *
 * @package     yana
 * @subpackage  data
 */
class SessionAdapter extends \Yana\Data\Adapters\ArrayAdapter implements \Yana\Data\Adapters\IsDataAdapter
{

    /**
     * Session index
     *
     * Used to identify where in the session to store the retrieved data.
     * Will be used as follows: $_SESSION[$index].
     *
     * @var  string
     */
    private $_index = __CLASS__;

    /**
     * constructor
     *
     * @param  string  $index  where to store session data $_SESSION[$index]
     */
    public function __construct($index = __CLASS__)
    {
        assert(is_string($index), 'Wrong argument type argument 1. String expected');
        $this->_index = "$index";
        if (!isset($_SESSION[$this->_index]) || !is_array($_SESSION[$this->_index])) {
            $_SESSION[$this->_index] = array();
        }
    }

    /**
     * Returns the index of the session array.
     *
     * Used to identify where in the session to store the retrieved data.
     * Will be used as follows: $_SESSION[$index].
     *
     * @return string
     */
    protected function _getIndex()
    {
        return $this->_index;
    }

    /**
     * Get item list.
     *
     * @return  array
     */
    protected function _getItems()
    {
        $session = isset($_SESSION) ? $_SESSION : array();
        return isset($session[$this->_getIndex()]) ? (array) $session[$this->_getIndex()] : array();
    }

    /**
     * Set item list.
     *
     * @param  array  $items  items to place in array
     */
    protected function _setItems(array $items)
    {
        $_SESSION[$this->_getIndex()] = $items;
    }

}

?>