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
class TextFormatter extends \Yana\Views\Helpers\Formatters\AbstractFormatter
{

    /**
     * @var string
     */
    private static $_dataDir = null;

    /**
     * @var array
     */
    private static $_userDefinedTags = null;

    /**
     * Get directory where icon files are stored.
     *
     * @return  string
     * @codeCoverageIgnore
     */
    protected function _getDataDir()
    {
        if (!isset(self::$_dataDir)) {
            assert('!isset($builder); // Cannot redeclare var $builder');
            assert('!isset($application); // Cannot redeclare var $application');
            $builder = new \Yana\ApplicationBuilder();
            $application = $builder->buildApplication();
            unset($builder);
            self::$_dataDir = $application->getVar('DATADIR');
        }
        return self::$_dataDir;
    }

    /**
     * Get list of embedded tags.
     *
     * @return  array
     * @codeCoverageIgnore
     */
    protected function _getUserDefinedTags()
    {
        if (!isset(self::$_userDefinedTags)) {
            assert('!isset($builder); // Cannot redeclare var $builder');
            assert('!isset($application); // Cannot redeclare var $application');
            $builder = new \Yana\ApplicationBuilder();
            $application = $builder->buildApplication();
            unset($builder);
            self::$_userDefinedTags = $application->getVar('PROFILE.EMBTAG');
        }
        return self::$_userDefinedTags;
    }

    /**
     * Creates and images tag linking to "icon_x.gif".
     *
     * @return  string
     * @codeCoverageIgnore
     */
    protected function _buildTagForInvalidResource()
    {
        return '<img title="the resource {$RESOURCE} was blocked because it contained illegal ' .
                'characters" alt="invalid {$RESOURCE}" border="0" src="' . $this->_getDataDir() . 'icon_x.gif"/>';
    }

