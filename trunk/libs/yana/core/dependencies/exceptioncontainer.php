<?php
/**
 * YANA library
 *
 * Primary controller class
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

namespace Yana\Core\Dependencies;

/**
 * Dependency container for the exception base class.
 *
 * @package     yana
 * @subpackage  core
 */
class ExceptionContainer extends \Yana\Core\Object implements \Yana\Core\Dependencies\IsExceptionContainer
{

    /**
     * To load language strings.
     *
     * @var  \Yana\Translations\IsFacade
     */
    private $_language = null;

    /**
     * <<constructor>> Creates an instance.
     *
     * @param  \Yana\Translations\IsFacade  $facade  to be injected
     */
    public function __construct(\Yana\Translations\IsFacade $facade)
    {
        $this->_language = $facade;
    }

    /**
     * Get language translation-repository.
     *
     * This returns the language component. If none exists, a new instance is created.
     *
     * @return  \Yana\Translations\IsFacade
     */
    public function getLanguage()
    {
        return $this->_language;
    }

}

?>