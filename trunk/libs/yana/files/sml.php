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

namespace Yana\Files;

/**
 * Simple Markup Language (SML) Files
 *
 * This is a wrapper-class that may be used to work with *.config and *.sml files.
 *
 * SML files provide the same semantics and functionality as JSON encoded files,
 * are as easy to read and understand, but they stick with XML-style markup,
 * which is widely used and understood by most people.
 *
 * @package     yana
 * @subpackage  files
 * @since       2.8.5
 */
class SML extends \Yana\Files\AbstractVarContainer
{

    /**
     * Returns an SML decoder.
     *
     * @return \Yana\Files\Decoders\SML
     */
    protected static function _getDecoder():  \Yana\Files\Decoders\IsDecoder
    {
        return new \Yana\Files\Decoders\SML();
    }

}

?>