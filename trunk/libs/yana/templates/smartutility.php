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

/**
 * <<utility>> SmartUtility
 *
 * This is a utility class. It encapsulates extensions to use with
 * the smarty temlate engine.
 *
 * This is also a global namespace for layout specific functions.
 * These functions implement recursive replacement of tokens.
 * This functionality is used for registry-files (sml/config)
 * of the framework.
 *
 * {@internal
 *
 * Additional smarty functions and modifiers are documented elsewhere.
 * These functions should be ignored in API-documentation.
 *
 * }}
 *
 * @static
 * @access      public
 * @package     yana
 * @subpackage  core
 * @ignore
 */
class SmartUtility extends \Yana\Core\AbstractUtility
{

    /**
     * <<smarty modifier>> replace token
     *
     * Replace a token within a provided text.
     *
     * Example:
     * <code>
     * // assume the token {$foo} is set to 'World'
     * $text = 'Hello {$foo}.';
     * // prints 'Hello World.'
     * print SmartUtility::replaceToken($string);
     * </code>
     *
     * NOTE: this method is case-insensitive
     *
     * This means if your reference array contains
     * keys with the same name but different writing,
     * e.g. 'a' and 'A', the keys that last of the both
     * keys is used and the other is ignored.
     *
     * Also note that a non-existing value in the reference array will
     * be left alone and not be replaced.
     * So you can call this function multiple times with different arrays
     * to implement a simple fallback behaviour.
     *
     * @static
     * @access  public
     * @param   string  $string   string
     * @param   array   $array    array
     * @return  string
     */
    public static function replaceToken($string, array $array = array())
    {
        /*
         * This function may be used as a default modifier.
         * If so, then the input may be anything and does not necessarily need
         * to be a string value. We need to skip these values.
         */
        if (!is_string($string)) {
            return $string;
        }
        /*
         * For better performance we skip all values that do not need to be
         * replaced.
         */
        if (mb_strpos("$string", YANA_LEFT_DELIMITER) === false) {
            return $string;
        }
        /*
         * If the input array is empty, we use the default
         */
        if (empty($array)) {

            $registry = \Yana\VDrive\Registry::getGlobalInstance();
            if ($registry instanceof \Yana\VDrive\Registry) {
                $array = $registry->getVar();
            }

        }
        /*
         * Replace all entities of array values in given string.
         */
        \Yana\Util\String::replaceToken($string, $array);
        return $string;
    }

    /**
     * <<smarty function>> select date
     *
     * <pre>
     * This function takes the following arguments:
     *
     * string  $name  (mandatory) name attribute of select element
     * string  $attr  (optional)  list of attributes for select element
     * string  $id    (optional)  id attribute of select element
     * array   $time  (optional)  selected timestamp
     * </pre>
     *
     * @access  public
     * @static
     * @param   array   $params     parameters
     * @return  string
     */
    public static function selectDate(array $params)
    {
        if (empty($params['name'])) {
            return "";
        } else {
            $name = (string) $params['name'];
        }
        $id = "";
        if (!empty($params['id'])) {
            $id = (string) $params['id'];
        }
        $attr = "";
        if (!empty($params['attr'])) {
            $attr = (string) $params['attr'];
        }
        // rename results from getdate()
        if (isset($params['time']['mon'])) {
            $params['time']['month'] = $params['time']['mon'];
        }
        if (isset($params['time']['mday'])) {
            $params['time']['day'] = $params['time']['mday'];
        }
        // get timestamp
        switch (true)
        {
            case empty($params['time']) || !is_array($params['time']):
            case !isset($params['time']['day']):
            case !isset($params['time']['month']):
            case !isset($params['time']['year']):
                // use current timestamp if no value provided
                $day = (int) date('j');
                $month = (int) date('n');
                $year = (int) date('Y');
            break;
            default:
                $day = (int) $params['time']['day'];
                $month = (int) $params['time']['month'];
                $year = (int) $params['time']['year'];
            break;
        }

        // calendar icon
        $icon = $GLOBALS['YANA']->getVar('DATADIR') . 'calendar.gif';

        // returns "<select day><select month><select year><icon>"
        return self::_generateSelect("{$id}_day", $attr, "{$name}[day]", 1, 31, $day) .
            self::_generateSelect("{$id}_month", $attr, "{$name}[month]", 1, 12, $month) .
            self::_generateSelect("{$id}_year", $attr, "{$name}[year]", $year - 5, $year + 5, $year) .
            '<script type="text/javascript">yanaAddCalendar("' . $icon . '", "' . $id . '", "' . $id . '_year", ' .
            $day . ', ' . ($month - 1) . ', ' . $year . ');</script>'.
            '<script type="text/javascript" src=\'' . Skin::getSkinDirectory('default') .
            'scripts/calendar/' . Language::getInstance()->getVar('calendar.js') . "'></script>";
    }

