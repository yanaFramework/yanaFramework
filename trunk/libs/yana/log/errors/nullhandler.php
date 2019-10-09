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

namespace Yana\Log\Errors;

/**
 * For unit testing only.
 *
 * @package     yana
 * @subpackage  log
 * @ignore
 * @codeCoverageIgnore
 */
class NullHandler extends \Yana\Log\Errors\AbstractHandler
{

    /**
     * This custom error handler implements logging of errors.
     *
     * @param   int     $errorNumber  ignored
     * @param   string  $description  ignored
     * @param   string  $file         ignored
     * @param   int     $lineNumber   ignored
     */
    public function handleError(int $errorNumber, string $description, string $file, int $lineNumber)
    {
        // intentionally left blank
    }

    /**
     * Handles failed assertions.
     *
     * @param   string  $pathToFile   ignored
     * @param   int     $lineNumber   ignored
     * @param   string  $code         ignored
     * @param   string  $description  ignored
     */
    public function handleAssertion(string $pathToFile, int $lineNumber, string $code, string $description = "")
    {
        // intentionally left blank
    }

    /**
     * Handles uncaught exceptions.
     *
     * @param  \Throwable  $e  ignored
     */
    public function handleException(\Throwable $e)
    {
        // intentionally left blank
    }

}

?>