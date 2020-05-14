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
 * <<interface>> Configuration.
 *
 * @package     yana
 * @subpackage  db
 */
interface IsConfiguration
{

    /**
     * Returns path to directory where blob-files are stored.
     *
     * @return  string
     */
    public function getDirectory(): string;

    /**
     * Set path to directory where blob-files are stored.
     * 
     * @param   string  $directory
     * @return  $this
     */
    public function setDirectory(string $directory);

    /**
     * Returns data adapter for caching file names.
     *
     * @return  \Yana\Data\Adapters\IsDataAdapter
     */
    public function getFileNameCache(): \Yana\Data\Adapters\IsDataAdapter;

    /**
     * Set data adapter for caching file names.
     *
     * @param   \Yana\Data\Adapters\IsDataAdapter  $fileNameCache  for example session cache
     * @return  $this
     */
    public function setFileNameCache(\Yana\Data\Adapters\IsDataAdapter $fileNameCache);
}

?>