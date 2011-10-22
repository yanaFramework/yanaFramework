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
 * <<utility>> Smart HTML-processors
 *
 * This is a utility class that collects the standard HTML post-, pre- and output processors used by default
 * by the framework.
 *
 * These functions are registered when instantiating the Smarty Engine in the {@see SmartTemplate} class.
 *
 * @static
 * @access      public
 * @package     yana
 * @subpackage  core
 * @ignore
 */
class SmartHtmlProcessorUtility extends \Yana\Core\AbstractUtility
{

    /**
     * <<smarty processor>> htmlPostProcessor
     *
     * Adds an invisible dummy-field (honey-pot) to forms for spam protection.
     * If it's filled, it's a bot.
     *
     * @access  public
     * @static
     * @param   string  $source  HTML with PHP source code
     * @return  string
     * @ignore
     */
    public static function htmlPostProcessor($source)
    {
        assert('is_string($source); // Wrong type for argument 1. String expected');

        if (!YanaUser::isLoggedIn()) {
            $replace = "<span class=\"yana_button\"><input type=\"text\" name=\"yana_url\"/></span>\n</form>";
            $source = str_replace("</form>", $replace, $source);
        }

        return $source;
    }

    /**
     * <<smarty processor>> htmlPreProcessor
     *
     * @access  public
     * @static
     * @param   string                    $source         HTML source
     * @param   Smarty_Internal_Template  $templateClass  template class
     * @return  string
     * @ignore
     */
    public static function htmlPreProcessor($source, Smarty_Internal_Template $templateClass)
    {
        assert('is_string($source); // Wrong type for argument 1. String expected');
        global $YANA;

        /**
         * prohibit illegal functions
         *
         * 1) funcionality of 'include' function has been moved to 'import', so ignore the original
         * 2) deactivate 'include_php' for security reasons
         * 3) deactivate 'php' for security reasons
         */
         $pattern = "/" . YANA_LEFT_DELIMITER_REGEXP . "(?:include(?:_php)?|php)? .*" .
            YANA_RIGHT_DELIMITER_REGEXP ."/Usi";
        if (preg_match($pattern, $source, $tag)) {
            trigger_error("Illegal function name: {$tag[0]}", E_USER_ERROR);
            exit(1);
        }
        unset($pattern);
        /**
         * if file is included, display body content only
         */
        if ($templateClass->getTemplateVars('FILE_IS_INCLUDE')) {
            $source = preg_replace("/^.*<body(.*)>(.*)<\/body>.*$/Usi", "<div\\1>\\2</div>", $source);
        }

        $language = Language::getInstance();
        $source = $language->replaceToken($source);

        /**
         * Create initialization for JavaScript/Ajax bridge
         */
        if (isset($YANA) && mb_strpos($source, '<head') > -1) {
            assert('!isset($script); // Cannot redeclare var $script');
            $script = "\n        " . '<script type="text/javascript" language="javascript"><!--' . "\n" .
            '        window.yanaProfileId="' . Yana::getId() . '";' . "\n" .
            '        window.yanaSessionName="{$SESSION_NAME}";' . "\n" .
            '        window.yanaSessionId="{$SESSION_ID}";' . "\n" .
            '        window.yanaLanguage="' . $language->getLanguage() . '";' . "\n" .
            '        var src="";' . "\n" .
            '        var php_self="' . $YANA->getVar('PHP_SELF') . '";' . "\n" .
            '        //--></script>';
            $source = preg_replace('/<head(>| [^\/>]*>)/', '${0}' . $script, $source);
            unset($script);
        }

        /**
         * resolve relative path names
         */
        $basedir = $templateClass->smarty->getTemplateVars('BASEDIR');
        if (empty($basedir)) {
            $basedir = (string) dirname($templateClass->buildTemplateFilepath());
        }
        if (!empty($basedir)) {
            $basedir .= '/';
            if ($basedir[0] === '.') {
                $basedir = preg_replace('/^\.[\/\\\]/', '' ,$basedir);
            }
            $pattern = '/(' . YANA_LEFT_DELIMITER_REGEXP . ')import\s+(?:preparser(?:="true")?\s+|)file="(\S*)(".*' .
                YANA_RIGHT_DELIMITER_REGEXP . ')/Ui';
            preg_match_all($pattern, $source, $match2);
            for ($i = 0; $i < count($match2[0]); $i++)
            {
                if (preg_match('/\sliteral\s/i', $match2[0][$i])) {
                    $pattern = '/' . preg_quote($match2[0][$i], '/') . '/i';
                    $source = preg_replace($pattern, preg_replace("/\sliteral\s/i", " ", $match2[0][$i]), $source);
                } elseif (preg_match('/\spreparser\s/i', $match2[0][$i])) {
                    $replacementPattern = "/.*<body[^>]*>(.*)<\/body>.*/si";
                    $replacement = preg_replace($replacementPattern, "\\1", implode("", file($basedir . $match2[2][$i])));
                    $pattern = '/' . preg_quote($match2[0][$i], '/') . '/i';
                    $source = preg_replace($pattern, $replacement, $source);
                } else {
                    $replace = $match2[1][$i] . 'import file="' . $basedir . $match2[2][$i] . $match2[3][$i];
                    $source = str_replace($match2[0][$i], $replace, $source);
                }
            }
            $pattern = '/(' . YANA_LEFT_DELIMITER_REGEXP . ')insert\s+file="(\S*)(".*' . YANA_RIGHT_DELIMITER_REGEXP .
                ')/Ui';
            preg_match_all($pattern, $source, $match2);
            for ($i = 0; $i < count($match2[0]); $i++)
            {
                $pattern = '/' . preg_quote($match2[0][$i], '/') . '/i';
                $replacement = $match2[1][$i] . 'insert file="' . $basedir . $match2[2][$i] . $match2[3][$i];
                $source = preg_replace($pattern, $replacement, $source);
            }

            preg_match_all('/ background\s*=\s*"(\S*)"/i', $source, $match2);
            for ($i = 0; $i < count($match2[1]); $i++)
            {
                $pattern = '/^https?:\/\/\S*/i';
                $secondPattern = '/^' . YANA_LEFT_DELIMITER_REGEXP . '\$PHP_SELF' . YANA_RIGHT_DELIMITER_REGEXP . '/i';
                if (!preg_match($pattern, $match2[1][$i]) && !preg_match($secondPattern, $match2[1][$i])) {
                    $pattern = '/ background\s*=\s*"' . preg_quote($match2[1][$i], '/') . '"/i';
                    $source = preg_replace($pattern, ' background="' . $basedir . $match2[1][$i] . '"', $source);
                }
            }
            $pattern = '/ src\s*=\s*"((?!' . YANA_LEFT_DELIMITER_REGEXP . '|http:|https:)\S*)"/i';
            $source = preg_replace($pattern, ' src="' . $basedir . '$1"', $source);

            $pattern = '/\.src\s*=\s*\'((?!' . YANA_LEFT_DELIMITER_REGEXP . '|http:|https:)\S*)\'/i';
            $source = preg_replace($pattern, '.src=\'' . $basedir . '$1\'', $source);

            $pattern = '/ url\(("|\')((?!' . YANA_LEFT_DELIMITER_REGEXP . '|http:|https:)[^\1]*?)\1\)/i';
            $source = preg_replace($pattern, ' url($1' . $basedir . '$2$1)', $source);

            $pattern = '/ href\s*=\s*"((?!' . YANA_LEFT_DELIMITER_REGEXP . '|http:|https:|javascript:|\&\#109\;' .
                '\&\#97\;\&\#105\;\&\#108\;\&\#116\;\&\#111\;\&\#58\;|mailto:)\S*)"/i';
            $source = preg_replace($pattern, ' href="' . $basedir . '$1"', $source);
        } // end if

        return $source;
    }

