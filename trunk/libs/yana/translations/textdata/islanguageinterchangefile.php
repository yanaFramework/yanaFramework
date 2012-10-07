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

namespace Yana\Translations\TextData;

/**
 * <<interface>> XML Language Interchange File Format (XLIFF).
 *
 * @package     yana
 * @subpackage  translations
 * @ignore
 */
interface IsLanguageInterchangeFile
{

    /**
     * Get source language as language code, e.g. en, en-US.
     *
     * @return  string
     */
    public function getSourceLanguage();

    /**
     * Get target language as language code, e.g. en, en-US.
     *
     * @return  string
     */
    public function getTargetLanguage();

    /**
     * Returns a list of all group-ids and the included translation units.
     *
     * A group is a container for multiple values.
     * The group names are used as array keys for better performance
     * in case you wish to look-up a if a certain group exists.
     *
     * Example input xml:
     * <code>
     *  <group id="group1"/>
     *  <group id="group2">
     *      <trans-unit id="group2.unit1"/>
     *      <bin-unit id="group2.unit2"/>
     *  </group>
     * </code>
     *
     * Produces this output:
     * <code>
     *  array(
     *      "group1" => array(),
     *      "group2" => array(
     *          "group2.unit1" => "unit1"
     *          "group2.unit2" => "unit2"
     *      )
     *  )
     * </code>
     *
     * @param   array  $array  optional base array
     * @return  array
     * @ignore
     */
    public function getGroups(array $array = array());

    /**
     * Returns a list of translation strings as associative array.
     *
     * @param   array  $array  optional base array
     * @return  array
     */
    public function toArray($array = array());

}

?>