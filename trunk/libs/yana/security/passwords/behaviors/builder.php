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
 * <<builder>> Helps building the behavior facade.
 *
 * @package     yana
 * @subpackage  security
 */
class Builder extends \Yana\Core\StdObject implements \Yana\Security\Passwords\Behaviors\IsBuilder
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
     * Used to change and check passwords.
     *
     * @var  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     */
    private $_authenticationProvider = null;

    /**
     * Set password algorithm builder dependency.
     *
     * @param   \Yana\Security\Passwords\Builders\Builder  $passwordAlgorithmBuilder  dependency
     * @return  $this
     */
    public function setPasswordAlgorithmBuilder(\Yana\Security\Passwords\Builders\Builder $passwordAlgorithmBuilder)
    {
        $this->_passwordAlgorithmBuilder = $passwordAlgorithmBuilder;
        return $this;
    }

    /**
     * Set password algorithm dependency.
     *
     * @param   \Yana\Security\Passwords\IsAlgorithm  $passwordAlgorithm  dependency
     * @return  $this
     */
    public function setPasswordAlgorithm(\Yana\Security\Passwords\IsAlgorithm $passwordAlgorithm)
    {
        $this->_passwordAlgorithm = $passwordAlgorithm;
        return $this;
    }

    /**
     * Set password generator dependency.
     *
     * @param   \Yana\Security\Passwords\Generators\IsAlgorithm  $passwordGenerator  dependency
     * @return  $this
     */
    public function setPasswordGenerator(\Yana\Security\Passwords\Generators\IsAlgorithm $passwordGenerator)
    {
        $this->_passwordGenerator = $passwordGenerator;
        return $this;
    }

    /**
     * Set auhtentication provider dependency.
     *
     * @param   \Yana\Security\Passwords\Providers\IsAuthenticationProvider  $provider  dependency
     * @return  $this
     */
    public function setAuthenticationProvider(\Yana\Security\Passwords\Providers\IsAuthenticationProvider $provider)
    {
        $this->_authenticationProvider = $provider;
        return $this;
    }

    /**
     * Retrieve password algorithm builder.
     *
     * @return  \Yana\Security\Passwords\Builders\IsBuilder
     */
    public function getPasswordAlgorithmBuilder(): \Yana\Security\Passwords\Builders\IsBuilder
    {
        if (!isset($this->_passwordAlgorithmBuilder)) {
            $this->_passwordAlgorithmBuilder = new \Yana\Security\Passwords\Builders\Builder();
        }
        return $this->_passwordAlgorithmBuilder;
    }

    /**
     * Retrieve password algorithm dependency.
     *
     * @return  \Yana\Security\Passwords\IsAlgorithm
     */
    public function getPasswordAlgorithm(): \Yana\Security\Passwords\IsAlgorithm
    {
        if (!isset($this->_passwordAlgorithm)) {
            $this->_passwordAlgorithm =
                $this->getPasswordAlgorithmBuilder()
                    ->add(\Yana\Security\Passwords\Builders\Enumeration::BASIC)
                    ->add(\Yana\Security\Passwords\Builders\Enumeration::BCRYPT)
                    ->add(\Yana\Security\Passwords\Builders\Enumeration::SHA256)
                    ->add(\Yana\Security\Passwords\Builders\Enumeration::SHA512)
                    ->__invoke();
        }
        return $this->_passwordAlgorithm;
    }

    /**
     * Retrieve password generator dependency.
     *
     * @return  \Yana\Security\Passwords\Generators\IsAlgorithm
     */
    public function getPasswordGenerator(): \Yana\Security\Passwords\Generators\IsAlgorithm
    {
        if (!isset($this->_passwordGenerator)) {
            $this->_passwordGenerator = new \Yana\Security\Passwords\Generators\StandardAlgorithm();
        }
        return $this->_passwordGenerator;
    }

    /**
     * Returns an authentication provider.
     *
     * The authentication provider is used to check and/or change passwords.
     * If none has been set, this function will initialize and return a standard
     * authentication provider by default.
     *
     * @return  \Yana\Security\Passwords\Providers\IsAuthenticationProvider
     */
    public function getAuthenticationProvider(): \Yana\Security\Passwords\Providers\IsAuthenticationProvider
    {
        if (!isset($this->_authenticationProvider)) {
            $this->_authenticationProvider =
                new \Yana\Security\Passwords\Providers\Standard($this->getPasswordAlgorithm());
        }
        return $this->_authenticationProvider;
    }

    /**
     * Retrieve password behavior dependency.
     *
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    public function __invoke(): \Yana\Security\Passwords\Behaviors\IsBehavior
    {
        return new \Yana\Security\Passwords\Behaviors\StandardBehavior($this->getPasswordAlgorithm(), $this->getPasswordGenerator(), $this->getAuthenticationProvider());
    }

}

?>