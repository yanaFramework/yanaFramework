<?php
/**
 * YANA library
 *
 * Primary controller class
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
declare(strict_types=1);

namespace Yana\Http\Uploads;

/**
 * To handle access to $_FILES array.
 *
 * @package     yana
 * @subpackage  http
 */
abstract class AbstractUploadWrapper extends \Yana\Core\StdObject implements \Yana\Http\Uploads\IsUploadWrapper
{

    /**
     * @var  array
     */
    private $_files = array();

    /**
     * Initialize files.
     *
     * @param  array  $files  should be converted version of $_FILES
     */
    public function __construct(array $files)
    {
        $this->_files = $files;
    }

    /**
     * Returns list of file settings.
     *
     * @return  mixed
     */
    protected function _getEntry(string $key)
    {
        return \Yana\Util\Hashtable::get($this->_files, \Yana\Util\Strings::toLowerCase($key));
    }

    /**
     * Returns bool(true) if input looks like file settings.
     *
     * @param   mixed  $something  to check
     * @return  bool
     */
    protected function _isFileEntry($something): bool
    {
        return is_array($something)
            && isset($something['name']) && is_string($something['name'])
            && isset($something['type']) && is_string($something['type'])
            && isset($something['tmp_name']) && is_string($something['tmp_name'])
            && isset($something['size']) && is_int($something['size'])
            && isset($something['error']) && is_int($something['error']);
    }

    /**
     * Returns bool(true) if input looks like file settings.
     *
     * @param   mixed  $something  to check
     * @return  bool
     */
    protected function _isValidFile($something): bool
    {
        return $this->_isFileEntry($something) && $something['name'] > "" && $something['tmp_name'] > "";
    }

    /**
     * Returns bool(true) if input looks like file list.
     *
     * @param   mixed  $something  to check
     * @return  bool
     */
    protected function _isList($something): bool
    {
        assert(!isset($isList), 'Cannot redeclare var $isList');
        $isList = is_array($something) && (empty($something['name']) || is_array($something['name']));
        if ($isList) {
            assert(!isset($couldBeFile), 'Cannot redeclare var $couldBeFile');
            foreach ($something as $couldBeFile)
            {
                if (!$this->_isFileEntry($couldBeFile)) {
                    $isList = false;
                    break;
                }
            }
            unset($couldBeFile);
        }
        return $isList;
    }

}

?>