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

namespace Yana\Forms;

/**
 * <<command>> For unit tests only.
 *
 * @package     yana
 * @subpackage  form
 * @ignore
 */
class NullBuilder extends \Yana\Forms\AbstractBuilder
{

    /**
     * <<magic>> Doesn't do anything, just returns an empty facade.
     *
     * @return  \Yana\Forms\Facade
     */
    public function __invoke()
    {
        return $this->_getFacade();
    }

    /**
     * Set name of database file.
     *
     * Schema the form is based upon.
     *
     * @param   string  $file  name of database file
     * @return  $this
     */
    public function setFile($file)
    {
        return $this->_setFile($file);
    }

    /**
     * Returns the cache adapter.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    public function getCache()
    {
        return $this->_getCache();
    }

    /**
     * Build \Yana\Db\Ddl\Form object.
     *
     * @return  \Yana\Db\Ddl\Form
     */
    protected function _buildForm()
    {
        return new \Yana\Db\Ddl\Form('form');
    }

}

?>