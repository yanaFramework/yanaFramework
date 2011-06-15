<?php
/**
 * Guestbook
 *
 * This is the guestbook main program.
 * It creates the GUI and manages reading and writing of guestbook entries to
 * and from a database or text file.
 *
 * {@translation
 *
 *   de: Gästebuch
 *
 *       Dies ist das Hauptprogramm des Gästebuches.
 *       Es erzeugt alle Eingabeoberflächen und liest bzw. schreibt Daten
 *       in die Datenbank oder eine Datei.
 *
 *   , fr: Livre d'or
 * }
 *
 * @author     Thomas Meyer
 * @type       primary
 * @group      guestbook
 * @license    http://www.gnu.org/licenses/gpl.txt
 *
 * @package    yana
 * @subpackage plugins
 */

/**
 * guestbook plugin
 *
 * This implements a guestbook application, including the ability to add comments and notification
 * on new entries via e-mail.
 *
 * @access     public
 * @package    yana
 * @subpackage plugins
 */
class plugin_guestbook extends StdClass implements IsPlugin
{

    /**
     * @access  private
     * @static
     * @var     mixed
     */
    private static $database = null;

    /**#@+
     * @access  private
     * @ignore
     */

    /** @var string (readonly) */ private $actionEntry        = "guestbook_entry";
    /** @var string (readonly) */ private $actionNewWrite     = "guestbook_write_new";
    /** @var string (readonly) */ private $actionDelete       = "guestbook_write_delete";
    /** @var string (readonly) */ private $actionEdit         = "guestbook_read_edit";
    /** @var string (readonly) */ private $actionEditWrite    = "guestbook_write_edit";
    /** @var string (readonly) */ private $actionComment      = "guestbook_default_comment";
    /** @var string (readonly) */ private $actionCommentWrite = "guestbook_write_comment";

    /**#@-*/

    /**
     * get database connection
     *
     * @access  protected
     * @static
     * @return  DbStream
     */
    protected static function getDatabase()
    {
        if (!isset(self::$database)) {
            self::$database = Yana::connect("guestbook");
        }
        return self::$database;
    }

    /**
     * Constructor
     *
     * @access  public
     * @ignore
     */
    public function __construct()
    {
        global $YANA;
        if (isset($YANA)) {
            /* Plugin-specific addities */
            $YANA->setVar('ACTION_ENTRY',         $this->actionEntry);
            $YANA->setVar('ACTION_NEW_WRITE',     $this->actionNewWrite);
            $YANA->setVar('ACTION_DELETE',        $this->actionDelete);
            $YANA->setVar('ACTION_EDIT',          $this->actionEdit);
            $YANA->setVar('ACTION_EDIT_WRITE',    $this->actionEditWrite);
            $YANA->setVar('ACTION_COMMENT',       $this->actionComment);
            $YANA->setVar('ACTION_COMMENT_WRITE', $this->actionCommentWrite);
        }
    }

    /**
     * Default event handler
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
     * edit guestbook entries
     *
     * this function expects one argument: 'target'
     *
     * @type        read
     * @user        group: guestbook, role: moderator
     * @user        group: admin, level: 75
     * @template    templates/edit.html.tpl
     * @language    guestbook
     * @style       templates/default.css
     * @onerror     goto: GUESTBOOK_READ
     *
     * @access      public
     * @param       int  $target  id of guestbook entry
     */
    public function guestbook_read_edit($target)
    {
        global $YANA;

        $row = self::getDatabase()->select("guestbook.$target");

        /* check if target row exists */
        if (empty($row)) {
            throw new InvalidInputWarning();
        }

        $YANA->setVar('ROW', $row);
    }

