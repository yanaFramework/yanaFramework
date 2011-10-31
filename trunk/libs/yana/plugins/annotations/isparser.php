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

namespace Yana\Plugins\Annotations;

/**
 * <<Interface>> annotation parser.
 *
 * This class identifies classes that may be used to parse annotations.
 *
 * @package     yana
 * @subpackage  plugins
 */
interface IsParser
{

    /**
     * Initialize instance.
     *
     * @param  string  $text  some text to parse for annotations
     */
    public function __construct($text = "");

    /**
     * Get doc-tags from comment.
     *
     * Returns a list of all matching annotations as an associative array.
     * It returns the default value (which defaults to an empty array), if no matching tag is found.
     *
     * @param   string  $tagName  name of doc-tag to extract
     * @param   array   $default  is returned if no tag is found
     * @return  array
     */
    public function getTags($tagName, array $default = array());

    /**
     * Get single doc-tag.
     *
     * Returns the doc tag as a string.
     *
     * Use this function if you expect only one tag with a single value.
     * Otherwise the default value is returned (which defaults to an empty string).
     *
     * @param   string  $tagName  name of doc-tag to extract
     * @param   string  $default  returned if not matching tag is found
     * @return  string
     */
    public function getTag($tagName, $default = "");


    /**
     * Get comment text.
     *
     * @return  string
     */
    public function getText();

    /**
     * Set comment text.
     *
     * Enter some text to parse for annotations.
     *
     * @param   string  $text  comment text to parse
     * @return  \Yana\Plugins\Annotations\IsParser
     */
    public function setText($text);

}

?>
