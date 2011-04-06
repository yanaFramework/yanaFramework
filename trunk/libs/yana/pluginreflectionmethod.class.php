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
class PluginReflectionMethod extends \ReflectionMethod
{

    /**#@+
     * @ignore
     * @access  private
     */

    /** @var string */ private $_className = "";
    /** @var string */ private $_title = null;
    /** @var string */ private $_text = null;
    /** @var string */ private $_docComment = null;

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
        $this->_className = $className;
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
            $match = array();
            if (preg_match('/\/\*\*[\s\*\r\f\n]*(\S.*?)[\r\f\n]/', $this->getDocComment(), $match)) {
                $this->_title = trim($match[1]);
            }
        }
        return $this->_title;
    }

    /**
     * get text
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
            if (preg_match('/\/\*\*[\s\*\r\f\n]*(\S.*?)[\r\f\n]/', $this->_docComment, $match)) {
                if (preg_match('/[\s\*\r\f\n]([^@\{]+)/si', $pageDoc, $match2, 0, mb_strlen($match[0]))) {
                    $this->_text = trim(preg_replace('/^\s*\*\s*/Um', '', $match2[1]));
                }
            }
        }
        return $this->_text;
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
     * get document comment
     *
     * @access  public
     * @return  string
     */
    public function getDocComment()
    {
        if (!isset($this->_docComment)) {

            $this->_docComment = parent::getDocComment();
            if ($this->_docComment === false) {

                $this->_docComment = "";

                $file = file($this->getFileName());

                for ($i = $this->getStartLine(); $i > 0; $i--)
                {
                    $this->_docComment = $file[$i] . $this->_docComment;

                    if (preg_match('/^\s*\/\*\*/', $file[$i])) {
                        break;
                    }
                }
                $this->_docComment = preg_replace('/^\s*(.*?\*\/).*/s', '$1', $this->_docComment);
            }
        }
        return $this->_docComment;
    }

}

?>