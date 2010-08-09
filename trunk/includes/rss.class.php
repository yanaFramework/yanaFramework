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

/**
 * <<entity>> RSS feed
 *
 * This class represents a RSS feed.
 * It offers functionality to publish new feeds.
 *
 * Examples of usage:
 * <ol>
 *  <li> Creating and printing a RSS feed inside a plugin:
 *       <code>
 *  function rss_foobar($args)
 *  {
 *       // create rss feed
 *       $rss = new RSS();
 *       // provide some description for it
 *       $rss->title = 'foo';
 *       $rss->description = 'this is my RSS feed';
 *       // create 1st item
 *       $item = new RSSitem();
 *       $item->title = '1st entry';
 *       $item->link = 'http://some.url';
 *       $item->description = 'some text';
 *       $rss->addItem($item);
 *       // create 2nd item
 *       $item = new RSSitem();
 *       $item->title = '2nd entry';
 *       $item->link = 'http://some.other.url';
 *       $item->description = 'some text';
 *       $rss->addItem($item);
 *       // print rss feed
 *       print $rss->toString();
 *       // when you're done, terminate the program
 *       exit(0);
 *  }
 *  </code>
 *  </li>
 *  <li> Showing the RSS logo + link from inside a plugin:
 *       <code>RSS::publishFeed('rss_foobar')</code>
 *       Where 'rss_foobar' is the value for the "action" parameter of the
 *       URL. You don't need to call it 'rss_foobar' - just call it whatever
 *       you like.
 *       You are to implement the code for action 'rss_foobar' yourself.
 *       To find out how to do that, see the example above.
 *  </li>
 * </ol>
 *
 * @access      public
 * @package     yana
 * @subpackage  utilities
 */
class RSS extends Object
{
    /**
     * list of published RSS-feeds
     *
     * @access  protected
     * @static
     * @var     array
     * @ignore
     */
    protected static $feeds = array();

    /**#@+
     * this field is mandatory
     *
     * @access  public
     */

    /**
     * name of the channel, e.g. title of the website the channel refers to
     * @var string */ public $title = "";
    /**
     * a link that points to the originating website of the feed
     * @var string */ public $link = "";
    /**
     * a text to describe context and purpose of this channel
     * @var string */ public $description = "";

    /**#@-*/
    /**#@+
     * this field is optional
     *
     * @access  public
     */

    /**
     * language the channel is written in
     * @var string */ public $language = "";
    /**
     * copyright notice of your choice
     * @var string */ public $copyright = "";
    /**
     * mail of person responsible for editorial content
     * @var string */ public $managingEditor = "";
    /** 
     * mail of person responsible for technical issues
     * @var string */ public $webMaster = "";
    /**
     * "time to live" - a number of minutes that indicates how long a channel can be cached before
     *                  refreshing from the source
     * @var int    */ public $ttl = 0;
    /**
     * *.jpg, *.gif, or *.png image to display with the feed
     * @var string */ public $image = "";
    /**
     * one or more categories that the channel belongs to
     * @var array  */ public $category = array();
    /**
     * a CSS stylesheet that formats the channel
     * @var string */ public $css = "";
    /**
     * a XSL transformation that formats the channel
     * @var string */ public $xslt = "";
    /**
     * list of one or more items of the channel
     * @var array  */ public $item = array();

    /**#@-*/
    /**#@+
     * @ignore
     * @access  private
     */

    /**
     * date when the channel was published (auto-generated)
     * @var string */ private $pubDate = "";
    /**
     * pubDate of most recent item (auto-generated)
     * @var string */ private $lastBuildDate = "";
    /**
     * a string to identify the class / program that created the file
     * @var string */ private $generator = "";

    /**#@-*/
    
    /**
     * constructor
     *
     * creates and initializes a new instance of this class
     */
    public function __construct()
    {
        $language = Language::getInstance();
        $this->title = $language->getVar('program_title');
        $this->generator = "Yana Framework ".YANA_VERSION;
        $this->item = array();
        if (isset($_SESSION['language'])) {
            $this->language = $_SESSION['language'];
        }
        $this->link = Request::getUri();
    }

