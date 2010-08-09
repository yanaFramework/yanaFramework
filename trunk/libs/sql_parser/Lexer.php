<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2004 Brent Cook                                        |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// |                                                                      |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// |                                                                      |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330,Boston,MA 02111-1307 USA|
// +----------------------------------------------------------------------+
// | Authors: Brent Cook <busterbcook@yahoo.com>                          |
// |          Jason Pell <jasonpell@hotmail.com>                          |
// +----------------------------------------------------------------------+
//
// $Id: Lexer.php,v 1.20 2004/05/07 12:33:35 busterb Exp $
//

include 'ctype.php';

// {{{ token definitions
// variables: 'ident', 'sys_var'
// values:    'real_val', 'text_val', 'int_val', null
// }}}

/**
 * A lexigraphical analyser inspired by the msql lexer
 *
 * @author  Brent Cook <busterbcook@yahoo.com>
 * @version 0.5
 * @access  public
 * @package SQL_Parser
 */
class Lexer implements Iterator
{
    // array of valid tokens for the lexer to recognize
    // format is 'token literal'=>TOKEN_VALUE
    protected $symbols = array();

// {{{ instance variables
    protected $tokPtr = 0;
    protected $tokStart = 0;
    protected $tokLen = 0;
    protected $tokText = '';
    protected $lineNo = 0;
    protected $lineBegin = 0;
    protected $string = '';
    protected $stringLen = 0;
    
    // Will not be altered by skip()
    protected $tokAbsStart = 0;
    protected $skipText = '';
    
    // Provide lookahead capability.
    protected $lookahead = 0;
    // Specify how many tokens to save in tokenStack, so the
    // token stream can be pushed back.
    protected $tokenStack = array();
    protected $stackPtr = 0;
// }}}

// {{{ incidental functions
    public function __construct($string = '', $lookahead=0)
    {
        $this->string = $string;
        $this->stringLen = strlen($string);
        $this->lookahead = $lookahead;
    }

    public function setSymbols(&$symbols) {
        $this->symbols =& $symbols;
    }

    public function nextChar() {
        ++$this->tokPtr;
        ++$this->tokLen;
        return $this->currentChar();
    }

    public function reverseChar() {
        --$this->tokPtr;
        --$this->tokLen;
        return $this->currentChar();
    }

    public function currentChar() {
        return ($this->tokPtr <= $this->stringLen) ? $this->string{$this->tokPtr - 1} : null;
    }

    public function skip() {
        ++$this->tokStart;
        return ($this->tokPtr != $this->stringLen) ? $this->string{$this->tokPtr++} : '';
    }

    public function valid() {
        return $this->stackPtr === 0 || !empty($this->token);
    }

    public function rewind() {
        $this->tokPtr = $this->tokStart;
        $this->tokLen = 0;
    }

    protected function isCompop($c) {
        return (($c == '<') || ($c == '>') || ($c == '=') || ($c == '!'));
    }
// }}}

    // {{{ reverse()
    /*
     * Push back a token, so the very next call to lex() will return that token.
     * Calls to this function will be ignored if there is no lookahead specified
     * to the constructor, or the pushBack() function has already been called the
     * maximum number of token's that can be looked ahead.
     */
    public function reverse()
    {
        if($this->lookahead>0 && count($this->tokenStack)>0 && $this->stackPtr>0) {
            $this->stackPtr--;
        }
    }
    // }}}

    // {{{ current()
    public function current()
    {
        return $this->tokText;
    }
    // }}}

    // {{{ key()
    public function key()
    {
        return $this->token;
    }
    // }}}

