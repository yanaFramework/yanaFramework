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
 * <<interface>> Contains translation strings.
 *
 * @package     yana
 * @subpackage  translations
 */
interface IsTextContainer extends \Yana\Core\IsVarContainer
{

    /**
     * Checks wether the id is marked as loaded.
     *
     * @param   string  $id  alpha-numeric text
     * @return  bool
     */
    public function isLoaded($id);

    /**
     * Marks the id as loaded.
     *
     * @param   string  $id  alpha-numeric text
     * @return  \Yana\Translations\TextData\TextContainer
     */
    public function setLoaded($id);

    /**
     * Add translation strings.
     *
     * The keys are the translation-ids and the values are the translation strings.
     *
     * @param   array  $strings  list of translation strings
     * @return  \Yana\Translations\TextData\TextContainer
     */
    public function addVars(array $strings);

    /**
     * Add translation groups.
     *
     * A group is a container for multiple values.
     * The group names are used as array keys for better performance
     * in case you wish to look-up a if a certain group exists.
     *
     * Your groups should look like this:
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
     * The idea is that you can easily look up all translation-ids in "group2",
     * so that you can get all translations in that group.
     * 
     * @return  array
     */
    public function addGroups(array $groups);

    /**
     * Check wether a given name is a valid group name.
     *
     * @param   string  $groupName  index to check for
     * @return  bool
     */
    public function isGroup($groupName);

    /**
     * Get translation groups.
     *
     * The group names are used as array keys for better performance
     * in case you wish to look-up a if a certain group exists.
     * The values are arrays containing all translation-ids that belong to a certain group.
     * You may use these to retrieve all translations (aka "translation units") that belong to a group.
     *
     * Groups are an optional feature.
     * If there are no groups the list may be empty.
     *
     * @return  array
     */
    public function getGroups();

    /**
     * Returns group settings.
     *
     * The returned associative array has the names of the groups as keys.
     * The values are (again) associative arrays with the fully qualified names of the members
     * as keys and the local names of the group members as values.
     *
     * @param   string  $groupName  index to retrieve
     * @return  array
     */
    public function getGroupMembers($groupName);

    /**
     * Replace a token within a provided text.
     *
     * Note that this function replaces ALL entities found.
     * If a token refers to a non-existing value it is removed.
     *
     * @param   string  $string  text containing tokens like {lang id="FOO"}
     * @return  string
     */
    public function replaceToken(string $string): string;

}

?>