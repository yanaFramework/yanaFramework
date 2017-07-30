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

namespace Yana\Security\Data\SecurityLevels;

/**
 * <<interface>> Security level.
 *
 * @package     yana
 * @subpackage  security
 */
interface IsLevelEntity extends \Yana\Security\Data\SecurityLevels\IsLevel, \Yana\Data\Adapters\IsEntity
{

    /**
     * Set associated application profile.
     *
     * @param   string  $profileName  application profile id
     * @return  self
     */
    public function setProfile($profileName);

    /**
     * Get associated application profile.
     *
     * @return  string
     */
    public function getProfile();

    /**
     * Get the id of the user this rule applies to.
     *
     * @return  string
     */
    public function getUserName();

    /**
     * Get the id of the user who created this rule.
     *
     * @return  string
     */
    public function getGrantedByUser();

    /**
     * Set the id of the user this rule applies to.
     *
     * @param   string  $userName  id referencing user table
     * @return  self
     */
    public function setUserName($userName);

    /**
     * Set the id of the user who created this rule.
     *
     * @param   string  $createdByUser  id referencing user table
     * @return  self
     */
    public function setGrantedByUser($createdByUser);
}

?>