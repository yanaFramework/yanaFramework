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

namespace Yana\Core\MetaData;

/**
 * XML meta data file.
 *
 * @package     yana
 * @subpackage  core
 */
class XmlMetaData extends \SimpleXMLElement
{

    /**
     * Executes the XPath and returns the results as string.
     *
     * If no result is found an empty string is returned.
     * If multiple results are found they are given as a comma-seperated list.
     *
     * @param   string  $xpath  must be a valid XPath
     * @return  string
     */
    private function _getItemAsString($xpath)
    {
        assert('is_string($xpath)', ' Invalid argument $xpath: string expected');

        $item = "";

        // We use XPath here since we don't know what element we are at right now
        $items = (array) $this->xpath($xpath);
        if (!empty($items)) {
            $item = (string) implode(', ', $items);
        }

        return $item;
    }

    /**
     * Get package title.
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->_getItemAsString('//title');
    }

    /**
     * Get package descriptions.
     *
     * Returns an array of descriptions, where the keys are the given locales (if any) and the values
     * are the translated strings.
     * If no locale is provided the key will be an empty string.
     *
     * Each locale must only be used once.
     * If multiple entries with the same locale exist, previous entries will get replaced and only the last
     * entry will be returned.
     *
     * @return  array
     */
    public function getDescriptions()
    {
        $descriptions = array();
        // We use XPath here since we don't know what element we are at right now
        $items = $this->xpath('//description');
        if (!empty($items)) {
            foreach ($items as $item)
            {
                $language = (isset($item['lang'])) ? (string) $item['lang'] : '';
                $descriptions[$language] = (string) $item;
            }
        }
        return $descriptions;
    }

    /**
     * Get name(s) of the autor(s).
     *
     * @return  string
     */
    public function getAuthor()
    {
        return $this->_getItemAsString('//author');
    }

    /**
     * Get URL to author's website.
     *
     * This should point the user to a website where more information and/or
     * updates are available for this package.
     *
     * @return  string
     */
    public function getUrl()
    {
        return $this->_getItemAsString('//url');
    }

}

?>