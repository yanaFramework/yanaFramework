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
 * @package     yana
 * @subpackage  translations
 */
class Manager extends \Yana\Core\Object
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
     * @var  \Yana\Core\MetaData\IsDataProvider[]
     */
    private $_dataProviders = array();

    /**
     * Get name of selected language.
     *
     * Returns the name of the currently selected
     * language as a string, or bool(false) on error.
     *
     * Example:
     * Returns 'en' for English, 'de' for German.
     *
     * Technically spoken, this is the name of the
     * sub-directory, where the current language's
     * files are stored. Check the directory
     * "languages/" for a complete list.
     *
     * @return  string|bool(false)
     * @since   2.9.6
     * @name    Language::getLanguage()
     * @see     Language::getCountry()
     * @see     Language::getLocale()
     */
    public function getLanguage()
    {
        return (!empty($this->_language)) ? $this->_language : false;
    }

    /**
     * Get name of selected country.
     *
     * Returns the name of the currently selected
     * country as a string, or bool(false) on error.
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
     * @return  string|bool(false)
     * @since   3.1.0
     * @name    Language::getCountry()
     * @see     Language::getLocale()
     * @see     Language::getLanguage()
     */
    public function getCountry()
    {
        return (!empty($this->_country)) ? $this->_country : false;
    }

    /**
     * Get name of selected locale.
     *
     * Returns the name of the currently selected
     * locale as a string, or bool(false) on error.
     *
     * Example:
     * Returns 'en' for English, 'de' for German, 'en-US' for american English,
     * or 'de-AU' for austrian German. The country part of the locale is
     * optional.
     *
     * @return  string|bool(false)
     * @since   3.1.0
     * @name    Language::getCountry()
     * @see     Language::getLocale()
     * @see     Language::getLanguage()
     */
    public function getLocale()
    {
        $locale = false;
        if (!empty($this->_country)) {
            $locale = $this->_language . '-' . $this->_country;

        } elseif (!empty($this->_language)) {
            $locale = (string) $this->_language;
        }
        return $locale;
    }

    /**
     * Add a data provider (e.g. pointing to a directory) to the list of sources to search.
     *
     * If the data source is already part of the list, it is not added.
     * 
     * @param   \Yana\Core\MetaData\IsDataProvider  $provider  some data source
     * @return  \Yana\Translations\Manager
     */
    public function addDataProvider(\Yana\Core\MetaData\IsDataProvider $provider)
    {
        if (!\in_array($provider, $this->_dataProviders)) {
            $this->_dataProviders[] = $provider;
        }
        return $this;
    }

    /**
     * Remove a data source.
     *
     * If the data source is not registered, nothing happens.
     * 
     * @param   \Yana\Core\MetaData\IsDataProvider  $provider  some data source
     * @return  \Yana\Translations\Manager
     */
    public function removeDataProvider(\Yana\Core\MetaData\IsDataProvider $provider)
    {
        $key = \array_search($provider, $this->_dataProviders);
        if (\is_int($key)) {
            unset($this->_dataProviders[$key]);
        }
        return $this;
    }

    /**
     * Returns list of all data sources for iteration.
     *
     * The list returned should not contain duplicate entries.
     *
     * @return  \Yana\Core\MetaData\IsDataProvider[]
     */
    public function getDataProviders()
    {
        assert('is_array($this->_dataProviders);');
        return $this->_dataProviders;
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
        assert('is_string($selectedLanguage); // Wrong type for argument 1. String expected');
        assert('is_string($selectedCountry); // Wrong argument type for argument 2. String expected.');

        $selectedLanguage = mb_strtolower($selectedLanguage);
        $selectedCountry = mb_strtoupper($selectedCountry);

        // convert to locale string
        $locale = "$selectedLanguage";
        if ($selectedCountry != "") {
            $locale .= "-$selectedCountry";
        }

        // check if locale is valid
        if (!preg_match('/^[a-z]{2}(-[A-Z]{2})?$/s', $locale)) {
            $message = "Invalid locale setting '$selectedLanguage'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }

        // set system locale
        setlocale(LC_ALL, $locale);

        $this->_language = $selectedLanguage;
        $this->_country = $selectedCountry;

        return $this;
    }

}

?>