    /**
     * get string value
     *
     * This returns this object as a string in RSS 2.0 syntax.
     *
     * Returns bool(false) and issues an E_USER_NOTICE on error.
     *
     * @access  public
     * @return  bool(false)|string
     *
     * @name    RSS::toString()
     */
    public function toString()
    {
        assert('is_string($this->title) && !empty($this->title); // '.
            'Unable to create RSS. Mandatory field "title" missing.');
        assert('is_string($this->link) && !empty($this->link); // '.
            'Unable to create RSS. Mandatory field "link" missing.');
        assert('is_string($this->description) && !empty($this->description); // '.
            'Unable to create RSS. Mandatory field "description" missing.');
        assert('is_string($this->language); // Unable to create RSS. Field "language" must be a string.');
        assert('is_string($this->copyright); // Unable to create RSS. Field "copyright" must be a string.');
        assert('is_string($this->image); // Unable to create RSS. Field "image" must be a string.');
        assert('is_int($this->ttl); // Unable to create RSS. Field "copyright" must be an integer.');
        assert('is_string($this->managingEditor); // Unable to create RSS. Field "managingEditor" must be a string.');
        assert('is_string($this->webMaster); // Unable to create RSS. Field "webMaster" must be a string.');
        assert('is_string($this->css); // Unable to create RSS. Field "css" must be a string.');
        assert('is_string($this->xslt); // Unable to create RSS. Field "xslt" must be a string.');
        assert('is_array($this->item); // Unable to create RSS. Field "item" must be a array.');
        
        /* check optional fields */
        if (empty($this->title) || empty($this->link) || empty($this->description)) {
            trigger_error("Unable to create RSS. Mandatory field missing.", E_USER_NOTICE);
            return false;
        }
        if (!preg_match('/^(?:|\w{2}(?:-\w{2})?)$/s', $this->language)) {
            $message = "Unable to create RSS. Invalid 'language' value.\n\t\t".
                "Use following syntax: 'xx-xx', e.g. 'en', 'en-us', 'de', 'de-de', 'de-at' aso.";
            trigger_error($message, E_USER_NOTICE);
            return false;
        }
        $tmp = $this->managingEditor;
        $this->managingEditor = filter_var($this->managingEditor, FILTER_SANITIZE_EMAIL);
        if ($tmp != $this->managingEditor) {
            $message = "RSS item has been auto-converted.\n\t\tThe input value '".
                $tmp."' did not seem to be valid.\n\t\tThe value has been changed to '".$this->managingEditor."'.";
            trigger_error($message, E_USER_NOTICE);
        }
        $tmp = $this->webMaster;
        $this->webMaster = filter_var($this->webMaster, FILTER_SANITIZE_EMAIL);
        if ($tmp != $this->webMaster) {
            $message = "RSS item has been auto-converted.\n\t\tThe input value '".
                $tmp."' did not seem to be valid.\n\t\tThe value has been changed to '".$this->webMaster."'.";
            trigger_error($message, E_USER_NOTICE);
        }

        /* auto-fill fields, that can be generated */
        $this->pubDate = date('r', time());
        if (is_array($this->item) && count($this->item) > 0) {
            $time = 0;
            foreach ($this->item as $item)
            {
                if (!empty($item['pubDate'])) {
                    $tmp = strtotime($item['pubDate']);
                    if (is_int($tmp) && $tmp > $time) {
                        $time = $tmp;
                    }
                }
            } /* end foreach */
            if ($time > 0) {
                $this->lastBuildDate = date('r', $time);
            } else {
                $this->lastBuildDate = "";
            }
        }

        /* publish data */
        $tpl = new SmartTemplate('id:RSS_TEMPLATE');
        $tpl->setVar('rss', get_object_vars($this));
        return $tpl->toString();
    }

    /**
     * add RSS feed item to this channel
     *
     * The $item has to be a valid RSSitem object.
     *
     * Returns bool(true) on success and bool(false) on error.
     *
     * Note: this function does not check for duplicate guid's
     *
     * @access  public
     * @param   RSSitem  $item  new RSSitem
     * @return  bool
     */
    public function addItem(RSSitem $item)
    {
        assert('is_array($this->item);');
        $item = $item->get();
        assert('is_array($item);');
        if (empty($item)) {
            return false;
        }
        $this->item[] = $item;
        return true;
    }

    /**
     * publish a rss feed
     *
     * Adds the action identified by $action to the list of rss-feeds
     * to be offered to the user.
     * You should define a function with the name of $action in your plugin,
     * that must produce the RSS content.
     *
     * Example of usage:
     *
     * Add this to your plugin code
     * <code>RSS::publishFeed('create_rss');</code>
     * Where 'create_rss' is the name of the plugin function
     * that displays the rss feed.
     *
     * @access  public
     * @static
     * @param   string  $action  action
     */
    public static function publishFeed($action)
    {
        assert('is_string($action); // Wrong argument type argument 1. String expected');

        self::$feeds[] = "$action";
        array_unique(self::$feeds);
    }

    /**
     * get list of RSS-feeds
     *
     * Returns a list of all previously published RSS-feeds.
     *
     * @access  public
     * @static
     * @return  array
     */
    public static function getFeeds()
    {
        assert('is_array(self::$feeds); // Member "feeds" should be an array.');
        return self::$feeds;
    }
}

?>