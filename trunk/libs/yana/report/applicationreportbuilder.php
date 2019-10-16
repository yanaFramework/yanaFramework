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

namespace Yana\Report;

/**
 * <<builder>> Adds data to a report.
 *
 * @package    yana
 * @subpackage report
 */
class ApplicationReportBuilder extends \Yana\Core\StdObject
{

    /**
     * @var  \Yana\Report\IsReport
     */
    private $_report = null;

    /**
     * <<constructor>> Initialize report object.
     *
     * @param  \Yana\Report\IsReport  $report  report to add data to
     */
    public function __construct(\Yana\Report\IsReport $report)
    {
        $this->_report = $report;
    }

    /**
     * Return report to work on.
     *
     * @return  \Yana\Report\IsReport
     */
    protected function _getReport()
    {
        return $this->_report;
    }


    /**
     * Run diagnostics and create a system report.
     *
     * This function runs full diagnosicts on all mounted sub-systems.
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
     * @param   \Yana\Application  $application  source application to run diagnostics on
     * @return  \Yana\Report\IsReport
     */
    public function buildApplicationReport(\Yana\Application $application)
    {
        $report = $this->_getReport();

        $this->_buildSystemInfo($application);
        $this->_buildFilesystemReport($application);
        $this->_buildSystemIntegrityReport();
        $this->_buildPluginsReport($application->getPlugins());
        $this->_buildSkinsReport($application->getSkin());
        $this->_buildRegistryReport($application->getRegistry());
        $this->_buildIconIntegrityReport($application->getRegistry());

        return $report;
    }

    /**
     * Build general system information header.
     *
     * @param   \Yana\Application  $application  object to run diagnostics on
     * @return  \Yana\Report\IsReport
     */
    protected function _buildSystemInfo(\Yana\Application  $application)
    {
        $report = $this->_getReport();
        $report->addNotice("installed version of Yana Framework is: " . YANA_VERSION);
        $report->addNotice("installed version of PHP is: " . PHP_VERSION);
        $report->addNotice("current server time is: " . date("r", time()));
        $report->addNotice("running diagnostics on profile: " . $application->getProfileId());

        return $report;
    }

    /**
     * Build general system integrity sub-report.
     *
     * Add a list of MD5 checksums for several important files.
     *
     * @return  \Yana\Report\IsReport
     */
    protected function _buildSystemIntegrityReport()
    {
        $systemIntegrityReport = $this->_getReport()->addReport("System-integrity check");
        $message = "The following list contains the MD5 checksums of several important files. " .
            "Compare these with your own list to see, " .
            "if any of these files have recently been modified without your knowledge.";
        $systemIntegrityReport->addText($message);

        // @codeCoverageIgnoreStart
        if (is_dir('manual')) {
            $message = "You do not need to copy the directory 'manual' to your website. " .
                "It is not required to run the program. You might want to remove it to safe space.";
            $systemIntegrityReport->addNotice($message);
        }
        // @codeCoverageIgnoreEnd

        foreach (glob('./*.php') as $root)
        {
            $root = basename($root);
            // @codeCoverageIgnoreStart
            if (!in_array($root, array('index.php', 'library.php', 'cli.php'))) {
                $message = "Unexpected file '" . $root . "' found. " .
                    "If you did'nt place this file here, " .
                    "it might be the result of an hijacking attempt. " .
                    "You should consider removing this file.";
                $systemIntegrityReport->addWarning($message);
            } else {
                $systemIntegrityReport->addText("{$root} = " . md5_file($root));
            }
            // @codeCoverageIgnoreEnd
        } // end foreach

        return $systemIntegrityReport;
    }

