<?php
/**
 * PHPUnit test-case
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

namespace Yana\Data;

/**
 * @ignore
 */
require_once __DIR__ . '/../../../include.php';

/**
 * Test class for BIC
 *
 * @package  test
 */
class BicValidatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Data\BicValidator
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new \Yana\Data\BicValidator();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @return  array
     */
    public function provider()
    {
        return array(
            array('BELADEBEXXX', true),
            array('RBOSGGSX', true),
            array('CHASGB2LXXX', true),
            array('RZTIAT22263', true),
            array('BCEELULL', true),
            array('MARKDEFF', true),
            array('MARKDEFFXXX', true),
            array('GENODEF1JEV', true),
            array('UBSWCHZH80A', true),
            array('CEDELULLXXX', true),
            array('HELADEF1RRS', true),
            array('GENODEF1S04', true),
            array('BH67BMAG00001299123456', false),
            array('XXXXXXXXXXX', true),
            array('0XXXXXXXXXX', false),
            array('X0XXXXXXXXX', false),
            array('XX0XXXXXXXX', false),
            array('XXX0XXXXXXX', false),
            array('XXXX0XXXXXX', false),
            array('XXXXX0XXXXX', false),
            array('XXXXXX0XXXX', false),
            array('XXXXXXX0XXX', false),
            array('XXXXXXXX0XX', true),
            array('XXXXXXXX01X', true),
            array('XXXXXXXX012', true),
            array('XXXXXXXXX0X', false),
            array('XXXXXXXXXX0', false),
        );
    }

    /**
     * @param  string  $bic
     * @param  bool    $isValid
     * @dataProvider  provider
     * @test
     */
    public function testValidate($bic, $isValid)
    {
        $this->assertEquals($isValid, \Yana\Data\BicValidator::validate($bic));
    }

    /**
     * @param  string  $bic
     * @param  bool    $isValid
     * @dataProvider  provider
     * @test
     */
    public function test__Invoke($bic, $isValid)
    {
        $result = $this->_object->__invoke($bic);
        if ($isValid) {
            $this->assertEquals($bic, $result);
        } else {
            $this->assertNull($result);
        }
    }

}
