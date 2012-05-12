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

namespace Yana\Files;

/**
 * Manipulate a directory
 *
 * This class represents a directory.
 * You may use this to get a list of contents, or remove,
 * or create a directory.
 *
 * @package     yana
 * @subpackage  files
 */
class Dir extends \Yana\Files\AbstractResource implements \Yana\Files\IsReadable
{

    /**#@+
     * @ignore
     */
    /** @var array  */ protected $content = array();
    /** @var string */ protected $filter = "";
    /** @var array  */ protected static $size = null;
    /**#@-*/

    /**
     * constructor
     *
     * Create a new instance of this class.
     *
     * @param  string  $path  path to directory
     */
    public function __construct($path)
    {
        assert('is_string($path); /* Wrong argument type for argument 1. String expected. */');
        if (!preg_match('/.*\/$/', $path)) { // auto-append path seperator
            $path .= '/';
        }
        parent::__construct($path);
    }

    /**
     * read contents and put results in cache (filter settings will be applied)
     *
     * @return  \Yana\Files\Dir
     * @throws  \Yana\Core\Exceptions\NotFoundException  when directory is not found
     */
    public function read()
    {
        if (!$this->exists()) {
            throw new \Yana\Core\Exceptions\NotFoundException("No such directory: '{$this->getPath()}'.", E_USER_WARNING);
        }
        $this->content = $this->_dirlist();
        sort($this->content);
        return $this;
    }

    /**
     * list contents of a directory
     *
     * The argument $filter may contain multiple file extension,
     * use a pipe '|' sign to seperate them.
     * Example: "*.xml|*.html" will find all xml- and html-files
     *
     * The argument $switch may be used to get only subdirectories (YANA_GET_DIRS),
     * or only files (YANA_GET_FILES), or all contents (YANA_GET_ALL), which is the default.
     *
     * @return array
     */
    private function _dirlist()
    {
        $dir = $this->getPath();
        $filter = $this->getFilter();
        $switch = null;

        /* Input handling */
        if ($filter == "") {
            $filter = false;
        } elseif (strpos($filter, '|') !== false) {
            $filter = preg_replace("/[^\.\-\_\w\d\|]/", "", $filter);
            assert('!isset($tok); /* cannot redeclare variable $tok */');
            $tok = strtok($filter, "|");
            $filter = "";
            while ($tok !== false)
            {
                $filter .= preg_quote($tok, '/');
                $tok = strtok("|");
                if ($tok !== false) {
                    $filter .= "|";
                }
            }
            unset($tok);
        } else {
            $filter = preg_replace("/[^\.\-\_\w\d]/", "", $filter);
            $filter = preg_quote($filter, '/');
        }

        /* read contents from directory */
        $dirlist = array();
        if (is_dir($dir)) {
            $dirHandle = dir($dir);
            while($entry = $dirHandle->read())
            {
                if ($entry[0] !== '.' && ($filter === false || preg_match("/(?:{$filter})$/i", $entry))) {
                    assert('is_array($dirlist); /* Invariant condition failed: $dirlist is not an array. */');
                    switch ($switch)
                    {
                        case YANA_GET_ALL:
                            $dirlist[] = $entry;
                        break;
                        case YANA_GET_DIRS:
                            if (is_dir($dir.$entry)) {
                                $dirlist[] = $entry;
                            }
                        break;
                        case YANA_GET_FILES:
                        default:
                            if (is_file($dir.$entry)) {
                                $dirlist[] = $entry;
                            }
                        break;
                    }
                }
            } // end while
            unset($entry);
            $dirHandle->close();
            sort($dirlist);
            assert('is_array($dirlist); /* Unexpected result: $dirlist is not an array. */');
        } else {
            trigger_error("The directory '{$dir}' does not exist.", E_USER_NOTICE);
        }
        return $dirlist;
    }

    /**
     * Return list of files within the directory.
     *
     * This will only return filenames with the path stripped.
     *
     * @param   int  $index  number of file to return
     * @return  array
     */
    public function getContent($index = null)
    {
        assert('is_null($index) || is_int($index); // Wrong type for argument 1. Integer expected');
        if ($this->isEmpty()) {

            try { // automatically try to read directory contents

                $this->read();

            } catch (\Yana\Core\Exceptions\NotFoundException $e) { // directory does not exist
                $this->content = array();
            }

        }
        assert('is_array($this->content); // Unexpected return type. Array expected');

        // Retrieve directory contents
        $content = null;
        if (is_null($index)) {
            assert('is_array($this->content); // Unexpected return type. Array expected');
            $content = (array) $this->content;
  
        } elseif (isset($this->content[$index])) {
            assert('is_string($this->content[$index]); // Unexpected return type. String expected');
            $content = (string) $this->content[$index];
        }

        return $content;
    }