    /**
     * <<smarty function>> select time
     *
     * <pre>
     * This function takes the following arguments:
     *
     * string  $name  (mandatory) name attribute of select items
     * string  $attr  (optional)  list of attributes for select element
     * string  $id    (optional)  id attribute of select element
     * int     $time  (optional)  selected timestamp
     * </pre>
     *
     * @access  public
     * @static
     * @param   array   $params     parameters
     * @return  string
     */
    public static function selectTime(array $params)
    {
        if (empty($params['name'])) {
            return "";
        } else {
            $name = (string) $params['name'];
        }
        $id = "";
        if (!empty($params['id'])) {
            $id = (string) $params['id'];
        }
        $attr = "";
        if (!empty($params['attr'])) {
            $attr = (string) $params['attr'];
        }
        // rename results from getdate()
        if (isset($params['time']['hours'])) {
            $params['time']['hour'] = $params['time']['hours'];
        }
        if (isset($params['time']['minutes'])) {
            $params['time']['minute'] = $params['time']['minutes'];
        }
        // get timestamp
        switch (true)
        {
            case empty($params['time']):
            case !isset($params['time']['hour']):
            case !isset($params['time']['minute']):
                // use current timestamp if no value provided
                $hour = (int) date('H');
                $minute = (int) date('i');
            break;
            default:
                $hour = (int) $params['time']['hour'];
                $minute = (int) $params['time']['minute'];
            break;
        }

        // returns "<select hour>:<select minute>"
        return self::_generateSelect("{$id}_hour", $attr, "{$name}[hour]", 0, 23, $hour) . ':' .
            self::_generateSelect("{$id}_minute", $attr, "{$name}[minute]", 0, 59, $minute);
    }

    /**
     * generate select
     *
     * @access  private
     * @static
     * @param   string  $id        value of id attribute
     * @param   string  $attr      list of attributes for select element
     * @param   string  $name      value of name attribute
     * @param   int     $start     first value
     * @param   int     $end       last value
     * @param   int     $selected  selected value
     * @return  string
     */
    private static function _generateSelect($id, $attr, $name, $start, $end, $selected = null)
    {
        if (!empty($id)) {
            $id = "id=\"$id\" ";
        }
        $result = '<select ' . $id . $attr . ' name="' . $name . '">';
        for ($i = $start; $i <= $end; $i++)
        {
            $result .= '<option value="' . $i .
                ( ($i === $selected) ? '" selected="selected">' : '">' ) .
                ( ($i < 10) ? "0{$i}" : $i ) .
                '</option>';
        } // end for
        $result .= '</select>';
        return $result;
    }

    /**
     * <<smarty function>> toolbar
     *
     * Creates the toolbar and outputs it.
     *
     * @static
     * @access  public
     * @return  int
     * @since   3.1.0
     */
    public static function toolbar()
    {
        $menu = \Yana\Plugins\Menu::getInstance();
        return self::printUL3($menu->getTextMenu(), true);
    }

