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
 * <<abstract>> Implements standard password behavior.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
abstract class AbstractBehavior extends \Yana\Core\Object implements \Yana\Security\Passwords\Behaviors\IsBehavior
{

    /**
     * @var  \Yana\Security\Data\IsUser
     */
    private $_user = null;

    /**
     * @var  \Yana\Security\Passwords\IsAlgorithm
     */
    private $_algorithm = null;

    /**
     * @var  \Yana\Security\Passwords\Generators\IsAlgorithm
     */
    private $_generator = null;

    /**
     * Initialize dependencies.
     *
     * @param  \Yana\Security\Passwords\IsAlgorithm             $algorithm  to encode and compare passwords
     * @param  \Yana\Security\Passwords\Generators\IsAlgorithm  $generator  to generade new random passwords
     */
    public function __construct(\Yana\Security\Passwords\IsAlgorithm $algorithm, \Yana\Security\Passwords\Generators\IsAlgorithm $generator)
    {
        $this->_algorithm = $algorithm;
        $this->_generator = $generator;
    }

    /**
     * Get wrapped user.
     *
     * @return  \Yana\Security\Data\IsUser
     */
    public function getUser()
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
        return $this->_algorithm;
    }

    /**
     * Returns algorithm to generate random password.
     *
     * @return  \Yana\Security\Passwords\Generators\IsAlgorithm
     */
    protected function _getGenerator()
    {
        return $this->_generator;
    }

    /**
     * Replaces currently wrapped user.
     *
     * @param   \Yana\Security\Data\IsUser  $user  entity to wrap
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    public function setUser(\Yana\Security\Data\IsUser $user)
    {
        $this->_user = $user;
        return $this;
    }

    /**
     * Replaces currently used password hash algorithm.
     *
     * @param   \Yana\Security\Passwords\IsAlgorithm  $algorithm  new algorithm to use
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    protected function _setAlgorithm(\Yana\Security\Passwords\IsAlgorithm $algorithm)
    {
        $this->_algorithm = $algorithm;
        return $this;
    }

    /**
     * Replaces currently used password generating algorithm.
     *
     * @param   \Yana\Security\Passwords\Generators\IsAlgorithm  $generator  new algorithm to use
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    protected function _setGenerator(\Yana\Security\Passwords\Generators\IsAlgorithm $generator)
    {
        $this->_generator = $generator;
        return $this;
    }

}

?>