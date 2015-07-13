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

namespace Yana;

/**
 * <<Utility>> Encapsulated very basic debugging functions.
 *
 * @package    yana
 */
class DebugUtility extends \Yana\Core\AbstractUtility
{

    /**
     * debug breakpoint
     *
     * This is a debugging function that allows to use "de facto" breakpoints without the need of an extra debugger.
     * It creates and outputs a debug backtrace and dumps any vars you pass to it.
     *
     * Note: calling this function exists the program.
     *
     * Example of usage:
     * <code>
     * // ... some code here
     * ErrorUtility::breakpoint($foo, $bar);
     * // more code here ...
     * </code>
     */
    public static function breakpoint()
    {
        print "<h1>BREAKPOINT</h1>\n";

        /* var dumps */
        if (func_num_args() > 0) {
            print "<h2>Var-Dumps</h2>\n<ol>\n";
            assert('!isset($element)', ' Cannot redeclare var $element');
            foreach (func_get_args() as $element)
            {
                print "<li><pre>";
                var_dump($element);
                print "</pre></li>\n";
            } /* end foreach */
            unset($element); /* clean up garbage */
            print "</ol>\n";
        }

        /* debug backtrace */
        print "<h2>Backtrace</h2>\n";
        print "<ol>\n";

        $smarty = null;
        assert('!isset($element)', 'Cannot redeclare variable $element');
        foreach (debug_backtrace() as $element)
        {
            // ignore class ErrorUtility
            if (isset($element['class']) && (strcasecmp($element['class'], __CLASS__) === 0)) {
                continue;
            }

            // include line and file name
            if (isset($element['line']) && isset($element['file'])) {
                $element['file'] .= ", on line " . $element['line'];
                unset($element['line']);
            }

            // compose method name
            if (isset($element['class']) && isset($element['type'])) {
                $element['function'] = $element['class'] . $element['type'] . $element['function'];
                /* function arguments */
                $element['function'] .= '( ';
                if (!empty($element['args'])) {
                    assert('!isset($arg)', ' Cannot redeclare var $arg');
                    foreach ($element['args'] as $arg)
                    {
                        $element['function'] .= gettype($arg) . ' ';
                    } /* end foreach */
                    unset($arg); /* clean up garbage */
                } else {
                    $element['function'] .= 'void ';
                }
                $element['function'] .= ')';
                unset($element['class'], $element['type']);
            }
            print "<li><pre>";
            // add params
            $params = array('value' => &$element);
            print_r($params);
            print "</pre></li>";
        } /* end foreach */
        unset($element);

        print "</ol>\n";
        exit(0);
    }

}

?>