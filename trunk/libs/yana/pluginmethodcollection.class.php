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
 * <<Collection>> Plugin configuration method collection.
 *
 * This class is a type-safe collection of instances of {@see PluginConfigurationMethod}.
 *
 * @access      public
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 */
class PluginMethodCollection extends Collection
{

    /**
     * Unset item.
     *
     * @access  public
     * @param   string  $offset  lower-cased method-name
     */
    public function offsetUnset($offset)
    {
        assert('is_string($offset); // Invalid argument $offset: string expected');
        parent::offsetUnset(mb_strtolower($offset));
    }

    /**
     * Get item.
     *
     * @access  public
     * @param   string  $offset  lower-cased method-name
     */
    public function offsetGet($offset)
    {
        assert('is_string($offset); // Invalid argument $offset: string expected');
        return parent::offsetGet(mb_strtolower($offset));
    }

    /**
     * Insert or replace item.
     *
     * @access  public
     * @param   string                     $offset  ignored
     * @param   PluginConfigurationMethod  $value   newly added instance
     * @throws  InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        if ($value instanceof PluginConfigurationMethod) {
            if (!is_string($offset)) {
                $offset = $value->getMethodName();
            }
            assert('is_string($offset); // Invalid argument $offset: string expected');
            parent::offsetSet(mb_strtolower($offset), $value);
        } else {
            $message = "Instance of PluginConfigurationMethod expected. " .
                "Found " . gettype($value) . "(" . get_class($value) . ") instead.";
            throw new InvalidArgumentException($message);
        }
    }

}

?>