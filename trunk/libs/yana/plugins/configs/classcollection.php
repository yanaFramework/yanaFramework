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
 * <<Collection>> Plugin configuration class collection.
 *
 * This class is a type-safe collection of instances of {@see \Yana\Plugins\Configs\ClassConfiguration}.
 *
 * @package     yana
 * @subpackage  plugins
 */
class ClassCollection extends \Yana\Core\AbstractCollection implements \Yana\Plugins\Configs\IsClassCollection
{

    /**
     * Unset item.
     *
     * @param   string  $offset  lower-cased method-name
     */
    public function offsetUnset($offset)
    {
        assert(is_string($offset), 'Invalid argument $offset: string expected');
        parent::offsetUnset(mb_strtolower($offset));
    }

    /**
     * Get item.
     *
     * @param   string  $offset  lower-cased method-name
     * @return  \Yana\Plugins\Configs\IsClassConfiguration
     */
    public function offsetGet($offset)
    {
        assert(is_string($offset), 'Invalid argument $offset: string expected');
        return parent::offsetGet(mb_strtolower($offset));
    }

    /**
     * Check if item exists.
     *
     * @param   scalar  $offset  index of item to test
     * @return  bool
     */
    public function offsetExists($offset)
    {
        assert(is_string($offset), 'Invalid argument $offset: string expected');
        return parent::offsetExists(mb_strtolower($offset));
    }

    /**
     * Insert or replace item.
     *
     * @param   string                                      $offset  ignored
     * @param   \Yana\Plugins\Configs\IsClassConfiguration  $value   newly added instance
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException
     * @return  \Yana\Plugins\Configs\IsClassConfiguration
     */
    public function offsetSet($offset, $value)
    {
        if ($value instanceof \Yana\Plugins\Configs\IsClassConfiguration) {
            if (!is_string($offset)) {
                $offset = \Yana\Plugins\PluginNameMapper::fromClassname($value->getClassName());
            }
            return $this->_offsetSet(mb_strtolower($offset), $value);
        } else {
            $message = "Instance of \Yana\Plugins\Configs\IsClassConfiguration expected. " .
                "Found " . gettype($value) . "(" . ((is_object($value)) ? get_class($value) : $value) . ") instead.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message);
        }
    }

    /**
     * Check if plugin is active by default.
     *
     * A plugin that is active by default cannot be deactivated via the configuration menu.
     *
     * Returns bool(true) if the plugin identified by $pluginName exists
     * and is active and bool(false) otherwise.
     *
     * @param   string  $className  identifier for the plugin
     * @return  bool
     */
    public function isActiveByDefault($className)
    {
        assert(is_string($className), 'Invalid argument $className: string expected');
        $isDefaultActive = false;
        if ($this->offsetExists($className)) {
            $isDefaultActive = $this->offsetGet($className)->isActiveByDefault();
        }
        return $isDefaultActive;
    }

}

?>