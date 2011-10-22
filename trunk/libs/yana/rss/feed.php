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
 * <<entity>> RSS feed
 *
 * This class represents a RSS feed.
 *
 * Example of usage:
 * <code>
 *  // Creating and printing a RSS feed inside a plugin:
 *  public function rssFoobar($args)
 *  {
 *       // create rss feed
 *       $rss = new \Yana\RSS\Feed('this is my RSS feed');
 *       $rss->setTitle('foo');
 *       // create 1st item
 *       $item = new \Yana\RSS\Item('1st entry');
 *       $item->setLink('http://some.url')
 *          ->setDescription('some text');
 *       $rss->addItem($item);
 *       // create 2nd item
 *       $item = new \Yana\RSS\Item('2nd entry');
 *       $item->setLink('http://some.other.url')
 *          ->setDescription('some text');
 *       $rss->addItem($item);
 *       // print rss feed
 *       print $rss->toString();
 *       // when you're done, terminate the program
 *       exit(0);
 *  }
 * </code>
 *
 * @access      public
 * @package     yana
 * @subpackage  rss
 */
class Feed extends \Object
{

    /**
     * name of the channel, e.g. title of the website the channel refers to
     *
     * @var string
     */
    private $_title = "";
    /**
     * a link that points to the originating website of the feed
     *
     * @var string
     */
    private $_link = "";
    /**
     * a text to describe context and purpose of this channel
     *
     * @var string
     */
    private $_description = "";
    /**
     * language the channel is written in
     *
     * @var string
     */
    private $_language = "";
    /**
     * copyright notice of your choice
     *
     * @var string
     */
    private $_copyright = "";
    /**
     * mail of person responsible for editorial content
     *
     * @var string
     */
    private $_managingEditor = "";
    /**
     * mail of person responsible for technical issues
     *
     * @var string
     */
    private $_webMaster = "";
    /**
     * "time to live".
     *
     * A number of minutes that indicates how long a channel can be cached before refreshing from the source.
     *
     * @var int
     */
    private $_ttl = 0;
    /**
     * *.jpg, *.gif, or *.png image to display with the feed
     *
     * @var string
     */
    private $_image = "";
    /**
     * one or more categories that the channel belongs to
     *
     * @var array
     */
    private $_category = array();
    /**
     * a CSS stylesheet that formats the channel
     *
     * @var string
     */
    private $_css = "";
    /**
     * a XSL transformation that formats the channel
     *
     * @var string
     */
    private $_xslt = "";
    /**
     * list of one or more items of the channel
     *
     * @var array
     */
    private $_items = array();
    /**
     * pubDate of most recent item (auto-generated)
     *
     * @var string
     */
    private $_lastBuildDate = "";

    /**
     * Creates and initializes a new instance of this class.
     *
     * @param  string  $description  a text to describe context and purpose of this channel
     */
    public function __construct($description)
    {
        $this->setDescription($description);

        // auto-generated fields
        $language = \Language::getInstance();
        $this->_title = (string) $language->getVar('program_title');
        $this->_language = (string) $language->getLocale();
        $this->_link = \Request::getUri();
    }

    /**
     * Returns the title of the channel.
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set the name/title of the channel.
     *
     * @param  string  $title  e.g. title of the website the channel refers to
     * @return Feed
     */
    public function setTitle($title)
    {
        assert('is_string($title); // Invalid argument $title: string expected');

        $this->_title = $title;
        return $this;
    }

    /**
     * Returns a link that points to the originating website of the feed.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->_link;
    }

    /**
     * Set URL for link to the channel's website.
     *
     * @param  string  $link  link that points to the originating website of the feed
     * @return Feed
     */
    public function setLink($link)
    {
        assert('is_string($link); // Invalid argument $link: string expected');

        $this->_link = $link;
        return $this;
    }

    /**
     * Returns a text to describe context and purpose of this channel.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Set a channel description.
     *
     * @param  string  $description  a text to describe context and purpose of this channel
     * @return Feed 
     */
    public function setDescription($description)
    {
        assert('is_string($description); // Invalid argument $description: string expected');

        $this->_description = $description;
        return $this;
    }

    /**
     * Get locale/language the channel is written in.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * Set locale/language the channel is written in.
     *
     * Each channel should use 1 consistent language.
     * (If you need 2 languages, you should use 2 channels.)
     * The language or "locale" consists of a 2-letter language abbreviation in small letters,
     * optionally followed by a dash and a 2-letter country code in capital letters.
     * See documentation on the internet if you need more details.
     *
     * This settings is auto-detected from the currently used locale. Only change it if needed.
     *
     * @param  string  $language  valid language/locale string, e.g. en, en-US
     * @return Feed
     * @see    \Language
     */
    public function setLanguage($language)
    {
        assert('is_string($language); // Invalid argument $language: string expected');
        assert('preg_match("/^(?:|\w{2}(?:-\w{2})?)$/s", $language); // Invalid syntax: $language');

        $this->_language = $language;
        return $this;
    }

