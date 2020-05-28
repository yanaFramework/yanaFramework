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
declare(strict_types=1);

namespace Yana\Security\Passwords\Behaviors;

/**
 * <<abstract>> Implements standard password behavior.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
abstract class AbstractBehavior extends \Yana\Core\StdObject implements \Yana\Security\Passwords\Behaviors\IsBehavior
{

    /**
     * @var  \Yana\Security\Data\Users\IsEntity
     */
    private $_user = null;

    /**
     * @var  \Yana\Security\Dependencies\IsPasswordContainer
     */
    private $_container = null;

    /**
     * Initialize dependencies.
     *
     * @param  \Yana\Security\Passwords\IsAlgorithm                         $algorithm  to encode and compare passwords
     * @param  \Yana\Security\Passwords\Generators\IsAlgorithm              $generator  to generade new random passwords
     * @param  \Yana\Security\Passwords\Providers\IsAuthenticationProvider  $provider   used to check and change passwords
     */
    public function __construct(\Yana\Security\Dependencies\IsPasswordContainer $container)
    {
        $this->_container = $container;
    }

    /**
     * Returns an authentication provider.
     *
     * The authentication provider is used to check and/or change passwords.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user  entity
     * @return  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     */
    protected function _getAuthenticationProvider(\Yana\Security\Data\Users\IsEntity $user): \Yana\Security\Passwords\Providers\IsAuthenticationProvider
    {
        return $this->_container->getAuthenticationProvider($user);
    }

    /**
     * Get wrapped user.
     *
     * @return  \Yana\Security\Data\Users\IsEntity
     */
    public function getUser(): \Yana\Security\Data\Users\IsEntity
    {
        if (!isset($this->_user)) {
            $this->_user = new \Yana\Security\Data\Users\Guest();
        }
        return $this->_user;
    }

    /**
     * Returns password calculation algorithm.
     *
     * @return  \Yana\Security\Passwords\IsAlgorithm
     */
    protected function _getAlgorithm()
    {
        return $this->_container->getPasswordAlgorithm();
    }

    /**
     * Returns algorithm to generate random password.
     *
     * @return  \Yana\Security\Passwords\Generators\IsAlgorithm
     */
    protected function _getGenerator()
    {
        return $this->_container->getPasswordGenerator();
    }

    /**
     * Replaces currently wrapped user.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user  entity to wrap
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    public function setUser(\Yana\Security\Data\Users\IsEntity $user)
    {
        $this->_user = $user;
        return $this;
    }

}

?>