    /**
     * Return current file filter.
     *
     * The current last filter used is always cached
     * until reset. The default is an empty file filter (all files).
     * The empty file filter equals an empty string.
     *
     * @return  string
     * @since   3.1.0
     */
    public function getFilter()
    {
        assert('is_string($this->filter); // Wrong type for argument filter');
        return $this->filter;
    }

    /**
     * This sets up a file filter.
     *
     * The default is an empty file filter (all files).
     * To reset the filter, leave the setting empty.
     *
     * @param   string  $filter   current file filter
     * @return  \Yana\Files\Dir
     * @since   3.1.0
     */
    public function setFilter($filter = "")
    {
        assert('is_string($filter); // Wrong type for argument 1. String expected');
        $this->filter = (string) $filter;
        return $this;
    }

    /**
     * Tries to create the directory.
     *
     * Check the developer's cookbook for an example to this function.
     *
     * You may also want to review the PHP-manual for function chmod() on the use of the $mode
     * parameter.
     *
     * @param   int  $mode  access mode, an octal number of 1 through 0777.
     * @return  \Yana\Files\Dir
     * @name    Dir::create()
     * @throws  \Yana\Core\Exceptions\InvalidArgumentException  when argument $mode is not an integer or out of range
     * @throws  \Yana\Core\Exceptions\AlreadyExistsException    when the directory already exists
     * @throws  \Yana\Core\Exceptions\NotWriteableException     when target location is not writeable
     */
    public function create($mode = 0777)
    {
        assert('is_int($mode); // Wrong argument type argument 1. Integer expected');

        if ($mode > 0777 || $mode < 1) {
            $message = "Argument mode must be an octal number in range: [1,0777].";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }

        if ($this->exists()) {
            $message = "Unable to create directory '{$this->getPath()}'. " .
                "Another directory with the same name already exists.";
            throw new \Yana\Core\Exceptions\AlreadyExistsException($message, E_USER_NOTICE);
        }

        $path = $this->getPath();
        if (empty($path) || !@mkdir($path)) {
            $message = "Unable to create directory '$path'. Target not writeable.";
            throw new \Yana\Core\Exceptions\NotWriteableException($message, E_USER_WARNING);
        }

        chmod($path, $mode);

        return $this;
    }

    /**
     * Remove this directory.
     *
     * By option you may choose to also recursivly remove all files and subdirectories inside.
     * Otherwise the directory will only be removed if it is empty.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @param   bool  $isRecursive  triggers wether to remove directories even if they are not empty, default = false
     * @return  bool
     * @throws  \Yana\Core\Exceptions\NotWriteableException  when directory cannot be deleted
     */
    public function delete($isRecursive = false)
    {
        assert('is_bool($isRecursive); // Wrong argument type argument 1. Boolean expected');

        if ($isRecursive === true) {
            $content = $this->dirlist('');
            assert('!isset($element); /* cannot redeclare variable $element */');
            foreach ($content as $element)
            {
                $element = '/'.$element;
                if (is_file($this->getPath() . $element)) {
                    if (unlink($this->getPath() . $element) === false) {
                        return false;
                    }
                } elseif (is_dir($this->getPath() . $element)) {
                    $dir = new \Yana\Files\Dir($this->getPath() . $element);
                    if ($dir->delete(true) === false) {
                        return false;
                    }
                } else {
                    /* intentionally left blank */
                }
            } /* end foreach */
            unset($element);
        }
        if (@rmdir($this->getPath()) === false) {
            $message = "Unable to delete directory '" . $this->getPath() . "'.";
            throw new \Yana\Core\Exceptions\NotWriteableException($message, E_USER_ERROR);
        }
        return true;
    }

    /**
     * Returns a string with the contents of this directory.
     *
     * Entries are seperated by line-breaks.
     *
     * @return  string
     */
    public function __toString()
    {
        if (!$this->exists()) {
            return "Directory ".$this->getPath()." does not exist\n";
        } elseif ($this->isEmpty()) {
            return "Directory ".$this->getPath()." is not loaded\n";
        } else {
            return implode("\n", $this->getContent());
        }
    }

