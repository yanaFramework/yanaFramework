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

namespace Yana\Translations;

/**
 * <<interface>> Wraps translation functionality.
 *
 * @package     yana
 * @subpackage  translations
 */
interface IsTranslation extends \Yana\Log\IsLogable
{

    /**
     * Returns a container with all known translations.
     *
     * @return  \Yana\Translations\TextData\IsTextContainer
     */
    public function getTranslations();

    /**
     * Returns the language pack's meta information.
     *
     * Use this to get more info on the language pack's author, title or description.
     *
     * @param   string  $locale  name of language pack
     * @return  \Yana\Core\MetaData\IsPackageMetaData
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the requested language pack is not found
     */
    public function getMetaData($locale);

    /**
     * Read language strings.
     *
     * You may find valid filenames in the following directory 'languages/<locale>/*.xlf'.
     * Provide the file without path and file extension.
     *
     * You may access the file contents via $language->getVar('some.value').
     *
     * @param   string  $id  name of translation package that should be loaded
     * @return  self
     * @throws  \Yana\Core\Exceptions\Translations\InvalidFileNameException       when the given identifier is invalid
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException                      when the give filename is invalid
     * @throws  \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException  when the language file is not found
     */
    public function loadTranslations($id);

    /**
     * Returns a list of all languages.
     *
     * Returns an associative array where the keys are the ids
     * and the values are the names for all installed languages.
     *
     * @return  array
     */
    public function getLanguages();

    /**
     * Alias of getVar().
     *
     * @param   string  $key  translation key (case insensitive)
     * @return  string|array
     */
    public function __get($id);

    /**
     * Get language string.
     *
     * Note: the key may also refer to a group id. If so the function returns
     * all members of the group as an array.
     *
     * @param   string  $key  translation key (case insensitive)
     * @return  string|array
     */
    public function getVar($key);

    /**
     * Check if a translation exists.
     *
     * Returns bool(true) if the key can be translated and bool(false) otherwise.
     *
     * @param   string  $key  translation key (case insensitive)
     * @return  bool
     */
    public function isVar($key);

    /**
     * Replace a token within a provided text.
     *
     * If a token refers to a non-existing value it is removed.
     *
     * @param   string  $string  text including language ids
     * @return  string
     */
    public function replaceToken($string);

}

?>