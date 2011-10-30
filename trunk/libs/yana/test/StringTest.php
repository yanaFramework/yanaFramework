<?php
/**
 * PHPUnit test-case: String
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

namespace Yana\Util;

/**
 * @ignore
 */
require_once dirname(__FILE__) . '/include.php';

/**
 * Test class for String
 *
 * @package  test
 */
class StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    /**
     * Generated from @assert ("1") == 1.
     *
     * @test
     */
    public function testToInt()
    {
        $this->assertEquals(
          1,
          String::toInt("1")
        );
    }

    /**
     * Generated from @assert ("1.5") == 1.
     *
     * @test
     */
    public function testToInt2()
    {
        $this->assertEquals(
          1,
          String::toInt("1.5")
        );
    }

    /**
     * Generated from @assert ("a") == false.
     *
     * @test
     */
    public function testToInt3()
    {
        $this->assertFalse(
          String::toInt("a")
        );
    }

    /**
     * Generated from @assert ("1") == 1.0.
     *
     * @test
     */
    public function testToFloat()
    {
        $this->assertEquals(
          1.0,
          String::toFloat("1")
        );
    }

    /**
     * Generated from @assert ("1.5") == 1.5.
     *
     * @test
     */
    public function testToFloat2()
    {
        $this->assertEquals(
          1.5,
          String::toFloat("1.5")
        );
    }

    /**
     * Generated from @assert ("a") == false.
     *
     * @test
     */
    public function testToFloat3()
    {
        $this->assertFalse(
          String::toFloat("a")
        );
    }

    /**
     * Generated from @assert ("True") == true.
     *
     * @test
     */
    public function testToBool()
    {
        $this->assertTrue(
          String::toBool("True")
        );
    }

    /**
     * Generated from @assert ("False") == false.
     *
     * @test
     */
    public function testToBool2()
    {
        $this->assertFalse(
          String::toBool("False")
        );
    }

    /**
     * Generated from @assert ("0") == false.
     *
     * @test
     */
    public function testToBool3()
    {
        $this->assertFalse(
          String::toBool("0")
        );
    }

    /**
     * Generated from @assert ("1") == true.
     *
     * @test
     */
    public function testToBool4()
    {
        $this->assertTrue(
          String::toBool("1")
        );
    }

    /**
     * Generated from @assert ("") == false.
     *
     * @test
     */
    public function testToBool5()
    {
        $this->assertFalse(
          String::toBool("")
        );
    }

    /**
     * Generated from @assert ("a") == true.
     *
     * @test
     */
    public function testToBool6()
    {
        $this->assertTrue(
          String::toBool("a")
        );
    }

    /**
     * Generated from @assert ("a", "a") == '\a'.
     *
     * @test
     */
    public function testAddSlashes()
    {
        $this->assertEquals(
          '\a',
          String::addSlashes("a", "a")
        );
    }

    /**
     * Generated from @assert ("a", "b") == 'a'.
     *
     * @test
     */
    public function testAddSlashes2()
    {
        $this->assertEquals(
          'a',
          String::addSlashes("a", "b")
        );
    }

    /**
     * Generated from @assert ('\\a') == '\\\\a'.
     *
     * @test
     */
    public function testAddSlashes3()
    {
        $this->assertEquals(
          '\\\\a',
          String::addSlashes('\\a')
        );
    }

    /**
     * Generated from @assert ("a") == 'a'.
     *
     * @test
     */
    public function testRemoveSlashes()
    {
        $this->assertEquals(
          'a',
          String::removeSlashes("a")
        );
    }

    /**
     * Generated from @assert ('\a') == 'a'.
     *
     * @test
     */
    public function testRemoveSlashes2()
    {
        $this->assertEquals(
          'a',
          String::removeSlashes('\a')
        );
    }

    /**
     * Generated from @assert ('\\\\a') == '\\a'.
     *
     * @test
     */
    public function testRemoveSlashes3()
    {
        $this->assertEquals(
          '\\a',
          String::removeSlashes('\\\\a')
        );
    }

    /**
     * Generated from @assert ("Test", 0) == "T".
     *
     * @test
     */
    public function testCharAt()
    {
        $this->assertEquals(
          "T",
          String::charAt("Test", 0)
        );
    }

    /**
     * Generated from @assert ("Test", -1)
     *
     * @expectedException \Yana\Core\Exceptions\OutOfBoundsException
     * @test
     */
    public function testCharAt2()
    {
        $this->assertEquals(
            null,
            String::charAt("Test", -1)
        );
    }

    /**
     * Generated from @assert ("Test", 4)
     *
     * @expectedException \Yana\Core\Exceptions\OutOfBoundsException
     * @test
     */
    public function testCharAt3()
    {
        $this->assertEquals(
            null,
            String::charAt("Test", 4)
        );
    }

    /**
     * Generated from @assert ("Test", 3) == "t".
     *
     * @test
     */
    public function testCharAt4()
    {
        $this->assertEquals(
          "t",
          String::charAt("Test", 3)
        );
    }

    /**
     * Generated from @assert (" test ") == "test".
     *
     * @test
     */
    public function testTrim()
    {
        $this->assertEquals(
          "test",
          String::trim(" test ")
        );
    }

    /**
     * Generated from @assert (" test ", String::LEFT) == "test ".
     *
     * @test
     */
    public function testTrim2()
    {
        $this->assertEquals(
          "test ",
          String::trim(" test ", String::LEFT)
        );
    }

    /**
     * Generated from @assert (" test ", String::RIGHT) == " test".
     *
     * @test
     */
    public function testTrim3()
    {
        $this->assertEquals(
          " test",
          String::trim(" test ", String::RIGHT)
        );
    }

    /**
     * Generated from @assert ("test", "crc32") == -662733300.
     *
     * @test
     */
    public function testEncrypt()
    {
        $this->assertEquals(
          -662733300,
          String::encrypt("test", "crc32")
        );
    }

    /**
     * Generated from @assert ("test", "md5") == "098f6bcd4621d373cade4e832627b4f6".
     *
     * @test
     */
    public function testEncrypt2()
    {
        $this->assertEquals(
          "098f6bcd4621d373cade4e832627b4f6",
          String::encrypt("test", "md5")
        );
    }

    /**
     * Generated from @assert ("test", "sha") == "a94a8fe5ccb19ba61c4c0873d391e987982fbbd3".
     *
     * @test
     */
    public function testEncrypt3()
    {
        $this->assertEquals(
          "a94a8fe5ccb19ba61c4c0873d391e987982fbbd3",
          String::encrypt("test", "sha")
        );
    }

    /**
     * Generated from @assert ("test", "crypt", "pass") == "pawpU97AVNPO6".
     *
     * @test
     */
    public function testEncrypt4()
    {
        $this->assertEquals(
          "pawpU97AVNPO6",
          String::encrypt("test", "crypt", "pass")
        );
    }

    /**
     * Generated from @assert ("test", "des") == NULL.
     *
     * @test
     */
    public function testEncrypt5()
    {
        $this->assertEquals(
          NULL,
          String::encrypt("test", "des")
        );
    }

    /**
     * Generated from @assert ("test", "des", "pass") == "pawpU97AVNPO6".
     *
     * @test
     */
    public function testEncrypt6()
    {
        $this->assertEquals(
          "pawpU97AVNPO6",
          String::encrypt("test", "des", "pass")
        );
    }

    /**
     * Generated from @assert ("test", "blowfish", "passwordpassword") == "$2vU67iv49YBo".
     *
     * @test
     */
    public function testEncrypt7()
    {
        if (CRYPT_BLOWFISH) {
            $this->assertEquals(
              '$2vU67iv49YBo',
              String::encrypt("test", "blowfish", "passwordpassword")
            );
        } else {
            $this->assertEquals(
              null,
              String::encrypt("test", "blowfish", "passwordpassword")
            );
        }
    }

    /**
     * Generated from @assert ("test", "soundex") == "T230".
     *
     * @test
     */
    public function testEncrypt8()
    {
        $this->assertEquals(
          "T230",
          String::encrypt("test", "soundex")
        );
    }

    /**
     * Generated from @assert ("test", "metaphone") == "TST".
     *
     * @test
     */
    public function testEncrypt9()
    {
        $this->assertEquals(
          "TST",
          String::encrypt("test", "metaphone")
        );
    }

    /**
     * Generated from @assert ("aaaa", "xor", "    ") == "AAAA".
     *
     * @test
     */
    public function testEncrypt10()
    {
        $this->assertEquals(
          "AAAA",
          String::encrypt("aaaa", "xor", "    ")
        );
    }

    /**
     * Generated from @assert ("AbC") == "abc".
     *
     * @test
     */
    public function testToLowerCase()
    {
        $this->assertEquals(
          "abc",
          String::toLowerCase("AbC")
        );
    }

    /**
     * Generated from @assert ("AbC") == "ABC".
     *
     * @test
     */
    public function testToUpperCase()
    {
        $this->assertEquals(
          "ABC",
          String::toUpperCase("AbC")
        );
    }

    /**
     * Generated from @assert ("abc", 1) == "bc".
     *
     * @test
     */
    public function testSubstring()
    {
        $this->assertEquals(
          "bc",
          String::substring("abc", 1)
        );
    }

    /**
     * Generated from @assert ("abc", 1, 1) == "b".
     *
     * @test
     */
    public function testSubstring2()
    {
        $this->assertEquals(
          "b",
          String::substring("abc", 1, 1)
        );
    }

    /**
     * Generated from @assert ("abc", 1, -1) == "a".
     *
     * @test
     */
    public function testSubstring3()
    {
        $this->assertEquals(
          "ab",
          String::substring("abc", 0, -1)
        );
    }

    /**
     * Generated from @assert ("a", "b") == -1.
     *
     * @test
     */
    public function testCompareTo()
    {
        $this->assertEquals(
          -1,
          String::compareTo("a", "b")
        );
    }

    /**
     * Generated from @assert ("a", "a") == 0.
     *
     * @test
     */
    public function testCompareTo2()
    {
        $this->assertEquals(
          0,
          String::compareTo("a", "a")
        );
    }

    /**
     * Generated from @assert ("a", "A") == -1.
     *
     * @test
     */
    public function testCompareTo3()
    {
        $this->assertEquals(
          +1,
          String::compareTo("a", "A")
        );
    }

    /**
     * Generated from @assert ("b", "a") == +1.
     *
     * @test
     */
    public function testCompareTo4()
    {
        $this->assertEquals(
          +1,
          String::compareTo("b", "a")
        );
    }

    /**
     * Generated from @assert ("a", "b") == -1.
     *
     * @test
     */
    public function testCompareToIgnoreCase()
    {
        $this->assertEquals(
          -1,
          String::compareToIgnoreCase("a", "b")
        );
    }

    /**
     * Generated from @assert ("a", "a") == 0.
     *
     * @test
     */
    public function testCompareToIgnoreCase2()
    {
        $this->assertEquals(
          0,
          String::compareToIgnoreCase("a", "a")
        );
    }

    /**
     * Generated from @assert ("a", "A") == 0.
     *
     * @test
     */
    public function testCompareToIgnoreCase3()
    {
        $this->assertEquals(
          0,
          String::compareToIgnoreCase("a", "A")
        );
    }

    /**
     * Generated from @assert ("b", "a") == +1.
     *
     * @test
     */
    public function testCompareToIgnoreCase4()
    {
        $this->assertEquals(
          +1,
          String::compareToIgnoreCase("b", "a")
        );
    }

    /**
     * testEncode().
     * @covers String::encode
     * @covers String::decode
     *
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
            $encode = String::encode('this is a test string äöü', $code, ENT_COMPAT, 'UTF-8');
            $this->assertNotEquals('this is a test string äöü', $code, 'assert failed, expected two different strings for encoding '.$code.', result can not be equal');
            if($code != 'quote') {
                $decode = String::decode($encode, $code, ENT_COMPAT, 'UTF-8');
                $this->assertEquals('this is a test string äöü', $decode, 'assert failed, the expected result must be equal for decoding '.$code);
            }
        }
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
     * Generated from @assert ("b", "/a/") == false.
     */
    public function testMatch()
    {
        $this->assertFalse(
          String::match("b", "/a/")
        );
    }

    /**
     * Generated from @assert ("abc", "/a(b)c/") == array("abc", "b").
     */
    public function testMatch2()
    {
        $this->assertEquals(
          array("abc", "b"),
          String::match("abc", "/a(b)c/")
        );
    }

    /**
     * Generated from @assert ("b", "/a/") == false.
     */
    public function testMatchAll()
    {
        $this->assertFalse(
          String::matchAll("b", "/a/")
        );
    }

    /**
     * Generated from @assert ("abcab", "/a(b)/") == array(array("ab", "ab"), array("b", "b")).
     */
    public function testMatchAll2()
    {
        $this->assertEquals(
          array(array("ab", "ab"), array("b", "b")),
          String::matchAll("abcab", "/a(b)/")
        );
    }

    /**
     * Generated from @assert ("a", "b") == "a".
     */
    public function testReplace()
    {
        $this->assertEquals(
          "a",
          String::replace("a", "b")
        );
    }

    /**
     * Generated from @assert ("a", "a", "b") == "b".
     */
    public function testReplace2()
    {
        $this->assertEquals(
          "b",
          String::replace("a", "a", "b")
        );
    }

    /**
     * Generated from @assert ("a", "/b/") == "a".
     */
    public function testReplaceRegExp()
    {
        $this->assertEquals(
          "a",
          String::replaceRegExp("a", "/b/")
        );
    }

    /**
     * Generated from @assert ("a", "/a/", "b") == "b".
     */
    public function testReplaceRegExp2()
    {
        $this->assertEquals(
          "b",
          String::replaceRegExp("a", "/a/", "b")
        );
    }

    /**
     * Generated from @assert ("") == 0.
     */
    public function testLength()
    {
        $this->assertEquals(
          0,
          String::length("")
        );
    }

    /**
     * Generated from @assert ("a") == 1.
     */
    public function testLength2()
    {
        $this->assertEquals(
          1,
          String::length("a")
        );
    }

    /**
     * Generated from @assert ("ä") == 1.
     */
    public function testLength3()
    {
        $this->assertEquals(
          1,
          String::length("ä")
        );
    }

    /**
     * Generated from @assert ("a", "|") == array("a").
     */
    public function testSplit()
    {
        $this->assertEquals(
          array("a"),
          String::split("a", "|")
        );
    }

    /**
     * Generated from @assert ("a|b", "|") == array("a", "b").
     */
    public function testSplit2()
    {
        $this->assertEquals(
          array("a", "b"),
          String::split("a|b", "|")
        );
    }

    /**
     * Generated from @assert ("a|b|c", "|", 2) == array("a", "b|c").
     */
    public function testSplit3()
    {
        $this->assertEquals(
          array("a", "b|c"),
          String::split("a|b|c", "|", 2)
        );
    }

    /**
     * Generated from @assert ("a", "/\|/") == array("a").
     */
    public function testSplitRegExp()
    {
        $this->assertEquals(
          array("a"),
          String::splitRegExp("a", "/\|/")
        );
    }

    /**
     * Generated from @assert ("a|b", "/\|/") == array("a", "b").
     */
    public function testSplitRegExp2()
    {
        $this->assertEquals(
          array("a", "b"),
          String::splitRegExp("a|b", "/\|/")
        );
    }

    /**
     * Generated from @assert ("a|b|c", "/\|/", 2) == array("a", "b|c").
     */
    public function testSplitRegExp3()
    {
        $this->assertEquals(
          array("a", "b|c"),
          String::splitRegExp("a|b|c", "/\|/", 2)
        );
    }

    /**
     * Generated from @assert ("a", "b") == -1.
     */
    public function testIndexOf()
    {
        $this->assertEquals(
          -1,
          String::indexOf("a", "b")
        );
    }

    /**
     * Generated from @assert ("ab", "a", 1) == -1.
     */
    public function testIndexOf2()
    {
        $this->assertEquals(
          -1,
          String::indexOf("ab", "a", 1)
        );
    }

    /**
     * Generated from @assert ("ab", "b") == 1.
     */
    public function testIndexOf3()
    {
        $this->assertEquals(
          1,
          String::indexOf("ab", "b")
        );
    }

    /**
     * Generated from @assert ("ab", "b", 1) == 1.
     */
    public function testIndexOf4()
    {
        $this->assertEquals(
          1,
          String::indexOf("ab", "b", 1)
        );
    }

    /**
     * Generated from @assert ("aä", "ä") == 1.
     */
    public function testIndexOf5()
    {
        $this->assertEquals(
          1,
          String::indexOf("aä", "ä")
        );
    }

    /**
     * @todo Implement testWrap().
     */
    public function testWrap()
    {
        $text = 'this is a step for unit Tests';
        $wrap1 = String::wrap($text);
        $wrap2 = wordwrap($text);
        $this->assertEquals($wrap2, $wrap1, 'assert failed, the value can not be equal');
        $wrap1 = String::wrap($text, 20);
        $wrap2 = wordwrap($text, 20);
        $this->assertEquals($wrap2, $wrap1, 'assert failed, the value can not be equal');
        $wrap1 = String::wrap($text, 20, '|');
        $wrap2 = wordwrap($text, 20, '|');
        $this->assertEquals($wrap2, $wrap1, 'assert failed, the value can not be equal');
        $wrap1 = String::wrap($text, 20, '|', true);
        $wrap2 = wordwrap($text, 20, '|', true);
        $this->assertEquals($wrap2, $wrap1, 'assert failed, the value can not be equal');
    }

    /**
     * Generated from @assert ("ä") == "ä".
     */
    public function testShuffle()
    {
        $this->assertEquals(
          "ä",
          String::shuffle("ä")
        );
    }

    /**
     * Generated from @assert ("ä") == "ä".
     */
    public function testReverse()
    {
        $this->assertEquals(
          "ä",
          String::reverse("ä")
        );
    }

    /**
     * Generated from @assert ("abc") == "cba".
     */
    public function testReverse2()
    {
        $this->assertEquals(
          "cba",
          String::reverse("abc")
        );
    }

    /**
     * Generated from @assert (" ä") == "&#32;&#228;".
     */
    public function testHtmlEntities()
    {
        $this->assertEquals(
          "&#32;&#228;",
          String::htmlEntities(" ä")
        );
    }

    /**
     * @test
     */
    public function testHtmlSpecialChars()
    {
        $this->assertEquals(
            "&lt;ä id=&quot;&quot; title=''&gt;",
            String::htmlSpecialChars("<ä id=\"\" title=''>")
        );
    }

}

?>