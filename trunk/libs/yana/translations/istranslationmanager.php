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
interface IsTranslationManager extends \Yana\Translations\IsTranslation
{

    /**
     * Add a directory to the collection of accepted locales.
     *
     * @param   \Yana\Translations\IsLocale  $locale  must correspond to existing translation directory
     * @return  $this
     */
    public function addAcceptedLocale(\Yana\Translations\IsLocale $locale);

    /**
     * Adds a class that provides meta-information about a language package.
     *
     * Note that you can have only one meta-data provider per locale.
     * If you add multiple, only the first will be used.
     *
     * @param   \Yana\Core\MetaData\IsDataProvider  $provider  to load information about a language pack
     * @return  $this
     */
    public function addMetaDataProvider(\Yana\Core\MetaData\IsDataProvider $provider);

    /**
     * Adds a class that finds and loads translations.
     *
     * @param  \Yana\Translations\TextData\IsDataProvider  $provider  to load the contents of a language pack
     * @return  $this
     */
    public function addTextDataProvider(\Yana\Translations\TextData\IsDataProvider $provider);

}

?>