    /**
     * Returns copyright notice for this channel
     *
     * @return string
     */
    public function getCopyright()
    {
        return $this->_copyright;
    }

    /**
     * Set a copyright notice of your choice.
     *
     * Use this if you wish or need to set up copyright information on the channel.
     *
     * @param  string  $copyright  an URL or other reference to a license text
     * @return Feed
     */
    public function setCopyright($copyright)
    {
        assert('is_string($copyright); // Invalid argument $copyright: string expected');

        $this->_copyright = $copyright;
        return $this;
    }

    /**
     * Get e-mail of person responsible for editorial content.
     *
     * @return string
     */
    public function getManagingEditor()
    {
        return $this->_managingEditor;
    }

    /**
     * Set e-mail of person responsible for editorial content.
     *
     * In some legislations you may be required to provide a managing editor.
     * This is the person a reader might turn to if he/she thinks some of the channel's content
     * is incorrect or doubtful and thus needs to be changed or deleted.
     *
     * The managing editor is responsible for content, but not for questions regarding technical issues.
     * That will be the webmaster instead.
     *
     * The webmaster and the managing editor may be the same person in practice, in wich case you should name it twice.
     *
     * @param  string  $managingEditor  valid e-mail address
     * @return Feed
     */
    public function setManagingEditor($managingEditor)
    {
        assert('is_string($managingEditor); // Invalid argument $managingEditor: string expected');

        $this->_managingEditor = (string) filter_var($managingEditor, FILTER_SANITIZE_EMAIL);
        return $this;
    }

    /**
     * Get e-mail of person responsible for technical issues.
     *
     * @return string
     */
    public function getWebMaster()
    {
        return $this->_webMaster;
    }

    /**
     * Set e-mail of person responsible for technical issues.
     *
     * In some organizations may require you to name a person responsible for
     * questions regarding technical issues with the channel.
     *
     * The webmaster is responsible technical concerns, but not for the content of the channel.
     * That will be the managing editor instead.
     *
     * The webmaster and the managing editor may be the same person in practice, in wich case you should name it twice.
     *
     * @param  string  $webMaster  valid e-mail address
     * @return Feed
     */
    public function setWebMaster($webMaster)
    {
        assert('is_string($webMaster); // Invalid argument $webMaster: string expected');

        $this->_webMaster = (string) filter_var($webMaster, FILTER_SANITIZE_EMAIL);
        return $this;
    }

    /**
     * Get "time to live" in minutes. 
     *
     * Indicates how long a channel can be cached before refreshing from the source.
     * Defaults to 0.
     *
     * @return int
     */
    public function getTimeToLive()
    {
        return $this->_ttl;
    }

    /**
     * Set "time to live". 
     *
     * Indicates how long a channel can be cached before refreshing from the source.
     *
     * @param  int  $ttl  number of minutes, must be >= 0
     * @return Feed 
     */
    public function setTimeToLive($ttl)
    {
        assert('is_int($ttl); // Invalid argument $ttl: int expected');
        assert('$ttl >= 0; // Invalid argument $ttl: must be positive');

        $this->_ttl = $ttl;
        return $this;
    }

    /**
     * Get image URL.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->_image;
    }

    /**
     * Set image URL.
     *
     * URL to *.jpg, *.gif, or *.png image to display with the feed, e.g. a website logo.
     *
     * @param  string  $image  must be a valid URL.
     * @return Feed 
     */
    public function setImage($image)
    {
        assert('is_string($image); // Invalid argument $image: string expected');

        $this->_image = $image;
        return $this;
    }

    /**
     * Returns a list of categories that the channel belongs to.
     *
     * A category is a single line of text or word.
     * It identifies a key-word or name, to aggregate channels of similar types.
     * E.g. "private", "news" aso.
     *
     * @return array
     */
    public function getCategory()
    {
        return $this->_category;
    }

    /**
     * Include channel in one or more categories.
     *
     * A category is a single line of text or word.
     * It identifies a key-word or name, to aggregate channels of similar types.
     * E.g. "private", "news" aso.
     *
     * @param  array  $category  list of strings
     * @return Feed 
     */
    public function setCategory(array $category)
    {
        $this->_category = $category;
        return $this;
    }

    /**
     * Returns an URL to a CSS stylesheet that formats the channel.
     *
     * @return string
     */
    public function getCss()
    {
        return $this->_css;
    }

