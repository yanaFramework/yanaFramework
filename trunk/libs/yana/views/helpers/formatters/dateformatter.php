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
class DateFormatter extends \Yana\Views\Helpers\Formatters\AbstractFormatter
{

    /**
     * @var  string
     */
    private static $_phpFormat = '';

    /**
     * @var  string
     */
    private static $_javaScriptFormat = '';

    /**
     * Load and return configuration.
     *
     * @return  \Yana\Core\IsVarContainer
     * @codeCoverageIgnore
     */
    protected function _getConfiguration()
    {
        $builder = new \Yana\ApplicationBuilder();
        return $builder->buildApplication();
    }

    /**
     * Loads and sets the selected timeformat from the application profile.
     *
     * Sets self::$_javaScriptFormat with "date.toLocaleString()" as default.
     * Sets self::$_phpFormat with "r" as default.
     *
     * @return  array
     * @codeCoverageIgnore
     */
    protected function _loadDefaultDateFormat()
    {
        $varContainer = $this->_getConfiguration();
        $profileTimeFormat = (int) $varContainer->getVar("PROFILE.TIMEFORMAT");
        $timeformat = $varContainer->getVar("DATE." . $profileTimeFormat);
        assert('is_array($timeformat); // Time-format is expected to be an array.');

        self::$_javaScriptFormat = "date.toLocaleString()";
        if (isset($timeformat["JS"]) && is_string($timeformat["JS"])) {
            self::$_javaScriptFormat = (string) $timeformat["JS"];
        }

        self::$_phpFormat = "r";
        if (isset($timeformat["PHP"]) && is_string($timeformat["PHP"])) {
            self::$_phpFormat = (string) $timeformat["PHP"];
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
     * @return  string
     */
    protected function _getPhpFormat()
    {
        if (self::$_phpFormat === "") {
            $this->_loadDefaultDateFormat();
        }
        return self::$_phpFormat;
    }

    /**
     * Get default formatting string for JavaScript Date() object.
     *
     * @return  string
     */
    protected function _getJavaScriptFormat()
    {
        if (self::$_javaScriptFormat === "") {
            $this->_loadDefaultDateFormat();
        }
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