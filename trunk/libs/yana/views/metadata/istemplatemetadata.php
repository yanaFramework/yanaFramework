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
 *
 * @ignore
 */
declare(strict_types=1);

namespace Yana\Views\MetaData;

/**
 * <<interface>> Describes the configuration of a template.
 *
 * @package     yana
 * @subpackage  views
 */
interface IsTemplateMetaData
{

    /**
     * Get template id.
     *
     * @return  string
     */
    public function getId(): string;

    /**
     * Return path to template file.
     *
     * This returns the path and name of the template file associated with
     * the template as it was defined.
     *
     * Note: This function does not check if the defined file actually does exist.
     *
     * @return  string
     */
    public function getFile(): string;

    /**
     * Get list of language ids.
     *
     * The array may contain numeric and string indexes.
     * String indexes are to be used as identifiers.
     *
     * @return  array
     */
    public function getLanguages(): array;

    /**
     * Get list of script files.
     *
     * The array may contain numeric and string indexes.
     * String indexes are to be used as identifiers.
     *
     * @return  array
     */
    public function getScripts(): array;

    /**
     * Get list of stylesheet files.
     *
     * The array may contain numeric and string indexes.
     * String indexes are to be used as identifiers.
     *
     * @return  array
     */
    public function getStyles(): array;

}

?>