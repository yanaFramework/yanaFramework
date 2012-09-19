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
 * <<Singleton>> Language
 *
 * This class may be used to dynamically load additional
 * language files at runtime.
 *
 * @package     yana
 * @subpackage  core
 */
class Language extends \Yana\Core\AbstractSingleton implements \Serializable, \Yana\Log\IsLogable, \Yana\Core\IsVarContainer
{

    /**
     * This is a place-holder for the singleton's instance
     *
     * @var  \Yana\Translations\Language
     */
    private static $_instance = null;

    /**
     * a list of all languages installed
     *
     * @var  array
     */
    private $_languages = array();

    /**
     * file extension for language definition files
     *
     * @var  string
     */
    private static $_fileExtension = ".language.xml";

    /**
     * @var  array
     */
    private $_directories = array();

    /**
     * @var  string
     */
    private $_language = "";

    /**
     * @var  string
     */
    private $_country = "";

    /**
     * @var  array
     */
    private $_fileLoaded = array();

    /**
     * @var  array
     */
    private $_strings = array();

    /**
     * @var  array
     */
    private $_groups = array();

    /**
     * language information cache
     *
     * @var  array
     */
    private $_info = array();

    /**
     * cache for valid language directories
     *
     * @var  array
     */
    private $_validDirsCache = array();

    /**
     * Collection for keeping and calling loggers.
     *
     * @var  \Yana\Log\IsLogHandler
     */
    private $_loggers = null;

    /**
     * get instance of this class
     *
     * Creates an instance if there is none.
     * Then it returns a reference to this (single) instance.
     *
     * @return  \Yana\Translations\Language
     */
    public static function &getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * <<Singleton>> Constructor
     *
     * @ignore
     */
    private function __construct()
    {
        // intentionally left blank
    }

    /**
     * Alias of Language::getVar()
     *
     * @param   string  $id   id
     * @return  mixed
     * @see     Language::getVar()
     */
    public function __get($id)
    {
        assert('is_string($id); // Invalid argument $id: string expected');
        return $this->getVar($id);
    }