    /**
     * save edited guestbook entries
     *
     * this function expects one argument: 'target'
     *
     * @type        write
     * @user        group: guestbook, role: moderator
     * @user        group: admin, level: 75
     * @template    MESSAGE
     * @onsuccess   goto: GUESTBOOK_READ_EDIT
     * @onerror     goto: GUESTBOOK_READ_EDIT
     *
     * @access      public
     * @param       int     $target      guestbook id
     * @param       string  $name        author name
     * @param       string  $message     text
     * @param       string  $msgtyp      messenger type
     * @param       string  $messenger   messenger id
     * @param       string  $mail        author mail
     * @param       string  $hometown    author location
     * @param       string  $homepage    URL
     * @param       int     $opinion     rating (0..5)
     * @return      bool
     */
    public function guestbook_write_edit($target, $name, $message, $msgtyp, $messenger = "", $mail = "", $hometown = "", $homepage = "", $opinion = "")
    {
        global $YANA;

        $permission = $YANA->getVar("PERMISSION");
        /* avoid spamming */
        if (!is_int($permission) || $permission < 1) {
            if (PluginManager::getInstance()->isActive('antispam') && $YANA->getVar("PROFILE.SPAM.CAPTCHA")) {
                if ($YANA->callAction("security_check_image", Request::getPost()) === false) {
                    Log::report('SPAM: CAPTCHA not solved, entry has not been created.');
                    throw new SpamError();
                }
            }
        }

        /* values to create new entry */
        $entry = array(
            'GUESTBOOK_NAME' => $name,
            'GUESTBOOK_MESSAGE' => $message,
            'GUESTBOOK_MSGTYPE' => $msgtyp,
            'GUESTBOOK_MESSENGER' => $messenger,
            'GUESTBOOK_MAIL' => $mail,
            'GUESTBOOK_HOMETOWN' => $hometown,
            'GUESTBOOK_HOMEPAGE' => $homepage,
            'GUESTBOOK_OPINION' => $opinion
        );

        $database = self::getDatabase();

        /* before doing anything, check if entry exists */
        if (!$database->exists("guestbook.$target")) {
            /* error - no such entry */
            Log::report("The selected entry guestbook.$target does not exist!");
            throw new InvalidInputWarning();
        }

        /**
         * 1) If the update operation was not successful, issue an error
         *    message and abort. (will also forfeit all previously made,
         *    uncommited changes)
         */
        if (!$database->update("guestbook.$target", $entry)) {
            throw new InvalidInputWarning();
        }

        /* don't forget to save your recent changes ;-) */
        return $database->write();
    }

    /**
     * delete guestbook entries
     *
     * this function expects one argument: 'selected_entries'
     *
     * @type        write
     * @user        group: guestbook, role: moderator
     * @user        group: admin, level: 75
     * @template    MESSAGE
     * @onsuccess   goto: GUESTBOOK_READ
     * @onerror     goto: GUESTBOOK_READ
     *
     * @access      public
     * @param       array  $selected_entries  list of entries to delete
     * @return      bool
     * @throws      Alert  when some input data is invalid
     */
    public function guestbook_write_delete(array $selected_entries)
    {
        /* check if input exists */
        if (!empty($selected_entries)) {
            throw new InvalidInputWarning();
        }
        $database = self::getDatabase();
        /* loop through selected entries */
        foreach($selected_entries as $id)
        {
            $id = mb_strtolower($id);

            /**
             * 1) If the entry to delete does not exist,
             *    issue an error message and abort. (will also forfeit all
             *    previously made, uncommited changes)
             */
            if (!$database->exists("guestbook.$id")) {
                Log::report("The selected entry guestbook.$id does not exist!");
                throw new InvalidInputWarning();

            }

            /*
             * 2) If the delete operation was not successful, issue an error
             *    message and abort. (will also forfeit all previously made,
             *    uncommited changes)
             */
            if (!$database->remove("guestbook.$id")) {
                throw new InvalidInputWarning();

            /*
             * 3) If all was fine, proceed to next value.
             */
            } else {
                continue;
            }
        } /* end foreach */
        /* now delete those entries */
        return $database->write();
    }

