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
abstract class AbstractManager extends \Yana\Core\Object implements \Yana\Log\IsLogable, \Yana\Translations\IsTranslationManager
{

    /**
     * @var  \Yana\Translations\TextData\DataProviderCollection
     */
    private $_textDataProviders = null;

    /**
     * @var  \Yana\Core\MetaData\DataProviderCollection
     */
    private $_metaDataProviders = null;

    /**
     * @var  \Yana\Translations\TextData\IsTextContainer
     */
    private $_translationContainer = null;

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
        $this->_textDataProviders = new \Yana\Translations\TextData\DataProviderCollection();
        $this->_metaDataProviders = new \Yana\Core\MetaData\DataProviderCollection();
        $this->_translationContainer = new \Yana\Translations\TextData\TextContainer();
    }

    /**
     * Adds a class that provides meta-information about a language package.
     *
     * Note that you can have only one meta-data provider per locale.
     * If you add multiple, only the first will be used.
     *
     * @param   \Yana\Core\MetaData\IsDataProvider  $provider  to load information about a language pack
     * @return  \Yana\Translations\Manager
     */
    public function addMetaDataProvider(\Yana\Core\MetaData\IsDataProvider $provider)
    {
        $this->_metaDataProviders[] = $provider;
        return $this;
    }

    /**
     * Adds a class that finds and loads translations.
     *
     * @param  \Yana\Translations\TextData\IsDataProvider  $provider  to load the contents of a language pack
     * @return  \Yana\Translations\Manager
     */
    public function addTextDataProvider(\Yana\Translations\TextData\IsDataProvider $provider)
    {
        $this->_textDataProviders[] = $provider;
        return $this;
    }

    /**
     * Use this collection to load information about language packs.
     *
     * @return \Yana\Core\MetaData\DataProviderCollection
     */
    protected function _getMetaDataProviders()
    {
        return $this->_metaDataProviders;
    }

    /**
     * Use this collection to load translations from language packs.
     *
     * @return \Yana\Translations\TextData\DataProviderCollection
     */
    protected function _getTextDataProviders()
    {
        return $this->_textDataProviders;
    }

    /**
     * Returns a container with all known translations.
     *
     * @return  \Yana\Translations\TextData\IsTextContainer
     */
    protected function _getTranslations()
    {
        return $this->_translationContainer;
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

        assert('!isset($metaData); // Cannot redeclare var $metaData');
        $metaData = null;

        // Iterate over all data sources and search for meta data
        assert('!isset($provider); // Cannot redeclare var $provider');
        foreach ($this->_getMetaDataProviders() as $provider)
        {
            /* @var $provider \Yana\Core\MetaData\IsDataProvider */
            try {
                $metaData = $provider->loadOject($locale); // may throw NotFoundException
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
     * @throws  \Yana\Core\Exceptions\Translations\InvalidFileNameException       when the given identifier is invalid
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException                      when the give filename is invalid
     * @throws  \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException  when the language file is not found
     */
    public function loadTranslations($id)
    {
        assert('is_string($id); // Invalid argument $id: string expected');

        assert('!isset($knownTranslations); // Cannot redeclare var $knownTranslations');
        $knownTranslations = $this->_getTranslations();

        if (!$knownTranslations->isLoaded($id)) { // If pack is already loaded, do nothing.

            // override defaults where available
            assert('!isset($selectedFile); // Cannot redeclare var $selectedFile');
            assert('!isset($provider); // Cannot redeclare var $provider');
            foreach ($this->_getTextDataProviders() as $provider)
            {
                /* @var $provider \Yana\Translations\TextData\IsDataProvider */
                assert($provider instanceof \Yana\Translations\TextData\IsDataProvider);
                /* Try to read a given language pack.
                 * If the id is not valid, write warnings to the logs.
                 */
                try {
                    // The following loads and copies the translations to the container
                    $knownTranslations = $provider->loadOject($id, $knownTranslations); // may throw exception

                } catch (\Yana\Core\Exceptions\Translations\LanguageFileNotFoundException $e) {
                    // Not all sources will have the requested pack.
                    // This is normal as long as at least one has it.
                    unset($e);

                } catch (\Yana\Core\Exceptions\Translations\InvalidSyntaxException $e) {
                    // When a source has been found, but the contents retrieved were invalid.
                    assert('!isset($message); // Cannot redeclare var $message');
                    $message = "Error in language source: '" . $id . "'.";
                    assert('!isset($level); // Cannot redeclare variable $level');
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    $this->getLogger()->addLog($message, $level, $e->getMessage());
                    unset($e, $message, $level);
                }
            }
            unset($provider);

            //  If the pack has not been found by any provider, we need to issue a notice
            if (!$knownTranslations->isLoaded($id)) {
                assert('!isset($message); // Cannot redeclare var $message');
                $message = "No language-pack found for id '{$id}'.";
                assert('!isset($level); // Cannot redeclare variable $level');
                $level = \Yana\Log\TypeEnumeration::INFO;
                assert('!isset($e); // Cannot redeclare variable $e');
                $e = new \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException($message, $level);
                throw $e->setFilename($id);
            }
        }
        return $this;
    }

    /**
     * Returns a list of all languages.
     *
     * Returns an associative array where the keys are the ids
     * and the values are the names for all installed languages.
     *
     * @return  array
     */
    public function getLanguages()
    {
        $languages = array();
        assert('!isset($provider); // Cannot redeclare var $provider');
        /* @var $provider \Yana\Core\MetaData\IsDataProvider */
        foreach ($this->_getMetaDataProviders()->toArray() as $provider)
        {
            assert('!isset($id); // Cannot redeclare var $id');
            /* @var $id string */
            foreach ($provider->getListOfValidIds() as $id)
            {
                if (!isset($languages[$id])) {
                    $metaData = $provider->loadOject($id);
                    $languages[$id] = $metaData->getTitle();
                }
            }
            unset($id);
        }
        unset($provider);

        return $languages;
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

        $translations = $this->_getTranslations();
        $isVar = $translations->isVar($key) || $translations->isGroup($key);
        return $isVar;
    }

    /**
     * Get language string.
     *
     * Note: the key may also refer to a group id. If so the function returns
     * all members of the group as an array.
     *
     * @param   string  $key  translation key (case insensitive)
     * @return  string|array
     */
    public function getVar($key)
    {
        assert('is_string($key); /* Wrong argument type for argument 1. String expected. */');

        $translations = $this->_getTranslations();

        $translationResult = "";
        if ($translations->isVar($key)) {

            $translationResult = $translations->getVar($key);
            assert('is_string($translationResult)');

        } elseif ($translations->isGroup($key)) {

            $translationResult = $translations->getGroupMembers($key);
            assert('is_array($translationResult)');

        } else {
            $message = "No text found for key '{$key}'.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            $this->getLogger()->addLog($message, $level);
            unset($message, $level);
            $translationResult = (string) $key;
        }

        return $translationResult;
    }

    /**
     * Replace a token within a provided text.
     *
     * If a token refers to a non-existing value it is removed.
     *
     * @param   string  $string  text including language ids
     * @return  string
     */
    public function replaceToken($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');

        return $this->_getTranslations()->replaceToken($string);
    }
}

?>