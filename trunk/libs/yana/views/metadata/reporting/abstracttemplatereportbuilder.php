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

namespace Yana\Views\MetaData\Reporting;

/**
 * Helper class that creates reporting objects for managers.
 *
 * @package     yana
 * @subpackage  views
 * @ignore
 */
abstract class AbstractTemplateReportBuilder extends \Yana\Views\MetaData\Reporting\AbstractBuilder implements \Yana\Views\MetaData\Reporting\IsTemplateReportBuilder
{

    /**
     * @var  \Yana\Views\MetaData\TemplateMetaData
     */
    private $_templateConfiguration = null;

    /**
     * Get the template configuration.
     *
     * @return \Yana\Views\MetaData\TemplateMetaData
     */
    public function getTemplateConfiguration()
    {
        return $this->_templateConfiguration;
    }

    /**
     * Set the template configuration object.
     *
     * @param \Yana\Views\MetaData\TemplateMetaData $templateConfiguration
     * @return \Yana\Views\Skins\ReportBuilder
     */
    public function setTemplateConfiguration(\Yana\Views\MetaData\TemplateMetaData $templateConfiguration)
    {
        $this->_templateConfiguration = $templateConfiguration;
        return $this;
    }

    /**
     * Builds the report.
     *
     * Returns a \Yana\Report\Xml object, which you may print, transform or output to a file.
     * Informs about configuration issues or errors.
     *
     * @param   \Yana\Report\IsReport  $report  base report
     * @return  \Yana\Report\IsReport
     * @name    Skin::getReport()
     * @ignore
     */
    public function buildReport()
    {
        assert(!isset($hasError), 'Cannot redeclare var $hasError');
        $hasError =
            !$this->_checkTemplateFileExists() ||
            !$this->_checkLanguageFilesExist() ||
            !$this->_checkStylesheetFilesExist() ||
            !$this->_checkScriptFilesExist();

        if ($hasError !== true) {
            $this->_onSuccess();
        } else {
            $this->_onError();
        }

        return $this->_getReport();
    }

    /**
     * Check if template file exists.
     *
     * Returns bool(true) if it does and bool(false) otherwise.
     * On FALSE it also creates adds an error description to the report.
     *
     * @return  bool
     */
    abstract protected function _checkTemplateFileExists();

    /**
     * Check language file references.
     *
     * Returns bool(true) if all required language files exist and bool(false) otherwise.
     * On FALSE it also creates adds an error description to the report.
     *
     * @return  bool
     */
    abstract protected function _checkLanguageFilesExist();

    /**
     * Check stylesheet references.
     *
     * Returns bool(true) if all required stylesheet files exist and bool(false) otherwise.
     * On FALSE it also creates adds an error description to the report.
     *
     * @return  bool
     */
    abstract protected function _checkStylesheetFilesExist();

    /**
     * Check script file references.
     *
     * Returns bool(true) if all required script files exist and bool(false) otherwise.
     * On FALSE it also creates adds an error description to the report.
     *
     * @return  bool
     */
    abstract protected function _checkScriptFilesExist();

    /**
     * Hook function on success.
     */
    abstract protected function _onSuccess();

    /**
     * Hook function on failure.
     */
    abstract protected function _onError();

}

?>