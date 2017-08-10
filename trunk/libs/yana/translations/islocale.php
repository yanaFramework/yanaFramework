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
 * <<interface>> for locale settings.
 *
 * @package     yana
 * @subpackage  translations
 * @ignore
 */
interface IsLocale
{

    /**
     * Set the language part of the locale.
     *
     * @param   string  $selectedLanguage  2 character language code
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the provided language is not valid
     * @return  self
     */
    public function setLanguage($selectedLanguage);

    /**
     * Set the country part of the locale.
     *
     * @param   string  $selectedCountry   2 character country code
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the provided country is not valid
     * @return  self
     */
    public function setCountry($selectedCountry);

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
    public function toString();

    /**
     * Get name of selected language.
     *
     * Returns the name of the currently selected language as a string.
     *
     * Example:
     * Returns 'en' for English, 'de' for German.
     *
     * @internal Technically spoken, this is the name of the sub-directory,
     * where the current language's files are stored.
     * Check the directory "languages/" for a complete list.
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
     * Example: Returns 'EN' for England or 'DE' for Germany.
     *
     * @return  string
     */
    public function getCountry();

}

?>