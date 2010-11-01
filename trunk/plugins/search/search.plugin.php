<?php
/**
 * Search Engine
 *
 * This main program installs a search engine for private website of up to 10 MB in size.
 * The program works on an index, which can be created with an included Java application.
 *
 * {@translation
 *
 *    de:  Stichwortsuche
 *
 *         Diese Hauptprogramm installiert eine Suchmaschine für private Webseiten bis zu einer Größe von etwa 10 MB.
 *         Das Programm arbeitet auf einem Suchindex, der über ein beigelegtes Java-Programm erstellt werden kann.
 *
 *  , fr:  Recherche
 *
 * }
 *
 * @author     Thomas Meyer
 * @type       primary
 * @group      search
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * Search plugin
 *
 * This plugin searches an index for words.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_search extends StdClass implements IsPlugin
{
    /**
     * @access  private
     * @var     string
     */
    private $searchString = "";

    /**
     * @access  private
     * @var     string
     */
    private $cache = array();

    /**
     * @access  private
     * @static
     * @var     string
     */
    private static $name = "search";

    /**
     * Default event handler
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @access  public
     * @return  bool
     * @param   string  $event  name of the called event in lower-case
     * @param   array   $ARGS   array of arguments passed to the function
     * @ignore
     */
    public function catchAll($event, array $ARGS)
    {
        return true;
    }

    /**
     * Show table of statistics
     *
     * this function does not expect any arguments
     *
     * @type        read
     * @template    SEARCH_START
     *
     * @access      public
     * @return      bool
     */
    public function search_stats ()
    {
        global $YANA;

        if (class_exists('Microsummary')) {
            Microsummary::publishSummary(__CLASS__);
        }

        $counter = new Counter(self::$name);
        $numbers = $counter->get();
        if (!empty($numbers)) {
            uasort($numbers, array($this, "_sortStatistics"));
            $YANA->setVar("STATS", $numbers);
        }

        return true;
    }

    /**
     * Search index for a specific string
     *
     * parameters taken:
     *
     * <ul>
     * <li> string target string to search for </li>
     * </ul>
     *
     * @type        read
     * @template    SEARCH_START
     * @menu        group: start
     *
     * @access      public
     * @param       string  $target  search term
     * @return      bool
     */
    public function search_start ($target)
    {
        global $YANA;

        $this->searchString = preg_replace("/[^\s\w\däöüß&;]/ui", "", $target);

        if (empty($this->searchString)) {
            return true;
        }
        assert('!isset($temp);');
        $temp = explode(" ", mb_strtolower($this->searchString));
        for ($i = 0; $i < count($temp); $i++)
        {
            /* update counter value */
            assert('!isset($counter_id);');
            assert('!isset($counter_info);');
            assert('!isset($counter_value);');
            $dummy = null;
            $counter_id = self::_applyStemming($temp[$i], $dummy);
            unset($dummy);
            $counter_info = $temp[$i];
            /* @var $statistics Counter */
            $statistics = Counter::getInstance(self::$name . '/' . $counter_id);
            $statistics->setInfo($counter_info);
            $counter_value = $statistics->getNextValue();
            /* update Microsummary */
            if (class_exists('Microsummary')) {
                assert('!isset($numbers);');
                $numbers = $statistics->getCount();
                if (is_array($numbers) && count($numbers) > 0) {
                    $most_wanted = array_pop($numbers);
                    if ($most_wanted <= $counter_value) {
                        Microsummary::setText(__CLASS__,
                            'Search most wanted: '.$counter_info.'('.$counter_value.')');
                    }
                }
                unset($numbers);
            }
            unset($counter_id,$counter_info,$counter_value);
        } /* end for */
        unset($temp);

        $hitlist = $this->_getFromCache($this->searchString);
        if (empty($hitlist)) {
            $hitlist = $this->_commit($this->searchString);
        }

        $YANA->setVar("SUBJECT", $this->searchString);
        $target = $YANA->getVar("PROFILE.SEARCH.TARGET");
        if (empty($target)) {
            $target = "_self";
        }
        $prefix = $YANA->getVar("PROFILE.SEARCH.PREFIX");

        assert('!isset($results); // Cannot redeclare var $results');
        $results = array();
        for ($i = 0; $i < count($hitlist); $i++)
        {
            if (count($hitlist[$i]) > 0 && isset($hitlist[$i][0]) && $hitlist[$i][0] != "\n") {
                $subject = preg_replace("/^\n/u", "", $hitlist[$i][0]);
                $url = $prefix . htmlspecialchars(preg_replace("/^.\//u", "", $subject), ENT_COMPAT, 'UTF-8');
                if (!empty($hitlist[$i][1]) || !empty($hitlist[$i][2])) {
                    $results[] = array(
                        'URL' => $url,
                        'TARGET' => $target,
                        'TITLE' => @$hitlist[$i][1],
                        'TEXT' => @$hitlist[$i][2]
                    );
                }
            }
        } /* end for */
        unset($i);

        $YANA->setVar("RESULTS", $results);

        return true;

    }

    /**
     * Create index file for search engine
     *
     * parameters taken:
     *
     * <ul>
     * <li> string  $dir               source directory </li>
     * <li> bool    $recurse  = false  recurse sub-directories </li>
     * <li> bool    $meta     = false  include meta-tags (title, keywords aso.) </li>
     * </ul>
     *
     * @type        primary
     * @user        group: admin, level: 100
     * @template    null
     * @safemode    true
     *
     * @access      public
     * @param       string  $dir      directory to index
     * @param       bool    $recurse  recurse into sub-directories (yes/no)
     * @param       bool    $meta     use meta tags (yes/no)
     * @return      bool
     */
    public function search_create_index ($dir, $recurse = false, $meta = false)
    {
        global $YANA;

        if (!headers_sent()) {
            header("Content-type: text/plain");
        }

        @set_time_limit(500);

        if (!is_dir(dir)) {
            print "WARNING: Directory '{$dir}' not found.\n";
            return false;
        }

        /*
         * 2) cache input values for later use
         */
        $YANA->callAction('set_config_default',
                array(self::$name => array('dir' => $dir, 'recurse' => $recurse, 'meta' => $meta)));

        /*
         * 3) Open document list for output
         */
        $resultKeywords = array();
        $currentDocument = 0;
        $fKeywords = $YANA->plugins->{"search:/keywords.file"};
        $fKeywords = $fKeywords->getPath();
        $fDocuments = $YANA->plugins->{"search:/documents.file"};
        $fDocuments = $fDocuments->getPath();
        $hDocuments = fopen($fDocuments, "w+");
        if ($hDocuments === false) {
            print "ERROR: Unable to open index-file '$fDocuments' for output.\n";
            return false;
        }

        /*
         * 4) Scan dir
         */
        assert('!isset($i); // Cannot redeclare var $i');
        assert('!isset($file); // Cannot redeclare var $file');
        foreach (self::_getListOfFiles($dir, '*.htm|*.html|*.xml|*.shtml', $recurse) as $i =>  $file)
        {
            $handle = fopen("$file", "r");
            if ($handle === false) {
                print "NOTICE: Unable to open file '$file'.\n";
                continue;
            } else {
                fwrite($hDocuments, $file);
            }

            print "(${i}) ${file}\n";

            assert('!isset($docTitle); // Cannot redeclare var $docTitle');
            $docTitle = false;
            assert('!isset($docDesc); // Cannot redeclare var $docDesc');
            $docDesc = "";
            assert('!isset($keywords); // Cannot redeclare var $keywords');
            $keywords = false;
            assert('!isset($headContent); // Cannot redeclare var $headContent');
            $headContent = null;

            assert('!isset($content); // Cannot redeclare var $content');
            $content = '';
            assert('!isset($h); // Cannot redeclare var $h');
            while (!feof($handle))
            {
                $content .= fread($handle, 8192);
                $h = null;
                if ($docTitle === false) {
                    $isTitle = preg_match('/<title>\s*([^<]+)\s*<\/title>/uUsi', $content, $docTitle);
                    $isTitle |= preg_match('/<meta\s+name="title"\s+content="([^">]+)"/uUsi', $content, $docTitle);
                    if ($isTitle) {
                        $content = str_replace($docTitle[0], '', $content);
                        $docTitle = preg_replace('/\s+/us', ' ', $docTitle[1]
                        );
                    } else {
                        $docTitle = false;
                    }
                }
                $isHead = preg_match('/<head>.*?<\/head>/usi', $content, $headContent);
                if ($meta === true && $keywords === false && $isHead) {
                    $headContent = $headContent[0];
                    while (preg_match('/<meta\s+name="keywords"\s+content="([^">]+)"/uUsi', $headContent, $h))
                    {
                        $headContent = str_replace($h[0], '', $headContent);
                        $keywords = array();
                        if (strpos($h[1], ',') !== false) {
                            $keywords = array_merge($keywords, explode(',', preg_replace('/\s{2,}/u', ' ', $h[1])));
                        } else {
                            $keywords[] = preg_replace('/\s+/us', ' ', $h[1]);
                        }
                    }
                }
                if (empty($docDesc)) {
                    $content = preg_replace('/^.*<body[^>]*>\s*/usi', '', $content);
                }
                $content = preg_replace('/<[^>]+>/u', ' ', $content);
                $content = html_entity_decode($content);
                $content = strip_tags($content);
                $content = preg_replace('/[\s\t]+/u', ' ', $content);
                if (mb_strlen($docDesc) < 150) {
                    $docDesc .= trim($content);
                }
                $content = preg_replace('/[^a-zA-Z\säöüß]+/u', ' ', $content);
                $content = preg_replace('/[\s\t]+/u', ' ', $content);
                $content = explode(' ', $content);
                for ($i = 0; $i < count($content) - 1; $i++)
                {
                    if ($content[$i] !== '') {
                        if (!isset($resultKeywords[$content[$i]])) {
                            $resultKeywords[$content[$i]] = array(0 => array(), 1 => array($currentDocument));
                        } else {
                            $resultKeywords[$content[$i]][1][] = $currentDocument;
                        }
                    }
                }
                unset($i);
                $content = array_pop($content) . ' ';
            }
            unset($content, $h);

            if (fclose($handle) === false) {
                trigger_error("Unable to close file '$file'.", E_USER_NOTICE);
                continue;
            }

            if (empty($docTitle)) {
                $docTitle = $file;
            } else {
                $content = html_entity_decode($docTitle);
                $content = strip_tags($content);
                $content = explode(' ', $content);
                for ($i = 0; $i < count($content); $i++)
                {
                    if ($content[$i] !== '') {
                        if (!isset($resultKeywords[$content[$i]])) {
                            $resultKeywords[$content[$i]] = array(0 => array(), 1 => array($currentDocument));
                        } else {
                            $resultKeywords[$content[$i]][1][] = $currentDocument;
                        }
                    }
                }
                unset($content, $i);
            }
            fwrite($hDocuments, ',' . str_replace(',', '', $docTitle) . ',' .
                htmlspecialchars(mb_substr($docDesc, 0, 150), ENT_COMPAT, 'UTF-8') . "]\n");

            if (is_array($keywords)) {
                for ($i = 0; $i < count($keywords); $i++)
                {
                    if (!isset($resultKeywords[$keywords[$i]])) {
                        $resultKeywords[$keywords[$i]] = array(0 => array(), 1 => array($currentDocument));
                    } else {
                        $resultKeywords[$keywords[$i]][1][] = $currentDocument;
                    }
                }
                unset($i);
            }

            unset($keywords, $headContent, $docTitle, $docDesc);
            
            ++$currentDocument;
        } /* end foreach */
        unset($i, $file); /* clean up garbage */

        /**
         * 5) compress results
         */
        assert('!isset($keyword); // Cannot redeclare var $keyword');
        assert('!isset($array); // Cannot redeclare var $array');
        assert('!isset($compare); // Cannot redeclare var $compare');
        assert('!isset($newKeyword); // Cannot redeclare var $newKeyword');
        foreach ($resultKeywords as $keyword => $array)
        {
            $compare = "";
            $newKeyword = self::_applyStemming($keyword, $compare);

            if ($newKeyword === '') {
                unset($resultKeywords[$keyword]);
            } elseif ($newKeyword !== $keyword) {
                unset($resultKeywords[$keyword]);
                if (isset($resultKeywords[$newKeyword])) {
                    if ($compare !== '') {
                        if (!isset($resultKeywords[$newKeyword][0])) {
                            $resultKeywords[$newKeyword][0] = array($compare);
                        } elseif (!in_array($compare, $resultKeywords[$newKeyword][0])) {
                            $resultKeywords[$newKeyword][0][] = $compare;
                        }
                    }
                    assert('!isset($id); // Cannot redeclare var $id');
                    foreach ($array[1] as $id)
                    {
                        if (!in_array($id, $resultKeywords[$newKeyword][1])) {
                            $resultKeywords[$newKeyword][1][] = $id;
                        }
                    }
                    unset($id);
                } else {
                    if ($compare !== '') {
                        $resultKeywords[$newKeyword] = array(0 => array($compare), 1 => $array[1]);
                    } else {
                        $resultKeywords[$newKeyword] = array(0 => array(), 1 => $array[1]);
                    }
                }
            }
        }
        unset($keyword, $array, $compare, $newKeyword);
        ksort($resultKeywords);

        /*
         * 6) close document list
         */
        if (fclose($hDocuments) === false) {
            print "ERROR: Unable to close index-file '$fDocuments'.\n";
            return false;
        }

        print "\n#" . count($resultKeywords) . " keywords indexed\n";

        /*
         * 7) output keywords
         */
        $hKeywords = fopen($fKeywords, "w+");
        if ($hKeywords === false) {
            print "ERROR: Unable to open index-file '$fKeywords' for output.\n";
            return false;
        }

        assert('!isset($keyword); // Cannot redeclare var $keyword');
        assert('!isset($array); // Cannot redeclare var $array');
        foreach ($resultKeywords as $keyword => $array)
        {
            fwrite($hKeywords, $keyword . '=');
            if (count($array[0]) > 0) {
                fwrite($hKeywords, '|'. implode('|', array_unique($array[0])));
            }
            if (count($array[1]) > 0) {
                fwrite($hKeywords, '"'. implode('"', array_unique($array[1])) . '"');
            }
            unset($resultKeywords[$keyword]);
            if (count($resultKeywords) > 0) {
                fwrite($hKeywords, ', ');
            }
        }
        unset($keyword, $array, $compare, $newKeyword);
        if (fclose($hKeywords) === false) {
            print "ERROR: Unable to close index-file '$fKeywords' for output.\n";
            return false;
        }

        return true;
    }

    /**
     * upload search index files to server
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        write
     * @user        group: admin, level: 100
     * @template    MESSAGE
     * @safemode    true
     * @onsuccess   goto: search_setup
     * @onerror     goto: search_setup
     *
     * @access      public
     * @return      bool
     */
    public function search_write_upload()
    {
        global $YANA;

        /*
         * 1) get path of target files
         */
        $documents_dat = $YANA->plugins->{'search:/documents.file'};
        if (!is_object($documents_dat)) {
            return false;
        } else {
            $documents_dat = $documents_dat->getPath();
        }

        $keywords_dat = $YANA->plugins->{'search:/keywords.file'};
        if (!is_object($keywords_dat)) {
            return false;
        } else {
            $keywords_dat = $keywords_dat->getPath();
        }

        /*
         * 2) check if both files have been provided
         */
        if (empty($_FILES['documents_dat']['tmp_name'])) {
            $data = array('FIELD' => '"documents.dat"');
            $error = new MissingFieldWarning();
            $error->setData($data);
            throw new $error;
        }
        if (empty($_FILES['keywords_dat']['tmp_name'])) {
            $data = array('FIELD' => '"keywords.dat"');
            $error = new MissingFieldWarning();
            $error->setData($data);
            throw new $error;
        }
        /*
         * 3) check names of uploaded files
         */
        if ($_FILES['documents_dat']['name'] !== 'documents.dat') {
            $data = 'documents.dat = "' . $_FILES['documents_dat']['name'] . '"';
            throw new InvalidValueWarning($data);
        }
        if ($_FILES['keywords_dat']['name'] !== 'keywords.dat') {
            $data = '"keywords.dat = "' . $_FILES['keywords_dat']['name'] . '"';
            throw new InvalidValueWarning($data);
        }
        /*
         * 4) move uploaded files to destination
         */
        if (!move_uploaded_file($_FILES['documents_dat']['tmp_name'], $documents_dat)) {
            throw new InvalidValueWarning('"documents.dat"');
        }
        if (!move_uploaded_file($_FILES['keywords_dat']['tmp_name'], $keywords_dat)) {
            throw new InvalidValueWarning('"keywords.dat"');
        }
        return true;
    }

    /**
     * search for a subject
     *
     * @access  private
     * @param   string  $subject    subject
     * @return  array
     * @ignore
     */
    private function _commit($subject)
    {
        assert('is_string($subject); // Wrong type for argument 1. String expected');

        global $YANA;
        $found = false;
        $hits = array();
        $hitlist = array();
        $request = explode(" ", $subject);
        $keywords = $YANA->plugins->{'search:/keywords.file'};
        $documents = $YANA->plugins->{'search:/documents.file'};
        $documentList = array();

        if (!$keywords->exists() || !$documents->exists()) {
            throw new SearchFailedError();
        }

        $keywords->read();
        $KEYS = preg_replace("/\n/u", "", $keywords->getContent());
        $KEYS = explode(", ", $KEYS);

        assert('!isset($i); // Cannot redeclare var $i');
        for ($i = 0; $i < count($request); $i++)
        {
            $dummy = null;
            $enc_string = self::_applyStemming($request[$i], $dummy);
            unset($dummy);
            $found = false;
            $max = count($KEYS)-1;
            $min = 0;
            $n = floor($max /2);
            $prev_n = array(-1,-1);

            while (!$found && $max != $min)
            {
                $temp2 = preg_replace("/=.*/ui", "", $KEYS[$n]);
                $temp2 = strcasecmp($temp2, $enc_string);
                if ($temp2 > 0) {
                    $max = $n;
                } else if ($temp2<0) {
                    $min = $n;
                } else {
                    $hits[$request[$i]]=explode("\"", preg_replace("/.*=/ui", "", $KEYS[$n]));
                    $_stringWithPrefix = preg_replace("/~/u", $enc_string, $hits[$request[$i]][0]);
                    $_stringWithSuffix = trim(preg_replace("/\|/u", " ", $_stringWithPrefix));
                    $whatsRelated = preg_replace("/\s\s/u", " ", $_stringWithSuffix);
                    if ($hits[$request[$i]][0] != "" && $request[$i] != $whatsRelated) {
                        $this->searchString .= '<span class="search_related">' .
                            '<a class="search_related" href="javascript:whatsRelated()" target="_self">' .
                            $YANA->language->getVar('related') . '&nbsp;&quot;' .
                            $request[$i]. '&quot;:</a> ' . $whatsRelated . '</span>';
                    }
                    $found = true;
                } /* end if */

                array_shift($prev_n);
                $prev_n[count($prev_n)] = $n;

                if ($min!=$n) {
                        $n = $min + floor(($max - $min) /2);
                } else {
                        $n = $min + ceil(($max - $min) /2);
                }

                if ($prev_n[0] == $n || $prev_n[1] == $n) {
                    $hits[$request[$i]][0] = "";
                    break;
                }

            } /* end while */
        } /* end for */
        unset($i);

        if (count($hits) > 0) {
             array_shift($hits[$request[0]]); /* 0th is always empty - so drop it */
             $hitlist=$hits[$request[0]];
        }

        /* BEGIN logical AND */
        assert('!isset($i); // Cannot redeclare var $i');
        for ($i = 1; $i < count($request); $i++)
        {
            $myTemp = array();
            assert('!isset($j); // Cannot redeclare var $j');
            for ($j = 1; $j < count($hits[$request[$i]]); $j++)
            {
                if (in_array($hits[$request[$i]][$j], $hitlist)) {
                    $myTemp[count($myTemp)] = $hits[$request[$i]][$j];
                }
            }
            unset($j);
            $hitlist = $myTemp;
        } /* end for */
        unset($i);
        /* END logical AND */

        /* BEGIN load list if documents */
        $zeile = 0;
        $buffer = "";

        $file = fopen($documents->getPath(), "r");
        flock($file, LOCK_SH);
        while (!feof($file))
        {
            $temp = fgetc($file);
            if ($temp == ']') {
                if (in_array((int) $zeile, $hitlist)) {
                    $documentList[$zeile] = explode(',', $buffer);
                }
                $buffer="";
                $zeile++;
            } else {
                $buffer .= $temp;
            }
        } /* end while */
        flock($file, LOCK_UN);
        fclose($file);
        /* END load list if documents */

        if (count($hitlist) >= 1) {
            /* resolve Ids */
            sort($request);
            $this->_toCache(implode(" ", $request), $hitlist, $documentList);
            assert('!isset($i); // Cannot redeclare var $i');
            for ($i = 0; $i < count($hitlist); $i++)
            {
                if (isset($hitlist[$i]) && $hitlist[$i] != "") {
                    $hitlist[$i] = $documentList[(int) $hitlist[$i]];
                }
            } /* end for */
            unset($i);
        } /* end if */

        return $hitlist;
    }

    /**
     * add a result to cache
     *
     * @access  private
     * @param   string  $subject     subject
     * @param   array   &$value      value
     * @param   array   &$documents  documents
     * @return  bool
     * @ignore
     */
    private function _toCache($subject, array &$value, array &$documents)
    {
        assert('is_string($subject); // Wrong type for argument 1. String expected');

        global $YANA;

        $id = $YANA->getVar('ID');
        if (empty($id)) {
            $id = "default";
        }
        $cacheFile = $YANA->getVar('TEMPDIR')."$id.cache";
        if (sizeOf($this->cache)>30) {
            array_pop($this->cache);
        }

        $subject = self::_getCacheId($subject);
        $text = $subject.'='.rawurlencode($this->searchString).";";

        for ($i = 0; $i < count($value); $i++)
        {
            if (isset($documents[$value[$i]])) {
                $text .= rawurlencode(trim(preg_replace("/\s/us", " ", implode(",", $documents[$value[$i]])))).";";
            }
        }
        unset($i);

        $text .= "\n" . implode("", $this->cache);

        if (! file_exists($cacheFile)) {
            $file = fopen($cacheFile, "w+");
            flock($file, LOCK_EX);
            fwrite($file, $text);
            flock($file, LOCK_UN);
            fclose($file);
        } elseif (is_writeable($cacheFile)) {
            $file = fopen($cacheFile, "w+");
            flock($file, LOCK_EX);
            fwrite($file, $text);
            flock($file, LOCK_UN);
            fclose($file);
        } else {
            return false;
        }

        return true;

    }

    /**
     * calculate cache-id
     *
     * @access  private
     * @static
     * @param   string  &$subject  list of search terms
     * @return  string
     * @ignore
     */
    private static function _getCacheId(&$subject)
    {
        assert('is_string($subject); // Wrong type for argument 1. String expected');
        $temp = explode(" ", "$subject");
        $dummy = null;
        for ($i = 0; $i < sizeOf($temp); $i++)
        {
            $temp[$i] = self::_applyStemming($temp[$i], $dummy);
        }
        sort($temp);
        return implode(" ", $temp);
    }

    /**
     * user defined sort function
     *
     * @access  private
     * @param   array  $A  base array of statistics
     * @param   array  $B  compared array of statistics
     * @return  int
     * @ignore
     */
    private function _sortStatistics($A, $B)
    {
        if ($A['COUNT'] < $B['COUNT']) {
            return +1;
        } elseif ($A['COUNT'] > $B['COUNT']) {
            return -1;
        } elseif ($A['INFO'] < $B['INFO']) {
            return -1;
        } elseif ($A['INFO'] > $B['INFO']) {
            return +1;
        } else {
            return 0;
        }
    }

    /**
     * apply stemming
     *
     * @access  private
     * @static
     * @param   string  $inputString  input var
     * @param   string  &$compare     output var
     * @return  string
     * @ignore
     */
    private static function _applyStemming($inputString, &$compare)
    {
        assert('is_string($inputString); // Wrong type for argument 1. String expected');
        $compare = "";

        $inputString = mb_strtolower($inputString);
        $inputString = html_entity_decode($inputString);
        $inputString = str_replace('ä', 'a', $inputString);
        $inputString = str_replace('ü', 'u', $inputString);
        $inputString = str_replace('ö', 'o', $inputString);
        $inputString = str_replace('ß', 'ss', $inputString);
        $inputString = preg_replace('/[^a-zA-Z\s]/u', '', $inputString);

        /* @var $YANA Yana */
        global $YANA;
        $grammar = $YANA->plugins->search->getVar('GRAMMAR');
        assert('is_array($grammar);');

        if (in_array($inputString, $grammar['STOPWORDS'])) {
            $inputString = "";
        } else {
            foreach ($grammar['START_WORD'] as $word)
            {
                if ($inputString != $word) {
                    if (preg_match("/^$word/u", $inputString)) {
                        $inputString = preg_replace("/^$word/u", "", $inputString, 1);
                        $compare = $word . '~';
                        break;
                    }
                }
            }
            foreach ($grammar['END_WORD'] as $word)
            {
                if ($inputString != $word) {
                    if (preg_match("/$word$/u", $inputString)) {
                        $inputString = preg_replace("/$word$/u", "", $inputString, 1);
                        if ($compare === "") {
                            $compare = '~' . $word;
                        } else {
                            $compare .= $word;
                        }
                        break;
                    }
                }
            }
            $inputString = preg_replace('/\s/us', '', $inputString);
        }

        $inputString = trim($inputString);
        if (mb_strlen($inputString) < 3 || mb_strlen($inputString) > 20) {
            return "";
        } else {
            return $inputString;
        }
    }

    /**
     * get list of files
     *
     * @access  private
     * @static
     * @param   string  $dir      directory
     * @param   string  $filter   filter
     * @param   bool    $recurse  recurse
     * @return  array
     * @ignore
     */
    private static function _getListOfFiles($dir, $filter, $recurse)
    {
        assert('is_string($dir); // Wrong type for argument 1. String expected');
        assert('is_dir($dir); // Invalid argument 1. Directory expected');
        assert('is_string($filter); // Wrong type for argument 2. String expected');
        assert('is_bool($recurse); // Wrong type for argument 3. Boolean expected');
        $list = array();

        $dir .= '/';

        /* 1 recurse sub-directories */
        if ($recurse) {
            assert('!isset($subdir); // Cannot redeclare var $subdir');
            foreach (dirlist($dir, "", YANA_GET_DIRS) as $subdir)
            {
                /* ignore directories, which start with an underscore */
                if (strpos($subdir, '_') === 0) {
                    continue;
                } else {
                    $list = array_merge($list, self::_getListOfFiles($dir . $subdir, $filter, true));
                }
            }
            unset($subdir);
        }

        /* 2 get files in current directory */
        assert('!isset($file); // Cannot redeclare var $subdir');
        foreach (dirlist($dir, $filter, YANA_GET_FILES) as $file)
        {
            $list[] = $dir . $file;
        }
        unset($file);

        return $list;
    }

    /**
     * load from cache
     *
     * @access  private
     * @param   string  $subject    subject
     * @return  array|bool(false)
     * @ignore
     */
    private function _getFromCache($subject)
    {
        assert('is_string($subject); // Wrong type for argument 1. String expected');
        global $YANA;

        $id = $YANA->getVar('ID');
        if (empty($id)) {
            $id = "default";
        }
        $cacheFile = $YANA->getVar('TEMPDIR')."$id.cache";
        $hitlist = array();

        if (file_exists($cacheFile) && is_readable($cacheFile)) {
            $this->cache = file($cacheFile);
        } else {
            return false;
        }

        $subject = self::_getCacheId($subject);
        $temp = "";
        for ($i = 0; $i < sizeOf($this->cache); $i++)
        {
            if (preg_match("/^".$subject."=/ui", $this->cache[$i])) {
                $temp = preg_replace("/^".$subject."=/u", "", $this->cache[$i]);
                break;
            }
        }
        unset($i);

        if (!empty($temp)) {
            $temp = explode(";", $temp);
            $this->searchString = rawurldecode($temp[0]);
            assert('!isset($i); // Cannot redeclare var $i');
            for ($i = 1; $i < count($temp); $i++)
            {
                $hitlist[] = explode(',', rawurldecode($temp[$i]));
            }
            unset($i);
            return $hitlist;
        } else {
            return false;
        }
    }
}

?>