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

namespace Yana\Security\Passwords\Providers;

/**
 * <<abstract>> User data-adapter.
 *
 * This persistent class provides access to user data and function to set logins and passwords.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
abstract class AbstractAdapter extends \Yana\Data\Adapters\AbstractDatabaseAdapter implements \Yana\Security\Passwords\Providers\IsAdapter
{

    /**
     * Basic ORM helper object.
     *
     * @var  \Yana\Data\Adapters\IsEntityMapper
     */
    private $_entityMapper = null;

    /**
     * <<construct>> Creates a new authentication provider manager.
     *
     * If no mapper is given, this function creates and uses an instance of \Yana\Security\Passwords\Providers\Mapper.
     *
     * @param  \Yana\Db\IsConnection               $connection  database connection to schema user
     * @param  \Yana\Data\Adapters\IsEntityMapper  $mapper      simple OR-mapper to convert database entries to objects
     */
    public function __construct(\Yana\Db\IsConnection $connection, ?\Yana\Data\Adapters\IsEntityMapper $mapper = null)
    {
        parent::__construct($connection);
        $this->_entityMapper = $mapper;
    }

    /**
     * Returns an instance of an OR-mapping class.
     *
     * Use this to map database entries to objects and vice-versa.
     *
     * @return  \Yana\Data\Adapters\IsEntityMapper
     */
    protected function _getEntityMapper()
    {
        if (!isset($this->_entityMapper)) {
            // @codeCoverageIgnoreStart
            $this->_entityMapper = new \Yana\Security\Passwords\Providers\Mapper();
            // @codeCoverageIgnoreEnd
        }
        return $this->_entityMapper;
    }

}

?>
