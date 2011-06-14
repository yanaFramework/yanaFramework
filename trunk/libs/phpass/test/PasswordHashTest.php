<?php
/**
 * This is a test program for the portable PHP password hashing framework.
 *
 * Written by Solar Designer and placed in the public domain.
 * See PasswordHash.php for more information.
 */

namespace PhPass;

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__) . '/../PasswordHash.php';

/**
 * Test class for PasswordHash.
 * Generated by PHPUnit on 2011-06-14 at 16:54:31.
 */
class PasswordHashTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var PasswordHash
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new PasswordHash(8, false);
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
    public function testSecure()
    {
        # Try to use stronger but system-specific hashes, with a possible fallback to
        # the weaker portable hashes.

        $correct = 'test12345';
        $hash = $this->object->hashPassword($correct);

        $this->assertTrue($this->object->checkPassword($correct, $hash));

        $wrong = 'test12346';
        $this->assertFalse($this->object->checkPassword($wrong, $hash));
    }

    /**
     * @test
     */
    public function testFast()
    {
        $this->object = new PasswordHash(8, true);

        $correct = 'test12345';
        $hash = $this->object->hashPassword($correct);
        $this->assertTrue($this->object->checkPassword($correct, $hash));

        $wrong = 'test12346';
        $this->assertFalse($this->object->checkPassword($wrong, $hash));
    }


    /**
     * @test
     */
    public function testPortable()
    {
        $correct = 'test12345';
        # A correct portable hash for 'test12345'.
        # Please note the use of single quotes to ensure that the dollar signs will
        # be interpreted literally.  Of course, a real application making use of the
        # framework won't store password hashes within a PHP source file anyway.
        # We only do this for testing.
        $hash = '$P$9IQRaTwmfeRo7ud9Fh4E2PdI0S3r.L0';

        $this->assertTrue($this->object->checkPassword($correct, $hash));

        $wrong = 'test12346';
        $this->assertFalse($this->object->checkPassword($wrong, $hash));
    }

}

?>
