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

namespace Yana\Core;

require_once __DIR__ . '/isobject.php';
require_once __DIR__ . '/iscloneable.php';
require_once __DIR__ . '/object.php';
require_once __DIR__ . '/autoloaders/ismapper.php';
require_once __DIR__ . '/iscountablearray.php';
require_once __DIR__ . '/iscollection.php';
require_once __DIR__ . '/abstractcollection.php';
require_once __DIR__ . '/autoloaders/mappercollection.php';
require_once __DIR__ . '/autoloaders/abstractmapper.php';
require_once __DIR__ . '/autoloaders/genericmapper.php';
require_once __DIR__ . '/autoloaders/lowercasedmapper.php';
require_once __DIR__ . '/autoloaders/directmapper.php';
require_once __DIR__ . '/autoloaders/isloader.php';
require_once __DIR__ . '/autoloaders/abstractloader.php';
require_once __DIR__ . '/autoloaders/loader.php';
require_once __DIR__ . '/autoloaders/wrapper.php';
require_once __DIR__ . '/autoloaders/classnotfoundexception.php';
require_once __DIR__ . '/../log/formatter/message.php';

/**
 * <<utility>> YANA Automatic class loader.
 *
 * @package     yana
 * @subpackage  core
 */
class AutoLoadBuilder extends \Yana\Core\Object
{

    const GENERIC_MAPPER = 0;
    const DIRECT_MAPPER = 1;
    const LOWERCASED_MAPPER = 2;

    /**
     * @var  \Yana\Core\Autoloaders\IsLoader
     */
    private $_loader = null;

    /**
     * Creates a new Auto-Loader and returns it.
     *
     * @return  \Yana\Core\Autoloaders\IsLoader
     */
    protected function _getLoader()
    {
        if (!isset($this->_loader)) {
            $this->_loader = new \Yana\Core\Autoloaders\Loader();
        }
        return $this->_loader;
    }

    /**
     * Creates and adds a selected class mapper.
     *
     * @param   int  $mapperType  one of the given class constants
     * @return  \Yana\Core\Autoloaders\IsMapper
     */
    public function addClassMapper($mapperType = self::GENERIC_MAPPER)
    {
        switch ($mapperType)
        {
            case self::DIRECT_MAPPER:
                $mapper = new \Yana\Core\Autoloaders\DirectMapper();
            break;
            case self::LOWERCASED_MAPPER:
                $mapper = new \Yana\Core\Autoloaders\LowerCasedMapper();
            break;
            case self::GENERIC_MAPPER:
            default:
                $mapper = new \Yana\Core\Autoloaders\GenericMapper();
        }
        $maps = $this->_getLoader()->getMaps();
        $maps[] = $mapper;
        return $mapper;
    }

    /**
     * Register the created auto-loader.
     */
    public function registerLoader()
    {
        $wrapper = new \Yana\Core\Autoloaders\Wrapper();
        $wrapper->registerAutoLoader($this->_getLoader());
    }

}

?>