    /**
     * <<smarty function>> print unordered list
     *
     * Print an array using a tree menu.
     *
     * The following CSS classes are used:
     * <ul>
     *  <li> ul.gui_array_list </li>
     *  <li> ul.gui_array_list &gt; li.gui_array_list </li>
     *  <li> ul.gui_array_list &gt; li.gui_array_head </li>
     *  <li> ul.gui_array_list &gt; li.gui_array_list &gt; span.gui_array_key </li>
     *  <li> ul.gui_array_list &gt; li.gui_array_list &gt; span.gui_array_value </li>
     *  <li> ul.gui_array_list &gt; li.gui_array_head &gt; span.gui_array_key </li>
     *  <li> ul.gui_array_list &gt; li.gui_array_head &gt; span.gui_array_value </li>
     * </ul>
     *
     * This function takes the following arguments:
     * <ul>
     *  <li> array  $value         list contents (possibly multi-dimensional) </li>
     *  <li> bool   $allow_html    allow HTML values (defaults to false) </li>
     *  <li> bool   $keys_as_href  convert array keys to links (defaults to false) </li>
     *  <li> int    $layout        you may choose between layout 1 through 3 (defaults to 1) </li>
     * </ul>
     *
     * If you set the argument 'keys_as_href' to true,
     * the tree menu will create links with the array
     * values as link text and the array keys as hrefs.
     * Links are only created if the value is scalar,
     * which means you may create sub-menues by using
     * another array as the value.
     *
     * @access  public
     * @static
     * @param   array  $params  parameters
     * @return  string
     */
    public static function printUnorderedList(array $params)
    {
        /* argument $value */
        if (!isset($params['value'])) {
            return "";
        } else {
            $array = $params['value'];
        }

        /* argument $keysAsHref */
        if (empty($params['keys_as_href'])) {
            $keysAsHref = 0;
        } else {
            $keysAsHref = 1;
        }

        /* argument $allowHtml */
        if (empty($params['allow_html'])) {
            $allowHtml = false;
        } else {
            $allowHtml = true;
        }

        /* argument $layout */
        if (empty($params['layout'])) {
            $layout = 1;
        } else {
            $layout = (int) $params['layout'];
        }

        /* implementation */
        if (is_array($array)) {
            switch ($layout)
            {
                case 1:
                    return self::printUL1($array, $keysAsHref, $allowHtml);
                break;
                case 2:
                    return self::printUL2($array, $keysAsHref, $allowHtml);
                break;
                case 3:
                    return self::printUL3($array, $keysAsHref, $allowHtml);
                break;
                default:
                    return self::printUL1($array, $keysAsHref, $allowHtml);
                break;
            }
        } else {
            return $array;
        }
    }

    /**
     * Alias of SmartUtility::printUL1()
     *
     * @see  SmartUtility::printUL1()
     * @param   array   $array      data
     * @param   int     $keys       convert keys to href
     * @param   bool    $allowHtml  allow HTML values
     * @ignore
     */
    public static function printUL(array $array, $keys = 0, $allowHtml = false)
    {
        self::printUL1($array, $keys, $allowHtml);
    }

