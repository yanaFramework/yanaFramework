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
declare(strict_types=1);

namespace Yana\Translations\TextData;

/**
 * For testing purposes.
 *
 * @package     yana
 * @subpackage  translations
 * @ignore
 */
class NullDataProvider extends \Yana\Core\StdObject implements \Yana\Translations\TextData\IsDataProvider
{

    /**
     * Returns a text container.
     *
     * @param   string                                       $id         name of the object to load
     * @param   \Yana\Translations\Locale                    $locale     Locale to get the translation for
     * @param   \Yana\Translations\TextData\IsTextContainer  $container  container to fill
     * @return  \Yana\Translations\TextData\IsTextContainer
     */
    public function loadOject($id, \Yana\Translations\Locale $locale, \Yana\Translations\TextData\IsTextContainer $container = null)
    {
        if (!$container instanceof \Yana\Translations\TextData\IsTextContainer) {
            $container = new \Yana\Translations\TextData\TextContainer();
        }
        return $container;
    }

}

?>