    /**
     * Set URL to a CSS stylesheet that formats the channel.
     *
     * You may set an URL to either a CSS or XSLT file that the browser can use to render the RSS-file.
     *
     * @param  string  $css  must be a valid URL
     * @return Feed 
     */
    public function setCss($css)
    {
        assert('is_string($css); // Invalid argument $css: string expected');

        $this->_css = $css;
        return $this;
    }

    /**
     * Returns an URL to a XSL transformation that formats the channel.
     *
     * @return string
     */
    public function getXslt()
    {
        return $this->_xslt;
    }


    /**
     * Set URL to a XSL transformation that formats the channel.
     *
     * You may set an URL to either a CSS or XSLT file that the browser can use to render the RSS-file.
     *
     * @param  string  $xslt  must be a valid URL
     * @return Feed 
     */
    public function setXslt($xslt)
    {
        assert('is_string($xslt); // Invalid argument $xslt: string expected');

        $this->_xslt = $xslt;
        return $this;
    }

    /**
     * Add RSS feed item to this channel.
     *
     * Note: this function does not check for duplicate guid's.
     *
     * @param   \Yana\RSS\Item  $item  new RSSitem
     * @return  Feed
     */
    public function addItem(Item $item)
    {
        assert('is_array($this->_items);');
        $this->_items[] = $item;
        return $this;
    }

    /**
     * Get RSS feed items of this channel.
     *
     * @return \Yana\RSS\Item[] 
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * This returns this object as a string in RSS 2.0 syntax.
     *
     * @return  string
     */
    public function toString()
    {
        $xmlString = $this->toSimpleXml()->asXML();
        $xmlString = preg_replace('/<\?xml.*?\?>/', '', $xmlString);
        $string = '<?xml version="1.0"?>' . "\n";
        if ($this->getXslt()) {
            $string .= '<?xml-stylesheet type="text/xsl" href="' . $this->getXslt() . '" version="1.0"?>' . "\n";
        }
        if ($this->getCss()) {
            $string .= '<?xml-stylesheet type="text/css" href="' . $this->getCss() . '" media="screen"?>' . "\n";
        }
        return $string . $xmlString;
    }

    /**
     * Convert item to XML fragment.
     *
     * Returns a RSS channel-fragment based on RSS 2.0 standard.
     *
     * @return \SimpleXMLElement
     */
    public function toSimpleXml()
    {
        $rss = new \SimpleXMLElement('<rss version="2.0"/>', LIBXML_NOXMLDECL);
        $channel = $rss->addChild('channel');
        $channel->addChild('docs', 'http://blogs.law.harvard.edu/tech/rss');
        // The following fields are mandatory, as stated at http://blogs.law.harvard.edu/tech/rss
        $channel->addChild('title', $this->getTitle());
        $channel->addChild('link', $this->getLink());
        $channel->addChild('description', $this->getDescription());
        // The following fields are optional
        $channel->addChild('language', $this->getLanguage());
        $channel->addChild('pubDate', date('r'));
        $channel->addChild('generator', "Yana Framework " . YANA_VERSION);

        assert('!isset($lastBuildDate); // Cannot redeclare var $lastBuildDate');
        $lastBuildDate = 0;
        $items = $this->getItems();
        assert('!isset($item); // Cannot redeclare var $item');
        foreach ($items as $item)
        {
            $tmp = strtotime($item->getPubDate());
            if (is_int($tmp) && $tmp > $lastBuildDate) {
                $lastBuildDate = $tmp;
            }
            unset($tmp);
        } // end foreach
        unset($item);
        if ($lastBuildDate > 0) {
            $channel->addChild('lastBuildDate', $lastBuildDate);
            $this->_lastBuildDate = date('r', $lastBuildDate);
        }
        unset($lastBuildDate);

        if ($this->getCopyright()) {
            $channel->addChild('copyright', $this->getCopyright());
        }
        if ($this->getManagingEditor()) {
            $channel->addChild('managingEditor', $this->getManagingEditor());
        }
        if ($this->getWebMaster()) {
            $channel->addChild('webMaster', $this->getWebMaster());
        }
        if ($this->getTimeToLive()) {
            $channel->addChild('ttl', $this->getTimeToLive());
        }

        assert('!isset($category); // Cannot redeclare var $category');
        foreach ($this->getCategory() as $category)
        {
            $channel->addChild('category', $category);
        }
        unset($category);

        assert('!isset($imageXML); // Cannot redeclare var $imageXML');
        if ($this->getImage()) {
            $imageXML = $channel->addChild('image');
            $imageXML->addChild('title', $this->getTitle());
            $imageXML->addChild('link', $this->getLink());
            $imageXML->addChild('url', $this->getImage());
        }
        unset($imageXML);

        assert('!isset($item); // Cannot redeclare var $item');
        /* @var $item Item */
        foreach ($items as $item)
        {
            $item->toSimpleXml($channel); // add item to channel
        }
        unset($item);

        return $rss;
    }

}

?>