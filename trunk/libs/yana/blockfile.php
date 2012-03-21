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

/**
 * BlockFile
 *
 * @package     yana
 * @subpackage  file_system
 *
 * @ignore
 */
class BlockFile extends File
{

    /**
     * User's IP-adress.
     *
     * @var string
     */
    private $_remoteAddress = "";

    /**
     * Returns the registered IP-address.
     *
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->_remoteAddress;
    }

    /**
     * Set IP-address.
     *
     * @param string $remoteAddress
     * @return \BlockFile 
     */
    public function setRemoteAddress($remoteAddress)
    {
        assert('is_string($remoteAddress); // Invalid argument $remoteAddress: string expected');
        assert('filter_var($remoteAddress, FILTER_VALIDATE_IP); // Not a valid IP-address');
        $this->_remoteAddress = $remoteAddress;
        return $this;
    }

    /**
     * Replace file contents by $input.
     *
     * Note that changes are buffered and will
     * not be written to the file unless you explicitely
     * call write().
     *
     * @param   string  $input new file contents
     * @return  bool
     * @name    BlockFile::setContent()
     */
    public function setContent($input)
    {
        assert('is_string($input); // Wrong type for argument 1. String expected');

        $input = preg_replace("/[;,\s]+/s", "\n", "$input");
        $input = explode("\n", $input);
        assert('is_array($input);');
        $this->content = $input;
        for ($i = 0; $i < count($this->content); $i++)
        {
            $this->content[$i] .= "\n";
        }
        return true;
    }

    /**
     * Alias of BlockFile::setContent()
     *
     * @param   string  $input  new file contents
     * @return  bool
     * @see     BlockFile::setContent()
     * @deprec  since 3.5
     */
    public function set($input)
    {
        return $this->setContent($input);
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
     * Check if the current user has been blocked.
     *
     * Returns bool(true) if the visitor's IP
     * has been black-listed and bool(false) otherwise.
     *
     * @return  bool
     */
    public function isBlocked()
    {
        assert('is_array($this->content);');

        $this->read(); // read file contents
        $this->content = (array) $this->content;

        $_remoteAddress = $this->getRemoteAddress(); // get remote address
        if (empty($_remoteAddress)) {
            return false;
        }

        /* check if remote address is black-listed */
        assert('!isset($line); /* cannot redeclare variable $line */');
        foreach ($this->content as $line)
        {
            $remoteAddress = filter_var((string) $line, FILTER_VALIDATE_IP);
            if ($remoteAddress) {
                $remoteAddress = str_replace('.', '\.', $remoteAddress);
                $remoteAddress = str_replace('*', '[a-fA-F0-9]{,4}', $remoteAddress);
                if (preg_match("/".$remoteAddress."/", $_remoteAddress)) {
                    // match found, return bool(true) (and abort loop)
                    return true;
                } else {
                    // entry does not match (continue with next)
                }
            } else {
                // not an IP-address - treat as comment
            }
        }
        unset($line);

        return false;
    }

}

?>