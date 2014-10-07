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
 * Virtual Drive.
 *
 * Class to abstract from real filesystems by mapping
 * filenames to aliases (mountpoints).
 * Mountpoints may be mounted and unmounted at runtime.
 * When reading a mountpoint, a corresponding file wrapper
 * will be returned to work on the file.
 *
 * Using virtual drives will clean your source from
 * hard-coded file- and directory names, as well as
 * doing all the job of creating and initializing
 * the file wrappers for you.
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
 * To access "foo.file" you might use this code:
 * <code>
 * $drive = new VDrive('my_drive.xml');
 * $foo = $drive->getResource('config/profiles/foo.file');
 * // do something with the file, e.g. copy it:
 * $foo->copy('some/where/bar.config');
 * </code>
 *
 * You may want to see the DTD for a more in-depth definition
 * of the elements: config/dtd/drive.dtd
 *
 * @package    yana
 * @subpackage vdrive
 * @name       VDrive
 */
class VDrive extends \Yana\Files\AbstractResource implements \Yana\Report\IsReportable, \Serializable
{

    /**
     * @var  array
     */
    private $_drive = array();

    /**
     * @var  string
     */
    private $_baseDir = "";

    /**
     * @var  array
     */
    private $_files = array();

    /**
     * @var  \Yana\VDrive\Configuration
     */
    private $_content = null;

    /**
     * local directory settings
     *
     * @var  array
     * @ignore
     */
    protected $vars = array();

    /**
     * default directory settings
     *
     * @var  array
     */
    private static $_defaultSettings = array();

    /**
     * use default directory settings
     *
     * true = yes, false = no
     *
     * @var  bool
     */
    private static $_useDefaults = false;

    /**
     * The default namespace as prefix for class-names of file wrappers.
     *
     * @var  string
     */
    private $_defaultNamespaceForFileWrappers = '\Yana\Files\\';

    /**
     * Creates a new virtual drive instance.
     *
     * @name   VDrive::__construct()
     * @param  string  $path     absolute path to configuration file
     * @param  string  $baseDir  base directory
     */
    public function __construct($path, $baseDir = "")
    {
        assert('is_string($path); // Wrong type for argument 1. String expected');
        assert('is_string($baseDir); // Wrong type for argument 2. String expected');
        parent::__construct($path);
        $this->_baseDir = (string) $baseDir;
    }

    /**
     * Get a drive's mountpoint.
     *
     * You may access the drive of a plugin by using it's name.
     *
     * @param   string  $name  name of plugin
     * @return  \Yana\VDrive\VDrive
     */
    public function __get($name)
    {
        assert('is_string($name); // Wrong type for argument 1. String expected');
        return $this->getResource($name);
    }

    /**
     * Returns the default namespace as prefix for class-names of file wrappers.
     * 
     * @return  string
     */
    protected function _getDefaultNamespaceForFileWrappers()
    {
        return $this->_defaultNamespaceForFileWrappers;
    }

    /**
     * Make this the default drive.
     *
     * Each drive has it's own private settings. However: you can make
     * one drive make a public "default drive". The settings of this drive
     * will become publicly visible to all other instances.
     *
     * These are standard directory settings, which are used to auto-replace references
     * found in VDrive-config-files.
     *
     * These settings are automatically initialized by the framework, so normally you
     * don't need to care for these settings.
     *
     * You can recall this function to change the global drive at any time.
     * This will not replace or remove the private settings of the prior "global" drive.
     *
     * But be adviced: you should always do this BEFORE creating the object,
     * or otherwise it will have no effect.
     *
     * @ignore
     */
    public function setAsGlobal()
    {
        self::$_defaultSettings =& $this->vars;
    }

