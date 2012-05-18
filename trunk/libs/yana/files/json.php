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

namespace Yana\Files;

/**
 * JSON Files.
 *
 * This is a wrapper-class that may be used to work with *.json files.
 *
 * @package     yana
 * @subpackage  files
 * @since       3.1.0
 * @name        JsonFile
 */
class Json extends \Yana\Files\AbstractVarContainer
{

    /**
     * @return \Yana\Files\Decoders\SML
     */
    protected static function _getDecoder()
    {
        return new \Yana\Files\Decoders\Json();
    }

}

?>