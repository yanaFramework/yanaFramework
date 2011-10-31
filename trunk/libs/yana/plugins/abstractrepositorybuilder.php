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

namespace Yana\Plugins;

/**
 * <<abstract>> Plugin configuration repository builder.
 *
 * This class produces a configuration repository by scanning a directory.
 *
 * @package     yana
 * @subpackage  plugins
 */
abstract class AbstractRepositoryBuilder extends \Yana\Core\Object
{

    /**
     * Plugin repository raw object.
     *
     * @var  \Yana\Plugins\Repository
     */
    protected $object = null;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->createNewRepository();
    }

    /**
     * Resets the instance that is currently build.
     */
    public function createNewRepository()
    {
        $this->object = new \Yana\Plugins\Repository();
    }

    /**
     * Build new repository.
     */
    abstract protected function buildRepository();

    /**
     * Returns the built object.
     *
     * @return  \Yana\Plugins\Configs\ClassConfiguration
     */
    public function getRepository()
    {
        $this->buildRepository();
        return $this->object;
    }

}

?>