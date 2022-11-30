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
declare(strict_types=1);

namespace Yana\Data\Adapters;

/**
 * <<trait>> has cache.
 *
 * Implements getter and setter functions necessary for a class that has a cache adapter.
 *
 * @package     yana
 * @subpackage  data
 */
trait HasCache
{

    /**
     * @var  \Yana\Data\Adapters\IsDataAdapter
     */
    private $_cache = null;

    /**
     * Replace the cache adapter.
     *
     * Overwrite only for unit-tests, or if you are absolutely sure you need to and know what you are doing.
     * Replacing this by the wrong adapter might introduce a security risk,
     * unless you are in a very specific usage scenario.
     *
     * Note that this may also replace the cache contents.
     *
     * @param   \Yana\Data\Adapters\IsDataAdapter  $cache  new cache adapter
     * @return  $this
     */
    public function setCache(\Yana\Data\Adapters\IsDataAdapter $cache)
    {
        $this->_cache = $cache;
        return $this;
    }

    /**
     * Returns the currently selected cache adapter.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    protected function _getCache()
    {
        if (!isset($this->_cache)) {
            $this->_cache = new \Yana\Data\Adapters\ArrayAdapter();
        }
        return $this->_cache;
    }

}

?>
