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

namespace Yana\Security\Data\Behaviors;

/**
 * <<builder>> Helps building the behavior facade.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Builder extends \Yana\Security\Data\UserBuilder implements \Yana\Security\Data\Behaviors\IsBuilder
{

    /**
     * @var  \Yana\Security\Dependencies\IsContainer
     */
    private $_dependencyContainer;

    /**
     * Inject dependency container.
     *
     * @param   \Yana\Security\Dependencies\IsContainer  $container  instance to be injected
     * @return  self
     */
    public function setDependencyContainer(\Yana\Security\Dependencies\IsContainer $container)
    {
        $this->_dependencyContainer = $container;
        return $this;
    }

    /**
     * Get injected dependency container.
     *
     * Creates a container with default settings if none is given.
     *
     * @return  \Yana\Security\Dependencies\IsContainer
     */
    public function getDependencyContainer()
    {
        if (!isset($this->_dependencyContainer)) {
            $this->_dependencyContainer = new \Yana\Security\Dependencies\Container();
        }
        return $this->_dependencyContainer;
    }

    /**
     * Build new user behavior facade.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user  entity
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  if no such user is found in the database
     */
    public function __invoke(\Yana\Security\Data\Users\IsEntity $user)
    {
        return new \Yana\Security\Data\Behaviors\Standard($this->getDependencyContainer(), $user);
    }

    /**
     * Build an user object from the current user name saved in the session data.
     *
     * Returns a \Yana\Security\Data\GuestUser if the session contains no username.
     * Returns an \Yana\Security\Data\User otherwise.
     *
     * @param   \Yana\Security\Sessions\IsWrapper  $session  with the user name at index 'user_name'
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\NotFoundException  if no such user is found in the database
     */
    public function buildFromSession(?\Yana\Security\Sessions\IsWrapper $session = null)
    {
        $entity = parent::buildFromSession($session);
        return $this($entity);
    }

    /**
     * Build an user object based on a given name.
     *
     * @param   string  $userId  the name/id of the user as it is stored in the database
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  if no such user is found in the database
     */
    public function buildFromUserName($userId)
    {
        $entity = parent::buildFromUserName($userId);
        return $this($entity);
    }

    /**
     * Build an user object based on a given name.
     *
     * @param   string  $userId  the name/id of the user as it is stored in the database
     * @param   string  $mail    the user's e-mail address (must be unique)
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     */
    public function buildNewUser($userId, $mail)
    {
        $entity = parent::buildNewUser($userId, $mail);
        return $this($entity);
    }

    /**
     * Build an user object based on a given mail address.
     *
     * @param   string  $mail  an unique mail address
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\MailNotFoundException  if no such user is found in the database
     */
    public function buildFromUserMail($mail)
    {
        assert(is_string($mail), 'Invalid argument $mail: string expected');

        $entity = parent::buildFromUserMail($mail); // may throw exception
        return $this($entity);
    }

    /**
     * Build an user object based on a given recovery id.
     *
     * @param   string  $recoveryId  unique identifier provided by user input
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no such user exists
     */
    public function buildFromRecoveryId($recoveryId)
    {
        assert(is_string($recoveryId), 'Invalid argument $recoveryId: string expected');

        $entity = parent::buildFromRecoveryId($recoveryId); // may throw exception
        return $this($entity);
    }

}

?>
