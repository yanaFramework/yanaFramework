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

namespace Yana\Views\Resources\Helpers;


/**
 * Helper for file resource.
 *
 * @package     yana
 * @subpackage  views
 */
class RelativePathsFilter extends \Yana\Views\Helpers\AbstractViewHelper
{

    /**
     * Resolve relative path names
     *
     * @param   string  $source   HTML source
     * @param   string  $basedir  template base dir
     * @return  string
     */
    public function __invoke($source, $basedir)
    {
        assert('is_string($source); // Wrong type for argument 1. String expected');
        assert('is_string($basedir); // Invalid argument $basedir: string expected');

        $smarty = $this->_getViewManager()->getSmarty();
        $lDelim = preg_quote($smarty->left_delimiter, '/');
        $rDelim = preg_quote($smarty->right_delimiter, '/');

        if (!empty($basedir)) {
            $basedir .= '/';
            if ($basedir[0] === '.') {
                $basedir = preg_replace('/^\.[\/\\\]/', '' ,$basedir);
            }
            $pattern = '/(' . $lDelim . ')import\s+(?:preparser(?:="true")?\s+|)file="(\S*)(".*' .
                $rDelim . ')/Ui';
            $match2 = array();
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
            $pattern = '/(' . $lDelim . ')insert\s+file="(\S*)(".*' . $rDelim .
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
                $secondPattern = '/^' . $lDelim . '\$PHP_SELF' . $rDelim . '/i';
                if (!preg_match($pattern, $match2[1][$i]) && !preg_match($secondPattern, $match2[1][$i])) {
                    $pattern = '/ background\s*=\s*"' . preg_quote($match2[1][$i], '/') . '"/i';
                    $source = preg_replace($pattern, ' background="' . $basedir . $match2[1][$i] . '"', $source);
                }
            }
            $pattern = '/ src\s*=\s*"((?!' . $lDelim . '|http:|https:)\S*)"/i';
            $source = preg_replace($pattern, ' src="' . $basedir . '$1"', $source);

            $pattern = '/\.src\s*=\s*\'((?!' . $lDelim . '|http:|https:)\S*)\'/i';
            $source = preg_replace($pattern, '.src=\'' . $basedir . '$1\'', $source);

            $pattern = '/ url\(("|\')((?!' . $lDelim . '|http:|https:)[^\1]*?)\1\)/i';
            $source = preg_replace($pattern, ' url($1' . $basedir . '$2$1)', $source);

            $pattern = '/ href\s*=\s*"((?!' . $lDelim . '|http:|https:|javascript:|\&\#109\;' .
                '\&\#97\;\&\#105\;\&\#108\;\&\#116\;\&\#111\;\&\#58\;|mailto:)\S*)"/i';
            $source = preg_replace($pattern, ' href="' . $basedir . '$1"', $source);
        } // end if

        return $source;
    }

}

?>