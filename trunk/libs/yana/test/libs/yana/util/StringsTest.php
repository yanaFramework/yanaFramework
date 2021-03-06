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
declare(strict_types=1);

namespace Yana\Util;

/**
 * @ignore
 */
require_once __Dir__ . '/../../../include.php';


/**
 * @package  test
 */
class StringsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * Generated from @assert ("1") == 1.
     */
    public function testToInt()
    {
        $this->assertEquals(
            1, \Yana\Util\Strings::toInt("1")
        );
    }

    /**
     * Generated from @assert ("1.5") == 1.
     */
    public function testToInt2()
    {
        $this->assertEquals(
            1, \Yana\Util\Strings::toInt("1.5")
        );
    }

    /**
     * Generated from @assert ("a") == false.
     */
    public function testToInt3()
    {
        $this->assertFalse(
            \Yana\Util\Strings::toInt("a")
        );
    }

    /**
     * Generated from @assert ("1") == 1.0.
     */
    public function testToFloat()
    {
        $this->assertEquals(
            1.0, \Yana\Util\Strings::toFloat("1")
        );
    }

    /**
     * Generated from @assert ("1.5") == 1.5.
     */
    public function testToFloat2()
    {
        $this->assertEquals(
            1.5, \Yana\Util\Strings::toFloat("1.5")
        );
    }

    /**
     * Generated from @assert ("a") == false.
     */
    public function testToFloat3()
    {
        $this->assertFalse(
            \Yana\Util\Strings::toFloat("a")
        );
    }

    /**
     * Generated from @assert ("True") == true.
     */
    public function testToBool()
    {
        $this->assertTrue(
            \Yana\Util\Strings::toBool("True")
        );
    }

    /**
     * Generated from @assert ("False") == false.
     */
    public function testToBool2()
    {
        $this->assertFalse(
            \Yana\Util\Strings::toBool("False")
        );
    }

    /**
     * Generated from @assert ("0") == false.
     */
    public function testToBool3()
    {
        $this->assertFalse(
            \Yana\Util\Strings::toBool("0")
        );
    }

    /**
     * Generated from @assert ("1") == true.
     */
    public function testToBool4()
    {
        $this->assertTrue(
            \Yana\Util\Strings::toBool("1")
        );
    }

    /**
     * Generated from @assert ("") == false.
     */
    public function testToBool5()
    {
        $this->assertFalse(
            \Yana\Util\Strings::toBool("")
        );
    }

    /**
     * Generated from @assert ("a") == false.
     */
    public function testToBool6()
    {
        $this->assertFalse(
            \Yana\Util\Strings::toBool("a")
        );
    }

    /**
     * Generated from @assert ("a", "a") == '\a'.
     */
    public function testAddSlashes()
    {
        $this->assertEquals(
            '\a', \Yana\Util\Strings::addSlashes("a", "a")
        );
    }

    /**
     * Generated from @assert ("a", "b") == 'a'.
     */
    public function testAddSlashes2()
    {
        $this->assertEquals(
            'a', \Yana\Util\Strings::addSlashes("a", "b")
        );
    }

    /**
     * Generated from @assert ('\\a') == '\\\\a'.
     */
    public function testAddSlashes3()
    {
        $this->assertEquals(
            '\\\\a', \Yana\Util\Strings::addSlashes('\\a')
        );
    }

    /**
     * Generated from @assert ("a") == 'a'.
     */
    public function testRemoveSlashes()
    {
        $this->assertEquals(
            'a', \Yana\Util\Strings::removeSlashes("a")
        );
    }

    /**
     * Generated from @assert ('\a') == 'a'.
     */
    public function testRemoveSlashes2()
    {
        $this->assertEquals(
            'a', \Yana\Util\Strings::removeSlashes('\a')
        );
    }

    /**
     * Generated from @assert ('\\\\a') == '\\a'.
     */
    public function testRemoveSlashes3()
    {
        $this->assertEquals(
            '\\a', \Yana\Util\Strings::removeSlashes('\\\\a')
        );
    }

    /**
     * Generated from @assert ("Test", 0) == "T".
     */
    public function testCharAt()
    {
        $this->assertEquals(
            "T", \Yana\Util\Strings::charAt("Test", 0)
        );
    }

    /**
     * Generated from @assert ("Test", 3) == "t".
     */
    public function testCharAt2()
    {
        $this->assertEquals(
            "t", \Yana\Util\Strings::charAt("Test", 3)
        );
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\OutOfBoundsException
     */
    public function testCharAtOutOfBoundsException()
    {
        \Yana\Util\Strings::charAt("t", 1);
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\OutOfBoundsException
     */
    public function testCharAtOutOfBoundsException2()
    {
        \Yana\Util\Strings::charAt("t", -1);
    }

    /**
     * Generated from @assert (" test ") == "test".
     */
    public function testTrim()
    {
        $this->assertEquals(
            "test", \Yana\Util\Strings::trim(" test ")
        );
    }

    /**
     * Generated from @assert (" test ", self::LEFT) == "test ".
     */
    public function testTrim2()
    {
        $this->assertEquals(
            "test ", \Yana\Util\Strings::trim(" test ", Strings::LEFT)
        );
    }

    /**
     * Generated from @assert (" test ", self::RIGHT) == " test".
     */
    public function testTrim3()
    {
        $this->assertEquals(
            " test", \Yana\Util\Strings::trim(" test ", Strings::RIGHT)
        );
    }

    /**
     * @test
     */
    public function testEncrypt()
    {
        $this->assertEquals(
                \crc32("test"), \Yana\Util\Strings::encrypt("test", "crc32")
        );
    }

    /**
     * Generated from @assert ("test", "md5") == "098f6bcd4621d373cade4e832627b4f6".
     */
    public function testEncrypt2()
    {
        $this->assertEquals(
            "098f6bcd4621d373cade4e832627b4f6", \Yana\Util\Strings::encrypt("test", "md5")
        );
    }

    /**
     * Generated from @assert ("test", "sha") == "a94a8fe5ccb19ba61c4c0873d391e987982fbbd3".
     */
    public function testEncrypt3()
    {
        $this->assertEquals(
            "a94a8fe5ccb19ba61c4c0873d391e987982fbbd3", \Yana\Util\Strings::encrypt("test", "sha")
        );
    }

    /**
     * Generated from @assert ("test", "crypt", "pass") == "pawpU97AVNPO6".
     */
    public function testEncrypt4()
    {
        $this->assertEquals(
            "pawpU97AVNPO6", \Yana\Util\Strings::encrypt("test", "crypt", "pass")
        );
    }

    /**
     * Generated from @assert ("test", "des") == NULL.
     */
    public function testEncrypt5()
    {
        $this->assertEquals(
            NULL, \Yana\Util\Strings::encrypt("test", "des")
        );
    }

    /**
     * Generated from @assert ("test", "des", "pass") == "pawpU97AVNPO6".
     */
    public function testEncrypt6()
    {
        $this->assertEquals(
            "pawpU97AVNPO6", \Yana\Util\Strings::encrypt("test", "des", "pass")
        );
    }

    /**
     * Generated from @assert ("test", "blowfish", "passwordpassword") == '$2vU67iv49YBo'.
     */
    public function testEncrypt7()
    {
        $this->assertEquals(
            '$2y$10$passwordpasswordpasswe5CTNQfLGuOENdWfsXOxrnwUshKsXqmu', \Yana\Util\Strings::encrypt("test", "blowfish", "passwordpasswordpassword")
        );
    }

    /**
     * Generated from @assert ("test", "soundex") == "T230".
     */
    public function testEncrypt8()
    {
        $this->assertEquals(
            "T230", \Yana\Util\Strings::encrypt("test", "soundex")
        );
    }

    /**
     * Generated from @assert ("test", "metaphone") == "TST".
     */
    public function testEncrypt9()
    {
        $this->assertEquals(
            "TST", \Yana\Util\Strings::encrypt("test", "metaphone")
        );
    }

    /**
     * Generated from @assert ("aaaa", "xor", "    ") == "AAAA".
     */
    public function testEncrypt10()
    {
        $this->assertEquals(
            "AAAA", \Yana\Util\Strings::encrypt("aaaa", "xor", "    ")
        );
    }

    /**
     * Generated from @assert ("AbC") == "abc".
     */
    public function testToLowerCase()
    {
        $this->assertEquals(
            "abc", \Yana\Util\Strings::toLowerCase("AbC")
        );
    }

    /**
     * Generated from @assert ("AbC") == "ABC".
     */
    public function testToUpperCase()
    {
        $this->assertEquals(
            "ABC", \Yana\Util\Strings::toUpperCase("AbC")
        );
    }

    /**
     * Generated from @assert ("abc", 1) == "bc".
     */
    public function testSubstring()
    {
        $this->assertEquals(
            "bc", \Yana\Util\Strings::substring("abc", 1)
        );
    }

    /**
     * Generated from @assert ("abc", 1, 1) == "b".
     */
    public function testSubstring2()
    {
        $this->assertEquals(
            "b", \Yana\Util\Strings::substring("abc", 1, 1)
        );
    }

    /**
     * Generated from @assert ("abc", 0, -1) == "ab".
     */
    public function testSubstring3()
    {
        $this->assertEquals(
            "ab", \Yana\Util\Strings::substring("abc", 0, -1)
        );
    }

    /**
     * Generated from @assert ("a", "b") == -1.
     */
    public function testCompareTo()
    {
        $this->assertEquals(
            -1, \Yana\Util\Strings::compareTo("a", "b")
        );
    }

    /**
     * Generated from @assert ("a", "a") == 0.
     */
    public function testCompareTo2()
    {
        $this->assertEquals(
            0, \Yana\Util\Strings::compareTo("a", "a")
        );
    }

    /**
     * Generated from @assert ("a", "A") == +1.
     */
    public function testCompareTo3()
    {
        $this->assertEquals(
            +1, \Yana\Util\Strings::compareTo("a", "A")
        );
    }

    /**
     * Generated from @assert ("b", "a") == +1.
     */
    public function testCompareTo4()
    {
        $this->assertEquals(
            +1, \Yana\Util\Strings::compareTo("b", "a")
        );
    }

    /**
     * Generated from @assert ("a", "b") == -1.
     */
    public function testCompareToIgnoreCase()
    {
        $this->assertEquals(
            -1, \Yana\Util\Strings::compareToIgnoreCase("a", "b")
        );
    }

    /**
     * Generated from @assert ("a", "a") == 0.
     */
    public function testCompareToIgnoreCase2()
    {
        $this->assertEquals(
            0, \Yana\Util\Strings::compareToIgnoreCase("a", "a")
        );
    }

    /**
     * Generated from @assert ("a", "A") == 0.
     */
    public function testCompareToIgnoreCase3()
    {
        $this->assertEquals(
            0, \Yana\Util\Strings::compareToIgnoreCase("a", "A")
        );
    }

    /**
     * Generated from @assert ("b", "a") == +1.
     */
    public function testCompareToIgnoreCase4()
    {
        $this->assertEquals(
            +1, \Yana\Util\Strings::compareToIgnoreCase("b", "a")
        );
    }

    /**
     * Generated from @assert ("b", "/a/") == false.
     */
    public function testMatch()
    {
        $this->assertFalse(
            \Yana\Util\Strings::match("b", "/a/")
        );
    }

    /**
     * Generated from @assert ("abc", "/a(b)c/") == array("abc", "b").
     */
    public function testMatch2()
    {
        $this->assertEquals(
            array("abc", "b"), \Yana\Util\Strings::match("abc", "/a(b)c/")
        );
    }

    /**
     * Generated from @assert ("b", "/a/") == false.
     */
    public function testMatchAll()
    {
        $this->assertFalse(
            \Yana\Util\Strings::matchAll("b", "/a/")
        );
    }

    /**
     * Generated from @assert ("abcab", "/a(b)/") == array(array("ab", "ab"), array("b", "b")).
     */
    public function testMatchAll2()
    {
        $this->assertEquals(
            array(array("ab", "ab"), array("b", "b")), \Yana\Util\Strings::matchAll("abcab", "/a(b)/")
        );
    }

    /**
     * Generated from @assert ("a", "b") == "a".
     */
    public function testReplace()
    {
        $this->assertEquals(
            "a", \Yana\Util\Strings::replace("a", "b")
        );
    }

    /**
     * Generated from @assert ("a", "a", "b") == "b".
     */
    public function testReplace2()
    {
        $this->assertEquals(
            "b", \Yana\Util\Strings::replace("a", "a", "b")
        );
    }

    /**
     * Generated from @assert ("a", "/b/") == "a".
     */
    public function testReplaceRegExp()
    {
        $this->assertEquals(
            "a", \Yana\Util\Strings::replaceRegExp("a", "/b/")
        );
    }

    /**
     * Generated from @assert ("a", "/a/", "b") == "b".
     */
    public function testReplaceRegExp2()
    {
        $this->assertEquals(
            "b", \Yana\Util\Strings::replaceRegExp("a", "/a/", "b")
        );
    }

    /**
     * Generated from @assert ("") == 0.
     */
    public function testLength()
    {
        $this->assertEquals(
            0, \Yana\Util\Strings::length("")
        );
    }

    /**
     * Generated from @assert ("a") == 1.
     */
    public function testLength2()
    {
        $this->assertEquals(
            1, \Yana\Util\Strings::length("a")
        );
    }

    /**
     * Generated from @assert ("ä") == 1.
     */
    public function testLength3()
    {
        $this->assertEquals(
            1, \Yana\Util\Strings::length("ä")
        );
    }

    /**
     * Generated from @assert ("a", "|") == array("a").
     */
    public function testSplit()
    {
        $this->assertEquals(
            array("a"), \Yana\Util\Strings::split("a", "|")
        );
    }

    /**
     * Generated from @assert ("a|b", "|") == array("a", "b").
     */
    public function testSplit2()
    {
        $this->assertEquals(
            array("a", "b"), \Yana\Util\Strings::split("a|b", "|")
        );
    }

    /**
     * Generated from @assert ("a|b|c", "|", 2) == array("a", "b|c").
     */
    public function testSplit3()
    {
        $this->assertEquals(
            array("a", "b|c"), \Yana\Util\Strings::split("a|b|c", "|", 2)
        );
    }

    /**
     * Generated from @assert ("a", "/\|/") == array("a").
     */
    public function testSplitRegExp()
    {
        $this->assertEquals(
            array("a"), \Yana\Util\Strings::splitRegExp("a", "/\|/")
        );
    }

    /**
     * Generated from @assert ("a|b", "/\|/") == array("a", "b").
     */
    public function testSplitRegExp2()
    {
        $this->assertEquals(
            array("a", "b"), \Yana\Util\Strings::splitRegExp("a|b", "/\|/")
        );
    }

    /**
     * Generated from @assert ("a|b|c", "/\|/", 2) == array("a", "b|c").
     */
    public function testSplitRegExp3()
    {
        $this->assertEquals(
            array("a", "b|c"), \Yana\Util\Strings::splitRegExp("a|b|c", "/\|/", 2)
        );
    }

    /**
     * Generated from @assert ("a", "b") == -1.
     */
    public function testIndexOf()
    {
        $this->assertEquals(
            -1, \Yana\Util\Strings::indexOf("a", "b")
        );
    }

    /**
     * Generated from @assert ("ab", "a", 1) == -1.
     */
    public function testIndexOf2()
    {
        $this->assertEquals(
            -1, \Yana\Util\Strings::indexOf("ab", "a", 1)
        );
    }

    /**
     * Generated from @assert ("ab", "b") == 1.
     */
    public function testIndexOf3()
    {
        $this->assertEquals(
            1, \Yana\Util\Strings::indexOf("ab", "b")
        );
    }

    /**
     * Generated from @assert ("ab", "b", 1) == 1.
     */
    public function testIndexOf4()
    {
        $this->assertEquals(
            1, \Yana\Util\Strings::indexOf("ab", "b", 1)
        );
    }

    /**
     * Generated from @assert ("aä", "ä") == 1.
     */
    public function testIndexOf5()
    {
        $this->assertEquals(
            1, \Yana\Util\Strings::indexOf("aä", "ä")
        );
    }

    /**
     * Generated from @assert ("ä") == "ä".
     */
    public function testShuffle()
    {
        $this->assertEquals(
            "ä", \Yana\Util\Strings::shuffle("ä")
        );
    }

    /**
     * Generated from @assert ("ä") == "ä".
     */
    public function testReverse()
    {
        $this->assertEquals(
            "ä", \Yana\Util\Strings::reverse("ä")
        );
    }

    /**
     * Generated from @assert ("abc") == "cba".
     */
    public function testReverse2()
    {
        $this->assertEquals(
            "cba", \Yana\Util\Strings::reverse("abc")
        );
    }

    /**
     * Generated from @assert (" ä") == "&#32;&#228;".
     */
    public function testHtmlEntities()
    {
        $this->assertEquals(
            "&#32;&#228;", \Yana\Util\Strings::htmlEntities(" ä")
        );
    }

    /**
     * Generated from @assert ("<ä id=\"\" title=''>") == "&lt;ä id=&quot;&quot; title=''&gt;".
     */
    public function testHtmlSpecialChars()
    {
        $this->assertEquals(
            "&lt;ä id=&quot;&quot; title=''&gt;", \Yana\Util\Strings::htmlSpecialChars("<ä id=\"\" title=''>")
        );
    }

    /**
     * Generated from @assert ("test", "te") == true.
     */
    public function testStartsWith()
    {
        $this->assertTrue(
            \Yana\Util\Strings::startsWith("test", "te")
        );
    }

    /**
     * Generated from @assert ("test", "T") == false.
     */
    public function testStartsWith2()
    {
        $this->assertFalse(
            \Yana\Util\Strings::startsWith("test", "T")
        );
    }

    /**
     * Generated from @assert ("test", "a") == false.
     */
    public function testStartsWith3()
    {
        $this->assertFalse(
            \Yana\Util\Strings::startsWith("test", "a")
        );
    }

    /**
     * Generated from @assert ("test", "st") == true.
     */
    public function testEndsWith()
    {
        $this->assertTrue(
            \Yana\Util\Strings::endsWith("test", "st")
        );
    }

    /**
     * Generated from @assert ("test", "T") == false.
     */
    public function testEndsWith2()
    {
        $this->assertFalse(
            \Yana\Util\Strings::endsWith("test", "T")
        );
    }

    /**
     * Generated from @assert ("test", "a") == false.
     */
    public function testEndsWith3()
    {
        $this->assertFalse(
            \Yana\Util\Strings::endsWith("test", "a")
        );
    }

    /**
     * Generated from @assert ("test", "tester") == false.
     */
    public function testEndsWith4()
    {
        $this->assertFalse(
            \Yana\Util\Strings::endsWith("test", "tester")
        );
    }

    /**
     * Generated from @assert  ("test", "s") == false.
     */
    public function testEndsWith5()
    {
        $this->assertFalse(
            \Yana\Util\Strings::endsWith("test", "s")
        );
    }

    /**
     * @test
     */
    public function testEncode()
    { 
        $encoding = array('unicode',
                          'utf',
                          'utf8',
                          'base64',
                          'url',
                          'rawurl',
                          'entities',
                          'quote',
                          'rot13',);
        foreach($encoding as $code)
        {  
            $encode = \Yana\Util\Strings::encode('this is a test string äöü', $code, ENT_COMPAT, 'UTF-8');
            $this->assertNotEquals('this is a test string äöü', $code);
            if ($code != 'quote') {
                $decode = \Yana\Util\Strings::decode($encode, $code, ENT_COMPAT, 'UTF-8');
                $this->assertEquals('this is a test string äöü', $decode, 'assert failed, the expected result must be equal for decoding ' . $code);
            }
        }
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testEncodeInvalidArgumentException()
    {
        \Yana\Util\Strings::encode('test', 'invalid');
    }

    /**
     * @test
     */
    public function testEncodeRegExp()
    {
        $this->assertSame('test\\/test', \Yana\Util\Strings::encode('test/test', 'regexp'));
        $this->assertSame('test\\/test', \Yana\Util\Strings::encode('test/test', 'regular expression'));
    }

    /**
     * @test
     */
    public function testEncodeHtmlEntities()
    {
        $this->assertSame('&auml;', \Yana\Util\Strings::encode('ä', 'entities', 1));
    }

    /**
     * testDecode
     *
     * @test
     */
    public function testDecode()
    {
        // intentionally left blank
    }

    /**
     * @test
     * @expectedException \Yana\Core\Exceptions\InvalidArgumentException
     */
    public function testDecodeInvalidArgumentException()
    {
        \Yana\Util\Strings::decode('test', 'invalid');
    }

    /**
     * @test
     */
    public function testDecodeHtmlEntities()
    {
        $this->assertSame('ä', \Yana\Util\Strings::decode('&auml;', 'entities', 1));
    }

    /**
     * Generated from @assert ("test abc", 3, ",", false) == "test,abc".
     */
    public function testWrap()
    {
        $this->assertEquals(
            "test,abc",
            \Yana\Util\Strings::wrap("test abc", 3, ",", false)
        );
    }

    /**
     * Generated from @assert ("test test", 3, ",", true) == "tes,t,abc".
     */
    public function testWrap2()
    {
        $this->assertEquals(
            "tes,t,abc",
            \Yana\Util\Strings::wrap("test abc", 3, ",", true)
        );
    }

    /**
     * Generated from @assert ("abc", "a") == true.
     */
    public function testContains()
    {
        $this->assertTrue(
            \Yana\Util\Strings::contains("abc", "a")
        );
    }
    /**
     * Generated from @assert ("abc", "A") == false.
     */
    public function testContains2()
    {
        $this->assertFalse(
            \Yana\Util\Strings::contains("abc", "A")
        );
    }
    /**
     * Generated from @assert ("abc", "a") == true.
     */
    public function testContains3()
    {
        $this->assertFalse(
            \Yana\Util\Strings::contains("abc", "d")
        );
    }
    /**
     * Generated from @assert ("abc", "a") == true.
     */
    public function testReplaceToken()
    {
        $this->assertSame("abc", \Yana\Util\Strings::replaceToken("abc", array()));
        $this->assertSame("adc", \Yana\Util\Strings::replaceToken('a{B}c', array('B' => 'd'), '{', '}'));
        $this->assertSame("adc", \Yana\Util\Strings::replaceToken('a{b}c', array('B' => 'd'), '{', '}'));
        $this->assertSame("adc", \Yana\Util\Strings::replaceToken('a{B}c', array('b' => 'd'), '{', '}'));
    }

}
