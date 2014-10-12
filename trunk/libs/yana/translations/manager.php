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
class Manager extends \Yana\Translations\AbstractManager
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
     * Returns the language pack's meta information.
     *
     * Use this to get more info on the language pack's author, title or description.
     *
     * @param   string  $locale  name of language pack
     * @return  \Yana\Core\MetaData\IsPackageMetaData
     * @throws  \Yana\Core\Exceptions\NotFoundException  if the requested language pack is not found
     */
    public function getMetaData($locale = "")
    {
        assert('is_string($locale); // Invalid argument $locale: string expected');
    
        if (empty($locale)) {
            $locale = $this->getLocale();
        }

        return parent::getMetaData($locale);
    }

    /**
     * Set the system locale.
     *
     * @param   string  $selectedLanguage  current language
     * @param   string  $selectedCountry   current country (optional)
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the provided locale is not valid
     * @return \Yana\Translations\Language
     *
     * @ignore
     */
    public function setLocale($selectedLanguage, $selectedCountry = "")
    {
        assert('is_string($selectedLanguage); // Invalid argument $selectedLanguage: string expected');
        assert('is_string($selectedCountry); // Invalid argument $selectedCountry: string expected');

        assert('!isset($selectedLanguageLowercased); // Cannot redeclare var $selectedLanguageLowercased');
        $selectedLanguageLowercased = mb_strtolower($selectedLanguage);
        assert('!isset($selectedCountryUppercased); // Cannot redeclare var $selectedCountryUppercased');
        $selectedCountryUppercased = mb_strtoupper($selectedCountry);

        // check if locale is valid
        if (!preg_match('/^[a-z]{2}$/s', $selectedLanguageLowercased)) {
            $message = "Invalid language string '$selectedLanguage'. Must be exactly 2 characters.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);

        } elseif ("" === $selectedCountry && !preg_match('/^[A-Z]{2}$/s', $selectedCountryUppercased)) {
            $message = "Invalid country string '$selectedCountry'. Must be exactly 2 characters.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
        }

        $this->_language = $selectedLanguageLowercased;
        $this->_country = $selectedCountryUppercased;

        $this->_setSystemLocale();

        return $this;
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
    public function getLocale()
    {
        assert('!isset($locale); // Cannot redeclare var $locale');
        $locale = $this->_getLanguage();

        assert('!isset($country); // Cannot redeclare var $country');
        $country = $this->_getCountry();
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
    protected function _getLanguage()
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
    protected function _getCountry()
    {
        return $this->_country;
    }

    /**
     * Calls setlocale().
     *
     * @internal Since you may not want that in unit-tests, please overwrite this method as needed.
     *
     * @param  string  $locale  new system locale
     * @return  \Yana\Translations\Manager
     */
    protected function _setSystemLocale($locale)
    {
        assert('is_string($locale); // Invalid argument $locale: string expected');

        // set system locale
        setlocale(LC_ALL, $locale);

        return $this;
    }

    /**
     * Replace a token within a provided text.
     *
     * If a token refers to a non-existing value it is removed.
     *
     * @param   string  $string  text including language ids
     * @return  string
     */
    public function replaceToken($string) {
        return $this->_getTranslations()->replaceToken($string);
    }

}

?>