    /**
     * <<smarty modifier>> print unordered list
     *
     * This implements layout #1.
     *
     * called by SmartUtility::printUnorderedList()
     *
     * - currently not used as a modifier
     *
     * @access  public
     * @static
     * @param   array   $array      data
     * @param   int     $keys       convert keys to href
     * @param   bool    $allowHtml  allow HTML values
     * @return  string
     * @ignore
     */
    public static function printUL1(array $array, $keys = 0, $allowHtml = false)
    {
        if (isset($GLOBALS['YANA'])) {
            $dir = $GLOBALS['YANA']->getVar('DATADIR');
        } else {
            $dir = 'common_files/';
        }
        $textFormatter = new \Yana\Templates\Helpers\Formatters\TextFormatter();
        $ul = '<ul class="gui_array_list">';
        foreach ($array as $key => $element)
        {
            /* print key */
            if (is_array($element)) {
                $ul .= '<li class="gui_array_head" onmouseover="this.className=\'gui_array_head_open\'" ' .
                    'onmouseout="this.className=\'gui_array_head\'">';
                $ul .= '<span class="gui_array_key">';
                $ul .= htmlspecialchars($key, ENT_COMPAT, 'UTF-8');
                $ul .= '</span>';
            } else {
                $ul .= '<li class="gui_array_list">';
                if ($keys === 2) {
                    /* intentionally left blank */
                } elseif ($keys == 1 && is_scalar($element)) {
                    $ul .= '<a href="' . htmlspecialchars($key, ENT_COMPAT, 'UTF-8') . '">';
                } else {
                    $ul .= '<span class="gui_array_key">';
                    if ($allowHtml) {
                        $ul .= $key;
                    } else {
                        $ul .= htmlspecialchars($key, ENT_COMPAT, 'UTF-8') . ':';
                    }
                    $ul .= '</span>';
                }
            }

            /* print value */
            if (is_bool($element)) {
                $ul .= '<span class="gui_array_value">';
                if ($element) {
                    $ul .= '<img alt="true" title="true" src="' . $dir . 'boolean_true.gif"/>';
                } else {
                    $ul .= '<img alt="false" title="false" src="' . $dir . 'boolean_false.gif"/>';
                }
                $ul .= '</span>';
            } elseif (is_string($element)) {
                if (!$allowHtml) {
                    $ul .= '<span class="gui_array_value">' .
                        $textFormatter(htmlspecialchars($element, ENT_COMPAT, 'UTF-8')) . '</span>';
                } else {
                    $ul .= '<span class="gui_array_value">' . $element . '</span>';
                }
            } elseif (is_scalar($element)) {
                $ul .= '<span class="gui_array_value">' . htmlspecialchars($element, ENT_COMPAT, 'UTF-8') . '</span>';
            } elseif (is_array($element)) {
                $ul .= self::printUL1($element, $keys, $allowHtml);
            } elseif (is_object($element)) {
                $ul .= '<span class="gui_array_value">' . htmlspecialchars((string) $element, ENT_COMPAT, 'UTF-8') . '</span>';
            } else {
                $ul .= '<span class="gui_array_value">' . htmlspecialchars(print_r($element, true), ENT_COMPAT, 'UTF-8') .
                    '</span>';
            }

            /* close open 'a' tag */
            if ($keys == 1 && is_scalar($element)) {
                $ul .= '</a>';
            }
            $ul .= '</li>';
        }
        $ul .= '</ul>';
        return $ul;
    }

    /**
     * <<smarty modifier>> print unordered list
     *
     * This implements layout #2.
     *
     * called by SmartUtility::printUnorderedList()
     *
     * - currently not used as a modifier
     *
     * @access  public
     * @static
     * @param   array   $array      data
     * @param   bool    $keys       convert keys to href
     * @param   bool    $allowHtml  allow HTML values
     * @param   bool    $isRoot     (true = root , false otherweise)
     * @return  string
     * @ignore
     */
    public static function printUL2(array $array, $keys = false, $allowHtml = false, $isRoot = true)
    {
        /* @var $dir string */
        $dir = "";
        /* @var $ul string */
        $ul = "";
        if (isset($GLOBALS['YANA'])) {
            $dir = $GLOBALS['YANA']->getVar('DATADIR');
        } else {
            $dir = 'common_files/';
        }
        if ($isRoot) {
            $ul = '<ul class="menu root">';
        } else {
            $ul = '<ul class="menu">';
        }
        foreach ($array as $key => $element)
        {
            /* print key */
            if (is_array($element)) {
                $ul .= '<li class="menu">';
                $ul .= '<div class="menu_head" onclick="yanaMenu(this)">' .
                    htmlspecialchars($key, ENT_COMPAT, 'UTF-8') . '</div>';
            } else {
                $ul .= '<li class="entry">';
                if ($keys === 2) {
                    /* intentionally left blank */
                } elseif ($keys == 1 && is_scalar($element)) {
                    $ul .= '<a href="'.htmlspecialchars($key, ENT_COMPAT, 'UTF-8').'">';
                } else {
                    $ul .= '<span class="gui_array_key">';
                    if ($allowHtml) {
                        $ul .= $key;
                    } else {
                        $ul .= htmlspecialchars($key, ENT_COMPAT, 'UTF-8').':';
                    }
                    $ul .= '</span>';
                }
            }

            /* print value */
            if (is_array($element)) {
                $ul .= self::printUL2($element, $keys, $allowHtml, false);
            } else {
                if ($keys == 0) {
                    $ul .= '<span class="gui_array_value">';
                }
                if (is_bool($element)) {
                    if ($element) {
                        $ul .= '<img alt="true" title="true" src="' . $dir . 'boolean_true.gif"/>';
                    } else {
                        $ul .= '<img alt="false" title="false" src="' . $dir . 'boolean_false.gif"/>';
                    }
                } elseif (is_scalar($element)) {
                    if ($allowHtml) {
                        $ul .= $element;
                    } else {
                        $ul .= htmlspecialchars($element, ENT_COMPAT, 'UTF-8');
                    }
                } else {
                    $ul .= htmlspecialchars((string) $element, ENT_COMPAT, 'UTF-8');
                }
                if ($keys == 0) {
                    $ul .= '</span>';
                }
            }

            /* close open 'a' tag */
            if ($keys == 1 && is_scalar($element)) {
                $ul .= '</a>';
            }
            $ul .= '</li>';
        }
        $ul .= '</ul>';
        return $ul;
    }

