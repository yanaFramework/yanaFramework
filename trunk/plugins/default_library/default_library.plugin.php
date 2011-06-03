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

/**
 * Default library for common functions
 *
 * This plugin is important. It provides functionality
 * that might be usefull for other plugins.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_default_library extends StdClass implements IsPlugin
{

    /**
     * Default event handler
     *
     * @access  public
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of params passed to the function
     * @return  bool
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * clear server's template-cache
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
        SmartTemplate::clearCache();
        PluginMenu::clearCache();
    }

    /**
     * create preview message
     *
     * Note: this function ends the program
     *
     * parameters taken:
     *
     * <ul>
     * <li> string eintraege    text to be previewed </li>
     * </ul>
     *
     * @type        primary
     * @template    null
     *
     * @access      public
     * @param       string  $eintraege        text to format
     * @param       bool    $is_ajax_request  is Ajax request
     */
    public function preview($eintraege, $is_ajax_request = false)
    {
        $eintraege = \Yana\Io\StringValidator::sanitize($eintraege, 0, \Yana\Io\StringValidator::USERTEXT);
        $eintraege = SmartUtility::smilies($eintraege);
        $eintraege = SmartUtility::embeddedTags($eintraege);
        if ($is_ajax_request) {
            exit($eintraege);
        } else {
            $doc = new SmartTemplate('id:blank');
            $content = '<div style="overflow: hidden; height: 100%;">' . $eintraege . '</div>';
            $doc->setVar('INSERT_CONTENT_HERE', $content);
            exit("$doc");
        }
    }

    /**
     * Check for correct syntax of embedded tags in all textarea fields.
     *
     * Note: this function ends the program
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
        $yana = Yana::getInstance();
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
                            exit(Hashtable::toXML($data));
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
                                exit(XMLencHashtable::toXMLode($data));
                                unset ($data);
                            } elseif ($isEndTag) {
                                if ($top < 0) {
                                    header('Content-type: text/xml');
                                    header('Charset: utf-8');
                                    $data = array('error' => 3, 'text' => $langError . " " . $langChar . " " . $i .
                                        ".\n" . $langEndTag . " [/" . $buffer . "]. " . $langUnexpTag . ".\n" .
                                        $langProceed);
                                    exit(Hashtable::toXML($data));
                                    unset ($data);
                                } elseif ($buffer == $tagList[$top]) {
                                    unset($tagList[$top--]);
                                } else {
                                    header('Content-type: text/xml');
                                    header('Charset: utf-8');
                                    $data = array('error' => 4, 'text' => $langError . " " . $langChar . " " . $i .
                                        ".\n" . $langEndTag . " [/" . $buffer . "]. " . $langExpTag .
                                        " [/" . $tagList[$top] . "].\n" . $langProceed);
                                    exit(Hashtable::toXML($data));
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
                exit(Hashtable::toXML($data));
                unset ($data);
            }
            if (preg_match('/^\s*(\[br\]|\n)/s', $text[$index])) {
                header('Content-type: text/xml');
                header('Charset: utf-8');
                $data = array('error' => 6, 'text' =>  $langError . ".\n" . $langBr . ".\n" . $langProceed);
                exit(Hashtable::toXML($data));
                unset ($data);
            }
        }
        exit(0);
    }

    /**
     * Create CAPTCHA-image.
     *
     * parameters taken:
     *
     * <ul>
     * <li> int security_image_index    index of image to display </li>
     * </ul>
     *
     * @type        primary
     * @template    null
     *
     * @access      public
     * @param       int  $security_image_index  id of index to check
     */
    public function security_get_image($security_image_index)
    {
        global $YANA;
        $imagesrc = $YANA->getVar("CONFIGDIR") . "security_image/security_image" . rand(0, 9) . ".png";
        $file = $YANA->getPlugins()->default_library->getResource('lib:/security.datfile');
        $contents = array();

        if (!$file->exists()) {
            $file->create();
        }
        $file->read();
        if (!$file->isEmpty()) {
            $contents = $file->getLine(0);
        }

        if (!isset($contents['TIME']) || $contents['TIME'] < time() || $contents['MAX_TIME'] < time()) {
            $contents = array();
            $contents['MAX_TIME'] = time() + 10000;
            for ($i=1;$i<10;$i++)
            {
                $contents["_$i"] = "";
                for ($j=0;$j<5;$j++)
                {
                    $letter = "";
                    // while letter is empty or black-listed
                    while (empty($letter) || in_array(mb_strtolower($letter), array('1', '0', 'o', 'l', 'i')))
                    {
                        switch (rand(0, 2))
                        {
                            case 0:
                                $letter = chr(rand(65, 90));
                            break;
                            case 1:
                                $letter = chr(rand(48, 57));
                            break;
                            case 2:
                                $letter = chr(rand(97, 122));
                            break;
                        }
                    }
                    $contents["_$i"] .= $letter;
                }
            }
        }
        $contents['TIME'] = time() + 1200;
        $file->setLine(0, $contents);
        $file->write();

        if ($security_image_index < 1 || $security_image_index > 9) {
            $text =& $contents['_1'];
        } else {

            $text =& $contents['_'.$security_image_index];
        }

        $image = new Image($imagesrc, 'png');
        for ($i = 0; $i < 5; $i++)
        {
            $image->drawString(
                $text[$i],             // Text
                4+($i*9)+rand(0, 1),   // x
                1+rand(-1, 1),         // y
                $image->getColor(      // color (palette index number)
                    40+rand(-30, 60),  // r
                    40+rand(-30, 60),  // g
                    40+rand(-30, 60)   // b
                ),
                5                      // font size
            );
        }
        $image->outputToScreen();
        exit(0);
    }

    /**
     * Test if a string matches the corresponding CAPTCHA.
     *
     * parameters taken:
     *
     * <ul>
     * <li> int security_image_index    index of image to display </li>
     * <li> string security_image       text to compare with CAPTCHA </li>
     * </ul>
     *
     * @type        primary
     * @template    null
     *
     * @access      public
     * @param       int     $security_image_index  id of index to check
     * @param       string  $security_image        user-entered text
     * @return      bool
     */
    public function security_check_image($security_image_index, $security_image)
    {
        global $YANA;

        $permission = $YANA->getVar("PERMISSION");
        if (is_int($permission) && $permission > 0) {
            return true;
        }

        $file = $YANA->getPlugins()->default_library->getResource('lib:/security.datfile');
        $file->read();
        if (!$file->isEmpty()) {
            $contents = $file->getLine(0);
        } else {
            $contents = array();
        }

        if ($contents['MAX_TIME'] < time()) {
            return false;
        } else {
            if ($security_image_index < 1 || $security_image_index > 9) {
                $text =& $contents['_1'];
            } else {
                $text =& $contents['_'.$security_image_index];
            }
            return (bool) (!empty($text) && ($security_image == $text));
        }
    }

    /**
     * Create a microsummary for a page.
     *
     * Note: this function terminates program execution.
     *
     * Semantic description:
     *
     * "Microsummaries" are a Firefox 2.0 feature that allows users
     * to create dynamic bookmark titles that automatically updates
     * when the content of the bookmarked page changes.
     *
     * Have a look at what microsummaries can be:
     * <ul>
     *   <li> the numbers of downloads of a file on a download site </li>
     *   <li> the latest news on a news page </li>
     *   <li> current number of unread e-mail in the inbox of a webmail service </li>
     *   <li> current total of donations to a project </li>
     *   <li> the date of latest updates on a database </li>
     *   <li> the latest submission to a guestbook or forum </li>
     *   <li> the number of visitors currently online in a chat room </li>
     *   <li> the latest stock values aso. </li>
     * </ul>
     *
     * Examples of usage:
     * <ol>
     *  <li> Setting a microsummary from a plugin:
     *       <code>Microsummary::setText($id, 'Summary text');</code>
     *  </li>
     *  <li> Retrieving a microsummary in a plugin:
     *       <code>$microsummary = Microsummary::getText($id);</code>
     *  </li>
     *  <li> To indicate that a microsummary exists for your plugin
     *       add this as the last line in your plugin constructor
     *       <code>Microsummary::publishSummary($id);</code>
     *  </li>
     *  <li> Calling a microsummary from a browser:
     *       <code>index.php?action=get_microsummary&target=guestbook</code>
     *       (where 'guestbook' is the name of the plugin)
     *  </li>
     * </ol>
     * Note: you may want to use the name of your plugin as value for $id.
     *
     * @type        read
     * @template    null
     *
     * @access      public
     * @param       string  $target  identifies summary to get
     */
    public function get_microsummary($target)
    {
        if (empty($target)) {
            exit('Error: illegal request');
        }
        $microsummary = Microsummary::getText($target);
        if (empty($microsummary)) {
            exit('No summary available');
        }
        print $microsummary;
        exit(0);
    }

    /**
     * Shows a color-picker.
     *
     * @type        primary
     * @template    colorpicker
     *
     * @access      public
     */
    public function colorpicker()
    {
        // Just views template - no business logic required.
    }

    /**
     * Shows a map of several color value.
     *
     * @type        primary
     * @template    colormap
     *
     * @access      public
     */
    public function colormap()
    {
        // Just views template - no business logic required.
    }

}

?>