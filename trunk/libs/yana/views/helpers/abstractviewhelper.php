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

namespace Yana\Views\Helpers;

/**
 * <<abstract>> Basic Helper class.
 *
 * @package     yana
 * @subpackage  views
 */
abstract class AbstractViewHelper extends \Yana\Core\Object
{

    /**
     * @var \Yana\Views\Manager
     */
    private $_manager = null;

    /**
     * Create a new instance.
     *
     * This also loads the configuration.
     */
    public function __construct(\Yana\Views\Manager $manager)
    {
        $this->_manager = $manager;
    }

    /**
     * Returns a reference to the registered view manager.
     *
     * You may use this to modify settings of the view layer and access the template engine.
     *
     * @return \Yana\Views\Manager
     */
    protected function _getViewManager()
    {
        return $this->_manager;
    }

}

?>