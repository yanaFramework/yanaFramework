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

namespace Yana\Security\Dependencies;

/**
 * <<abstract>> Helps building the behavior facade.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class PasswordContainer extends \Yana\Core\Object implements \Yana\Security\Users\Behaviors\IsBuilderDependencyContainer
{

    /**
     * @var  \Yana\Security\Passwords\Builders\Builder
     */
    private $_passwordAlgorithmBuilder = null;

    /**
     * @var  \Yana\Security\Passwords\IsAlgorithm
     */
    private $_passwordAlgorithm = null;

    /**
     * @var  \Yana\Security\Passwords\Generators\IsAlgorithm
     */
    private $_passwordGenerator = null;

    /**
     * @var  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    private $_passwordBehavior = null;

    /**
     * @return \Yana\Security\Rules\Requirements\DataReader
     */
    public function getRequirementsDataReader()
    {
        if (!isset($this->_requirementsDataReader)) {
            $this->_requirementsDataReader = new \Yana\Security\Rules\Requirements\DefaultableDataReader(
                $this->getDataConnection(), $this->getDefaultEventUser());
        }
        return $this->_requirementsDataReader;
    }

    /**
     * 
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    public function getSession()
    {
        if (!isset($this->_session)) {
            $this->_session = new \Yana\Security\Sessions\Wrapper();
        }
        return $this->_session;
    }

    /**
     * 
     * @param   \Yana\Security\Sessions\IsWrapper  $session  dependency
     * @return  \Yana\Security\Dependencies\Container
     */
    public function setSession(\Yana\Security\Sessions\IsWrapper $session)
    {
        $this->_session = $session;
        return $this;
    }

    /**
     * 
     *
     * @param   \Yana\Security\Logins\IsBehavior  $loginBehavior  dependency
     * @return  \Yana\Security\Dependencies\Container
     */
    public function setLoginBehavior(\Yana\Security\Logins\IsBehavior $loginBehavior)
    {
        $this->_loginBehavior = $loginBehavior;
        return $this;
    }

    /**
     * 
     * @param   \Yana\Security\Passwords\Builders\Builder  $passwordAlgorithmBuilder  dependency
     * @return  \Yana\Security\Dependencies\Container
     */
    public function setPasswordAlgorithmBuilder(\Yana\Security\Passwords\Builders\Builder $passwordAlgorithmBuilder)
    {
        $this->_passwordAlgorithmBuilder = $passwordAlgorithmBuilder;
        return $this;
    }

    /**
     * 
     * @param   \Yana\Security\Passwords\IsAlgorithm  $passwordAlgorithm  dependency
     * @return  \Yana\Security\Dependencies\Container
     */
    public function setPasswordAlgorithm(\Yana\Security\Passwords\IsAlgorithm $passwordAlgorithm)
    {
        $this->_passwordAlgorithm = $passwordAlgorithm;
        return $this;
    }

    /**
     * 
     * @param   \Yana\Security\Passwords\Generators\IsAlgorithm  $passwordGenerator  dependency
     * @return  \Yana\Security\Dependencies\Container
     */
    public function setPasswordGenerator(\Yana\Security\Passwords\Generators\IsAlgorithm $passwordGenerator)
    {
        $this->_passwordGenerator = $passwordGenerator;
        return $this;
    }

    /**
     * 
     * @param   \Yana\Security\Passwords\Behaviors\IsBehavior  $passwordBehavior  dependency
     * @return  \Yana\Security\Dependencies\Container
     */
    public function setPasswordBehavior(\Yana\Security\Passwords\Behaviors\IsBehavior $passwordBehavior)
    {
        $this->_passwordBehavior = $passwordBehavior;
        return $this;
    }

    /**
     * @return  \Yana\Security\Passwords\Builders\Builder
     */
    public function getPasswordAlgorithmBuilder()
    {
        if (!isset($this->_passwordAlgorithmBuilder)) {
            $this->_passwordAlgorithmBuilder = new \Yana\Security\Passwords\Builders\Builder();
        }
        return $this->_passwordAlgorithmBuilder;
    }

    /**
     * Retrieve password behavior dependency.
     *
     * @return  \Yana\Security\Logins\IsBehavior
     */
    public function getLoginBehavior()
    {
        if (!isset($this->_loginBehavior)) {
            $this->_loginBehavior = new \Yana\Security\Logins\StandardBehavior($this->getSession());
        }
        return $this->_loginBehavior;
    }

    /**
     * Retrieve password algorithm dependency.
     *
     * @return  \Yana\Security\Passwords\IsAlgorithm
     */
    public function getPasswordAlgorithm()
    {
        if (!isset($this->_passwordAlgorithm)) {
            $this->_passwordAlgorithm =
                $this->getPasswordAlgorithmBuilder()
                    ->add(\Yana\Security\Passwords\Builders\Enumeration::BASIC)
                    ->add(\Yana\Security\Passwords\Builders\Enumeration::BLOWFISH)
                    ->add(\Yana\Security\Passwords\Builders\Enumeration::SHA256)
                    ->add(\Yana\Security\Passwords\Builders\Enumeration::SHA512)
                    ->add(\Yana\Security\Passwords\Builders\Enumeration::BCRYPT)
                    ->__invoke();
        }
        return $this->_passwordAlgorithm;
    }

    /**
     * Retrieve password generator dependency.
     *
     * @return  \Yana\Security\Passwords\Generators\IsAlgorithm
     */
    public function getPasswordGenerator()
    {
        if (!isset($this->_passwordGenerator)) {
            $this->_passwordGenerator = new \Yana\Security\Passwords\Generators\StandardAlgorithm();
        }
        return $this->_passwordGenerator;
    }

    /**
     * Retrieve password behavior dependency.
     *
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    public function getPasswordBehavior()
    {
        if (!isset($this->_passwordBehavior)) {
            $this->_passwordBehavior = new \Yana\Security\Passwords\Behaviors\StandardBehavior(
                $this->getPasswordAlgorithm(), $this->getPasswordGenerator()
            );
        }
        return $this->_passwordBehavior;
    }

}

?>