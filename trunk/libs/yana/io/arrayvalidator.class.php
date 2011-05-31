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

namespace Yana\Io;

/**
 * Used for validating array input.
 *
 * @package     yana
 * @subpackage  io
 * @ignore
 */
class ArrayValidator extends AbstractValidator
{

    /**
     * Maximum count of entries in array.
     *
     * @var int
     */
    private $_maxCount = 0;

    /**
     * Binary array of options.
     *
     * @var int
     */
    private $_options = 0;

    /**
     * Convert array to SML-string.
     */
    const TO_SML = 1;

    /**
     * Convert array to XML-string.
     */
    const TO_XML = 2;

    /**
     * Get maximum count of entries in array.
     *
     * @return  int
     */
    protected function getMaxCount()
    {
        return $this->_maxCount;
    }

    /**
     * Get processing options.
     *
     * The options are a binary array of values, taken from this class' constants.
     *
     * @return  int
     */
    protected function getOptions()
    {
        return $this->_options;
    }

    /**
     * Set maximum count of entries in array.
     *
     * @param   int  $maxCount  positive number, 0 = no restrictions
     * @return  ArrayValidator 
     */
    public function setMaxCount($maxCount)
    {
        assert('is_int($maxCount); // Invalid argument $maxCount: int expected');
        assert('$maxCount >= 0; // $maxCount must not be negative');
        $this->_maxCount = (int) $maxCount;
        return $this;
    }

    /**
     * Add processing option.
     *
     * @param   int  $option  any of the class' constants.
     * @return  ArrayValidator 
     */
    public function addOption($option)
    {
        assert('is_int($option); // Invalid argument $option: int expected');
        $this->_options = $this->_options | $option;
        return $this;
    }

    /**
     * Validate a value as array.
     *
     * Returns bool(true) if valid and bool(false) if not.
     *
     * @param   mixed  $array     value to validate
     * @param   int    $maxCount  maximum count of entries in array
     * @return  bool
     */
    public static function validate($array, $maxCount = 0)
    {
        return is_array($array) && (!$maxCount || count($array) <= $maxCount);
    }

    /**
     * Sanitize / convert an array-input.
     *
     * @param   mixed  $array     value to sanitize
     * @param   int    $maxCount  maximum count of entries in array
     * @param   int    $option    any of the class' constants, use bitwise OR to chain options
     * @return  mixed 
     */
    public static function sanitize($array, $maxCount = 0, $options = 0)
    {
        $validator = new self();
        return $validator->setMaxCount($maxCount)
            ->addOption($options)
            ->__invoke($array);
    }

    /**
     * Sanitize / convert an array-input.
     *
     * @param   mixed  $array  value to sanitize
     * @return  mixed 
     */
    public function __invoke($array)
    {
        $array = (array) $array;
        $array = $this->_processMaxCount($array);
        $array = $this->_processOptions($array);
        return $array;
    }

    /**
     * Limit number of items to maximum item-count and return result.
     *
     * @param   array  $array  value to process
     * @return  array 
     */
    protected function _processMaxCount(array $array)
    {
        $length = $this->getMaxCount();
        if ($length > 0 && count($array) > $length) {
            $array = array_slice($array, 0, $length, true);
        }
        return $array;
    }

    /**
     * Execute array conversion- and sanitation-options and return result.
     *
     * Note: should always be called last.
     *
     * @param   array  $array  value to process
     * @return  array 
     */
    protected function _processOptions(array $array)
    {
        $options = $this->getOptions();
        if ($options & self::TO_SML) {

            $array = \SML::encode($array, null, CASE_UPPER);

        } elseif ($options & self::TO_XML) {

            $array = \Hashtable::toXML($array);

        }
        return $array;
    }

}

?>