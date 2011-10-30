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
 * <<Utility>> String
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
 * @access      public
 * @package     yana
 * @subpackage  util
 */
class String extends \Yana\Core\AbstractUtility
{
    /**#@+
     * used as 2nd argument in method String::trim()
     *
     * @see String::trim()
     */
    const BOTH = 0;
    const LEFT = 1;
    const RIGHT = 2;
    /**#@-*/

    /**
     * return value as int
     *
     * Converts the string value to an integer and
     * returns it. Returns bool(false) if the string
     * is not numeric.
     *
     * @access  public
     * @static
     * @param   string  $string  value to convert
     * @return  int|bool(false)
     *
     * @name    String::toInt()
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
     * return value as float
     *
     * Converts the string value to a float and
     * returns it. Returns bool(false) if the string
     * is not numeric.
     *
     * @access  public
     * @static
     * @param   string  $string  value to convert
     * @return  float|bool(false)
     *
     * @name    String::toFloat()
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
     * return value as boolean
     *
     * Returns a boolean value depending on the value of the string.
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
     * @access  public
     * @static
     * @param   string  $string  value to convert
     * @return  bool
     *
     * @name    String::toBool()
     *
     * @assert ("True") == true
     * @assert ("False") == false
     * @assert ("0") == false
     * @assert ("1") == true
     * @assert ("") == false
     * @assert ("a") == true
     */
    public static function toBool($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        switch (mb_strtolower($string))
        {
            case 'false':
                return false;
            break;
            case 'true':
                return true;
            break;
            default:
                return (bool) $string;
            break;
        }
    }

