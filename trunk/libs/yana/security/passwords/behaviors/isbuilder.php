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

namespace Yana\Security\Passwords\Behaviors;

/**
 * <<interface>> Helps building the behavior facade.
 *
 * @package     yana
 * @subpackage  security
 */
interface IsBuilder
{

    /**
     * Set password algorithm builder dependency.
     *
     * @param   \Yana\Security\Passwords\Builders\Builder  $passwordAlgorithmBuilder  dependency
     * @return  self
     */
    public function setPasswordAlgorithmBuilder(\Yana\Security\Passwords\Builders\Builder $passwordAlgorithmBuilder);

    /**
     * Set password algorithm dependency.
     *
     * @param   \Yana\Security\Passwords\IsAlgorithm  $passwordAlgorithm  dependency
     * @return  self
     */
    public function setPasswordAlgorithm(\Yana\Security\Passwords\IsAlgorithm $passwordAlgorithm);

    /**
     * Set password generator dependency.
     *
     * @param   \Yana\Security\Passwords\Generators\IsAlgorithm  $passwordGenerator  dependency
     * @return  self
     */
    public function setPasswordGenerator(\Yana\Security\Passwords\Generators\IsAlgorithm $passwordGenerator);

    /**
     * Retrieve password behavior dependency.
     *
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    public function __invoke();

}

?>