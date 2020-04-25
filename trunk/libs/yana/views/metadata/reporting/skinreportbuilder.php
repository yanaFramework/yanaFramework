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
class SkinReportBuilder extends \Yana\Views\MetaData\Reporting\AbstractBuilder implements \Yana\Views\MetaData\Reporting\IsSkinReportBuilder
{

    /**
     * @var  \Yana\Views\MetaData\SkinMetaData
     */
    private $_skinConfiguration = null;

    /**
     * Returns a list of SkinMetaData objects.
     *
     * @return  \Yana\Views\MetaData\SkinMetaData|NULL
     */
    public function getSkinConfiguration(): ?\Yana\Views\MetaData\SkinMetaData
    {
        return $this->_skinConfiguration;
    }

    /**
     * Adds a configuration object.
     *
     * @param   \Yana\Views\MetaData\SkinMetaData  $skinConfiguration
     * @return  $this
     */
    public function setSkinConfiguration(\Yana\Views\MetaData\SkinMetaData $skinConfiguration)
    {
        $this->_skinConfiguration = $skinConfiguration;
        return $this;
    }

    /**
     * Builds the report.
     *
     * Returns a \Yana\Report\Xml object, which you may print, transform, or output to a file.
     * Informs about configuration issues or errors.
     *
     * @param   \Yana\Report\IsReport  $report  base report
     * @return  \Yana\Report\IsReport
     */
    public function buildReport()
    {
        assert(!isset($report), '$report already declared');
        $report = $this->_getReport();

        // loop through template definition and create a report for each
        if ($this->getSkinConfiguration()) {
            assert(!isset($template), 'Cannot redeclare var $template');
            foreach ($this->getSkinConfiguration()->getTemplates() as $key => $template)
            {
                // Create template sub-report
                $template->getReport($report->addReport((string) $key));
            }
            unset($template);
        }

        return $report;
    }

}

?>