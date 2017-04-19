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

namespace Yana\Data;

/**
 * Used for validating bank account information.
 *
 * This validator is based on the IBAN REGISTRY for ISO 13616 in version 70 issued April 2017.
 * Note! It will need future updates (probably multiple times a year) when new versions are released.
 * New versions may bring additional valid formats for IBANs for excisting and new member countries.
 *
 * I will try to keep it updated, but since this is NOT my main occupation, you should NOT rely on me doing that!
 * Instead check the official websites on a regular basis and make sure you update and/or extend this class as necessary.
 *
 * If you don't, then there is a chance you might identify new valid IBANs as invalid and thus reject paying customers.
 * Consider yourself warned!
 *
 * This version checks the (as of date) 81 valid formats in 66 countries for IBANs and the universal checksum.
 * It does however NOT check additional national checksums that some countries have added and it does especially
 * NOT check if a given International Bank Account number actually refers to an excisting bank and/or account!
 * If you need any of that, please consider contributing to this code or write your own extension to this class.
 *
 * If you use this for actual payments, I strongly suggest you see this validator as a first check only.
 * It is helpful for recognizing typos, but you may want to get an additional service (probably a third-party web-service)
 * to check if the given banks and/or accounts actually exist.
 *
 * Note! You CANNOT effectively conclude the country by the IBAN.
 * There are countries that use the country-prefix of other countries in their IBAN.
 * There are also colonies that have their own country-prefix despite being de-jure part of another state.
 *
 * Finally: there are "pseudo"-IBANs by non-member states.
 * These are non-standardized formats that identify a bank and account in a non-member-state (like Canada or the US).
 * They are NOT real IBANs, but contain all the information necessary to make a payment. So some people started to use them when
 * forms that allow only IBAN input became more frequent.
 * Banks are aware of this issue and some of them may actually accept such invalid IBANs.
 * You should check whether or not you are comfortable with accepting such IBANs and respond accordingly.
 * By default this validator will rejected them, but you can switch that off, in which case only the checksum will be checked
 * and the invalid country-code will be ignored.
 *
 * @package     yana
 * @subpackage  io
 */
class IbanValidator extends \Yana\Data\AbstractValidator
{

    /**
     * @var  bool
     */
    private $_allowInvalidCountryCode = false;

    /**
     * @var  string[]
     */
    private $_limitCountries = array();


    /**
     * Should invalid country codes be allowed?
     *
     * @return  bool
     */
    public function allowInvalidCountryCode()
    {
        return $this->_allowInvalidCountryCode;
    }

    /**
     * To what countries should the IBANs be limited?
     *
     * @return  string[]
     */
    public function getLimitCountries()
    {
        return $this->_limitCountries;
    }

    /**
     * Set to bool(true) to allow unknown country codes.
     *
     * The default is bool(false).
     *
     * @param   bool  $allowInvalidCountryCode  true = allow pseudo-IBANs, false = disallow
     * @return  self
     */
    public function setAllowInvalidCountryCode($allowInvalidCountryCode)
    {
        assert('is_bool($allowInvalidCountryCode); // Invalid argument $allowInvalidCountryCode: bool expected');
        $this->_allowInvalidCountryCode = (bool) $allowInvalidCountryCode;
        return $this;
    }

    /**
     * Limit IBANs to the given countries.
     *
     * All country codes not listed will be considered invalid.
     * If empty, all known country codes will be considered valid.
     *
     * @param   array  $limitCountries  list of 2-letter country codes
     * @return  self
     */
    public function setLimitCountries(array $limitCountries)
    {
        $this->_limitCountries = (array) $limitCountries;
        return $this;
    }

