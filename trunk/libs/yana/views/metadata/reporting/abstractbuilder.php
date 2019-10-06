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

namespace Yana\Views\MetaData\Reporting;

/**
 * Helper class that creates reporting objects for managers.
 *
 * @package     yana
 * @subpackage  views
 * @ignore
 */
abstract class AbstractBuilder extends \Yana\Core\StdObject implements \Yana\Views\MetaData\Reporting\IsBuilder
{

    /**
     * @var  \Yana\Report\IsReport
     */
    private $_report = null;

    /**
     * Initializes the manager class
     *
     * @param  \Yana\Report\IsReport  $report  inject base report class
     */
    public function __construct(\Yana\Report\IsReport $report)
    {
        $this->_report = $report;
    }

    /**
     * Returns the report object.
     *
     * @return  \Yana\Report\IsReport
     */
    protected function _getReport(): \Yana\Report\IsReport
    {
        return $this->_report;
    }

}

?>