    /**
     * <<smarty outputfilter>> outputfilter
     *
     * @access  public
     * @static
     * @param   string  $source  HTML code with PHP tags
     * @return  string
     * @ignore
     */
    public static function outputFilter($source)
    {
        assert('is_string($source); // Wrong type for argument 1. String expected');

        /**
         * add meta-data to header
         */
        if (mb_strpos($source, '</head>') > -1) {

            $htmlHead = "";

            /**
             * import rss
             */
            settype($feeds, 'array');
            $title = Language::getInstance()->getVar("PROGRAM_TITLE");
            foreach (\Yana\RSS\Publisher::getFeeds() as $action)
            {
                $htmlHead .= '        <link rel="alternate" type="application/rss+xml"' .
                ' title="' . $title . '" href="' . SmartUtility::url("action=$action") . "\"/>\n";
            }
            unset($action);

            /**
             * import stylesheets
             */
            assert('!isset($styleList); // Cannot redeclare var $styleList');
            $styleList = SmartView::getStyles();

            assert('is_array($styleList);');
            if (!empty($styleList)) {
                $styleList = array_reverse($styleList, true);
                assert('!isset($stylesheet); /* cannot redeclare variable $stylesheet */');
                foreach ($styleList as $stylesheet)
                {
                    $htmlHead = "        " . SmartUtility::css($stylesheet) . "\n" . $htmlHead;
                }
                unset($stylesheet);
            }
            unset($styleList);

            /**
             * import microsummaries
             */
            assert('!isset($summary); /* cannot redeclare variable $summary */');
            foreach (Microsummary::getSummaries() as $summary)
            {
                $htmlHead .= "        " . SmartUtility::microsummary($summary) . "\n";
            }
            unset($summary);

            /**
             * write header
             */
            $source = preg_replace('/<head(>| [^\/>]*>)/', "\$0\n" . $htmlHead, $source, 1);
            unset($htmlHead);

            /**
             * import javascripts
             */
            $htmlHead = "";
            foreach (SmartView::getScripts() as $script)
            {
                $htmlHead .= "        " . SmartUtility::script($script) . "\n";
            }
            unset($script);
            $source = preg_replace('/^\s*<\/head>/m', $htmlHead . "\$0", $source, 1);

            /*
             * remove empty comments
             */
            $source = preg_replace('/\s*<\!--\s*-->\s*/s', '', $source);
        } // end if

        $source = Language::getInstance()->replaceToken($source);
        return $source;
    }

}

?>