    /**
     * Use defaults.
     *
     * If set to true, this function will cause all
     * drives created in the future to always fall back to the
     * default settings.
     * This means always the last defined source path is taken,
     * which is the default.
     *
     * Example:
     * <code>
     * <?xml version="1.0" ?>
     * <drive>
     * 	 <file name="some.file">
     * 	   <source>foo.txt</source>
     * 	   <source>bar.txt</source>
     * 	 </file>
     * </drive>
     * </code>
     *
     * If $useDefaults is set to false, the drive will load
     * "foo.txt" and only if it does not exist, will fall
     * back to "bar.txt". If $useDefaults is set to true,
     * the drive will ignore "foo.txt" and always load
     * "bar.txt", no matter if any of both exists.
     *
     * @param  bool  $useDefaults  (true = use defaults, false otherweise)
     */
    public static function useDefaults($useDefaults)
    {
        self::$_useDefaults = (bool) $useDefaults;
    }

    /**
     * Mount an unmounted virtual drive.
     *
     * Mount the mountpoint identified by $name and copies the contents
     * (if any) to the repository.
     *
     * This function returns bool(true) on success, or bool(false) on error.
     *
     * @name    VDrive::mount()
     * @param   string  $name  name of the drive to mount
     * @return  bool
     */
    public function mount($name)
    {
        assert('is_string($name); // Wrong argument type for argument 1. String expected.');

        /* try to mounting the file */
        if (!isset($this->_drive["$name"]) || !$this->_drive["$name"]->mount()) {
            return false;
        }
        assert('!isset($file); // Cannot redeclare var $file');
        $file = $this->_files["$name"] = $this->_drive["$name"]->getMountpoint();

        /* if it is a SML file, load the configuration */
        if ($file instanceOf \Yana\Core\IsVarContainer && $file->exists()) {
            assert('!isset($array); // Cannot redeclare var $array');
            /* @var $file \Yana\Core\IsVarContainer */
            $array = $file->getVars();
            assert('is_null($array) || is_array($array); /* unexpected result: $array */');
            if (is_array($array)) {
                $this->vars = \Yana\Util\Hashtable::merge($this->vars, $array);
            }
            unset($array);
        }

        return true;
    }

    /**
     * Read the virtual drive.
     *
     * This loads the virtual drive and initializes it's contents.
     * It does nothing when called multiple times.
     *
     * If the file does not exist or is not readable, the function throws an exception.
     *
     * @name    VDrive::read()
     * @throws  \Yana\Core\Exceptions\NotReadableException    when source file is not readable
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when the file could not be read or contains invalid syntax
     */
    public function read()
    {
        if (isset($this->_content)) {
            return;
        }
        /* get file content */
        $content = file_get_contents($this->path);
        if (empty($content)) {
            $message = "VDrive configuration file is empty or not readable: '{$this->getPath()}'.";
            throw new \Yana\Core\Exceptions\NotReadableException($message, E_USER_WARNING);
        }
        /* apply default settings */
        $content = \Yana\Util\String::replaceToken($content, self::$_defaultSettings);
        /* create configuration */
        $this->_content = \Yana\VDrive\Configuration::loadString($content);
        /* read XML */
        if (!($this->_content instanceOf \Yana\VDrive\Configuration)) {
            $message = "Not a valid VDrive configuration file: '{$this->getPath()}'";
            throw new \Yana\Core\Exceptions\InvalidSyntaxException($message, E_USER_WARNING);
        }
        $this->_readXML($this->_content);
    }

