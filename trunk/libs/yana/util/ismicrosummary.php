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

namespace Yana\Util;

/**
 * <<interface>> Microsummary.
 *
 * This class provides methods to read and write persistent microsummaries.
 *
 * "Microsummaries" are a Firefox 2.0 feature that allows users to create dynamic bookmark
 * titles that automatically update when the content of the bookmarked page changes.
 *
 * @package     yana
 * @subpackage  utilities
 */
interface IsMicrosummary
{

    /**
     * Get the text of a microsummary.
     *
     * Reads the microsummary string identified by $id and returns it.
     * This function returns bool(false) if no corresponding summary exists.
     *
     * Example of usage:
     *
     * Retrieving a microsummary in a plugin:
     * <code>$text = $microsummary->getText(__CLASS__);</code>
     *
     * @param   string  $id  identifies the summary to get
     * @return  string
     */
    public function getText($id);

    /**
     * Set the text of a microsummary.
     *
     * Saves the microsummary string identified by $id for later use.
     * This function returns bool(false) if the input is invalid or
     * the entry could not be saved.
     *
     * Example of usage:
     *
     * Retrieving a microsummary in a plugin:
     * <code>$text = $microsummary->getText(__CLASS__);</code>
     *
     * @param   string  $id    identifies the summary to get
     * @param   string  $text  the text of the microsummary
     * @return  bool
     */
    public function setText($id, $text);

    /**
     * Publish a microsummary.
     *
     * Adds the microsummary identified by $id to the list of
     * microsummaries to be printed to the browser.
     *
     * Example of usage:
     *
     * Add this to your output function
     * <code>$microsummary->publishSummary($this->name);</code>
     *
     * Returns bool(false) on error.
     *
     * @param   string  $id  identifies the summary to get
     * @return  bool
     */
    public function publishSummary($id);
    /**
     * Returns a list of all previously published microsummaries.
     *
     * @return  array
     */
    public function getSummaries();

}

?>