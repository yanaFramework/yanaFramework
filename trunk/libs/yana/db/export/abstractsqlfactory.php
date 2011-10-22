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
 */

namespace Yana\Db\Export;

/**
 * <<abstract>> database Creator.
 *
 * This decorator class is intended to create SQL DDL (data definition language)
 * from YANA Framework - database structure files.
 *
 * For this task it provides functions which create specific
 * DDL for various DBS.
 *
 * @package     yana
 * @subpackage  db
 */
abstract class AbstractSqlFactory extends \Object
{

    /**
     * @var \Yana\Db\Export\IsXslProvider
     */
    private $_provider = null;

    /**
     * Get XSL-Document provider.
     *
     * @return \Yana\Db\Export\IsXslProvider 
     */
    protected function _getProvider()
    {
        if (!isset($this->_provider)) {
            $this->_provider = new \Yana\Db\Export\Xsl\Provider();
        }
        return $this->_provider;
    }

    /**
     * Set XSL-Document provider.
     *
     * @param   \Yana\Db\Export\IsXslProvider  $provider  loads XSL templates
     * @return  AbstractSqlFactory
     */
    protected function _setProvider(\Yana\Db\Export\IsXslProvider $provider)
    {
        if (!isset($this->_provider)) {
            $this->_provider = new \Yana\Db\Export\Xsl\Provider();
        }
        return $this;
    }

}

?>