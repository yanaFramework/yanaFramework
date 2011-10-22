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

/**
 * XML Language Interchange File Format (XLIFF)
 *
 * Class to provide access to XLIFF files, as used by the language processor.
 *
 * @access     public
 * @package    yana
 * @subpackage core
 * @name       LanguageInterchangeFile
 *
 * @ignore
 */
class LanguageInterchangeFile extends \SimpleXMLElement
{
    /**
     * get source language
     *
     * Returns standard language code, e.g. en, en-US
     *
     * @access  public
     * @return  string
     */
    public function getSourceLanguage()
    {
        $node = $this->xpath("//file");
        return (string) $node[0]->attributes()->{'source-language'};
    }

    /**
     * get target language
     *
     * Returns standard language code, e.g. en, en-US
     *
     * @access  public
     * @return  string
     */
    public function getTargetLanguage()
    {
        $node = $this->xpath("//file");
        return (string) $node[0]->attributes()->{'target-language'};
    }

    /**
     * set source language
     *
     * @access  public
     * @param   string  $languageCode  standard language code, e.g. en, en-US
     */
    public function setSourceLanguage($languageCode)
    {
        assert('is_string($languageCode); // Wrong argument type for argument 1. String expected.');
        foreach ($this->xpath("//file") as $node)
        {
            $sourceLanguage = $node->attributes()->{'source-language'};
            if (empty($sourceLanguage)) {
                $node->addAttribute('source-language', $languageCode);
            } else {
                $sourceLanguage = $languageCode;
            }
        }
    }

    /**
     * set target language
     *
     * @access  public
     * @param   string  $languageCode  standard language code, e.g. en, en-US
     */
    public function setTargetLanguage($languageCode)
    {
        assert('is_string($languageCode); // Wrong argument type for argument 1. String expected.');
        foreach ($this->xpath("//file") as $node)
        {
            $targetLanguage = $node->attributes()->__get('target-language');
            if (empty($targetLanguage)) {
                $node->addAttribute('target-language', $languageCode);
            } else {
                $targetLanguage = $languageCode;
            }
        }
    }

    /**
     * get list of groups
     *
     * Returns a list of all group-ids.
     *
     * A group is a container for multiple values.
     * The group names are used as array keys for better performance
     * in case you wish to look-up a if a certain group exists.
     *
     * @access  public
     * @param   array  &$array  optional base array
     * @return  array
     * @ignore
     */
    public function getGroups(array &$array = array())
    {
        foreach ($this->xpath("//group") as $groupNode)
        {
            $groupName = (string) $groupNode->attributes()->id;
            $array[$groupName] = array();
            $groupRegExp = preg_quote($groupName, '/');
            $path = "//group[@id='$groupName']/trans-unit/@id | //group[@id='$groupName']/bin-unit/@id";
            foreach ($groupNode->xpath($path) as $id)
            {
                $id = (string) $id;
                $localId = preg_replace("/^$groupRegExp\./", '', $id);
                $array[$groupName][$id] = $localId;
            }
        }
        return $array;
    }

    /**
     * get XML content as array
     *
     * Returns a list of translation strings as associative array.
     *
     * @access  public
     * @param   array  &$array  optional base array
     * @return  array
     */
    public function toArray(&$array = array())
    {
        foreach ($this->xpath("//trans-unit | //bin-unit") as $node)
        {
            $id = (string) $node->attributes()->id;
            $array[$id] = $this->_decodeValue($node->target->asXML());
        }
        return $array;
    }

    /**
     * convert a string
     *
     * Takes a translation string and converts all inline tags for output.
     *
     * @access  private
     * @param   string  $string  string to convert
     * @return  string
     */
    private function _decodeValue($string)
    {
        $string = strip_tags($string);
        $string = html_entity_decode($string);
        return $string;
    }
}

?>