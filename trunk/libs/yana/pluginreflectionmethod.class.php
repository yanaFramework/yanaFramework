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
 * @name        PluginReflectionMethod
 * @package     yana
 * @subpackage  core
 *
 * @ignore
 */
class PluginReflectionMethod extends ReflectionMethod
{

    /**#@+
     * @ignore
     * @access  private
     */

    /** @var string */ private $className = "";
    /** @var string */ private $title = "";
    /** @var string */ private $text = "";
    /** @var string */ private $docComment = "";

    /**#@-*/

    /**
     * Constructor
     *
     * @access  public
     * @param   string  $className   name of class
     * @param   string  $methodName  name of method
     */
    public function __construct($className, $methodName)
    {
        parent::__construct($className, $methodName);
        $this->className = $className;

        $file = file($this->getFileName());

        if (parent::getDocComment() === false) {

            $this->docComment = "";

            for ($i = $this->getStartLine(); $i > 0; $i--)
            {
                $this->docComment = $file[$i] . $this->docComment;

                if (preg_match('/^\s*\/\*\*/', $file[$i])) {
                    break;
                }
            }
            $this->docComment = preg_replace('/^\s*(.*?\*\/).*/s', '$1', $this->docComment);
        } else {
            $this->docComment = parent::getDocComment();
        }
        $this->_parse();
    }

    /**
     * parse doc-block
     *
     * @access  private
     */
    private function _parse()
    {
        /**
         * Title
         */
        $match = array();
        if (preg_match('/\/\*\*[\s\*\r\f\n]*(\S.*?)[\r\f\n]/', $this->docComment, $match)) {
            $this->title = trim($match[1]);

            /**
             * Text
             */
            $match2 = array();
            if (preg_match('/[\s\*\r\f\n]([^@\{]+)/si', $this->docComment, $match2, 0, mb_strlen($match[0]))) {
                $this->text = trim(preg_replace('/^\s*\*\s*/Um', '', $match2[1]));
            }
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
        return PluginReflectionClass::getTagsFromComment($this->docComment, $tagName);
    }

    /**
     * get tag
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
        return PluginReflectionClass::getTagFromComment($this->docComment, $tagName, $default);
    }

    /**
     * get title
     *
     * @access  public
     * @return  string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * get text
     *
     * @access  public
     * @return  string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * get class name
     *
     * @access  public
     * @return  string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * get document comment
     *
     * @access  public
     * @return  string
     */
    public function getDocComment()
    {
        return $this->docComment;
    }
}

?>