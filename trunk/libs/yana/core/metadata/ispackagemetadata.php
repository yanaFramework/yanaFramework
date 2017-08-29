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

namespace Yana\Core\MetaData;

/**
 * Collection base class.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsPackageMetaData
{

    /**
     * Returns the package title.
     *
     * @return  string
     */
    public function getTitle();

    /**
     * Returns the package description.
     *
     * @param   string  $language  target language
     * @param   string  $country   target country
     * @return  string
     */
    public function getText($language = "", $country = "");

    /**
     * Get time when package was last modified.
     *
     * May return NULL!
     *
     * @return  int|NULL
     */
    public function getLastModified();

    /**
     * Returns the package URL.
     *
     * The URL is meant to point to a website where the user may find additional
     * information about the auhtor or the package itself.
     *
     * @return  string
     */
    public function getUrl();

    /**
     * Returns the name of the author(s) as a string.
     *
     * If none are given, it returns an empty string.
     *
     * @return  string
     */
    public function getAuthor();

    /**
     * Returns the path to a preview image.
     *
     * Note that this function does not check, whether the image exists.
     *
     * @return  string
     */
    public function getPreviewImage();

    /**
     * Returns the version of this package.
     *
     * See the manual on the function version_compare() if you want more information on what
     * version string should look like.
     *
     * @return  string
     */
    public function getVersion();

}

?>