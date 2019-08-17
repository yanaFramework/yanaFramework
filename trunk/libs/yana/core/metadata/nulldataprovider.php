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

namespace Yana\Core\MetaData;

/**
 * Null adapter for testing purposes.
 *
 * @package     yana
 * @subpackage  core
 */
class NullDataProvider extends \Yana\Core\Object implements \Yana\Core\MetaData\IsDataProvider
{

    /**
     * Load meta data object.
     *
     * @param   string  $id  identifier for the file to be loaded
     * @return  \Yana\Core\MetaData\IsPackageMetaData
     * @throws  \Yana\Core\Exceptions\NotFoundException  when the file for this identifier is not found
     */
    public function loadOject($id)
    {
        return new \Yana\Core\MetaData\PackageMetaData();
    }

    /**
     * Returns an empty array.
     *
     * @return  array
     */
    public function getListOfValidIds()
    {
        return array();
    }

}

?>