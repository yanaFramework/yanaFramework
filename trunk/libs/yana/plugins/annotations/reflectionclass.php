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

namespace Yana\Plugins\Annotations;

/**
 * Plugin information
 *
 * This class represents a plugin's meta information.
 * This is it's interface, name and description plus and more.
 *
 * @name        PluginReflectionClass
 * @package     yana
 * @subpackage  plugins
 */
class ReflectionClass extends \ReflectionClass
{

    /**
     * @var string
     */
    private $_className = "";

    /**
     * @var string
     */
    private $_classDoc = null;

    /**
     * @var string
     */
    private $_pageDoc = null;

    /**
     * @var string
     */
    private $_title = null;

    /**
     * @var string
     */
    private $_text = null;

    /**
     * @var array
     */
    private $_methods = array();

    /**
     * Constructor
     *
     * @param   string  $className  class name
     */
    public function __construct($className)
    {
        parent::__construct($className);
        $this->_className = $className;
    }

    /**
     * Get class name.
     *
     * @return  string
     */
    public function getClassName()
    {
        return $this->_className;
    }

    /**
     * Get method reflection.
     *
     * @param   string  $methodName   method name
     * @return  \Yana\Plugins\Annotations\ReflectionMethod
     */
    public function getMethod($methodName)
    {
        return new \Yana\Plugins\Annotations\ReflectionMethod($this->_className, $methodName);
    }

    /**
     * Get methods as reflections.
     *
     * @param   int  $filter    filter
     * @return  \Yana\Plugins\Annotations\ReflectionMethod[]
     */
    public function getMethods($filter = \ReflectionProperty::IS_PUBLIC)
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
     * Get title.
     *
     * The title is the first line or first sentence of a comment.
     * It should be followed by a blank line.
     *
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
     * Get description text.
     *
     * The description is the comment text without the title and possibly following annotations.
     *
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
     * Get page comment.
     *
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
     * @return  string
     */
    public function getDocComment()
    {
        if (!isset($this->_classDoc)) {

            $this->_classDoc = parent::getDocComment();
            if ($this->_classDoc === false) {
                // That's just a fallback. In some cases, the parent class returns an empty doc-comment.
                // @codeCoverageIgnoreStart

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

                // @codeCoverageIgnoreEnd
            }
        }
        return $this->_classDoc;
    }

    /**
     * get time when file was last modified
     *
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