    /**
     * OO-Alias of: addslashes(), addcslashes()
     *
     * @access  public
     * @static
     * @param   string  $string    string
     * @param   string  $charlist  a string of characters that should be escaped
     * @return  string
     *
     * @name    String::addSlashes()
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
     * @access  public
     * @static
     * @param   string  $string     string
     * @return  string
     *
     * @name    String::removeSlashes()
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
     * Issues an E_USER_ERROR if $index is of wrong type.
     * Issues an E_USER_NOTICE if $index is out of bounds.
     *
     * Note that indices are numbered starting with '0'.
     *
     * @access  public
     * @static
     * @param   string  $string string
     * @param   int     $index  position of the character (starting with 0)
     *
     * @name    String::charAt()
     *
     * @assert ("Test", 0) == "T"
     * @assert ("Test", 3) == "t"
     */
    public static function charAt($string, $index)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_int($index); // Wrong argument type for argument 2. Integer expected.');
        /* check if $index is in bounds */
        /* If the input is no integer at all, issue an E_USER_ERROR and abort. */
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
     * @access  public
     * @static
     * @param   string  $string  string
     * @param   int     $type    may be one of
     * @return  string
     *
     * @name    String::trim()
     *
     * @assert (" test ") == "test"
     * @assert (" test ", String::LEFT) == "test "
     * @assert (" test ", String::RIGHT) == " test"
     */
    public static function trim($string, $type = String::BOTH)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_int($type); // Wrong argument type for argument 2. Integer expected.');
        switch ($type)
        {
            case String::LEFT:
                return ltrim($string);
            break;
            case String::RIGHT:
                return rtrim($string);
            break;
            default:
                return trim($string);
            break;
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
     * @access  public
     * @static
     * @param   string  $string      string
     * @param   string  $encryption  see the list of valid inputs for details
     * @param   string  $salt        only used for certain encryption types
     * @return  string
     *
     * @name    String::encrypt()
     * @see     String::encode()
     *
     * @assert ("test", "crc32") == -662733300
     * @assert ("test", "md5") == "098f6bcd4621d373cade4e832627b4f6"
     * @assert ("test", "sha") == "a94a8fe5ccb19ba61c4c0873d391e987982fbbd3"
     * @assert ("test", "crypt", "pass") == "pawpU97AVNPO6"
     * @assert ("test", "des") == NULL
     * @assert ("test", "des", "pass") == "pawpU97AVNPO6"
     * @assert ("test", "blowfish", "passwordpassword") == '$2vU67iv49YBo'
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
                if (function_exists('crc32')) {
                    return crc32($string);
                } else {
                    $message = "Unsupported encryption method: '$encryption'.";
                    throw new \Yana\Core\Exceptions\NotImplementedException($message);
                }
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
                if (function_exists('sha1')) {
                    return sha1($string);
                } else {
                    $message = "Unsupported encryption method: '$encryption'.";
                    throw new \Yana\Core\Exceptions\NotImplementedException($message);
                }
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
            case 'blowfish':
                if (CRYPT_BLOWFISH != 1 || mb_strlen($salt) < 12) {
                    return NULL;
                } else {
                    return crypt($string, '$2a$' . mb_substr($salt, 0, 12));
                }
            break;
            case 'soundex':
                if (function_exists('soundex')) {
                    return soundex($string);

                } else {
                    $message = "Unsupported encryption method: '$encryption'.";
                    throw new \Yana\Core\Exceptions\NotImplementedException($message);
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
                    $message = "Unsupported encryption method: '$encryption'.";
                    throw new \Yana\Core\Exceptions\NotImplementedException($message);
                }
            break;
            case 'xor':
                $pass = $salt;
                for ($i = 0; $i < strlen($string); $i++)
                {
                    $string[$i] = $string[$i] ^ $pass[$i % strlen($pass)];
                }
                return $string;
            break;
            default:
                $message = "Unsupported encryption method: '$encryption'.";
                throw new \Yana\Core\Exceptions\NotImplementedException($message);
            break;
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
     * @access  public
     * @static
     * @param   string  $string    string
     * @param   string  $encoding  see the list of valid inputs for details
     * @param   int     $style     used for entity conversion
     * @param   string  $charset   used for entity conversion
     * @return  string
     *
     * @name    String::encode()
     * @see     String::encrypt()
     * @see     String::decode()
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
            break;
            case 'base64':
                return base64_encode($string);
            break;
            case 'url':
                return urlencode($string);
            break;
            case 'rawurl':
                return rawurlencode($string);
            break;
            case 'rot13':
                return str_rot13($string);
            break;
            case 'entities':
                switch($style)
                {
                    case ENT_COMPAT:
                    case ENT_QUOTES:
                    case ENT_NOQUOTES:
                        return htmlentities($string, $style, $charset);
                    break;
                    case ENT_FULL:
                        return mb_encode_numericentity($string, array(0x0, 0xffff, 0, 0xffff), $charset);
                    break;
                    default:
                        return htmlentities($string, ENT_COMPAT, $charset);
                    break;
                }
            break;
            case 'quote':
                return quotemeta($string);
            break;
            case 'regexp':
            case 'regular expression':
                return preg_quote($string, '/');
            break;
            default:
                $message = "The value of the \$encoding parameter (argument 1) is invalid: '".$encoding."'.";
                trigger_error($message, E_USER_WARNING);
                return null;
            break;
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
     * @access  public
     * @static
     * @param   string  $string    string
     * @param   string  $encoding  encoding name
     * @param   int     $style     (optional)
     * @param   string  $charset   (optional)
     * @return  string
     *
     * @name    String::decode()
     * @see     String::encrypt()
     * @see     String::encode()
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
            break;
            case 'base64':
                return base64_decode($string);
            break;
            case 'url':
                return urldecode($string);
            break;
            case 'rawurl':
                return rawurldecode($string);
            break;
            case 'rot13':
                return str_rot13($string);
            break;
            case 'entities':
                if (($style == ENT_COMPAT || $style == ENT_QUOTES || $style == ENT_NOQUOTES)) {
                    if ($charset != "") {
                        return html_entity_decode($string, $style, $charset);
                    } else {
                        return html_entity_decode($string, $style);
                    }
                } else {
                    return html_entity_decode($string);
                }
            break;
            case 'rot13':
                return str_rot13($string);
            break;
            default:
                $message = "The value of the \$encoding parameter (argument 1) is invalid: '".$encoding."'.";
                trigger_error($message, E_USER_WARNING);
                return null;
            break;
        }
    }

    /**
     * return a lower-cased version of the string
     *
     * @access  public
     * @static
     * @param   string  $string     string
     * @return  string
     *
     * @name    String::toLowerCase()
     * @see     String::toUpperCase()
     *
     * @assert ("AbC") == "abc"
     */
    public static function toLowerCase($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        return mb_strtolower($string);
    }

    /**
     * return a upper-cased version of the string
     *
     * @access  public
     * @static
     * @param   string  $string     string
     * @return  string
     *
     * @name    String::toUpperCase()
     * @see     String::toLowerCase()
     *
     * @assert ("AbC") == "ABC"
     */
    public static function toUpperCase($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        return mb_strtoupper($string);
    }

    /**
     * extract a substring
     *
     * Returns a substring beginning at character-offset $start with
     * $length characters.
     * See PHP-Manual "string functions" "mb_substr()" for details.
     *
     * @access  public
     * @static
     * @param   string  $string  string
     * @param   int     $start   start
     * @param   int     $length  (optional)
     * @return  string
     *
     * @name    String::substring()
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
     * compare two strings
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
     * @access  public
     * @static
     * @param   string  $string         string
     * @param   string  $anotherString  some other string
     * @return  int(+1)|int(0)|int(-1)
     *
     * @name    String::compareTo()
     * @see     String::compareToIgnoreCase()
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
     * compare two strings (ignore case)
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
     * @access  public
     * @static
     * @param   string  $string         string
     * @param   string  $anotherString  some other string
     * @return  int(+1)|int(0)|int(-1)
     *
     * @name    String::compareToIgnoreCase()
     * @see     String::compareTo()
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
     * match string against regular expression
     *
     * Matches this string against a given Perl-compatible regular expression.
     * Returns an array containing the FIRST set of matches or bool(false) if
     * the regular expression did not match at all.
     *
     * @access  public
     * @static
     * @param   string  $string                 string
     * @param   string  $regularExpression      regular expresion
     * @param   int     &$count                 count
     * @return  array|bool(false)
     *
     * @name    String::match()
     * @see     String::matchAll()
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
     * match string against regular expression (return all results)
     *
     * Matches this string against a given Perl-compatible regular expression.
     * Returns an array containing ALL the matches or bool(false) if
     * the regular expression did not match at all.
     *
     * @access  public
     * @static
     * @param   string  $string                 string
     * @param   string  $regularExpression      regular expresion
     * @param   int     &$count                 count
     * @return  array|bool(false)
     *
     * @name    String::matchAll()
     * @see     String::match()
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
     * replace a needle with a substitute
     *
     * This will replace all entities of $needle with $substitute.
     * Returns the number of times $needle is replaced.
     *
     * @access  public
     * @static
     * @param   string  $string         string
     * @param   string  $needle         needle
     * @param   string  $substitute     (optional)
     * @param   int     &$count         (optional)
     * @return  string
     *
     * @name    String::replace()
     * @see     String::replaceRegExp()
     * @assert  ("a", "b") == "a"
     * @assert  ("a", "a", "b") == "b"
     */
    public static function replace($string, $needle, $substitute = "", &$count = null)
    {
        return str_replace($needle, $substitute, $string, $count);
    }

    /**
     * replace a substring by using a regular expression
     *
     * This will replace all hits of the Perl-compatible $regularExpression with $substitute.
     *
     * Returns the number of times $needle is replaced.
     *
     * @access  public
     * @static
     * @param   string  $string               string
     * @param   string  $regularExpression    regular expression
     * @param   string  $substitute           (optional)
     * @param   int     $limit                (optional) must be a positive integer > 0, defaults to -1 (no limit)
     * @param   int     &$count               (optional)
     * @return  int
     *
     * @name    String::replaceRegExp()
     * @see     String::replace()
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
     * get the length of the string
     *
     * Returns the number of characters in the string.
     *
     * @access  public
     * @static
     * @param   string  $string   string
     * @return  int
     *
     * @name    String::length()
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
     * convert string to an array
     *
     * @access  public
     * @static
     * @param   string  $string       string
     * @param   string  $separator    seperator
     * @param   int     $limit        limit
     * @return  array
     *
     * @name    String::split()
     * @see     String::splitRegExp()
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
     * convert string to an array by using regular expression to find a speratator
     *
     * @access  public
     * @static
     * @param   string  $string     string
     * @param   string  $separator  seperator
     * @param   int     $limit      limit
     * @return  array
     *
     * @name    String::splitRegExp()
     * @see     String::split()
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
     * get position of first occurence of a needle inside the string
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
     * @access  public
     * @static
     * @param   string  $string     string
     * @param   string  $needle     needle
     * @param   int     $offset     offset
     * @return  int
     *
     * @name    String::indexOf()
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
     * wrap a long text
     *
     * @access  public
     * @static
     * @param   string  $string     string
     * @param   int     $width      width
     * @param   string  $break      break
     * @param   bool    $cut        cut (DEFAULT false)
     * @return  string
     *
     * @name    String::wrap()
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
     * shuffle the string's characters
     *
     * This implementation is Unicode-aware.
     *
     * @access  public
     * @static
     * @param   string  $string     string
     * @return  string
     *
     * @name    String::shuffle()
     * @assert  ("ä") == "ä"
     */
    public static function shuffle($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        if (!empty($string)) {
            $array = array();
            preg_match_all('/./us', $string, $array);
            shuffle($array[0]);
            return join('', $array[0]);
        }
    }

    /**
     * reverse the string value
     *
     * This implementation is Unicode-aware.
     *
     * @access  public
     * @static
     * @param   string  $string     string
     * @return  string
     *
     * @name    String::reverse()
     * @assert  ("ä") == "ä"
     * @assert  ("abc") == "cba"
     */
    public static function reverse($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        if (!empty($string)) {
            $array = array();
            preg_match_all('/./us', $string, $array);
            return join('', array_reverse($array[0]));
        }
    }

    /**
     * convert to html entities
     *
     * @access  public
     * @static
     * @param   string  $string     string
     * @return  string
     *
     * @name    String::htmlEntities()
     * @see     String::encode()
     * @assert  (" ä") == "&#32;&#228;"
     */
    public static function htmlEntities($string)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        return String::encode($string, 'entities', ENT_FULL);
    }

    /**
     * convert html special characters
     *
     * This function is much like the original htmlspecialchars().
     *
     * Argument $quoteStyle changes handling of quotes:
     * ENT_COMPAT = encode " only, ENT_QUOTES = encode " and ', ENT_NOQUOTES = don't encode quotes.
     *
     * Unlike the original function, the $charset argument defaults to UTF-8.
     *
     * If $doubleEncode is set to false, the function will not encode existing HTML entities.
     * This argument was introduced in PHP 5.2.3.
     *
     * @access  public
     * @static
     * @param   string  $string        text to encode
     * @param   int     $quoteStyle    ENT_COMPAT, ENT_QUOTES, ENT_NOQUOTES
     * @param   string  $charset       e.g. UTF-8 or ISO-8859-1
     * @param   bool    $doubleEncode  set to true to avoid double encoded string
     * @return  string
     * @since   2.9.6
     */
    public static function htmlSpecialChars($string, $quoteStyle = ENT_COMPAT, $charset = 'UTF-8', $doubleEncode = true)
    {
        assert('is_string($string); // Wrong argument type for argument 1. String expected.');
        assert('is_int($quoteStyle); // Wrong argument type for argument 2. Integer expected.');
        assert('is_string($charset); // Wrong argument type for argument 3. String expected.');
        assert('is_bool($doubleEncode); // Wrong argument type for argument 4. Boolean expected.');

        return htmlspecialchars($string, $quoteStyle, $charset, $doubleEncode);
    }

}

?>