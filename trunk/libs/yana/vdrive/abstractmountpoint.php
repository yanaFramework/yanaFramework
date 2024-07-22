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
 * @package    yana
 * @license    http://www.gnu.org/licenses/gpl.txt
 */
declare(strict_types=1);

namespace Yana\VDrive;

/**
 * <<abstract>> Virtual Drive Mountpoint.
 *
 * An abstract super class for all mountpoints.
 *
 * @package    yana
 * @subpackage vdrive
 *
 * @ignore
 */
abstract class AbstractMountpoint extends \Yana\Core\StdObject implements \Yana\Report\IsReportable
{

    /**
     * @var \Yana\Files\IsReadable
     * @ignore
     */
    protected $mountpoint = null;

    /**
     * @var bool
     * @ignore
     */
    protected $isMounted = false;

    /**
     * @var string
     * @ignore
     */
    protected $type = "";

    /**
     * @var string
     * @ignore
     */
    protected $path = "";

    /**
     * @var bool
     */
    private $_requiresReadable = false;

    /**
     * @var bool
     */
    private $_requiresWriteable = false;

    /**
     * @var bool
     */
    private $_requiresExecutable = false;

    /**
     * Constructor.
     *
     * Should initialize the following properties:
     * <ul>
     *   <li> type </li>
     *   <li> path </li>
     *   <li> mountpoint </li>
     * </ul>
     *
     * @param   string  $path  path to the source file
     * @return  bool
     */
    abstract public function __construct($path);

    /**
     * Mount the current resource.
     *
     * Returns bool(true) on success and bool(false) on error.
     * In this case "mounting" means, it calls the function "read()"
     * on the resource object, if it has it.
     *
     * @return  bool
     */
    public function mount()
    {
        assert(is_bool($this->isMounted), 'Unexpected member type for $this->isMounted. Boolean expected.');

        // check if resource is already mounted
        if ($this->isMounted === true) {
            return true;
        }
        // check if resource is valid
        if (!($this->mountpoint instanceof \Yana\Files\IsReadable)) {
            return false;
        }
        if ($this->mountpoint->exists()) {
            $this->mountpoint->read();
        }
        $this->isMounted = true;
        return true;
    }

    /**
     * Returns the mounted file resource or bool(false) if none is present.
     *
     * @return  \Yana\Files\IsReadable|bool(false)
     */
    public function getMountpoint()
    {
        if (isset($this->mountpoint) && is_object($this->mountpoint)) {
            return $this->mountpoint;
        } else {
            return false;
        }
    }

    /**
     * Get source-path attribute.
     *
     * Return the source-path of the file represented by this mountpoint as a string or bool(false) on error.
     *
     * @return  string|bool(false)
     */
    public function getPath()
    {
        assert(is_string($this->path), 'Unexpected member type for $this->path. String expected.');
        if (!empty($this->path)) {
            return (string) $this->path;
        } else {
            return false;
        }
    }

    /**
     * Get type attribute.
     *
     * Return the class-name (type) of the implementing class of this mountpoint as a string or bool(false) on error.
     *
     * @return  string|bool(false)
     */
    public function getType()
    {
        assert(is_string($this->type), 'Unexpected member type for $this->type. String expected.');
        if (!empty($this->type)) {
            return (string) $this->type;
        } else {
            return false;
        }
    }

    /**
     * Get a report of this mountpoint.
     *
     * Returns a report, which you may print, transform or output to a file.
     *
     * @param   \Yana\Report\IsReport  $report  base report
     * @return  \Yana\Report\IsReport
     */
    public function getReport(?\Yana\Report\IsReport $report = null)
    {
        if (is_null($report)) {
            $report = \Yana\Report\Xml::createReport(__CLASS__);
        }

        if (isset($this->mountpoint)) {
            $report->addText("Type: {$this->type}");
            $report->addText("Path: {$this->path}");            

            if ($this->requiresReadable() && !is_readable($this->path)) {
                $message = "Is not readable. Please make sure the file exists AND this program has permission ".
                    "to open it.";
                $report->addError($message);
            }

            if ($this->requiresWriteable() && !is_writeable($this->path)) {
                $message = "Is not writeable. Please make sure the file exists AND this program has permission ".
                    "to write at the file and the directory containing it.";
                $report->addError($message);
            }

            if ($this->requiresExecutable() && !is_executable($this->path)) {
                $message = "Is not executable. Please make sure the file exists AND this program has permission ".
                    "to access it.";
                $report->addError($message);
            }

        } elseif (empty($this->mountpoint)) {
            $report->addText("undefined Mountpoint");

        } else {
            $report->addError("invalid Mountpoint");

        }

        return $report;
    }

    /**
     * Return a textual representation.
     *
     * @return  string
     */
    public function __toString()
    {
        return (string) $this->getReport();
    }

    /**
     * Check if mountpoint has been mounted.
     *
     * Return bool(true) if mount() has been called on the object
     * and bool(false) otherwise.
     *
     * @return  bool
     */
    public function isMounted()
    {
        assert(is_bool($this->isMounted), 'Unexpected member type for $this->isMounted. Boolean expected.');
        return (bool) $this->isMounted;
    }

    /**
     * Set requirements.
     *
     * Sets wether the resource must be read-, write, and/or executable.
     *
     * @param   bool  $readable       (true = is readable, false otherweise)
     * @param   bool  $writeable      (true = is writeable, false otherweise)
     * @param   bool  $executable     (true = is executable, false otherweise)
     */
    public function setRequirements($readable = false, $writeable = false, $executable = false)
    {
        assert(is_bool($readable), 'Invalid argument $readable: bool expected');
        assert(is_bool($writeable), 'Invalid argument $writeable: bool expected');
        assert(is_bool($executable), 'Invalid argument $executable: bool expected');
        $this->_requiresReadable = (bool) $readable;
        $this->_requiresWriteable = (bool) $writeable;
        $this->_requiresExecutable = (bool) $executable;
    }

    /**
     * Must be readable?
     *
     * Returns wether or not the resource must be readable.
     * Default is bool(false).
     *
     * @return  bool
     */
    public function requiresReadable()
    {
        assert(is_bool($this->_requiresReadable), 'Unexpected type for instance property "requiresReadable". Bool expected');
        return (bool) $this->_requiresReadable;
    }

    /**
     * Must be writeable?
     *
     * Returns wether or not the resource must be writeable.
     * Default is bool(false).
     *
     * @return  bool
     */
    public function requiresWriteable()
    {
        assert(is_bool($this->_requiresWriteable), 'Unexpected type for instance property "requiresWriteable". Bool expected');
        return (bool) $this->_requiresWriteable;
    }

    /**
     * Must be executable?
     *
     * Returns wether or not the resource must be executable.
     * Default is bool(false).
     *
     * @return  bool
     */
    public function requiresExecutable()
    {
        assert(is_bool($this->_requiresExecutable), 'Unexpected type for instance property "requiresExecutable". Bool expected');
        return (bool) $this->_requiresExecutable;
    }

}

?>
