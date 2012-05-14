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
 * Virtual Drive File Configuration.
 *
 * WARNING! Don't try to string-cast this class.
 * Always call ->__toString()!
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
 * @package    yana
 * @subpackage vdrive
 * @name       Configuration
 *
 * @ignore
 */
class Configuration extends \Yana\XmlArray
{

    /**
     * <<factory>> load a file
     *
     * Returns the file identified by $path as a Configuration object.
     *
     * @param   string  $path  file path
     * @return  \Yana\VDrive\Configuration
     */
    public static function loadFile($path)
    {
        assert('is_string($path); // Wrong type for argument 1. String expected');
        return new self($path, 0, true);
    }

    /**
     * <<factory>> load a string
     *
     * Returns $string as a Configuration object.
     *
     * @param   string  $string     string
     * @return  \Yana\VDrive\Configuration
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
     * @return  \Yana\VDrive\Configuration
     */
    public static function createDrive()
    {
        return new self("<drive></drive>");
    }

    /**
     * Sets the name of this mountpoint.
     *
     * @param   string  $name  mountpoint-name
     * @return  \Yana\VDrive\Configuration
     */
    public function setNodeName($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');

        if ($this->isMountpoint() || $this->isVar()) {
            if (!isset($this->attributes()->name)) {
                $this->addAttribute("name", "$name");
            } else {
                $this->attributes()->name = $name;
            }
        }
        return $this;
    }

    /**
     * Returns the name of this mountpoint.
     *
     * @return  string
     */
    public function getNodeName()
    {
        $name = null;
        if (isset($this->attributes()->name)) {
            $name = (string) $this->attributes()->name;
        }
        return $name;
    }

    /**
     * Returns a the value attribute of a var-node if it is set.
     *
     * @return  string
     */
    public function getNodeValue()
    {
        $value = null;
        if (isset($this->attributes()->value)) {
            $value = (string) $this->attributes()->value;
        }
        return $value;
    }

    /**
     * Returns a the namespace attribute of a file-node if it is set.
     *
     * @return  string
     */
    public function getNodeNamespace()
    {
        $namespace = null;
        if (isset($this->attributes()->namespace)) {
            $namespace = (string) $this->attributes()->namespace;
        }
        return $namespace;
    }

    /**
     * Sets the namespace attribute of a file-node.
     *
     * @param  string  $namespace  a PHP namespace used as prefix for class-names of file wrappers
     * @return \Yana\VDrive\Configuration
     */
    public function setNodeNamespace($namespace)
    {
        assert('is_string($namespace); // Invalid argument $namespace: string expected');

        if ($this->isFile()) {
            if (!isset($this->attributes()->namespace)) {
                $this->addAttribute("namespace", $namespace);
            } else {
                $this->attributes()->namespace = $namespace;
            }
        }
        return $this;
    }

    /**
     * Sets the filter of this mountpoint.
     *
     * @param   string  $filter  mountpoint-filter
     * @return  \Yana\VDrive\Configuration
     */
    public function setNodeFilter($filter)
    {
        assert('is_string($filter); // Wrong type for argument 1. String expected');

        if ($this->isDir()) {
            if (!isset($this->attributes()->filter)) {
                $this->addAttribute("filter", "$filter");
            } else {
                $this->attributes()->filter = $filter;
            }
        }
        return $this;
    }

    /**
     * Returns the filter setting of this mountpoint.
     *
     * @return  string
     */
    public function getNodeFilter()
    {
        $filter = null;
        if (isset($this->attributes()->filter)) {
            $filter = (string) $this->attributes()->filter;
        }
        return $filter;
    }

    /**
     * Sets the auto-mount attribute of this mountpoint.
     *
     * @param   string  $isAutomount  mountpoint-auto-mount setting
     * @return  \Yana\VDrive\Configuration
     */
    public function setNodeAutomount($isAutomount)
    {
        assert('is_bool($isAutomount); // Invalid argument $isAutomount: bool expected');

        if ($this->isMountpoint()) {
            $isAutomount = ($isAutomount) ? "yes" : "no";
            if (!isset($this->attributes()->automount)) {
                $this->addAttribute("automount", $isAutomount);
            } else {
                $this->attributes()->automount = $isAutomount;
            }
        }
        return $this;
    }

