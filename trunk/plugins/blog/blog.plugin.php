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

namespace Plugins\Blog;

/**
 * <<plugin>> class "plugin_blog"
 *
 * @package     yana
 * @subpackage  plugins
 */
class BlogPlugin extends \Yana\Plugins\AbstractPlugin
{
    /**
     * Connection to data source (API)
     *
     * @var     \Yana\Db\IsConnection
     */
    private static $database = null;

    /**
     * get database connection
     *
     * @return  \Yana\Db\IsConnection
     */
    protected function _getDatabase()
    {
        if (!isset(self::$database)) {
            self::$database = $this->_connectToDatabase("blog");
        }
        return self::$database;
    }

    /**
     * get form definition
     *
     * @return  \Yana\Forms\Facade
     */
    protected function _getBlogForm()
    {
        $builder = $this->_getApplication()->buildForm('blog');
        return $builder->__invoke();
    }

    /**
     * get form definition
     *
     * @return  FormFacade
     */
    protected function _getCommentForm()
    {
        $form = $this->_getBlogForm();
        return $form->getForm('blogcmt');
    }

    /**
     * Provide edit-form.
     *
     * @type        read
     * @menu        group: start
     * @template    templates/blog.html.tpl
     * @language    blog
     */
    public function blog()
    {
        \Yana\RSS\Publisher::publishFeed('blog_rss');
    }

    /**
     * Process search query.
     *
     * @type        read
     * @user        group: blog, role: moderator
     * @user        group: admin, level: 75
     * @template    templates/blog.html.tpl
     * @language    blog
     *
     * @return  bool
     */
    public function blog_search_blog()
    {
        return $this->blog();
    }

    /**
     * Save changes made in edit-form.
     *
     * @type        write
     * @user        group: blog, role: moderator
     * @user        group: admin, level: 75
     * @template    MESSAGE
     * @language    blog
     * @onsuccess   goto: blog
     * @onerror     goto: blog
     *
     * @return  bool
     * @name    plugin_blog::blog_write_edit_blog()
     */
    public function blog_edit_blog()
    {
        $form = $this->_getBlogForm();
        $worker = new \Yana\Forms\Worker($this->_getDatabase(), $form);
        return $worker->update();
    }

    /**
     * Delete a blog-entry.
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
     * @param   array  $selected_entries  array of entries to delete
     * @return  bool
     */
    public function blog_delete_blog(array $selected_entries)
    {
        $form = $this->_getBlogForm();
        $worker = new \Yana\Forms\Worker($this->_getDatabase(), $form);
        return $worker->delete($selected_entries);
    }

    /**
     * Write new blog-entry to database.
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
     * @return  bool
     */
    public function blog_new_blog()
    {
        $form = $this->_getBlogForm();
        $worker = new \Yana\Forms\Worker($this->_getDatabase(), $form);
        return (bool) $worker->create();
    }

    /**
     * Write new blog-comment to database.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type        write
     * @template    MESSAGE
     * @language    blog
     * @onsuccess   goto: blog
     * @onerror     goto: blog
     *
     * @param   array  $ARGS  array of params passed to the function
     * @return  bool
     */
    public function blog_new_blogcmt (array $ARGS)
    {
        $form = $this->_getCommentForm();
        $worker = new \Yana\Forms\Worker($this->_getDatabase(), $form);
        return $worker->create();
    }

    /**
     * Update a blog-comment in database.
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
     * @return  bool
     */
    public function blog_edit_blogcmt()
    {
        $form = $this->_getCommentForm();
        $worker = new \Yana\Forms\Worker($this->_getDatabase(), $form);
        return $worker->update();
    }

    /**
     * Remove blog-comment from database.
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
     * @param   array  $selected_entries  list of entries to delete
     * @return  bool
     */
    public function blog_delete_blogcmt (array $selected_entries)
    {
        $form = $this->_getCommentForm();
        $worker = new \Yana\Forms\Worker($this->_getDatabase(), $form);
        return $worker->delete($selected_entries);
    }

    /**
     * Produce RSS-feed.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * @type        read
     * @template    NULL
     * @language    blog
     *
     * @return  bool
     * @name    plugin_blog::blog_rss()
     */
    public function blog_rss ()
    {
        $YANA = $this->_getApplication();
        /* get entries from database */
        $key = 'blog';
        $where = array();
        $orderBy = 'blog_created';
        $offset = 0;
        $limit = 10;
        $desc = true;
        $database = $this->_getDatabase();
        $rows = $database->select($key, $where, $orderBy, $offset, $limit, $desc);
        /*
         * create RSS feed
         */
        $lang = $YANA->getLanguage();
        $rss = new \Yana\RSS\Feed(
            (string) $lang->getVar('RSS_DESCRIPTION'),
            (string) $lang->getVar('program_title'),
            (string) $lang->getLocale()
        );
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