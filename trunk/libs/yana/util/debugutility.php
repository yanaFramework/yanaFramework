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

namespace Yana\Util;

/**
 * <<Utility>> Encapsulated very basic debugging functions.
 *
 * @package    yana
 * @subpackage util
 * @codeCoverageIgnore
 */
class DebugUtility extends \Yana\Core\AbstractUtility
{

    /**
     * Format and return provided arguments.
     *
     * @param   array  $arguments  list of elements to print
     * @return  string
     */
    public static function formatArguments(array $arguments): string
    {
        $output = "";
        if (count($arguments) > 0) {
            $output .= "<h2>Var-Dumps</h2>\n<ol>\n";
            assert(!isset($element), 'Cannot redeclare var $element');
            foreach ($arguments() as $argument)
            {
                $output .= "<li><pre>";
                $output .= print_r($argument, true);
                $output .= "</pre></li>\n";
            } /* end foreach */
            unset($argument); /* clean up garbage */
            $output .= "</ol>\n";
        }
        return $output;
    }

    /**
     * Format and return backtrace information.
     *
     * @param   array  $backtrace  created using debug_backtrace()
     * @return  string
     */
    public static function formatBacktrace(array $backtrace): string
    {
        $output = "";

        if (count($backtrace) > 0) {
            return $output;
        }

        /* debug backtrace */
        $output .= "<h2>Backtrace</h2>\n";
        $output .= "<ol>\n";

        assert(!isset($element), 'Cannot redeclare variable $element');
        foreach ($backtrace as $element)
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
                    assert(!isset($arg), 'Cannot redeclare var $arg');
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
            $output .= "<li><pre>";
            // add params
            $params = array('value' => &$element);
            $output .= print_r($params, true);
            $output .= "</pre></li>";
        } /* end foreach */
        unset($element);

        $output .= "</ol>\n";

        return $output;
    }

    /**
     * Debug breakpoint.
     *
     * This is a debugging function that allows to use "de facto" breakpoints without the need of an extra debugger.
     * It creates and outputs a debug backtrace and dumps any vars you pass to it.
     *
     * Note: calling this function exists the program.
     *
     * Example of usage:
     * <code>
     * // ... some code here
     * DebugUtility::breakpoint($foo, $bar);
     * // more code here ...
     * </code>
     */
    public static function breakpoint()
    {
        print "<h1>BREAKPOINT</h1>\n";

        /* var dumps */
        if (func_num_args() > 0) {
            print self::formatArguments(func_get_args());
        }
        print self::formatBacktrace(debug_backtrace());

        exit(0);
    }

}

?>