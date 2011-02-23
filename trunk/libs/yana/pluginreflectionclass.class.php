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
 */

/**
 * Plugin information
 *
 * This class represents a plugin's meta information.
 * This is it's interface, name and description plus and more.
 *
 * @access      public
 * @name        PluginReflectionClass
 * @package     yana
 * @subpackage  core
 */
class PluginReflectionClass extends ReflectionClass
{

    /**#@+
     * @ignore
     * @access  private
     */

    /** @var string */ private $_className = "";
    /** @var string */ private $_classDoc = null;
    /** @var string */ private $_pageDoc = null;
    /** @var string */ private $_title = null;
    /** @var string */ private $_text = null;
    /** @var array  */ private $_methods = array();

    /**#@-*/

    /**
     * Constructor
     *
     * @access  public
     * @param   string  $className  class name
     */
    public function __construct($className)
    {
        parent::__construct($className);
        $this->_className = $className;
    }

    /**
     * get doc-tags from comment
     *
     * extract tags and values
     *
     * valid tag styles:
     * <code>
     * &#64;foo
     * &#64;foo  value
     * &#64;foo  key: value
     * &#64;foo  key1: value 1, key2: value 2
     * {&#64;foo
     *   key1: value 1,
     *   key2: value 2
     * }
     * </code>
     *
     * The above tags will return the following values:
     * <code>
     * Array
     * (
     *     [0] => 1
     *
     *     [1] => value
     *
     *     [2] => Array
     *         (
     *             [key] => value
     *         )
     *
     *     [2] => Array
     *         (
     *             [key1] => value 1
     *             [key2] => value 2
     *         )
     *
     *     [3] => Array
     *         (
     *             [key1] => value 1
     *             [key2] => value 2
     *         )
     * )
     * </code>
     *
     * If the same key is mentionend multiple times,
     * the value of the last occurence overwrites the previous.
     *
     * @access  public
     * @static
     * @param   string  $comment  doc-block of parsed file
     * @param   string  $tagName  name of doc-tag to extract
     * @return  array
     */
    public static function getTagsFromComment($comment, $tagName)
    {
        assert('is_string($comment); // Wrong type for argument 1. String expected');
        assert('is_string($tagName); // Wrong type for argument 2. String expected');
        $tagName = preg_quote($tagName, '/');

        $result = array();
        $match = array();
        /**
         * 1) get tags
         */

        /**
         * 1.1) simple tags: @foo
         */
        if (preg_match_all('/ @' . $tagName . '(\s.*|)$/mi', $comment, $match)) {

            assert('!isset($i); // Cannot redeclare var $i');
            assert('!isset($tagContent); // Cannot redeclare var $tagContent');
            foreach ($match[1] as $i => $tagContent)
            {
                $count = count($result);

                $tagContent = trim($tagContent);
                /**
                 * 2) get list of values
                 */
                assert('!isset($match2); // Cannot redeclare var $match2');
                if ($tagContent === "") {
                    $result[$count] = true;
                } elseif (preg_match_all('/([\w-]+)\:\s+([^,]*)/', $match[1][$i], $match2)) {
                    $result[$count] = array();
                    for ($j = 0; $j < count($match2[0]); $j++)
                    {
                        /**
                         * 3) assign values
                         */
                        $key = $match2[1][$j];
                        $value = trim($match2[2][$j]);
                        if ($value === "") {
                            $result[$count][$match2[1][$j]] = true;
                        } else {
                            $result[$count][$match2[1][$j]] = $value;
                        }
                    }
                } else {
                    $result[$count] = $tagContent;
                }
                unset($match2);
            } /* end foreach */
            unset($i, $tagContent);
        } /* end if */

        /**
         * 1.2) more complex tags: {@foo ... }
         */
        if (preg_match_all('/\{@' . $tagName . '\s*([^\}]*)/si', $comment, $match)) {

            assert('!isset($i); // Cannot redeclare var $i');
            assert('!isset($tagContent); // Cannot redeclare var $tagContent');
            foreach ($match[1] as $i => $tagContent)
            {
                $count = count($result);

                $tagContent = trim(preg_replace('/^\s*\*\s*/Um', ' ', $tagContent));
                /**
                 * 2) get list of values
                 */
                assert('!isset($match2); // Cannot redeclare var $match2');
                if ($tagContent === "") {
                    $result[$count] = true;
                } elseif (preg_match('/([\w-]+)\:\s+/', $tagContent)) {
                    $result[$count] = array();
                    while (preg_match('/(([\w-]+)\:\s+(.*?))(?:,?\s*?[\w-]+\:|$)/s', $tagContent, $match2))
                    {
                        $tagContent = str_replace($match2[1], '', $tagContent);
                        /**
                         * 3) assign values
                         */
                        $key = $match2[2];
                        $value = trim($match2[3]);
                        if ($value === "") {
                            $result[$count][$match2[2]] = true;
                        } else {
                            $result[$count][$match2[2]] = $value;
                        }
                    } /* end while */
                } else {
                    $result[$count] = $tagContent;
                }
                unset($match2);
            } /* end foreach */
            unset($i, $value);
        } /* end if */

        assert('is_array($result); // result is expected to be an array');
        return $result;
    }

