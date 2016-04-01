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
 * <<builder>> Builds upload object.
 *
 * @package     yana
 * @subpackage  http
 */
class Builder extends \Yana\Core\Object
{

    /**
     * @var \Yana\Http\Uploads\IsArrayMapper
     */
    private $_arrayMapper = null;

    /**
     * Allows injection of ArrayMapper
     *
     * @param  \Yana\Http\Uploads\IsArrayMapper  $arrayMapper  converts $_FILE array into more manageable form
     */
    public function __construct(\Yana\Http\Uploads\IsArrayMapper $arrayMapper = null)
    {
        $this->_arrayMapper = $arrayMapper;
    }

    /**
     * Get an instance of ArrayMapper.
     *
     * @return  \Yana\Http\Uploads\IsArrayMapper
     */
    protected function _getArrayMapper()
    {
        if (!isset($this->_arrayMapper)) {
            $this->_arrayMapper = new \Yana\Http\Uploads\ArrayMapper();
        }
        return $this->_arrayMapper;
    }

    /**
     * Create UploadWrapper instance using custom settings and return it.
     *
     * @param   array  $files  should follow syntax of $_FILES array
     * @return  \Yana\Http\Uploads\UploadWrapper
     */
    public function __invoke(array $files)
    {
        return new \Yana\Http\Uploads\UploadWrapper($this->_getArrayMapper()->convertArray($files));
    }

    /**
     * Creates UploadWrapper instance using super-global $_FILES array.
     *
     * @return  \Yana\Http\Uploads\UploadWrapper
     */
    public static function buildFromSuperGlobals()
    {
        $builder = new self();
        return $builder($_FILES);
    }

}

?>