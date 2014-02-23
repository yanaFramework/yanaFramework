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
 * Language manager.
 *
 * This class may be used to dynamically load additional language strings at runtime.
 *
 * How to use:
 * <code>
 * $manager = new \Yana\Translations\Manager();
 * $manager->attachLogger($logger);
 * $manager->addMetaDataProvider($fileSystemLoader);
 * $manager->addTextDataProvider($defaultLanguageSource);
 * $manager->addTextDataProvider($pluginFooLanguageSource);
 * $manager->addTextDataProvider($pluginBarLanguageSource);
 * $manager->setLocale($selectedLanguage, $selectedCountry);
 * $manager->loadTranslations('translation file or database id')
 * </code>
 *
 * @package     yana
 * @subpackage  translations
 */
interface IsTranslationManager
{

    /**
     * Set the system locale.
     *
     * @param   string  $selectedLanguage  current language
     * @param   string  $selectedCountry   current country (optional)
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the provided locale is not valid
     * @return \Yana\Translations\Language
     */
    public function setLocale($selectedLanguage, $selectedCountry = "");

    /**
     * Get name of selected locale.
     *
     * Returns the name of the currently selected locale as a string.
     *
     * Example:
     * Returns 'en' for English, 'de' for German, 'en-US' for American English,
     * or 'de-AU' for Austrian German. The country part of the locale is
     * optional.
     *
     * @return  string
     */
    public function getLocale();

    /**
     * Adds a class that provides meta-information about a language package.
     *
     * Note that you can have only one meta-data provider per locale.
     * If you add multiple, only the first will be used.
     *
     * @param   \Yana\Core\MetaData\IsDataProvider  $provider  to load information about a language pack
     * @return  \Yana\Translations\Manager
     */
    public function addMetaDataProvider(\Yana\Core\MetaData\IsDataProvider $provider);

    /**
     * Adds a class that finds and loads translations.
     *
     * @param  \Yana\Translations\TextData\IsDataProvider  $provider  to load the contents of a language pack
     * @return  \Yana\Translations\Manager
     */
    public function addTextDataProvider(\Yana\Translations\TextData\IsDataProvider $provider);

    /**
     * Returns the language pack's meta information.
     *
     * Use this to get more info on the language pack's author, title or description.
     *
     * @param   string  $locale  name of language pack
     * @return  \Yana\Core\MetaData\IsPackageMetaData
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the requested language pack is not found
     */
    public function getMetaData($locale = "");

    /**
     * Read language strings.
     *
     * You may find valid filenames in the following directory 'languages/<locale>/*.xlf'.
     * Provide the file without path and file extension.
     *
     * You may access the file contents via $language->getVar('some.value')
     *
     * This function issues an E_USER_NOTICE if the file does not exist.
     * It returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $id  name of translation package that should be loaded
     * @return  \Yana\Translations\Language
     * @throws  \Yana\Core\Exceptions\Translations\InvalidFileNameException  when the given identifier is invalid
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException                 when the give filename is invalid
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException                when the language file is not found
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