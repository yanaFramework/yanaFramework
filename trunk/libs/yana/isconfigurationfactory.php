<?php
/**
 * YANA library
 *
 * Primary controller class
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

namespace Yana;

/**
 * <<interface>> Configuration loader.
 *
 * Loads the system configuration from a file and returns it as an instance of XmlArray.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsConfigurationFactory
{

    /**
     * Load a system configuration file and return it as an object.
     *
     * The system config file contains default- and startup-settings
     * to initialize this class.
     *
     * @param   string  $filename  path to system.config
     * @return  \Yana\Util\Xml\IsObject
     */
    public function loadConfiguration($filename);

}

?>