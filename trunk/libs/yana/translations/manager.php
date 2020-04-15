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
     * Add a directory to the collection of accepted locales.
     *
     * @param  \Yana\Translations\IsLocale  $locale  must correspond to existing translation directory
     * @return  self
     */
    public function addAcceptedLocale(\Yana\Translations\IsLocale $locale)
    {
        $locales = $this->_getAcceptedLocales();

        // Check if the locale already exists, to avoid duplicate values
        assert(!isset($id), 'Cannot redeclare var $id');
        assert(!isset($acceptedLocale), 'Cannot redeclare var $acceptedLocale');
        foreach ($locales as $id => $acceptedLocale)
        {
            if ($acceptedLocale->equals($locale)) {
                $locales->offsetUnset($id);
            }
        }
        unset($acceptedLocale);

        $locales[] = $locale;
        return $this;
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
    public function getMetaData(string $locale): \Yana\Core\MetaData\IsPackageMetaData
    {
        return parent::getMetaData($locale);
    }

    /**
     * Read language strings.
     *
     * You may find valid filenames in the following directory 'languages/<locale>/*.xlf'.
     * Provide the file without path and file extension.
     *
     * You may access the file contents via $language->getVar('some.value').
     *
     * @param   string  $id  name of translation package that should be loaded
     * @return  self
     * @throws  \Yana\Core\Exceptions\Translations\InvalidFileNameException       when the given identifier is invalid
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException                      when the give filename is invalid
     * @throws  \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException  when the language file is not found
     */
    public function loadTranslations(string $id)
    {
        // check syntax of filename
        if (!preg_match("/^[\w_\-\d]+$/i", $id)) {
            $message = "The provided language-file id contains illegal characters.".
                " Be aware that only alphanumeric (a-z,0-9,-,_) characters are allowed.";
            $level = \Yana\Log\TypeEnumeration::INFO;
            $e = new \Yana\Core\Exceptions\Translations\InvalidFileNameException($message, $level);
            throw $e->setFilename($id);
        }

        assert(!isset($knownTranslations), 'Cannot redeclare var $knownTranslations');
        $knownTranslations = $this->getTranslations();

        if (!$knownTranslations->isLoaded($id)) { // If pack is already loaded, do nothing.

            // override defaults where available
            assert(!isset($selectedFile), 'Cannot redeclare var $selectedFile');
            assert(!isset($provider), 'Cannot redeclare var $provider');
            foreach ($this->_getTextDataProviders() as $provider)
            {
                assert(!isset($locale), 'Cannot redeclare var $locale');
                foreach ($this->_getAcceptedLocales() as $locale)
                {
                    /* @var $provider \Yana\Translations\TextData\IsDataProvider */
                    assert($provider instanceof \Yana\Translations\TextData\IsDataProvider);
                    /* Try to read a given language pack.
                     * If the id is not valid, write warnings to the logs.
                     */
                    try {
                        // The following loads and copies the translations to the container
                        $knownTranslations = $provider->loadOject($id, $locale, $knownTranslations); // may throw exception

                    } catch (\Yana\Core\Exceptions\Translations\LanguageFileNotFoundException $e) {
                        // Not all sources will have the requested pack.
                        // This is normal as long as at least one has it.
                        unset($e);

                    } catch (\Yana\Core\Exceptions\Translations\InvalidSyntaxException $e) {
                        // When a source has been found, but the contents retrieved were invalid.
                        assert(!isset($message), 'Cannot redeclare var $message');
                        $message = "Error in language source: '" . $id . "'.";
                        assert(!isset($level), 'Cannot redeclare variable $level');
                        $level = \Yana\Log\TypeEnumeration::WARNING;
                        $this->getLogger()->addLog($message, $level, $e->getMessage());
                        unset($e, $message, $level);
                    }
                }
                unset($locale);
            }
            unset($provider);

            //  If the pack has not been found by any provider, we need to issue a notice
            if (!$knownTranslations->isLoaded($id)) {
                assert(!isset($message), 'Cannot redeclare var $message');
                $message = "No language-pack found for id '{$id}'.";
                assert(!isset($level), 'Cannot redeclare variable $level');
                $level = \Yana\Log\TypeEnumeration::INFO;
                assert(!isset($e), 'Cannot redeclare variable $e');
                $e = new \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException($message, $level);
                throw $e->setFilename($id);
            }
        }
        return $this;
    }

}

?>