    /**
     * Converts emb.-tags
     *
     * @param   string  $string  HTML text
     * @return  string
     * @assert ('a[br]b') == 'a<br />b'
     * @assert ('a[wbr]b') == 'a&shy;b'
     * @assert ('a[i]bc') == 'a<span class="embtag_tag_i">bc</span>'
     * @assert ('a[i]b[/i]c') == 'a<span class="embtag_tag_i">b</span>c'
     * @assert ('a[u]b[/u]c') == 'a<span class="embtag_tag_u">b</span>c'
     * @assert ('a[emp]b[/emp]c') == 'a<span class="embtag_tag_emp">b</span>c'
     * @assert ('a[h]b[/h]c') == 'a<span class="embtag_tag_h">b</span>c'
     * @assert ('a[c]b[/c]c') == 'a<span class="embtag_tag_c">b</span>c'
     * @assert ('a[small]b[/small]c') == 'a<span class="embtag_tag_small">b</span>c'
     * @assert ('a[big]b[/big]c') == 'a<span class="embtag_tag_big">b</span>c'
     * @assert ('a[code]b[/code]c') == 'a<span class="embtag_tag_code">b</span>c'
     * @assert ('a[hide]b[/hide]c') == 'a<span class="embtag_tag_hide">b</span>c'
     * @assert ('a[mark=a]b[/mark]c') == 'a<span class="embtag_tag_mark" style="background-color:a">b</span>c'
     * @assert ('a[color=a]b[/color]c') == 'a<span class="embtag_tag_color" style="color:a">b</span>c'
     * @assert ('a[mail=mailto:a[wbr]@b.c]b[/mail]c') == 'a<a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;a@b.c" target="_blank">b</a>c'
     * @assert ('a[url]b[/url]c') == 'a<a href="http://b" target="_blank">b</a>c'
     * @assert ('a:[c](b)[/c]') == 'a:<span class="embtag_tag_c">(b)</span>'
     */
    public function __invoke($string)
    {
        assert('is_string($string); // Invalid argument $string: string expected');

        /*
         * if not necessary -> skip the whole section for better performance
         */
        $machtedTags = array();
        if (mb_strpos($string, '[') !== false && preg_match_all('/(?<=\[)\w+/', $string, $machtedTags)) {

            $offset = mb_strpos($string, '[');

            /* remove duplicates */
            $machtedTags = array_unique($machtedTags[0]);

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
                        $string = preg_replace($pattern, '<span class="embtag_tag_' . $tag . '">${1}</span>', $string);
                    break;

                    /*
                     * handle tag [php]
                     */
                    case 'php':
                        if (YANA_EMBTAG_ALLOW_PHP) {
                            $m = array();
                            while (preg_match('/\[php\](.*)(\[\/php\]|$)/Us', $string, $m))
                            {
                                /*
                                 * This is to avoid double quoting , since highlight_string() will quote the
                                 * string again.
                                 */
                                $m[1] = html_entity_decode($m[1]);
                                $m[1] = '<span class="embtag_tag_code">' .
                                    highlight_string("<?php " . $m[1] . " ?>", true) . '</span>';
                                $string  = str_replace($m[0], $m[1], $string);
                            }
                            unset($m);
                        }
                    break;

                    /*
                     * handle tag [mark]
                     */
                    case 'mark':
                        $pattern = "/\[$tag\](.*)(?:\[\/$tag\]|$)/Us";
                        $string = preg_replace($pattern, '<span class="embtag_tag_' . $tag . '">${1}</span>', $string);
                        $string = preg_replace(
                            "/\[mark=(\w+|\#[\da-fA-F]{3}|\#[\da-fA-F]{6})\](.*)(?:\[\/mark\]|$)/Us",
                            '<span class="embtag_tag_mark" style="background-color:${1}">${2}</span>',
                            $string
                        );
                    break;

                    /*
                     * handle tag [color]
                     */
                    case 'color':
                        $pattern = "/\[$tag\](.*)(?:\[\/$tag\]|$)/Us";
                        $string = preg_replace($pattern, '<span class="embtag_tag_' . $tag . '">${1}</span>', $string);
                        $string = preg_replace(
                            "/\[color=(\w+|\#[\da-fA-F]{3}|\#[\da-fA-F]{6})\](.*)(?:\[\/color\]|$)/Us",
                            '<span class="embtag_tag_color" style="color:${1}">${2}</span>',
                            $string
                        );
                    break;

                    /*
                     * handle tag [mail]
                     */
                    case 'mail':
                        $string = preg_replace("/\[mail=mailto:(.*)\]/Us", '[mail=${1}]', $string);
                        $string = preg_replace("/\[mail]mailto:(.*)\[\/mail\]/Us", '[mail]${1}[/mail]', $string);
                        /**
                         * may contain word-/line- breaks that need to be removed
                         */
                        while (preg_match('/\[mail=[^\[\]]*\[wbr\]/i', $string))
                        {
                            $string = preg_replace('/(\[mail=[^\[\]]*)\[wbr\]/i', '${1}', $string);
                        }
                        $pattern1 = "/\[mail=(.*)\](.*)\[\/mail\]/Usi";
                        $pattern2 = "/\[mail\](.*)\[\/mail\]/Ui";
                        $matches1 = $matches2 = array();
                        while (preg_match($pattern1, $string, $matches1) || preg_match($pattern2, $string, $matches2))
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
                                $string = str_replace(
                                    $mailMatch,
                                    '<a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;' . $mailHref .
                                        '" target="_blank">' . $mailText . '</a>',
                                    $string);
                            } else {
                                // @codeCoverageIgnoreStart
                                $replace = str_replace('{$RESOURCE}', 'mail', $this->_buildTagForInvalidResource());
                                $string = str_replace($mailMatch, $replace, $string);
                                // @codeCoverageIgnoreEnd
                            }
                        } // end while
                    break;