    /**
     * <<smarty modifier>> print unordered list
     *
     * This implements layout #3.
     *
     * called by SmartUtility::printUnorderedList()
     *
     * - currently not used as a modifier
     *
     * @access  public
     * @static
     * @param   array   $array      data
     * @param   bool    $keys       convert keys to href
     * @param   bool    $allowHtml  allow HTML values
     * @return  string
     * @ignore
     */
    public static function printUL3(array $array, $keys = false, $allowHtml = false)
    {
        if (isset($GLOBALS['YANA'])) {
            $dir = $GLOBALS['YANA']->getVar('DATADIR');
        } else {
            $dir = 'common_files/';
        }
        $ul = '<ul class="hmenu">';
        foreach ($array as $key => $element)
        {
            /* print key */
            if (is_array($element)) {
                $ul .= '<li onmouseover="yanaHMenu(this,true)" onmouseout="yanaHMenu(this,false)" class="hmenu">';
                $ul .= '<div class="menu_head">' . htmlspecialchars($key, ENT_COMPAT, 'UTF-8') . '</div>';
            } else {
                $ul .= '<li class="entry">';
                if ($keys === 2) {
                    /* intentionally left blank */
                } elseif ($keys == 1 && is_scalar($element)) {
                    $ul .= '<a href="'.htmlspecialchars($key, ENT_COMPAT, 'UTF-8').'">';
                } else {
                    $ul .= '<span class="gui_array_key">';
                    if ($allowHtml) {
                        $ul .= $key;
                    } else {
                        $ul .= htmlspecialchars($key, ENT_COMPAT, 'UTF-8').':';
                    }
                    $ul .= '</span>';
                }
            }

            /* print value */
            if (is_array($element)) {
                $ul .= self::printUL3($element, $keys, $allowHtml);
            } else {
                if ($keys == 0) {
                    $ul .= '<span class="gui_array_value">';
                }
                if (is_bool($element)) {
                    if ($element) {
                        $ul .= '<img alt="true" title="true" src="' . $dir . 'boolean_true.gif"/>';
                    } else {
                        $ul .= '<img alt="false" title="false" src="' . $dir . 'boolean_false.gif"/>';
                    }
                } elseif (is_scalar($element)) {
                    if ($allowHtml) {
                        $ul .= $element;
                    } else {
                        $ul .= htmlspecialchars($element, ENT_COMPAT, 'UTF-8');
                    }
                } else {
                    $ul .= htmlspecialchars(print_r((string) $element, true), ENT_COMPAT, 'UTF-8');
                }
                if ($keys == 0) {
                    $ul .= '</span>';
                }
            }

            /* close open 'a' tag */
            if ($keys == 1 && is_scalar($element)) {
                $ul .= '</a>';
            }
            $ul .= '</li>';
        }
        $ul .= '</ul>';
        return $ul;
    }

}

?>