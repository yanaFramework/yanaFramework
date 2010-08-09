<?php
/**
 * Blog
 *
 * This is a plugin made to write blogs.
 *
 * {@translation
 *
 *    de: Blog
 *
 *        Dies ist ein Plugin zum Schreiben von Weblogs.
 * }
 *
 * @type        primary
 * @author      Thomas Meyer
 * @license     http://www.gnu.org/licenses/gpl.txt
 * @menu        group: blog, title: Blog
 * @menu        group: blog.view, title: {lang id="menu.view"}
 * @menu        group: blog.edit, title: {lang id="menu.edit"}
 * @group       blog
 * @package     yana
 * @subpackage  plugins
 */

/**
 * <<plugin>> class "plugin_blog"
 *
 * @access      public
 * @package     yana
 * @subpackage  plugins
 */
class plugin_blog extends StdClass implements IsPlugin
{
    /**
     * Connection to data source (API)
     *
     * @access  private
     * @static
     * @var     DbStream
     */
    private static $database = null;

    /**
     * Form definition for blog entries
     *
     * @access  private
     * @static
     * @var     DDLDefaultForm
     */
    private static $blogForm = null;

    /**
     * Form definition for blog comments
     *
     * @access  private
     * @static
     * @var     DDLDefaultForm
     */
    private static $commentForm = null;

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
            self::$database = Yana::connect("blog");
        }
        return self::$database;
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  DDLDefaultForm
     */
    protected static function getBlogForm()
    {
        if (!isset(self::$blogForm)) {
            $database = self::getDatabase();
            self::$blogForm = $database->schema->getForm("blog");
        }
        return self::$blogForm;
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  DDLDefaultForm
     */
    protected static function getCommentForm()
    {
        if (!isset(self::$commentForm)) {
            $form = self::getBlogForm();
            self::$commentForm = $form->getForm("blogcmt");
        }
        return self::$commentForm;
    }

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
    public function _default($event, array $ARGS)
    {
        return true;
    }

    /**
     * provide edit-form
     *
     * @type        read
     * @menu        group: start
     * @template    templates/blog.html.tpl
     * @language    blog
     *
     * @access  public
     * @return  bool
     */
    public function blog()
    {
        RSS::publishFeed('blog_rss');
        Microsummary::publishSummary(__CLASS__);
        return true;
    }

    /**
     * process search query
     *
     * @type        read
     * @user        group: blog, role: moderator
     * @user        group: admin, level: 75
     * @template    templates/blog.html.tpl
     * @language    blog
     *
     * @access  public
     * @return  bool
     */
    public function blog_search_blog()
    {
        if (!$this->blog()) {
            return false;
        }
        $form = $this->getBlogForm();
        $where = $form->getSearchValuesAsWhereClause();
        if (!is_null($where)) {
            $form->getQuery()->setHaving($where);
        }
        return true;
    }

    /**
     * save changes made in edit-form
     *
     * @type        write
     * @user        group: blog, role: moderator
     * @user        group: admin, level: 75
     * @template    MESSAGE
     * @language    blog
     * @onsuccess   goto: blog
     * @onerror     goto: blog
     *
     * @access  public
     * @return  bool
     * @name    plugin_blog::blog_write_edit_blog()
     */
    public function blog_edit_blog()
    {
        $database = self::getDatabase();
        $updatedEntries = self::getBlogForm()->getUpdateValues();

        /* no data has been provided */
        if (empty($updatedEntries)) {
            throw new InvalidInputWarning();
        }

        foreach ($updatedEntries as $id => $entry)
        {
            $id = mb_strtolower($id);

            /* before doing anything, check if entry exists */
            if (!$database->exists("blog.${id}")) {

                /* error - no such entry */
                throw new InvalidInputWarning();

            /* update the row */
            } elseif (!$database->update("blog.${id}", $entry)) {
                /* error - unable to perform update - possibly readonly */
                return false;
            }
        } /* end for */
        /* commit changes */
        return $database->write();
    }

    /**
     * delete a blog-entry
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type        write
     * @user        group: blog, role: moderator
     * @user        group: admin, level: 75
     * @template    MESSAGE
     * @language    blog
     * @onsuccess   goto: blog
     * @onerror     goto: blog
     *
     * @access  public
     * @param   array  $selected_entries  array of entries to delete
     * @return  bool
     */
    public function blog_delete_blog(array $selected_entries)
    {
        /* check if user forgot to mark at least 1 row */
        if (empty($selected_entries)) {
            throw new InvalidInputWarning();
        }
        $database = self::getDatabase();
        /* remove entry from database */
        foreach ($selected_entries as $id)
        {
            if (!$database->remove("blog.${id}")) {
                /* entry does not exist */
                throw new InvalidInputWarning();
            }
        } /* end for */
        return $database->commit(); // commit changes
    }

    /**
     * write new blog-entry to database
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type        write
     * @user        group: blog
     * @user        group: admin, level: 30
     * @template    MESSAGE
     * @language    blog
     * @onsuccess   goto: blog
     * @onerror     goto: blog
     *
     * @access  public
     * @return  bool
     */
    public function blog_new_blog()
    {
        $form = self::getBlogForm();
        $newEntry = $form->getInsertValues();

        /* no data has been provided */
        if (empty($newEntry)) {
            throw new InvalidInputWarning();
        }

        $database = self::getDatabase();
        /* insert new entry into table */
        if (!$database->insert("blog.*", $newEntry)) {
            throw new InvalidInputWarning();
        }
        if ($database->write()) {
            Microsummary::setText(__CLASS__, 'Blog, update '.date('d M y G:s', time()));
            return true;
        } else {
            return false;
        }
    }

    /**
     * write new blog-comment to database
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type        write
     * @template    MESSAGE
     * @language    blog
     * @onsuccess   goto: blog
     * @onerror     goto: blog
     *
     * @access  public
     * @param   array  $ARGS  array of params passed to the function
     * @return  bool
     */
    public function blog_new_blogcmt (array $ARGS)
    {
        /* global variables */
        global $YANA;

        /* avoid spamming */
        $permission = $YANA->getVar("PERMISSION");
        if (!is_int($permission) || $permission < 1) {
            if ($YANA->callAction("security_check_image", $ARGS) === false) {
                Log::report('SPAM: CAPTCHA not solved, entry has not been created.');
                throw new SpamError();
            }
        }

        $database = self::getDatabase();
        $form = self::getCommentForm();
        $newEntry = $form->getInsertValues();

        /* no data has been provided */
        if (empty($newEntry)) {
            throw new InvalidInputWarning();
        }

        /* insert new entry into table */
        if (!$database->insert("blogcmt.*", $newEntry)) {
            throw new InvalidInputWarning();
        }
        return $database->write();
    }

    /**
     * update a blog-comment in database
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type        write
     * @user        group: blog, role: moderator
     * @user        group: admin, level: 75
     * @template    MESSAGE
     * @language    blog
     * @onsuccess   goto: blog
     * @onerror     goto: blog
     *
     * @access  public
     * @return  bool
     */
    public function blog_edit_blogcmt()
    {
        $form = self::getCommentForm();
        $updatedEntries = $form->getUpdateValues();

        /* no data has been provided */
        if (empty($updatedEntries)) {
            throw new InvalidInputWarning();
        }

        $database = self::getDatabase();
        foreach ($updatedEntries as $id => $entry)
        {
            $id = mb_strtolower($id);

            /* before doing anything, check if entry exists */
            if (!$database->exists("blogcmt.${id}")) {

                /* error - no such entry */
                throw new InvalidInputWarning();

            /* update the row */
            } else if (!$database->update("blogcmt.${id}", $entry)) {
                /* error - unable to perform update - possibly readonly */
                return false;
            }
        } /* end for */
        /* commit changes */
        return $database->write();
    }

    /**
     * remove blog-comment from database
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type        write
     * @user        group: blog, role: moderator
     * @user        group: admin, level: 75
     * @template    MESSAGE
     * @language    blog
     * @onsuccess   goto: blog
     * @onerror     goto: blog
     *
     * @access  public
     * @param   array  $selected_entries  list of entries to delete
     * @return  bool
     */
    public function blog_delete_blogcmt (array $selected_entries)
    {
        /* check if user forgot to mark at least 1 row */
        if (empty($selected_entries)) {
            throw new InvalidInputWarning();
        }
        $database = self::getDatabase();
        /* remove entry from database */
        foreach ($selected_entries as $id)
        {
            if (!$database->remove("blogcmt.${id}")) {
                /* entry does not exist */
                throw new InvalidInputWarning();
            }
        } /* end for */
        /* commit changes */
        return $database->write();
    }

    /**
     * produce RSS-feed
     *
     * returns bool(true) on success and bool(false) on error
     *
     * @type        read
     * @template    NULL
     * @language    blog
     *
     * @access  public
     * @return  bool
     * @name    plugin_blog::blog_rss()
     */
    public function blog_rss ()
    {
        global $YANA;
        /* get entries from database */
        $key = 'blog';
        $where = array();
        $orderBy = 'blog_created';
        $offset = 0;
        $limit = 10;
        $desc = true;
        $database = self::getDatabase();
        $rows = $database->select($key, $where, $orderBy, $offset, $limit, $desc);
        /*
         * create RSS feed
         */
        $rss = new RSS();
        $rss->description = $YANA->language->getVar('RSS_DESCRIPTION');
        /*
         * add items to feed
         */
        foreach ($rows as $row)
        {
            $item = new RSSitem();
            /* 1) process title */
            $item->title = $row['BLOG_TITLE'];
            /* 2) process link */
            $id = $row['BLOG_ID'];
            $item->link = SmartUtility::url("action=blog_read_read_seperated_blog&blog_id=$id", true);
            $item->link = str_replace(session_name()."=".session_id(), '', $item->link);
            /* 3) process description */
            $item->description = $row['BLOG_TEXT'];
            $item->description = SmartUtility::embeddedTags($item->description);
            $item->description = SmartUtility::smilies($item->description);
            $item->description = strip_tags($item->description);
            if (mb_strlen($item->description) > 500) {
                $item->description = mb_substr($item->description, 0, 496).' ...';
            }
            /* 4) process pubDate */
            $item->pubDate = $row['BLOG_CREATED'];
            if (is_numeric($item->pubDate)) {
                $item->pubDate = date('r', $item->pubDate);
            }
            $rss->addItem($item);
        } // end foreach
        print $rss->toString();
        exit(0);
    }

}
?>