    /**
     * Build the virtual drive.
     *
     * This iterates through the XML file and builds the virtual drive
     * as definded.
     *
     * @param   \Yana\VDrive\Configuration  $content  current xml node
     * @param   string                      $path     current virtual path
     */
    private function _readXML(\Yana\VDrive\Configuration $content, $path = "")
    {
        assert('is_string($path); // Wrong type for argument 2. String expected');

        if ($content->isDrive()) {

            if ($path == "" && $content->getNodeName()) {
                $path = $content->getNodeName() . ":/";
            }

            /* 1) handle vars */
            assert('!isset($node); // Cannot redeclare var $node');
            foreach ($content->getNodeVars() as $node)
            {
                $name = $node->getNodeName();
                $value = $node->getNodeValue();

                // skip if value already defined
                if (!isset($this->vars[$name])) {
                    // recursively replace vars
                    if (mb_strpos($value, YANA_LEFT_DELIMITER) !== false) {
                        $value = \Yana\Util\String::replaceToken($value, $this->vars);
                    }
                    // assign value
                    $this->vars[$name] = $value;
                }
                unset($name, $value);
            }
            unset($node);

            /* 2) handle includes */
            assert('!isset($node); // Cannot redeclare var $node');
            foreach ($content->getNodeIncludes() as $node)
            {
                $file = (string) $node->attributes()->path;

                if (!preg_match('/^[\w\/]*[\w\.]+\.php$/s', $file)) {
                    trigger_error("Invalid filename to include: '{$this->_baseDir}{$file}'.", E_USER_WARNING);

                } elseif (!is_file("{$this->_baseDir}{$file}")) {
                    trigger_error("No such file to include: '{$this->_baseDir}{$file}'.", E_USER_WARNING);

                } else {
                    include_once "{$this->_baseDir}{$file}";
                }
            }
            unset($node);

        } /* end if */

        assert('!isset($node); // Cannot redeclare var $node');
        foreach ($content as $node)
        {
            if (!$node->isMountpoint()) {
                continue;
            }

            // get the virtual path name
            $name = $path . $node->getNodeName();

            // get the source path
            $source = $this->_getSource($node);

            if ($node->isDir()) {

                // create a new mount-point
                $this->_drive[$name] = new Dir($source);

                // set file filter
                $filter = $node->getNodeFilter();
                if (isset($filter)) {
                    $this->_drive[$name]->setFilter($filter);
                }

                // recurse into directory
                $this->_readXML($node, $name . '/');

            } elseif ($node->isFile()) {

                // get class name
                assert('!isset($namespace); // Cannot redeclare var $namespace');
                $namespace = ($node->getNodeNamespace()) ? ($node->getNodeNamespace()) :
                    $this->_getDefaultNamespaceForFileWrappers();
                assert('!isset($type); // Cannot redeclare var $type');
                $type = '';
                assert('!isset($match); // Cannot redeclare var $match');
                $match = array();
                if (preg_match('/\.(\w+)$/', $name, $match)) {
                    $type = $match[1];
                }
                unset($match);

                // create a new mount-point
                $this->_drive[$name] = new \Yana\VDrive\File($source, $namespace . $type);
                unset($type, $namespace);

            } /* end if */

            // mount the dir automatically when requested
            if ($node->getNodeAutomount()) {
                $this->mount($name);
            }

            // set requirements
            $this->_drive[$name]->setRequirements($node->nodeRequiresReadable(),
                                                 $node->nodeRequiresWriteable(),
                                                 $node->nodeRequiresExecutable());

        } /* end foreach */
    }

    /**
     * Get source value from XML node.
     *
     * Interates through sources and returns the first existing file or directory in the list.
     * If none exists, it returns the last element in the list.
     *
     * @param   \Yana\VDrive\Configuration  $content  Virtual Drive configuration settings
     * @return  string
     */
    private function _getSource(\Yana\VDrive\Configuration $content)
    {
        $sources = $content->getNodeSources();

        /* default mode
         *
         * When in default mode, always the last (default)
         * element will be used.
         */
        if (self::$_useDefaults) {
            $sources = array(end($sources));
        }

        /* standard mode */
        $source = "";
        foreach ($sources as $source)
        {
            $source = (string) $source;
            if (mb_strpos($source, YANA_LEFT_DELIMITER) !== false) {
                $source = \Yana\Util\String::replaceToken($source, self::$_defaultSettings);
            } else {
                /* intentionally left blank */
            }
            if (!preg_match('/^(\w:|\/)/', $source)) {
                $source = $this->_baseDir . $source;
            }
            if (file_exists($source)) {
                return $source;
            }
        }
        return $source;
    }

    /**
     * Get string represenation of a virtual drive.
     *
     * This returns a human readable overview of the currently loaded virtual drive and
     * it's contents.
     *
     * You might want to use this for debugging purposes.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getReport()->asXML();
    }

    /**
     * Return file contents as string.
     *
     * @return  string
     */
    public function getContent()
    {
        // read file if not already read
        $this->read();
        return $this->_content->__toString();
    }