    /**
     * output RSS feed
     *
     * @type        read
     * @template    NULL
     * @language    guestbook
     *
     * @access      public
     */
    public function guestbook_read_rss()
    {
        global $YANA;
        if (!class_exists('RSS')) {
            throw new Error("RSS wrapper class not found. Unable to proceed.");
        }
        self::_securityCheck(); // throws FileNotFoundError

        /* get entries */
        $rows = $this->_getTable();
        assert('is_array($rows); /* unexpected result: $rows */');

        /* create RSS feed */
        $rss = new RSS();
        $rss->description = $YANA->getLanguage()->getVar('RSS_DESCRIPTION');
        if (empty($rss->description)) {
            $rss->description = 'the 10 most recent guestbook entries';
        }
        SmartUtility::loadSmilies();
        foreach ($rows as $row)
        {
            $item = new RSSitem();
            /* 1) process title */
            $item->title = $row['GUESTBOOK_NAME'];
            /* 2) process link */
            $id = $row['GUESTBOOK_ID'];
            $item->link = SmartUtility::url('action=guestbook_read&target='.$id, true);
            /**
             * {@internal
             * Note: guid is ignored by some aggregators and link is used instead
             * where this is the case, the session id can not be part of the uri as this
             * is subject to change. }}
             */
            $item->link = str_replace(session_name()."=".session_id(), '', $item->link);
            /* 3) process description */
            $item->description = $row['GUESTBOOK_MESSAGE'];
            $item->description = SmartUtility::embeddedTags($item->description);
            $item->description = SmartUtility::smilies($item->description);
            $item->description = strip_tags($item->description);
            if (mb_strlen($item->description) > 500) {
                $item->description = mb_substr($item->description, 0, 496).' ...';
            }
            /* 4) process pubDate */
            $item->pubDate     = $row['GUESTBOOK_DATE'];
            if (is_numeric($item->pubDate)) {
                $item->pubDate = date('r', $item->pubDate);
            }
            $rss->addItem($item);
        } /* end foreach */
        print $rss->toString();
        exit(0);
    }

    /**
     * save a new guestbook entry
     *
     * @type        write
     * @language    guestbook
     * @template    MESSAGE
     * @onsuccess   goto: GUESTBOOK_READ
     * @onerror     goto: GUESTBOOK_READ
     *
     * @access      public
     * @param       string  $name        author name
     * @param       string  $message     text
     * @param       string  $msgtyp      messenger type
     * @param       string  $messenger   messenger id
     * @param       string  $mail        author mail
     * @param       string  $hometown    author location
     * @param       string  $homepage    URL
     * @param       int     $opinion     rating (0..5)
     */
    public function guestbook_write_new($name, $message, $msgtyp, $messenger = "", $mail = "", $hometown = "", $homepage = "", $opinion = "")
    {
        global $YANA;
        self::_securityCheck(); // throws FileNotFoundError

        $permission = $YANA->getVar("PERMISSION");
        /* avoid spamming */
        if (!is_int($permission) || $permission < 1) {
            if (PluginManager::getInstance()->isActive('antispam') && $YANA->getVar("PROFILE.SPAM.CAPTCHA")) {
                if ($YANA->callAction("security_check_image", Request::getPost()) === false) {
                    Log::report('SPAM: CAPTCHA not solved, entry has not been created.');
                    throw new SpamError();
                }
            }
        }

        $database = self::getDatabase();
        /* values to create new entry */
        $entry = array(
            'guestbook_name' => $name,
            'guestbook_message' => $message,
            'guestbook_msgtype' => $msgtyp,
            'guestbook_messenger' => $messenger,
            'guestbook_mail' => $mail,
            'guestbook_hometown' => $hometown,
            'guestbook_homepage' => $homepage,
            'guestbook_opinion' => $opinion
        );

        /* set profile id (overwrite if already exists */
        $entry['profile_id'] = Yana::getId();

        /* mark registered users */
        if (strcasecmp(YanaUser::getUserName(), $name) === 0) {
            $entry['guestbook_is_registered'] = 1;
        }

        /* avoid flooding
         *
         * return error if entry with same content already exists
         */
        $myFlood = $YANA->getPlugins()->{"guestbook:/my.floodfile"};
        $myFlood->setMax((int)$YANA->getVar("PROFILE.GUESTBOOK.FLOODING"));
        if ($myFlood->isBlocked()) {
            Log::report('Possibly flooding attempt detected. User request rejected.');
            throw new FloodWarning();
        }
        assert('!isset($where); // Cannot redeclare var $where');
        $where = array('profile_id', '=', Yana::getId());
        $recent_entry = $database->select("guestbook.?.guestbook_message", $where);
        unset($where);
        if (!empty($entry['guestbook_message']) && $recent_entry == $entry['guestbook_message']) {
            throw new SpamWarning();
        }
        if ($myFlood->getMax() > 0) {
            $myFlood->set();
        }
        /* insert new entry into table */
        if (!$database->insert('guestbook.*', $entry)) {
            Log::report('Failed to insert entry.', E_USER_NOTICE, $entry);
            throw new InvalidInputWarning();
        }
        if (!$database->write()) {
            Log::report('Unable to submit entry.', E_USER_NOTICE, $entry);
            throw new Error();
        }
        /* send Mail */
        if ($YANA->getVar("PROFILE.GUESTBOOK.MAIL") && $YANA->getVar("PROFILE.GUESTBOOK.NOTIFICATION")) {
            $mailer = $YANA->getPlugins()->{"guestbook:/notification.mailer"};
            self::_sendMail($mailer, $entry);
        }
        Microsummary::setText(__CLASS__, 'Guestbook, update ' . date('d M y G:s', time()));
    }

