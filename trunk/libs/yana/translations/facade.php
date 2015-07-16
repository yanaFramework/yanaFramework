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
 * <<Singleton>> Facade.
 *
 * This class may be used to dynamically load additional
 * language files at runtime.
 *
 * @package     yana
 * @subpackage  translations
 */
class Facade extends \Yana\Core\AbstractSingleton implements \Serializable, \Yana\Log\IsLogable
{

    /**
     * @var  \Yana\Translations\Manager
     */
    private $_manager = null;

    /**
     * Call manager method
     *
     * @param  string  $name       method name
     * @param  array   $arguments  list of arguments
     */
    public function __call($name, array $arguments)
    {
        return \call_user_func_array(array($this->_getManager(), $name), $arguments);
    }

    /**
     * Lazy-load and return translation-manager.
     *
     * @return  \Yana\Translations\Manager
     */
    protected function _getManager()
    {
        if (!isset($this->_manager)) {
            $this->_manager = new \Yana\Translations\Manager();
        }
        return $this->_manager;
    }

    /**
     * Calls setlocale().
     *
     * @internal Since you may not want that in unit-tests, please overwrite this method as needed.
     *
     * @param  string  $locale  new system locale
     * @return  \Yana\Translations\Manager
     */
    private function _setSystemLocale($locale)
    {
        assert('is_string($locale); // Invalid argument $locale: string expected');

        // set system locale
        setlocale(LC_ALL, $locale);

        return $this;
    }

    /**
     * Used for late-static binding by the parent class.
     *
     * @return  string
     */
    protected static function _getClassName()
    {
        return __CLASS__;
    }

    /**
     * Alias of Facade::getVar()
     *
     * @param   string  $id   id
     * @return  mixed
     */
    public function __get($id)
    {
        assert('is_string($id); // Invalid argument $id: string expected');
        return $this->_getManager()->getVar($id);
    }

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
        return $this->_getManager()->getLocale()->getLanguage();
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
        return $this->_getManager()->getLocale()->getCountry();
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
        return $this->_getManager()->getLocale()->toString();
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
     * @return  \Yana\Translations\Facade
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException   when the give filename is invalid
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  when the language file is not found
     */
    public function readFile($file)
    {
        assert('is_string($file); // Wrong type for argument 1. String expected');

        $this->_getManager()->loadTranslations($file);
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
        assert('is_string($key); // Wrong argument type for argument 1. String expected.');

        return $this->_getManager()->getVar($key);
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
        return $this->_getManager()->isVar($key);
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
        return $this->_getManager()->getLanguages();
    }

    /**
     * Add a directory to the list of language directories.
     *
     * @param   string  $directory  base directory
     * @return  \Yana\Translations\Facade
     * @throws  \Yana\Core\Exceptions\NotFoundException   when the chosen directory does not exist
     *
     * @ignore
     */
    public function addDirectory($directory)
    {
        assert('is_string($directory); // Wrong type for argument 1. String expected');

        $dir = new \Yana\Files\Dir($directory);
        $provider = new \Yana\Translations\TextData\XliffDataProvider($dir);
        $this->_getManager()->addTextDataProvider($provider);
        return $this;
    }

    /**
     * Set the system locale.
     *
     * @param   string  $selectedLanguage  current language
     * @param   string  $selectedCountry   current country (optional)
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the provided locale is not valid
     * @return  \Yana\Translations\Facade
     *
     * @ignore
     */
    public function setLocale($selectedLanguage, $selectedCountry = "")
    {
        assert('is_string($selectedLanguage); // Wrong type for argument 1. String expected');
        assert('is_string($selectedCountry); // Wrong argument type for argument 2. String expected.');

        $locale = $this->_getManager()->getLocale();
        $locale->setLanguage($selectedLanguage)->setCountry($selectedCountry);
        $this->_setSystemLocale($locale->toString());
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
        assert('is_string($languageName); // Wrong type for argument 1. String expected');

        return $this->_getManager()->getMetaData($languageName);
    }

    /**
     * Returns the serialized object as a string.
     *
     * @return  string
     */
    public function serialize()
    {
        // returns a list of key => value pairs
        $properties = get_object_vars($this);
        // return the names
        return serialize($properties);
    }

    /**
     * Reinitializes the object.
     *
     * @param   string  $string  string to unserialize
     */
    public function unserialize($string)
    {
        foreach (unserialize($string) as $key => $value)
        {
            $this->$key = $value;
        }
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
     * print \Yana\Util\String::replaceToken($string);
     * </code>
     *
     * @param   string  $string   sting text (look example)
     * @return  string
     */
    public function replaceToken($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');

        return $this->_getManager()->replaceToken($string);
    }

    /**
     * Adds a logger to the class.
     *
     * @param   \Yana\Log\IsLogger  $logger  instance that will handle the logging
     * @return  \Yana\Translations\Facade
     */
    public function attachLogger(\Yana\Log\IsLogger $logger)
    {
        $this->_getManager()->attachLogger($logger);
        return $this;
    }

    /**
     * Returns the attached loggers.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger()
    {
        return $this->_getManager()->getLogger();
    }

}

?>