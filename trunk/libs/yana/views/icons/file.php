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

namespace Yana\Views\Icons;

/**
 * <<entity>> Contains information about an icon file.
 *
 * @package     yana
 * @subpackage  views
 */
class File extends \Yana\Data\Adapters\AbstractEntity implements \Yana\Views\Icons\IsFile
{

    /**
     * @var  string
     */
    private $_id = "";

    /**
     * @var  string
     */
    private $_path = "";

    /**
     * @var  string
     */
    private $_regularExpression = "";

    /**
     * Returns a unique identifier.
     *
     * @return  string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set the identifying value for this entity.
     *
     * @param   string  $id  unique identier
     * @return  self
     */
    public function setId($id)
    {
        assert(is_string($id), 'Invalid argument $id: string expected');
        $this->_id = (string) $id;
        return $this;
    }

    /**
     * Returns path to file.
     *
     * @return  string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Set the path to file.
     *
     * @param   string  $path  file path or URL
     * @return  self
     */
    public function setPath($path)
    {
        assert(is_string($path), 'Invalid argument $path: string expected');
        $this->_path = (string) $path;
        return $this;
    }

    /**
     * Returns regex to find matching icon reference in text.
     *
     * @return  string
     */
    public function getRegularExpression()
    {
        return $this->_regularExpression;
    }

    /**
     * Returns regex to find matching icon reference in text.
     *
     * @param   string  $regex  Perl-compatible regular expression
     * @return  self
     */
    public function setRegularExpression($regex)
    {
        assert(is_string($regex), 'Invalid argument $regex: string expected');
        $this->_regularExpression = (string) $regex;
        return $this;
    }

}

?>