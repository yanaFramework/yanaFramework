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

namespace Yana\Files;

/**
 * Class for IP blocking
 *
 * @package     yana
 * @subpackage  files
 *
 * @ignore
 */
class Block extends \Yana\Files\File
{

    /**
     * Replace file contents by $input.
     *
     * Note that changes are buffered and will
     * not be written to the file unless you explicitely call write().
     *
     * @param   string|array  $input new file contents
     * @return  \Yana\Files\Block
     * @name    Block::setContent()
     */
    public function setContent($input)
    {
        if (!is_array($input)) {
            assert('is_string($input); // Wrong type for argument 1. String expected');
            $input = preg_replace("/[;,\s]+/s", "\n", "$input");
            $input = explode("\n", $input);
        }
        assert('is_array($input);');
        $this->content = $input;
        for ($i = 0; $i < count($this->content); $i++)
        {
            $this->content[$i] .= "\n";
        }
        return $this;
    }

    /**
     * Read the file contents to buffer.
     *
     * @return  bool
     * @throws  \Yana\Core\Exceptions\NotReadableException  if the file is not readable
     * @throws  \Yana\Core\Exceptions\NotFoundException     if the file does not exist
     */
    public function read()
    {
        parent::read();  // inherit functionality from parent

        /**
         * additional actions:
         *
         * There are multiple allowed delitimers. They are: ";", ",", any whitespace.
         * But only "\n" is used automatically. The following code handles the missing ones.
         */
        assert('is_array($this->content);');
        $content = implode("", $this->content); // convert array to string
        $content = trim($content); // remove trailing spaces
        $content = preg_replace("/[;,\s]+/", "\n", $content); // map all delimiters to "\n"
        $this->content = explode("\n", $content); // explode by delimiter
        assert('is_array($this->content);');
    }

    /**
     * Check if the current user has been listed.
     *
     * Returns bool(true) if the user's IP has been listed and bool(false) otherwise.
     *
     * @param   string  $remoteAddress  the user's IP address (IPv4 and IPv6 supported)  
     * @return  bool
     */
    public function isBlocked($remoteAddress)
    {
        assert('is_string($remoteAddress); // Invalid argument $remoteAddress: string expected');
        assert('filter_var($remoteAddress, FILTER_VALIDATE_IP); // Not a valid IP-address');

        assert('is_array($this->content);');

        if (empty($this->content)) { // read file contents
            $this->read();
        }

        assert('!isset($line); // cannot redeclare variable $line');
        foreach ((array) $this->content as $line)
        {
            // convert to regular expression
            $line = str_replace("\n", '', $line);
            $line = preg_quote($line, '/');
            $line = str_replace('\\*', '[a-f0-9]{0,4}', $line);

            if (preg_match("/" . $line . "/i", (string) $remoteAddress)) {
                // match found, return bool(true) (and abort loop)
                return true;
            } else {
                // entry does not match (continue with next)
            }
        }
        unset($line);

        return false;
    }

}

?>