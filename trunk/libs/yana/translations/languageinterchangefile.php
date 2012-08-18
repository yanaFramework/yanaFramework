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
 * XML Language Interchange File Format (XLIFF).
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
     * Get source language as language code, e.g. en, en-US.
     *
     * @return  string
     */
    public function getSourceLanguage()
    {
        $node = $this->xpath("//file");
        return (string) $node[0]->attributes()->{'source-language'};
    }

    /**
     * Get target language as language code, e.g. en, en-US.
     *
     * @return  string
     */
    public function getTargetLanguage()
    {
        $node = $this->xpath("//file");
        return (string) $node[0]->attributes()->{'target-language'};
    }

    /**
     * Set source language.
     *
     * @param   string  $languageCode  standard language code, e.g. en, en-US
     * @return  \Yana\Translations\LanguageInterchangeFile
     */
    public function setSourceLanguage($languageCode)
    {
        assert('is_string($languageCode); // Wrong argument type for argument 1. String expected.');
        foreach ($this->xpath("//file") as $node)
        {
            /* @var $node SimpleXMLElement */
            $sourceLanguage = $node['source-language'];
            if (empty($sourceLanguage)) {
                $node->addAttribute('source-language', $languageCode);
            } else {
                $node['source-language'] = $languageCode;
            }
        }
        return $this;
    }

    /**
     * Set target language.
     *
     * @param   string  $languageCode  standard language code, e.g. en, en-US
     * @return  \Yana\Translations\LanguageInterchangeFile
     */
    public function setTargetLanguage($languageCode)
    {
        assert('is_string($languageCode); // Wrong argument type for argument 1. String expected.');
        foreach ($this->xpath("//file") as $node)
        {
            /* @var $node SimpleXMLElement */
            $targetLanguage = $node['target-language'];
            if (empty($targetLanguage)) {
                $node->addAttribute('target-language', $languageCode);
            } else {
                $node['target-language'] = $languageCode;
            }
        }
        return $this;
    }

    /**
     * Returns a list of all group-ids and the included translation units.
     *
     * A group is a container for multiple values.
     * The group names are used as array keys for better performance
     * in case you wish to look-up a if a certain group exists.
     *
     * Example input xml:
     * <code>
     *  <group id="group1"/>
     *  <group id="group2">
     *      <trans-unit id="group2.unit1"/>
     *      <bin-unit id="group2.unit2"/>
     *  </group>
     * </code>
     *
     * Produces this output:
     * <code>
     *  array(
     *      "group1" => array(),
     *      "group2" => array(
     *          "group2.unit1" => "unit1"
     *          "group2.unit2" => "unit2"
     *      )
     *  )
     * </code>
     *
     * @param   array  $array  optional base array
     * @return  array
     * @ignore
     */
    public function getGroups(array $array = array())
    {
        foreach ($this->xpath("//group") as $groupNode)
        {
            if (isset($groupNode['id'])) { // Group must have an identifier (the XML schema demands that)
                $groupName = (string) $groupNode['id'];
                // add the group-name to the result array
                $array[$groupName] = array();
                // get the names of translation units that belong to this group
                $groupRegExp = preg_quote($groupName, '/');
                $path = "//group[@id='$groupName']/trans-unit/@id | //group[@id='$groupName']/bin-unit/@id";
                $nodes = $groupNode->xpath($path);

                if (is_array($nodes)) { // skip this if the group has no translations
                    foreach ($nodes as $id)
                    {
                        $id = (string) $id;
                        $localId = preg_replace("/^$groupRegExp\./", '', $id);
                        $array[$groupName][$id] = $localId;
                    }
                }
            }
        }
        return $array;
    }

    /**
     * Returns a list of translation strings as associative array.
     *
     * @param   array  $array  optional base array
     * @return  array
     */
    public function toArray($array = array())
    {
        foreach ($this->xpath("//trans-unit | //bin-unit") as $node)
        {
            $id = (string) $node->attributes()->id;
            $array[$id] = $this->_decodeValue($node->target->asXML());
        }
        return $array;
    }

    /**
     * Takes a translation string and converts all inline tags for output.
     *
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