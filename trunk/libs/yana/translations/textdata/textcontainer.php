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
 * Contains translation strings.
 *
 * @package     yana
 * @subpackage  translations
 * @ignore
 */
class TextContainer extends \Yana\Core\VarContainer implements \Yana\Translations\TextData\IsTextContainer
{

    /**
     * @var  array
     */
    private $_loaded = array();

    /**
     * @var  array
     */
    private $_groups = array();

    /**
     * Convert a key name to a lower-cased offset string.
     *
     * @param   sclar  $key  some valid identifier, either a number or a non-empty text
     * @return  string
     */
    protected function _toArrayOffset($key)
    {
        assert('is_scalar($key); // Invalid argument $key: string expected');
        return (string) \mb_strtolower($key);
    }

    /**
     * Replace a token within a provided text.
     *
     * Note that this function replaces ALL entities found.
     * If a token refers to a non-existing value it is removed.
     *
     * @param   string  $string  text containing tokens like {lang id="FOO"}
     * @return  string
     */
    public function replaceToken($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');

        assert('!isset($pattern); // Cannot redeclare var $pattern');
        $pattern = '/'. YANA_LEFT_DELIMITER_REGEXP . 'lang id=["\']([\w_\.]+)["\']' . YANA_RIGHT_DELIMITER_REGEXP .'/';
        assert('!isset($matches); // Cannot redeclare var $matches');
        $matches = array();
        // Search for {lang id="($key)"} and replace with translation string
        if (preg_match_all($pattern, $string, $matches)) {
            assert('!isset($i); // Cannot redeclare var $i');
            assert('!isset($key); // Cannot redeclare var $key');
            foreach ($matches[1] as $i => $key)
            {
                assert('!isset($translation); // Cannot redeclare var $translation');
                $translation = ($this->isVar($key)) ? $this->getVar($key) : $key;
                $string = str_replace($matches[0][$i], $translation, $string);
                unset($translation);
            }
            unset($i, $key);
        }
        return $string;
    }

    /**
     * Checks wether the id is marked as loaded.
     *
     * @param   string  $id  alpha-numeric text
     * @return  bool
     */
    public function isLoaded($id)
    {
        assert('is_string($id); // Invalid argument $id: string expected');
        $lowerCasedId = $this->_toArrayOffset($id);
        return !empty($this->_loaded[$lowerCasedId]);
    }

    /**
     * Marks the id as loaded.
     *
     * @param   string  $id  alpha-numeric text
     * @return  \Yana\Translations\TextData\TextContainer
     */
    public function setLoaded($id)
    {
        assert('is_string($id); // Invalid argument $id: string expected');
        $lowerCasedId = $this->_toArrayOffset($id);
        $this->_loaded[$lowerCasedId] = true;
        return $this;
    }

    /**
     * Add translation strings.
     *
     * The keys are the translation-ids and the values are the translation strings.
     *
     * @param   array  $strings  list of translation strings
     * @return  \Yana\Translations\TextData\TextContainer
     */
    public function addVars(array $strings)
    {
        assert('!isset($lcStrings); // Cannot redeclare var $lcStrings');
        $lcStrings = \array_change_key_case($strings, CASE_LOWER);
        // This uses the union operator. It adds all elements of the right array, that are missing in the left array
        // The operator is diffrenent from array_merge() in the sense that it doesn't create duplicate values
        assert('!isset($combinedStrings); // Cannot redeclare var $combinedStrings');
        $combinedStrings = $lcStrings + $this->getVars();
        $this->setVars($combinedStrings);
        return $this;
    }

    /**
     * Add translation groups.
     *
     * A group is a container for multiple values.
     * The group names are used as array keys for better performance
     * in case you wish to check if a certain group exists.
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
    public function addGroups(array $groups)
    {
        assert('!isset($lcGroups); // Cannot redeclare var $lcGroups');
        $lcGroups = \array_change_key_case($groups, CASE_LOWER);
        $this->_groups = \Yana\Util\Hashtable::merge($this->_groups, $lcGroups);
        return $this;
    }

    /**
     * Check wether a given name is a valid group name.
     *
     * @param   string  $groupName  index to check for
     * @return  bool
     */
    public function isGroup($groupName)
    {
        assert('is_string($groupName); // Invalid argument $groupName: string expected');
        $lcGroupName = $this->_toArrayOffset($groupName);
        return isset($this->_groups[$lcGroupName]);
    }

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
    public function getGroups()
    {
        return $this->_groups;
    }

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
    public function getGroupMembers($groupName)
    {
        assert('is_string($groupName); // Invalid argument $groupName: string expected');
        $lcGroupName = $this->_toArrayOffset($groupName);

        $groupMembers = array();

        $groups = $this->getGroups();
        if (isset($groups[$lcGroupName]) && is_array($groups[$lcGroupName])) {

            foreach ($groups[$lcGroupName] as $globalId => $localId)
            {
                $groupMembers[$localId] = $this->getVar($globalId);
            }
            unset($globalId, $localId);

        }
        assert('is_array($groupMembers)');
        return $groupMembers;
    }

    /**
     * Check if a translation exists.
     *
     * Returns bool(true) if the key can be translated and bool(false) otherwise.
     *
     * @param   string  $key  translation key (case insensitive)
     * @return  bool
     */
    public function isVar($key)
    {
        assert('is_string($key); // Wrong argument type for argument 1. String expected.');

        $isVar = parent::isVar($key) || $this->isGroup($key);
        return $isVar;
    }

    /**
     * Get language string.
     *
     * Note: the key may also refer to a group id. If so the function returns
     * all members of the group as an array.
     *
     * @param   string  $key  translation key (case insensitive)
     * @return  string|array
     * @throws  \Yana\Core\Exceptions\Translations\NotFoundException  when the translation is not found
     */
    public function getVar($key)
    {
        assert('is_string($key); // Wrong argument type for argument 1. String expected.');

        $translationResult = "";
        if (parent::isVar($key)) {

            $translationResult = parent::getVar($key);
            assert('is_string($translationResult)');

        } elseif ($this->isGroup($key)) {

            $translationResult = $this->getGroupMembers($key);
            assert('is_array($translationResult)');

        } else {

            throw new \Yana\Core\Exceptions\Translations\NotFoundException("No text found for key '{$key}'.");
        }

        return $translationResult;
    }

}

?>