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

namespace Yana\Security\Data;

/**
 * <<abstract>> entity manager.
 *
 * This persistent class provides access to user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
abstract class AbstractAdapter extends \Yana\Core\Object
{

    /**
     * Connection to database.
     *
     * @var  \Yana\Db\IsConnection
     */
    private $_connection = null;

    /**
     * <<construct>> Creates a new user-manager.
     *
     * @param  \Yana\Db\IsConnection  $connection  database connection to table user
     */
    public function __construct(\Yana\Db\IsConnection $connection)
    {
        $this->_connection = $connection;
    }

    /**
     * Returns the connection to the user database.
     *
     * @return  \Yana\Db\IsConnection
     */
    protected function _getConnection()
    {
        return $this->_connection;
    }

}

?>