    // {{{ next()
    public function next()
    {
        if($this->lookahead>0) {
            // The stackPtr, should always be the same as the count of
            // elements in the tokenStack.  The stackPtr, can be thought
            // of as pointing to the next token to be added.  If however
            // a pushBack() call is made, the stackPtr, will be less than the
            // count, to indicate that we should take that token from the
            // stack, instead of calling nextToken for a new token.

            if ($this->stackPtr<count($this->tokenStack)) {

                $this->tokText = $this->tokenStack[$this->stackPtr]['tokText'];
                $this->skipText = $this->tokenStack[$this->stackPtr]['skipText'];
                $token = $this->tokenStack[$this->stackPtr]['token'];

                // We have read the token, so now iterate again.
                $this->stackPtr++;
                $this->token = $token;

            } else {

                // If $tokenStack is full (equal to lookahead), pop the oldest
                // element off, to make room for the new one.

                if ($this->stackPtr == $this->lookahead) {
                    // For some reason array_shift and
                    // array_pop screw up the indexing, so we do it manually.
                    for($i=0; $i<(count($this->tokenStack)-1); $i++) {
                        $this->tokenStack[$i] = $this->tokenStack[$i+1];
                    }

                    // Indicate that we should put the element in
                    // at the stackPtr position.
                    $this->stackPtr--;
                }

                $token = $this->parseToken();
                $this->tokenStack[$this->stackPtr] =
                    array('token'=>$token,
                          'tokText'=>$this->tokText,
                          'skipText'=>$this->skipText);
                $this->stackPtr++;
                $this->token = $token;
            }
        }
        else
        {
            $this->token = $this->parseToken();
        }
    }
    // }}}