    /**
     * display single guestbook entry
     *
     * @type        read
     * @template    templates/entry.html.tpl
     * @language    guestbook
     * @style       templates/default.css
     * @onerror     goto: null, text: InvalidInputWarning
     *
     * @access      public
     * @param       int  $target  id of entry to show
     * @return      bool
     */
    public function guestbook_entry($target)
    {
        global $YANA;
        self::_securityCheck(); // throws FileNotFoundError

        // create link to user profile (if profile viewer is installed)
        if ($YANA->getPlugins()->isActive('user_admin')) {
            $YANA->setVar('GUESTBOOK_USER_LINK', SmartUtility::url("action=view_profile&target[user_id]="));
        } else {
            $YANA->setVar('GUESTBOOK_USER_LINK', false);
        }

        /* get entries */
        $row = self::getDatabase()->select("guestbook.$target");
        if (empty($row)) {
            return false;
        } else {
            assert('is_array($row); /* unexpected result: $row */');
            $YANA->setVar('CURRENT', $row);
            return true;
        }
    }

    /**
     * Guestbook
     *
     * @type        default
     * @template    templates/read.html.tpl
     * @language    guestbook
     * @style       ../../skins/default/styles/gui_generator.css
     * @style       templates/default.css
     * @script      templates/ajax.js
     * @onerror     goto: null, text: InvalidInputWarning
     * @menu        group: start
     *
     * @access      public
     * @param       int  $page     current page number (if there are more)
     * @param       int  $entries  number of entries per page
     */
    public function guestbook_read($page = 0, $entries = null)
    {
        global $YANA;
        self::_securityCheck(); // throws FileNotFoundError

        Microsummary::publishSummary(__CLASS__);
        RSS::publishFeed('guestbook_read_rss');

        /* get entries */
        $rows = $this->_getTable($page, $entries);
        assert('is_array($rows); /* unexpected result: $rows */');

        // create link to user profile (if profile viewer is installed)
        if ($YANA->getPlugins()->isActive('user_admin')) {
            $YANA->setVar('GUESTBOOK_USER_LINK', SmartUtility::url("action=view_profile&target[user_id]="));
        } else {
            $YANA->setVar('GUESTBOOK_USER_LINK', false);
        }
        $YANA->setVar('ROWS', $rows);
        $YANA->setVar('DESCRIPTION', $YANA->getLanguage()->getVar('descr_show'));
        $useCaptcha = PluginManager::getInstance()->isActive('antispam') && $YANA->getVar("PROFILE.SPAM.CAPTCHA") &&
            !YanaUser::isLoggedIn();
        $YANA->setVar('USE_CAPTCHA', $useCaptcha);
    }

