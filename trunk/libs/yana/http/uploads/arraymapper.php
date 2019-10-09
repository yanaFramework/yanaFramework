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

namespace Yana\Http\Uploads;

/**
 * Converts $_FILE-array to something more readable.
 *
 * @package     yana
 * @subpackage  http
 */
class ArrayMapper extends \Yana\Core\StdObject implements \Yana\Http\Uploads\IsArrayMapper
{

    /**
     * Build file array from $_FILES input.
     *
     * Converts this:
     * <code>
     * array(
     *   'Outer' => array(
     *     'name' => array(
     *       'Inner' => array(
     *          'column1' => 'filename1',
     *          'column2' => 'filename2'
     *       )
     *     ),
     *     'type' => array(
     *       'Inner' => array(
     *          'column1' => 'type1',
     *          'column2' => 'type2'
     *       )
     *     ),
     *     'tmp_name' => array(
     *       'Inner' => array(
     *          'column1' => 'temp_name1',
     *          'column2' => 'temp_name2'
     *       )
     *     )
     *     // ...
     *   )
     * );
     * </code>
     *
     * To this:
     * <code>
     * array(
     *   'outer' => array(
     *     'inner' => array(
     *       'column1' => array(
     *          'name' => 'filename1',
     *          'type' => 'type1',
     *          'tmp_name' => 'temp_name1'
     *          // ...
     *       ),
     *       'column2' => array(
     *          'name' => 'filename2',
     *          'type' => 'type2',
     *          'tmp_name' => 'temp_name2'
     *          // ...
     *       )
     *     )
     *   )
     * );
     * </code>
     *
     * @param   array  $files  expected to be equal to $_FILE array structure
     * @return  array
     */
    public function convertArray(array $files)
    {
        $filesArray = array();
        foreach (\Yana\Util\Hashtable::changeCase($files) as $name => $file)
        {
            $checkedItem = array();
            foreach ($file as $property => $item)
            {
                if (is_array($item)) {
                    $item = $this->_buildFileArray($item, $property);
                    $checkedItem = \Yana\Util\Hashtable::merge($checkedItem, $item);
                }
            }
            unset($item, $property);
            $filesArray[$name] = $checkedItem;
        }
        unset($file);
        return $this->_removeEmptyFiles($filesArray);
    }

    /**
     * Build file array from $_FILES input.
     *
     * Converts:
     * <code>
     * $property = 'name';
     * $files = array(
     *   'ddldefaultinsertiterator' => array(
     *     'column1' => 'filename1',
     *     'column2' => 'filename2'
     *   )
     * );
     * </code>
     *
     * To this:
     * <code>
     * array(
     *   'ddldefaultinsertiterator' => array(
     *     'column1' => array(
     *       'name' => 'filename1'
     *     ),
     *     'column2' => array(
     *       'name' => 'filename2'
     *     ),
     *   )
     * );
     * </code>
     *
     * @param   mixed   $files     file array or scalar property
     * @param   string  $property  one of: name, type, size, tmp_name, error
     * @return  array
     */
    private function _buildFileArray($files, $property)
    {
        assert(is_array($files) || is_scalar($files), '$files expected to be Array or Scalar');
        if (is_array($files)) {
            $result = array();
            $files = array_change_key_case($files, CASE_LOWER);
            foreach ($files as $key => $item)
            {
                $result[$key] = $this->_buildFileArray($item, $property);
            }
            return $result;
        } else {
            return array($property => $files);
        }
    }

    /**
     * Remove empty (phantom) entries.
     *
     * PHP adds entries to $_FILES array even when no file was uploaded at all, adding a phantom error
     * UPLOAD_ERR_NO_FILE.
     *
     * To avoid bogus error messages and unexpected behavior, we remove these phantom files from the upload list.
     *
     * @param   array  $files  list of files
     * @return  array
     */
    private function _removeEmptyFiles(array $files)
    {
        foreach ($files as $key => &$item)
        {
            if (!isset($item['name']) && isset($item['error']) && $item['error'] === \Yana\Http\Uploads\ErrorEnumeration::NO_FILE) {
                unset($files[$key]);
            } elseif (is_array($item)) {
                $item = $this->_removeEmptyFiles($item);
            }
        }
        return $files;
    }

}

?>