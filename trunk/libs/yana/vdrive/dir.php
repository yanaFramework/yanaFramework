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
 * @access     public
 * @package    yana
 * @subpackage vdrive
 *
 * @ignore
 */
class Dir extends Mountpoint
{

    /**
     * Note: this overwrites the variable of the parent class
     *
     * @access  protected
     * @var     string
     * @ignore
     */
    protected $type = "Dir";

    /**
     * constructor
     *
     * @access  public
     * @param   string  $path   path
     */
    public function __construct($path)
    {
        assert('is_string($path); // Wrong type for argument 1. String expected');
        $this->mountpoint = new \Dir($path);
        $this->path = $path;
    }

    /**
     * set directory's file filter
     *
     * @access  public
     * @param   string  $filter   directory's file filter
     */
    public function setFilter($filter = "")
    {
        assert('is_string($filter); // Wrong type for argument 3. String expected');
        $this->mountpoint->setFilter($filter);
    }

    /**
     * get directory's file filter
     *
     * @access  public
     * @return  string
     */
    public function getFilter()
    {
        return $this->mountpoint->getFilter();
    }

}

?>