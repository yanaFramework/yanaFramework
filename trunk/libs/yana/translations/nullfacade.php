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
 * Fake Facade.
 *
 * For testing purposes and as a fallback only.
 *
 * @package     yana
 * @subpackage  translations
 */
class NullFacade extends \Yana\Core\Object implements \Yana\Translations\IsFacade
{

    /**
     * Get name of selected language.
     *
     * @return  string
     */
    public function getLanguage()
    {
        return '';
    }

    /**
     * Get name of selected country.
     *
     * @return  string
     */
    public function getCountry()
    {
        return '';
    }

    /**
     * Get name of selected locale.
     *
     * @return  string
     */
    public function getLocale()
    {
        return '';
    }

    /**
     * Read language strings from a file.
     *
     * @param   string  $file  name of translation file that should be loaded
     * @return  self
     */
    public function readFile($file)
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
    public function getLanguages()
    {
        return array();
    }

    /**
     * Add a directory to the list of language directories.
     *
     * @param   string  $directory  base directory
     * @return  self
     */
    public function addDirectory($directory)
    {
        return $this;
    }

    /**
     * Set the system locale.
     *
     * @param   string  $selectedLanguage  current language
     * @param   string  $selectedCountry   current country (optional)
     * @return  self
     */
    public function setLocale($selectedLanguage, $selectedCountry = "")
    {
        return $this;
    }

    /**
     * Get meta-info on a language packs.
     *
     * @param   string  $languageName  name of language pack
     * @return  \Yana\Core\MetaData\PackageMetaData
     */
    public function getMetaData($languageName)
    {
        return new \Yana\Core\MetaData\PackageMetaData();
    }

    /**
     * Replace a token within a provided text.
     *
     * @param   string  $string   sting text (look example)
     * @return  string
     */
    public function replaceToken($string)
    {
        return $string;
    }

    /**
     * Adds a class that provides meta-information about a language package.
     *
     * @param   \Yana\Core\MetaData\IsDataProvider  $provider  to load information about a language pack
     * @return  self
     */
    public function addMetaDataProvider(\Yana\Core\MetaData\IsDataProvider $provider)
    {
        return $this;
    }

    /**
     * Adds a class that finds and loads translations.
     *
     * @param  \Yana\Translations\TextData\IsDataProvider  $provider  to load the contents of a language pack
     * @return  self
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
    public function getTranslations()
    {
        return new \Yana\Translations\TextData\TextContainer();
    }

    /**
     * Read language strings.
     *
     * @param   string  $id  name of translation package that should be loaded
     * @return  self
     */
    public function loadTranslations($id)
    {
        return $this;
    }

    /**
     * Adds a logger to the class.
     *
     * @param   \Yana\Log\IsLogger  $logger  instance that will handle the logging
     * @return  self
     */
    public function attachLogger(\Yana\Log\IsLogger $logger)
    {
        return $this;
    }

    /**
     * Returns the attached loggers.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    public function getLogger()
    {
        return new \Yana\Log\NullLogger();
    }

}

?>