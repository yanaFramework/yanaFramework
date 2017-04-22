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
 * @subpackage  views
 */
class ScanForAtModifier extends \Yana\Views\Helpers\AbstractViewHelper implements \Yana\Views\Helpers\IsModifier
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
        $matches = array();
        if (is_string($source) && preg_match_all("/[\w\.\-_]+@[\w\.\-_]+/", $source, $matches)) {

            assert('!isset($match); // $match already declared');
            foreach ($matches[0] as $match)
            {
                assert('!isset($exceptionMatch); // $exceptionMatch already declared');
                assert('!isset($encodedMatch); // $encodedMatch already declared');

                $encodedMatch = \Yana\Util\Strings::htmlEntities($match); // the replacement string

                // encode all mail addresses
                $source = str_replace($match, $encodedMatch, $source);
                // but avoid addresses in input fields
                $exceptionMatch = array();
                if (preg_match("/(\<input[^\>]+)" . preg_quote($encodedMatch, '/') . "/si", $source, $exceptionMatch)) {
                    $source = str_replace($exceptionMatch[0], $exceptionMatch[1] . $match, $source);
                }

                unset($exceptionMatch, $encodedMatch);
            }
            unset($match);
        }
        return $source;
    }

}

?>