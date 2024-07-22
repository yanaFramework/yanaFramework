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
 *
 * @ignore
 */

namespace Yana\Translations\TextData;

/**
 * Meta Data provider.
 *
 * @package     yana
 * @subpackage  core
 */
interface IsDataProvider
{

    /**
     * Load text data.
     *
     * If no text container is provided, a new container is created.
     * The strings are then loaded and added to the text container.
     * Already existing strings will be replaced.
     * The container is returned.
     *
     * If the resource identified by the $id-parameter is not found,
     * an exception is thrown.
     *
     * Other exceptions may be thrown, when the resource is found but not valid, aso.
     *
     * @param   string                                       $id         name of the object to load
     * @param   \Yana\Translations\Locale                    $locale     Locale to get the translation for
     * @param   \Yana\Translations\TextData\IsTextContainer  $container  container to fill
     * @return  \Yana\Translations\TextData\IsTextContainer
     * @throws  \Yana\Core\Exceptions\Translations\LanguageFileNotFoundException  when the object for this id is not found
     * @throws  \Yana\Core\Exceptions\Translations\TranslationException           for arbitrary errors
     */
    public function loadOject($id, \Yana\Translations\Locale $locale, ?\Yana\Translations\TextData\IsTextContainer $container = null);

}

?>
