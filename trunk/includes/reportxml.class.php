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

/**
 * Report Configuration
 *
 * This is meant to be used to create new report files.
 *
 * A report should automatically be generated when you
 * cast an object to a string.
 * It is recommended to use this Reporting class to create
 * such a report.
 *
 * Example:
 * <code>
 * $report = ReportXML::createReport();
 * $group = $report->addGroup("");
 * </code>
 *
 * @access     public
 * @package    yana
 * @subpackage error_reporting
 * @name       ReportXML
 *
 * @ignore
 */
class ReportXML extends SimpleXMLElement
{
    /**
     * <<factory>> load a file
     *
     * Returns the file identified by $path as a ReportXML object.
     * Returns NULL on error.
     *
     * @access  public
     * @static
     * @param   string  $path  file path
     * @return  ReportXML
     */
    public static function loadFile($path)
    {
        assert('is_string($path); // Wrong type for argument 1. String expected');
        try {

            return simplexml_load_file($path, __CLASS__);

        } catch (Exception $e) {
            Log::report("Error in report file: '$path'.", E_USER_WARNING, $e->getMessage());
            return null;
        }
    }

    /**
     * <<factory>> create a report
     *
     * Returns the an empty file identified by $path as a ReportXML object.
     *
     * A report may have a title and contains a number of sub-reports or
     * messages.
     *
     * @access  public
     * @static
     * @param   string  $title  report title
     * @return  ReportXML
     */
    public static function createReport($title = "")
    {
        assert('is_string($title); // Wrong type for argument 1. String expected');
        if (!empty($title)) {
            return new ReportXML("<report><title>$title</title></report>");
        } else {
            return new ReportXML("<report></report>");
        }
    }

    /**
     * add a sub-report
     *
     * Adds a sub-report.
     * It may have a title, contain more sub-reports or messages.
     *
     * @access  public
     * @param   string  $title  report title
     * @return  ReportXML
     */
    public function addReport($title = "")
    {
        assert('is_string($title); // Wrong type for argument 1. String expected');
        if ($this->getName() === 'report') {
            $report = $this->addChild("report");
            if (!empty($title)) {
                $report->addChild("title", (string) $title);
            }
            return $report;
        } else {
            return null;
        }
    }

    /**
     * get list of sub-reports
     *
     * Returns a list of reports.
     *
     * @access  public
     * @return  array
     */
    public function getReports()
    {
        if (isset($this->report)) {
            return $this->report;
        } else {
            return array();
        }
    }

    /**
     * get title
     *
     * Returns the title of a report.
     *
     * @access  public
     * @return  string
     */
    public function getTitle()
    {
        if (isset($this->title)) {
            return $this->title;
        } else {
            return "";
        }
    }

    /**
     * add text
     *
     * Adds a neutral text to the report.
     *
     * @access  public
     * @param   string  $message  text of message
     * @return  string
     */
    public function addText($message)
    {
        assert('is_string($message); // Wrong type for argument 1. String expected');
        assert('!empty($message); // Argument 1 should not be empty');
        if ($this->getName() === 'report') {
            $this->addChild("text", (string) $message);
        }
    }

    /**
     * get list of texts
     *
     * Returns a list of texts.
     *
     * @access  public
     * @return  array
     */
    public function getTexts()
    {
        if (isset($this->text)) {
            return $this->text;
        } else {
            return array();
        }
    }

    /**
     * add notice
     *
     * Adds a notice to the report.
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
     * @return  string
     */
    public function addNotice($message)
    {
        assert('is_string($message); // Wrong type for argument 1. String expected');
        assert('!empty($message); // Argument 1 should not be empty');
        if ($this->getName() === 'report') {
            $this->addChild("notice", (string) $message);
        }
    }

    /**
     * get list of notices
     *
     * Returns a list of notices.
     *
     * @access  public
     * @return  array
     */
    public function getNotices()
    {
        if (isset($this->notice)) {
            return $this->notice;
        } else {
            return array();
        }
    }

    /**
     * add warning
     *
     * Adds a warning to the report.
     * A warning is meant to inform the user that something is wrong.
     * While the issue itself is not critical, it may lead to more severe problems.
     *
     * If a warning is not solved, it may cause loss of functionality or
     * the software may not react as expected.
     *
     * @access  public
     * @param   string  $message  text of message
     * @return  string
     */
    public function addWarning($message)
    {
        assert('is_string($message); // Wrong type for argument 1. String expected');
        assert('!empty($message); // Argument 1 should not be empty');
        if ($this->getName() === 'report') {
            $this->addChild("warning", (string) $message);
        }
    }

    /**
     * get list of warnings
     *
     * Returns a list of warnings.
     *
     * @access  public
     * @return  array
     */
    public function getWarnings()
    {
        if (isset($this->warning)) {
            return $this->warning;
        } else {
            return array();
        }
    }

    /**
     * add error
     *
     * Adds an error to the report.
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
     * @return  string
     */
    public function addError($message)
    {
        assert('is_string($message); // Wrong type for argument 1. String expected');
        assert('!empty($message); // Argument 1 should not be empty');
        if ($this->getName() === 'report') {
            $this->addChild("error", (string) $message);
        }
    }

    /**
     * get list of errors
     *
     * Returns a list of errors.
     *
     * @access  public
     * @return  array
     */
    public function getErrors()
    {
        if (isset($this->error)) {
            return $this->error;
        } else {
            return array();
        }
    }

    /**
     * <<magic>> convert to string
     *
     * Outputs the contents as an XML string.
     *
     * @access  public
     * @return  string
     * @ignore
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * convert to string
     *
     * Outputs the contents as an XML string.
     *
     * @access  public
     * @return  string
     */
    public function toString()
    {
        return $this->asXML();
    }

    /**
     * check type of node
     *
     * Returns bool(true) if the node has the given type and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isReport()
    {
        return $this->getName() === 'report';
    }

    /**
     * check type of node
     *
     * Returns bool(true) if the node has the given type and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isTitle()
    {
        return $this->getName() === 'title';
    }

    /**
     * check type of node
     *
     * Returns bool(true) if the node has the given type and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isText()
    {
        return $this->getName() === 'text';
    }

    /**
     * check type of node
     *
     * Returns bool(true) if the node has the given type and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isNotice()
    {
        return $this->getName() === 'notice';
    }

    /**
     * check type of node
     *
     * Returns bool(true) if the node has the given type and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isWarning()
    {
        return $this->getName() === 'warning';
    }

    /**
     * check type of node
     *
     * Returns bool(true) if the node has the given type and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isError()
    {
        return $this->getName() === 'error';
    }
}

?>
