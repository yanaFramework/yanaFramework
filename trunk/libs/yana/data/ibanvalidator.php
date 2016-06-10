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
 * This validator is based on the IBAN REGISTRY for ISO 13616 in version 50 issued September 2014.
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
     * @param   string  $country  2 character country code based on ISO 13616 version 50
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
            'AL' => $n . '{8}' . $c . '{16}',
            'AD' => $n . '{4}' . $n . '{4}' . $c . '{12}',
            'AT' => $n . '{5}' . $n . '{11}',
            'AZ' => $a . '{4}' . $c . '{20}',
            'BH' => $a . '{4}' . $c . '{14}',
            'BE' => $n . '{3}' . $n . '{7}' . $n . '{2}',
            'BA' => $n . '{3}' . $n . '{3}' . $n . '{8}' . $n . '{2}',
            'BR' => $n . '{8}' . $n . '{5}' . $n . '{10}' . $a . '{1}' . $c . '{1}',
            'BG' => $a . '{4}' . $n . '{4}' . $n . '{2}' . $c . '{8}',
            'CR' => $n . '{3}' . $n . '{14}',
            'HR' => $n . '{7}' . $n . '{10}',
            'CY' => $n . '{3}' . $n . '{5}' . $c . '{16}',
            'CZ' => $n . '{4}' . $n . '{6}' . $n . '{10}',
            'DK' => $n . '{4}' . $n . '{9}' . $n . '{1}',
            'FO' => $n . '{4}' . $n . '{9}' . $n . '{1}',
            'GL' => $n . '{4}' . $n . '{9}' . $n . '{1}',
            'DO' => $c . '{4}' . $n . '{20}',
            'EE' => $n . '{2}' . $n . '{2}' . $n . '{11}' . $n . '{1}',
            'FI' => $n . '{6}' . $n . '{7}' . $n . '{1}',
            'FR' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}',
            'GF' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}',
            'GP' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}',
            'MQ' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}',
            'RE' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}',
            'PF' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}',
            'TF' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}',
            'YT' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}',
            'NC' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}',
            'BL' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}',
            'MF' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}',
            'PM' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}',
            'WF' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}',
            'GE' => $a . '{2}' . $n . '{16}',
            'DE' => $n . '{8}' . $n . '{10}',
            'GI' => $a . '{4}' . $c . '{15}',
            'GR' => $n . '{3}' . $n . '{4}' . $c . '{16}',
            'GT' => $c . '{4}' . $c . '{20}',
            'HU' => $n . '{3}' . $n . '{4}' . $n . '{1}' . $n . '{15}' . $n . '{1}',
            'IS' => $n . '{4}' . $n . '{2}' . $n . '{6}' . $n . '{10}',
            'IE' => $a . '{4}' . $n . '{6}' . $n . '{8}',
            'IL' => $n . '{3}' . $n . '{3}' . $n . '{13}',
            'IL' => $n . '{3}' . $n . '{3}' . $n . '{13}',
            'IT' => $a . '{1}' . $n . '{5}' . $n . '{5}' . $c . '{12}',
            'JO' => $a . '{4}' . $n . '{4}' . $c . '{18}',
            'KZ' => $n . '{3}' . $c . '{13}',
            'XK' => $n . '{4}' . $n . '{10}' . $n . '{2}',
            'KW' => $a . '{4}' . $c . '{22}',
            'LV' => $a . '{4}' . $c . '{13}',
            'LB' => $n . '{4}' . $c . '{20}',
            'LI' => $n . '{5}' . $c . '{12}',
            'LT' => $n . '{5}' . $n . '{11}',
            'LU' => $n . '{3}' . $c . '{13}',
            'MK' => $n . '{3}' . $c . '{10}' . $n . '{2}',
            'MT' => $a . '{4}' . $n . '{5}' . $c . '{18}',
            'MR' => $n . '{5}' . $n . '{5}' . $n . '{11}' . $n . '{2}',
            'MU' => $a . '{4}' . $n . '{2}' . $n . '{2}' . $n . '{12}' . $n . '{3}' . $a . '{3}',
            'MD' => $c . '{2}' . $c . '{18}',
            'MC' => $n . '{5}' . $n . '{5}' . $c . '{11}' . $n . '{2}',
            'ME' => $n . '{3}' . $n . '{13}' . $n . '{2}',
            'NL' => $a . '{4}' . $n . '{10}',
            'NO' => $n . '{4}' . $n . '{6}' . $n . '{1}',
            'PK' => $a . '{4}' . $c . '{16}',
            'PS' => $a . '{4}' . $c . '{21}',
            'PL' => $n . '{8}' . $n . '{16}',
            'PT' => $n . '{4}' . $n . '{4}' . $n . '{11}' . $n . '{2}',
            'RO' => $a . '{4}' . $c . '{16}',
            'QA' => $a . '{4}' . $c . '{21}',
            'SM' => $a . '{1}' . $n . '{5}' . $n . '{5}' . $c . '{12}',
            'SA' => $n . '{2}' . $c . '{18}',
            'RS' => $n . '{3}' . $n . '{13}' . $n . '{2}',
            'SK' => $n . '{4}' . $n . '{6}' . $n . '{10}',
            'SI' => $n . '{5}' . $n . '{8}' . $n . '{2}',
            'ES' => $n . '{4}' . $n . '{4}' . $n . '{1}' . $n . '{1}' . $n . '{10}',
            'SE' => $n . '{3}' . $n . '{16}' . $n . '{1}',
            'CH' => $n . '{5}' . $c . '{12}',
            'TL' => $n . '{3}' . $n . '{14}' . $n . '{2}',
            'TN' => $n . '{2}' . $n . '{3}' . $n . '{13}' . $n . '{2}',
            'TR' => $n . '{5}' . $c . '{1}' . $c . '{16}',
            'AE' => $n . '{3}' . $n . '{16}',
            'GB' => $a . '{4}' . $n . '{6}' . $n . '{8}',
            'VG' => $a . '{4}' . $n . '{16}'
        );

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
     * @link    http://www.swift.com/dsp/resources/documents/IBAN_Registry.pdf
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