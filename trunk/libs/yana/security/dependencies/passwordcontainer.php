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
 * Dependency container.
 *
 * For testing purposes.
 *
 * @package     yana
 * @subpackage  security
 * @ignore
 */
class PasswordContainer extends \Yana\Core\StdObject implements \Yana\Security\Dependencies\IsPasswordContainer
{

    use \Yana\Security\Dependencies\HasPassword;

    /**
     * @var \Yana\Db\IsConnectionFactory
     */
    private $_connectionFactory = null;

    /**
     * Returns a ready-to-use factory to create open database connections.
     *
     * @return  \Yana\Db\IsConnectionFactory
     */
    public function getConnectionFactory(): \Yana\Db\IsConnectionFactory
    {
        if (!isset($this->_connectionFactory)) {
            $cache = new \Yana\Data\Adapters\SessionAdapter(__CLASS__);
            $schemaFactory = new \Yana\Db\SchemaFactory($cache);
            $this->_connectionFactory = new \Yana\Db\ConnectionFactory($schemaFactory);
        }
        return $this->_connectionFactory;
    }

}

?>