    /**
     * Comment
     *
     * @type        default
     * @template    templates/comment.html.tpl
     * @language    guestbook
     * @style       ../../skins/default/styles/gui_generator.css
     * @style       templates/default.css
     * @script      templates/ajax.js
     * @user        group: guestbook
     * @user        group: admin, level: 30
     * @onerror     goto: GUESTBOOK_READ
     *
     * @access      public
     * @param       int  $target  id of comment to retrieve
     */
    public function guestbook_default_comment($target)
    {
        global $YANA;
        $guestbook_comment = self::getDatabase()->select('guestbook.'.$target.'.guestbook_comment');
        if (!empty($guestbook_comment)) {
             $YANA->setVar('GUESTBOOK_COMMENT', $guestbook_comment);
        }
    }

    /**
     * Comment
     *
     * @type        write
     * @template    message
     * @user        group: guestbook
     * @user        group: admin, level: 30
     * @onsuccess   goto: GUESTBOOK_READ
     * @onerror     goto: GUESTBOOK_DEFAULT_COMMENT
     *
     * @access      public
     * @param       int     $target             id of comment to edit
     * @param       string  $guestbook_comment  comment text
     */
    public function guestbook_write_comment($target, $guestbook_comment = "")
    {
        $database = self::getDatabase();
        // If the update operation was not successful, issue an error message and abort.
        if (!$database->update("guestbook.${target}.guestbook_comment", $guestbook_comment)) {
            $message = "Unable to insert comment at 'guestbook.${target}.'";
            Log::report($message, E_USER_WARNING, array('guestbook_comment' => $guestbook_comment));
            throw new InvalidInputWarning();
        }
        if (!$database->write()) {
            $message = "Unable to commit changes to entry 'guestbook.${target}.'";
            Log::report($message, E_USER_ERROR, array('guestbook_comment' => $guestbook_comment));
            throw new Error();
        }
    }

