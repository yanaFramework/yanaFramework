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
declare(strict_types=1);

namespace Yana\Translations;

/**
 * <<entity>> Locale settings.
 *
 * Helper class that stores the selected language and country for translations.
 *
 * @package     yana
 * @subpackage  translations
 * @ignore
 */
class Locale extends \Yana\Core\StdObject implements \Yana\Translations\IsLocale
{

    /**
     * @var  string
     */
    private $_language = "";

    /**
     * @var  string
     */
    private $_country = "";

    /**
     * <<constructor>> Initialize settings.
     *
     * @param  string  $language  2 character language code
     * @param  string  $country   2 character country code
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when either language or country are invalid
     */
    public function __construct(string $language = "", string $country = "")
    {
        if ($language > "") {
            $this->setLanguage($language);
        }
        if ($country > "") {
            $this->setCountry($country);
        }
    }

    /**
     * Set the language part of the locale.
     *
     * @param   string  $selectedLanguage  2 character language code
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the provided language is not valid
     * @return  $this
     */
    public function setLanguage(string $selectedLanguage)
    {
        assert(!isset($selectedLanguageLowercased), 'Cannot redeclare var $selectedLanguageLowercased');
        $selectedLanguageLowercased = mb_strtolower($selectedLanguage);

        // check if locale is valid
        if (!preg_match('/^[a-z]{2}$/s', $selectedLanguageLowercased)) {
            $message = "Invalid language string '$selectedLanguage'. Must be exactly 2 characters.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);

        }

        $this->_language = $selectedLanguageLowercased;

        return $this;
    }

    /**
     * Set the country part of the locale.
     *
     * @param   string  $selectedCountry   2 character country code
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the provided country is not valid
     * @return  $this
     */
    public function setCountry(string $selectedCountry)
    {
        assert(!isset($selectedCountryUppercased), 'Cannot redeclare var $selectedCountryUppercased');
        $selectedCountryUppercased = mb_strtoupper($selectedCountry);

        // check if locale is valid
        if (!preg_match('/^([A-Z]{2})?$/s', $selectedCountryUppercased)) {
            $message = "Invalid country string '$selectedCountry'. Must be exactly 2 characters.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
        }

        $this->_country = $selectedCountryUppercased;

        return $this;
    }

    /**
     * Alias of toString().
     *
     * @return  string
     * @ignore
     */
    public function __toString()
    {
        return $this->toString();
    }

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
    public function toString(): string
    {
        assert(!isset($locale), 'Cannot redeclare var $locale');
        $locale = $this->getLanguage();

        assert(!isset($country), 'Cannot redeclare var $country');
        $country = $this->getCountry();
        if (!empty($country)) {
            $locale .= '-' . $country;

        }

        return $locale;
    }

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
    public function getLanguage(): string
    {
        return $this->_language;
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
     * Example: Returns 'EN' for England or 'DE' for Germany.
     *
     * @return  string
     */
    public function getCountry(): string
    {
        return $this->_country;
    }

}

?>