    /**
     * Alias of Language::setVar()
     *
     * @param   string  $id     id
     * @param   string  $value  value
     * @return  string
     * @see     Language::setVar()
     */
    public function __set($id, $value)
    {
        assert('is_string($id); // Invalid argument $id: string expected');
        assert('is_string($value); // Invalid argument $value: string expected');
        $this->setVar($id, $value);
        return $value;
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
        if (empty($this->_language)) {
            return false;

        } elseif (!empty($this->_country)) {
            return $this->_language . '-' . $this->_country;

        } else {
            return $this->_language;
        }
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
     * @return  \Yana\Translations\Language
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException   when the give filename is invalid
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException  when the language file is not found
     */
    public function readFile($file)
    {
        assert('is_string($file); // Wrong type for argument 1. String expected');

        /**
         * If file is not yet loaded, read it now.
         * Value $this->fileLoaded should be set to true on success and remain false on error.
         */
        if (empty($this->_fileLoaded[$file])) {

            // check syntax of filename
            if (!preg_match("/^[\w_-\d]+$/i", $file)) {
                $message = "The provided language-file id contains illegal characters.".
                    " Be aware that only alphanumeric (a-z,0-9,-,_) characters are allowed.";
                $level = \Yana\Log\TypeEnumeration::INFO;
                $e = new \Yana\Core\Exceptions\Translations\InvalidFileNameException($message, $level);
                throw $e->setFilename($file);
            }

            // override defaults where available
            assert('!isset($directory); // Cannot redeclare var $directory');
            assert('!isset($selectedFile); // Cannot redeclare var $selectedFile');
            foreach ($this->_getValidDirectories() as $directory)
            {
                $selectedFile = "{$directory}{$file}.xlf";
                if (file_exists($selectedFile)) {
                    /*
                     * Read XLIFF-file.
                     *
                     * This tries to read a given language file.
                     * If the file is not valied, it writes a warning to the logs.
                     */
                    try {

                        // LanguageInterchangeFile extends \SimpleXMLElement
                        $xml = new \Yana\Translations\LanguageInterchangeFile($selectedFile, LIBXML_NOENT, true);
                        $this->_strings = $xml->toArray($this->_strings);
                        $this->_groups = $xml->getGroups($this->_groups);
                        $this->_fileLoaded[$file] = true;
                        $this->_strings = array_change_key_case($this->_strings, CASE_LOWER);
                        $this->_groups = array_change_key_case($this->_groups, CASE_LOWER);

                    } catch (\Exception $e) {
                        assert('!isset($message); // Cannot redeclare var $message');
                        $message = "Error in language file: '$file'.";
                        $this->getLogger()->addLog($message, E_USER_WARNING, $e->getMessage());
                        unset($message);
                    }
                }
                unset($selectedFile);
            }
            unset($directory);
        }
        if (empty($this->_fileLoaded[$file])) {
            $message = "No language-file found for id '{$file}'.";
            $level = \Yana\Log\TypeEnumeration::INFO;
            $e = new \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException($message, $level);
            throw $e->setFilename($file);
        }
        return $this;
    }

    /**
     * Get list of validated directories.
     *
     * The list of directories is just a list of base directories.
     * These directories are not specific for the given locale settings.
     *
     * The locale is a combined setting of language code and country code,
     * but may also be just a language.
     *
     * Since checking for the right directories that match the current
     * language settings may take some time, this function caches the results,
     * unless the locale settings are changed.
     *
     * @return  array
     */
    private function _getValidDirectories()
    {
        assert('is_array($this->_validDirsCache);');
        if (empty($this->_validDirsCache)) {
            $this->_validDirsCache = array();
            $this->_validateDirectories($this->_directories);
        }
        assert('is_array($this->_validDirsCache);');
        return $this->_validDirsCache;
    }

    /**
     * Validate directories.
     *
     * @param   array  $directories  list of paths to validate
     */
    private function _validateDirectories($directories)
    {
        foreach ($directories as $directory)
        {
            $this->_validateDirectory($directory);
        }
    }

    /**
     * Validate directory.
     *
     * @param  string  $directory  path to validate
     */
    private function _validateDirectory($directory)
    {
        if (is_dir("$directory{$this->_language}-{$this->_country}")) {
            $this->_validDirsCache[] = "$directory{$this->_language}-{$this->_country}/";
        } elseif (is_dir("$directory{$this->_language}")) {
            $this->_validDirsCache[] = "$directory{$this->_language}/";
        }
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
     * @name    Language::getVar()
     * @see     Language::setVar()
     */
    public function getVar($key)
    {
        assert('is_string($key); /* Wrong argument type for argument 1. String expected. */');

        $key = mb_strtolower((string) $key);

        if (isset($this->_strings[$key])) {
            return $this->_strings[$key];

        } elseif (isset($this->_groups[$key])) {

            $array = array();
            foreach($this->_groups[$key] as $globalId => $localId)
            {
                $array[$localId] = $this->getVar($globalId);
            }
            return $array;

        } else {
            $message = "No text found for key '{$key}'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $this->getLogger()->addLog($message, $level);
            return "$key";
        }
    }

    /**
     * Returns all strings from language file.
     *
     * @param   string  $key  translation key (case insensitive)
     * @return  array
     */
    public function getVars()
    {
        return $this->_strings;
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
        assert('is_string($key); /* Wrong argument type for argument 1. String expected. */');
        $key = mb_strtolower("$key");
        return isset($this->_strings[$key]) || isset($this->_groups[$key]);
    }

    /**
     * Set the translation string specified by parameter $key.
     *
     * Note that the translation is saved even if there is no source text.
     *
     * @param   string  $key    adress of data in memory (case insensitive)
     * @param   string  $value  new value (may be scalar value or array)
     * @return  \Yana\Translations\Language
     * @name    Language::setVar()
     * @see     Language::getVar()
     */
    public function setVar($key, $value)
    {
        return $this->setVarByReference($key, $value);
    }

    /**
     * Set the translation string specified by parameter $key.
     *
     * Note that the translation is saved even if there is no source text.
     *
     * @param   string  $key    adress of data in memory (case insensitive)
     * @param   string  $value  new value (may be scalar value or array)
     * @return  \Yana\Translations\Language
     */
    public function setVarByReference($key, &$value)
    {
        assert('is_string($key); // Wrong argument type for argument 1. String expected.');
        assert('is_string($value); // Wrong argument type for argument 2. String expected.');
        $key = mb_strtolower((string) $key);

        $this->_strings[$key] =& $value;
        return $this;
    }

    /**
     * Replaces the translation strings.
     *
     * @param   array  $values  set of new strings
     * @return  \Yana\Translations\Language
     */
    public function setVars(array $values)
    {
        return $this->setVarsByReference($values);
    }

    /**
     * Replaces the translation strings by reference.
     *
     * @param   array  &$values  set of new strings
     * @return  \Yana\Translations\Language
     */
    public function setVarsByReference(array &$values)
    {
        $values = \array_change_key_case($values, \CASE_LOWER);
        $this->_strings =& $values;
        return $this;
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
        assert('is_array($this->_languages);');
        if (empty($this->_languages)) {
            $this->_languages = array();
            foreach (glob($this->getDefaultDirectory() . "*" . self::$_fileExtension) as $file)
            {
                $id = basename($file, self::$_fileExtension);
                $xml = \simplexml_load_file($file, null, LIBXML_NOWARNING | LIBXML_NOERROR);
                $title = $id;
                if (!empty($xml)) {
                    $title = (string) $xml->title;
                }
                $this->_languages[$id] = $title;
            }
        }
        assert('is_array($this->_languages);');
        return $this->_languages;
    }

    /**
     * Add a directory to the list of language directories.
     *
     * @param   string  $directory  base directory
     * @return \Yana\Translations\Language
     * @throws  \Yana\Core\Exceptions\NotFoundException   when the chosen directory does not exist
     *
     * @ignore
     */
    public function addDirectory($directory)
    {
        assert('is_string($directory); // Wrong type for argument 1. String expected');
        if (!is_dir($directory)) {
            throw new \Yana\Core\Exceptions\NotFoundException("Directory '$directory' does not exist.");
        }
        if (!in_array($directory, $this->_directories)) {
            $this->_directories[] = "$directory/";
            $this->_validateDirectory($directory);
        }
        return $this;
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

        // revalidate directories
        $this->_validateDirectories($this->_directories);
        array_unique($this->_validDirsCache);
        return $this;
    }

    /**
     * Get path of default directory.
     *
     * @return  string
     * @ignore
     */
    protected function getDefaultDirectory()
    {
        assert('is_array($this->_directories);');
        if (isset($this->_directories[0])) {
            return $this->_directories[0];
        } else {
            return "";
        }
    }

    /**
     * Get directories.
     *
     * @return  array
     * @ignore
     */
    public function getDirectories()
    {
        assert('is_array($this->_directories);');
        return $this->_directories;
    }

    /**
     * Get list of info fields.
     *
     * Returns an array of language settings on success,
     * or bool(false) on error.
     *
     * @param   string  $languageName  name of language pack
     * @return  \Yana\Core\PackageMetaData
     * @throws  \Yana\Core\Exceptions\NotFoundException  when requested file is not found
     *
     * @ignore
     */
    public function getInfo($languageName)
    {
        assert('is_string($languageName); // Wrong type for argument 1. String expected');

        if (!isset($this->_info[$languageName])) {
            // get path to definition file
            $file = $this->getDefaultDirectory() . "$languageName.language.xml";
            if (!is_file($file)) {
                throw new \Yana\Core\Exceptions\NotFoundException("Language definition not found: '$languageName'.");
            }
            // load definition
            $xml = simplexml_load_file($file, null, LIBXML_NOWARNING | LIBXML_NOERROR);
            $metaData = new \Yana\Core\PackageMetaData();
            // get information
            if (!empty($xml)) {
                $metaData->setPreviewImage($this->getDefaultDirectory() . "/$languageName/icon.png")
                    ->setLastModified(filemtime($file))
                    ->setTitle((string) $xml->title)
                    ->setUrl((string) $xml->url);
                // get list of authors
                assert('!isset($author); /* cannot redeclare variable $author */');
                $author = $xml->xpath('//author');
                if (!empty($author)) {
                    $metaData->setAuthor((string) implode(', ', $author));
                }
                unset($author);
                // get translated description
                assert('!isset($description); /* cannot redeclare variable $description */');
                $description = $xml->xpath('//description[@lang="'. $this->getLocale() .'"]');
                if (empty($description)) {
                    $description = $xml->xpath('//description[@lang="'. $this->getLanguage() .'"]');
                }
                if (empty($description)) {
                    $description = $xml->xpath('//description[not(@lang)]');
                }
                if (!empty($description)) {
                    $metaData->setText((string) implode(', ', $description));
                }
                unset($description);
            }
            $this->_info[$languageName] = $metaData;
        }
        return $this->_info[$languageName];
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
        // remove the database connection object
        unset($properties['_validDirsCache']);
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

        $pattern = '/'. YANA_LEFT_DELIMITER_REGEXP . 'lang id=["\']([\w_\.]+)["\']' . YANA_RIGHT_DELIMITER_REGEXP .'/';
        $matches = array();
        if (preg_match_all($pattern, $string, $matches)) {
            foreach ($matches[1] as $i => $key)
            {
                $key = mb_strtolower("$key");
                if (isset($this->_strings[$key])) {
                    $value = $this->_strings[$key];

                } else {
                    $value = $key;
                }
                $string = str_replace($matches[0][$i], $value, $string);
            }
        }
        return $string;
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