    /**
     * Build report on file system integrity.
     *
     * Check for availability of PEAR-DB.
     * Check availability of configuration file and configuration directory.
     *
     * @param   \Yana\Application  $application  object to run diagnostics on
     * @return  \Yana\Report\IsReport
     */
    protected function _buildFilesystemReport(\Yana\Application  $application)
    {
        $subReport = $this->_getReport()->addReport("Testing installation");

        @include_once "MDB2.php";
        // @codeCoverageIgnoreStart
        if (!class_exists("MDB2", false)) {
            $message = "PHP PEAR-MDB2 module not found. " .
                "Some database systems that work with YANA require PEAR-MDB2 and will not be able to connect unless you install it. " .
                "To install PEAR-MDB2, run pear install MDB2.";
            $subReport->addWarning($message);
        } else {
            $subReport->addText(
                "PHP PEAR-MDB2 found. Yana supports the following drivers: " . \implode(", ", \Yana\Db\Mdb2\DriverEnumeration::getValidItems())
            );
        }
        if (!class_exists('\Doctrine\DBAL\Driver', false)) {
            $message = "Doctrine DBAL module not found. " .
                "Some database systems that work with YANA require Doctrine DBAL and will not be able to connect unless you install it.";
            $subReport->addError($message);
        } else {
            $subReport->addText(
                "Doctrine DBAL found. Supported drivers: " . \implode(", ", \Yana\Db\Doctrine\DriverEnumeration::getValidItems())
            );
        }
        if (!@class_exists('\SQL_Parser')) {
            $message = "PEAR SQL-Parser module not found. " .
                "This class is required to parse SQL statements to query objects for in-memory database simulation. " .
                "Without it, you will not be able to test SQL statements without an actual database. " .
                "To install PEAR SQL-Parser, run pear install SQL_Parser.";
            $subReport->addError($message);
        } else {
            $subReport->addText(
                "PEAR SQL-Parser found. In-memory parsing and simulation of SQL statements available."
            );
        }

        if (YANA_CDROM === true) {
            // @codeCoverageIgnoreStart
            if (!is_writeable(YANA_CDROM_DIR)) {
                $message = "Temporary directory " . YANA_CDROM_DIR . " is not writeable. " .
                    "Set access rights for directory '" .
                    YANA_CDROM_DIR . "' to 777, including all subdirectories and files.";
                $subReport->addError($message);
            }
            // @codeCoverageIgnoreEnd
        } else {
            if (!is_writeable($application->getVar('CONFIGDIR'))) {
                $message = "Configuration directory is not writeable. " .
                    "Set access rights for directory '" . $application->getVar('CONFIGDIR') .
                    "' to 777, including all subdirectories and files.";
                $subReport->addError($message);
            }
            if (!is_writeable($application->getVar('TEMPDIR'))) {
                $message = "Directory for temporary files is not writeable. " .
                    "Set access rights for directory '" . $application->getVar('TEMPDIR') .
                    "' to 777, including all subdirectories and files.";
                $subReport->addError($message);
            }
        }

        return $subReport;
    }

    /**
     * Build plugings sub-report.
     *
     * @param   \Yana\Plugins\Facade  $plugins  object to run diagnostics on
     * @return  \Yana\Report\IsReport
     * @codeCoverageIgnore
     */
    protected function _buildPluginsReport(\Yana\Plugins\Facade $plugins)
    {
        $pluginsReport = $this->_getReport()->addReport("Plugins");
        try {
            $plugins->getReport($pluginsReport);

        } catch (\Exception $e) {
            $pluginsReport->addError($e->getMessage());
        }

        return $pluginsReport;
    }

    /**
     * Build skins sub-report.
     *
     * @param   \Yana\Views\Skins\IsSkin  $skin  object to run diagnostics on
     * @return  \Yana\Report\IsReport
     * @codeCoverageIgnore
     */
    protected function _buildSkinsReport(\Yana\Views\Skins\IsSkin $skin)
    {
        $skinsReport = $this->_getReport()->addReport("Skins");
        try {
            $skin->getReport($skinsReport);

        } catch (\Exception $e) {
            $skinsReport->addError($e->getMessage());
        }

        return $skinsReport;
    }

    /**
     * Build registry sub-report.
     *
     * @param   \Yana\VDrive\IsRegistry  $registry  object to run diagnostics on
     * @return  \Yana\Report\IsReport
     * @codeCoverageIgnore
     */
    protected function _buildRegistryReport(\Yana\VDrive\IsRegistry $registry)
    {
        $registryReport = $this->_getReport()->addReport("Virtual files");
        try {
            $registry->getReport($registryReport);

        } catch (\Exception $e) {
            $registryReport->addError($e->getMessage());
        }

        return $registryReport;
    }

    /**
     * Build icon integrity sub-report.
     *
     * @param   \Yana\VDrive\IsRegistry $registry  object to run diagnostics on
     * @return  \Yana\Report\IsReport
     */
    protected function _buildIconIntegrityReport(\Yana\VDrive\IsRegistry $registry)
    {
        $iconIntegrityReport = $this->_getReport()->addReport("Searching for icon images");

        /* @var $dir \Dir */
        assert(!isset($dir), 'Cannot redeclare var $dir');
        $dir = $registry->getResource('system:/smile');
        /* @var $dir \Yana\Files\Dir */

        // @codeCoverageIgnoreStart
        try {
            $smilies = $dir->listFiles();
        } catch (\Exception $e) {
            $iconIntegrityReport->addError($e->getMessage());
            $smilies = array();
        }
        if (count($smilies)==0) {
            $message = "No Icons found. Please check if the given directory is correct: '" .
                $dir->getPath() . "'.";
            $iconIntegrityReport->addWarning($message);
        } else {
            $iconIntegrityReport->addText(count($smilies) . " Icons found in directory '" . $dir->getPath() . "'.");
            $iconIntegrityReport->addText("No problems found: Directory setting seems to be correct.");
        }
        // @codeCoverageIgnoreEnd
        unset($dir);

        return $iconIntegrityReport;
    }
}

?>