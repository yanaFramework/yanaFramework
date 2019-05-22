<?php
/**
 * Yana Framework Library
 *
 * A library with various standard functions, which can be used by other plugins.
 *
 * {@translation
 *   de: Yana Framework Library
 *
 *       Eine Bibliothek mit verschiedenen Standardfunktionen, welche von anderen Plugins genutzt werden können.
 * }
 *
 * @menu       group: start, title: {lang id="sitemap_title"}
 * @author     Thomas Meyer
 * @type       primary
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @active     always
 *
 * @package    yana
 * @subpackage plugins
 */

namespace Plugins\DefaultLibrary;

/**
 * Default library for common functions
 *
 * This plugin is important. It provides functionality
 * that might be usefull for other plugins.
 *
 * @package    yana
 * @subpackage plugins
 */
class DefaultLibraryPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * Clear server's template-cache.
     *
     * @type        primary
     * @template    message
     * @user        group: admin, level: 100
     * @onsuccess   goto: index
     * @onerror     goto: index
     *
     * @access      public
     */
    public function clear_server_cache()
    {
        $this->_getApplication()->clearCache();
    }

    /**
     * Create preview message.
     *
     * Note: this function ends the program.
     *
     * @type        primary
     * @template    null
     *
     * @access      public
     * @param       string  $eintraege        text to be previewed
     * @param       bool    $is_ajax_request  is Ajax request
     */
    public function preview($eintraege, $is_ajax_request = false)
    {
        $sanitizedText = \Yana\Data\StringValidator::sanitize($eintraege, 0, \Yana\Data\StringValidator::USERTEXT);
        $formatter = new \Yana\Views\Helpers\Formatters\TextFormatterCollection();
        $formattedText = $formatter($sanitizedText);
        if ($is_ajax_request) {
            exit($formattedText);
        } else {
            $doc = $this->_getApplication()->getView()->createContentTemplate('id:blank');
            $content = '<div style="overflow: hidden; height: 100%;">' . $formattedText . '</div>';
            $doc->setVar('INSERT_CONTENT_HERE', $content);
            exit("$doc");
        }
    }

    /**
     * Checks syntax of embedded tags.
     *
     * Checks for correct syntax of embedded tags in all textarea fields.
     *
     * Note: this function ends the program.
     *
     * This functions produces a XML output, containing an array,
     * where index "error" contains an error number and index
     * "text" contains a description.
     *
     * If no error is found, the result is empty.
     *
     * List of error codes:
     * <ul>
     *  <li> 0 = no error </li>
     *  <li> 1 = unexpected start tag </li>
     *  <li> 2 = unknown or unhandled tag </li>
     *  <li> 3 = unexpected end tag </li>
     *  <li> 4 = missing an expected tag </li>
     *  <li> 5 = missing end tag </li>
     *  <li> 6 = unexpected line break </li>
     * </ul>
     *
     * @type        primary
     * @template    null
     *
     * @access      public
     * @param       array  $text  text to be checked
     */
    public function chkembtags(array $text)
    {
        $yana = $this->_getApplication();
        $language = $yana->getLanguage();
        $tags = 'b|i|u|emp|h|c|small|big|code|hide|php|mark|color|mail|img|url';
        $userTags = $yana->getVar('PROFILE.EMBTAG');
        if (is_array($userTags) && !empty($userTags)) {
            $tags .= '|' . mb_strtolower(implode('|', array_keys($userTags)));
        }
        /* get strings */
        $langError      = $language->getVar('TAGS.JS.ERR');
        $langChar       = $language->getVar('TAGS.JS.CHAR');
        $langEndTag     = $language->getVar('TAGS.JS.END');
        $langUnknownTag = $language->getVar('TAGS.JS.UNKNOWN');
        $langUnexpTag   = $language->getVar('TAGS.JS.UNEXP');
        $langExpTag     = $language->getVar('TAGS.JS.EXP');
        $langUnclTag    = $language->getVar('TAGS.JS.UNCL');
        $langBr         = $language->getVar('TAGS.JS.BR');
        $langProceed    = $language->getVar('TAGS.JS.PROCEED');

        /* check request */
        foreach ($text as $index => $val)
        {
            if (!is_string($val)) {
                continue;
            }
            $val = preg_replace('/\[w?br\]/', '', $val);
            $isTag = false;
            $isEndTag = false;
            $buffer = "";
            $tagList = array();
            $top = -1;
            $val = preg_match_all('/./u', $val, $vals);
            $val = $vals[0];

            for ($i = 0; $i < count($val); $i++)
            {
                $c = $val[$i];
                switch ($c)
                {
                    /**
                     * begin of tag
                     */
                    case '[':
                        if ($isTag) {
                            header('Content-type: text/xml');
                            header('Charset: utf-8');
                            $data = array('error' => 1, 'text' => $langError . " " . $langChar . " " . $i .
                                ".\n" . $langEndTag . " [" . $buffer . "].\n" . $langProceed) ;
                            exit(\Yana\Util\Hashtable::toXML($data));
                            unset ($data);
                        }
                        $isTag = true;
                    break;
                    /**
                     * end of tag
                     */
                    case '=':
                    case ']':
                        if ($isTag) {
                            if (!preg_match('/' . $tags . '/', $buffer)) {
                                header('Content-type: text/xml');
                                header('Charset: utf-8');

                                $data = array('error' => 2, 'text' => $langError . " " . $langChar . " " . $i .
                                    ".\n" . $langUnknownTag . " [" . $buffer . "].\n" . $langProceed);
                                exit(XMLenc\Yana\Util\Hashtable::toXMLode($data));
                                unset ($data);
                            } elseif ($isEndTag) {
                                if ($top < 0) {
                                    header('Content-type: text/xml');
                                    header('Charset: utf-8');
                                    $data = array('error' => 3, 'text' => $langError . " " . $langChar . " " . $i .
                                        ".\n" . $langEndTag . " [/" . $buffer . "]. " . $langUnexpTag . ".\n" .
                                        $langProceed);
                                    exit(\Yana\Util\Hashtable::toXML($data));
                                    unset ($data);
                                } elseif ($buffer == $tagList[$top]) {
                                    unset($tagList[$top--]);
                                } else {
                                    header('Content-type: text/xml');
                                    header('Charset: utf-8');
                                    $data = array('error' => 4, 'text' => $langError . " " . $langChar . " " . $i .
                                        ".\n" . $langEndTag . " [/" . $buffer . "]. " . $langExpTag .
                                        " [/" . $tagList[$top] . "].\n" . $langProceed);
                                    exit(\Yana\Util\Hashtable::toXML($data));
                                    unset ($data);
                                }
                                $isEndTag = false;
                            } else {
                                $tagList[++$top] = $buffer;
                            }
                            $isTag = false;
                        }
                        $buffer = "";
                    break;
                    /**
                     * is end tag
                     */
                    case '/':
                        if ($isTag) {
                            $isEndTag = true;
                        }
                    break;
                    /**
                     * character
                     */
                    default:
                        if ($isTag) {
                            $buffer .= $c;
                        }
                    break;
                }
            }
            if ($top > -1) {
                header('Content-type: text/xml');
                header('Charset: utf-8');
                $data = array('error' => 5, 'text' => $langError . ".\n" . $langUnclTag .
                    ": [" . implode('], [', $tagList) . "].\n" . $langProceed);
                exit(\Yana\Util\Hashtable::toXML($data));
                unset ($data);
            }
            if (preg_match('/^\s*(\[br\]|\n)/s', $text[$index])) {
                header('Content-type: text/xml');
                header('Charset: utf-8');
                $data = array('error' => 6, 'text' =>  $langError . ".\n" . $langBr . ".\n" . $langProceed);
                exit(\Yana\Util\Hashtable::toXML($data));
                unset ($data);
            }
        }
        exit(0);
    }

    /**
     * Show color-picker.
     *
     * @type        primary
     * @template    COLORPICKER_CONTENT
     *
     * @access      public
     */
    public function colorpicker()
    {
        // Just views template - no business logic required.
    }

}

?>