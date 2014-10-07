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
class plugin_blog extends StdClass implements \Yana\IsPlugin
{
    /**
     * Connection to data source (API)
     *
     * @access  private
     * @static
     * @var     \Yana\Db\IsConnection
     */
    private static $database = null;

    /**
     * get database connection
     *
     * @access  protected
     * @static
     * @return  \Yana\Db\IsConnection
     */
    protected static function getDatabase()
    {
        if (!isset(self::$database)) {
            self::$database = \Yana\Application::connect("blog");
        }
        return self::$database;
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  FormFacade
     */
    protected static function getBlogForm()
    {
        $builder = new \Yana\Forms\Builder('blog');
        return $builder->setId('blog')->__invoke();
    }

    /**
     * get form definition
     *
     * @access  protected
     * @static
     * @return  FormFacade
     */
    protected static function getCommentForm()
    {
        $form = self::getBlogForm();
        return $form->getForm('blogcmt');
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
    public function catchAll($event, array $ARGS)
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
     */
    public function blog()
    {
        \Yana\RSS\Publisher::publishFeed('blog_rss');
        \Yana\Util\Microsummary::publishSummary(__CLASS__);
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
        return $this->blog();
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
        $form = self::getBlogForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        return $worker->update();
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
        $form = self::getBlogForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        return $worker->delete($selected_entries);
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
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        $success = (bool) $worker->create();
        if ($success) {
            \Yana\Util\Microsummary::setText(__CLASS__, 'Blog, update '.date('d M y G:s', time()));
        }
        return $success;
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
            if (\Yana\Plugins\Manager::getInstance()->isActive('antispam') && $YANA->getVar("PROFILE.SPAM.CAPTCHA")) {
                if ($YANA->callAction("security_check_image", $ARGS) === false) {
                    $message = 'CAPTCHA not solved, entry has not been created.';
                    $level = \Yana\Log\TypeEnumeration::DEBUG;
                    \Yana\Log\LogManager::getLogger()->addLog($message, $level);
                    $level = \Yana\Log\TypeEnumeration::WARNING;
                    throw new \Yana\Core\Exceptions\Forms\SpamException($message, $level);
                }
            }
        }
        $form = self::getCommentForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        return $worker->create();
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
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        return $worker->update();
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
        $form = self::getCommentForm();
        $worker = new \Yana\Forms\Worker(self::getDatabase(), $form);
        return $worker->delete($selected_entries);
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
        $rss = new \Yana\RSS\Feed();
        $rss->description = $YANA->getLanguage()->getVar('RSS_DESCRIPTION');
        $urlFormatter = new \Yana\Views\Helpers\Formatters\UrlFormatter();
        $textFormatter = new \Yana\Views\Helpers\Formatters\TextFormatterCollection();
        /*
         * add items to feed
         */
        foreach ($rows as $row)
        {
            $item = new \Yana\RSS\Item($row['BLOG_TITLE']);
            // process link
            $id = $row['BLOG_ID'];
            $link = $urlFormatter("action=blog_read_read_seperated_blog&blog_id=$id", true);
            $link = str_replace(session_name()."=".session_id(), '', $link);
            $item->setLink($link);
            // process description
            $description = $row['BLOG_TEXT'];
            $description = $textFormatter($description);
            $description = strip_tags($description);
            if (mb_strlen($description) > 500) {
                $description = mb_substr($description, 0, 496).' ...';
            }
            $item->setDescription($description);
            // process pubDate
            if (is_numeric($row['BLOG_CREATED'])) {
                $item->setPubDate(date('r', $row['BLOG_CREATED']));
            }
            $rss->addItem($item);
        } // end foreach
        print (string) $rss;
        exit(0);
    }

}

?>