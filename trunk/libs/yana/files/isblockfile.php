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

namespace Yana\Files;

/**
 * <<interface>> For IP blocking
 *
 * @package     yana
 * @subpackage  files
 * @ignore
 */
interface IsBlockFile extends \Yana\Files\IsWritable
{

    /**
     * Replace file contents by $input.
     *
     * Note that changes are buffered and will
     * not be written to the file unless you explicitely call write().
     *
     * @param   string|array  $input new file contents
     * @return  $this
     */
    public function setContent($input);

    /**
     * Check if the current user has been listed.
     *
     * Returns bool(true) if the user's IP has been listed and bool(false) otherwise.
     *
     * @param   string  $remoteAddress  the user's IP address (IPv4 and IPv6 supported)  
     * @return  bool
     */
    public function isBlocked(string $remoteAddress): bool;

}

?>