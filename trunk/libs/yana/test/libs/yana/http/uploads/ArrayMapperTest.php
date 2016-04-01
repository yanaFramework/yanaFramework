<?php
/**
 * PHPUnit test-case: DbInfoColumn
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
 * @package  test
 * @license  http://www.gnu.org/licenses/gpl.txt
 */

namespace Yana\Http\Uploads;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../../include.php';

/**
 * @package  test
 */
class ArrayMapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Http\Uploads\ArrayMapper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new \Yana\Http\Uploads\ArrayMapper();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @test
     */
    public function testConvertArray()
    {
        $inputArray = array(
            'Outer' => array(
              'name' => array(
                'Inner' => array(
                   'column1' => 'filename1',
                   'column2' => 'filename2',
                   'column4' => 'filename4'
                )
              ),
              'type' => array(
                'Inner' => array(
                   'column1' => 'type1',
                   'column2' => 'type2',
                   'column4' => 'type4'
                )
              ),
              'tmp_name' => array(
                'Inner' => array(
                   'column1' => 'temp_name1',
                   'column2' => 'temp_name2',
                   'column4' => 'temp_name4'
                )
              ),
              'error' => array(
                'Inner' => array(
                   'column1' => 1,
                   'column2' => 2,
                   'column3' => \Yana\Http\Uploads\ErrorEnumeration::NO_FILE,
                   'column4' => \Yana\Http\Uploads\ErrorEnumeration::NO_FILE
                )
              ),
              'size' => array(
                'Inner' => array(
                   'column1' => 1,
                   'column2' => 2,
                   'column4' => 3
                )
              )
            )
        );
        $expectedArray = array(
            'outer' => array(
                'inner' => array(
                    'column1' => array(
                        'name' => 'filename1',
                        'type' => 'type1',
                        'tmp_name' => 'temp_name1',
                        'error' => 1,
                        'size' => 1
                    ),
                    'column2' => array(
                        'name' => 'filename2',
                        'type' => 'type2',
                        'tmp_name' => 'temp_name2',
                        'error' => 2,
                        'size' => 2
                    ),
                    'column4' => array(
                        'name' => 'filename4',
                        'type' => 'type4',
                        'tmp_name' => 'temp_name4',
                        'error' => \Yana\Http\Uploads\ErrorEnumeration::NO_FILE,
                        'size' => 3
                    )
                )
            )
        );
        $this->assertEquals($expectedArray, $this->object->convertArray($inputArray));
    }

}
