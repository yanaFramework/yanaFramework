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
 * <<abstract>> Security level rule data-adapter.
 *
 * Provides access to security data.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
abstract class AbstractAdapter extends \Yana\Data\Adapters\AbstractDatabaseAdapter implements \Yana\Security\Data\SecurityLevels\IsDataAdapter
{

    /**
     * Basic ORM helper object.
     *
     * @var  \Yana\Security\Data\SecurityLevels\IsMapper
     */
    private $_entityMapper = null;

    /**
     * <<construct>> Creates a new user-manager.
     *
     * If no mapper is given, this function creates and uses an instance of \Yana\Security\Data\UserMapper.
     *
     * @param  \Yana\Db\IsConnection                        $connection  database connection to table user
     * @param  \Yana\Security\Data\SecurityLevels\IsMapper  $mapper      simple OR-mapper to convert database entries to objects
     */
    public function __construct(\Yana\Db\IsConnection $connection, \Yana\Security\Data\SecurityLevels\IsMapper $mapper = null)
    {
        parent::__construct($connection);
        $this->_entityMapper = $mapper;
    }

    /**
     * Returns an instance of an OR-mappinging class.
     *
     * Use this to map database entries to objects and vice-versa.
     *
     * @return  \Yana\Security\Data\SecurityLevels\IsMapper
     */
    protected function _getEntityMapper()
    {
        if (!isset($this->_entityMapper)) {
            $this->_entityMapper = new \Yana\Security\Data\SecurityLevels\Mapper();
        }
        return $this->_entityMapper;
    }

}

?>