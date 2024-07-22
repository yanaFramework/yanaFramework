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
declare(strict_types=1);

namespace Yana\Views\MetaData;

/**
 * Describes the configuration of a template.
 *
 * @package     yana
 * @subpackage  views
 */
class TemplateMetaData extends \Yana\Core\StdObject implements \Yana\Views\MetaData\IsTemplateMetaData, \Yana\Report\IsReportable
{

    /**
     * @var  string
     */
    private $_id = "";

    /**
     * @var  string
     */
    private $_file = "";

    /**
     * @var  array
     */
    private $_languages = array();

    /**
     * @var  array
     */
    private $_scripts = array();

    /**
     * @var  array
     */
    private $_styles = array();

    /**
     * Set template id.
     *
     * @param   string  $id  some string that is a valid identifier
     * @return  $this
     */
    public function setId(string $id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * Get template id.
     *
     * @return  string
     */
    public function getId(): string
    {
        return $this->_id;
    }

    /**
     * Set path to template file.
     *
     * @param   string  $file  valid file path
     * @return  $this
     */
    public function setFile(string $file)
    {
        $this->_file = $file;
        return $this;
    }

    /**
     * Return path to template file.
     *
     * This returns the path and name of the template file associated with
     * the template as it was defined.
     *
     * Note: This function does not check if the defined file actually does exist.
     *
     * @return  string
     */
    public function getFile(): string
    {
        return $this->_file;
    }

    /**
     * Set list of language ids.
     *
     * See {@see \Yana\Translations\Language} for more details on language ids.
     *
     * @param   array  $languages  the ids are filenames (without path or extension)
     * @return  $this
     */
    public function setLanguages(array $languages)
    {
        $this->_languages = $languages;
        return $this;
    }

    /**
     * Get list of language ids.
     *
     * The array may contain numeric and string indexes.
     * String indexes are to be used as identifiers.
     *
     * @return  array
     */
    public function getLanguages(): array
    {
        return $this->_languages;
    }

    /**
     * Set list of script files.
     *
     * These should be relative paths that can be used as URIs.
     *
     * @param   array  $scripts  list of valid file paths
     * @return  $this
     */
    public function setScripts(array $scripts)
    {
        $this->_scripts = $scripts;
        return $this;
    }

    /**
     * Get list of script files.
     *
     * The array may contain numeric and string indexes.
     * String indexes are to be used as identifiers.
     *
     * @return  array
     */
    public function getScripts(): array
    {
        return $this->_scripts;
    }

    /**
     * Set list of stylesheet files.
     *
     * These should be relative paths that can be used as URIs.
     *
     * @param   array  $styles  list of valid file paths
     * @return  $this
     */
    public function setStyles(array $styles)
    {
        $this->_styles = $styles;
        return $this;
    }

    /**
     * Get list of stylesheet files.
     *
     * The array may contain numeric and string indexes.
     * String indexes are to be used as identifiers.
     *
     * @return  array
     */
    public function getStyles(): array
    {
        return $this->_styles;
    }

    /**
     * Check this object for errors and return a report.
     *
     * Returns the a report object.
     * The report is expected to be a valid XML document.
     *
     * @param   \Yana\Report\IsReport  $report  base report
     * @return  \Yana\Report\IsReport
     */
    public function getReport(?\Yana\Report\IsReport $report = null)
    {
        if (!$report instanceof \Yana\Report\IsReport) {
            $report = \Yana\Report\Xml::createReport(__CLASS__);
        }
        $reportBuilder = new \Yana\Views\MetaData\Reporting\TemplateReportBuilder($report);
        $reportBuilder->setTemplateConfiguration($this);
        return $reportBuilder->buildReport();
    }

}

?>