    /**
     * _getTable
     *
     * @access  private
     * @param   int  $page        current page number (if there are more)
     * @param   int  $entPerPage  number of entries per page
     * @return  array
     * @ignore
     */
    private function _getTable($page = 0, $entPerPage = null)
    {
        global $YANA;

        $database = self::getDatabase();
        /* check if $table really is a table */
        $table = $database->getSchema()->getTable("guestbook");
        assert('$table instanceof DDLTable;');

        /* get the name of the primary key */
        $primary_key = $table->getPrimaryKey();
        assert('is_string($primary_key); /* unexpected result: $primary_key */');

        if (empty($entPerPage)) {
            $entPerPage = (int) $YANA->getVar("PROFILE.GUESTBOOK.ENTPERPAGE");
        }
        if ($entPerPage < 1) {
            $entPerPage = 10;
        }

        $where = array("profile_id", "=", Yana::getId());
        $sortBy = "guestbook_date";
        $desc = true;
        /* get rows from database */
        $result = $database->select($table->getName(), $where, $sortBy, $page, $entPerPage, $desc);

        /* set template vars */

        $YANA->setVar('TABLE', $table->getName()); /* table name */
        $YANA->setVar('PRIMARY_KEY', $primary_key); /* name of primary key column */
        $last_page = $database->length($table->getName(), $where); /* total number of rows */
        $YANA->setVar('LAST_PAGE', $last_page);
        $YANA->setVar('SORT', $sortBy); /* column name */

        /* create a footer with links to other pages of the resultset */
        if ($last_page > ($entPerPage * 2)) {
            $liste = array();
            $dots = false;
            for($k = 0; $k < ceil($last_page / $entPerPage); $k++)
            {
                $it   = count($liste);
                $first = ($k * $entPerPage);
                if (($k + 1) * $entPerPage < $last_page) {
                    $last  = ($k + 1) * $entPerPage;
                } else {
                    $last  = $last_page;
                }
                $has10Pages = $last_page > 10 * $entPerPage;
                $isNotFirstPage = $k > 1;
                $isNotLastPage = $k < ceil($last_page/$entPerPage) - 2;
                $isNearCurrentPage = ($first > $page - $entPerPage - 1 && $last < $page + 1 + (2 * $entPerPage));
                if ($has10Pages && $isNotFirstPage && $isNotLastPage && !$isNearCurrentPage) {
                    if (!$dots) {
                        $dots = true;
                        $liste[$it]['TOO_MANY'] = true;
                    }
                } else {
                    $liste[$it]['FIRST'] = ($first + 1);
                    $liste[$it]['LAST']  = $last;
                    if ($first > $page-1 && $last < $page + $entPerPage + 1) {
                        $dots = false;
                    }
                }
            } // end foreach

            /* provide result to template */
            $YANA->setVar('LIST_OF_ENTRIES', $liste);
        } else {
            /* only one page - footer is ignored */
            $YANA->setVar('LIST_OF_ENTRIES', array());
        }// end if

        $YANA->setVar('ENTRIES_PER_PAGE', $entPerPage); /* chunk size - numer of entries per page */
        $YANA->setVar('DESC', $desc); /* boolean value indicating sorting direction */
        /* number of the first result on the current page */
        if ($last_page > 0) {
            $YANA->setVar('FIRST_PAGE', $page + 1);
        } else {
            $YANA->setVar('FIRST_PAGE', 0); /* no entries on current page */
        }
        /* the numer of the last entry on the currently viewed page */
        if ($page + $entPerPage > $last_page) {
            $YANA->setVar('OFFSET_PAGE', $last_page); /* currenly viewed page is the last of the resultset */
        } else {
            $YANA->setVar('OFFSET_PAGE', $page + $entPerPage); /* there are more pages ... */
        }

        /* return an array containing the requested rows */
        return $result;
    }

    /**
     * _securityCheck
     *
     * @access  private
     * @static
     * @throws  FileNotFoundError
     */
    private static function _securityCheck()
    {
        global $YANA;
        $id = Yana::getId();
        /* do not show new guestbooks, if Auto-Option is deactivated */
        $dir = $YANA->getResource('system:/config/profiledir');
        $file = new FileReadonly($dir->getPath() . $id . '.cfg');
        if ($id !== 'default' && !$file->exists() && !$YANA->getVar("PROFILE.AUTO")) {
            Log::report('Access restriction in effect. Access to undefined profile ' . $id . ' denied.');
            throw new FileNotFoundError();
        }
    }

    /**
     * send mail
     *
     * @access  private
     * @static
     * @param   Mailer  $mail       mail template
     * @param   array   $INPUT      input data
     */
    private static function _sendMail(Mailer $mail, array $INPUT)
    {
        global $YANA;
        $sender = $YANA->getVar("PROFILE.MAIL");
        $recipient = $YANA->getVar("PROFILE.GUESTBOOK.MAIL");
        if (filter_var($recipient, FILTER_VALIDATE_EMAIL) && filter_var($sender, FILTER_VALIDATE_EMAIL)) {
            $INPUT = \Yana\Util\Hashtable::changeCase($INPUT, CASE_UPPER);
            $now = getdate();
            $mail->setSubject($YANA->getLanguage()->getVar("MAIL_SUBJECT"));
            $mail->setSender($sender);
            $mail->setVar('*', $INPUT);
            $mail->setVar('DATE', $now['mday'] . '.' . $now['mon'] . '.' . $now['year']);
            $mail->send($recipient);
        }
    }

}

?>