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

namespace Yana\Db\Binaries;

/**
 * <<singleton>> Holds the configuration for the file pool.
 *
 * @package     yana
 * @subpackage  db
 */
class ConfigurationSingleton extends \Yana\Core\AbstractSingleton implements \Yana\Db\Binaries\IsConfiguration
{

    /**
     * @var  string
     */
    private  $_directory = 'config/db/.blob/';

    /**
     * Returns the class name of the called class.
     *
     * @return string
     */
    protected static function _getClassName()
    {
        return __CLASS__;
    }

    /**
     * Returns path to directory where blob-files are stored.
     *
     * @return  string
     */
    public function getDirectory()
    {
        assert('is_dir($this->_directory); // Blob-dir does not exist');
        return $this->_directory;
    }

    /**
     * Set path to directory where blob-files are stored.
     * 
     * @param   string  $directory
     * @return  self
     */
    public function setDirectory($directory)
    {
        assert('is_dir($directory); // Directory does not exist');
        $this->_directory = realpath($directory) . '/';
        return $this;
    }

}

?>