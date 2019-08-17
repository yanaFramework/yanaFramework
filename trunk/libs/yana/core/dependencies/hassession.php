<?php
/**
 * YANA library
 *
 * Primary controller class
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
 */
declare(strict_types=1);

namespace Yana\Core\Dependencies;

/**
 * <<trait>> Has session instance.
 *
 * @package     yana
 * @subpackage  core
 */
trait HasSession
{

    /**
     * @var  \Yana\Security\Sessions\IsWrapper
     */
    private $_session = null;

    /**
     * Retrieve session wrapper.
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
     * Inject session wrapper.
     *
     * @param   \Yana\Security\Sessions\IsWrapper  $session  dependency
     * @return  self
     */
    public function setSession(\Yana\Security\Sessions\IsWrapper $session)
    {
        $this->_session = $session;
        return $this;
    }

}

?>