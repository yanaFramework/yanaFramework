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

namespace Yana\VDrive;

/**
 * Virtual Drive File Configuration
 *
 * Class to abstract from real filesystems by mapping
 * filenames to aliases (mountpoints).
 *
 * This class can be used to create and update configuration files.
 *
 * A virtual drive is defined by a XML configuration file.
 * Here is a simple example:
 * <code>
 * <?xml version="1.0" ?>
 * <drive>
 * 	<dir name="config">
 * 		<source>config/</source>
 * 		<file name="sql.file">
 * 			<source>config/sql/default.sql</source>
 * 		</file>
 * 		<dir name="profiles">
 * 			<source>config/profiles/</source>
 * 			<file name="default.sml">
 * 				<source>config/profiles/default.config</source>
 * 			</file>
 * 			<file name="foo.file">
 * 				<source>config/etc/foo.config</source>
 * 			</file>
 * 		</dir>
 * 	</dir>
 * </drive>
 * </code>
 *
 * To create an instance of this class use:
 * <code>
 * $xml = Configuration::loadFile('foo.drive.xml');
 * // which is the same as
 * $xml = simplexml_load_file('foo.drive.xml', 'Configuration');
 * // to create an empty drive definition use
 * $xml = Configuration::createDrive();
 * </code>
 * Everything else is just the same as with any SimpleXML document.
 * There are some additional function to aid you in creating your files.
 *
 * Writing a file to some location is just as simple:
 * <code>
 * file_put_contents('bar.drive.xml', $xml);
 * </code>
 * Note that you don't need to convert the Configuration object to a string.
 * This is done automatically.
 *
 * @access     public
 * @package    yana
 * @subpackage vdrive
 * @name       Configuration
 *
 * @ignore
 */
class Configuration extends \XmlArray
{
    /**
     * <<factory>> load a file
     *
     * Returns the file identified by $path as a Configuration object.
     *
     * @access  public
     * @static
     * @param   string  $path  file path
     * @return  Configuration
     */
    public static function loadFile($path)
    {
        assert('is_string($path); // Wrong type for argument 1. String expected');
        return \simplexml_load_file($path, __CLASS__);
    }

    /**
     * <<factory>> load a string
     *
     * Returns $string as a Configuration object.
     *
     * @access  public
     * @static
     * @param   string  $string     string
     * @return  Configuration
     */
    public static function loadString($string)
    {
        assert('is_string($string); // Wrong type for argument 1. String expected');
        return \simplexml_load_string($string, __CLASS__);
    }

    /**
     * <<factory>> create a drive configuration
     *
     * Returns the an empty file identified by $path as a Configuration object.
     *
     * @access  public
     * @static
     * @return  Configuration
     */
    public static function createDrive()
    {
        return new self("<drive></drive>");
    }

    /**
     * set name
     *
     * Sets the name of this mountpoint.
     *
     * @access  public
     * @param   string  $name  mountpoint-name
     */
    public function setNodeName($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        switch ($this->getName())
        {
            case 'dir':
            case 'file':
            case 'var':
                if (!isset($this->attributes()->name)) {
                    $this->addAttribute("name", "$name");
                } else {
                    $this->attributes()->name = $name;
                }
            break;
        }
    }

    /**
     * get name
     *
     * Returns the name of this mountpoint.
     *
     * @access  public
     * @return  string
     */
    public function getNodeName()
    {
        if (isset($this->attributes()->name)) {
            return (string) $this->attributes()->name;
        } else {
            return null;
        }
    }

    /**
     * get value
     *
     * Returns a the value attribute of a var-node if it is set.
     *
     * @access  public
     * @return  string
     */
    public function getNodeValue()
    {
        if (isset($this->attributes()->value)) {
            return (string) $this->attributes()->value;
        } else {
            return null;
        }
    }

    /**
     * set filter
     *
     * Sets the filter of this mountpoint.
     *
     * @access  public
     * @param   string  $filter  mountpoint-filter
     */
    public function setNodeFilter($filter)
    {
        assert('is_string($filter); // Wrong type for argument 1. String expected');
        switch ($this->getName())
        {
            case 'dir':
                if (!isset($this->attributes()->filter)) {
                    $this->addAttribute("filter", "$filter");
                } else {
                    $this->attributes()->filter = $filter;
                }
            break;
        }
    }

    /**
     * get filter
     *
     * Returns the filter setting of this mountpoint.
     *
     * @access  public
     * @return  string
     */
    public function getNodeFilter()
    {
        if (isset($this->attributes()->filter)) {
            return (string) $this->attributes()->filter;
        } else {
            return null;
        }
    }