    /**
     * Choose and return IBAN reg-exp based on country.
     *
     * {@internal{
     * This section may need frequent (monthly) maintenance.
     *
     * 1) find the latest release of the "IBAN Registry" here: https://www.swift.com/standards/data-standards/iban
     * 2) download and open the current PDF
     * 3) check the document history using the version number mentioned in this documentation
     * 4) open the coresponding changed pages, and apply the changes to the code below
     * 5) update the version number in the documentation accordingly
     *
     * Example:
     *
     * - AL – Albania Documentation = BBAN structure: 8!n16!c 
     * - Code: 'AL' => $n . '{8}' . $c . '{16}',
     * }}
     *
     * @param   string  $country  2 character country code based on ISO 13616
     * @return  string
     * @throws  \Yana\Data\IbanCountryUnrecognizedException  when the country code is unknown (can be turned off)
     * @throws  \Yana\Data\IbanCountryDisallowedException    when the country code is not allowed (has to be turned on)
     */
    private function _buildRegExp($country)
    {
        assert('is_string($country); // Invalid argument $country: string expected');

        if (count($this->getLimitCountries()) > 0 && !\in_array($country, $this->getLimitCountries())) {
            throw new \Yana\Data\IbanCountryDisallowedException($country);
        }

        $a = '[A-Z]';
        $c = '[A-Z0-9]';
        $n = '\d';
        $countryRegEx = array(
            'AD' => $n . '{4}' . $n . '{4}' . $c . '{12}', // Andorra
            'AE' => $n . '{3}' . $n . '{16}', // United Arab Emirates
            'AL' => $n . '{8}' . $c . '{16}', // Albania
            'AT' => $n . '{5}' . $n . '{11}', // Austria
            'AZ' => $a . '{4}' . $c . '{20}', // Azerbaijan
            'BA' => $n . '{3}' . $n . '{3}' . $n . '{8}' . $n . '{2}', // Bosnia and Herzegovina
            'BE' => $n . '{3}' . $n . '{7}' . $n . '{2}', // Belgium
            'BG' => $a . '{4}' . $n . '{4}' . $n . '{2}' . $c . '{8}', // Bulgaria
            'BH' => $a . '{4}' . $c . '{14}', // Bahrain
            'BR' => $n . '{8}' . $n . '{5}' . $n . '{10}' . $a . '{1}' . $c . '{1}', // Brazil
            'BY' => $c . '{4}' . $n . '{4}' . $c . '{16}', // Republic of Belarus
            'CH' => $n . '{5}' . $c . '{12}', // Switzerland
            'CR' => $n . '{4}' . $n . '{14}', // Costa Rica
            'CY' => $n . '{3}' . $n . '{5}' . $c . '{16}', // Cyprus
            'CZ' => $n . '{4}' . $n . '{6}' . $n . '{10}', // Czech Republic
            'DE' => $n . '{8}' . $n . '{10}', // Germany
            'DK' => $n . '{4}' . $n . '{9}' . $n . '{1}', // Denmark
            'DO' => $c . '{4}' . $n . '{20}', // Dominican Republic
            'EE' => $n . '{2}' . $n . '{2}' . $n . '{11}' . $n . '{1}', // Estonia
            'ES' => $n . '{4}' . $n . '{4}' . $n . '{1}' . $n . '{1}' . $n . '{10}', // Spain
            'FI' => $n . '{3}' . $n . '{11}', // Finland
            'FO' => $n . '{4}' . $n . '{9}' . $n . '{1}', // Faroe Islands
            'FR' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}', // France
            'GB' => $a . '{4}' . $n . '{6}' . $n . '{8}', // United Kingdom
            'GE' => $a . '{2}' . $n . '{16}', // Georgia
            'GI' => $a . '{4}' . $c . '{15}', // Gibraltar
            'GL' => $n . '{4}' . $n . '{9}' . $n . '{1}', // Greenland
            'GR' => $n . '{3}' . $n . '{4}' . $c . '{16}', // Greece
            'GT' => $c . '{4}' . $c . '{20}', // Guatemala
            'HR' => $n . '{7}' . $n . '{10}', // Croatia
            'HU' => $n . '{3}' . $n . '{4}' . $n . '{1}' . $n . '{15}' . $n . '{1}', // Hungary
            'IE' => $a . '{4}' . $n . '{6}' . $n . '{8}', // Ireland
            'IL' => $n . '{3}' . $n . '{3}' . $n . '{13}', // Israel
            'IL' => $a . '{4}' . $n . '{3}' . $n . '{12}', // Iraq
            'IS' => $n . '{4}' . $n . '{2}' . $n . '{6}' . $n . '{10}', // Iceland
            'IT' => $a . '{1}' . $n . '{5}' . $n . '{5}' . $c . '{12}', // Italy
            'JO' => $a . '{4}' . $n . '{4}' . $c . '{18}', // Jordan
            'KW' => $a . '{4}' . $c . '{22}', // Kuwait
            'KZ' => $n . '{3}' . $c . '{13}', // Kazakhstan
            'LB' => $n . '{4}' . $c . '{20}', // Lebanon
            'LC' => $a . '{4}' . $c . '{24}', // Saint Lucia
            'LI' => $n . '{5}' . $c . '{12}', // Liechtenstein
            'LT' => $n . '{5}' . $n . '{11}', // Lithuania
            'LU' => $n . '{3}' . $c . '{13}', // Luxembourg
            'LV' => $a . '{4}' . $c . '{13}', // Latvia
            'MC' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}', // Monaco
            'MD' => $c . '{2}' . $c . '{18}', // Moldova
            'ME' => $n . '{3}' . $n . '{13}' . $n . '{2}', // Montenegro
            'MK' => $n . '{3}' . $c . '{10}' . $n . '{2}', // Macedonia
            'MR' => $n . '{5}' . $n . '{5}' . $n . '{11}' . $n . '{2}', // Mauritania
            'MT' => $a . '{4}' . $n . '{5}' . $c . '{18}', // Malta
            'MU' => $a . '{4}' . $n . '{2}' . $n . '{2}' . $n . '{12}' . $n . '{3}' . $a . '{3}', // Mauritius
            'NL' => $a . '{4}' . $n . '{10}', // Netherlands
            'NO' => $n . '{4}' . $n . '{6}' . $n . '{1}', // Norway
            'PK' => $a . '{4}' . $c . '{16}', // Pakistan
            'PL' => $n . '{8}' . $n . '{16}', // Poland
            'PS' => $a . '{4}' . $c . '{21}', // Palestine
            'PT' => $n . '{4}' . $n . '{4}' . $n . '{11}' . $n . '{2}', // Portugal
            'QA' => $a . '{4}' . $c . '{21}', // Qatar
            'RO' => $a . '{4}' . $c . '{16}', // Romania
            'RS' => $n . '{3}' . $n . '{13}' . $n . '{2}', // Serbia
            'SA' => $n . '{2}' . $c . '{18}', // Saudi Arabia
            'SC' => $a . '{4}' . $n . '{2}' . $n . '{2}' . $n . '{16}' . $a . '{3}', // Seychelles
            'SE' => $n . '{3}' . $n . '{16}' . $n . '{1}', // Sweden
            'SI' => $n . '{5}' . $n . '{8}' . $n . '{2}', // Slovenia
            'SK' => $n . '{4}' . $n . '{6}' . $n . '{10}', // Slovakia
            'SM' => $a . '{1}' . $n . '{5}' . $n . '{5}' . $c . '{12}', // San Marino
            'ST' => $n . '{4}' . $n . '{4}' . $c . '{11}' . $n . '{2}', // Sao Tome and Principe
            'SV' => $a . '{4}' . $n . '{20}', // El Salvador
            'TL' => $n . '{3}' . $n . '{14}' . $n . '{2}', // Timor-Leste
            'TN' => $n . '{2}' . $n . '{3}' . $n . '{13}' . $n . '{2}', // Tunisia
            'TR' => $n . '{5}' . $n . '{1}' . $c . '{16}', // Turkey
            'UA' => $n . '{6}' . $c . '{19}', // Ukraine
            'VG' => $a . '{4}' . $n . '{16}', // Virgin Islands
            'XK' => $n . '{4}' . $n . '{10}' . $n . '{2}' // Kosovo
        );

        // The following countries are not included in ISO 13616, but nevertheless accept IBANs
        $unofficialCountriesRegEx = array(
            'AO' => $n . '{6}' . $n . '{8}', // Aland Islands (unofficial)
            'AX' => $n . '{21}', // Angola (unofficial)
            'BF' => $n . '{23}', // Burkina Faso (unofficial)
            'BI' => $n . '{12}', // Burundi (unofficial)
            'BJ' => $a . '{1}' . $n . '{23}', // Benin (unofficial)
            'BL' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}', // Saint Barthelemy (unofficial)
            'CI' => $a . '{1}' . $n . '{23}', // Côte d'Ivoire (unofficial)
            'CM' => $n . '{23}', // Cameroon (unofficial)
            'CV' => $n . '{21}', // Cape Verde (unofficial)
            'DZ' => $n . '{20}', // Algeria (unofficial)
            'GF' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}', // French Guiana (unofficial)
            'GP' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}', // Guadelope (unofficial)
            'PF' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}', // French Polynesia (unofficial)
            'TF' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}', // French Southern Territories (unofficial)
            'IR' => $n . '{22}', // Iran (unofficial)
            'MG' => $n . '{23}', // Madagascar (unofficial)
            'ML' => $a . '{1}' . $n . '{23}', // Mali (unofficial)
            'MQ' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}', // Martinique (unofficial)
            'MZ' => $n . '{21}', // Mozambique (unofficial)
            'NC' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}', // New Caledonia (unofficial)
            'MF' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}', // Saint Martin (unofficial)
            'PM' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}', // Saint Pierre et Miquelon (unofficial)
            'RE' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}', // Reunion (unofficial)
            'SN' => $a . '{1}' . $n . '{23}', // Senegal (unofficial)
            'WF' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}', // Wallis and Futuna Islands (unofficial)
            'YT' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}', // Mayotte (unofficial)
        );

        $countryRegEx += $unofficialCountriesRegEx; // this adds the unofficial codes

        assert('!isset($regExp); // Cannot redeclare var $regExp');
        if (!isset($countryRegEx[$country])) {

            if (!$this->allowInvalidCountryCode()) {
                throw new \Yana\Data\IbanCountryUnrecognizedException($country);
            }
            $regExp = $c . '{2}' . $n . '{2}' . $c . '{5,35}';

        } else {
            $regExp = $country . $n . '{2}' . $countryRegEx[$country];
        }

        return '/^' . $regExp . '$/';
    }

    /**
     * Check IBAN syntax.
     *
     * The following is copied from the official ISO 13616 and was tested with given examples.
     * However: I cannot guarantee it to be correct. Be warned!
     *
     * @param   string  $iban  International bank account number
     * @return  string
     * @link    https://www.swift.com/sites/default/files/resources/swift_standards_ibanregistry.pdf
     */
    private function _isStructureValid($iban)
    {
        assert('is_string($iban); // Invalid argument $iban: string expected');

        $string = strtoupper($iban);
        $country = (strlen($string) > 1) ? $string[0] . $string[1] : "";

        assert('!isset($isValid); // Cannot redeclare var $isValid');
        try {
            $countryRegEx = $this->_buildRegExp($country);
            $isValid = 1 === \preg_match($countryRegEx, $string);
        } catch(\Yana\Data\ValidatorException $e) {
            unset($e);
            $isValid = false;
        }

        assert('is_bool($isValid); // Boolean expected');
        return $isValid;
    }

    /**
     * Convert IBAN to numeric string.
     *
     * To calculate the checksum the IBAN has to be converted to a number.
     * For that the first 4 digits are copied to the end and all characters are replaced by their character values.
     * The character value is based on the alphabet, so that A = 1, B = 2 ...
     *
     * @param   string  $iban  International bank account number
     * @return  string
     */
    private function _toNumericString($iban)
    {
        assert('is_string($iban); // Invalid argument $iban: string expected');

        $string = strtoupper(substr($iban, 4) . substr($iban, 0, 4));
        $number = "";

        // The string is treated as an array on purpose. This is NOT an accident. Unicode is NOT an issue here.
        for ($i = 0; $i < strlen($string); $i++)
        {
            $number .= is_numeric($string[$i]) ? (string) $string[$i] : (string) (ord($string[$i]) - 55);
        }
        unset($i, $string);

        return $number;
    }

    /**
     * Calculate recursive modulo function.
     *
     * A little bit of a specialty to the modulo calculation of the IBAN.
     * So just using the standard modulo operator won't work.
     *
     * @param   string  $leftNumber
     * @param   scalar  $rightNumber
     * @return  int
     */
    private function _mod($leftNumber, $rightNumber)
    {
        assert('is_string($leftNumber); // Invalid argument $leftNumber: string expected');
        assert('is_scalar($rightNumber); // Invalid argument $rightNumber: scalar expected');

        $modNumber = 0;

        for ($i = 0; $i < strlen($leftNumber); $i++) {
            $modNumber = (int) ($modNumber . $leftNumber[$i]) % (int) $rightNumber;
        }

        return $modNumber;
    }

    /**
     * Validate a value as valid IBAN.
     *
     * Calculates the checksum, but does NOT check if the account or bank exists.
     * Returns bool(true) if valid and bool(false) if not.
     *
     * @param   mixed  $iban  bank account number
     * @param   bool   $allowInvalidCountryCode  true = allow pseudo-IBANs, false = disallow
     * @param   array  $limitCountries  list of 2-letter country codes
     * @return  bool
     */
    public static function validate($iban, $allowInvalidCountryCode = false, array $limitCountries = array())
    {
        assert('is_bool($allowInvalidCountryCode); // Invalid argument $allowInvalidCountryCode: bool expected');

        $validator = new self();
        $validator->setAllowInvalidCountryCode($allowInvalidCountryCode)->setLimitCountries($limitCountries);
        $validStructure = is_string($iban) && $validator->_isStructureValid($iban);
        $validChecksum = is_string($iban) && $validator->_isChecksumValid($iban);

        return $validStructure && $validChecksum;
    }

    /**
     * Sanitize IBAN.
     *
     * @param   mixed  $iban  International Bank Account Number
     * @return  mixed 
     */
    public static function sanitize($iban)
    {
        return preg_replace('/[^A-Z0-9]/', '', strtoupper($iban));
    }


    /**
     * Validate checksum.
     *
     * Returns bool(true) if valid and bool(false) if not.
     *
     * @param   mixed  $iban  bank account number
     * @return  bool
     */
    protected function _isChecksumValid($iban)
    {
        $number = $this->_toNumericString($iban);

        return 1 === $this->_mod($number, 97);
    }

    /**
     * Sanitize IBAN.
     *
     * Returns NULL for invalid objects.
     *
     * @param   mixed  $value  value to sanitize
     * @return  string 
     */
    public function __invoke($value)
    {
        assert('!isset($trimmedString); // Cannot redeclare var $trimmedString');
        $trimmedString = static::sanitize($value);
        assert('!isset($result); // Cannot redeclare var $result');
        $result = ($this->_isStructureValid($trimmedString) && $this->_isChecksumValid($trimmedString)) ? $trimmedString : null;
        return $result;
    }

}

?>
