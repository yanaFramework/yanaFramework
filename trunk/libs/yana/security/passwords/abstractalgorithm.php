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

namespace Yana\Security\Passwords;

/**
 * Password hashing algorithm.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
abstract class AbstractAlgorithm extends \Yana\Core\Object implements \Yana\Security\Passwords\IsAlgorithm
{

    /**
     * @var  \Yana\Security\Passwords\IsAlgorithm 
     */
    private $_fallback = null;

    /**
     * <<constructor>> Set a fallback algorithm.
     *
     * Leave blank for none. Will throw exception if a fallback is requested but not set.
     *
     * Carefull! Doesn't check for circular dependencies.
     *
     * @param  \Yana\Security\Passwords\IsAlgorithm  $fallback  another algorithm
     */
    public function __construct(\Yana\Security\Passwords\IsAlgorithm $fallback = null)
    {
        $this->_fallback = $fallback;
    }

    /**
     * Get fallback algorithm.
     *
     * @return  \Yana\Security\Passwords\IsAlgorithm
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when no algorithm is set
     */
    protected function _getFallback()
    {
        if (!isset($this->_fallback)) {
            throw new \Yana\Core\Exceptions\NotImplementedException("No suitable hash algorithm found");
        }
        return $this->_fallback;
    }

}

?>