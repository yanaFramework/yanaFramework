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

namespace Yana\Views\Helpers\Formatters;

/**
 * <<formatter>> This class encapsulates an extension for HTML creation.
 *
 * @package     yana
 * @subpackage  views
 */
class DateFormatter extends \Yana\Core\Object implements \Yana\Views\Helpers\IsFormatter
{

    /**
     * @var string
     */
    private static $_phpFormat = 'r';

    /**
     * @var string
     */
    private static $_javaScriptFormat = 'date.toLocaleString()';

    /**
     * Create a new instance.
     *
     * This also loads the configuration.
     */
    public function __construct()
    {
        $YANA = \Yana\Application::getInstance();
        if (isset($YANA)) {
            $profileTimeFormat = $YANA->getVar("PROFILE.TIMEFORMAT");
            if (!is_numeric($profileTimeFormat)) {
                $profileTimeFormat = 0;
            }
            $timeformat = $YANA->getVar("DATE." . $profileTimeFormat);
            assert('is_array($timeformat); // Time-format is expected to be an array.');
            unset($profileTimeFormat);
            self::setFormat($timeformat['PHP'], $timeformat['JS']);
        }
    }

    /**
     * Set default date format.
     *
     * @param  string  $phpFormat
     * @param  string  $javaScriptFormat 
     */
    public static function setFormat($phpFormat, $javaScriptFormat)
    {
        self::$_phpFormat = $phpFormat;
        self::$_javaScriptFormat = $javaScriptFormat;
    }

    /**
     * Get default formatting string for date() function.
     *
     * @return string
     */
    protected function _getPhpFormat()
    {
        return self::$_phpFormat;
    }

    /**
     * Get default formatting string for JavaScript Date() object.
     *
     * @return string
     */
    protected function _getJavaScriptFormat()
    {
        return self::$_javaScriptFormat;
    }

    /**
     * Create HTML from a unix timestamp.
     *
     * @param   int  $time  timestamp
     * @return  string
     */
    public function __invoke($time)
    {
        // provide javascript
        $script = '<script type="text/javascript" language="JavaScript">' .
            'date=new Date(' . $time . "000);document.write(" . $this->_getJavaScriptFormat() . ");</script>";

        // provide textual representation as a fallback
        $script .= '<span class="yana_noscript">' . date($this->_getPhpFormat(), $time) . '</span>';

        return $script;
    }

}

?>