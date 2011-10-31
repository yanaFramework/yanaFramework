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

namespace Yana\Plugins\Configs;

/**
 * <<abstract>> Plugin configuration builder
 *
 * This class produces a configuration from a class reflection.
 *
 * @name        AbstractBuilder
 * @package     yana
 * @subpackage  plugins
 *
 * @ignore
 */
abstract class AbstractBuilder extends \Yana\Core\Object
{

    /**
     * Plugin configuration raw object.
     *
     * @var \Yana\Plugins\Configs\ClassConfiguration
     */
    protected $object = null;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->createNewConfiguration();
    }

    /**
     * Resets the instance that is currently build.
     */
    public function createNewConfiguration()
    {
        $this->object = new \Yana\Plugins\Configs\ClassConfiguration();
    }

    /**
     * Build class object.
     */
    abstract protected function buildClass();

    /**
     * Build method object.
     */
    abstract protected function buildMethod();

    /**
     * Returns the built object.
     *
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function getPluginConfigurationClass()
    {
        $this->buildClass();
        return $this->object;
    }

}

?>