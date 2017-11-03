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

namespace Yana\Util;

/**
 * <<Utility>> Static string functions.
 *
 * This is an OO-wrapper for the string functions of PHP.
 *
 * This utility class is meant to ease the pain of the naming
 * chaos in string functions left over from previous PHP versions.
 * Some of them derive from the names of these functions in C
 * and follow C-naming conventions, while others derive from Perl,
 * using another convention and others are even new, following yet
 * another convention.
 *
 * Now: this renames all functions, giving you a clean interface for all.
 *
 * @package     yana
 * @subpackage  util
 */
class Strings extends \Yana\Core\AbstractUtility
{

    /**#@+
     * used as 2nd argument in method self::trim()
     *
     * @see self::trim()
     */
    const BOTH = 0;
    const LEFT = 1;
    const RIGHT = 2;
    /**#@-*/

    /**
     * Convert string to an integer.
     *
     * Returns bool(false) if the string is not numeric.
     *
     * @param   string  $string  value to convert
     * @return  int|bool(false)
     *
     * @name    Strings::toInt()
     *
     * @assert ("1") == 1
     * @assert ("1.5") == 1
     * @assert ("a") == false
     */
    public static function toInt($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        if (is_numeric($string)) {
            return intval($string);
        } else {
            return false;
        }
    }

    /**
     * Convert string to a float.
     *
     * Returns bool(false) if the string is not numeric.
     *
     * @param   string  $string  value to convert
     * @return  float|bool(false)
     *
     * @name    Strings::toFloat()
     *
     * @assert ("1") == 1.0
     * @assert ("1.5") == 1.5
     * @assert ("a") == false
     */
    public static function toFloat($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        if (is_numeric($string)) {
            return floatval($string);
        } else {
            return false;
        }
    }

    /**
     * Returns a boolean value depending on the value of the string.
     *
     * <ul>
     *     <li>    string("false") returns bool(false)    </li>
     *     <li>    string("true")  returns bool(true)     </li>
     *     <li>    any other value returns a boolean value
     *             depending on the result of PHP's
     *             internal conversion mechanism,
     *             BUT also issues an E_USER_NOTICE for on
     *             an invalid string to bool conversion   </li>
     * </ul>
     *
     * Note:
     * If you just want to check wether a string is empty
     * or not, use $string == "" instead.
     *
     * @param   string  $string  value to convert
     * @return  bool
     *
     * @name    Strings::toBool()
     *
     * @assert ("True") == true
     * @assert ("False") == false
     * @assert ("0") == false
     * @assert ("1") == true
     * @assert ("") == false
     * @assert ("a") == false
     */
    public static function toBool($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        
        return filter_var($string, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * OO-Alias of: addslashes(), addcslashes()
     *
     * @param   string  $string    string
     * @param   string  $charlist  a string of characters that should be escaped
     * @return  string
     *
     * @name    Strings::addSlashes()
     *
     * @assert ("a", "a") == '\a'
     * @assert ("a", "b") == 'a'
     * @assert ('\\a') == '\\\\a'
     */
    public static function addSlashes($string, $charlist = "")
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_string($charlist); // Wrong argument type for argument 2. String expected.');
        if (!empty($charlist)) {
            return addcslashes($string, $charlist);
        } else {
            return addslashes($string);
        }
    }

    /**
     * OO-Alias of: stripslashes(), stripcslashes()
     *
     * @param   string  $string     string
     * @return  string
     *
     * @name    Strings::removeSlashes()
     *
     * @assert ("a") == 'a'
     * @assert ('\a') == 'a'
     * @assert ('\\\\a') == '\\a'
     */
    public static function removeSlashes($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        return stripslashes($string);
    }

    /**
     * OO-Alias of: $string[$index]
     *
     * Returns bool(false) on error.
     *
     * Note that indices are numbered starting with '0'.
     *
     * @param   string  $string string
     * @param   int     $index  position of the character (starting with 0)
     * @throws  \Yana\Core\Exceptions\OutOfBoundsException  if $index is out of bounds
     *
     * @name    Strings::charAt()
     *
     * @assert ("Test", 0) == "T"
     * @assert ("Test", 3) == "t"
     */
    public static function charAt($string, $index)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_int($index); // Wrong argument type for argument 2. Integer expected.');
        /* check if $index is in bounds */
        /* If the input is no integer at all, throw an exception. */
        if ($index < 0 || $index >= mb_strlen($string)) {
            throw new \Yana\Core\Exceptions\OutOfBoundsException("String index '".$index."' out of bounds.");
        } else {
            /* all fine, proceed */
            return $string[$index];
        }
    }