    /**
     * Returns the auto-mount setting of this mountpoint.
     *
     * The default is bool(false).
     *
     * @return  bool
     */
    public function getNodeAutomount()
    {
        $isAutomount = false;
        if (isset($this->attributes()->automount)) {
            $isAutomount = $this->attributes()->automount == 'yes';
        }
        return $isAutomount;
    }

    /**
     * Adds a variable definition to the drive.
     *
     * @param   string  $name   name of var
     * @param   string  $value  value of var
     * @return  \Yana\VDrive\Configuration
     */
    public function addNodeVar($name, $value)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        assert('is_string($value); // Wrong type for argument 2. String expected');

        $var = null;
        if ($this->isDrive()) {
            $var = $this->addChild("var");
            $var->addAttribute("name", "$name");
            $var->addAttribute("value", "$value");
        }
        return $var;
    }

    /**
     * Returns a list of vars for the drive.
     *
     * @return  array
     */
    public function getNodeVars()
    {
        $vars = array();
        if (isset($this->var)) {
            $vars = $this->var;
        }
        return $vars;
    }

    /**
     * Adds a file definition to the drive.
     *
     * @param   string  $name         name of file
     * @param   bool    $isAutomount  auto-mount setting
     * @return  \Yana\VDrive\Configuration
     */
    public function addNodeFile($name, $isAutomount = false)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        assert('is_bool($isAutomount); // Wrong type for argument 2. Boolean expected');

        $file = null;
        if ($this->isDrive() || $this->isDir()) {
            $file = $this->addChild("file");
            $file->setNodeName($name);
            $file->setNodeAutomount($isAutomount);
        }
        return $file;
    }

    /**
     * Returns a list of files for the current node.
     *
     * @return  array
     */
    public function getNodeFiles()
    {
        $files = array();
        if (isset($this->file)) {
            $files = $this->file;
        }
        return $files;
    }

    /**
     * Adds a directory definition to the drive.
     *
     * @param   string  $name       name of directory
     * @param   bool    $automount  auto-mount setting
     * @param   string  $filter     (optional)
     * @return  \Yana\VDrive\Configuration
     */
    public function addNodeDir($name, $automount = false, $filter = "")
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        assert('is_bool($automount); // Wrong type for argument 2. Boolean expected');
        assert('is_string($filter); // Wrong type for argument 3. String expected');

        $dir = null;
        if ($this->isDrive() || $this->isDir()) {
            $dir = $this->addChild("dir");
            $dir->setNodeName($name);
            $dir->setNodeAutomount($automount);
            $dir->setNodeFilter($filter);
        }
        return $dir;
    }

    /**
     * Returns a list of files for the current node.
     *
     * @return  array
     */
    public function getNodeDirs()
    {
        $dirs = array();
        if (isset($this->dir)) {
            $dirs = $this->dir;
        }
        return $dirs;
    }

    /**
     * Adds an include path definition to the drive.
     *
     * @param   string  $path   path
     * @return  \Yana\VDrive\Configuration
     */
    public function addNodeInclude($path)
    {
        assert('is_string($path); // Wrong type for argument 1. String expected');

        $include = null;
        if ($this->isDrive()) {
            $include = $this->addChild("include");
            $include->addAttribute("path", "$path");
        }
        return $include;
    }

    /**
     * Returns a list of include paths for the drive.
     *
     * @return  array
     */
    public function getNodeIncludes()
    {
        $includes = array();
        if (isset($this->include)) {
            $includes = $this->include;
        }
        return $includes;
    }

    /**
     * Adds a source path to the list.
     *
     * Note: a resource may have multiple alternative paths.
     * Where the first path that refers to an existing resource "wins".
     * The last path should hold a default value.
     *
     * New values are always added to the end of the list.
     *
     * @param   string  $path  source path to add
     * @return  \Yana\VDrive\Configuration
     */
    public function addNodeSource($path)
    {
        assert('is_string($path); // Invalid argument $path: string expected');

        $source = null;
        if ($this->isMountpoint()) {
            $source = $this->addChild("source", "$path");
        }
        return $source;
    }

    /**
     * Returns a list of source for the mountpoint, sorted by priority.
     *
     * @return  array
     */
    public function getNodeSources()
    {
        $sources = array();
        if (isset($this->source)) {
            $sources = $this->source;
        }
        return $sources;
    }

    /**
     * Sets wether the resource must be read-, write, and/or executable.
     *
     * @param   string  $readable    (true = is redable , false otherweise)
     * @param   string  $writeable   (true = is writeable , false otherweise)
     * @param   string  $executable  (true = is executable , false otherweise)
     * @return  \Yana\VDrive\Configuration
     */
    public function setNodeRequirements($readable = false, $writeable = false, $executable = false)
    {
        assert('is_bool($readable); // Wrong type for argument 1. Boolean expected');
        assert('is_bool($writeable); // Wrong type for argument 2. Boolean expected');
        assert('is_bool($executable); // Wrong type for argument 3. Boolean expected');

        $requirements = null;
        if ($this->isMountpoint()) {
            $isReadable = ($readable) ? 'yes' : 'no';
            $isWriteable = ($writeable) ? 'yes' : 'no';
            $isExecutable = ($executable) ? 'yes' : 'no';
            if (!isset($this->requirements)) {
                $requirements = $this->addChild('requirements');
                $requirements->addAttribute('readable', $isReadable);
                $requirements->addAttribute('writeable', $isWriteable);
                $requirements->addAttribute('executable', $isExecutable);
            } else {
                $requirements =& $this->requirements;
                $requirements->attributes()->readable = $isReadable;
                $requirements->attributes()->writeable = $isWriteable;
                $requirements->attributes()->executable = $isExecutable;
            }
        }
        return $requirements;
    }

    /**
     * Returns wether or not the resource must be readable.
     *
     * Default is bool(false).
     *
     * @return  bool
     */
    public function nodeRequiresReadable()
    {
        $isReadable = false;
        if (isset($this->requirements) && !empty($this->requirements->attributes()->readable)) {
            $isReadable = $this->requirements->attributes()->readable == 'yes';
        }
        return $isReadable;
    }

    /**
     * Returns wether or not the resource must be writeable.
     *
     * Default is bool(false).
     *
     * @return  bool
     */
    public function nodeRequiresWriteable()
    {
        $isWriteable = false;
        if (isset($this->requirements) && isset($this->requirements->attributes()->writeable)) {
            $isWriteable = $this->requirements->attributes()->writeable == 'yes';
        }
        return $isWriteable;
    }

    /**
     * Returns wether or not the resource must be executable.
     *
     * Default is bool(false).
     *
     * @return  bool
     */
    public function nodeRequiresExecutable()
    {
        $isExecutable = false;
        if (isset($this->requirements) && isset($this->requirements->attributes()->executable)) {
            $isExecutable = $this->requirements->attributes()->executable == 'yes';
        }
        return $isExecutable;
    }

    /**
     * <<magic>> Outputs the contents as an XML string.
     *
     * @return  string
     * @ignore
     */
    public function __toString()
    {
        return $this->asXML();
    }

    /**
     * Returns bool(true) if the node is a dircetory.
     *
     * Contains sub-directories and files.
     *
     * @return  bool
     */
    public function isDir()
    {
        return $this->getName() === 'dir';
    }

    /**
     * Returns bool(true) if the node is a drive configuration.
     *
     * Contains includes, vars, directories and files.
     *
     * @return  bool
     */
    public function isDrive()
    {
        return $this->getName() === 'drive';
    }

    /**
     * Returns bool(true) if the node is a file.
     *
     * Has no children.
     *
     * @return  bool
     */
    public function isFile()
    {
        return $this->getName() === 'file';
    }

    /**
     * Returns bool(true) if the node is a variable definition.
     *
     * Has no children.
     *
     * @return  bool
     */
    public function isVar()
    {
        return $this->getName() === 'var';
    }

    /**
     * Returns bool(true) if the node is a class include definition.
     *
     * @return  bool
     */
    public function isInclude()
    {
        return $this->getName() === 'include';
    }

    /**
     * Returns bool(true) if the node is a file or a directory.
     *
     * @return  bool
     */
    public function isMountpoint()
    {
        return $this->isFile() || $this->isDir();
    }

}

?>