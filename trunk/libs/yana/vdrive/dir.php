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
 * @package    yana
 * @license    http://www.gnu.org/licenses/gpl.txt
 */

namespace Yana\VDrive;

/**
 * Virtual Drive Directory
 *
 * class representing virtual directories
 *
 * @package    yana
 * @subpackage vdrive
 *
 * @ignore
 */
class Dir extends AbstractMountpoint
{

    /**
     * Note: this overwrites the variable of the parent class
     *
     * @var  string
     * @ignore
     */
    protected $type = "Dir";

    /**
     * Constructor.
     *
     * @param   string  $path   path
     */
    public function __construct($path)
    {
        assert(is_string($path), 'Invalid argument $path: string expected');
        $this->mountpoint = new \Yana\Files\Dir($path);
        $this->path = $path;
    }

    /**
     * Set directory's file filter.
     *
     * @param   string  $filter   directory's file filter
     * @return  self
     */
    public function setFilter($filter = "")
    {
        assert(is_string($filter), 'Invalid argument $filter: string expected');
        $this->mountpoint->setFilter($filter);
        return $this;
    }

    /**
     * Get directory's file filter.
     *
     * @return  string
     */
    public function getFilter()
    {
        return $this->mountpoint->getFilter();
    }

}

?>