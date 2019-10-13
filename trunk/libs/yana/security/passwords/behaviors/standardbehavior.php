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
 * <<facade>> Implements standard password behavior.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class StandardBehavior extends \Yana\Security\Passwords\Behaviors\AbstractBehavior
{

    /**
     * Reset failure count.
     *
     * Resets the number of times the user entered an invalid password back to 0.
     * Use this, when the maximum failure time has expired.
     *
     * @param  \Yana\Security\Data\Users\IsEntity  $user  the user to operate on
     */
    protected function _resetFailureCount(\Yana\Security\Data\Users\IsEntity $user)
    {
        $user
            ->setFailureCount(0)
            ->setFailureTime(0);
    }

    /**
     * Add failed login.
     *
     * Call this if the user failed to authenticate correctly.
     *
     * @param  \Yana\Security\Data\Users\IsEntity  $user  the user to operate on
     */
    protected function _increaseFailureCount(\Yana\Security\Data\Users\IsEntity $user)
    {
        $user
            ->setFailureCount($this->getUser()->getFailureCount() + 1)
            ->setFailureTime(time());
    }

    /**
     * Check if password is 'uninitialized'.
     *
     * @param   \Yana\Security\Data\Users\IsEntity  $user  entity
     * @return  bool
     */
    protected function _isUninitializedPassword(\Yana\Security\Data\Users\IsEntity $user)
    {
        return \strcasecmp((string) $user->getPassword(), 'UNINITIALIZED') === 0;
    }

    /**
     * Compare recovery id with recovery id of current user.
     *
     * Returns bool(true) if the id is correct an bool(false) otherwise.
     *
     * @param   string  $recoveryId  user password recovery id
     * @return  bool
     */
    public function checkRecoveryId($recoveryId)
    {
        assert(is_string($recoveryId), 'Wrong type for argument $userPwd. String expected');

        assert(!isset($user), 'Cannot redeclare variable $user');
        $user = $this->getUser();
        assert(!isset($isCorrect), 'Cannot redeclare variable $isCorrect');
        $isCorrect = false;
        if ($recoveryId > "") {
            $isCorrect = $this->_getAlgorithm()->isEqual($recoveryId, (string) $user->getPasswordRecoveryId());
        }

        if ($isCorrect) {
            // reset failure count
            $this->_resetFailureCount($user);
        } else {
            $this->_increaseFailureCount($user);
        }
        $user->saveEntity();
        return (bool) $isCorrect;
    }

    /**
     * Compare password with password of current user.
     *
     * Returns bool(true) if the password is correct an bool(false) otherwise.
     *
     * @param   string  $userPwd  user password
     * @return  bool
     */
    public function checkPassword(string $userPwd): bool
    {
        assert(!isset($user), 'Cannot redeclare variable $user');
        $user = $this->getUser();
        assert(!isset($isCorrect), 'Cannot redeclare variable $isCorrect');
        $isCorrect = false;
        switch (true)
        {
            case $this->_isUninitializedPassword($user):
            case $userPwd > "" && $this->_getAuthenticationProvider()->checkPassword($user, $userPwd):
                $isCorrect = true;
        }

        if ($isCorrect) {
            // reset failure count
            $this->_resetFailureCount($user);
        } else {
            $this->_increaseFailureCount($user);
        }
        $user->saveEntity();
        return (bool) $isCorrect;
    }

    /**
     * Change password.
     *
     * Set login password to $password for current user.
     *
     * @param   string  $password  non-empty alpha-numeric text with optional special characters
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    public function changePassword($password)
    {
        assert(is_string($password) && strlen($password) > 0, 'Wrong type for argument 1. String expected');

        $user = $this->getUser();
        $user
                // reset password recovery id if there is any
                ->setPasswordRecoveryId("")
                ->setPasswordRecoveryTime(0);
        $this->_getAuthenticationProvider()->changePassword($user, $password);

        return $this;
    }

    /**
     * Reset to new random password and return it.
     *
     * A new random 10 characters long password is generated, applied to the user and then returned.
     *
     * @return  string
     */
    public function generateRandomPassword()
    {
        // auto-generate new random password
        $password = $this->_getGenerator()->__invoke(10);
        $this->changePassword($password);

        return $password;
    }

    /**
     * Create new password recovery id.
     *
     * When the user requests a new password, a recovery id is created and the time is stored.
     * This is to ensure that the user is a allowed to reset the password and determine, when the
     * request has expired.
     *
     * Returns the new recovery id.
     *
     * @return  string
     */
    public function generatePasswordRecoveryId()
    {
        $recoveryId = $this->_getGenerator()->__invoke(10);
        $recoveryIdHash = $this->_getAlgorithm()->__invoke($recoveryId);

        $this->getUser()
            ->setPasswordRecoveryId($recoveryIdHash)
            ->setPasswordRecoveryTime(time())
            ->saveEntity();

        return $recoveryId;
    }

}

?>