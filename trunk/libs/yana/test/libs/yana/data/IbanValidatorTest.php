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
 * Test class for IBAN
 *
 * @package  test
 */
class IbanValidatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Yana\Data\IbanValidator
     */
    protected $_object = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new \Yana\Data\IbanValidator();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        // intentionally left blank
    }

    /**
     * @return  array
     */
    public function provider()
    {
        return array(
            array('AX2112345600000785', true),
            array('AO44123412341234123412341', true),
            array('AL47212110090000000235698741', true),
            array('FO2000400440116243', true),
            array('GL2000400440116243', true),
            array('XK051212012345678906', true),
            array('GF4120041010050500013M02606', true),
            array('PF5720041010050500013M02606', true),
            array('TF2120041010050500013M02606', true),
            array('GP1120041010050500013M02606', true),
            array('MQ5120041010050500013M02606', true),
            array('YT3120041010050500013M02606', true),
            array('NC8420041010050500013M02606', true),
            array('QA73BBME000000000004056677001', true),
            array('RE4220041010050500013M02606', true),
            array('BL6820041010050500013M02606', true),
            array('LC55HEMM000100010012001200023015', true),
            array('MF8420041010050500013M02606', true),
            array('PM3620041010050500013M02606', true),
            array('ST68000100010051845310112', true),
            array('UA213996220000026007233566001', true),
            array('WF9120041010050500013M02606', true),
            array('SC18SSCB11010000000000001497USD', true),
            array('AD1200012030200359100100', true),
            array('AT611904300234573201', true),
            array('AZ21NABZ00000000137010001944', true),
            array('BH67BMAG00001299123456', true),
            array('BE68539007547034', true),
            array('BA391290079401028494', true),
            array('BR9700360305000010009795493P1', true),
            array('BR1800000000141455123924100C2', true),
            array('BG80BNBG96611020345678', true),
            array('CR05015202001026284066', true),
            array('HR1210010051863000160', true),
            array('DE68210501700012345678', true),
            array('CZ6508000000192000145399', true),
            array('CZ9455000000001011038930', true),
            array('CY17002001280000001200527600', true),
            array('DK5000400440116243', true),
            array('FO6264600001631634', true),
            array('GL8964710001000206', true),
            array('DO28BAGR00000001212453611324', true),
            array('DO28BAGR0000000121245361136', false),
            array('EE382200221020145685', true),
            array('FI2112345600000785', true),
            array('FI5542345670000081', true),
            array('FR1420041010050500013M02606', true),
            array('DE89370400440532013000', true),
            array('GE29NB0000000101904917', true),
            array('GI75NWBK000000007099453', true),
            array('GR1601101250000000012300695', true),
            array('GT82TRAJ01020000001210029690', true),
            array('HU42117730161111101800000000', true),
            array('IS140159260076545510730339', true),
            array('IE29AIBK93115212345678', true),
            array('IL620108000000099999999', true),
            array('IT60X0542811101000000123456', true),
            array('JO94CBJO0010000000000131000302', true),
            array('KZ86125KZT5004100100', true),
            array('XK051000000000000053', true),
            array('KW81CBKU0000000000001234560101', true),
            array('LV80BANK0000435195001', true),
            array('LB62099900000001001901229114', true),
            array('LI21088100002324013AA', true),
            array('LT121000011101001000', true),
            array('LU280019400644750000', true),
            array('MK07250120000058984', true),
            array('MT84MALT011000012345MTLCAST001S', true),
            array('MR1300020001010000123456753', true),
            array('MU17BOMM0101101030300200000MUR', true),
            array('MD24AG000225100013104168', true),
            array('MC5811222000010123456789030', true),
            array('ME25505000012345678951', true),
            array('NL91ABNA0417164300', true),
            array('NO9386011117947', true),
            array('PK36SCBL0000001123456702', true),
            array('PS92PALS000000000400123456702', true),
            array('PL61109010140000071219812874', true),
            array('PT50000201231234567890154', true),
            array('RO49AAAA1B31007593840000', true),
            array('QA58DOHB00001234567890ABCDEFG', true),
            array('SM86U0322509800000000270100', true),
            array('SA0380000000608010167519', true),
            array('RS35260005601001611379', true),
            array('SK3112000000198742637541', true),
            array('SI56263300012039086', true),
            array('ES9121000418450200051332', true),
            array('SE4550000000058398257466', true),
            array('CH9300762011623852957', true),
            array('TL380080012345678910157', true),
            array('TN5910006035183598478831', true),
            array('TR330006100519786457841326', true),
            array('AE070331234567890123456', true),
            array('GB29NWBK60161331926819', true),
            array('VG96VPVG0000012345678901', true),
            array('DE68210501700012345679', false),
            array('', false)
        );
    }

    /**
     * @param  string  $iban
     * @param  bool    $isValid
     * @dataProvider  provider
     * @test
     */
    public function testValidate($iban, $isValid)
    {
        $this->assertEquals($isValid, \Yana\Data\IbanValidator::validate($iban));
    }

    /**
     * @param  string  $iban
     * @param  bool    $isValid
     * @dataProvider  provider
     * @test
     */
    public function test__Invoke($iban, $isValid)
    {
        $result = $this->_object->__invoke($iban);
        if ($isValid) {
            $this->assertEquals($iban, $result);
        } else {
            $this->assertNull($result);
        }
    }

    /**
     * @test
     */
    public function testAllowInvalidCountryCode()
    {
        $this->assertFalse($this->_object->allowInvalidCountryCode());
    }

    /**
     * @test
     */
    public function testSetAllowInvalidCountryCode()
    {
        $this->assertTrue($this->_object->setAllowInvalidCountryCode(true)->allowInvalidCountryCode());
        $pseudoIban = 'CA031234567890';
        $this->assertEquals($pseudoIban, $this->_object->__invoke($pseudoIban));
        $this->_object->setAllowInvalidCountryCode(false);
        $this->assertNull($this->_object->__invoke($pseudoIban));
    }

    /**
     * @test
     */
    public function testGetLimitCountries()
    {
        $this->assertEquals(array(), $this->_object->getLimitCountries());
    }

    /**
     * @test
     */
    public function testSetLimitCountries()
    {
        $this->assertEquals(array('DE'), $this->_object->setLimitCountries(array('DE'))->getLimitCountries());
        $iban = 'DE89370400440532013000';
        $this->assertEquals($iban, $this->_object->__invoke($iban));
        $iban2 = 'MR1300020001010000123456753';
        $this->assertNull($this->_object->__invoke($iban2));
        $this->_object->setLimitCountries(array());
        $this->assertEquals($iban2, $this->_object->__invoke($iban2));
    }

}

?>