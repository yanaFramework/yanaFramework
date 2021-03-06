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
declare(strict_types=1);

namespace Yana\Db\Sources;

/**
 * <<enumeration>> Lists all supported RDBMS.
 *
 * @package     yana
 * @subpackage  db
 */
class TableEnumeration extends \Yana\Core\AbstractEnumeration
{
    const TABLE = 'datasources';
    const ID = 'datasource_id';
    const NAME = 'datasource_name';
    const DBMS = 'datasource_dbms';
    const HOST = 'datasource_host';
    const PORT = 'datasource_port';
    const DATABASE = 'datasource_database';
    const USER = 'datasource_user';
    const PASSWORD = 'datasource_pass';

}

?>