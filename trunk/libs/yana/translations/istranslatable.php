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
 * <<interface>> Classes having a translation manager.
 *
 * Use this interface to indicate that a class has getter/setter functions for a translation manager.
 *
 * @package     yana
 * @subpackage  translations
 */
interface IsTranslatable
{

    /**
     * Attach a translation manager.
     *
     * @param   \Yana\Translations\IsTranslationManager  $manager  loads and provides translation strings
     * @return  self
     */
    public function setTranslationManager(\Yana\Translations\IsTranslationManager $manager);

    /**
     * Returns a translation manager instance.
     *
     * If none was given, an empty instance is provided.
     *
     * @return  \Yana\Translations\IsTranslationManager
     */
    public function getTranslationManager();

}

?>