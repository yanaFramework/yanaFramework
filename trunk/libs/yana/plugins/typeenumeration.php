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
 * <<Enumeration>> Plugin type.
 *
 * The type depicts the semantic of a plugin.
 *
 * @access      public
 * @package     yana
 * @subpackage  plugins
 * @ignore
 */
class TypeEnumeration extends \Yana\Core\AbstractEnumeration
{

    /**
     * No special behavior expected.
     */
    const DEFAULT_SETTING = 'default';
    /**
     * Is primary application, that may have derived plugins,
     * but should have no parent.
     */
    const PRIMARY = 'primary';
    /**
     * Sub-plugin that configurates the behavior of other plugins.
     */
    const CONFIGURATION = 'config';
    /**
     * Method or plugin that may only read, but not write to data sources.
     */
    const READ = 'read';
    /**
     * Method or plugin that may write and (where needed) read on data sources.
     * It is expected to store data, but not do anything else.
     */
    const WRITE = 'write';
    /**
     * Sub-plugin that evaluates access restrictions to other plugins.
     */
    const SECURITY = 'security';
    /**
     * Third-party plugin, that does nothing by itself and is instead used as a library for others.
     */
    const LIBRARY = 'library';

    /**
     * get enumeration item from string representation
     *
     * Every enumeration item has an equivalent string representation that can be used within
     * annotation inside a PHP doc block.
     *
     * @access  public
     * @static
     * @param   string  $string  text representation to convert
     * @return  string
     */
    public static function fromString($string)
    {
        assert('is_string($string)', ' Wrong type for argument 1. String expected');

        switch (mb_strtolower($string))
        {
            case 'config':
            case 'configuration':
                return self::CONFIGURATION;
            case 'library':
                return self::LIBRARY;
            case 'primary':
                return self::PRIMARY;
            case 'read':
                return self::READ;
            case 'write':
                return self::WRITE;
            case 'security':
                return self::SECURITY;
            default:
                return self::DEFAULT_SETTING;
        }
    }

}

?>