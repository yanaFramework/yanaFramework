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
 * <<interface>> Defines dependencies required by behavior-builder.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
interface IsContainer
{

    /**
     * Retrieve session wrapper.
     *
     * @return  \Yana\Security\Sessions\IsWrapper
     */
    public function getSession();

    /**
     * Retrieve password behavior dependency.
     *
     * @return  \Yana\Security\Passwords\Behaviors\IsBehavior
     */
    public function getPasswordBehavior();

    /**
     * Retrieve login behavior dependency.
     *
     * @return  \Yana\Security\Logins\IsBehavior
     */
    public function getLoginBehavior();

    /**
     * Retrieve levels data adapter.
     *
     * @return  \Yana\Security\Data\SecurityLevels\Adapter
     */
    public function getLevelsAdapter();

    /**
     * Retrieve rules data adapter.
     *
     * @return  \Yana\Security\Data\SecurityRules\Adapter
     */
    public function getRulesAdapter();

}

?>