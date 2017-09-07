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
 * <<interface>> Translation manager facade.
 *
 * This class may be used to dynamically load additional
 * language files at runtime.
 *
 * @package     yana
 * @subpackage  translations
 */
interface IsFacade extends \Yana\Translations\IsTranslation
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
    public function getLanguage();

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
    public function getCountry();

    /**
     * Returns locale settings.
     *
     * @return  string
     */
    public function getLocale();

    /**
     * Read language strings from a file.
     *
     * You may find valid filenames in the following directory 'languages/<locale>/*.xlf'.
     * Provide the file without path and file extension.
     *
     * You may access the file contents via $language->getVar('some.value')
     *
     * @param   string  $file  name of translation file that should be loaded
     * @return  self
     * @throws  \Yana\Core\Exceptions\Translations\InvalidFileNameException       when the given identifier is invalid
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException                      when the give filename is invalid
     * @throws  \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException  when the language file is not found
     */
    public function readFile($file);

    /**
     * Add a directory to the list of language directories.
     *
     * @param   string  $directory  base directory
     * @return  self
     * @throws  \Yana\Core\Exceptions\NotFoundException   when the chosen directory does not exist
     */
    public function addDirectory($directory);

    /**
     * Set the system locale.
     *
     * @param   string  $selectedLanguage  current language
     * @param   string  $selectedCountry   current country (optional)
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the provided locale is not valid
     * @return  self
     */
    public function setLocale($selectedLanguage, $selectedCountry = "");

}

?>