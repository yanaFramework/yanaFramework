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
class Manager extends \Yana\Core\Object implements \Yana\Log\IsLogable
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
     * @var  \Yana\Translations\TextData\DataProviderCollection
     */
    private $_contentDataProviders = null;

    /**
     * @var  \Yana\Core\MetaData\DataProviderCollection
     */
    private $_metaDataProviders = null;

    /**
     * Collection for keeping and calling loggers.
     *
     * @var  \Yana\Log\IsLogHandler
     */
    private $_loggers = null;

    /**
     * Initializes collections
     */
    public function __construct()
    {
        $this->_contentDataProviders = new \Yana\Translations\TextData\DataProviderCollection();
        $this->_metaDataProviders = new \Yana\Core\MetaData\DataProviderCollection();
    }

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
        assert('!isset($locale); // Cannot redeclare var $locale');
        $locale = false;

        if (!empty($this->_country)) {
            $locale = $this->_language . '-' . $this->_country;

        } elseif (!empty($this->_language)) {
            $locale = (string) $this->_language;
        }

        return $locale;
    }

    /**
     * Returns the collection of content-data providers.
     * 
     * @return  \Yana\Translations\TextData\DataProviderCollection
     */
    public function getContentDataProviders()
    {
        return $this->_contentDataProviders;
    }

    /**
     * Returns the collection of meta-data providers.
     * 
     * @return  \Yana\Core\MetaData\DataProviderCollection
     */
    public function getMetaDataProviders()
    {
        return $this->_metaDataProviders;
    }

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

        assert('!isset($metaData); // Cannot redeclare var $metaData');
        $metaData = null;

        // Iterate over all data sources and search for meta data
        assert('!isset($provider); // Cannot redeclare var $provider');
        foreach ($this->getMetaDataProviders() as $provider)
        {
            /* @var $provider \Yana\Core\MetaData\IsDataProvider */
            try {
                $metaData = $provider->loadOject($locale);
                assert($metaData instanceof \Yana\Core\MetaData\IsPackageMetaData);
                break; // Accept the first hit as result
            } catch (\Yana\Core\Exceptions\NotFoundException $e) {
                unset($e); // Not here: try the next one
            }
        }
        unset($provider);
        // $metaData may still be NULL here

        if (!$metaData instanceof \Yana\Core\MetaData\IsPackageMetaData) {
            assert('!isset($message); // Cannot redeclare var $message');
            $message = "Unable to find language pack: '{$locale}'.";
            assert('!isset($level); // Cannot redeclare var $level');
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\NotFoundException($message, $level);
        }

        return $metaData;
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

        // convert to locale string
        assert('!isset($locale); // Cannot redeclare var $locale');
        $locale = "$selectedLanguageLowercased";
        if ($selectedCountryUppercased != "") {
            $locale .= "-" . $selectedCountryUppercased;
        }

        // check if locale is valid
        if (!preg_match('/^[a-z]{2}(-[A-Z]{2})?$/s', $locale)) {
            assert('!isset($message); // Cannot redeclare var $message');
            $message = "Invalid locale setting '{$selectedLanguage}'.";
            assert('!isset($level); // Cannot redeclare var $level');
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, $level);
        }

        // set system locale
        setlocale(LC_ALL, $locale);

        $this->_language = $selectedLanguageLowercased;
        $this->_country = $selectedCountryUppercased;

        return $this;
    }

    /**
     * Adds a logger to the class.
     *
     * @param  \Yana\Log\IsLogger  $logger  instance that will handle the logging
     * @return \Yana\Translations\Language
     */
    public function attachLogger(\Yana\Log\IsLogger $logger)
    {
        $collection = $this->getLogger();
        $collection[] = $logger;
        return $this;
    }

    /**
     * Returns the attached loggers.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger()
    {
        if (!isset($this->_loggers)) {
            $this->_loggers = new \Yana\Log\LoggerCollection();
        }
        return $this->_loggers;
    }

}

?>