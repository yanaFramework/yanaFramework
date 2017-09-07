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
 * Report Configuration.
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
 * $report = Xml::createReport();
 * $group = $report->addGroup("");
 * </code>
 *
 * @package    yana
 * @subpackage report
 * @name       Xml
 *
 * @ignore
 */
class Xml extends \SimpleXMLElement implements IsReport
{

    /**
     * <<factory>> load a file.
     *
     * Returns the file identified by $path as a Xml object.
     * Returns NULL on error.
     *
     * @param   string  $path  file path
     * @return  \Yana\Report\Xml
     */
    public static function loadFile($path)
    {
        assert('is_string($path); // Wrong type for argument 1. String expected');
        return \simplexml_load_file($path, __CLASS__);
    }

    /**
     * <<factory>> create a report.
     *
     * A report may have a title and contains a number of sub-reports or messages.
     *
     * @param   string  $title  report title
     * @return  \Yana\Report\Xml
     */
    public static function createReport($title = "")
    {
        assert('is_string($title); // Wrong type for argument 1. String expected');
        $content = "";
        if (!empty($title)) {
            $content = "<title>$title</title>";
        }
        return new self("<report>$content</report>");
    }

    /**
     * Adds a sub-report.
     *
     * It may have a title, contain more sub-reports or messages.
     *
     * @param   string  $title  report title
     * @return  self
     */
    public function addReport($title = "")
    {
        assert('is_string($title); // Wrong type for argument 1. String expected');
        $report = null;
        if ($this->getName() === 'report') {
            $report = $this->addChild("report");
            if (!empty($title)) {
                $report->addChild("title", (string) $title);
            }
        }
        return $report;
    }

    /**
     * Returns an array of all child tags that are report tags.
     *
     * @return  array
     */
    public function getReports()
    {
        return $this->_getChildrenByName('report');
    }

    /**
     * Returns the title of a report.
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->_getNodeAsText('title');
    }

    /**
     * Adds a neutral text to the report.
     *
     * @param   string  $message  text of message
     * @return  self
     */
    public function addText($message)
    {
        assert('is_string($message); // Wrong type for argument 1. String expected');
        assert('!empty($message); // Argument 1 should not be empty');
        if ($this->getName() === 'report') {
            $this->addChild("text", (string) $message);
        }
        return $this;
    }

    /**
     * Returns a list of texts.
     *
     * @return  array
     */
    public function getTexts()
    {
        return $this->_getChildrenByName('text');
    }

    /**
     * Finds and returns a list of all child-nodes with the given name.
     *
     * If there are none, the list is empty.
     *
     * @param   string  $name  tag name to match
     * @return  array
     */
    protected function _getChildrenByName($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');

        $nodes = array();

        foreach ($this->children() as $node)
        {
            if ($node->getName() === $name) {
                $nodes[] = $node;
            }
        }
        unset($node);

        return $nodes;
    }

    /**
     * Return inner text of node.
     *
     * If the node is empty, the returned text is empty.
     *
     * @return  string
     */
    protected function _asText()
    {
        return (string) parent::__toString();
    }

    /**
     * Return inner text of node.
     *
     * If the node is empty, the returned text is empty.
     *
     * @param   string  $name  of tag
     * @return  string
     */
    protected function _getNodeAsText($name)
    {
        $text = "";
        if (isset($this->$name) && $this->$name instanceof \Yana\Report\Xml) {
            /* @var $node \Yana\Report\Xml */
            $node = $this->$name;
            $text = $node->_asText();
        }
        return $text;
    }
    
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
     * @param   string  $message  text of message
     * @return  self
     */
    public function addNotice($message)
    {
        assert('is_string($message); // Wrong type for argument 1. String expected');
        assert('!empty($message); // Argument 1 should not be empty');
        if ($this->getName() === 'report') {
            $this->addChild("notice", (string) $message);
        }
        return $this;
    }

    /**
     * Returns a list of notices.
     *
     * @return  array
     */
    public function getNotices()
    {
        return $this->_getChildrenByName('notice');
    }

    /**
     * Adds a warning to the report.
     *
     * A warning is meant to inform the user that something is wrong.
     * While the issue itself is not critical, it may lead to more severe problems.
     *
     * If a warning is not solved, it may cause loss of functionality or
     * the software may not react as expected.
     *
     * @param   string  $message  text of message
     * @return  self
     */
    public function addWarning($message)
    {
        assert('is_string($message); // Wrong type for argument 1. String expected');
        assert('!empty($message); // Argument 1 should not be empty');
        if ($this->getName() === 'report') {
            $this->addChild("warning", (string) $message);
        }
        return $this;
    }

    /**
     * Returns a list of warnings.
     *
     * @return  array
     */
    public function getWarnings()
    {
        return $this->_getChildrenByName('warning');
    }

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
     * @param   string  $message  text of message
     * @return  self
     */
    public function addError($message)
    {
        assert('is_string($message); // Wrong type for argument 1. String expected');
        assert('!empty($message); // Argument 1 should not be empty');
        if ($this->getName() === 'report') {
            $this->addChild("error", (string) $message);
        }
        return $this;
    }

    /**
     * Returns a list of errors.
     *
     * @return  array
     */
    public function getErrors()
    {
        return $this->_getChildrenByName('error');
    }

    /**
     * <<magic>> Outputs the contents as an XML string.
     *
     * @return  string
     * @ignore
     */
    public function __toString()
    {
        return $this->asXML();
    }

    /**
     * Returns bool(true) if the node is 'report'.
     *
     * @return  bool
     */
    public function isReport()
    {
        return $this->getName() === 'report';
    }

    /**
     * Returns bool(true) if the node is 'title'.
     *
     * @return  bool
     */
    public function isTitle()
    {
        return $this->getName() === 'title';
    }

    /**
     * Returns bool(true) if the node is 'text'.
     *
     * @return  bool
     */
    public function isText()
    {
        return $this->getName() === 'text';
    }

    /**
     * Returns bool(true) if the node is 'notice'.
     *
     * @return  bool
     */
    public function isNotice()
    {
        return $this->getName() === 'notice';
    }

    /**
     * Returns bool(true) if the node is 'warning'.
     *
     * @return  bool
     */
    public function isWarning()
    {
        return $this->getName() === 'warning';
    }

    /**
     * Returns bool(true) if the node is 'error'.
     *
     * @return  bool
     */
    public function isError()
    {
        return $this->getName() === 'error';
    }

}

?>