    /**
     * OO-Alias of: trim(), chop()
     *
     * @param   string  $string  string
     * @param   int     $type    may be one of
     * @return  string
     *
     * @name    Strings::trim()
     *
     * @assert (" test ") == "test"
     * @assert (" test ", Strings::LEFT) == "test "
     * @assert (" test ", Strings::RIGHT) == " test"
     */
    public static function trim($string, $type = self::BOTH)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_int($type); // Wrong argument type for argument 2. Integer expected.');
        switch ($type)
        {
            case self::LEFT:
                return ltrim($string);
            case self::RIGHT:
                return rtrim($string);
            default:
                return trim($string);
        }
    }

    /**
     * hashing function, encryption, transformation (not revertable)
     *
     * Returns an encrypted version depending on
     * the type of encryption you choose.
     *
     * The input value is not case-sensitive.
     *
     * Note: The result of this function is alwas
     * irreversible. If you are looking for
     * reversible encryption methods see the "encode"
     * function.
     *
     * The following values are available.
     * <ul>
     *     <li>    crc32:    computes the crc32 checksum value of the string    </li>
     *     <li>    md5:      computes the md5 hash-string (128Bit)              </li>
     *     <li>    sha1:     computes the sha1 hash-string (160Bit)             </li>
     *     <li>    crypt:    uses crypt() function, result depending on $salt.
     *                       See PHP-Manual for details.                        </li>
     *     <li>    des:      uses DES encryption algorithm                      </li>
     *     <li>    blowfish: uses BLOWFISH encryption algorithm                 </li>
     *     <li>    soundex:  calculates the soundex hash: While this is not an
     *                       encryption algorithm, it is listed here, because it
     *                       is uses some sort of irreversible hashing and thus
     *                       won't fit to encoding()                            </li>
     *     <li>    metaphone:calculates the metaphone hash: While this is not an
     *                       encryption algorithm, it is listed here, for the
     *                       same reason as "soundex". Note: uses the argument
     *                       $salt as second argument for metaphone() if $salt
     *                       is a numeric value, that can be converted to int.  </li>
     *     <li>              any of the encryption types supported by the PHP
     *                       "hash()" function introduced as of PHP 5.1.2       </li>
     *     <li>    xor:      XOR is a simple revertable block chiffre.
     *                       Use $salt string as password to encrypt the clear
     *                       text. Call encrypt() with the same arguments again
     *                       to revert the ciphered text back to clear text.
     *                       Note that the security of XOR and all other simple
     *                       block chiffres depend on the length and security
     *                       off your password. To be really secure your password
     *                       needs to be minimum as long as the clear text, which
     *                       is not always praticable.                          </li>
     * </ul>
     *
     * {@internal
     *
     * Note:
     * Add other types of encryption as you see fit.
     * Remember to update not only the source, but
     * also the documentation on this function.
     *
     * }}
     *
     * @param   string  $string      string
     * @param   string  $encryption  see the list of valid inputs for details
     * @param   string  $salt        only used for certain encryption types
     * @return  string
     *
     * @name    Strings::encrypt()
     * @see     Strings::encode()
     *
     * @assert ("test", "crc32") == -662733300
     * @assert ("test", "md5") == "098f6bcd4621d373cade4e832627b4f6"
     * @assert ("test", "sha") == "a94a8fe5ccb19ba61c4c0873d391e987982fbbd3"
     * @assert ("test", "crypt", "pass") == "pawpU97AVNPO6"
     * @assert ("test", "des") == NULL
     * @assert ("test", "des", "pass") == "pawpU97AVNPO6"
     * @assert ("test", "blowfish", "passwordpasswordpassword") == '$2y$10$passwordpasswordpasswe5CTNQfLGuOENdWfsXOxrnwUshKsXqmu'
     * @assert ("test", "soundex") == "T230"
     * @assert ("test", "metaphone") == "TST"
     * @assert ("aaaa", "xor", "    ") == "AAAA"
     * @throws  \Yana\Core\Exceptions\NotImplementedException  when the requested encryption method is not available
     */
    public static function encrypt($string, $encryption = "md5", $salt = "")
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_string($encryption); // Wrong argument type for argument 2. String expected.');
        assert('is_string($salt); // Wrong argument type for argument 3. String expected.');

        switch (mb_strtolower($encryption))
        {
            case 'crc32':
                // @codeCoverageIgnoreStart
                if (function_exists('crc32')) {
                    return crc32($string);
                } else {
                    $message = "Unsupported encryption method: '$encryption'.";
                    throw new \Yana\Core\Exceptions\NotImplementedException($message);
                }
                // @codeCoverageIgnoreEnd
            break;
            case 'md5':
                if (mb_strlen($salt) > 8 && CRYPT_MD5 == 1) {
                    return crypt($string, '$1$'. mb_substr($salt, 0, 9));
                } else {
                    return md5($string);
                }
            break;
            case 'sha':
            case 'sha1':
                // @codeCoverageIgnoreStart
                if (function_exists('sha1')) {
                    return sha1($string);
                } else {
                    $message = "Unsupported encryption method: '$encryption'.";
                    throw new \Yana\Core\Exceptions\NotImplementedException($message);
                }
                // @codeCoverageIgnoreEnd
            break;
            case 'crypt':
                if (mb_strlen($salt) > 0) {
                    return crypt($string, $salt);
                } else {
                    return crypt($string);
                }
            break;
            case 'des':
                if (mb_strlen($salt) == 0) {
                    return NULL;
                } else {
                    if (CRYPT_EXT_DES == 1 && mb_strlen($salt) > 8) {
                        return crypt($string, mb_substr($salt, 0, 9));

                    } elseif (CRYPT_STD_DES == 1 && mb_strlen($salt) > 1) {
                        return crypt($string, mb_substr($salt, 0, 2));

                    } else {
                        return NULL;
                    }
                }
            break;
            case 'bcrypt':
                // auto-generates salt, will return different string each time
                return \password_hash($salt . $string, \PASSWORD_BCRYPT);
            break;
            case 'blowfish':
                if (CRYPT_BLOWFISH != 1 || mb_strlen($salt) < 22) {
                    return NULL;
                } else {
                    return crypt($string, '$2y$10$' . mb_substr($salt, 0, 22));
                }
            break;
            case 'soundex':
                if (function_exists('soundex')) {
                    return soundex($string);

                } else {
                    // @codeCoverageIgnoreStart
                    $message = "Unsupported encryption method: '$encryption'.";
                    throw new \Yana\Core\Exceptions\NotImplementedException($message);
                    // @codeCoverageIgnoreEnd
                }
            break;
            case 'metaphone':
                if (function_exists('metaphone')) {
                    if (mb_strlen($salt) > 0 && is_numeric($salt)) {
                        /* settype to INTEGER */
                        $phones = (int) $salt;
                        return metaphone($string, $phones);
                    } else {
                        return metaphone($string);
                    }
                } else {
                    // @codeCoverageIgnoreStart
                    $message = "Unsupported encryption method: '$encryption'.";
                    throw new \Yana\Core\Exceptions\NotImplementedException($message);
                    // @codeCoverageIgnoreEnd
                }
            break;
            case 'xor':
                $pass = $salt;
                for ($i = 0; $i < strlen($string); $i++)
                {
                    $string[$i] = $string[$i] ^ $pass[$i % strlen($pass)];
                }
                return $string;
            default:
                $message = "Unsupported encryption method: '$encryption'.";
                throw new \Yana\Core\Exceptions\NotImplementedException($message);
        }
    }

    /**
     * encoding, or converting a string (revertable)
     *
     * Note: charset applies only to encoding = "entities"
     *
     * Returns an encoded version depending on
     * the type of encoding you choose.
     *
     * The input value is not case-sensitive.
     *
     * Note: The results of this function can be reversed using
     * the "decode()" function with the same values.
     * If you are looking for checksums and hashing-methods
     * see the "encrypt" function.
     *
     * The following values are available.
     * <ul>
     *     <li>    unicode:  uses utf8_encode(), aliases: "utf", "utf8"         </li>
     *     <li>    base64:   uses base64_encode()                               </li>
     *     <li>    url:      uses urlencode()                                   </li>
     *     <li>    rawurl:   uses rawurlencode()                                </li>
     *     <li>    entities: uses htmlentities(), uses $style argument          </li>
     *     <li>    rot13:    does a ROT13 transformation                        </li>
     *     <li>    quote:    quotes meta signs using quotemeta()                </li>
     *     <li>    regexp:   uses preg_quote(), alias: "regular expression"     </li>
     * </ul>
     *
     * {@internal
     *
     * Note:
     * Add other types of encodings as you see fit.
     * Remember to add a corresponding decode method
     * in function "decode()" as well.
     * Remember to update not only the source, but
     * also the documentation on this function.
     *
     * }}
     *
     * @param   string  $string    string
     * @param   string  $encoding  see the list of valid inputs for details
     * @param   int     $style     used for entity conversion
     * @param   string  $charset   used for entity conversion
     * @return  string
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the selected encoding is not valid
     *
     * @name    Strings::encode()
     * @see     Strings::encrypt()
     * @see     Strings::decode()
     */
    public static function encode($string, $encoding, $style = ENT_COMPAT, $charset = "UTF-8")
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_string($encoding); // Wrong argument type for argument 2. String expected.');
        assert('is_int($style); // Wrong argument type for argument 3. Integer expected.');
        assert('is_string($charset); // Wrong argument type for argument 4. String expected.');

        switch (mb_strtolower($encoding))
        {
            case 'unicode':
            case 'utf':
            case 'utf8':
                return utf8_encode($string);
            case 'base64':
                return base64_encode($string);
            case 'url':
                return urlencode($string);
            case 'rawurl':
                return rawurlencode($string);
            case 'rot13':
                return str_rot13($string);
            case 'entities':
                switch($style)
                {
                    case ENT_COMPAT:
                    case ENT_QUOTES:
                    case ENT_NOQUOTES:
                        return htmlentities($string, $style, $charset);
                    case ENT_FULL:
                        return mb_encode_numericentity($string, array(0x0, 0xffff, 0, 0xffff), $charset);
                    default:
                        return htmlentities($string, ENT_COMPAT, $charset);
                }
            break;
            case 'quote':
                return quotemeta($string);
            case 'regexp':
            case 'regular expression':
                return preg_quote($string, '/');
            default:
                $message = "The value of the \$encoding parameter (argument 1) is invalid: '" . $encoding . "'.";
                throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
    }

    /**
     * decode a string (revertable)
     *
     * Note: charset applies only to encoding = "entities"
     *
     * This function is the opposite of "encode()".
     * See "encode()" for details on the available types of
     * encoding.
     *
     * @param   string  $string    string
     * @param   string  $encoding  encoding name
     * @param   int     $style     (optional)
     * @param   string  $charset   (optional)
     * @return  string
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when the selected encoding is not valid
     *
     * @name    Strings::decode()
     * @see     Strings::encrypt()
     * @see     Strings::encode()
     */
    public static function decode($string, $encoding, $style = ENT_COMPAT, $charset = "")
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_string($encoding); // Wrong argument type for argument 2. String expected.');
        assert('is_int($style); // Wrong argument type for argument 3. Integer expected.');
        assert('is_string($charset); // Wrong argument type for argument 4. String expected.');

        switch (mb_strtolower($encoding))
        {
            case 'unicode':
            case 'utf':
            case 'utf8':
                return utf8_decode($string);
            case 'base64':
                return base64_decode($string);
            case 'url':
                return urldecode($string);
            case 'rawurl':
                return rawurldecode($string);
            case 'rot13':
                return str_rot13($string);
            case 'entities':
                // @codeCoverageIgnoreStart
                if ($style == ENT_COMPAT || $style == ENT_QUOTES || $style == ENT_NOQUOTES) {
                    if ($charset != "") {
                        return html_entity_decode($string, $style, $charset);
                    }
                    return html_entity_decode($string, $style);
                }
                return html_entity_decode($string);
                // @codeCoverageIgnoreEnd
            default:
                $message = "The value of the \$encoding parameter (argument 1) is invalid: '" . $encoding . "'.";
                throw new \Yana\Core\Exceptions\InvalidArgumentException($message, \Yana\Log\TypeEnumeration::WARNING);
        }
    }

    /**
     * Return a lower-cased version of the string.
     *
     * @param   string  $string  text in mixed case
     * @return  string
     *
     * @name    Strings::toLowerCase()
     * @see     Strings::toUpperCase()
     *
     * @assert ("AbC") == "abc"
     */
    public static function toLowerCase($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        return mb_strtolower($string);
    }

    /**
     * Return a upper-cased version of the string.
     *
     * @param   string  $string  text in mixed case
     * @return  string
     *
     * @name    Strings::toUpperCase()
     * @see     Strings::toLowerCase()
     *
     * @assert ("AbC") == "ABC"
     */
    public static function toUpperCase($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        return mb_strtoupper($string);
    }

    /**
     * Extract a substring.
     *
     * Returns a substring beginning at character-offset $start with
     * $length characters.
     * See PHP-Manual "string functions" "mb_substr()" for details.
     *
     * @param   string  $string  base string
     * @param   int     $start   position of start character
     * @param   int     $length  number of characters to return (0 = all)
     * @return  string
     *
     * @name    Strings::substring()
     *
     * @assert ("abc", 1) == "bc"
     * @assert ("abc", 1, 1) == "b"
     * @assert ("abc", 0, -1) == "ab"
     */
    public static function substring($string, $start, $length = 0)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_int($start); // Wrong argument type for argument 2. Integer expected.');
        assert('is_int($length); // Wrong argument type for argument 3. Integer expected.');

        if ($length != 0) {
            return mb_substr($string, $start, $length);
        } else {
            return mb_substr($string, $start);
        }
    }

    /**
     * Compare two strings.
     *
     * Returns
     * <ul>
     *     <li>    int(-1)  if this string < $anotherString      </li>
     *     <li>    int(+0)  if this string === $anotherString    </li>
     *     <li>    int(+1)  if this string > $anotherString      </li>
     * </ul>
     *
     * Note: This function is case-sensitive.
     *
     * @param   string  $string         string
     * @param   string  $anotherString  some other string
     * @return  int(+1)|int(0)|int(-1)
     *
     * @name    Strings::compareTo()
     * @see     Strings::compareToIgnoreCase()
     *
     * @assert ("a", "b") == -1
     * @assert ("a", "a") == 0
     * @assert ("a", "A") == +1
     * @assert ("b", "a") == +1
     */
    public static function compareTo($string, $anotherString)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_string($anotherString); // Wrong argument type for argument 2. String expected.');
        return strcmp($string, $anotherString);
    }

    /**
     * Compare two strings (ignore case).
     *
     * Returns
     * <ul>
     *     <li>    int(-1)  if this string < $anotherString      </li>
     *     <li>    int(+0)  if this string === $anotherString    </li>
     *     <li>    int(+1)  if this string > $anotherString      </li>
     * </ul>
     *
     * Note: This function is NOT case-sensitive.
     *
     * @param   string  $string         string
     * @param   string  $anotherString  some other string
     * @return  int(+1)|int(0)|int(-1)
     *
     * @name    Strings::compareToIgnoreCase()
     * @see     Strings::compareTo()
     *
     * @assert ("a", "b") == -1
     * @assert ("a", "a") == 0
     * @assert ("a", "A") == 0
     * @assert ("b", "a") == +1
     */
    public static function compareToIgnoreCase($string, $anotherString)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_string($anotherString); // Wrong argument type for argument 2. String expected.');
        return strcasecmp($string, $anotherString);
    }

    /**
     * Match string against regular expression.
     *
     * Returns an array containing the FIRST set of matches or bool(false) if
     * the regular expression did not match at all.
     *
     * @param   string  $string              haystack
     * @param   string  $regularExpression   regular expresion
     * @param   int     &$count              returns number of times the expression matches
     * @return  array|bool(false)
     *
     * @name    Strings::match()
     * @see     Strings::matchAll()
     * @assert  ("b", "/a/") == false
     * @assert  ("abc", "/a(b)c/") == array("abc", "b")
     */
    public static function match($string, $regularExpression, &$count = null)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_string($regularExpression); // Wrong argument type for argument 2. String expected.');
        $matches = array();
        $count = (int) preg_match($regularExpression, $string, $matches);
        if ($count > 0) {
            assert('is_array($matches);');
            return $matches;
        } else {
            return false;
        }
    }

    /**
     * Match string against regular expression (return all results).
     *
     * Returns an array containing ALL the matches or bool(false) if
     * the regular expression did not match at all.
     *
     * @param   string  $string              haystack
     * @param   string  $regularExpression   regular expresion
     * @param   int     &$count              returns number of times the expression matches
     * @return  array|bool(false)
     *
     * @name    Strings::matchAll()
     * @see     Strings::match()
     * @assert  ("b", "/a/") == false
     * @assert  ("abcab", "/a(b)/") == array(array("ab", "ab"), array("b", "b"))
     */
    public static function matchAll($string, $regularExpression, &$count = null)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_string($regularExpression); // Wrong argument type for argument 2. String expected.');
        $matches = array();
        $count = (int) preg_match_all($regularExpression, $string, $matches);
        if ($count > 0) {
            assert('is_array($matches);');
            return $matches;
        } else {
            return false;
        }
    }

    /**
     * Replace a needle with a substitute.
     *
     * @param   string  $string      haystack
     * @param   string  $needle      replaced string
     * @param   string  $substitute  new string
     * @param   int     &$count      number of times the string is replaced
     * @return  string
     *
     * @name    Strings::replace()
     * @see     Strings::replaceRegExp()
     * @assert  ("a", "b") == "a"
     * @assert  ("a", "a", "b") == "b"
     */
    public static function replace($string, $needle, $substitute = "", &$count = null)
    {
        return str_replace($needle, $substitute, $string, $count);
    }

    /**
     * Returns bool(true) if a string is contained in another.
     *
     * @param   string  $string  haystack
     * @param   string  $needle  string to search for
     * @return  bool
     *
     * @assert  ("abc", "a") == true
     * @assert  ("abc", "A") == false
     * @assert  ("abc", "d") == false
     */
    public static function contains($string, $needle)
    {
        assert('is_string($string); // Invalid argument $string: string expected');
        assert('is_string($needle); // Invalid argument $needle: string expected');

        return mb_strpos($string, $needle) !== false;
    }

    /**
     * replace a substring by using a regular expression
     *
     * This will replace all hits of the Perl-compatible $regularExpression with $substitute.
     *
     * @param   string  $string             haystack
     * @param   string  $regularExpression  regular expression of replaced string
     * @param   string  $substitute         new string, may return back-references
     * @param   int     $limit              must be a positive integer > 0, defaults to -1 (no limit)
     * @param   int     &$count             number of times the string is replaced
     * @return  int
     *
     * @name    Strings::replaceRegExp()
     * @see     Strings::replace()
     * @assert  ("a", "/b/") == "a"
     * @assert  ("a", "/a/", "b") == "b"
     */
    public static function replaceRegExp($string, $regularExpression, $substitute = "", $limit = -1, &$count = null)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_string($regularExpression); // Wrong argument type for argument 2. String expected.');
        assert('is_string($substitute); // Wrong argument type for argument 3. String expected.');
        assert('is_int($limit); // Wrong argument type for argument 4. Integer expected.');

        /**
         * Limit must be a positive integer > 0.
         * All other values default to -1 (= no limit).
         */
        if ($limit < 1) {
            $limit = -1;
        }

        return preg_replace($regularExpression, $substitute, $string, $limit, $count);
    }

    /**
     * Get the number of characters in the string.
     *
     * This function is unicode-aware.
     *
     * @param   string  $string   string
     * @return  int
     *
     * @name    Strings::length()
     * @assert  ("") == 0
     * @assert  ("a") == 1
     * @assert  ("ä") == 1
     */
    public static function length($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        return mb_strlen($string);
    }

    /**
     * Convert string to an array.
     *
     * @param   string  $string     text to split
     * @param   string  $separator  delimiter
     * @param   int     $limit      maximum number of chunks
     * @return  array
     *
     * @name    Strings::split()
     * @see     Strings::splitRegExp()
     * @assert  ("a", "|") == array("a")
     * @assert  ("a|b", "|") == array("a", "b")
     * @assert  ("a|b|c", "|", 2) == array("a", "b|c")
     */
    public static function split($string, $separator, $limit = 0)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_string($separator); // Wrong argument type for argument 2. String expected.');
        assert('is_int($limit); // Wrong argument type for argument 3. Integer expected.');

        if ($limit > 0) {
            return explode($separator, $string, $limit);
        } else {
            return explode($separator, $string);
        }
    }

    /**
     * Convert string to an array by using regular expression to find a speratator.
     *
     * @param   string  $string     text to split
     * @param   string  $separator  regular expression of delimiter to search for
     * @param   int     $limit      maximum number of chunks
     * @return  array
     *
     * @name    Strings::splitRegExp()
     * @see     Strings::split()
     * @assert  ("a", "/\|/") == array("a")
     * @assert  ("a|b", "/\|/") == array("a", "b")
     * @assert  ("a|b|c", "/\|/", 2) == array("a", "b|c")
     */
    public static function splitRegExp($string, $separator, $limit = 0)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_string($separator); // Wrong argument type for argument 2. String expected.');
        assert('is_int($limit); // Wrong argument type for argument 3. Integer expected.');

        if ($limit > 0) {
            return preg_split($separator, $string, $limit);
        } else {
            return preg_split($separator, $string);
        }
    }

    /**
     * Get position of first occurence of a needle inside the string.
     *
     * Returns character-offset of first occurence of $needle within this string.
     * Indices starting with int(0).
     *
     * Returns Java-style int(-1) if $needle is not found, NOT Php-style bool(false).
     * This is because int(0) and bool(false) might get mixed by accident.
     *
     * So while if <code>(strpos($string, $needle) == 0))</code> will return true,
     * even if $needle is not found, the test <code>if ($string->indexOf($needle) == 0)</code>
     * will return false if $needle is not found and true if and only if $string
     * starts with the string $needle.
     *
     * @param   string  $string  haystack
     * @param   string  $needle  text to search for
     * @param   int     $offset  character position from which to start searching
     * @return  int
     *
     * @name    Strings::indexOf()
     * @assert  ("a", "b") == -1
     * @assert  ("ab", "a", 1) == -1
     * @assert  ("ab", "b") == 1
     * @assert  ("ab", "b", 1) == 1
     * @assert  ("aä", "ä") == 1
     */
    public static function indexOf($string, $needle, $offset = 0)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_string($needle); // Wrong argument type for argument 2. String expected.');
        assert('is_int($offset); // Wrong argument type for argument 3. Integer expected.');

        if ($offset <= 0) {
            $offset = null;
        }
        $result = mb_strpos($string, $needle, $offset);
        if ($result === false) {
            return -1;
        } else {
            return $result;
        }
    }

    /**
     * Wrap a long text.
     *
     * @param   string  $string   text to wrap
     * @param   int     $width    maximum number of characters per line
     * @param   string  $break    character to use as line delimiter
     * @param   bool    $cut      true = hard cut (cut through words), false = soft cut (keep last word intact)
     * @return  string
     *
     * @name    Strings::wrap()
     * @assert  ("test abc", 3, ",", false) == "test,abc"
     * @assert  ("test test", 3, ",", true) == "tes,t,abc"
     */
    public static function wrap($string, $width = 75, $break = "\n", $cut = false)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_int($width); // Wrong argument type for argument 2. Integer expected.');
        assert('is_string($break); // Wrong argument type for argument 3. String expected.');
        assert('is_bool($cut); // Wrong argument type for argument 4. Boolean expected.');

        return wordwrap($string, $width, $break, $cut);
    }

    /**
     * Shuffle the string's characters.
     *
     * This implementation is Unicode-aware.
     *
     * @param   string  $string     string
     * @return  string
     *
     * @name    Strings::shuffle()
     * @assert  ("ä") == "ä"
     */
    public static function shuffle($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        $shuffle = (string) $string;
        if (strlen($shuffle) > 1) {
            $array = array();
            preg_match_all('/./us', $shuffle, $array);
            shuffle($array[0]);
            $shuffle = join('', $array[0]);
        }
        return $shuffle;
    }

    /**
     * Reverse the string value.
     *
     * This implementation is Unicode-aware.
     *
     * @param   string  $string  text to reverse
     * @return  string
     *
     * @name    Strings::reverse()
     * @assert  ("ä") == "ä"
     * @assert  ("abc") == "cba"
     */
    public static function reverse($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        $reverse = (string) $string;
        if (strlen($reverse) > 1) {
            $array = array();
            preg_match_all('/./us', $reverse, $array);
            $reverse = join('', array_reverse($array[0]));
        }
        return $reverse;
    }

    /**
     * Convert text to html entities.
     *
     * @param   string  $string  text to convert
     * @return  string
     *
     * @name    Strings::htmlEntities()
     * @see     Strings::encode()
     * @assert  (" ä") == "&#32;&#228;"
     */
    public static function htmlEntities($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        return self::encode($string, 'entities', ENT_FULL);
    }

    /**
     * Convert html special characters.
     *
     * This function is much like the original htmlspecialchars().
     *
     * Argument $quoteStyle changes handling of quotes:
     * ENT_COMPAT = encode " only, ENT_QUOTES = encode " and ', ENT_NOQUOTES = don't encode quotes.
     *
     * Unlike the original function, the $charset argument defaults to UTF-8.
     *
     * @param   string  $string        text to encode
     * @param   int     $quoteStyle    ENT_COMPAT, ENT_QUOTES, ENT_NOQUOTES
     * @param   string  $charset       e.g. UTF-8 or ISO-8859-1
     * @param   bool    $doubleEncode  set to true to avoid double encoded string
     * @return  string
     * @since   2.9.6
     * @assert  ("<ä id=\"\" title=''>") == "&lt;ä id=&quot;&quot; title=''&gt;"
     */
    public static function htmlSpecialChars($string, $quoteStyle = ENT_COMPAT, $charset = 'UTF-8', $doubleEncode = true)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_int($quoteStyle); // Wrong argument type for argument 2. Integer expected.');
        assert('is_string($charset); // Wrong argument type for argument 3. String expected.');
        assert('is_bool($doubleEncode); // Wrong argument type for argument 4. Boolean expected.');

        return htmlspecialchars($string, $quoteStyle, $charset, $doubleEncode);
    }

    /**
     * Returns bool(true) if the string starts with the given needle.
     *
     * @param   string  $string  text to search
     * @param   string  $needle  should start with this string
     * @return  bool
     *
     * @assert  ("test", "te") == true
     * @assert  ("test", "T") == false
     * @assert  ("test", "a") == false
     */
    public static function startsWith($string, $needle)
    {
        assert('is_string($string); // Invalid argument $string: string expected');
        assert('is_string($needle); // Invalid argument $needle: string expected');

        // We don't care for UTF-8 here, since 0 equals 0 - Unicode or not.
        return strpos($string, $needle) === 0;
    }

    /**
     * Returns bool(true) if the string ends with the given needle.
     *
     * @param   string  $string  text to search
     * @param   string  $needle  should start with this string
     * @return  bool
     *
     * @assert  ("test", "st") == true
     * @assert  ("test", "T") == false
     * @assert  ("test", "a") == false
     */
    public static function endsWith($string, $needle)
    {
        assert('is_string($string); // Invalid argument $string: string expected');
        assert('is_string($needle); // Invalid argument $needle: string expected');

        // No need to check for Unicode here, binary comparison will be fine.
        return strrpos($string, $needle, strlen($needle)) !== false;
    }

    /**
     * Replace each token within a text/template.
     *
     * NOTE: this method is not case-sensitive.
     *
     * @param   string  $string  haystack
     * @param   array   $array   values to replace
     * @param   string  $lDelim  left token delimiter (default = '{$')
     * @param   string  $rDelim  right token delimiter (default = '}')
     * @return  string
     */
    public static function replaceToken($string, array $array, $lDelim = null, $rDelim = null)
    {
        assert('is_string($string); // Wrong type for argument 1. String expected');

        if (is_null($lDelim)) {
            $lDelim = YANA_LEFT_DELIMITER . '$';
        }
        if (is_null($rDelim)) {
            $rDelim = YANA_RIGHT_DELIMITER;
        }
        $ldimRegExp = preg_quote($lDelim, '/');
        $rdimRegExp = preg_quote($rDelim, '/');

        $match = array();
        if (preg_match_all("/$ldimRegExp([\w_\.]+?)$rdimRegExp/", $string, $match) > 0) {
            $array = \Yana\Util\Hashtable::changeCase($array, \CASE_UPPER);
            foreach ($match[1] as $currentMatch)
            {
                $tmp = \Yana\Util\Hashtable::get($array, mb_strtoupper($currentMatch));
                /* if $tmp is NULL, the reference $match is pointing to a non-existing value */
                if (is_null($tmp) || !is_scalar($tmp)) {
                    continue;
                }

                $tmp = (string) $tmp;
                /**
                 * if the content string we got from the reference array contains token as well,
                 * we recursivle replace them.
                 */
                if (mb_strpos($tmp, $lDelim) !== false) {
                    assert('is_string($tmp); // Unexpected result: $tmp is supposed to be a string');
                    self::replaceToken($tmp, $array, $lDelim, $rDelim);
                }
                assert('is_string($tmp); // Unexpected result: $tmp is supposed to be a string');
                $regExpMatch = preg_quote($currentMatch, '/');
                $string = preg_replace("/(<[^\!^>]+){$ldimRegExp}{$regExpMatch}{$rdimRegExp}([^>]+>)/Usi", '${1}'.
                    addcslashes(htmlspecialchars($tmp, ENT_COMPAT, 'UTF-8'), '\\') . '${2}', $string);
                $string = str_replace($lDelim . $currentMatch . $rDelim, $tmp, $string);
            } // end for
        } // end if
        assert('is_string($string); // Unexpected result: $string is supposed to be a string');
        return $string;
    }

}

?>