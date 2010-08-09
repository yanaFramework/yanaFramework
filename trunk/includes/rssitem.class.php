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
 * <<entity>> RSS item
 *
 * This class represents an item of an RSS feed.
 *
 * Note: all attributes are optional, but at least one of title or description must be set.
 *
 * @access      public
 * @package     yana
 * @subpackage  utilities
 */
class RSSitem extends Object
{
    /**#@+
     * this field is optional
     *
     * @access  public
     */

    /**
     * title of the item
     * @var string */ public $title = "";
    /**
     * URL of the item
     * @var string */ public $link = "";
    /**
     * item synopsis, note that this may contain HTML
     * @var string */ public $description = "";
    /**
     * Email address of the author
     * @var string */ public $author = "";
    /**
     * include item in one or more categories
     * @var array  */ public $category = array();
    /**
     * URL of a page for comments
     * @var string */ public $comments = "";
    /**
     * indicates when the item was published (auto-generated if not provided)
     * @var string */ public $pubDate = "";

    /**#@-*/
    /**#@+
     * this field is optional
     *
     * @access  private
     */

    /**
     * unique identity (always auto-generated)
     * @var string */ private $guid = "";

    /**#@-*/

    /**
     * constructor
     *
     * creates and initializes a new instance of this class
     *
     */
    public function __construct()
    {
        $this->pubDate = date('r', time());
    }
    
    /**
     * return item as associative array
     *
     * This checks the values of all attributes, creates
     * a unique id for this item, and returns
     * an associative array of all object vars.
     *
     * Returns an empty array and issues an E_USER_NOTICE on error.
     *
     * @access  public
     * @return  array
     */
    public function get()
    {
        assert('is_string($this->title); // Unable to create RSS item. Field "title" must be a string.');
        assert('is_string($this->link); // Unable to create RSS item. Field "link" must be a string.');
        assert('is_string($this->description); // Unable to create RSS item. Field "description" must be a string.');
        assert('is_string($this->author); // Unable to create RSS item. Field "author" must be a string.');
        assert('is_array($this->category); // Unable to create RSS item. Field "category" must be a array.');
        assert('is_string($this->comments); // Unable to create RSS item. Field "comments" must be a string.');
        assert('is_string($this->guid); // Unable to create RSS item. Field "guid" must be a string.');
        assert('is_string($this->pubDate); // Unable to create RSS item. Field "pubDate" must be a string.');

        /* check optional fields */
        $tmp = $this->author;
        $this->author = filter_var($this->author, FILTER_SANITIZE_EMAIL);
        if ($tmp != $this->author) {
            $message = "RSS item has been auto-converted.\n\t\tThe input value '".
                $tmp."' did not seem to be valid.\n\t\tThe value has been changed to '".$this->author."'.";
            trigger_error($message, E_USER_NOTICE);
        }
        $tmp = $this->comments;
        $this->comments = filter_var($this->comments, FILTER_SANITIZE_URL);
        if ($tmp != $this->comments) {
            $message = "RSS item has been auto-converted.\n\t\tThe input value '".
                $tmp."' did not seem to be valid.\n\t\tThe value has been changed to '".$this->comments."'.";
            trigger_error($message, E_USER_NOTICE);
        }
        if (empty($this->title) && empty($this->description)) {
            $message = "Unable to create RSS item. Need at least 'title' or 'description' to be present.";
            trigger_error($message, E_USER_NOTICE);
            return array();
        }

        /* auto-fill fields, that can be generated */
        $this->guid = md5($this->title.$this->description);

        /* return result */
        return get_object_vars($this);
    }

}
?>