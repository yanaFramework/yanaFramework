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
 * <<abstract>> annotation parser.
 *
 * This is the base class for parsers to extract annotations from a comment text.
 *
 * @package     yana
 * @subpackage  plugins
 */
abstract class AbstractParser extends \Yana\Core\Object implements \Yana\Plugins\Annotations\IsParser
{

    /**
     * @var  string
     */
    private $_text = "";

    /**
     * Initialize instance.
     *
     * @param  string  $text  some text to parse for annotations
     */
    public function __construct($text = "")
    {
        assert('is_string($text)', ' Invalid argument $text: string expected');
        $this->setText($text);
    }

    /**
     * Get comment text.
     *
     * @return  string
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Set comment text.
     *
     * Enter some text to parse for annotations.
     *
     * @param   string  $text  comment text to parse
     * @return  \Yana\Plugins\Annotations\AbstractParser 
     */
    public function setText($text)
    {
        assert('is_string($text)', ' Invalid argument $text: string expected');
        $this->_text = (string) $text;
        return $this;
    }

}

?>