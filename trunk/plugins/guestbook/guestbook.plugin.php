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

namespace Plugins\Guestbook;

/**
 * guestbook plugin
 *
 * This implements a guestbook application, including the ability to add comments and notification
 * on new entries via e-mail.
 *
 * @package    yana
 * @subpackage plugins
 */
class GuestbookPlugin extends \Yana\Plugins\AbstractPlugin
{

    /**
     * @var string (readonly)
     */
    private $actionEntry = "guestbook_entry";

    /**
     * @var string (readonly)
     */
    private $actionNewWrite = "guestbook_write_new";

    /**
     * @var string (readonly)
     */
    private $actionDelete = "guestbook_write_delete";

    /**
     * @var string (readonly)
     */
    private $actionEdit = "guestbook_read_edit";

    /**
     * @var string (readonly)
     */
    private $actionEditWrite = "guestbook_write_edit";

    /**
     * @var string (readonly)
     */
    private $actionComment = "guestbook_default_comment";

    /** @var string (readonly)
     */
    private $actionCommentWrite = "guestbook_write_comment";

    /**
     * Get database connection.
     *
     * @return  \Yana\Db\IsConnection
     */
    protected function _getDatabase()
    {
        return $this->_connectToDatabase("guestbook");
    }

    /**
     * Constructor
     *
     * @ignore
     */
    public function __construct()
    {
        $YANA = $this->_getApplication();
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
     * @throws      \Yana\Core\Exceptions\NotFoundException  when the selected row does not exist or is not readable
     */
    public function guestbook_read_edit($target)
    {
        $YANA = $this->_getApplication();

        $row = $this->_getDatabase()->select("guestbook.{$target}");

        /* check if target row exists */
        if (empty($row)) {
            $message = "The row guestbook.{$target} was not found.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Core\Exceptions\NotFoundException($message, $level);
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
     * @param       string  $mail        author mail
     * @param       string  $hometown    author location
     * @param       string  $homepage    URL
     * @param       int     $opinion     rating (0..5)
     * @throws      \Yana\Db\Queries\Exceptions\NotUpdatedException  when the entry was not updated
     */
    public function guestbook_write_edit($target, $name, $message, $mail = "", $hometown = "", $homepage = "", $opinion = "")
    {
        $YANA = $this->_getApplication();

        $permission = $YANA->getVar("PERMISSION");
        /* avoid spamming */
        if (!is_int($permission) || $permission < 1) {
            if ($this->_getPluginsFacade()->isActive('antispam') && $YANA->getVar("PROFILE.SPAM.CAPTCHA")) {
                if ($YANA->execute("security_check_image", \Yana\Http\Requests\Builder::buildFromSuperGlobals()->post()->asArrayOfStrings()) === false) {
                    $message = 'CAPTCHA not solved, entry has not been created.';
                    $level = \Yana\Log\TypeEnumeration::DEBUG;
                    \Yana\Log\LogManager::getLogger()->addLog($message, $level);
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    throw new \Yana\Core\Exceptions\Forms\SpamException($message, $level);
                }
            }
        }

        /* values to create new entry */
        $entry = array(
            'GUESTBOOK_NAME' => $name,
            'GUESTBOOK_MESSAGE' => $message,
            'GUESTBOOK_MAIL' => $mail,
            'GUESTBOOK_HOMETOWN' => $hometown,
            'GUESTBOOK_HOMEPAGE' => $homepage,
            'GUESTBOOK_OPINION' => $opinion
        );

        $database = $this->_getDatabase();

        /* before doing anything, check if entry exists */
        if (!$database->exists("guestbook.{$target}")) {
            /* error - no such entry */
            $message = "The selected entry guestbook.{$target} does not exist!";
            $level = \Yana\Log\TypeEnumeration::INFO;
            \Yana\Log\LogManager::getLogger()->addLog($message, $level);
            throw new \Yana\Db\Queries\Exceptions\NotUpdatedException($message, $level);
        }

        /**
         * If the update operation was not successful, issue an error
         * message and abort. (will also forfeit all previously made,
         * uncommited changes)
         */
        try {
            $database->update("guestbook.{$target}", $entry)
                ->commit(); // may throw exception
        } catch (\Exception $e) {
            $message = "The entry guestbook.{$target} could not be updated!";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\NotUpdatedException($message, $level, $e);
        }
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
     * @throws      \Yana\Db\Queries\Exceptions\NotDeletedException       when the entry could not be deleted
     * @throws      \Yana\Core\Exceptions\Forms\NothingSelectedException  when the list of entries to delete is empty
     */
    public function guestbook_write_delete(array $selected_entries)
    {
        /* check if input exists */
        if (empty($selected_entries)) {
            $message = "No entry selected for deletion.";
            $level = \Yana\Log\TypeEnumeration::INFO;
            throw new \Yana\Core\Exceptions\Forms\NothingSelectedException($message, $level);
        }
        $database = $this->_getDatabase();
        /* loop through selected entries */
        foreach($selected_entries as $id)
        {
            $id = mb_strtolower($id);

            /**
             * 1) If the entry to delete does not exist,
             *    issue an error message and abort. (will also forfeit all
             *    previously made, uncommited changes)
             */
            if (!$database->exists("guestbook.{$id}")) {
                $message = "The selected entry guestbook.{$id} could not be deleted because it does not exist.";
                $level = \Yana\Log\TypeEnumeration::INFO;
                throw new \Yana\Db\Queries\Exceptions\NotDeletedException($message, $level);
            }

            /*
             * 2) If the delete operation was not successful, issue an error
             *    message and abort. (will also forfeit all previously made,
             *    uncommited changes)
             */
            try {
                $database->remove("guestbook.{$id}");
            } catch (\Exception $e) {
                $message = "The selected entry guestbook.{$id} could not be deleted.";
                $level = \Yana\Log\TypeEnumeration::WARNING;
                throw new \Yana\Db\Queries\Exceptions\NotDeletedException($message, $level, $e);
            }
        } /* end foreach */
        /* now delete those entries */
        try {
            $database->commit(); // may throw exception
        } catch (\Exception $e) {
            $message = "Unable to commit changes.";
            $level = \Yana\Log\TypeEnumeration::WARNING;
            throw new \Yana\Db\Queries\Exceptions\NotDeletedException($message, $level, $e);
        }
        
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
        $YANA = $this->_getApplication();
        $this->_securityCheck(); // throws \Yana\Core\Exceptions\Files\NotFoundException

        /* get entries */
        $rows = $this->_getTable();
        assert('is_array($rows); /* unexpected result: $rows */');

        /* create RSS feed */
        $rss = new \Yana\RSS\Feed($YANA->getLanguage()->getVar('RSS_DESCRIPTION'));
        if ($rss->getDescription() == "") {
            $rss->setDescription('the 10 most recent guestbook entries');
        }
        $urlFormatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();
        $textFormatter = new \Yana\Views\Helpers\Formatters\TextFormatterCollection();
        foreach ($rows as $row)
        {
            $item = new \Yana\RSS\Item($row['GUESTBOOK_NAME']);
            // process link
            $id = $row['GUESTBOOK_ID'];
            $link = $urlFormatter->__invoke('action=guestbook_read&target=' . $id, true);
            /**
             * {@internal
             * Note: guid is ignored by some aggregators and link is used instead
             * where this is the case, the session id can not be part of the uri as this
             * is subject to change. }}
             */
            $link = str_replace(session_name()."=".session_id(), '', $link);
            $item->setLink($link);
            // process description
            $description = $row['GUESTBOOK_MESSAGE'];
            $description = $textFormatter($description);
            $description = strip_tags($description);
            if (mb_strlen($description) > 500) {
                $description = mb_substr($description, 0, 496).' ...';
            }
            $item->setDescription($description);
            // process pubDate
            if (is_numeric($row['GUESTBOOK_DATE'])) {
                $item->setPubDate(date('r', $row['GUESTBOOK_DATE']));
            }
            $rss->addItem($item);
        } // end foreach
        print (string) $rss;
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
     * @throws      \Yana\Core\Exceptions\Forms\FloodException         when user sent too many posts in a row
     * @throws      \Yana\Core\Exceptions\Forms\AlreadySavedException  when user submits the same text twice
     * @throws      \Yana\Db\Queries\Exceptions\NotCreatedException    when the entry was not created
     */
    public function guestbook_write_new($name, $message, $msgtyp, $messenger = "", $mail = "", $hometown = "", $homepage = "", $opinion = "")
    {
        /* @var $YANA \Yana\Application */
        $YANA = $this->_getApplication();
        $this->_securityCheck(); // throws \Yana\Core\Exceptions\Files\NotFoundException

        $permission = $YANA->getVar("PERMISSION");
        /* avoid spamming */
        if (!is_int($permission) || $permission < 1) {
            if ($this->_getPluginsFacade()->isActive('antispam') && $YANA->getVar("PROFILE.SPAM.CAPTCHA")) {
                if ($YANA->execute("security_check_image", \Yana\Http\Requests\Builder::buildFromSuperGlobals()->post()->asArrayOfStrings()) === false) {
                    $message = 'CAPTCHA not solved, entry has not been created.';
                    $level = \Yana\Log\TypeEnumeration::DEBUG;
                    \Yana\Log\LogManager::getLogger()->addLog($message, $level);
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    throw new \Yana\Core\Exceptions\Forms\SpamException($message, $level);
                }
            }
        }

        $database = $this->_getDatabase();
        /* values to create new entry */
        $entry = array(
            'guestbook_name' => $name,
            'guestbook_message' => $message,
            'guestbook_mail' => $mail,
            'guestbook_hometown' => $hometown,
            'guestbook_homepage' => $homepage,
            'guestbook_opinion' => $opinion
        );

        /* set profile id (overwrite if already exists */
        $entry['profile_id'] = $YANA->getProfileId();

        /* mark registered users */
        if (strcasecmp($this->_getSession()->getCurrentUserName(), $name) === 0) {
            $entry['guestbook_is_registered'] = 1;
        }

        /* avoid flooding
         *
         * return error if entry with same content already exists
         */
        $myFlood = $YANA->getPlugins()->{"guestbook:/my.flood"};
        $myFlood->setMax((int)$YANA->getVar("PROFILE.GUESTBOOK.FLOODING"));
        if ($myFlood->isBlocked()) {
            $message = 'Possibly flooding attempt detected. Request rejected.';
            $code = \Yana\Log\TypeEnumeration::WARNING;
            \Yana\Log\LogManager::getLogger()->addLog($message, $code);
            throw new \Yana\Core\Exceptions\Forms\FloodException($message, $code);
        }
        assert('!isset($where); // Cannot redeclare var $where');
        $where = array('profile_id', '=', $YANA->getProfileId());
        $recent_entry = $database->select("guestbook.?.guestbook_message", $where);
        unset($where);
        if (!empty($entry['guestbook_message']) && $recent_entry == $entry['guestbook_message']) {
            $message = "Duplicate entry. The same message was submit twice.";
            $level = \Yana\Log\TypeEnumeration::INFO;
            throw new \Yana\Core\Exceptions\Forms\AlreadySavedException($message, $level);
        }
        if ($myFlood->getMax() > 0) {
            $myFlood->set();
        }
        /* insert new entry into table */
        try {
            $database->insert('guestbook.*', $entry)
                ->commit(); // may throw exception
        }
        catch (\Exception $e) {
            $message = 'Failed to insert entry.';
            $level = \Yana\Log\TypeEnumeration::ERROR;
            \Yana\Log\LogManager::getLogger()->addLog($message, $level, $entry);
            throw new \Yana\Db\Queries\Exceptions\NotCreatedException($message, $level, $e);
        }
        /* send Mail */
        if ($YANA->getVar("PROFILE.GUESTBOOK.MAIL") && $YANA->getVar("PROFILE.GUESTBOOK.NOTIFICATION")) {
            $templateFile = $YANA->getPlugins()->{"guestbook:/notification.file"};
            $template = $YANA->getView()->createContentTemplate($templateFile->getPath());
            $this->_sendMail($template, $entry);
        }
    }

    /**
     * display single guestbook entry
     *
     * @type        read
     * @template    templates/entry.html.tpl
     * @language    guestbook
     * @style       templates/default.css
     * @onerror     goto: null, text: Yana\Core\Exceptions\InvalidInputException
     *
     * @access      public
     * @param       int  $target  id of entry to show
     * @return      bool
     */
    public function guestbook_entry($target)
    {
        $YANA = $this->_getApplication();
        $this->_securityCheck(); // throws \Yana\Core\Exceptions\Files\NotFoundException

        // create link to user profile (if profile viewer is installed)
        if ($YANA->getPlugins()->isActive('user_admin')) {
            $urlFormatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();
            $YANA->setVar('GUESTBOOK_USER_LINK', $urlFormatter("action=view_profile&target[user_id]="));
        } else {
            $YANA->setVar('GUESTBOOK_USER_LINK', false);
        }

        /* get entries */
        $row = $this->_getDatabase()->select("guestbook.$target");
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
     * @onerror     goto: null, text: Yana\Core\Exceptions\InvalidInputException
     * @menu        group: start
     *
     * @access      public
     * @param       int  $page     current page number (if there are more)
     * @param       int  $entries  number of entries per page
     */
    public function guestbook_read($page = 0, $entries = null)
    {
        $YANA = $this->_getApplication();
        $this->_securityCheck(); // throws \Yana\Core\Exceptions\Files\NotFoundException

        \Yana\RSS\Publisher::publishFeed('guestbook_read_rss');

        /* get entries */
        $rows = $this->_getTable($page, $entries);
        assert('is_array($rows); /* unexpected result: $rows */');

        // create link to user profile (if profile viewer is installed)
        if ($YANA->getPlugins()->isActive('user_admin')) {
            $urlFormatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();
            $YANA->setVar('GUESTBOOK_USER_LINK', $urlFormatter("action=view_profile&target[user_id]="));
        } else {
            $YANA->setVar('GUESTBOOK_USER_LINK', false);
        }
        $YANA->setVar('ROWS', $rows);
        $YANA->setVar('DESCRIPTION', $YANA->getLanguage()->getVar('descr_show'));
        $useCaptcha = $this->_getPluginsFacade()->isActive('antispam') && $YANA->getVar("PROFILE.SPAM.CAPTCHA") &&
            !$this->_getSecurityFacade()->loadUser()->isLoggedIn();
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
        $YANA = $this->_getApplication();
        $guestbook_comment = $this->_getDatabase()->select('guestbook.'.$target.'.guestbook_comment');
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
     * @throws      \Yana\Db\Queries\Exceptions\NotCreatedException  when the entry was not created
     */
    public function guestbook_write_comment($target, $guestbook_comment = "")
    {
        $database = $this->_getDatabase();
        // If the update operation was not successful, issue an error message and abort.
        try {
            $database->update("guestbook.{$target}.guestbook_comment", $guestbook_comment)
                ->commit(); // may throw exception
        }
        catch (\Exception $e) {
            $message = "Unable to insert comment at 'guestbook.{$target}.'";
            $level = \Yana\Log\TypeEnumeration::ERROR;
            \Yana\Log\LogManager::getLogger()->addLog($message, $level, array('guestbook_comment' => $guestbook_comment));
            throw new \Yana\Db\Queries\Exceptions\NotCreatedException($message, $level, $e);
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
        $YANA = $this->_getApplication();

        $database = $this->_getDatabase();
        /* check if $table really is a table */
        $table = $database->getSchema()->getTable("guestbook");
        assert('$table instanceof \Yana\Db\Ddl\Table;');

        /* get the name of the primary key */
        $primary_key = $table->getPrimaryKey();
        assert('is_string($primary_key); /* unexpected result: $primary_key */');

        if (empty($entPerPage)) {
            $entPerPage = (int) $YANA->getVar("PROFILE.GUESTBOOK.ENTPERPAGE");
        }
        if ($entPerPage < 1) {
            $entPerPage = 10;
        }

        $where = array("profile_id", "=", $YANA->getProfileId());
        $sortBy = "guestbook_date";
        $desc = true;
        /* get rows from database */
        $result = $database->select($table->getName(), $where, array($sortBy), $page, $entPerPage, array($desc));

        /* set template vars */

        $YANA->setVar('TABLE', $table->getName()); /* table name */
        $YANA->setVar('PRIMARY_KEY', $primary_key); /* name of primary key column */
        $last_page = $database->length($table->getName(), $where); /* total number of rows */
        $YANA->setVar('LAST_PAGE', $last_page);
        $YANA->setVar('SORT', $sortBy); /* column name */

        /* create a footer with links to other pages of the resultset */
        $listOfEntries = array();
        if ($last_page > ($entPerPage * 2)) {
            $dots = false;
            for($k = 0; $k < ceil($last_page / $entPerPage); $k++)
            {
                $it   = count($listOfEntries);
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
                        $listOfEntries[$it]['TOO_MANY'] = true;
                    }
                } else {
                    $listOfEntries[$it]['FIRST'] = ($first + 1);
                    $listOfEntries[$it]['LAST']  = $last;
                    if ($first > $page-1 && $last < $page + $entPerPage + 1) {
                        $dots = false;
                    }
                }
            } // end foreach

        } // end if
        /* provide result to template */
        $YANA->setVar('LIST_OF_ENTRIES', $listOfEntries);

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
     * @throws  \Yana\Core\Exceptions\Files\NotFoundException
     */
    private function _securityCheck()
    {
        $YANA = $this->_getApplication();
        $id = $YANA->getProfileId();
        /* do not show new guestbooks, if Auto-Option is deactivated */
        $dir = $YANA->getResource('system:/config/profiledir');
        $file = new \Yana\Files\Readonly($dir->getPath() . $id . '.cfg');
        if ($id !== 'default' && !$file->exists() && !$YANA->getVar("PROFILE.AUTO")) {
            $message = "Access restriction in effect. Access to undefined profile {$id} denied.";
            $code = \Yana\Log\TypeEnumeration::WARNING;
            \Yana\Log\LogManager::getLogger()->addLog($message);
            throw new \Yana\Core\Exceptions\Files\NotFoundException($message, $code);
        }
    }

    /**
     * send mail
     *
     * @param   \Yana\Views\Templates\IsTemplate  $template  mail template
     * @param   array                             $vars      input data
     */
    private function _sendMail(\Yana\Views\Templates\IsTemplate $template, array $vars)
    {
        $YANA = $this->_getApplication();
        $sender = $YANA->getVar("PROFILE.MAIL");
        $recipient = $YANA->getVar("PROFILE.GUESTBOOK.MAIL");

        if (filter_var($recipient, FILTER_VALIDATE_EMAIL) && filter_var($sender, FILTER_VALIDATE_EMAIL)) {

            $templateMailer = new \Yana\Mails\TemplateMailer($template);
            $subject = $YANA->getLanguage()->getVar("mail_subject");
            $vars = \Yana\Util\Hashtable::changeCase($vars, CASE_UPPER);
            $vars['DATE'] = date('d-m-Y');
            $headers = array('from' => $sender);
            $templateMailer->send($recipient, $subject, $vars, $headers);
        }
    }

}

?>