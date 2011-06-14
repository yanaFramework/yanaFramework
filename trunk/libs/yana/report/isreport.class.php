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

namespace Yana\Report;

/**
 * <<Interface>> Report Configuration.
 *
 * This defines the methods a reporting-class must implement.
 *
 * @access     public
 * @package    yana
 * @subpackage report
 * @name       IsReport
 *
 * @ignore
 */
interface IsReport
{

    /**
     * <<factory>> create a report.
     *
     * A report may have a title and contains a number of sub-reports or messages.
     *
     * @access  public
     * @static
     * @param   string  $title  report title
     * @return  IsReport
     */
    public static function createReport($title = "");

    /**
     * Adds a sub-report.
     *
     * It may have a title, contain more sub-reports or messages.
     *
     * @access  public
     * @param   string  $title  report title
     * @return  IsReport
     */
    public function addReport($title = "");

    /**
     * Returns a list of sub-reports.
     *
     * @access  public
     * @return  array
     */
    public function getReports();

    /**
     * Returns the title of a report.
     *
     * @access  public
     * @return  string
     */
    public function getTitle();

    /**
     * Adds a neutral text to the report.
     *
     * @access  public
     * @param   string  $message  text of message
     * @return  IsReport
     */
    public function addText($message);

    /**
     * Returns a list of texts.
     *
     * @access  public
     * @return  array
     */
    public function getTexts();

    /**
     * Adds a notice to the report.
     *
     * A notice is meant to inform about circumstances
     * that may or may be not the result of an error.
     *
     * Example:
     * "(Notice) you didn't lock the door." You may intentionally have
     * left the door open, but you might also have forgotten to lock it.
     * So this notice is meant to remind you - just in case.
     *
     * @access  public
     * @param   string  $message  text of message
     * @return  IsReport
     */
    public function addNotice($message);

    /**
     * Returns a list of notices.
     *
     * @access  public
     * @return  array
     */
    public function getNotices();

    /**
     * Adds a warning to the report.
     *
     * A warning is meant to inform the user that something is wrong.
     * While the issue itself is not critical, it may lead to more severe problems.
     *
     * If a warning is not solved, it may cause loss of functionality or
     * the software may not react as expected.
     *
     * @access  public
     * @param   string  $message  text of message
     * @return  IsReport
     */
    public function addWarning($message);

    /**
     * Returns a list of warnings.
     *
     * @access  public
     * @return  array
     */
    public function getWarnings();

    /**
     * Adds an error to the report.
     *
     * An error is meant to inform the user about a critical problem that
     * prevents the software from working properly.
     *
     * If an error is not solved, the software will not work
     * at all under certain circumstances and might possible crash -
     * including all the well known side-effects that may result from
     * a software crash, e.g. possible loss of data.
     *
     * @access  public
     * @param   string  $message  text of message
     * @return  IsReport
     */
    public function addError($message);

    /**
     * Returns a list of errors.
     *
     * @access  public
     * @return  array
     */
    public function getErrors();

}

?>