    // {{{ parseToken()
    protected function parseToken()
    {
        if ($this->string == '') return;
        $state = 0;
        $this->tokAbsStart = $this->tokStart;

        while (true){
            //echo "State: $state, Char: $c\n";
            switch($state) {
                // {{{ State 0 : Start of token
                case 0:
                    $this->tokPtr = $this->tokStart;
                    $this->tokText = '';
                    $this->tokLen = 0;
                    $c = $this->nextChar();

                    if (is_null($c)) { // End Of Input
                        $state = 1000;
                        break;
                    }

                    while (($c == ' ') || ($c == "\t")
                        || ($c == "\n") || ($c == "\r")) {
                        if ($c == "\n" || $c == "\r") {
                            // Handle MAC/Unix/Windows line endings.
                            if($c == "\r") {
                                $c = $this->skip();

                                // If not DOS newline
                                if($c != "\n")
                                    $this->reverseChar();
                            }
                            ++$this->lineNo;
                            $this->lineBegin = $this->tokPtr;
                        }

                           $c = $this->skip();
                        $this->tokLen = 1;
                    }

                    // Escape quotes and backslashes
                    if ($c == '\\') {
                         $t = $this->nextChar();
                        if ($t == '\'' || $t == '\\' || $t == '"') {
                            $this->tokText = $t;
                            $this->tokStart = $this->tokPtr;
                            return $this->tokText;
                        } else {
                            $this->reverseChar();

                            // Unknown token.  Revert to single char
                            $state = 999;
                            break;
                        }
                    }

                    if (($c == '\'') || ($c == '"')) { // text string
                        $quote = $c;
                        $state = 12;
                        break;
                    }

                    if ($c == '_') { // system variable
                        $state = 18;
                        break;
                    }

                    if (ctype_alpha(ord($c))) { // keyword or ident
                        $state = 1;
                        break;
                    }

                    if (ctype_digit(ord($c))) { // real or int number
                        $state = 5;
                        break;
                    }

                    if ($c == '.') {
                        $t = $this->nextChar();
                        if ($t == '.') { // ellipsis
                            if ($this->nextChar() == '.') {
                                $this->tokText = '...';
                                $this->tokStart = $this->tokPtr;
                                return $this->tokText;
                            } else {
                                $state = 999;
                                break;
                            }
                        } else if (ctype_digit(ord($t))) { // real number
                            $this->reverseChar();
                            $state = 7;
                            break;
                        } else { // period
                            $this->reverseChar();
                        }
                    }

                    if ($c == '#') { // Comments
                        $state = 14;
                        break;
                    }
                    if ($c == '-') {
                        $t = $this->nextChar();
                        if ($t == '-') {
                            $state = 14;
                            break;
                        } else { // negative number
                            $this->reverseChar();
                            $state = 5;
                            break;
                        }
                    }

                    if ($this->isCompop($c)) { // comparison operator
                        $state = 10;
                        break;
                    }
                    // Unknown token.  Revert to single char
                    $state = 999;
                    break;
                // }}}

                // {{{ State 1 : Incomplete keyword or ident
                case 1:
                    $c = $this->nextChar();
                    if (ctype_alnum(ord($c)) || ($c == '_') || ($c == '.')) {
                        $state = 1;
                        break;
                    }
                    $state = 2;
                    break;
                // }}}

                /* {{{ State 2 : Complete keyword or ident */
                case 2:
                    $this->reverseChar();
                    $this->tokText = substr($this->string, $this->tokStart,
                                            $this->tokLen);

                    $testToken = strtolower($this->tokText);
                    if (isset($this->symbols[$testToken])) {

                        $this->skipText = substr($this->string, $this->tokAbsStart,
                                                $this->tokStart-$this->tokAbsStart);
                        $this->tokStart = $this->tokPtr;
                        return $testToken;
                    } else {
                        $this->skipText = substr($this->string, $this->tokAbsStart,
                                                $this->tokStart-$this->tokAbsStart);
                        $this->tokStart = $this->tokPtr;
                        return 'ident';
                    }
                    break;
                // }}}

                // {{{ State 5: Incomplete real or int number
                case 5:
                    $c = $this->nextChar();
                    if (ctype_digit(ord($c))) {
                        $state = 5;
                        break;
                    } else if ($c == '.') {
                        $t = $this->nextChar();
                        if($t == '.') { // ellipsis
                            $this->reverseChar();
                        } else { // real number
                            $state = 7;
                            break;
                        }
                    } else if(ctype_alpha(ord($c))) { // number must end with non-alpha character
                        $state = 999;
                        break;
                    } else {
                    // complete number
                    $state = 6;
                    break;
                    }
                // }}}

                // {{{ State 6: Complete integer number
                case 6:
                    $this->reverseChar();
                    $this->tokText = intval(substr($this->string, $this->tokStart,
                                                   $this->tokLen));
                    $this->skipText = substr($this->string, $this->tokAbsStart,
                                             $this->tokStart-$this->tokAbsStart);
                    $this->tokStart = $this->tokPtr;
                    return 'int_val';
                    break;
                // }}}

                // {{{ State 7: Incomplete real number
                case 7:
                    $c = $this->nextChar();

                    /* Analogy Start */
                    if ($c == 'e' || $c == 'E') {
                            $state = 15;
                            break;
                    }
                    /* Analogy End   */

                    if (ctype_digit(ord($c))) {
                        $state = 7;
                        break;
                    }
                    $state = 8;
                    break;
                // }}}

                // {{{ State 8: Complete real number */
                case 8:
                    $this->reverseChar();
                    $this->tokText = floatval(substr($this->string, $this->tokStart,
                                            $this->tokLen));
                    $this->skipText = substr($this->string, $this->tokAbsStart,
                                             $this->tokStart-$this->tokAbsStart);
                    $this->tokStart = $this->tokPtr;
                    return 'real_val';
                // }}}

                // {{{ State 10: Incomplete comparison operator
                case 10:
                    $c = $this->nextChar();
                    if ($this->isCompop($c))
                    {
                        $state = 10;
                        break;
                    }
                    $state = 11;
                    break;
                // }}}

                // {{{ State 11: Complete comparison operator
                case 11:
                    $this->reverseChar();
                    $this->tokText = substr($this->string, $this->tokStart,
                                            $this->tokLen);
                    if($this->tokText) {
                        $this->skipText = substr($this->string, $this->tokAbsStart,
                                                $this->tokStart-$this->tokAbsStart);
                        $this->tokStart = $this->tokPtr;
                        return $this->tokText;
                    }
                    $state = 999;
                    break;
                // }}}

                // {{{ State 12: Incomplete text string
                case 12:
                    $bail = false;
                    while (!$bail) {
                        switch ($this->nextChar()) {
                            case '':
                                $this->tokText = null;
                                $bail = true;
                                break;
                            case "\\":
                                if (!$this->nextChar()) {
                                    $this->tokText = null;
                                    $bail = true;
                                }
                                    //$bail = true;
                                break;
                            case $quote:
                                $this->tokText = stripslashes(substr($this->string,
                                           ($this->tokStart+1), ($this->tokLen-2)));
                                $bail = true;
                                break;
                        }
                    }
                    if (!is_null($this->tokText)) {
                        $state = 13;
                        break;
                    }
                    $state = 999;
                    break;
                // }}}

                // {{{ State 13: Complete text string
                case 13:
                    $this->skipText = substr($this->string, $this->tokAbsStart,
                                             $this->tokStart-$this->tokAbsStart);
                    $this->tokStart = $this->tokPtr;
                    return 'text_val';
                    break;
                // }}}

                // {{{ State 14: Comment
                case 14:
                    $c = $this->skip();
                    if ($c == "\n" || $c == "\r" || $c == "") {
                        // Handle MAC/Unix/Windows line endings.
                        if ($c == "\r") {
                            $c = $this->skip();
                            // If not DOS newline
                            if ($c != "\n") {
                                $this->reverseChar();
                            }
                        }

                        if ($c != "") {
                            ++$this->lineNo;
                            $this->lineBegin = $this->tokPtr;
                        }

                        // We need to skip all the text.
                        $this->tokStart = $this->tokPtr;
                        $state = 0;
                    } else {
                        $state = 14;
                    }
                    break;
                // }}}

                // {{{ State 15: Exponent Sign in Scientific Notation
                case 15:
                        $c = $this->nextChar();
                        if($c == '-' || $c == '+') {
                                $state = 16;
                                break;
                        }
                        $state = 999;
                        break;
                // }}}

                // {{{ state 16: Exponent Value-first digit in Scientific Notation
                case 16:
                        $c = $this->nextChar();
                        if (ctype_digit(ord($c))) {
                                $state = 17;
                                break;
                        }
                        $state = 999;  // if no digit, then token is unknown
                        break;
                // }}}

                // {{{ State 17: Exponent Value in Scientific Notation
                case 17:
                        $c = $this->nextChar();
                        if (ctype_digit(ord($c))) {
                                $state = 17;
                                break;
                        }
                        $state = 8;  // At least 1 exponent digit was required
                        break;
                // }}}

                // {{{ State 18 : Incomplete System Variable
                case 18:
                    $c = $this->nextChar();
                    if (ctype_alnum(ord($c)) || $c == '_') {
                        $state = 18;
                        break;
                    }
                    $state = 19;
                    break;
                // }}}

                // {{{ State 19: Complete Sys Var
                case 19:
                    $this->reverseChar();
                    $this->tokText = substr($this->string, $this->tokStart,
                                            $this->tokLen);
                    $this->skipText = substr($this->string, $this->tokAbsStart,
                                             $this->tokStart-$this->tokAbsStart);
                    $this->tokStart = $this->tokPtr;
                    return 'sys_var';
                // }}}

                // {{{ State 999 : Unknown token.  Revert to single char
                case 999:
                    $this->rewind();
                    $this->tokText = $this->nextChar();
                    $this->skipText = substr($this->string, $this->tokAbsStart,
                                             $this->tokStart-$this->tokAbsStart);
                    $this->tokStart = $this->tokPtr;
                    return $this->tokText;
                // }}}

                // {{{ State 1000 : End Of Input
                case 1000:
                    $this->tokText = '*end of input*';
                    $this->skipText = substr($this->string, $this->tokAbsStart,
                                             $this->tokStart-$this->tokAbsStart);
                    $this->tokStart = $this->tokPtr;
                    return null;
                // }}}
            }
        }
    }
    // }}}

    // {{{ formatError($message)
    public function formatError($message) {
        $end = 0;
        if ($this->string != '') {
            while (($this->lineBegin+$end < $this->stringLen)
               && ($this->string{$this->lineBegin+$end} != "\n")){
                ++$end;
            }
        }

        $message = 'Parse error: '.$message.' on line '.
            ($this->lineNo+1)."\n";
        $message .= substr($this->string, $this->lineBegin, $end)."\n";
        $length = is_null($this->token) ? 0 : strlen($this->tokText);
        $message .= str_repeat(' ', abs($this->tokPtr -
                               $this->lineBegin - $length))."^";
        $message .= ' found: "'.$this->tokText.'"';

        return $message;
    }
    // }}}
}
?>