    /**
     * get single doc-tag
     *
     * Returns the doc tag as a string.
     *
     * Use this function if you expect only one tag with a single value.
     * Otherwise the default value is returned.
     *
     * @access  public
     * @static
     * @param   string  $comment  doc-block of parsed file
     * @param   string  $tagName  name of doc-tag to extract
     * @param   string  $default  default value (if tag not found)
     * @return  string
     */
    public static function getTagFromComment($comment, $tagName, $default = "")
    {
        assert('is_string($comment); // Wrong type for argument 1. String expected');
        assert('is_string($tagName); // Wrong type for argument 2. String expected');
        assert('is_string($default); // Wrong type for argument 3. String expected');
        $result = self::getTagsFromComment($comment, $tagName);
        if (count($result) === 1) {
            return $result[0];
        } else {
            return $default;
        }
    }

    /**
     * get tag list
     *
     * Returns the doc tag as a string.
     *
     * Use this function if you expect only one tag with a single value.
     * Otherwise the default value is returned.
     *
     * @access  public
     * @param   string  $tagName  name of doc-tag to extract
     * @return  array
     */
    public function getTags($tagName)
    {
        return PluginReflectionClass::getTagsFromComment($this->getPageComment(), $tagName);
    }

    /**
     * get string from tag
     *
     * Returns the doc tag as a string.
     *
     * Use this function if you expect only one tag with a single value.
     * Otherwise the default value is returned.
     *
     * @access  public
     * @param   string  $tagName  name of doc-tag to extract
     * @param   string  $default  default value (if tag not found)
     * @return  array
     */
    public function getTag($tagName, $default = "")
    {
        return PluginReflectionClass::getTagFromComment($this->getPageComment(), $tagName, $default);
    }

    /**
     * get class name
     *
     * @access  public
     * @return  string
     */
    public function getClassName()
    {
        return $this->_className;
    }

    /**
     * get method
     *
     * @access  public
     * @param   string  $methodName   method name
     * @return  PluginReflectionMethod
     */
    public function getMethod($methodName)
    {
        return new PluginReflectionMethod($this->_className, $methodName);
    }

    /**
     * get methods
     *
     * @access  public
     * @param   int  $filter    filter
     * @return  PluginReflectionMethod[]
     */
    public function getMethods($filter = ReflectionProperty::IS_PUBLIC)
    {
        if (empty($this->_methods[$filter])) {
            $this->_methods[$filter] = array();
            foreach(parent::getMethods($filter) as $method)
            {
                $this->_methods[$filter][] = $this->getMethod($method->getName());
            }
        }
        return $this->_methods[$filter];
    }

    /**
     * get title
     *
     * @access  public
     * @return  string
     */
    public function getTitle()
    {
        if (!isset($this->_title)) {
            $this->_title = "";
            if (preg_match('/(?:\/\*\*|^)[\s\*\r\f\n]*(\S.*?)[\r\f\n]/', $this->getPageComment(), $match)) {
                $this->_title = trim($match[1]);
            }
        }
        return $this->_title;
    }

    /**
     * get description text
     *
     * Returns bool(false) on error.
     *
     * @access  public
     * @return  string
     */
    public function getText()
    {
        if (!isset($this->_text)) {
            $this->_text = "";
            $pageDoc = $this->getPageComment();
            $match = $match2 = array();
            if (preg_match('/(?:\/\*\*|^)[\s\*\r\f\n]*\S.*?[\r\f\n]/', $pageDoc, $match)) {
                if (preg_match('/[\s\*\r\f\n]([^@\{]+)/si', $pageDoc, $match2, 0, mb_strlen($match[0]))) {
                    $this->_text = trim(preg_replace('/^\s*\*\s*/Um', '', $match2[1]));
                }
            }
        }
        return $this->_text;
    }

    /**
     * get page comment
     *
     * @access  public
     * @return  string
     */
    public function getPageComment()
    {
        if (!isset($this->_pageDoc)) {
            $this->_pageDoc = "";
            $file = file($this->getFileName());
            for ($i = 0; $i < $this->getStartLine(); $i++)
            {
                $this->_pageDoc .= $file[$i];

                if (strpos($file[$i], '*/') !== false) {
                    break;
                }
            }
            $this->_pageDoc = preg_replace('/^.*?(\/\*\*.*?\*\/).*$/s', '$1', $this->_pageDoc);
        }
        return $this->_pageDoc;
    }

    /**
     * get document comment
     *
     * @access  public
     * @return  string
     */
    public function getDocComment()
    {
        if (!isset($this->_classDoc)) {

            $this->_classDoc = parent::getDocComment();
            if ($this->_classDoc === false) {

                $this->_classDoc = "";
                $file = file($this->getFileName());

                for ($i = $this->getStartLine(); $i > 0; $i--)
                {
                    $this->_classDoc = $file[$i] . $this->_classDoc;

                    if (preg_match('/^\s*\/\*\*/', $file[$i])) {
                        break;
                    }
                }
                $this->_classDoc = preg_replace('/^\s*(.*?\*\/).*/s', '$1', $this->_classDoc);
            }
        }
        return $this->_classDoc;
    }

    /**
     * get time when file was last modified
     *
     * @access  public
     * @return  string
     */
    public function getLastModified()
    {
        return filemtime($this->getFileName());
    }

    /**
     * get directory where file is stored
     *
     * Returns bool(false) on error.
     *
     * @access  public
     * @return  string
     */
    public function getDirectory()
    {
        /* get directory from filename */
        $directory = dirname($this->getFileName());
        /* remove current working directory */
        $directory = str_replace(getcwd(), '', $directory);
        /* replace Windows-style directory seperators */
        $directory = str_replace('\\', '/', $directory);
        /* remove leading '/' */
        if ($directory[0] === '/') {
            $directory = mb_substr($directory, 1);
        }
        return $directory;
    }
}

?>