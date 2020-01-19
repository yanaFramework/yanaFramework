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

namespace Yana\Db\Sources;

/**
 * <<interface>> Data source connection settings data-adapter.
 *
 * This loads and stores connection information to data sources.
 *
 * @package     yana
 * @subpackage  db
 *
 * @ignore
 */
interface IsAdapter extends \Yana\Data\Adapters\IsDataBaseAdapter
{

    /**
     * Load a data source by its name.
     *
     * @param   string  $name  unique name of the data set
     * @return  \Yana\Db\Sources\IsEntity
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no unique data source with that name was found
     */
    public function getFromDataSourceName(string $name): \Yana\Db\Sources\IsEntity;

}

?>