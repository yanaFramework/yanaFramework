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

namespace Yana\Db\Helpers;

/**
 * <<algorithm>> Creates a pseudo-random unique id.
 *
 * WARNING! As with AFAIK all pseudo-random "unique" identifiers, there is a race condition.
 * That's not the fault of the implementation, it's just an inherent property of this class of generators.
 *
 * Thus, if two requests for uploads on the exact same table and column are handled at the exact same time,
 * the same identifiers may be generated.
 *
 * Thus, it is necessary to understand that the generated id is only "mostly" unique.
 *
 * The risk is very minor for low-traffic sites, especially with only one server running.
 *
 * However, high-traffic sites with multiple front-end servers may be more exposed to this issue.
 * Also, for sensitive data, even a minor risk may be unacceptable.
 *
 * Unfortunately there is no easy work-around for this problem.
 * Note that even database clusters may run into similar problems when generating IDs - it simply comes with the territory.
 *
 * Developers are thus highly encouraged to check the expected load and requirements of their system,
 * know the limitations of whatever solution they are using,
 * and - where necessary - implement a custom solution that fits their needs.
 *
 * @package     yana
 * @subpackage  db
 */
class IdGenerator extends \Yana\Core\Object
{

    /**
     * Create unique id to identify a file.
     *
     * @param   \Yana\Db\Ddl\Column  $column  column definition
     * @return  string
     */
    public function __invoke(\Yana\Db\Ddl\Column $column)
    {
        $table = $column->getParent();
        $tableName = "";
        if ($table instanceof \Yana\Db\Ddl\Table) {
            $tableName = $table->getName();
        }
        $columnName = $column->getName();
        return md5(uniqid("$tableName.$columnName.", true));
    }

}

?>