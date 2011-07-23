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

namespace Yana\RSS;

/**
 * <<entity>> RSS item.
 *
 * This class represents an item of an RSS feed.
 *
 * Note: all attributes are optional, but at least one of title or description must be set.
 *
 * @package     yana
 * @subpackage  rss
 */
class Item extends \Object
{

    /**
     * title of the item
     *
     * @var string
     */
    private $_title = "";

    /**
     * item synopsis, note that this may contain HTML
     *
     * @var string
     */
    private $_description = "";

    /**
     * URL of the item
     *
     * @var string
     */
    private $_link = "";

    /**
     * Email address of the author
     *
     * @var string
     */
     private $_author = "";

    /**
     * include item in one or more categories
     *
     * @var array
     */
     private $_category = array();

    /**
     * URL of a page for comments
     *
     * @var string
     */
     private $_comments = "";

    /**
     * indicates when the item was published (auto-generated if not provided)
     *
     * @var string
     */
     private $_pubDate = "";

    /**
     * Creates and initializes a new instance of this class.
     *
     * @param  string  $title  single line of text, may contain HTML
     */
    public function __construct($title)
    {
        assert('is_string($title); // Invalid argument $title: string expected');

        $this->_title = (string) $title;

        $this->_pubDate = date('r');
    }

    /**
     * Returns the general unique identifier.
     *
     * This value is always auto-generated based on the title and description.
     *
     * @return string 
     */
    public function getGUID()
    {
        return md5($this->_title . $this->_description);
    }

    /**
     * Returns the title of the item.
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Get URL of the item.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->_link;
    }

    /**
     * Set URL of the item.
     *
     * Any item must refer to an existing web-resource.
     *
     * @param  string  $link  must be a valid URL
     * @return Item 
     */
    public function setLink($link)
    {
        assert('is_string($link); // Invalid argument $link: string expected');

        $this->_link = (string) $link;
        return $this;
    }

    /**
     * Item synopsis, note that this may contain HTML.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Set item synopsis.
     *
     * @param  string  $description  some text - may contain HTML
     * @return Item 
     */
    public function setDescription($description)
    {
        assert('is_string($description); // Invalid argument $description: string expected');

        $this->_description = (string) $description;
        return $this;
    }

    /**
     * Get e-mail address of the author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->_author;
    }

    /**
     * Set e-mail address of the author.
     *
     * @param  string  $author  must be valid e-mail address
     * @return Item 
     */
    public function setAuthor($author)
    {
        assert('is_string($author); // Invalid argument $author: string expected');

        $this->_author = (string) filter_var($author, FILTER_SANITIZE_EMAIL);
        return $this;
    }

    /**
     * Get list of categories.
     *
     * @return array  list of strings
     */
    public function getCategory()
    {
        return $this->_category;
    }

    /**
     * Include item in one or more categories.
     *
     * A category is a single line of text or word.
     * It identifies a key-word or name, to aggregate items of similar types.
     * E.g. all "announcements", all "news-updates" aso.
     *
     * @param  array  $category  list of strings
     * @return Item 
     */
    public function setCategory(array $category)
    {
        $this->_category = $category;
        return $this;
    }

    /**
     * Get URL to a page for comments.
     *
     * @return string
     */
    public function getComments()
    {
        return $this->_comments;
    }

    /**
     * Set URL to a page for comments.
     *
     * Optionally the reader may comment on the read content.
     * If you provide a comment page or forum to do so, enter the URL here.
     * The comment-URL and the content-URL may be identical.
     *
     * @param  string  $comments  must be a valid URL
     * @return Item 
     */
    public function setComments($comments)
    {
        assert('is_string($comments); // Invalid argument $comments: string expected');

        $this->_comments = (string) filter_var($comments, FILTER_SANITIZE_URL);
        return $this;
    }

    /**
     * Indicates when the item was published.
     *
     * @return string
     */
    public function getPubDate()
    {
        return $this->_pubDate;
    }

    /**
     * Set a publication date.
     *
     * This value indicates when the item was published.
     * It is auto-generated if not provided.
     *
     * Note: Use date('r'); to create a valid string.
     *
     * @param  string  $pubDate  formatted date using RFC 2822
     * @return Item 
     */
    public function setPubDate($pubDate)
    {
        assert('is_string($pubDate); // Invalid argument $pubDate: string expected');

        $this->_pubDate = $pubDate;
        return $this;
    }

    /**
     * This returns this object as a string in RSS 2.0 syntax.
     *
     * @return  string
     */
    public function toString()
    {
        return $this->toSimpleXml()->asXML();
    }

    /**
     * Convert item to XML fragment.
     *
     * Returns an item-fragment based on RSS 2.0 standard.
     *
     * @param  \SimpleXMLElement  $channel  parent XML node, for valid RSS this should be a "channel" element
     * @return \SimpleXMLElement
     */
    public function toSimpleXml(\SimpleXMLElement $channel = null)
    {
        if (is_null($channel)) {
            $xml = new \SimpleXMLElement('<item/>', LIBXML_NOXMLDECL);
        } else {
            $xml = $channel->addChild('item');
        }
        $xml->addChild('title', $this->getTitle());
        $xml->addChild('pubDate', $this->getPubDate());
        if ($this->getDescription()) {
            $xml->addChild('description', $this->getDescription());
        }
        foreach ($this->getCategory() as $category)
        {
            $xml->addChild('category', $category);
        }
        $xml->addChild('guid', $this->getGUID())->addAttribute('isPermaLink', 'false');
        if ($this->getAuthor()) {
            $xml->addChild('author', $this->getAuthor());
        }
        return $xml;
    }

}

?>