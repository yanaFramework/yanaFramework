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

namespace Yana\Core\Dependencies;

/**
 * <<trait>> Security sub-system dependencies.
 *
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 */
trait HasSecurity
{

    use \Yana\Data\Adapters\HasCache, \Yana\Core\Dependencies\HasSession, \Yana\Security\Dependencies\HasPassword;

    /**
     * Level data adapter.
     *
     * @var  \Yana\Security\Data\SecurityLevels\Adapter
     */
    private $_levelsAdapter = null;

    /**
     * Rules data adapter.
     *
     * @var  \Yana\Security\Data\SecurityRules\Adapter
     */
    private $_rulesAdapter = null;

    /**
     * @var  \Yana\Security\Rules\Requirements\IsDataReader
     */
    private $_requirementsDataReader = null;

    /**
     * @var  \Yana\Security\Rules\IsChecker
     */
    private $_rulesChecker = null;

    /**
     * Handles the login- and logout-functionality.
     *
     * @var  \Yana\Security\Logins\IsBehavior
     */
    private $_loginBehavior = null;

    /**
     * Handles the changing of passwords.
     *
     * @var  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    private $_passwordBehavior = null;

    /**
     * Get default user settings.
     *
     * @return  array
     */
    abstract public function getDefaultUser(): array;

    /**
     * Get default user settings.
     *
     * @return  array
     */
    abstract public function getDefaultUserRequirements(): array;

    /**
     * Builds and returns a rule-checker object.
     *
     * @return  \Yana\Security\Rules\IsChecker
     */
    public function getRulesChecker(): \Yana\Security\Rules\IsChecker
    {
        if (!isset($this->_rulesChecker)) {
            $rulesChecker = new \Yana\Security\Rules\CacheableChecker($this->getRequirementsDataReader());
            $rulesChecker->setCache($this->_getCache());
            $this->_rulesChecker = $rulesChecker;
        }
        return $this->_rulesChecker;
    }

    /**
     * Builds and returns a default data reader.
     *
     * The purpose of the data reader is to retrieve requirements data from the database and build corresponding entities.
     *
     * @return \Yana\Security\Rules\Requirements\IsDataReader
     */
    public function getRequirementsDataReader(): \Yana\Security\Rules\Requirements\IsDataReader
    {
        if (!isset($this->_requirementsDataReader)) {
            $this->_requirementsDataReader = new \Yana\Security\Rules\Requirements\DefaultableDataReader(
                $this->_getDataConnection(), $this->getDefaultUserRequirements());
        }
        return $this->_requirementsDataReader;
    }

    /**
     * Inject login behavior instance.
     *
     * @param   \Yana\Security\Logins\IsBehavior  $loginBehavior  dependency
     * @return  $this
     */
    public function setLoginBehavior(\Yana\Security\Logins\IsBehavior $loginBehavior)
    {
        $this->_loginBehavior = $loginBehavior;
        return $this;
    }

    /**
     * Inject password checking behavior.
     *
     * @param   \Yana\Security\Passwords\Behaviors\IsBehavior  $passwordBehavior  dependency
     * @return  $this
     */
    public function setPasswordBehavior(\Yana\Security\Passwords\Behaviors\IsBehavior $passwordBehavior)
    {
        $this->_passwordBehavior = $passwordBehavior;
        return $this;
    }

    /**
     * Retrieve password behavior dependency.
     *
     * @return  \Yana\Security\Logins\IsBehavior
     */
    public function getLoginBehavior(): \Yana\Security\Logins\IsBehavior
    {
        if (!isset($this->_loginBehavior)) {
            $this->_loginBehavior = new \Yana\Security\Logins\StandardBehavior($this->getSession());
        }
        return $this->_loginBehavior;
    }

    /**
     * Retrieve password behavior dependency.
     *
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    public function getPasswordBehavior(): \Yana\Security\Passwords\Behaviors\IsBehavior
    {
        if (!isset($this->_passwordBehavior)) {
            $this->_passwordBehavior = new \Yana\Security\Passwords\Behaviors\StandardBehavior($this);
        }
        return $this->_passwordBehavior;
    }

    /**
     * Retrieve levels data adapter.
     *
     * @return  \Yana\Security\Data\SecurityLevels\Adapter
     */
    public function getLevelsAdapter(): \Yana\Security\Data\SecurityLevels\Adapter
    {
        if (!isset($this->_levelsAdapter)) {
            $this->_levelsAdapter = new \Yana\Security\Data\SecurityLevels\Adapter($this->_getDataConnection());
        }
        return $this->_levelsAdapter;
    }

    /**
     * Retrieve rules data adapter.
     *
     * @return  \Yana\Security\Data\SecurityRules\Adapter
     */
    public function getRulesAdapter(): \Yana\Security\Data\SecurityRules\Adapter
    {
        if (!isset($this->_rulesAdapter)) {
            $this->_rulesAdapter = new \Yana\Security\Data\SecurityRules\Adapter($this->_getDataConnection());
        }
        return $this->_rulesAdapter;
    }

    /**
     * Create and return data reader.
     *
     * @return \Yana\Security\Rules\Requirements\IsDataReader
     */
    public function getDataReader(): \Yana\Security\Rules\Requirements\IsDataReader
    {
        return new \Yana\Security\Rules\Requirements\DefaultableDataReader($this->_getDataConnection(), $this->getDefaultUserRequirements());
    }

    /**
     * Create and return data writer.
     *
     * @return \Yana\Security\Rules\Requirements\IsDataWriter
     */
    public function getDataWriter(): \Yana\Security\Rules\Requirements\IsDataWriter
    {
        return new \Yana\Security\Rules\Requirements\DataWriter($this->_getDataConnection());
    }

    /**
     * Create and return user adapter.
     *
     * @return \Yana\Security\Data\Users\Adapter
     */
    public function getUserAdapter(): \Yana\Security\Data\Users\IsDataAdapter
    {
        return new \Yana\Security\Data\Users\Adapter($this->_getDataConnection(), $this->_getUserMapper());
    }

    /**
     * Create and return user database mapper.
     *
     * @return  \Yana\Data\Adapters\IsEntityMapper
     */
    protected function _getUserMapper(): \Yana\Data\Adapters\IsEntityMapper
    {
        return new \Yana\Security\Data\Users\Mapper();
    }

}

?>