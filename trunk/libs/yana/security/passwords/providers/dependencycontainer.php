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

namespace Yana\Security\Passwords\Providers;

/**
 * Defines dependencies required to build authentication providers.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class DependencyContainer extends \Yana\Core\StdObject implements \Yana\Security\Passwords\Providers\IsDependencyContainer
{

    /**
     * @var  \Yana\Security\Passwords\IsAlgorithm
     */
    private $_passwordAlgorithm = null;

    /**
     * @var  \Yana\Security\Passwords\Providers\IsEntity
     */
    private $_authenticationSettings = null;

    /**
     * <<constructor>> Set up and initialize dependencies.
     *
     * @param  \Yana\Security\Passwords\Providers\IsEntity  $entity     authentication provider settings
     * @param  \Yana\Security\Passwords\IsAlgorithm         $algorithm  inject a NULL-algorithm for Unit-tests,
     *                                                                  otherwise the most obvious choice is the "BasicAlgorithm" class
     */
    public function __construct(\Yana\Security\Passwords\Providers\IsEntity $entity, \Yana\Security\Passwords\IsAlgorithm $algorithm)
    {
        $this->_authenticationSettings = $entity;
        $this->_passwordAlgorithm = $algorithm;
    }

    /**
     * Get configuration of authentication provider.
     *
     * @return  \Yana\Security\Passwords\Providers\IsEntity
     */
    public function getAuthenticationSettings(): \Yana\Security\Passwords\Providers\IsEntity
    {
        return $this->_authenticationSettings;
    }

    /**
     * Returns password creation algorithm.
     *
     * @return  \Yana\Security\Passwords\IsAlgorithm
     */
    public function getPasswordAlgorithm(): \Yana\Security\Passwords\IsAlgorithm
    {
        return $this->_passwordAlgorithm;
    }

}

?>