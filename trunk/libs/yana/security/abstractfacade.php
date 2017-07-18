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

namespace Yana\Security;

/**
 * <<abstract>> Facade base-class, providing protected access functions.
 *
 * This serves no other purpose but to increase the readability of the derived class.
 *
 * @package     yana
 * @subpackage  security
 * @ignore
 */
abstract class AbstractFacade extends \Yana\Core\Object implements \Yana\Security\IsFacade
{

    /**
     * @var  \Yana\Security\Dependencies\IsFacadeContainer
     */
    private $_container = null;

    /**
     * Initialize dependencies.
     *
     * @param   \Yana\Security\Dependencies\IsFacadeContainer  $container  dependency container
     */
    public function __construct(\Yana\Security\Dependencies\IsFacadeContainer $container = null)
    {
        $this->_container = $container;
    }

    /**
     * Creates dependency container on demand and returns it.
     *
     * @return  \Yana\Security\Dependencies\IsFacadeContainer
     */
    protected function _getContainer()
    {
        if (!isset($this->_container)) {
            $this->_container = new \Yana\Security\Dependencies\Container();
        }
        return $this->_container;
    }

    /**
     * @return \Yana\Security\Rules\Requirements\DataReader
     */
    protected function _createDataReader()
    {
        $container = $this->_getContainer();
        return new \Yana\Security\Rules\Requirements\DefaultableDataReader($container->getDataConnection(), $container->getDefaultEventUser());
    }

    /**
     * @return \Yana\Security\Rules\Requirements\DataWriter
     */
    protected function _createDataWriter()
    {
        return new \Yana\Security\Rules\Requirements\DataWriter($this->_getContainer()->getDataConnection());
    }

    /**
     * @return \Yana\Security\Data\Users\Adapter
     */
    protected function _createUserAdapter()
    {
        return new \Yana\Security\Data\Users\Adapter($this->_getContainer()->getDataConnection());
    }

    /**
     * @return \Yana\Security\Data\Behaviors\Builder
     */
    protected function _createUserBuilder()
    {
        $builder = new \Yana\Security\Data\Behaviors\Builder($this->_createUserAdapter());
        $builder->setDependencyContainer($this->_getContainer());
        return $builder;
    }

    /**
     * @param   string  $userName  identifies user
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  if no such user is found in the database
     */
    protected function _buildUserEntity($userName = "")
    {
        assert('is_string($userName); // Invalid argument $userName: string expected');
        $builder = $this->_createUserBuilder();
        if ($userName > "") {
            $user = $builder->buildFromUserName($userName);
        } else {
            $user = $builder->buildFromSession($this->_getContainer()->getSession());
        }
        return $user;
    }

}

?>