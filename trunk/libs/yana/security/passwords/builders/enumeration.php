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

namespace Yana\Security\Passwords\Builders;

/**
 * <<enumeration>> Of algorithm names.
 *
 * @package     yana
 * @subpackage  security
 *
 * @ignore
 */
class Enumeration extends \Yana\Core\Object
{

    /**
     * Uses the SHA-256 algorithm.
     */
    const SHA256 = 'sha256';
    /**
     * Uses the SHA-512 algorithm.
     */
    const SHA512 = 'sha512';
    /**
     * Uses the Blowfish algorithm.
     *
     * Alias of BCRYPT.
     */
    const BLOWFISH = 'blowfish';
    /**
     * Uses the default algorithm.
     *
     * Note! This may change from one version of PHP to the other.
     */
    const BASIC = 'default';
    /**
     * Uses the Blowfish algorithm
     *
     * Alias of BLOWFISH.
     */
    const BCRYPT = 'bcrypt';

}

?>