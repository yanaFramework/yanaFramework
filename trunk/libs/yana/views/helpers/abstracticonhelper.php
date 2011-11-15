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
 * <<abstract>> Loads and provides informations about registered, usable icons.
 *
 * @package     yana
 * @subpackage  views
 */
abstract class AbstractIconHelper extends \Yana\Core\Object
{

    /**
     * @var string
     */
    protected static $_dir = 'common_files/';

    /**
     * @var array
     */
    protected static $_icons = array();

    /**
     * Create a new instance.
     *
     * This also loads the configuration.
     */
    public function __construct()
    {
        global $YANA;
        if (isset($YANA)) {
            self::$_dir = $YANA->getVar('PROFILE.SMILEYDIR');
        }

        if (!is_dir(self::$_dir)) {
            $message = "Unable to load smilies. The directory '" . self::$_dir . "' does not exist.";
            throw new \Yana\Core\Exceptions\NotFoundException($message, E_USER_WARNING);
        }

        self::$_icons = array();
        foreach (glob(self::$_dir . '/*.gif') as $file)
        {
            self::$_icons[$file] = basename($file, '.gif');
        }
        unset($file);

        $configFile = self::$_dir . '/config.xml';
        if (is_file($configFile)) {
            foreach (simplexml_load_file($configFile) as $file)
            {
                self::$_icons[(string) $file['name']] = (string) $file['regex'];
            }
        }

        sort(self::$_icons);
    }

}

?>