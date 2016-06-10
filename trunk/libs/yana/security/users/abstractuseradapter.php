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

namespace Yana\Security\Users;

/**
 * <<abstract>> User manager.
 *
 * This persistent class provides access to user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
abstract class AbstractUserAdapter extends \Yana\Core\Object implements \Yana\Security\Users\IsDataAdapter
{

    /**
     * Basic ORM helper object.
     *
     * @var  \Yana\Data\Adapters\IsEntityMapper
     */
    private $_entityMapper = null;

    /**
     * Connection to database.
     *
     * @var  \Yana\Db\IsConnection
     */
    private $_connection = null;

    /**
     * <<construct>> Creates a new user-manager.
     *
     * To create the required connection you may use the following short-hand function:
     * <code>
     * $connection = \Yana\Application::connect("user");
     * </code>
     *
     * If no mapper is given, this function creates and uses an instance of \Yana\Security\Users\UserMapper.
     *
     * @param  \Yana\Db\IsConnection               $connection  database connection to table user
     * @param  \Yana\Data\Adapters\IsEntityMapper  $mapper      simple OR-mapper to convert database entries to objects
     */
    public function __construct(\Yana\Db\IsConnection $connection, \Yana\Data\Adapters\IsEntityMapper $mapper = null)
    {
        if (\is_null($mapper)) {
            $mapper = new \Yana\Security\Users\UserMapper();
        }
        $this->_connection = $connection;
        $this->_entityMapper = $mapper;
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

    /**
     * Returns an instance of an OR-mappinging class.
     *
     * Use this to map database entries to objects and vice-versa.
     *
     * @return  \Yana\Data\Adapters\IsEntityMapper
     */
    protected function _getEntityMapper()
    {
        return $this->_entityMapper;
    }

}

?>