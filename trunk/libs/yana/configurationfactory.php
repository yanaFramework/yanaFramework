<?php
/**
 * YANA library
 *
 * Primary controller class
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

namespace Yana;

/**
 * <<factory>> Configuration loader.
 *
 * Loads the system configuration from a file and returns it as an instance of XmlArray.
 *
 * @package     yana
 * @subpackage  core
 */
class ConfigurationFactory extends \Yana\Core\StdObject implements \Yana\IsConfigurationFactory
{

    /**
     * Load a system configuration file and return it as an object.
     *
     * The system config file contains default- and startup-settings
     * to initialize this class.
     *
     * @param   string  $filename  path to system.config
     * @return  \Yana\Util\Xml\IsObject
     */
    public function loadConfiguration($filename)
    {
        assert(is_string($filename), 'Wrong type for argument 1. String expected');
        assert(is_file($filename), 'Invalid argument 1. Input is not a file.');
        assert(is_readable($filename), 'Invalid argument 1. Configuration file is not readable.');
        // get System Config file
        $xmlSource = simplexml_load_file($filename);
        $configuration = \Yana\Util\Xml\Converter::convertXmlToObject($xmlSource);
        // load CD-ROM application settings on demand
        if (YANA_CDROM === true) {
            $this->_activateCDApplication($configuration);
        } else {
            $this->_setRealPaths($configuration, getcwd());
        }
        return $configuration;
    }

    /**
     * Set directory references to real paths.
     *
     * @param  \Yana\Util\Xml\IsObject  $configuration  base configuration
     * @param  string                   $cwd            current working directory
     */
    private function _setRealPaths(\Yana\Util\Xml\IsObject $configuration, $cwd)
    {
        $cwd .= '/';
        $configuration->tempdir = $cwd . (string) $configuration->tempdir;
        $configuration->configdir = $cwd . (string) $configuration->configdir;
        $configuration->configdrive = $cwd . (string) $configuration->configdrive;
    }

    /**
     * Activate CD-ROM settings.
     *
     * Sets the configuration to CD-ROM settings.
     * Configuration is expected to be loaded prior to calling this function.
     *
     * @param  \Yana\Util\Xml\IsObject  $configuration  base configuration
     */
    private function _activateCDApplication(\Yana\Util\Xml\IsObject $configuration)
    {
        assert(isset($this->_configuration), 'Configuration must be loaded first');
        if (!file_exists(YANA_CDROM_DIR)) {
            mkdir(YANA_CDROM_DIR);
            chmod(YANA_CDROM_DIR, 0777);
        }
        $configDir = (string) $configuration->configdir;
        $this->_setRealPaths(YANA_CDROM_DIR);
        $tempDir = (string) $configuration->tempdir;
        if (!file_exists($tempDir)) {
            mkdir($tempDir);
            chmod($tempDir, 0777);
        }
        if (!file_exists($configDir)) {
            $configSrc = new \Yana\Files\Dir($configDir);
            $configSrc->copy($configDir, true, 0777, true, null, '/^(?!\.blob$)/i', true);
            unset($configSrc);
        }
        unset($configDir);
    }

}

?>