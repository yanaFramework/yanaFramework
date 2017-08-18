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
 * <<interface>> Virtual Drive File Configuration.
 *
 * @package    yana
 * @subpackage vdrive
 */
interface IsConfiguration extends \Yana\Util\IsXmlArray
{

    /**
     * Sets the name of this mountpoint.
     *
     * @param   string  $name  mountpoint-name
     * @return  self
     */
    public function setNodeName($name);

    /**
     * Returns the name of this mountpoint.
     *
     * @return  string
     */
    public function getNodeName();

    /**
     * Returns a the value attribute of a var-node if it is set.
     *
     * @return  string
     */
    public function getNodeValue();

    /**
     * Returns a the namespace attribute of a file-node if it is set.
     *
     * @return  string
     */
    public function getNodeNamespace();

    /**
     * Sets the namespace attribute of a file-node.
     *
     * @param  string  $namespace  a PHP namespace used as prefix for class-names of file wrappers
     * @return \Yana\VDrive\Configuration
     */
    public function setNodeNamespace($namespace);

    /**
     * Sets the filter of this mountpoint.
     *
     * @param   string  $filter  mountpoint-filter
     * @return  self
     */
    public function setNodeFilter($filter);

    /**
     * Returns the filter setting of this mountpoint.
     *
     * @return  string
     */
    public function getNodeFilter();

    /**
     * Sets the auto-mount attribute of this mountpoint.
     *
     * @param   string  $isAutomount  mountpoint-auto-mount setting
     * @return  self
     */
    public function setNodeAutomount($isAutomount);

    /**
     * Returns the auto-mount setting of this mountpoint.
     *
     * The default is bool(false).
     *
     * @return  bool
     */
    public function getNodeAutomount();

    /**
     * Adds a variable definition to the drive.
     *
     * @param   string  $name   name of var
     * @param   string  $value  value of var
     * @return  self
     */
    public function addNodeVar($name, $value);

    /**
     * Returns a list of vars for the drive.
     *
     * @return  array
     */
    public function getNodeVars();

    /**
     * Adds a file definition to the drive.
     *
     * @param   string  $name         name of file
     * @param   bool    $isAutomount  auto-mount setting
     * @return  self
     */
    public function addNodeFile($name, $isAutomount = false);

    /**
     * Returns a list of files for the current node.
     *
     * @return  array
     */
    public function getNodeFiles();

    /**
     * Adds a directory definition to the drive.
     *
     * @param   string  $name       name of directory
     * @param   bool    $automount  auto-mount setting
     * @param   string  $filter     (optional)
     * @return  self
     */
    public function addNodeDir($name, $automount = false, $filter = "");

    /**
     * Returns a list of files for the current node.
     *
     * @return  array
     */
    public function getNodeDirs();

    /**
     * Adds an include path definition to the drive.
     *
     * @param   string  $path   path
     * @return  self
     */
    public function addNodeInclude($path);

    /**
     * Returns a list of include paths for the drive.
     *
     * @return  array
     */
    public function getNodeIncludes();

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
     * @return  self
     */
    public function addNodeSource($path);

    /**
     * Returns a list of sources for the mountpoint, sorted by priority.
     *
     * @return  self
     */
    public function getNodeSources();

    /**
     * Sets wether the resource must be read-, write, and/or executable.
     *
     * @param   string  $readable    (true = is redable , false otherweise)
     * @param   string  $writeable   (true = is writeable , false otherweise)
     * @param   string  $executable  (true = is executable , false otherweise)
     * @return  self
     */
    public function setNodeRequirements($readable = false, $writeable = false, $executable = false);

    /**
     * Returns wether or not the resource must be readable.
     *
     * Default is bool(false).
     *
     * @return  bool
     */
    public function nodeRequiresReadable();

    /**
     * Returns wether or not the resource must be writeable.
     *
     * Default is bool(false).
     *
     * @return  bool
     */
    public function nodeRequiresWriteable();

    /**
     * Returns wether or not the resource must be executable.
     *
     * Default is bool(false).
     *
     * @return  bool
     */
    public function nodeRequiresExecutable();

    /**
     * Returns bool(true) if the node is a dircetory.
     *
     * Contains sub-directories and files.
     *
     * @return  bool
     */
    public function isDir();

    /**
     * Returns bool(true) if the node is a drive configuration.
     *
     * Contains includes, vars, directories and files.
     *
     * @return  bool
     */
    public function isDrive();

    /**
     * Returns bool(true) if the node is a file.
     *
     * Has no children.
     *
     * @return  bool
     */
    public function isFile();

    /**
     * Returns bool(true) if the node is a variable definition.
     *
     * Has no children.
     *
     * @return  bool
     */
    public function isVar();

    /**
     * Returns bool(true) if the node is a class include definition.
     *
     * @return  bool
     */
    public function isInclude();

    /**
     * Returns bool(true) if the node is a file or a directory.
     *
     * @return  bool
     */
    public function isMountpoint();

}

?>