    /**
     * Check wether the directory has no contents.
     *
     * Returns bool(true) if there are no files that
     * match the current filter and bool(false) if there
     * is at least 1 file that matches.
     *
     * @return  bool
     */
    public function isEmpty()
    {
        return empty($this->content);        
    }

    /**
     * Get the number of files inside the directory.
     *
     * This returns a positive integer.
     * Note that this functions counts the files in respect
     * to the currently set file filter. So the number
     * of files reported here and the number in total
     * may vary.
     *
     * @return  int
     */
    public function length()
    {
        return count($this->content);
    }

    /**
     * List contents of a directory.
     *
     * The argument $filter may contain multiple file extension,
     * use a pipe '|' sign to seperate them.
     * Example: "*.xml|*.html" will find all xml- and html-files
     *
     * @param   string  $filter  filter
     * @return  array
     */
    public function dirlist($filter = null)
    {
        assert('is_null($filter) || is_string($filter); // Wrong type for argument 1. String expected');

        if (empty($this->content) || $this->filter !== $filter) {
            $this->filter = (string) $filter;
            $this->read();
        }
        return $this->content;
    }

    /**
     * Returns the size of $directory in bytes.
     *
     * This function gets the sum of the sizes of all files in
     * a directory.
     *
     * If $directory is not provided or NULL, the current directory is used.
     * If $countSubDirs is not provided or true, the result will
     * include all subdirectories.
     *
     * If $useCache is not provided or true, the result is cached for
     * the current directory.
     *
     * Returns bool(false) if the directory does not exist and issues an E_USER_WARNING.
     *
     * @param   string    $directory      directory name
     * @param   bool      $countSubDirs   on / off
     * @param   bool      $useCache       on / off
     * @return  int|bool
     * @since   2.8.8
     */
    public function getSize($directory = null, $countSubDirs = true, $useCache = true)
    {
        assert('is_null($directory) || is_string($directory); // Wrong argument type for argument 1. String expected.');
        assert('is_bool($countSubDirs); // Wrong argument type for argument 2. Boolean expected.');
        assert('is_bool($useCache); // Wrong argument type for argument 3. Boolean expected.');

        /* use default value */
        if (is_null($directory)) {
            if ($this->exists()) {
                $dir = $this->getPath();
            } else {
                trigger_error("The directory '".$this->getPath()."' does not exist.", E_USER_WARNING);
                return false;
            }

        /* directory does not exist */
        } elseif (!is_string($directory) || !is_dir($directory)) {
            trigger_error("Argument 1 '".print_r($directory, true)."' is not a directory.", E_USER_WARNING);
            return false;

        /* add slash */
        } else {
            $dir = $directory . DIRECTORY_SEPARATOR;
        }

        /* if value is already cached, return the cached size */
        if ($useCache === true && isset(self::$size[$dir])) {
            return self::$size[$dir];
        }

        /* else determine the size */
        $d = dir($dir);
        $dirsize = 0;
        while (false !== ($filename = $d->read()))
        {
            if ($filename == '.' || $filename == '..') {
                continue;

            /* accumulate file sizes */
            } elseif (is_file($dir.$filename)) {
                $dirsize += filesize($dir.$filename);

            /* only recurse if subdirs are to be included */
            } elseif ($countSubDirs === true) {
                $dirsize += $this->getSize($dir.$filename, true, false);
            }
        }
        $d->close();

        /* cache result for later requests */
        if ($useCache === true) {
            self::$size[$dir] = $dirsize;
        }

        /* return result */
        return $dirsize;
    }

    /**
     * Reset statistics.
     *
     * Reset directory stats, e.g. after creating a file that did not exist.
     *
     * @return  \Yana\Files\Dir
     * @ignore
     */
    protected function resetStats()
    {
        parent::resetStats();
        self::$size = null;
        return $this;
    }

    /**
     * Check if directory exists and is readable.
     *
     * Returns bool(true) on success and bool(false) otherwise.
     *
     * @return  bool
     */
    public function exists()
    {
        $name = $this->getPath();
        return is_dir($name) && file_exists($name) && is_readable($name);
    }