    /**
     * Get a resource.
     *
     * Returns the file system resource specified by $key, or bool(false) if the resource
     * does not exist, or was unable to return any contents.
     *
     * @name    VDrive::getResource()
     * @param   string  $path  virtual file path
     * @return  \Yana\Files\AbstractResource
     * @throws  \Yana\Core\Exceptions\NotFoundException       when virtual file or directory does not exist.
     * @throws  \Yana\Core\Exceptions\NotReadableException    when source file is not readable
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when the file could not be read or contains invalid syntax
     */
    public function getResource($path)
    {
        assert('is_string($path); // Wrong argument type for argument 1. String expected.');
        // read file if not already read
        $this->read();
        if (!isset($this->_drive[$path])) {
            $message = "No such virtual file or directory '$path'.";
            throw new \Yana\Core\Exceptions\NotFoundException($message, E_USER_WARNING);
        }
        return $this->_drive[$path]->getMountpoint();
    }

    /**
     * Resolves the virtual path and returns the real path of the resource.
     *
     * @param   string  $virtualPath    virtual path that should be converted to real path
     * @return  string
     * @throws  \Yana\Core\Exceptions\NotFoundException       when virtual file or directory does not exist.
     * @throws  \Yana\Core\Exceptions\NotReadableException    when the vDrive is not readable
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when the vDrive could not be read or contains invalid syntax
     */
    public function getResourcePath($virtualPath)
    {
        assert('is_string($virtualPath); // Invalid argument $virtualPath: string expected');

        return $this->getResource($virtualPath)->getPath();
    }

    /**
     * Get list of mountpoints.
     *
     * Returns an array of {@see Mountpoint}s where the keys are the file paths and the values
     * are the mountpoint definitions.
     *
     * @return  array
     */
    public function getMountpoints()
    {
        return $this->_drive;
    }

    /**
     * Check if drive is empty.
     *
     * Returns true if the file containing the VDrive-definition does not exist,
     * is not readable, or is empty.
     * Returns false otherwise.
     *
     * @return  bool
     */
    public function isEmpty()
    {
        return (bool) (!is_readable($this->path) || !is_file($this->path) || !filesize($this->path));
    }

    /**
     * Check a virtual drive for errors and return a report.
     *
     * This is to check a virtual drive for syntax errors and missing files.
     *
     * Returns the a report object.
     *
     * Example:
     * <code>
     * <?xml version="1.0"?>
     * <report>
     *   <text>Base directory: foo/</text>
     *   <report>
     *     <title>bar.file</title>
     *     <text>Type: file</text>
     *     <text>Path: bar.txt</text>
     *     <error>Is not readable ...</error>
     *   </report>
     *   <report>
     *     <title>foo</title>
     *     <text>Type: dir</text>
     *     <text>Path: bar/foo/</text>
     *   </report>
     * </report>
     * </code>
     *
     * @param   \Yana\Report\IsReport  $report  base report
     * @return  \Yana\Report\IsReport
     * @name    VDrive::getReport()
     * @ignore
     */
    public function getReport(\Yana\Report\IsReport $report = null)
    {
        if (is_null($report)) {
            $report = \Yana\Report\Xml::createReport(__CLASS__);
        }
        $report->addText("Base directory: {$this->_baseDir}");

        if (!isset($this->_content)) {
            $report->addWarning("Cannot perform check! Drive is not mounted.");
        } else {
            foreach ($this->_drive as $name => $node)
            {
                $subReport = $report->addReport("$name");
                $node->getReport($subReport);
            }
        } /* end if */
        return $report;
    }

    /**
     * Returns the serialized object as a string.
     *
     * @return  string
     */
    public function serialize()
    {
        // returns a list of key => value pairs
        $properties = get_object_vars($this);
        // remove the \SimpleXML object (throws an exception when trying to serialize)
        unset($properties['_content']);
        return serialize($properties);
    }

    /**
     * Reinitializes the object.
     *
     * @param  string  $string  string to unserialize
     */
    public function unserialize($string)
    {
        foreach (unserialize($string) as $key => $value)
        {
            $this->$key = $value;
        }
        $this->_content = Configuration::loadFile($this->path);
    }

}

?>