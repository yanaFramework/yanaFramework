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

namespace Yana\Security\Data\Users;

/**
 * User data-adapter.
 *
 * This persistent class provides access to user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class ArrayAdapter extends \Yana\Data\Adapters\ArrayAdapter implements \Yana\Security\Data\Users\IsDataAdapter
{

    /**
     * Loads and returns an user account from the database.
     *
     * @param   string  $mail  unique mail address
     * @return  \Yana\Security\Data\Users\IsEntity
     * @throws  \Yana\Core\Exceptions\User\MailNotFoundException  when no such user exists
     */
    public function findUserByMail($mail)
    {
        foreach ($this->_getItems() as $item)
        {
            /* @var $item \Yana\Security\Data\Users\IsEntity */
            if (\strcasecmp($item->getMail(), $mail) === 0) {
                return $item;
            }
        }

        $message = "No user found with mail: " . \htmlentities($mail);
        $level = \Yana\Log\TypeEnumeration::ERROR;
        throw new \Yana\Core\Exceptions\User\MailNotFoundException($message, $level);
    }

    /**
     * Loads and returns an user account from the database.
     *
     * @param   string  $recoveryId  unique identifier
     * @return  \Yana\Security\Data\Behaviors\IsBehavior
     * @throws  \Yana\Core\Exceptions\User\NotFoundException  when no such user exists
     */
    public function findUserByRecoveryId($recoveryId)
    {
        foreach ($this->_getItems() as $item)
        {
            /* @var $item \Yana\Security\Data\Users\IsEntity */
            if ($item->getPasswordRecoveryId() === $recoveryId) {
                return $item;
            }
        }

        $message = "No user found with recovery id: " . \htmlentities($recoveryId);
        $level = \Yana\Log\TypeEnumeration::ERROR;
        throw new \Yana\Core\Exceptions\User\NotFoundException($message, $level);
    }

    /**
     * Removes the given entity from the database.
     *
     * @param  \Yana\Data\Adapters\IsEntity  $entity  compose the where clause based on this object
     */
    public function delete(\Yana\Data\Adapters\IsEntity $entity)
    {
        foreach ($this->_getItems() as $offset => $item)
        {
            if ($item === $entity) {
                $this->offsetUnset($offset);
            }
        }
    }

}

?>