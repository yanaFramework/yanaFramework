<?php
/**
 * YANA_Sniffs_Functions_CheckStatmentDeclarationSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dariusz Josko <D.Josko@cc-carconsult.de>
 * @copyright 2010 CC-carconsult
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: FunctionDeclarationSniff.php,v 1.4 2008/12/01 05:45:49 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * YANA_Sniffs_Functions_CheckStatmentDeclarationSniff.
 *
 * check the "if" and "elseif" statements for Variable assignments.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Dariusz Josko <D.Josko@cc-carconsult.de>
 * @copyright 2010 CC-carconsult
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.0RC3
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class YANA_Sniffs_Functions_CheckStatmentDeclarationSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_IF,
                T_ELSEIF
                );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        for ($i = ($stackPtr - 1); $i >= 0; $i--) {
            if ($tokens[$i]['line'] !== $tokens[$stackPtr]['line']) {
                if ($tokens[$stackPtr+3]['code'] == 1013 || $tokens[$stackPtr+4]['code'] == 1013  || $tokens[$stackPtr+5]['code'] == 1013 || $tokens[$stackPtr+6]['code'] == 1013) {
                    if ($tokens[$stackPtr+3]['content'] == '=') {
                        $error = "Variable assignments are not premitted in '".$tokens[$stackPtr]['content']."' statement, found : ".
                            $tokens[$stackPtr]['content'].$tokens[$stackPtr+1]['content'].$tokens[$stackPtr+2]['content'].
                            $tokens[$stackPtr+3]['content'].$tokens[$stackPtr+4]['content'].$tokens[$stackPtr+5]['content'].
                            $tokens[$stackPtr+6]['content'].$tokens[$stackPtr+7]['content'];
                    }
                    if ($tokens[$stackPtr+4]['content'] == '=') {
                        $error = "Variable assignments are not premitted in '".$tokens[$stackPtr]['content']."' statement, found : ".
                            $tokens[$stackPtr]['content'].$tokens[$stackPtr+1]['content'].$tokens[$stackPtr+2]['content'].
                            $tokens[$stackPtr+3]['content'].$tokens[$stackPtr+4]['content'].$tokens[$stackPtr+5]['content'].
                            $tokens[$stackPtr+6]['content'].$tokens[$stackPtr+7]['content'];
                    }
                    if ($tokens[$stackPtr+5]['content'] == '=') {
                        $error = "Variable assignments are not premitted in '".$tokens[$stackPtr]['content']."' statement, found : ".
                            $tokens[$stackPtr]['content'].$tokens[$stackPtr+1]['content'].$tokens[$stackPtr+2]['content'].
                            $tokens[$stackPtr+3]['content'].$tokens[$stackPtr+4]['content'].$tokens[$stackPtr+5]['content'].
                            $tokens[$stackPtr+6]['content'].$tokens[$stackPtr+7]['content'].$tokens[$stackPtr+8]['content'];
                    }
                    if ($tokens[$stackPtr+6]['content'] == '=') {
                        $error = "Variable assignments are not premitted in '".$tokens[$stackPtr]['content']."' statement, found : ".
                            $tokens[$stackPtr]['content'].$tokens[$stackPtr+1]['content'].$tokens[$stackPtr+2]['content'].
                            $tokens[$stackPtr+3]['content'].$tokens[$stackPtr+4]['content'].$tokens[$stackPtr+5]['content'].
                            $tokens[$stackPtr+6]['content'].$tokens[$stackPtr+7]['content'].$tokens[$stackPtr+8]['content'].
                            $tokens[$stackPtr+9]['content'];
                    }
                    $phpcsFile->addError($error, $stackPtr);
                }
                $i++;
                break;
            }
        }
    }

}//end class

?>
