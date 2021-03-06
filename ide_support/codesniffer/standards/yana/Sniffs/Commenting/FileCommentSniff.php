<?php
/**
 * Parses and verifies the doc comments for files.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: FileCommentSniff.php,v 1.32 2009/02/10 06:01:46 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_CommentParser_ClassCommentParser', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_CommentParser_ClassCommentParser not found');
}

/**
 * Parses and verifies the doc comments for files.
 *
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>There is a blank newline after the short description.</li>
 *  <li>There is a blank newline between the long and short description.</li>
 *  <li>There is a blank newline between the long description and tags.</li>
 *  <li>A PHP version is specified.</li>
 *  <li>Check the order of the tags.</li>
 *  <li>Check the indentation of each tag.</li>
 *  <li>Check required and optional tags and the format of their content.</li>
 * </ul>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.0RC3
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

class YANA_Sniffs_Commenting_FileCommentSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * The header comment parser for the current file.
     *
     * @var PHP_CodeSniffer_Comment_Parser_ClassCommentParser
     */
    protected $commentParser = null;

    /**
     * The current PHP_CodeSniffer_File object we are processing.
     *
     * @var PHP_CodeSniffer_File
     */
    protected $currentFile = null;

    /**
     * Tags in correct order and related info.
     *
     * @var array
     */
    protected $tags = array(

                       'package'    => array(
                                        'required'       => true,
                                        'allow_multiple' => false
                                       ),
                       'subpackage' => array(
                                        'required'       => false,
                                        'allow_multiple' => false
                                       ),
                       'author'     => array(
                                        'required'       => false,
                                        'allow_multiple' => true
                                       ),
                       'copyright'  => array(
                                        'required'       => false,
                                        'allow_multiple' => true
                                       ),
                       'license'    => array(
                                        'required'       => true,
                                        'allow_multiple' => false
                                       ),
                       'version'    => array(
                                        'required'       => false,
                                        'allow_multiple' => false
                                       ),
                       'link'       => array(
                                        'required'       => true,
                                        'allow_multiple' => true
                                       ),
                       'see'        => array(
                                        'required'       => false,
                                        'allow_multiple' => true
                                       ),
                       'since'      => array(
                                        'required'       => false,
                                        'allow_multiple' => false
                                       ),
                       'deprecated' => array(
                                        'required'       => false,
                                        'allow_multiple' => false
                                       ),
                );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

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
        $this->currentFile = $phpcsFile;

        // We are only interested if this is the first open tag.
        if ($stackPtr !== 0) {
            if ($phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1)) !== false) {
                return;
            }
        }

        $tokens = $phpcsFile->getTokens();

        // Find the next non whitespace token.
        $commentStart
            = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        // Allow declare() statements at the top of the file.
        if ($tokens[$commentStart]['code'] === T_DECLARE) {
            $semicolon = $phpcsFile->findNext(T_SEMICOLON, ($commentStart + 1));
            $commentStart
                = $phpcsFile->findNext(T_WHITESPACE, ($semicolon + 1), null, true);
        }

        // Ignore vim header.
        if ($tokens[$commentStart]['code'] === T_COMMENT) {
            if (strstr($tokens[$commentStart]['content'], 'vim:') !== false) {
                $commentStart = $phpcsFile->findNext(
                    T_WHITESPACE,
                    ($commentStart + 1),
                    null,
                    true
                );
            }
        }

        $errorToken = ($stackPtr + 1);
        if (isset($tokens[$errorToken]) === false) {
            $errorToken--;
        }

        if ($tokens[$commentStart]['code'] === T_CLOSE_TAG) {
            // We are only interested if this is the first open tag.
            return;
        } else if ($tokens[$commentStart]['code'] === T_COMMENT) {
            $error = 'You must use "/**" style comments for a file comment';
            $phpcsFile->addError($error, $errorToken);
            return;
        } else if ($commentStart === false
            || $tokens[$commentStart]['code'] !== T_DOC_COMMENT
        ) {
            $phpcsFile->addError('Missing file doc comment', $errorToken);
            return;
        } else {

            // Extract the header comment docblock.
            $commentEnd = $phpcsFile->findNext(
                T_DOC_COMMENT,
                ($commentStart + 1),
                null,
                true
            );

            $commentEnd--;

            // Check if there is only 1 doc comment between the
            // open tag and class token.
            $nextToken   = array(
                            T_ABSTRACT,
                            T_CLASS,
                            T_FUNCTION,
                            T_DOC_COMMENT,
                           );

            $commentNext = $phpcsFile->findNext($nextToken, ($commentEnd + 1));
            if ($commentNext !== false
                && $tokens[$commentNext]['code'] !== T_DOC_COMMENT
            ) {
                // Found a class token right after comment doc block.
                $newlineToken = $phpcsFile->findNext(
                    T_WHITESPACE,
                    ($commentEnd + 1),
                    $commentNext,
                    false,
                    $phpcsFile->eolChar
                );

                if ($newlineToken !== false) {
                    $newlineToken = $phpcsFile->findNext(
                        T_WHITESPACE,
                        ($newlineToken + 1),
                        $commentNext,
                        false,
                        $phpcsFile->eolChar
                    );

                    if ($newlineToken === false) {
                        // No blank line between the class token and the doc block.
                        // The doc block is most likely a class comment.
                        $error = 'Missing file doc comment';
                        $phpcsFile->addError($error, $errorToken);
                        return;
                    }
                }
            }//end if

            $comment = $phpcsFile->getTokensAsString(
                $commentStart,
                ($commentEnd - $commentStart + 1)
            );

            // Parse the header comment docblock.
            try {
                $this->commentParser = new PHP_CodeSniffer_CommentParser_ClassCommentParser($comment, $phpcsFile);
                $this->commentParser->parse();
            } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
                $line = ($e->getLineWithinComment() + $commentStart);
                $phpcsFile->addError($e->getMessage(), $line);
                return;
            }

            $comment = $this->commentParser->getComment();
            if (is_null($comment) === true) {
                $error = 'File doc comment is empty';
                $phpcsFile->addError($error, $commentStart);
                return;
            }

            // No extra newline before short description.
            $short        = $comment->getShortComment();
            $newlineCount = 0;
            $newlineSpan  = strspn($short, $phpcsFile->eolChar);
            if ($short !== '' && $newlineSpan > 0) {
                $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
                $error = "Extra $line found before file comment short description";
                $phpcsFile->addError($error, ($commentStart + 1));
            }

            $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

            // Exactly one blank line between short and long description.
            $long = $comment->getLongComment();
            if (empty($long) === false) {
                $between        = $comment->getWhiteSpaceBetween();
                $newlineBetween = substr_count($between, $phpcsFile->eolChar);
                if ($newlineBetween !== 2) {
                    $error = 'There must be exactly one blank line between descriptions in file comment';
                    $phpcsFile->addError($error, ($commentStart + $newlineCount + 1));
                }

                $newlineCount += $newlineBetween;
            }

            // Exactly one blank line before tags.
            $tags = $this->commentParser->getTagOrders();
            if (count($tags) > 1) {
                $newlineSpan = $comment->getNewlineAfter();
                if ($newlineSpan !== 2) {
                    $error = 'There must be exactly one blank line before the tags in file comment';
                    if ($long !== '') {
                        $newlineCount += (substr_count($long, $phpcsFile->eolChar) - $newlineSpan + 1);
                    }

                    $phpcsFile->addError($error, ($commentStart + $newlineCount));
                    $short = rtrim($short, $phpcsFile->eolChar.' ');
                }
            }

            // Check the PHP Version.
            $this->processPHPVersion($commentStart, $commentEnd, $long);

            // Check each tag.
            $this->processTags($commentStart, $commentEnd);
        }//end if

    }//end process()


    /**
     * Check that the PHP version is specified.
     *
     * @param int    $commentStart Position in the stack where the comment started.
     * @param int    $commentEnd   Position in the stack where the comment ended.
     * @param string $comment      The text of the function comment.
     *
     * @return void
     */
    protected function processPHPVersion($commentStart, $commentEnd, $commentText)
    {
        /* no needed to define in each dokument wihich phpversion is needed*/
//        if (strstr(strtolower($commentText), 'php version') === false) {
//            $error = 'PHP version not specified';
//             $this->currentFile->addWarning($error, $commentEnd);
//        }

    }//end processPHPVersion()


    /**
     * Processes each required or optional tag.
     *
     * @param int $commentStart Position in the stack where the comment started.
     * @param int $commentEnd   Position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processTags($commentStart, $commentEnd)
    {
        $docBlock    = (get_class($this) === 'YANA_Sniffs_Commenting_FileCommentSniff') ? 'file' : 'class';
        $foundTags   = $this->commentParser->getTagOrders();
        $orderIndex  = 0;
        $indentation = array();
        $longestTag  = 0;
        $errorPos    = 0;
        $listOfIgnoreMissingTags = array('link', 'license', 'version');
        foreach ($this->tags as $tag => $info) {
            if(!in_array($tag, $listOfIgnoreMissingTags)) {
                // Required tag missing.
                if ($info['required'] === true && in_array($tag, $foundTags) === false) {
                    $error = "Missing @$tag tag in $docBlock comment";
                    $this->currentFile->addError($error, $commentEnd);
                    continue;
                }
            }

             // Get the line number for current tag.
            $tagName = ucfirst($tag);
            if ($info['allow_multiple'] === true) {
                $tagName .= 's';
            }

            $getMethod  = 'get'.$tagName;
            $tagElement = $this->commentParser->$getMethod();
            if (is_null($tagElement) === true || empty($tagElement) === true) {
                continue;
            }

            $errorPos = $commentStart;
            if (is_array($tagElement) === false) {
                $errorPos = ($commentStart + $tagElement->getLine());
            }

            // Get the tag order.
            $foundIndexes = array_keys($foundTags, $tag);

            if (count($foundIndexes) > 1) {
                // Multiple occurance not allowed.
                if ($info['allow_multiple'] === false) {
                    $error = "Only 1 @$tag tag is allowed in a $docBlock comment";
                    $this->currentFile->addError($error, $errorPos);
                } else {
                    // Make sure same tags are grouped together.
                    $i     = 0;
                    $count = $foundIndexes[0];
                    foreach ($foundIndexes as $index) {
                        if ($index !== $count) {
                            $errorPosIndex
                                = ($errorPos + $tagElement[$i]->getLine());
                            $error = "@$tag tags must be grouped together";
                            $this->currentFile->addError($error, $errorPosIndex);
                        }

                        $i++;
                        $count++;
                    }
                }
            }//end if

            // Store the indentation for checking.
            $len = strlen($tag);
            if ($len > $longestTag) {
                $longestTag = $len;
            }

            if (is_array($tagElement) === true) {
                foreach ($tagElement as $key => $element) {
                    $indentation[] = array(
                                      'tag'   => $tag,
                                      'space' => $this->getIndentation($tag, $element),
                                      'line'  => $element->getLine(),
                                     );
                }
            } else {
                $indentation[] = array(
                                  'tag'   => $tag,
                                  'space' => $this->getIndentation($tag, $tagElement),
                                 );
            }

            $method = 'process'.$tagName;
            if (method_exists($this, $method) === true) {
                // Process each tag if a method is defined.
                call_user_func(array($this, $method), $errorPos);
            } else {
                if (is_array($tagElement) === true) {
                    foreach ($tagElement as $key => $element) {
                        $element->process(
                            $this->currentFile,
                            $commentStart,
                            $docBlock
                        );
                    }
                } else {
                     $tagElement->process(
                         $this->currentFile,
                         $commentStart,
                         $docBlock
                     );
                }
            }
        }//end foreach
/* this foreach checks for spaces between the @ tag and the description , we expected that everybody decide alone for the space */
//        foreach ($indentation as $indentInfo) {
//            if ($indentInfo['space'] !== 0
//                && $indentInfo['space'] !== ($longestTag + 1)
//            ) {
//                $expected = (($longestTag - strlen($indentInfo['tag'])) + 1);
//                $space    = ($indentInfo['space'] - strlen($indentInfo['tag']));
//                $error    = "@$indentInfo[tag] tag comment indented incorrectly. ";
//                $error   .= "Expected $expected spaces but found $space.";
//
//                $getTagMethod = 'get'.ucfirst($indentInfo['tag']);
//
//                if ($this->tags[$indentInfo['tag']]['allow_multiple'] === true) {
//                    $line = $indentInfo['line'];
//                } else {
//                    $tagElem = $this->commentParser->$getTagMethod();
//                    $line    = $tagElem->getLine();
//                }
//
//                $this->currentFile->addError($error, ($commentStart + $line));
//            }
//        }

    }//end processTags()


    /**
     * Get the indentation information of each tag.
     *
     * @param string                                   $tagName    The name of the
     *                                                             doc comment
     *                                                             element.
     * @param PHP_CodeSniffer_CommentParser_DocElement $tagElement The doc comment
     *                                                             element.
     *
     * @return void
     */
    protected function getIndentation($tagName, $tagElement)
    {
        if ($tagElement instanceof PHP_CodeSniffer_CommentParser_SingleElement) {
            if ($tagElement->getContent() !== '') {
                return (strlen($tagName) + substr_count($tagElement->getWhitespaceBeforeContent(), ' '));
            }
        } else if ($tagElement instanceof PHP_CodeSniffer_CommentParser_PairElement) {
            if ($tagElement->getValue() !== '') {
                return (strlen($tagName) + substr_count($tagElement->getWhitespaceBeforeValue(), ' '));
            }
        }

        return 0;

    }//end getIndentation()


    /**
     * Process the category tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processCategory($errorPos)
    {
        $category = $this->commentParser->getCategory();
        if ($category !== null) {
            $content = $category->getContent();
            /* add strtoupper because the its not so important if the Category name is in lowercase - DJO */
            $content = strtoupper($content);
            if ($content !== '') {
                if (PHP_CodeSniffer::isUnderscoreName($content) !== true) {
                    $newContent = str_replace(' ', '_', $content);
                    $nameBits   = explode('_', $newContent);
                    $firstBit   = array_shift($nameBits);
                    $newName    = ucfirst($firstBit).'_';
                    foreach ($nameBits as $bit) {
                        $newName .= ucfirst($bit).'_';
                    }

                    $validName = trim($newName, '_');
                    $error     = "Category name \"$content\" is not valid; consider \"$validName\" instead";
                    $this->currentFile->addError($error, $errorPos);
                }
            } else {
                $error = '@category tag must contain a name';
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }//end processCategory()


    /**
     * Process the package tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processPackage($errorPos)
    {
        $package = $this->commentParser->getPackage();
        // no checking of package name DJO
    }//end processPackage()


    /**
     * Process the subpackage tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processSubpackage($errorPos)
    {
        $package = $this->commentParser->getSubpackage();
        // no checking of sub-package name DJO
    }//end processSubpackage()


    /**
     * Process the author tag(s) that this header comment has.
     *
     * This function is different from other _process functions
     * as $authors is an array of SingleElements, so we work out
     * the errorPos for each element separately
     *
     * @param int $commentStart The position in the stack where
     *                          the comment started.
     *
     * @return void
     */
    protected function processAuthors($commentStart)
    {
         $authors = $this->commentParser->getAuthors();
        // Report missing return.
        if (empty($authors) === false) {
            foreach ($authors as $author) {
                $errorPos = ($commentStart + $author->getLine());
                $content  = $author->getContent();
                if ($content !== '') {
                    if (!preg_match('/^([^<]*)(\s+<([^>]+)>)?$/', $content, $match)) {
                        $error = 'Content of the @author tag must be in the form "Display Name"';
                        $this->currentFile->addError($error, $errorPos);
                    }
                    if(isset($match) && is_array($match) && isset($match[3])) {
                        $mail = filter_var($match[3], FILTER_SANITIZE_EMAIL);
                        if (filter_var($mail, FILTER_VALIDATE_EMAIL) === false) {
                            $error = 'Mail-address of the @author tag is invalid';
                            $this->currentFile->addError($error, $errorPos);
                        }
                    }
                } else {
                    $docBlock = (get_class($this) === 'YANA_Sniffs_Commenting_FileCommentSniff') ? 'file' : 'class';
                    $error = "Content missing for @author tag in $docBlock comment";
                    $this->currentFile->addError($error, $errorPos);
                }
            }
        }

    }//end processAuthors()


    /**
     * Process the copyright tags.
     *
     * @param int $commentStart The position in the stack where
     *                          the comment started.
     *
     * @return void
     */
    protected function processCopyrights($commentStart)
    {
        $copyrights = $this->commentParser->getCopyrights();
        foreach ($copyrights as $copyright) {
            $errorPos = ($commentStart + $copyright->getLine());
            $content  = $copyright->getContent();
            if ($content !== '') {
                $matches = array();
                if (preg_match('/^([0-9]{4})((.{1})([0-9]{4}))? (.+)$/', $content, $matches) !== 0) {
                    // Check earliest-latest year order.
                    if ($matches[3] !== '') {
                        if ($matches[3] !== '-') {
                            $error = 'A hyphen must be used between the earliest and latest year';
                            $this->currentFile->addError($error, $errorPos);
                        }

                        if ($matches[4] !== '' && $matches[4] < $matches[1]) {
                            $error = "Invalid year span \"$matches[1]$matches[3]$matches[4]\" found; consider \"$matches[4]-$matches[1]\" instead";
                            $this->currentFile->addWarning($error, $errorPos);
                        }
                    }
                } else {
                    $error = '@copyright tag must contain a year and the name of the copyright holder';
                    $this->currentFile->addError($error, $errorPos);
                }
            } else {
                $error = '@copyright tag must contain a year and the name of the copyright holder';
                $this->currentFile->addError($error, $errorPos);
            }//end if
        }//end if

    }//end processCopyrights()


    /**
     * Process the license tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processLicense($errorPos)
    {
        /* this tag willl be ignored , the description of this tag*/
        $license = $this->commentParser->getLicense();
        if ($license !== null) {
            $value = $license->getValue();
            if ($value === '') {
                $error = '@license tag must contain an URL';
                $this->currentFile->addError($error, $errorPos);
            }
            if (filter_var($value, FILTER_VALIDATE_URL) === false) {
                $error = '@license contains invalid URL';
                $this->currentFile->addError($error, $errorPos);
            }
        }

    }//end processLicense()


    /**
     * Process the version tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processVersion($errorPos)
    {
        $version = $this->commentParser->getVersion();
        if ($version !== null) {
            $content = $version->getContent();
            $matches = array();
            if (empty($content) === true) {
                $error = 'Content missing for @version tag in file comment';
                $this->currentFile->addError($error, $errorPos);
            } else if (strstr($content, 'CVS:') === false
                && strstr($content, 'SVN:') === false
            ) {
                $error = "Invalid version \"$content\" in file comment; consider \"CVS: <cvs_id>\" or \"SVN: <svn_id>\" instead";
                $this->currentFile->addWarning($error, $errorPos);
            }
        }

    }//end processVersion()


}//end class

?>
