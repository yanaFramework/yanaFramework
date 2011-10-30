<?php
/**
 * YANA library
 *
 * Primary controller class
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

namespace Yana\Forms\Setups;

/**
 * <<manager>> Cache manager class.
 *
 * This base class is meant to 
 *
 * @access      public
 * @package     yana
 * @subpackage  cache
 */
class CacheManager extends \CacheSessionManager
{

    /**
     * <<magic>> Set cache item.
     *
     * This adds or replaces an item of the cache at the given index with whatever object $value contains.
     *
     * @access  public
     * @param   string             $name   index of cached object
     * @param   \Yana\Forms\Setup  $value  new value of cached instance
     */
    public function __set($name, $value)
    {
        assert('$value instanceof \Yana\Forms\Setup; // Invalid argument $value: instance of \Yana\Forms\Setup expected');
        parent::__set($name, $value);
    }

}

?>