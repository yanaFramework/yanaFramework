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

namespace Yana\Db\Sql\Quoting;

/**
 * <<algorithm>> This algorithm implements a generic way of quoting strings.
 *
 * @package     yana
 * @subpackage  db
 */
class GenericAlgorithm extends \Yana\Core\Object implements \Yana\Db\Sql\Quoting\IsAlgorithm
{

    /**
     * Returns the quoted database identifier as a string.
     *
     * @param   string  $value  any string that needs to be quoted
     * @return  string
     */
    public function quote(string $value): string
    {
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace(YANA_DB_DELIMITER, '\\' . YANA_DB_DELIMITER, $value);
        $value = preg_replace('/[\x00\x1A]/us', '', $value);
        $value = preg_replace("/\n/us", '\\n', $value);
        $value = preg_replace("/\r/us", '\\r', $value);
        $value = preg_replace("/\f/us", '\\f', $value);
        return YANA_DB_DELIMITER . $value . YANA_DB_DELIMITER;
    }

}

?>