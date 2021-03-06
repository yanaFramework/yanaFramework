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
 * Fake Facade.
 *
 * For testing purposes and as a fallback only.
 *
 * @package     yana
 * @subpackage  translations
 * @ignore
 */
class NullFacade extends \Yana\Core\StdObject implements \Yana\Translations\IsFacade, \Yana\Translations\IsTranslationManager
{

    /**
     * Does nothing.
     *
     * @param   \Yana\Translations\IsLocale  $locale  ignored
     * @return  $this
     */
    public function addAcceptedLocale(\Yana\Translations\IsLocale $locale)
    {
        return $this;
    }

    /**
     * Get name of selected language.
     *
     * @return  string
     */
    public function getLanguage(): string
    {
        return '';
    }

    /**
     * Get name of selected country.
     *
     * @return  string
     */
    public function getCountry(): string
    {
        return '';
    }

    /**
     * Get name of selected locale.
     *
     * @return  string
     */
    public function getLocale(): string
    {
        return '';
    }

    /**
     * Read language strings from a file.
     *
     * @param   string  $file  name of translation file that should be loaded
     * @return  $this
     */
    public function readFile(string $file)
    {
        return $this;
    }

    /**
     * Get language string.
     *
     * @param   string  $key  translation key (case insensitive)
     * @return  mixed
     */
    public function getVar($key)
    {
        return '';
    }

    /**
     * Get all language strings.
     *
     * @return  array
     */
    public function getVars()
    {
        return array();
    }

    /**
     * Check if a translation exists.
     *
     * @param   string  $key  translation key (case insensitive)
     * @return  bool
     */
    public function isVar($key)
    {
        return false;
    }

    /**
     * Returns a list of all languages.
     *
     * @return  array
     */
    public function getLanguages(): array 
   {
        return array();
    }

    /**
     * Add a directory to the list of language directories.
     *
     * @param   \Yana\Files\IsDir  $directory  base directory
     * @return  $this
     */
    public function addDirectory(\Yana\Files\IsDir $directory)
    {
        return $this;
    }

    /**
     * Set the system locale.
     *
     * @param   string  $selectedLanguage  current language
     * @param   string  $selectedCountry   current country (optional)
     * @return  $this
     */
    public function setLocale(string $selectedLanguage, string $selectedCountry = "")
    {
        return $this;
    }

    /**
     * Get meta-info on a language packs.
     *
     * @param   string  $languageName  name of language pack
     * @return  \Yana\Core\MetaData\IsPackageMetaData
     */
    public function getMetaData(string $languageName): \Yana\Core\MetaData\IsPackageMetaData
    {
        return new \Yana\Core\MetaData\PackageMetaData();
    }

    /**
     * Replace a token within a provided text.
     *
     * @param   string  $string   sting text (look example)
     * @return  string
     */
    public function replaceToken(string $string): string
    {
        return $string;
    }

    /**
     * Adds a class that provides meta-information about a language package.
     *
     * @param   \Yana\Core\MetaData\IsDataProvider  $provider  to load information about a language pack
     * @return  $this
     */
    public function addMetaDataProvider(\Yana\Core\MetaData\IsDataProvider $provider)
    {
        return $this;
    }

    /**
     * Adds a class that finds and loads translations.
     *
     * @param  \Yana\Translations\TextData\IsDataProvider  $provider  to load the contents of a language pack
     * @return  $this
     */
    public function addTextDataProvider(\Yana\Translations\TextData\IsDataProvider $provider)
    {
        return $this;
    }

    /**
     * Returns a container with all known translations.
     *
     * @return  \Yana\Translations\TextData\IsTextContainer
     */
    public function getTranslations(): \Yana\Translations\TextData\IsTextContainer
    {
        return new \Yana\Translations\TextData\TextContainer();
    }

    /**
     * Read language strings.
     *
     * @param   string  $id  name of translation package that should be loaded
     * @return  $this
     */
    public function loadTranslations(string $id)
    {
        return $this;
    }

    /**
     * Adds a logger to the class.
     *
     * @param   \Yana\Log\IsLogger  $logger  instance that will handle the logging
     * @return  $this
     */
    public function attachLogger(\Yana\Log\IsLogger $logger)
    {
        return $this;
    }

    /**
     * Returns the attached loggers.
     *
     * @return  \Yana\Log\IsLogger
     */
    public function getLogger()
    {
        return new \Yana\Log\NullLogger();
    }

}

?>