    /**
     * Copy the directory to some destination.
     *
     * This will create a copy of this directory and its contents on the
     * filesystem.
     * Bool(true) will be returned on success and bool(false) on error.
     *
     * You can also use this function with some regular expression.
     * To do so, set $useRegExp to bool(true). It defaults to bool(false).
     * See the PHP manual at php.net/docs for an in-depth introduction on the
     * use of regular expressions.
     *
     * If you don't want to use regular expressions, you may still use the
     * following wildcards for file- and directory patterns:
     * <ul>
     * <li>
     *       ? = match 1 symbol, example: f?o, matches 'foo', as well as 'fao'
     * </li>
     * <li>
     *       * = match any symbols, example: *foo*, matches 'foo', or 'foobar',
     *       or 'barfoo', or 'barfoobar'
     * </li>
     * <li>
     *       | = seperate 2 choices, example: foo|bar, matches either 'foo',
     *       or 'bar' (but NOT 'foobar')
     * </li>
     * </ul>
     *
     * See the following examples:
     * <code>
     * $dir = new Dir('foo/');
     *
     * // copy directory foo/ to destination bar/
     * $dir->copy('bar/');
     *
     * // try to copy to bar2/ but don't overwrite if it already exists
     * $dir->copy('bar2/', false);
     *
     * // copy again and make files write- and executable
     * $dir->copy('bar3/', true, 0777);
     *
     * // copy to bar4/ and recurse sub-dirs
     * $dir->copy('bar4/', true, 0766, true);
     *
     * // copy all *.xml and *.xhtml files to bar3/
     * $dir->copy('bar5/', true, 0766, true, '*.xml|*.xhtml');
     *
     * // copy all sub-directories, whose names end with 'bar/'
     * $dir->copy('bar6/', true, 0766, true, null, '*bar');
     *
     * // copy all but directory 'foobar/' (use regular expression)
     * $dir->copy('bar7/', true, 0766, true, null, '/^(?!foobar$)/i', true);
     * </code>
     *
     * @param    string   $destDir      destination to copy the file to
     * @param    bool     $overwrite    setting this to false will prevent existing files from getting overwritten
     * @param    int      $mode         the access restriction that applies to the copied file, defaults to 0766
     * @param    bool     $copySubDirs  setting this to true will cause sub-directories to be copied as well
     * @param    string   $fileFilter   use this to limit the copied files to a specific extension
     * @param    string   $dirFilter    use this to limit the copied directories to those matching the filter
     * @param    bool     $useRegExp    set this to bool(true) if you want filters to be treated as a regular expression
     * @throws   \Yana\Core\Exceptions\InvalidArgumentException  when one input argument is invalid
     * @throws   \Yana\Core\Exceptions\AlreadyExistsException    if the target directory already exists
     * @throws   \Yana\Core\Exceptions\NotWriteableException     if the target location is not writeable
     */
    public function copy($destDir, $overwrite = true, $mode = 0766, $copySubDirs = false, $fileFilter = null, $dirFilter = null, $useRegExp = false)
    {
        assert('is_string($destDir); // Wrong type for argument 1. String expected');
        assert('is_bool($overwrite); // Wrong type for argument 2. Boolean expected');
        assert('is_int($mode); // Wrong type for argument 3. Integer expected');
        assert('is_bool($copySubDirs); // Wrong type for argument 4. Boolean expected');
        assert('is_string($fileFilter) || is_null($fileFilter); // Wrong type for argument 5. String expected');
        assert('is_string($dirFilter) || is_null($dirFilter); // Wrong type for argument 6. String expected');
        assert('is_bool($useRegExp); // Wrong type for argument 7. Boolean expected');

        if ($mode > 0777 || $mode < 1) {
            $message = "Argument mode must be an octal number in range: [1,0777].";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }

        /* validity checking */
        if (empty($destDir) || mb_strlen($destDir) > 512) {
            $message = "Invalid directory name '{$destDir}'.";
            throw new \Yana\Core\Exceptions\InvalidArgumentException($message, E_USER_WARNING);
        }

        /* check if directory already exists */
        if ($overwrite === false && file_exists($destDir) === true) {
            $message = "Unable to copy directory '{$destDir}'. " .
                "Another directory with the same name does already exist.";
            throw new \Yana\Core\Exceptions\AlreadyExistsException($message, E_USER_NOTICE);
        }
        if ($overwrite === true && file_exists($destDir) === true && is_writeable($destDir) === false) {
            $message = "Unable to copydirectory '{$destDir}'. The directory does already exist and is not writeable.";
            throw new \Yana\Core\Exceptions\NotWriteableException($message, E_USER_NOTICE);
        }

        /* argument $fileFilter */
        if (!is_null($fileFilter) && $useRegExp === false) {
            if (strpos($fileFilter, '|') !== false) {
                $fileFilter = preg_replace("/[^\.\-\_\w\d\|]/", "", $fileFilter);
                assert('!isset($tok); // cannot redeclare variable $tok');
                $tok = strtok($fileFilter, "|");
                $fileFilter = "";
                while ($tok !== false)
                {
                    $fileFilter .= preg_quote($tok, '/');
                    $tok = strtok("|");
                    if ($tok !== false) {
                        $fileFilter .= "|";
                    }
                } /* end while */
                unset($tok);
            } else {
                $fileFilter = preg_replace("/[^\.\-\_\w\d]/", "", $fileFilter);
                $fileFilter = preg_quote($fileFilter, '/');
            } /* end if */
            $fileFilter = '/' . $fileFilter . '$/i';
        } /* end if */

        /* argument $dirFilter */
        if (!is_null($dirFilter) && $useRegExp === false) {
            $dirFilter = preg_replace('/[\.\\\+\[\^\]\$\(\)\{\}\=\!\<\>\:\/]/', '\\$0', $dirFilter);
            $dirFilter = str_replace('*', ".*", $dirFilter);
            $dirFilter = str_replace('?', ".?", $dirFilter);
            $dirFilter = '/' . $dirFilter . '$/i';
        } /* end if */

        assert('is_string($destDir); // Unexpected result: $destDir. String expected.');

        /* recursively create directories */
        if (!empty($destDir) && !is_dir($destDir)) {
            assert('!isset($currentDir); // cannot redeclare variable $currentDir');
            $currentDir = '';
            assert('!isset($current); // cannot redeclare variable $dir');
            assert('!isset($dir); // cannot redeclare variable $dir');
            foreach (explode('/', $destDir) as $dir)
            {
                if (!is_dir($currentDir . $dir)) {
                    $current = new \Yana\Files\Dir($currentDir . $dir);
                    $current->create($mode);
                }
                $currentDir .= $dir.'/';
            } /* end foreach */
            unset($dir,$current);
        } /* end if */

        /* copy directory */
        assert('!isset($dir); // cannot redeclare variable $dir');
        assert('!isset($item); // cannot redeclare variable $item');
        assert('!isset($handle); // cannot redeclare variable $handle');
        $handle = opendir($this->path);
        while ($item = readdir($handle))
        {
            /*
             * recurse sub-directories
             */
            if (is_dir($this->path . $item)) {
                /* if sub-dirs are to be handled recursively ... */
                if (!$copySubDirs || $item === '.' || $item === '..') {
                    continue;
                }
                /* if sub-dir matches the directory pattern ... */
                if (is_null($dirFilter) || preg_match($dirFilter, $item)) {
                    $dir = new \Yana\Files\Dir($this->path . $item);
                    assert('!isset($copySucceeded); // Cannot redeclare var $copySucceeded');
                    $copySucceeded = $dir->copy($destDir . $item . '/', $overwrite, $mode,
                        $copySubDirs, $fileFilter, $dirFilter, true);
                    if ($copySucceeded === false) {
                        return false;
                    }
                    unset($copySucceeded);
                    if (chmod($destDir . $item, decoct($mode)) === false) {
                        $message = "Unable to set mode (access level) for directory '{$destDir}{$item}'.";
                        trigger_error($message, E_USER_NOTICE);
                        return false;
                    }
                } // end if

            /*
             * handle files
             */
            } elseif (is_file($this->path . $item)) {
                if (is_null($fileFilter) || preg_match($fileFilter, $item)) {
                    if (copy($this->path . $item, $destDir . $item) === false) {
                        trigger_error("Unable to copy file '{$destDir}{$item}'.", E_USER_NOTICE);
                        return false;
                    } elseif (chmod($destDir . $item, $mode) === false) {
                        $message = "Unable to set mode (access level) for file '{$destDir}{$item}'.";
                        trigger_error($message, E_USER_NOTICE);
                        return false;
                    } else {
                        continue;
                    }
                } else {
                    continue;
                } // end if

            /*
             * error
             */
            } else {
                continue;
            } // end if
        } /* end while */
        closedir($handle);
        unset($dir, $item, $handle);

        return true;
    }

}

?>