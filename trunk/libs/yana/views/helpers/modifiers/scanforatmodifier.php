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

namespace Yana\Views\Helpers\Modifiers;

/**
 * Smarty-compatible modifier.
 *
 * This class is registered when instantiating the Smarty Engine.
 *
 * @package     yana
 * @subpackage  templates
 */
class ScanForAtModifier extends \Yana\Core\Object implements \Yana\Views\Helpers\IsModifier
{

    /**
     * <<smarty modifier>> Scan for at.
     *
     * Obfuscates e-mail addresses by converting all characters to entities.
     *
     * @param   string  $source  possibly an e-mail address
     * @return  string
     */
    public function __invoke($source)
    {
        if (is_string($source) && preg_match_all("/[\w\.\-_]+@[\w\.\-_]+/", $source, $matches)) {
            foreach ($matches[0] as $match)
            {
                $source = str_replace($match, htmlspecialchars($match, ENT_COMPAT, 'UTF-8'), $source);
            }
        }
        return $source;
    }

}

?>