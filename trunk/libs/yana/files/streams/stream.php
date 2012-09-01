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

namespace Yana\Files\Streams;

/**
 * <<facade>> Allows an easy to use API to work with stream wrappers.
 *
 * Example:
 * <code>
 * $stream = new Stream();
 * $stream->registerWrapper('null');
 * // or using procedural style
 * Stream::registerWrapper('null');
 * // No really - that's it!
 * // Now you can do the following
 * $contents = file_get_contents('null://path/file.ext');
 * </code>
 *
 * @package     yana
 * @subpackage  files
 */
class Stream extends \Yana\Core\Object
{

    /**
     * is a URL protocol
     */
    const IS_URL_WRAPPER = STREAM_IS_URL;

    /**
     * local stream protocol
     */
    const IS_LOCAL_WRAPPER = 0;

    /**
     * Register a class as new stream wrapper.
     *
     * @param   string  $protocolName  name of the stream protocol, e.g. 'file'
     * @param   string  $wrapperName   name of class in namespace, like \Yana\Files\Streams\Wrappers\($wrapperName)Wrapper
     * @param   int     $flags         either IS_URL_WRAPPER or IS_LOCAL_WRAPPER (default)
     * @return  bool
     */
    public static function registerWrapper($protocolName, $wrapperName = null, $flags = self::IS_LOCAL_WRAPPER)
    {
        if (empty($wrapperName)) {
            $wrapperName = $protocolName;
        }
        $className = (\class_exists($wrapperName)) ? $wrapperName : __NAMESPACE__ . '\\Wrappers\\' . $wrapperName . 'Wrapper';
        return \stream_wrapper_register($protocolName, $className, $flags);
    }

    /**
     * Protocol is registered.
     *
     * @param   string  $protocolName  name of the stream protocol, e.g. 'file'
     * @return  bool
     */
    public static function isRegistered($protocolName)
    {
        $wrappers = self::getWrappers();
        return \in_array($protocolName, $wrappers);
    }

    /**
     * Retrieve list of registered streams.
     *
     * @return  array
     */
    public static function getWrappers()
    {
        return \stream_get_wrappers();
    }

    /**
     * Restores a previously unregistered built-in wrapper.
     *
     * @param   string  $protocolName  name of the stream protocol, e.g. 'file'
     * @return  bool
     */
    public static function restoreWrapper($protocolName)
    {
        return \stream_wrapper_restore($protocolName);
    }

    /**
     * Unregister a URL wrapper.
     * 
     * @param   string  $protocolName  name of the stream protocol, e.g. 'file'
     * @return  bool
     */
    public static function unregisterWrapper($protocolName)
    {
        return \stream_wrapper_unregister($protocolName);
    }

}

?>