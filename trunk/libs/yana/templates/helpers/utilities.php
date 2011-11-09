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
 * <<utility>> This utility class encapsulates extensions for HTML creation.
 *
 * @package     yana
 * @subpackage  templates
 */
class Utilities extends \Yana\Core\AbstractUtility
{
    /**
     * replace each token within a text/template
     *
     * NOTE: this method is case-sensitive
     *
     * @param   string   &$template      template
     * @param   array    &$array         array
     * @return  string
     */
    private static function _replace(&$template, array &$array)
    {
        assert('is_string($template); // Wrong type for argument 1. String expected');
        if (!is_string($template)) {
            return $template;
        }

        $ldimRegExp = YANA_LEFT_DELIMITER_REGEXP . '\$';
        $rdimRegExp = YANA_RIGHT_DELIMITER_REGEXP;
        $ldim = YANA_LEFT_DELIMITER . '$';
        $rdim = YANA_RIGHT_DELIMITER;

        $match = array();
        if (preg_match_all("/$ldimRegExp([\w_\.]+?)$rdimRegExp/", $template, $match) > 0) {
            $match = $match[1];
            foreach ($match as $currentMatch)
            {
                $tmp =& \Yana\Util\Hashtable::get($array, mb_strtoupper($currentMatch));
                /* if $tmp is NULL, the reference $match is pointing to a non-existing value */
                if (is_null($tmp)) {
                    continue;
                } elseif (!is_scalar($tmp)) {
                    $message = "The token '$currentMatch' refers to a non-scalar value inside a string contetx. ".
                        "It's value will be converted to the string 'true'.";
                    trigger_error($message, E_USER_NOTICE);
                    continue;
                } else {
                    $tmp = (string) $tmp;
                    /**
                     * if the content string we got from the reference array contains token as well,
                     * we recursivle replace them.
                     */
                    if (mb_strpos($tmp, $ldim) !== false) {
                        assert('is_string($tmp); // Unexpected result: $tmp is supposed to be a string');
                        self::_replace($tmp, $array);
                    }
                    assert('is_string($tmp); // Unexpected result: $tmp is supposed to be a string');
                    $regExpMatch = preg_quote($currentMatch, '/');
                    $template = preg_replace("/(<[^\!^>]+){$ldimRegExp}{$regExpMatch}{$rdimRegExp}([^>]+>)/Usi", '${1}'.
                        addcslashes(htmlspecialchars($tmp, ENT_COMPAT, 'UTF-8'), '\\') . '${2}', $template);
                    $template = str_replace($ldim . $currentMatch . $rdim, $tmp, $template);
                } // end if
            } // end for
        } // end if
        assert('is_string($template); // Unexpected result: $template is supposed to be a string');
    }

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
        self::_replace($string, $array);
        return $string;
    }

    /**
     * <<smarty modifier>> embeddedTags
     *
     * @access  public
     * @static
     * @param   string  $txt   text
     * @return  string
     */
    public static function embeddedTags($txt)
    {
        global $YANA;
        /*
         * error - invalid argument type 'txt' (string expected)
         */
        if (!is_string($txt)) {
            return $txt;
        }

        /*
         * if not necessary -> skip the whole section for better performance
         */
        $machtedTags = array();
        if (mb_strpos($txt, '[') !== false && preg_match_all('/(?<=\[)\w+/', $txt, $machtedTags)) {

            $offset = mb_strpos($txt, '[');

            /* remove duplicates */
            $machtedTags = array_unique($machtedTags[0]);

            $invalidResource = '<img title="the resource {$RESOURCE} was blocked because it contained illegal '.
                'characters" alt="invalid {$RESOURCE}" border="0" src="' . $YANA->getVar('DATADIR') . 'icon_x.gif"/>';

            foreach ($machtedTags as $tag)
            {
                switch ($tag)
                {
                    case 'br':
                    case 'wbr':
                        /* need to be replaced LAST - ignored here */
                    break;

                    /*
                     * apply default embedded tags
                     */
                    case 'b':
                    case 'i':
                    case 'u':
                    case 'emp':
                    case 'h':
                    case 'c':
                    case 'small':
                    case 'big':
                    case 'code':
                    case 'hide':
                        $pattern = "/\[$tag\](.*)(?:\[\/$tag\]|$)/Us";
                        $txt = preg_replace($pattern, '<span class="embtag_tag_' . $tag . '">${1}</span>', $txt);
                    break;

                    /*
                     * handle tag [php]
                     */
                    case 'php':
                        if (YANA_EMBTAG_ALLOW_PHP) {
                            $m = array();
                            while (preg_match('/\[php\](.*)(\[\/php\]|$)/Us', $txt, $m))
                            {
                                /*
                                 * This is to avoid double quoting , since highlight_string() will quote the
                                 * string again.
                                 */
                                $m[1] = html_entity_decode($m[1]);
                                $m[1] = '<span class="embtag_tag_code">' .
                                    highlight_string("<?php " . $m[1] . " ?>", true) . '</span>';
                                $txt  = str_replace($m[0], $m[1], $txt);
                            }
                            unset($m);
                        }
                    break;

                    /*
                     * handle tag [mark]
                     */
                    case 'mark':
                        $pattern = "/\[$tag\](.*)(?:\[\/$tag\]|$)/Us";
                        $txt = preg_replace($pattern, '<span class="embtag_tag_' . $tag . '">${1}</span>', $txt);
                        $txt = preg_replace(
                            "/\[mark=(\w+|\#[\da-fA-F]{3}|\#[\da-fA-F]{6})\](.*)(?:\[\/mark\]|$)/Us",
                            '<span class="embtag_tag_mark" style="background-color:${1}">${2}</span>',
                            $txt
                        );
                    break;

                    /*
                     * handle tag [color]
                     */
                    case 'color':
                        $pattern = "/\[$tag\](.*)(?:\[\/$tag\]|$)/Us";
                        $txt = preg_replace($pattern, '<span class="embtag_tag_' . $tag . '">${1}</span>', $txt);
                        $txt = preg_replace(
                            "/\[color=(\w+|\#[\da-fA-F]{3}|\#[\da-fA-F]{6})\](.*)(?:\[\/color\]|$)/Us",
                            '<span class="embtag_tag_color" style="color:${1}">${2}</span>',
                            $txt
                        );
                    break;

                    /*
                     * handle tag [mail]
                     */
                    case 'mail':
                        $txt = preg_replace("/\[mail=mailto:(.*)\]/Us", '[mail=${1}]', $txt);
                        $txt = preg_replace("/\[mail]mailto:(.*)\[\/mail\]/Us", '[mail]${1}[/mail]', $txt);
                        /**
                         * may contain word-/line- breaks that need to be removed
                         */
                        while (preg_match('/\[mail=[^\[\]]*\[wbr\]/i', $txt))
                        {
                            $txt = preg_replace('/(\[mail=[^\[\]]*)\[wbr\]/i', '${1}', $txt);
                        }
                        $pattern1 = "/\[mail=(.*)\](.*)\[\/mail\]/Usi";
                        $pattern2 = "/\[mail\](.*)\[\/mail\]/Ui";
                        $matches1 = $matches2 = array();
                        while (preg_match($pattern1, $txt, $matches1) || preg_match($pattern2, $txt, $matches2))
                        {
                            if (!empty($matches1)) {
                                $mailMatch =& $matches1[0];
                                $mailHref  =  $matches1[1];
                                $mailText  =  $matches1[2];
                            } elseif (!empty($matches2)) {
                                $mailMatch =& $matches2[0];
                                $mailHref  =  $matches2[1];
                                $mailText  =  $matches2[1];
                                $mailText  =  preg_replace('/ ?(\[wbr\]|\[br\])/', '', $mailText);
                                if (mb_strlen($mailText) > 40) {
                                    $mailText = mb_substr($mailText, 0, 34) . "..." . mb_substr($mailText, -3);
                                }
                            }
                            /*
                             * Count in the possibility of some nasty guy injecting white space characters
                             */
                            $mailHref = preg_replace('/[\x00-\x1f]/', '', $mailHref);
                            /*
                             * Although there should not be any tags - you never know ...
                             */
                            $mailHref = strip_tags($mailHref);
                            $mailHref = preg_replace('/ ?(\[wbr\]|\[br\])/', '', $mailHref);
                            $mailHref = preg_replace('/^mailto:/i', '', $mailHref);
                            $mailHref = filter_var($mailHref, FILTER_SANITIZE_EMAIL);
                            $mailHref = htmlspecialchars($mailHref, ENT_COMPAT, 'UTF-8');
                            if (!empty($mailHref)) {
                                $txt = str_replace(
                                    $mailMatch,
                                    '<a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;' . $mailHref .
                                        '" target="_blank">' . $mailText . '</a>',
                                    $txt);
                            } else {
                                $replace = str_replace('{$RESOURCE}', 'mail', $invalidResource);
                                $txt = str_replace($mailMatch, $replace, $txt);
                            }
                        } // end while
                    break;

                    /*
                     * handle tag [img]
                     */
                    case 'img':
                        while (preg_match("/\[img\](.*)\[\/img\]/U", $txt, $matches))
                        {
                            if (preg_match("/^[\w\d-_\/]+\.(png|jpg|gif|jpeg)$/i", $matches[1], $ext)) {
                                $strip_tags = strip_tags(preg_replace("/\[wbr\]/i", "", $matches[1]));
                                $htmlspecialchars = htmlspecialchars($strip_tags, ENT_COMPAT, 'UTF-8');
                                $replace = '<img alt="" border="0" src="' . $htmlspecialchars .
                                    '" style="max-width: 320px; max-height: 240px" onload="javascript:if'.
                                    '(this.width>320) { this.width=320; }; if(this.height>240) { this.height=240; };'.
                                    '"/>';
                                $txt = str_replace($matches[0], $replace, $txt);
                            } else {
                                $replace = str_replace('{$RESOURCE}', 'image', $invalidResource);
                                $txt = str_replace($matches[0], $replace, $txt);
                            }
                        } // end foreach
                    break;

                    /*
                     * handle tag [url]
                     *
                     * may contain word-/line- breaks that need to be removed
                     */
                    case 'url':
                        while (preg_match('/\[url=[^\[\]]*(?:\[wbr\]|\[br\])/si', $txt))
                        {
                            $txt = preg_replace('/(\[url=[^\[\]]*)(?:\[wbr\]|\[br\])/si', '${1}', $txt);
                        }
                        $pattern1 = "/\[url=(.*)\](.*)\[\/url\]/Usi";
                        $pattern2 = "/\[url\](.*)\[\/url\]/Ui";
                        while (preg_match($pattern1, $txt, $matches1) || preg_match($pattern2, $txt, $matches2))
                        {
                            if (!empty($matches1)) {
                                $uriMatch =& $matches1[0];
                                $uriHref  =  $matches1[1];
                                $uriText  =  $matches1[2];
                            } elseif (!empty($matches2)) {
                                $uriMatch =& $matches2[0];
                                $uriHref  =  $matches2[1];
                                $uriText  =  $matches2[1];
                                $uriText  =  preg_replace('/ ?(\[wbr\])/', '', $uriText);
                                if (mb_strlen($uriText) > 40) {
                                    $uriText = mb_substr($uriText, 0, 34) . "..." . mb_substr($uriText, -3);
                                }
                            }
                            /*
                             * Count in the possibility of some nasty guy injecting white space characters
                             */
                            $uriHref = preg_replace('/[\x00-\x1f]/', '', $uriHref);
                            /*
                             * Although there should not be any tags - you never know ...
                             */
                            $uriHref = strip_tags($uriHref);
                            $uriHref = preg_replace('/ ?(\[wbr\]|\[br\])/', '', $uriHref);
                            $uriHref = filter_var($uriHref, FILTER_SANITIZE_URL);
                            $uriHref = htmlspecialchars($uriHref, ENT_COMPAT, 'UTF-8');
                            if (!preg_match('/^[^:]+:/', $uriHref)) {
                                $uriHref = 'http://'.$uriHref;
                            } elseif (!preg_match('/^(https?:\/\/|ftp:\/\/)/', $uriHref)) {
                                $uriHref = '';
                            }
                            if (!empty($uriHref)) {
                                $replace = '<a href="' . $uriHref . '" target="_blank">' . $uriText . '</a>';
                                $txt = str_replace($uriMatch, $replace, $txt);
                            } else {
                                $replace = str_replace('{$RESOURCE}', 'uri', $invalidResource);
                                $txt = str_replace($uriMatch, $replace, $txt);
                            }
                            unset($uriMatch);
                            unset($uriHref);
                            unset($uriText);
                        } // end while
                    break;

                    /*
                     * load and apply embedded tags from system configuration
                     */
                    default:
                        if (isset($YANA)) {
                            assert('!isset($userTag); // Cannot redeclare var $userTag');
                            assert('!isset($opt); // Cannot redeclare var $opt');
                            assert('!isset($regExp); // Cannot redeclare var $regExp');
                            assert('!isset($replace); // Cannot redeclare var $replace');
                            $userTag = $YANA->getVar('PROFILE.EMBTAG');
                            if (is_array($userTag)) {
                                foreach ($userTag as $tagName => $opt)
                                {
                                    $tagName = mb_strtolower($tagName);
                                    if (is_array($opt)) {
                                        if (isset($opt[1])) {
                                            $regExp = '/' . $opt[1] . '/Us';
                                        } else {
                                            $regExp = "/\[$tagName\](.*)(?:\[\/$tagName\]|$)/Us";
                                        }
                                        if (isset($opt[2])) {
                                            $replace = htmlspecialchars_decode($opt[2]);
                                        } else {
                                            $replace = '<span class="embtag_tag_' . $tagName . '">$1</span>123';
                                        }
                                        $txt = preg_replace($regExp, $replace, $txt);

                                    } else {
                                        $message = "Ignored an invalid embedded tag. String expected, found '" .
                                            gettype($opt) . "' instead.";
                                        trigger_error($message, E_USER_NOTICE);
                                        continue;

                                    } // end if
                                } // end foreach
                                unset($opt, $tagName, $regExp, $replace);
                            } // end if
                            unset($userTag);
                        }
                    break;
                } // end switch
            } // end foreach
            unset($tag);

            /*
             * handle tag [br] (line break)
             */
            $txt = str_replace('[br]', '<br />', $txt);

            /*
             * handle tag [wbr] (word break)
             */
            $txt = str_replace('[wbr]', '&shy;', $txt);

        } // end if

        assert('is_string($txt); // Unexpected result: $txt is supposed to be a string.');
        return $txt;
    }

    /**
     * <<smarty modifier>> smilies
     *
     * @access  public
     * @static
     * @param   string  $txt    text
     * @return  string
     */
    public static function smilies($txt)
    {
        assert('empty($txt) || is_string($txt); // Wrong argument type. String expected found '.
            gettype($txt).' instead.');
        if (!is_string($txt)) {
            return $txt;
        }

        global $YANA;
        if (isset($YANA)) {
            $smilies = $YANA->getVar('SMILIES');
            if (empty($smilies)) {
                self::loadSmilies();
                $smilies = $YANA->getVar("SMILIES");
            }
            $smilies_dir = $YANA->getVar('PROFILE.SMILEYDIR');
        } else {
            global $smilies, $smilies_dir;
        }

        /* default smilies */
        if (isset($smilies) && is_array($smilies)) {
            if (in_array("smile", $smilies)) {
                $replacement = '<img alt=":-)" border="0" hspace="2" src="'.$smilies_dir.'smile.gif"/>';
                $txt = preg_replace("/:[\-oO]?\)/", $replacement, $txt);
            }
            if (in_array("teufelchen", $smilies)) {
                $replacement = '<img alt="&gt:-)" border="0" hspace="2" src="'.$smilies_dir.'teufelchen.gif"/>';
                $txt = preg_replace("/>:[\-oO]?\)/", $replacement, $txt);
            }
            if (in_array("boese", $smilies)) {
                $replacement = '<img alt=":-(" border="0" hspace="2" src="'.$smilies_dir.'boese.gif"/>';
                $txt = preg_replace("/:[\-oO]?\(/", $replacement, $txt);
            }
            if (in_array("keinemeinung", $smilies)) {
                $replacement = '<img alt=":-|" border="0" hspace="2" src="'.$smilies_dir.'keinemeinung.gif"/>';
                $txt = preg_replace("/:[\-oO]?\|/", $replacement, $txt);
            }
            if (in_array("lachen", $smilies)) {
                $replacement = '<img alt=":-D" border="0" hspace="2" src="'.$smilies_dir.'lachen.gif"/>';
                $txt = preg_replace("/:[\-oO]?D/", $replacement, $txt);
            }
            if (in_array("grinsen", $smilies)) {
                $replacement = '<img alt=";-)" border="0" hspace="2" src="'.$smilies_dir.'grinsen.gif"/>';
                $txt = preg_replace("/;[\-oO]\)/", $replacement, $txt);
            }
            if (in_array("zunge", $smilies)) {
                $replacement = '<img alt=":-P" border="0" hspace="2" src="'.$smilies_dir.'zunge.gif"/>';
                $txt = preg_replace("/:[\-oO]?P/", $replacement, $txt);
            }
            if (in_array("traurig", $smilies)) {
                $replacement = '<img alt=":*(" border="0" hspace="2" src="'.$smilies_dir.'traurig.gif"/>';
                $txt = preg_replace("/:[\'\*]\(/", $replacement, $txt);
            }
        }

        /* if not necessary -> skip the whole section for better performance */
        if (mb_strpos($txt, ':') !== false) {
            /* Emot-Codes */
            for ($j = 0; $j < count($smilies); $j++)
            {
                while (preg_match("/:".$smilies[$j].":(\s|\[wbr\]|\[br\]|<br \/>)*:".$smilies[$j].":/i", $txt))
                {
                    $pattern = "/:".$smilies[$j].":(\s|\[wbr\]|\[br\]|<br \/>)*:".$smilies[$j].":/i";
                    $txt = preg_replace($pattern, ':'.$smilies[$j].':', $txt);
                }
                $pattern = "/:".addcslashes($smilies[$j], "+()[]{}.?*/\\$^").":/";
                $replacement = '<img alt="" border="0" hspace="2" src="'.$smilies_dir.$smilies[$j].'.gif"/>';
                $txt = preg_replace($pattern, $replacement, $txt);
            }
        }

        assert('is_string($txt);');
        return $txt;
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
            'scripts/calendar/' .Language::getInstance()->getVar('calendar.js') . "'></script>";
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
     * <<smarty function>> lang
     *
     * Replace language strings
     *
     * @static
     * @access  public
     * @param   array $params  parameters
     * @return  string
     * @since   3.1.0
     */
    public static function lang(array $params)
    {
        global $YANA;
        if (isset($params['id'])) {
            $id = (string) $params['id'];
            return (string) Language::getInstance()->getVar($id);
        } else {
            trigger_error("Missing argument 'id' in function " . __FUNCTION__ . "()", E_USER_WARNING);
            return "";
        }
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
        $ul = '<ul class="gui_array_list">';
        foreach ($array as $key => $element)
        {
            /* print key */
            if (is_array($element)) {
                $ul .= '<li class="gui_array_head" onmouseover="this.className=\'gui_array_head_open\'" '.
                    'onmouseout="this.className=\'gui_array_head\'">';
                $ul .= '<span class="gui_array_key">';
                $ul .= htmlspecialchars($key, ENT_COMPAT, 'UTF-8');
                $ul .= '</span>';
            } else {
                $ul .= '<li class="gui_array_list">';
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
                    $ul .= '<span class="gui_array_value">'.
                        self::embeddedTags(htmlspecialchars($element, ENT_COMPAT, 'UTF-8')).'</span>';
                } else {
                    $ul .= '<span class="gui_array_value">' . $element . '</span>';
                }

            } elseif (is_scalar($element)) {
                $ul .= '<span class="gui_array_value">'.htmlspecialchars($element, ENT_COMPAT, 'UTF-8').'</span>';

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

    /**
     * <<smarty function>> guiSmilies
     *
     * @static
     * @access  public
     * @name    SmartUtility::guiSmilies()
     * @param   array   $params  accepts optional parameter "width"
     * @return  string
     *
     * @see     SmartUtility::loadSmilies()
     */
    public static function guiSmilies(array $params)
    {
        global $YANA;
        $table   = '<table summary="smilies" class="gui_generator_smilies"><tr>';
        if (isset($YANA)) {
            $title = $YANA->getLanguage()->getVar("TITLE_SMILIES");
            $smilies_dir = $YANA->getVar("PROFILE.SMILEYDIR");
            $smilies = $YANA->getVar("SMILIES");
        } else {
            global $smilies, $smilies_dir;
            $title = "EmotIcons";
        }
        $params['width'] = (int) $params['width'];
        if ($params['width'] < 1) {
            $params['width'] = 1;
        }

        if (empty($smilies)) {
            self::loadSmilies();
            if (isset($YANA)) {
                $smilies = $YANA->getVar("SMILIES");
            }
        }

        $dir = htmlspecialchars($smilies_dir, ENT_COMPAT, 'UTF-8');
        for ($j = 0; $j < count($smilies); $j++)
        {
            $text = htmlspecialchars($smilies[$j], ENT_COMPAT, 'UTF-8');
            $url = urlencode($smilies[$j]);
            if ($j % $params['width'] == 0 && $j > 0) {
                $table .= '</tr><tr>';
            }
            $table .= '<td><a title="'.$title.'" href="javascript://:'.$url.':"><img alt="'.$text.'" src="'.$dir.$text.
                '.gif" onmousedown="yanaAddIcon(\':'.$text.':\',event)"/></a></td>'."\n";
        }

        return $table."</tr></table>";
    }

    /**
     * load smilies
     *
     * Note: this function is called automatically by
     * the framework on start-up.
     * So there is no need to call it by yourself.
     *
     * @static
     * @access  public
     * @name    SmartUtility::loadSmilies()
     * @param   string  $user_dir (optional)
     *
     * @see     SmartUtility::guiSmilies()
     */
    public static function loadSmilies($user_dir = "./")
    {
        assert('is_string($user_dir); // Invalid argument $user_dir: string expected');
        assert('is_dir($user_dir); /* The value \'' . $user_dir . '\' is not a directory */');

        global $YANA;
        if (isset($YANA)) {
            $smilies_dir = $YANA->getVar('PROFILE.SMILEYDIR');
        } else {
            global $smilies, $smilies_dir;
            $smilies_dir = $user_dir;
        }

        if (is_dir($smilies_dir)) {
            if (empty($smilies) || is_null($smilies)) {
                $smilies = array();

                $dir = dir($smilies_dir);
                while ($txt = $dir->read())
                {
                    if (preg_match("/\.gif$/i", $txt)) {
                        $smilies[count($smilies)] = mb_substr($txt, 0, mb_strrpos($txt, "."));
                    }
                }
                $dir->close();
                sort($smilies);
            }

            if (isset($YANA)) {
                $YANA->setVar('SMILIES', $smilies);
            }
        } else {
            $message = "Unable to load smilies. The directory '{$smilies_dir}' does not exist.";
            trigger_error($message, E_USER_WARNING);
        }
    }

    /**
     * <<smarty function>> import
     *
     * Import another template.
     * This replaces Smarty's default import function 'include'.
     *
     * In opposite to 'include' this function allows the file parameter
     * to use a relative path and does not force the template designer
     * to work with absolute paths.
     *
     * @static
     * @access  public
     * @param   array   $params  parameters
     * @return  string
     */
    public static function import(array $params)
    {
        $filename = '';
        if (isset($params['file'])) {

            assert('$params["file"]; // Wrong argument type argument 1. String expected');
            $filename = $params['file'];

        } elseif (isset($params['id'])) {

            assert('is_string($params["id"]); // Wrong argument type argument 1. String expected');
            $filename = 'id:' . $params['id'];

        } else {
            trigger_error("Missing argument. You need to provide either the argument 'file' or 'id'.", E_USER_WARNING);
            return false;

        }

        $document = new SmartView($filename);
        unset($params['file']);
        if (count($params) > 0) {
            $document->setVarByReference('*', $params);
        }
        $document->setVar('FILE_IS_INCLUDE', true);

        return $document->__toString();
    }

}

?>