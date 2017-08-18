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
 * Fake Facade.
 *
 * For testing purposes and as a fallback only.
 *
 * @package     yana
 * @subpackage  translations
 */
class NullFacade extends \Yana\Core\Object implements \Yana\Translations\IsFacade
{

    /**
     * Get name of selected language.
     *
     * Returns the name of the currently selected language as a string.
     *
     * Example:
     * Returns 'en' for English, 'de' for German.
     *
     * Technically spoken, this is the name of the
     * sub-directory, where the current language's
     * files are stored. Check the directory
     * "languages/" for a complete list.
     *
     * @return  string
     */
    public function getLanguage()
    {
        return '';
    }

    /**
     * Get name of selected country.
     *
     * Returns the name of the currently selected country as a string.
     *
     * Locale settings may consist of two parts:
     * a language plus a country. For example, 'en-US' for american English.
     *
     * This function returns the country part of the locale.
     *
     * Example:
     * Returns 'en' for English, 'de' for German.
     * May also return complete locales like 'en-US', if specified.
     *
     * @return  string
     */
    public function getCountry()
    {
        return '';
    }

    /**
     * Get name of selected locale.
     *
     * Returns the name of the currently selected locale as a string.
     *
     * Example:
     * Returns 'en' for English, 'de' for German, 'en-US' for american English,
     * or 'de-AU' for austrian German. The country part of the locale is
     * optional.
     *
     * @return  string
     */
    public function getLocale()
    {
        return '';
    }

    /**
     * Read language strings from a file.
     *
     * You may find valid filenames in the following directory 'languages/<locale>/*.xlf'.
     * Provide the file without path and file extension.
     *
     * You may access the file contents via $language->getVar('some.value')
     *
     * This function issues an E_USER_NOTICE if the file does not exist.
     * It returns bool(true) on success and bool(false) on error.
     *
     * @param   string  $file  name of translation file that should be loaded
     * @return  self
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException   when the give filename is invalid
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  when the language file is not found
     */
    public function readFile($file)
    {
        return $this;
    }

    /**
     * Get language string.
     *
     * Example:
     * <code>
     * $language->setVar('foo.bar', 'Hello World');
     * // outputs 'Hello World'
     * print $language->getVar('foo.bar');
     * </code>
     *
     * Note: the key may also refer to a group id. If so the function returns
     * all members of the group as an array.
     *
     * @param   string  $key  translation key (case insensitive)
     * @return  mixed
     */
    public function getVar($key)
    {
        return '';
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
        return false;
    }

    /**
     * Returns a list of all languages.
     *
     * Returns an associative array with a list of ids and names for all installed languages.
     *
     * @return  array
     * @since   3.1.0
     */
    public function getLanguages()
    {
        return array();
    }

    /**
     * Add a directory to the list of language directories.
     *
     * @param   string  $directory  base directory
     * @return  self
     * @throws  \Yana\Core\Exceptions\NotFoundException   when the chosen directory does not exist
     *
     * @ignore
     */
    public function addDirectory($directory)
    {
        return $this;
    }

    /**
     * Set the system locale.
     *
     * @param   string  $selectedLanguage  current language
     * @param   string  $selectedCountry   current country (optional)
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the provided locale is not valid
     * @return  self
     *
     * @ignore
     */
    public function setLocale($selectedLanguage, $selectedCountry = "")
    {
        return $this;
    }

    /**
     * Get meta-info on a language packs.
     *
     * @param   string  $languageName  name of language pack
     * @return  \Yana\Core\MetaData\PackageMetaData
     * @throws  \Yana\Core\Exceptions\NotFoundException  when requested file is not found
     *
     * @ignore
     */
    public function getMetaData($languageName)
    {
        return new \Yana\Core\MetaData\PackageMetaData();
    }

    /**
     * Replace a token within a provided text.
     *
     * Note that this function replaces ALL entities found.
     * If a token refers to a non-existing value it is removed.
     *
     * Example:
     * <code>
     * // assume the token {$foo} is set to 'World'
     * $text = 'Hello {$foo}.';
     * // prints 'Hello World.'
     * print \Yana\Util\Strings::replaceToken($string);
     * </code>
     *
     * @param   string  $string   sting text (look example)
     * @return  string
     */
    public function replaceToken($string)
    {
        return $string;
    }

    /**
     * Adds a class that provides meta-information about a language package.
     *
     * Note that you can have only one meta-data provider per locale.
     * If you add multiple, only the first will be used.
     *
     * @param   \Yana\Core\MetaData\IsDataProvider  $provider  to load information about a language pack
     * @return  self
     */
    public function addMetaDataProvider(\Yana\Core\MetaData\IsDataProvider $provider)
    {
        return $this;
    }

    /**
     * Adds a class that finds and loads translations.
     *
     * @param  \Yana\Translations\TextData\IsDataProvider  $provider  to load the contents of a language pack
     * @return  self
     */
    public function addTextDataProvider(TextData\IsDataProvider $provider)
    {
        return $this;
    }

    /**
     * Returns a container with all known translations.
     *
     * @return  \Yana\Translations\TextData\IsTextContainer
     */
    public function getTranslations()
    {
        return new \Yana\Translations\TextData\TextContainer();
    }

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
     * @return  self
     * @throws  \Yana\Core\Exceptions\Translations\InvalidFileNameException       when the given identifier is invalid
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException                      when the give filename is invalid
     * @throws  \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException  when the language file is not found
     */
    public function loadTranslations($id)
    {
        return $this;
    }

    /**
     * Adds a logger to the class.
     *
     * @param   \Yana\Log\IsLogger  $logger  instance that will handle the logging
     * @return  self
     */
    public function attachLogger(\Yana\Log\IsLogger $logger)
    {
        return $this;
    }

    /**
     * Returns the attached loggers.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger()
    {
        return new \Yana\Log\NullLogger();
    }

}

?>