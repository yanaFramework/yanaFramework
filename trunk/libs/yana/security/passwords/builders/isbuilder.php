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

namespace Yana\Security\Passwords\Builders;

/**
 * <<interface>> Helps creating password-algorithm instances.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsBuilder
{

    /**
     * Make this algorithm the new main and any previous the fallback.
     *
     * @param   string  $algorithm  hash-algorithm instance
     * @return  \Yana\Security\Passwords\Builders\IsBuilder
     */
    public function add($algorithm);

    /**
     * Return algorithm.
     *
     * Default is NullAlgorithm.
     *
     * @return  \Yana\Security\Passwords\IsAlgorithm
     */
    public function __invoke();

}

?>