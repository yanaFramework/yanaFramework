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

namespace Yana\Io\Adapters;

/**
 * <<Interface>> Data Entity
 *
 * Base class for plain entities.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsEntity extends \Yana\Core\IsObject
{

    /**
     * Return an array of all valid identifiers.
     *
     * @return  scalar
     */
    public function getId();

    /**
     * This sets the data adapter used to persist the entity
     *
     * @param  \Yana\Io\Adapters\IsDataAdapter  $adapter  object that should be used
     */
    public function setDataAdapter(\Yana\Io\Adapters\IsDataAdapter $adapter);

    /**
     * Calls the assigned data adapter to persist the entity.
     */
    public function saveEntity();

}

?>