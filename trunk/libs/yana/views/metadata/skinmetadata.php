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

namespace Yana\Views\MetaData;

/**
 * Skin meta data.
 *
 * @package     yana
 * @subpackage  views
 */
class SkinMetaData extends \Yana\Core\MetaData\PackageMetaData implements \Yana\Views\MetaData\IsSkinMetaData, \Yana\Report\IsReportable
{

    /**
     * @var  \Yana\Views\MetaData\TemplateMetaData[] 
     */
    private $_templates = array();

    /**
     * Add template information.
     *
     * @param   \Yana\Views\MetaData\TemplateMetaData  $template  meta data
     * @return  \Yana\Views\MetaData\SkinMetaData
     */
    public function addTemplate(\Yana\Views\MetaData\TemplateMetaData $template)
    {
        $this->_templates[$template->getId()] = $template;
        return $this;
    }

    /**
     * Get list of template data objects.
     *
     * @return  \Yana\Views\MetaData\IsTemplateMetaData[] 
     */
    public function getTemplates()
    {
        return $this->_templates;
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
        $reportBuilder = new \Yana\Views\MetaData\Reporting\SkinReportBuilder($report);
        $reportBuilder->setSkinConfiguration($this);

        return $reportBuilder->buildReport();
    }

}

?>
