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

namespace Yana\Log\ViewHelpers;

/**
 * <<resultobject>> Stores information about message.
 *
 * @package    yana
 * @subpackage log
 */
class Message extends \Yana\Core\Object
{

    /**
     * @var string
     */
    private $_header = "";

    /**
     * @var string
     */
    private $_text = "";

    /**
     * Returns the message header.
     *
     * @return  string
     */
    public function getHeader()
    {
        return $this->_header;
    }

    /**
     * Set message header.
     *
     * @param   string  $header  new message header
     * @return  \Yana\Log\ViewHelpers\Message
     */
    public function setHeader($header)
    {
        assert('is_string($header); // Invalid argument $header: string expected');

        $this->_header = $header;
        return $this;
    }

    /**
     * Returns the message text.
     *
     * @return  string
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Set message text.
     *
     * @param   string  $text  new message text
     * @return  \Yana\Log\ViewHelpers\Message
     */
    public function setText($text)
    {
        assert('is_string($text); // Invalid argument $text: string expected');

        $this->_text = $text;
        return $this;
    }

}