                    /*
                     * handle tag [img]
                     */
                    case 'img':
                        while (preg_match("/\[img\](.*)\[\/img\]/U", $string, $matches))
                        {
                            if (preg_match("/^[\w\d\-_\/]+\.(png|jpg|gif|jpeg)$/i", $matches[1], $ext)) {
                                $strip_tags = strip_tags(preg_replace("/\[wbr\]/i", "", $matches[1]));
                                $htmlspecialchars = htmlspecialchars($strip_tags, ENT_COMPAT, 'UTF-8');
                                $replace = '<img alt="" border="0" src="' . $htmlspecialchars .
                                    '" style="max-width: 320px; max-height: 240px" onload="javascript:if'.
                                    '(this.width>320) { this.width=320; }; if(this.height>240) { this.height=240; };'.
                                    '"/>';
                                $string = str_replace($matches[0], $replace, $string);
                            } else {
                                // @codeCoverageIgnoreStart
                                $replace = str_replace('{$RESOURCE}', 'image', $this->_buildTagForInvalidResource());
                                $string = str_replace($matches[0], $replace, $string);
                                // @codeCoverageIgnoreEnd
                            }
                        } // end foreach
                    break;

                    /*
                     * handle tag [url]
                     *
                     * may contain word-/line- breaks that need to be removed
                     */
                    case 'url':
                        while (preg_match('/\[url=[^\[\]]*(?:\[wbr\]|\[br\])/si', $string))
                        {
                            $string = preg_replace('/(\[url=[^\[\]]*)(?:\[wbr\]|\[br\])/si', '${1}', $string);
                        }
                        $pattern1 = "/\[url=(.*)\](.*)\[\/url\]/Usi";
                        $pattern2 = "/\[url\](.*)\[\/url\]/Ui";
                        while (preg_match($pattern1, $string, $matches1) || preg_match($pattern2, $string, $matches2))
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
                                $uriHref = 'http://' . $uriHref;
                            } elseif (!preg_match('/^(https?:\/\/|ftp:\/\/)/', $uriHref)) {
                                $uriHref = '';
                            }
                            if (!empty($uriHref)) {
                                $replace = '<a href="' . $uriHref . '" target="_blank">' . $uriText . '</a>';
                                $string = str_replace($uriMatch, $replace, $string);
                            } else {
                                // @codeCoverageIgnoreStart
                                $replace = str_replace('{$RESOURCE}', 'uri', $this->_buildTagForInvalidResource());
                                $string = str_replace($uriMatch, $replace, $string);
                                // @codeCoverageIgnoreEnd
                            }
                            unset($uriMatch, $uriHref, $uriText);
                        } // end while
                    break;

                    // load and apply embedded tags from system configuration
                    // @codeCoverageIgnoreStart
                    default:
                        assert('!isset($userTag); // Cannot redeclare var $userTag');
                        assert('!isset($opt); // Cannot redeclare var $opt');
                        assert('!isset($regExp); // Cannot redeclare var $regExp');
                        assert('!isset($replace); // Cannot redeclare var $replace');
                        foreach ((array) $this->_getUserDefinedTags() as $tagName => $opt)
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
                                $string = preg_replace($regExp, $replace, $string);

                            } else {
                                $message = "Ignored an invalid embedded tag. String expected, found '" .
                                    gettype($opt) . "' instead.";
                                \Yana\Log\LogManager::getLogger()->addLog($message, \Yana\Log\TypeEnumeration::INFO);
                                continue;

                            } // end if
                        } // end foreach
                        unset($opt, $tagName, $regExp, $replace);
                    break;
                    // @codeCoverageIgnoreEnd
                } // end switch
            } // end foreach
            unset($tag);

            /*
             * handle tag [br] (line break)
             */
            $string = str_replace('[br]', '<br />', $string);

            /*
             * handle tag [wbr] (word break)
             */
            $string = str_replace('[wbr]', '&shy;', $string);

        } // end if

        assert('is_string($string); // Unexpected result: $txt is supposed to be a string.');
        return $string;
    }

}

?>