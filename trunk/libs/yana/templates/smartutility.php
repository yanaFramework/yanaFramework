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