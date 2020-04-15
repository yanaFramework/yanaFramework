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

namespace Yana\Views\Skins;

/**
 * Skin Manager class.
 *
 * @package     yana
 * @subpackage  views
 */
interface IsSkin extends \Yana\Report\IsReportable
{

    /**
     * Choose a provider to load meta-data.
     *
     * @param   \Yana\Views\MetaData\IsDataProvider  $provider  designated meta-data provider
     * @return  $this
     * @see     \Yana\Views\MetaData\XmlDataProvider
     */
    public function setMetaDataProvider(\Yana\Views\MetaData\IsDataProvider $provider);

    /**
     * Returns the skin's meta information.
     *
     * Use this to get more info on the skin pack's author, title or description.
     *
     * @return  \Yana\Views\MetaData\IsSkinMetaData
     */
    public function getMetaData(): \Yana\Views\MetaData\IsSkinMetaData;

    /**
     * Returns a template definition.
     *
     * @param   string  $templateId  any valid identifier
     * @return  \Yana\Views\MetaData\IsTemplateMetaData
     * @throws  \Yana\Core\Exceptions\NotFoundException  when no matching template was found
     */
    public function getTemplateData(string $templateId): \Yana\Views\MetaData\IsTemplateMetaData;

    /**
     * Returns a list of all skins.
     *
     * Returns an associative array with a list of ids and names for all installed skins.
     *
     * @return  array
     * @since   3.1.0
     */
    public function getSkins(): array;

    /**
     * Returns the name of the skin.
     *
     * The default is 'default'.
     *
     * @return  string
     */
    public function getName(): string;

    /**
     * get this skin's directory path
     *
     * @return  string
     */
    public function getDirectory(): string;

}

?>