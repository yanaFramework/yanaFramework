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

namespace Yana\Db\Doctrine;

/**
 * <<abstract>> Wrapper / adapter for Doctrine database abstraction layer (DBAL).
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractDriver extends \Yana\Core\AbstractDecorator implements \Yana\Db\IsDriver
{

    /**
     * <<constructor>> Initialize instance.
     *
     * @param  \Doctrine\DBAL\Connection  $driver  a Doctrine connection object
     */
    public function __construct(\Doctrine\DBAL\Connection $driver)
    {
        $this->_setDecoratedObject($driver);
    }

    /**
     * Returns the instance that all calls will be relayed to.
     *
     * @return  \Doctrine\DBAL\Connection
     */
    protected function _getDecoratedObject()
    {
        return parent::_getDecoratedObject();
    }


}

?>