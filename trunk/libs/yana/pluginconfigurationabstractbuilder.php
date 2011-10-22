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
 */

/**
 * <<abstract>> Plugin configuration builder
 *
 * This class produces a configuration from a class reflection.
 *
 * @access      public
 * @name        PluginConfiguration
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 */
abstract class PluginConfigurationAbstractBuilder extends Object
{

    /**
     * Plugin configuration raw object.
     *
     * @var PluginConfigurationClass
     */
    protected $object = null;

    /**
     * constructor
     *
     * @access  public
     */
    public function __construct()
    {
        $this->createNewConfiguration();
    }

    /**
     * Resets the instance that is currently build.
     *
     * @access  public
     */
    public function createNewConfiguration()
    {
        $this->object = new PluginConfigurationClass();
    }

    /**
     * Build class object.
     *
     * @access protected
     * @abstract
     */
    abstract protected function buildClass();

    /**
     * Build method object.
     *
     * @access protected
     * @abstract
     */
    abstract protected function buildMethod();

    /**
     * Returns the built object.
     *
     * @access  public
     * @return  PluginConfigurationClass
     */
    public function getPluginConfigurationClass()
    {
        $this->buildClass();
        return $this->object;
    }

}

?>