    /**
     * set auto-mount
     *
     * Sets the auto-mount attribute of this mountpoint.
     *
     * @access  public
     * @param   string  $automount  mountpoint-auto-mount setting
     */
    public function setNodeAutomount($automount)
    {
        assert('is_bool($automount); // Wrong type for argument 1. Boolean expected');
        switch ($this->getName())
        {
            case 'dir':
            case 'file':
                if ($automount) {
                    $automount = "yes";
                } else {
                    $automount = "no";
                }
                if (!isset($this->attributes()->automount)) {
                    $this->addAttribute("automount", $automount);
                } else {
                    $this->attributes()->automount = $automount;
                }
            break;
        }
    }

    /**
     * get auto-mount
     *
     * Returns the auto-mount setting of this mountpoint.
     * The default is bool(false).
     *
     * @access  public
     * @return  bool
     */
    public function getNodeAutomount()
    {
        if (isset($this->attributes()->automount)) {
            return $this->attributes()->automount == 'yes';
        } else {
            return false;
        }
    }

    /**
     * add a drive var
     *
     * Adds a variable definition to the drive.
     *
     * @access  public
     * @param   string  $name   name of var
     * @param   string  $value  value of var
     * @return  Configuration
     */
    public function addNodeVar($name, $value)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        assert('is_string($value); // Wrong type for argument 2. String expected');
        switch ($this->getName())
        {
            case 'drive':
                $var = $this->addChild("var");
                $var->addAttribute("name", "$name");
                $var->addAttribute("value", "$value");
                return $var;
            break;
            default:
                return null;
            break;
        }
    }

    /**
     * get list of vars
     *
     * Returns a list of vars for the drive.
     *
     * @access  public
     * @return  array
     */
    public function getNodeVars()
    {
        if (isset($this->var)) {
            return $this->var;
        } else {
            return array();
        }
    }

    /**
     * add a drive file
     *
     * Adds a file definition to the drive.
     *
     * @access  public
     * @param   string  $name       name of file
     * @param   bool    $automount  auto-mount setting
     * @return  Configuration
     */
    public function addNodeFile($name, $automount = false)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        assert('is_bool($automount); // Wrong type for argument 2. Boolean expected');
        switch ($this->getName())
        {
            case 'drive':
            case 'dir':
                $file = $this->addChild("file");
                $file->setNodeName($name);
                $file->setNodeAutomount($automount);
                return $file;
            break;
            default:
                return null;
            break;
        }
    }

    /**
     * get list of files
     *
     * Returns a list of files for the current node.
     *
     * @access  public
     * @return  array
     */
    public function getNodeFiles()
    {
        if (isset($this->file)) {
            return $this->file;
        } else {
            return array();
        }
    }

    /**
     * add a drive directory
     *
     * Adds a directory definition to the drive.
     *
     * @access  public
     * @param   string  $name       name of directory
     * @param   bool    $automount  auto-mount setting
     * @param   string  $filter     (optional)
     * @return  Configuration
     */
    public function addNodeDir($name, $automount = false, $filter = "")
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        assert('is_bool($automount); // Wrong type for argument 2. Boolean expected');
        assert('is_string($filter); // Wrong type for argument 3. String expected');
        switch ($this->getName())
        {
            case 'drive':
            case 'dir':
                $dir = $this->addChild("dir");
                $dir->setNodeName($name);
                $dir->setNodeAutomount($automount);
                $dir->setNodeFilter($filter);
                return $dir;
            break;
            default:
                return null;
            break;
        }
    }

    /**
     * get list of directory
     *
     * Returns a list of files for the current node.
     *
     * @access  public
     * @return  array
     */
    public function getNodeDirs()
    {
        if (isset($this->dir)) {
            return $this->dir;
        } else {
            return array();
        }
    }

    /**
     * add a drive include path
     *
     * Adds an include path definition to the drive.
     *
     * @access  public
     * @param   string  $path   path
     * @return  Configuration
     */
    public function addNodeInclude($path)
    {
        assert('is_string($path); // Wrong type for argument 1. String expected');
        switch ($this->getName())
        {
            case 'drive':
                $include = $this->addChild("include");
                $include->addAttribute("path", "$path");
                return $include;
            break;
            default:
                return null;
            break;
        }
    }

    /**
     * get list of include paths
     *
     * Returns a list of include paths for the drive.
     *
     * @access  public
     * @return  array
     */
    public function getNodeIncludes()
    {
        if (isset($this->include)) {
            return $this->include;
        } else {
            return array();
        }
    }

    /**
     * add a source path
     *
     * Adds a source path to the list.
     * Note: a resource may have multiple alternative paths.
     * Where the first path that refers to an existing resource "wins".
     * The last path should hold a default value.
     *
     * New values are always added to the end of the list.
     *
     * @access  public
     * @param   string  $source  source path to add
     * @return  Configuration
     */
    public function addNodeSource($source)
    {
        assert('is_string($source); // Wrong type for argument 1. String expected');
        switch ($this->getName())
        {
            case 'dir':
            case 'file':
                return $this->addChild("source", "$source");
            break;
            default:
                return null;
            break;
        }
    }

    /**
     * get list of sources
     *
     * Returns a list of source for the mountpoint, sorted by priority.
     *
     * @access  public
     * @return  array
     */
    public function getNodeSources()
    {
        if (isset($this->source)) {
            return $this->source;
        } else {
            return array();
        }
    }

    /**
     * set requirements
     *
     * Sets wether the resource must be read-, write, and/or executable.
     *
     * @access  public
     * @param   string  $readable    (true = is redable , false otherweise)
     * @param   string  $writeable   (true = is writeable , false otherweise)
     * @param   string  $executable  (true = is executable , false otherweise)
     * @return  Configuration
     */
    public function setNodeRequirements($readable = false, $writeable = false, $executable = false)
    {
        assert('is_bool($readable); // Wrong type for argument 1. Boolean expected');
        assert('is_bool($writeable); // Wrong type for argument 2. Boolean expected');
        assert('is_bool($executable); // Wrong type for argument 3. Boolean expected');
        switch ($this->getName())
        {
            case 'dir':
            case 'file':
                $requirements = null;
                if (!isset($this->requirements)) {
                    $requirements = $this->addChild('requirements');
                    if ($readable) {
                        $requirements->addAttribute('readable', 'yes');
                    } else {
                        $requirements->addAttribute('readable', 'no');
                    }
                    if ($writeable) {
                        $requirements->addAttribute('writeable', 'yes');
                    } else {
                        $requirements->addAttribute('writeable', 'no');
                    }
                    if ($executable) {
                        $requirements->addAttribute('executable', 'yes');
                    } else {
                        $requirements->addAttribute('executable', 'no');
                    }
                } else {
                    $requirements =& $this->requirements;
                    if ($readable) {
                        $requirements->attributes()->readable = 'yes';
                    } else {
                        $requirements->attributes()->readable = 'no';
                    }
                    if ($writeable) {
                        $requirements->attributes()->writeable = 'yes';
                    } else {
                        $requirements->attributes()->writeable = 'no';
                    }
                    if ($executable) {
                        $requirements->attributes()->executable = 'yes';
                    } else {
                        $requirements->attributes()->executable = 'no';
                    }
                }
                return $requirements;
            default:
                return null;
        }
    }

    /**
     * must be readable?
     *
     * Returns wether or not the resource must be readable.
     * Default is bool(false).
     *
     * @access  public
     * @return  bool
     */
    public function nodeRequiresReadable()
    {
        if (isset($this->requirements) && !empty($this->requirements->attributes()->readable)) {
            return $this->requirements->attributes()->readable == 'yes';
        } else {
            return false;
        }
    }

    /**
     * must be writeable?
     *
     * Returns wether or not the resource must be writeable.
     * Default is bool(false).
     *
     * @access  public
     * @return  bool
     */
    public function nodeRequiresWriteable()
    {
        if (isset($this->requirements) && isset($this->requirements->attributes()->writeable)) {
            return $this->requirements->attributes()->writeable == 'yes';
        } else {
            return false;
        }
    }

    /**
     * must be executable?
     *
     * Returns wether or not the resource must be executable.
     * Default is bool(false).
     *
     * @access  public
     * @return  bool
     */
    public function nodeRequiresExecutable()
    {
        if (isset($this->requirements) && isset($this->requirements->attributes()->executable)) {
            return $this->requirements->attributes()->executable == 'yes';
        } else {
            return false;
        }
    }

    /**
     * <<magic>> convert to string
     *
     * Outputs the contents as an XML string.
     *
     * @access  public
     * @return  string
     * @ignore
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * convert to string
     *
     * Outputs the contents as an XML string.
     *
     * @access  public
     * @return  string
     */
    public function toString()
    {
        return $this->asXML();
    }

    /**
     * check type of node
     *
     * Returns bool(true) if the node has the given type and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isDir()
    {
        return $this->getName() === 'dir';
    }

    /**
     * check type of node
     *
     * Returns bool(true) if the node has the given type and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isDrive()
    {
        return $this->getName() === 'drive';
    }

    /**
     * check type of node
     *
     * Returns bool(true) if the node has the given type and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isFile()
    {
        return $this->getName() === 'file';
    }

    /**
     * check type of node
     *
     * Returns bool(true) if the node has the given type and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isVar()
    {
        return $this->getName() === 'var';
    }

    /**
     * check type of node
     *
     * Returns bool(true) if the node has the given type and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isInclude()
    {
        return $this->getName() === 'include';
    }

    /**
     * check type of node
     *
     * Returns bool(true) if the node is either a file or directory and bool(false) otherwise.
     *
     * @access  public
     * @return  bool
     */
    public function isMountpoint()
    {
        return $this->getName() === 'file' || $this->getName() === 'dir';
    }
}

?>